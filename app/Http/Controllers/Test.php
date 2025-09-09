<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;


class Test extends Controller
{   
    public function MissingOR(){

        $start = 18851;
        $end = 19650;

        $ar = array();
        DB::table('numbers_or')
        ->delete();
        for($i=$start;$i<=$end;$i++){
            $ar[]=[
                'or_no'=>$i
            ];
        }
        DB::table('numbers_or')
        ->insert($ar);





        $orMissing = DB::select("   WITH or_no as (
SELECT *,CAST(or_no as unsigned) as act_or FROM (
        SELECT DATE_FORMAT(transaction_date,'%m/%d/%Y') as transaction_date,or_no,SUM(total_payment) as payment,payor,status,'Repayment' as type FROM (
        SELECT rt.id_repayment,rt.id_repayment_transaction,if(rt.id_repayment is not null,concat('R',rt.id_repayment),concat('X',rt.id_repayment_transaction)) as ref,rt.or_no,rt.transaction_date,rt.total_payment,if(rt.status=10,'Cancelled','') as status,
        CASE WHEN rt.id_repayment is not null THEN RepaymentDescription(r.payment_for,r.id_repayment)
        ELSE concat(m.first_name,' ',m.last_name) END as payor
        FROM repayment_transaction as rt
        LEFT JOIN repayment as r on r.id_repayment = rt.id_repayment
        LEFT JOIN member as m on m.id_member = rt.id_member
        WHERE rt.id_cash_receipt_voucher+rt.id_journal_voucher > 0 AND rt.or_no is not null) as k 
        GROUP BY ref
        UNION ALL
        SELECT cr.date_received,cr.or_no,cr.total_payment,if(type=1,concat(m.first_name,' ',m.last_name),payee_text) as payor,if(cr.status=10,'CANCELLED','') as status,'CR' FROM cash_receipt as cr
        LEFT JOIN member as m on m.id_member = cr.id_member
        WHERE type = 1) as k
GROUP BY CAST(or_no as unsigned)
ORDER BY CAST(or_no as unsigned))
SELECT numbers_or.or_no,if(or_no.or_no is not null,1,0) as exist,or_no.payor,or_no.payment FROM numbers_or
LEFT JOIN or_no on or_no.or_no = numbers_or.or_no;
        ");
        // dd($orMissing);

        $FinalOutput = array();
        $chuncked = array_chunk($orMissing,50);



        $data['chunked'] = $chuncked;
        // dd($chuncked);

        // dd($chuncked);



        // foreach($chuncked as $ck){
        //     $ar = collect($ck)->sum('exists');
        //     // $FinalOutput[]=[
        //     //     'start' =>$ck[0]->or_no,
        //     //     'end' =>$ck[49]->or_no,
        //     //     'encoded_count'=> $ar,
        //     //     'remarks'=> ($ar == 0)?'MISSING':''
        //     // ];
        //     // if($ar == 0){
        //     //     $FinalOutput[]=[
        //     //         'start' =>$ck[0]->or_no
        //     //         'end' =>$ck[49]->or_no
        //     //         'remarks'=>''
        //     //     ];
        //     // }
        // }

        // $data['output'] = $FinalOutput;

        // dd($data);




       
        return view('or_missing',$data);

        dd($FinalOutput);
    }

    public function series_groupings($or,$all){
        $min_count = 1;
        $current_series = array();
        $temp = array();
        $series_lists = array();

        foreach($or as $c=>$h){
            if(!in_array($h,$current_series)){
                $series_start = $this->identifyStartingSeriesGroup($h);
                $current_series = $this->generateSeriesArray($series_start);
                if($c > 0){ 
                    $not_exists = array_values(array_diff($last_series,$temp));
                    // $out_range = $this->get_out_range($not_exists);
                    // $not_exists = array_values(array_diff($not_exists,$out_range));
                    $ne = $not_exists;
                    if((count($temp) >= $min_count && count($ne) > 0) || $all){
                        array_push($series_lists,[
                            'series'=>['start'=>$last_series[0],'end'=>$last_series[49]],
                            'missing'=>$ne
                        ]);
                    }
                }
                $temp = array();
                $last_series = $current_series;
            }
            array_push($temp,$h);
            if($c == count($or)-1){
                $ne = array_values(array_diff($last_series,$temp));
                // $out_range = $this->get_out_range($ne);
                // $ne = array_values(array_diff($ne,$out_range));
                if((count($temp) >= $min_count && count($ne) > 0) || $all){
                    array_push($series_lists,[
                        'series'=>['start'=>$current_series[0],'end'=>$current_series[49]],
                        'missing'=>$ne
                    ]);
                }
            }
        } 
        return $series_lists;
    }

    public function identifyStartingSeriesGroup($input){
        // Extract the numeric and non-numeric parts from the input string
        preg_match('/^(\D*)(\d+)(\D*)$/', $input, $matches);
        $prefix = $matches[1];
        $numericPart = $matches[2];
        $suffix = $matches[3];
    
        // Determine the starting number based on the numeric part
        $startNumber = (int)$numericPart;
        if($startNumber % 50 == 0){
            $startNumber = $startNumber-49;
        }
        // Determine the starting series group based on increments of 50
        $startingSeries = floor($startNumber / 50) * 50 + 1;
    
        // Format the starting series number with leading zeros
        $formattedStartingSeries = sprintf("%0" . strlen($numericPart) . "d", $startingSeries);
    
        // Construct the output string
        $output = $prefix . $formattedStartingSeries . $suffix;
    
        return $output;
    }

    public function generateSeriesArray($input){
        // Extract the numeric and non-numeric parts from the input string
        preg_match('/^(\D*)(\d+)(\D*)$/', $input, $matches);
        $prefix = $matches[1];
        $numericPart = $matches[2];
        $suffix = $matches[3];
    
        // Determine the starting number based on the numeric part
        $startNumber = (int)$numericPart;
    
        // Determine the length of the numeric part
        $length = strlen($numericPart);
    
        // Generate the series array
        $seriesArray = [];
        for ($i = $startNumber; $i < $startNumber + 50; $i++) {
            // Format the number with leading zeros
            $formattedNumber = sprintf("%0{$length}d", $i);
    
            // Append the formatted number to the original string
            $seriesArray[] = $prefix . $formattedNumber . $suffix;
        }
        return $seriesArray;
    }

    public function test_api_push(Request $request){
        // $tee = array();
        // $te= array(
        //     "fruit" => "apple",
        //     "color" => "red"
        // );
        // $tee[] = $te;
        // return json_decode(json_encode($tee),true);

        // $sql = "SELECT hawb_no,book_ref,s_branch_id,c_branch_id,transaction,transportation,rate,emp_id,acc_id,s_id,c_id,user_id,shipment,service_id,chk_intra,length,width,height,volume,actual,quantity,declared,transhipment,crating,others,freight,discount,valuation,x_handling,insurance,handling,x_insurance,x_dfs,tff,documentary,vat,total,shipment_date,service_mode,content,delivery,cost_center,s_chk,c_chk,date_con_received,remarks,time_con_received,mess_deliver,is_paid,received_by,current_location,dfs_am,dfs_av,dfs_al,dfs_vm,dfs_mv,option_1,option_2,valuation_sea,s_account,s_sub_account,s_name,s_company,s_street,s_brgy,s_municipality,s_city,s_province,s_address,s_building_name,s_floor_no,s_department,s_phone,s_email,c_account,c_sub_account,c_name,c_company,c_street,c_brgy,c_municipality,c_city,c_province,c_address,c_building_name,c_floor_no,c_department,c_phone,c_email,handling_s,handling_c,handling_a,dec_s,dec_c,dec_a,disc_s,disc_c,disc_a,tff_s,tff_c,tff_a,chk_perishable,chk_fragile,chk_dangerous_goods,chk_crating,chk_valuable_cargo,chk_rush,epeso,eparcel,enondocs,ecargo,eperishable,ediscount,cancel,dropoff,misroute,is_manifest,trace_flag,notification,is_send,accompanying,created_at,pl_status,id_tn_milestone,chk_rts,tn_rts
        // FROM lse.hawb_info
        // WHERe hawb_no in ('006543','001247','006544','006545','006546','006547')";

        $sql = "SELECT hawb_no, s_branch_id, c_branch_id, shipment_date, dropoff, transaction,0 as emp_id,55120 as acc_id, 
                s_chk,55120 as s_id, s_name, s_company, s_phone, s_email, 
                c_chk, c_id, c_name, c_company, c_phone, c_email, 
                content, delivery, 
                service_id, chk_intra, length, width, height, actual, quantity, declared, transhipment, crating, shipment, handling, accompanying, freight, insurance, tff, documentary, valuation, discount, vat, total, 
                524 as user_id, 
                dfs_av, dfs_am, 
                epeso, eparcel, transportation, x_dfs, x_insurance, option_1, x_handling, rate, option_2, 
                handling_s, handling_c, handling_a,
                dec_s, dec_c, dec_a,
                disc_s, disc_c, disc_a, 
                valuation_sea, date_con_received, remarks, time_con_received, mess_deliver, is_paid, received_by, current_location, 
                dfs_al, dfs_vm, dfs_mv, 
                others, 
                s_street, s_brgy, s_municipality, s_city, s_province, 
                c_street, c_brgy, c_municipality, c_city, c_province, 
                tff_s, tff_c, tff_a, 
                volume, enondocs, ecargo, eperishable, ediscount, cancel, misroute, is_manifest, trace_flag, notification
                FROM lse.hawb_info
                WHERe hawb_no in ('006543')";

        $data = DB::connection('cloud_db')
        ->select($sql);

        // return json_encode($data);
        // return $data;
        // return 123;
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => '112.198.236.60:7073/api/libcap_encoding/new_system?=',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>'{
      "data" : '.json_encode($data).'}',
      CURLOPT_HTTPHEADER => array(
        'libcap-key: 00af6126-ebff-461c-9cb1-43649e3f9bba',
        'host-ip: '.request()->getHost(),
        'Content-Type: application/json'
      ),
    ));



        $response = curl_exec($curl);
       
        curl_close($curl);
        return $response;

            return json_decode($response,true);
    }

}


