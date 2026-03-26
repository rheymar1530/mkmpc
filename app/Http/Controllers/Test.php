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
    public function cbu_adj(){


        $members = array(

        ['id_member'=>45,'amount'=>203.99],
        ['id_member'=>58,'amount'=>385.04],
        ['id_member'=>59,'amount'=>356.36],
        ['id_member'=>192,'amount'=>287.29],
        ['id_member'=>464,'amount'=>911.49],
        ['id_member'=>341,'amount'=>1196.2],
        ['id_member'=>545,'amount'=>315.72],
        ['id_member'=>639,'amount'=>289.07],
        ['id_member'=>191,'amount'=>188.61],
        ['id_member'=>465,'amount'=>253.99],
        ['id_member'=>466,'amount'=>373.18],
        ['id_member'=>546,'amount'=>304.9],
        ['id_member'=>9,'amount'=>286.26],
        ['id_member'=>250,'amount'=>621.28],
        ['id_member'=>737,'amount'=>230.8],
        ['id_member'=>467,'amount'=>847.36],
        ['id_member'=>183,'amount'=>265.85],
        ['id_member'=>373,'amount'=>172.05],
        ['id_member'=>175,'amount'=>726.25],
        ['id_member'=>289,'amount'=>787.32],
        ['id_member'=>736,'amount'=>183.99],
        ['id_member'=>79,'amount'=>301.79],
        ['id_member'=>297,'amount'=>599.25],
        ['id_member'=>372,'amount'=>659.98],
        ['id_member'=>320,'amount'=>579.3],
        ['id_member'=>700,'amount'=>33.46],
        ['id_member'=>269,'amount'=>232.48],
        ['id_member'=>10,'amount'=>284.8],
        ['id_member'=>374,'amount'=>195.14],
        ['id_member'=>298,'amount'=>385.4],
        ['id_member'=>468,'amount'=>1138.82],
        ['id_member'=>469,'amount'=>775.15],
        ['id_member'=>61,'amount'=>197.82],
        ['id_member'=>69,'amount'=>790.93],
        ['id_member'=>41,'amount'=>287.83],
        ['id_member'=>184,'amount'=>755.94],
        ['id_member'=>3,'amount'=>280.41],
        ['id_member'=>548,'amount'=>153.49],
        ['id_member'=>470,'amount'=>290.63],
        ['id_member'=>154,'amount'=>545],
        ['id_member'=>609,'amount'=>537.15],
        ['id_member'=>278,'amount'=>156.82],
        ['id_member'=>73,'amount'=>765.79],
        ['id_member'=>185,'amount'=>257.51],
        ['id_member'=>46,'amount'=>291.17],
        ['id_member'=>357,'amount'=>375.05],
        ['id_member'=>299,'amount'=>788.23],
        ['id_member'=>734,'amount'=>39.94],
        ['id_member'=>245,'amount'=>196.39],
        ['id_member'=>74,'amount'=>258.68],
        ['id_member'=>120,'amount'=>210.67],
        ['id_member'=>358,'amount'=>817.27],
        ['id_member'=>76,'amount'=>646.17],
        ['id_member'=>300,'amount'=>247.84],
        ['id_member'=>352,'amount'=>542.25],
        ['id_member'=>256,'amount'=>1770.44],
        ['id_member'=>353,'amount'=>646.92],
        ['id_member'=>261,'amount'=>423.37],
        ['id_member'=>193,'amount'=>215.4],
        ['id_member'=>216,'amount'=>1128.62],
        ['id_member'=>251,'amount'=>1363.21],
        ['id_member'=>77,'amount'=>867.91],
        ['id_member'=>11,'amount'=>484.46],
        ['id_member'=>364,'amount'=>604.81],
        ['id_member'=>733,'amount'=>164.27],
        ['id_member'=>732,'amount'=>45.1],
        ['id_member'=>134,'amount'=>774.45],
        ['id_member'=>731,'amount'=>175.78],
        ['id_member'=>730,'amount'=>46.24],
        ['id_member'=>44,'amount'=>156.47],
        ['id_member'=>327,'amount'=>56.01],
        ['id_member'=>472,'amount'=>1133.01],
        ['id_member'=>597,'amount'=>996.92],
        ['id_member'=>78,'amount'=>790.66],
        ['id_member'=>550,'amount'=>181.42],
        ['id_member'=>80,'amount'=>1728.15],
        ['id_member'=>729,'amount'=>52.44],
        ['id_member'=>551,'amount'=>269.49],
        ['id_member'=>295,'amount'=>550.6],
        ['id_member'=>473,'amount'=>334.21],
        ['id_member'=>296,'amount'=>703.72],
        ['id_member'=>355,'amount'=>993.25],
        ['id_member'=>82,'amount'=>468.27],
        ['id_member'=>474,'amount'=>400.15],
        ['id_member'=>85,'amount'=>1136.33],
        ['id_member'=>87,'amount'=>1429.27],
        ['id_member'=>163,'amount'=>390.98],
        ['id_member'=>91,'amount'=>405.02],
        ['id_member'=>92,'amount'=>527.71],
        ['id_member'=>149,'amount'=>278.63],
        ['id_member'=>94,'amount'=>900.75],
        ['id_member'=>602,'amount'=>703.72],
        ['id_member'=>28,'amount'=>993.25],
        ['id_member'=>728,'amount'=>241.48],
        ['id_member'=>623,'amount'=>159.57],
        ['id_member'=>726,'amount'=>21.52],
        ['id_member'=>97,'amount'=>915.4],
        ['id_member'=>625,'amount'=>370.82],
        ['id_member'=>99,'amount'=>1082.75],
        ['id_member'=>101,'amount'=>840.76],
        ['id_member'=>164,'amount'=>230.42],
        ['id_member'=>102,'amount'=>1074.76],
        ['id_member'=>103,'amount'=>544.99],
        ['id_member'=>104,'amount'=>681.66],
        ['id_member'=>106,'amount'=>892.03],
        ['id_member'=>232,'amount'=>433.31],
        ['id_member'=>155,'amount'=>637.96],
        ['id_member'=>5,'amount'=>272.38],
        ['id_member'=>124,'amount'=>754.52],
        ['id_member'=>121,'amount'=>631.65],
        ['id_member'=>35,'amount'=>983.74],
        ['id_member'=>600,'amount'=>817.16],
        ['id_member'=>233,'amount'=>218.83],
        ['id_member'=>475,'amount'=>562.65],
        ['id_member'=>23,'amount'=>206.65],
        ['id_member'=>36,'amount'=>469.4],
        ['id_member'=>270,'amount'=>227.87],
        ['id_member'=>271,'amount'=>198.85],
        ['id_member'=>367,'amount'=>338.46],
        ['id_member'=>725,'amount'=>93.64],
        ['id_member'=>368,'amount'=>243.09],
        ['id_member'=>234,'amount'=>232.22],
        ['id_member'=>111,'amount'=>1190.03],
        ['id_member'=>618,'amount'=>505.32],
        ['id_member'=>176,'amount'=>845.53],
        ['id_member'=>112,'amount'=>595.04],
        ['id_member'=>284,'amount'=>205.82],
        ['id_member'=>68,'amount'=>341.24],
        ['id_member'=>70,'amount'=>239.09],
        ['id_member'=>262,'amount'=>204.71],
        ['id_member'=>318,'amount'=>1390.81],
        ['id_member'=>285,'amount'=>759.89],
        ['id_member'=>114,'amount'=>574.2],
        ['id_member'=>116,'amount'=>600.46],
        ['id_member'=>118,'amount'=>299.29],
        ['id_member'=>122,'amount'=>525.36],
        ['id_member'=>123,'amount'=>282.92],
        ['id_member'=>125,'amount'=>587.18],
        ['id_member'=>127,'amount'=>2272.4],
        ['id_member'=>129,'amount'=>289.4],
        ['id_member'=>47,'amount'=>364.42],
        ['id_member'=>119,'amount'=>931.35],
        ['id_member'=>177,'amount'=>852.97],
        ['id_member'=>321,'amount'=>1104.18],
        ['id_member'=>724,'amount'=>313.02],
        ['id_member'=>132,'amount'=>195.67],
        ['id_member'=>133,'amount'=>232.85],
        ['id_member'=>136,'amount'=>624.34],
        ['id_member'=>231,'amount'=>285.3],
        ['id_member'=>19,'amount'=>250.43],
        ['id_member'=>336,'amount'=>1312.13],
        ['id_member'=>723,'amount'=>26.88],
        ['id_member'=>476,'amount'=>179.49],
        ['id_member'=>24,'amount'=>1053.96],
        ['id_member'=>375,'amount'=>261.28],
        ['id_member'=>354,'amount'=>271.27],
        ['id_member'=>343,'amount'=>527.94],
        ['id_member'=>608,'amount'=>492.19],
        ['id_member'=>382,'amount'=>1039.46],
        ['id_member'=>480,'amount'=>410.27],
        ['id_member'=>477,'amount'=>260.59],
        ['id_member'=>479,'amount'=>22.35],
        ['id_member'=>221,'amount'=>629.67],
        ['id_member'=>478,'amount'=>856.78],
        ['id_member'=>150,'amount'=>89.06],
        ['id_member'=>156,'amount'=>637.96],
        ['id_member'=>139,'amount'=>560.39],
        ['id_member'=>142,'amount'=>116.03],
        ['id_member'=>335,'amount'=>408.75],
        ['id_member'=>202,'amount'=>370.56],
        ['id_member'=>622,'amount'=>770.54],
        ['id_member'=>481,'amount'=>636.8],
        ['id_member'=>645,'amount'=>698.5],
        ['id_member'=>48,'amount'=>1354.95],
        ['id_member'=>619,'amount'=>884.03],
        ['id_member'=>482,'amount'=>486.13],
        ['id_member'=>666,'amount'=>11.53],
        ['id_member'=>603,'amount'=>545],
        ['id_member'=>661,'amount'=>12.64],
        ['id_member'=>81,'amount'=>746.54],
        ['id_member'=>359,'amount'=>239.07],
        ['id_member'=>584,'amount'=>545],
        ['id_member'=>83,'amount'=>220.37],
        ['id_member'=>632,'amount'=>339.36],
        ['id_member'=>12,'amount'=>260.83],
        ['id_member'=>487,'amount'=>98.75],
        ['id_member'=>37,'amount'=>1068.68],
        ['id_member'=>486,'amount'=>575.94],
        ['id_member'=>483,'amount'=>251.62],
        ['id_member'=>252,'amount'=>1038.98],
        ['id_member'=>484,'amount'=>937.32],
        ['id_member'=>194,'amount'=>567],
        ['id_member'=>342,'amount'=>267.31],
        ['id_member'=>488,'amount'=>340.68],
        ['id_member'=>485,'amount'=>390.05],
        ['id_member'=>553,'amount'=>1334.92],
        ['id_member'=>489,'amount'=>512.23],
        ['id_member'=>490,'amount'=>261.27],
        ['id_member'=>322,'amount'=>672.25],
        ['id_member'=>323,'amount'=>741.36],
        ['id_member'=>319,'amount'=>1760.76],
        ['id_member'=>13,'amount'=>593.5],
        ['id_member'=>722,'amount'=>247.79],
        ['id_member'=>491,'amount'=>1402.33],
        ['id_member'=>492,'amount'=>210.2],
        ['id_member'=>290,'amount'=>149.55],
        ['id_member'=>617,'amount'=>439.93],
        ['id_member'=>369,'amount'=>182.79],
        ['id_member'=>493,'amount'=>355.01],
        ['id_member'=>721,'amount'=>224.47],
        ['id_member'=>337,'amount'=>804.29],
        ['id_member'=>635,'amount'=>188.5],
        ['id_member'=>494,'amount'=>304.11],
        ['id_member'=>49,'amount'=>273.64],
        ['id_member'=>720,'amount'=>65.77],
        ['id_member'=>167,'amount'=>885.75],
        ['id_member'=>283,'amount'=>2326.87],
        ['id_member'=>495,'amount'=>230.77],
        ['id_member'=>496,'amount'=>22.07],
        ['id_member'=>497,'amount'=>114.21],
        ['id_member'=>593,'amount'=>481.84],
        ['id_member'=>64,'amount'=>771.69],
        ['id_member'=>664,'amount'=>12.64],
        ['id_member'=>498,'amount'=>618.46],
        ['id_member'=>246,'amount'=>640.35],
        ['id_member'=>500,'amount'=>288.19],
        ['id_member'=>140,'amount'=>393.59],
        ['id_member'=>291,'amount'=>610.59],
        ['id_member'=>25,'amount'=>634.61],
        ['id_member'=>144,'amount'=>755.92],
        ['id_member'=>554,'amount'=>275.1],
        ['id_member'=>126,'amount'=>725.62],
        ['id_member'=>158,'amount'=>570.14],
        ['id_member'=>135,'amount'=>1233.39],
        ['id_member'=>555,'amount'=>262.24],
        ['id_member'=>159,'amount'=>704.45],
        ['id_member'=>168,'amount'=>238.81],
        ['id_member'=>169,'amount'=>456.82],
        ['id_member'=>160,'amount'=>192.08],
        ['id_member'=>161,'amount'=>762.84],
        ['id_member'=>502,'amount'=>553.07],
        ['id_member'=>384,'amount'=>1991.65],
        ['id_member'=>504,'amount'=>684.75],
        ['id_member'=>208,'amount'=>311.63],
        ['id_member'=>719,'amount'=>63.42],
        ['id_member'=>505,'amount'=>1126.71],
        ['id_member'=>141,'amount'=>722.59],
        ['id_member'=>506,'amount'=>214.3],
        ['id_member'=>556,'amount'=>648.25],
        ['id_member'=>507,'amount'=>340.14],
        ['id_member'=>508,'amount'=>16.23],
        ['id_member'=>20,'amount'=>225.21],
        ['id_member'=>6,'amount'=>196.56],
        ['id_member'=>272,'amount'=>292.23],
        ['id_member'=>273,'amount'=>251.28],
        ['id_member'=>557,'amount'=>277.34],
        ['id_member'=>509,'amount'=>572.94],
        ['id_member'=>385,'amount'=>2237.02],
        ['id_member'=>227,'amount'=>1270.87],
        ['id_member'=>386,'amount'=>1408.32],
        ['id_member'=>387,'amount'=>273.13],
        ['id_member'=>309,'amount'=>155.13],
        ['id_member'=>360,'amount'=>295.83],
        ['id_member'=>640,'amount'=>289.07],
        ['id_member'=>628,'amount'=>129.57],
        ['id_member'=>388,'amount'=>182.82],
        ['id_member'=>222,'amount'=>1109.92],
        ['id_member'=>223,'amount'=>653.39],
        ['id_member'=>558,'amount'=>880.59],
        ['id_member'=>559,'amount'=>254.83],
        ['id_member'=>560,'amount'=>297.72],
        ['id_member'=>390,'amount'=>557.56],
        ['id_member'=>561,'amount'=>639.12],
        ['id_member'=>624,'amount'=>502.57],
        ['id_member'=>257,'amount'=>2406.33],
        ['id_member'=>391,'amount'=>278.09],
        ['id_member'=>392,'amount'=>356.29],
        ['id_member'=>641,'amount'=>242.69],
        ['id_member'=>642,'amount'=>289.07],
        ['id_member'=>310,'amount'=>330.28],
        ['id_member'=>292,'amount'=>317.8],
        ['id_member'=>620,'amount'=>446.11],
        ['id_member'=>247,'amount'=>221.93],
        ['id_member'=>629,'amount'=>301.71],
        ['id_member'=>636,'amount'=>150.86],
        ['id_member'=>344,'amount'=>248.97],
        ['id_member'=>274,'amount'=>171.64],
        ['id_member'=>510,'amount'=>335.44],
        ['id_member'=>203,'amount'=>293.79],
        ['id_member'=>279,'amount'=>383.69],
        ['id_member'=>328,'amount'=>253.83],
        ['id_member'=>117,'amount'=>1894.01],
        ['id_member'=>393,'amount'=>357.67],
        ['id_member'=>395,'amount'=>396.51],
        ['id_member'=>151,'amount'=>451.25],
        ['id_member'=>396,'amount'=>159.52],
        ['id_member'=>398,'amount'=>399.77],
        ['id_member'=>38,'amount'=>541.65],
        ['id_member'=>511,'amount'=>192.98],
        ['id_member'=>253,'amount'=>275.94],
        ['id_member'=>399,'amount'=>696.23],
        ['id_member'=>218,'amount'=>1334.92],
        ['id_member'=>401,'amount'=>631.54],
        ['id_member'=>403,'amount'=>532.52],
        ['id_member'=>376,'amount'=>196.85],
        ['id_member'=>377,'amount'=>825.68],
        ['id_member'=>378,'amount'=>758.23],
        ['id_member'=>263,'amount'=>166.87],
        ['id_member'=>379,'amount'=>222.68],
        ['id_member'=>604,'amount'=>488.53],
        ['id_member'=>637,'amount'=>150.86],
        ['id_member'=>14,'amount'=>230.7],
        ['id_member'=>39,'amount'=>792.54],
        ['id_member'=>42,'amount'=>215.8],
        ['id_member'=>370,'amount'=>480.24],
        ['id_member'=>224,'amount'=>217.62],
        ['id_member'=>380,'amount'=>291.53],
        ['id_member'=>84,'amount'=>491.73],
        ['id_member'=>210,'amount'=>158.48],
        ['id_member'=>404,'amount'=>657.78],
        ['id_member'=>211,'amount'=>569.46],
        ['id_member'=>564,'amount'=>194.16],
        ['id_member'=>406,'amount'=>610.17],
        ['id_member'=>275,'amount'=>289.04],
        ['id_member'=>200,'amount'=>1219.52],
        ['id_member'=>52,'amount'=>271.27],
        ['id_member'=>186,'amount'=>992.62],
        ['id_member'=>408,'amount'=>1364.7],
        ['id_member'=>409,'amount'=>329.11],
        ['id_member'=>90,'amount'=>885.99],
        ['id_member'=>361,'amount'=>377.59],
        ['id_member'=>152,'amount'=>923.41],
        ['id_member'=>718,'amount'=>70.57],
        ['id_member'=>411,'amount'=>427.98],
        ['id_member'=>717,'amount'=>50.31],
        ['id_member'=>264,'amount'=>389.28],
        ['id_member'=>265,'amount'=>572.89],
        ['id_member'=>266,'amount'=>514.74],
        ['id_member'=>16,'amount'=>265.6],
        ['id_member'=>15,'amount'=>280.02],
        ['id_member'=>195,'amount'=>579.37],
        ['id_member'=>412,'amount'=>152.2],
        ['id_member'=>621,'amount'=>319.86],
        ['id_member'=>414,'amount'=>388.33],
        ['id_member'=>565,'amount'=>169.2],
        ['id_member'=>315,'amount'=>891.11],
        ['id_member'=>316,'amount'=>1088.23],
        ['id_member'=>248,'amount'=>210.03],
        ['id_member'=>716,'amount'=>876.4],
        ['id_member'=>416,'amount'=>996.36],
        ['id_member'=>598,'amount'=>496.39],
        ['id_member'=>187,'amount'=>578.53],
        ['id_member'=>419,'amount'=>951.04],
        ['id_member'=>420,'amount'=>1522.21],
        ['id_member'=>280,'amount'=>786.96],
        ['id_member'=>212,'amount'=>704.98],
        ['id_member'=>643,'amount'=>289.07],
        ['id_member'=>311,'amount'=>342.89],
        ['id_member'=>314,'amount'=>102.06],
        ['id_member'=>293,'amount'=>581.69],
        ['id_member'=>422,'amount'=>552.39],
        ['id_member'=>301,'amount'=>494.45],
        ['id_member'=>307,'amount'=>792.88],
        ['id_member'=>225,'amount'=>383.3],
        ['id_member'=>93,'amount'=>665.29],
        ['id_member'=>430,'amount'=>1308.69],
        ['id_member'=>431,'amount'=>288.68],
        ['id_member'=>715,'amount'=>194.21],
        ['id_member'=>432,'amount'=>754.93],
        ['id_member'=>767,'amount'=>162.51],
        ['id_member'=>434,'amount'=>210.95],
        ['id_member'=>435,'amount'=>191.49],
        ['id_member'=>655,'amount'=>92.28],
        ['id_member'=>86,'amount'=>974.19],
        ['id_member'=>702,'amount'=>634.64],
        ['id_member'=>415,'amount'=>2285.95],
        ['id_member'=>137,'amount'=>1189.06],
        ['id_member'=>317,'amount'=>1255.46],
        ['id_member'=>626,'amount'=>446.11],
        ['id_member'=>713,'amount'=>319.49],
        ['id_member'=>712,'amount'=>141.42],
        ['id_member'=>711,'amount'=>188.42],
        ['id_member'=>302,'amount'=>246.51],
        ['id_member'=>239,'amount'=>226.98],
        ['id_member'=>654,'amount'=>81.75],
        ['id_member'=>173,'amount'=>659.89],
        ['id_member'=>443,'amount'=>472.37],
        ['id_member'=>662,'amount'=>13.2],
        ['id_member'=>157,'amount'=>1336.13],
        ['id_member'=>303,'amount'=>211.46],
        ['id_member'=>128,'amount'=>757.98],
        ['id_member'=>130,'amount'=>223.85],
        ['id_member'=>350,'amount'=>234.36],
        ['id_member'=>444,'amount'=>1092.49],
        ['id_member'=>235,'amount'=>439.69],
        ['id_member'=>238,'amount'=>1231.02],
        ['id_member'=>646,'amount'=>25.28],
        ['id_member'=>445,'amount'=>1286.82],
        ['id_member'=>446,'amount'=>572.63],
        ['id_member'=>29,'amount'=>687.16],
        ['id_member'=>2,'amount'=>812.28],
        ['id_member'=>40,'amount'=>1055.99],
        ['id_member'=>512,'amount'=>53.52],
        ['id_member'=>710,'amount'=>108.86],
        ['id_member'=>577,'amount'=>772.82],
        ['id_member'=>447,'amount'=>152.91],
        ['id_member'=>709,'amount'=>289.65],
        ['id_member'=>349,'amount'=>288.87],
        ['id_member'=>72,'amount'=>231.77],
        ['id_member'=>75,'amount'=>1087.36],
        ['id_member'=>579,'amount'=>772.82],
        ['id_member'=>448,'amount'=>347.74],
        ['id_member'=>131,'amount'=>347.17],
        ['id_member'=>7,'amount'=>548.97],
        ['id_member'=>204,'amount'=>735.84],
        ['id_member'=>610,'amount'=>46107],
        ['id_member'=>650,'amount'=>189.5],
        ['id_member'=>205,'amount'=>636.31],
        ['id_member'=>226,'amount'=>438.82],
        ['id_member'=>703,'amount'=>197.74],
        ['id_member'=>449,'amount'=>583.3],
        ['id_member'=>153,'amount'=>632.23],
        ['id_member'=>638,'amount'=>130.36],
        ['id_member'=>450,'amount'=>649.39],
        ['id_member'=>240,'amount'=>254.35],
        ['id_member'=>17,'amount'=>740.26],
        ['id_member'=>188,'amount'=>523.13],
        ['id_member'=>189,'amount'=>197.93],
        ['id_member'=>451,'amount'=>1642.17],
        ['id_member'=>236,'amount'=>270.85],
        ['id_member'=>66,'amount'=>716.36],
        ['id_member'=>65,'amount'=>1050.31],
        ['id_member'=>254,'amount'=>311.96],
        ['id_member'=>452,'amount'=>608.4],
        ['id_member'=>60,'amount'=>291.2],
        ['id_member'=>453,'amount'=>1352.89],
        ['id_member'=>454,'amount'=>535.26],
        ['id_member'=>513,'amount'=>6.29],
        ['id_member'=>566,'amount'=>280.39],
        ['id_member'=>196,'amount'=>214.81],
        ['id_member'=>455,'amount'=>271.28],
        ['id_member'=>456,'amount'=>1238.2],
        ['id_member'=>627,'amount'=>408.46],
        ['id_member'=>178,'amount'=>841.44],
        ['id_member'=>179,'amount'=>1397.1],
        ['id_member'=>567,'amount'=>282.98],
        ['id_member'=>197,'amount'=>599.69],
        ['id_member'=>219,'amount'=>1261.94],
        ['id_member'=>213,'amount'=>174.38],
        ['id_member'=>286,'amount'=>764.8],
        ['id_member'=>707,'amount'=>387.1],
        ['id_member'=>258,'amount'=>2133.15],
        ['id_member'=>53,'amount'=>264.2],
        ['id_member'=>665,'amount'=>12.64],
        ['id_member'=>21,'amount'=>404.12],
        ['id_member'=>329,'amount'=>279.25],
        ['id_member'=>457,'amount'=>1003.37],
        ['id_member'=>330,'amount'=>217.01],
        ['id_member'=>739,'amount'=>844.61],
        ['id_member'=>31,'amount'=>630.21],
        ['id_member'=>740,'amount'=>401.57],
        ['id_member'=>459,'amount'=>637.59],
        ['id_member'=>741,'amount'=>178.17],
        ['id_member'=>67,'amount'=>754],
        ['id_member'=>276,'amount'=>173.85],
        ['id_member'=>143,'amount'=>485.07],
        ['id_member'=>229,'amount'=>343.81],
        ['id_member'=>230,'amount'=>158.46],
        ['id_member'=>43,'amount'=>213.24],
        ['id_member'=>514,'amount'=>243.15],
        ['id_member'=>365,'amount'=>647.25],
        ['id_member'=>644,'amount'=>289.07],
        ['id_member'=>742,'amount'=>68.46],
        ['id_member'=>165,'amount'=>570.7],
        ['id_member'=>743,'amount'=>46.72],
        ['id_member'=>33,'amount'=>498.92],
        ['id_member'=>54,'amount'=>228.56],
        ['id_member'=>32,'amount'=>729.44],
        ['id_member'=>460,'amount'=>1402.62],
        ['id_member'=>34,'amount'=>208.91],
        ['id_member'=>568,'amount'=>1051.57],
        ['id_member'=>569,'amount'=>220.75],
        ['id_member'=>515,'amount'=>113.6],
        ['id_member'=>516,'amount'=>117.9],
        ['id_member'=>461,'amount'=>885.31],
        ['id_member'=>745,'amount'=>1121.08],
        ['id_member'=>517,'amount'=>187.86],
        ['id_member'=>241,'amount'=>327.12],
        ['id_member'=>356,'amount'=>188.49],
        ['id_member'=>570,'amount'=>271.94],
        ['id_member'=>55,'amount'=>435.23],
        ['id_member'=>605,'amount'=>816.65],
        ['id_member'=>518,'amount'=>62.04],
        ['id_member'=>463,'amount'=>576.09],
        ['id_member'=>746,'amount'=>66.21],
        ['id_member'=>519,'amount'=>823.09],
        ['id_member'=>520,'amount'=>246.52],
        ['id_member'=>747,'amount'=>36.91],
        ['id_member'=>228,'amount'=>921.91],
        ['id_member'=>667,'amount'=>176.14],
        ['id_member'=>616,'amount'=>496.39],
        ['id_member'=>351,'amount'=>264.48],
        ['id_member'=>331,'amount'=>279.91],
        ['id_member'=>522,'amount'=>640.18],
        ['id_member'=>332,'amount'=>205.95],
        ['id_member'=>268,'amount'=>1680.17],
        ['id_member'=>749,'amount'=>23.39],
        ['id_member'=>8,'amount'=>650.38],
        ['id_member'=>26,'amount'=>634.61],
        ['id_member'=>523,'amount'=>245.52],
        ['id_member'=>524,'amount'=>105.12],
        ['id_member'=>170,'amount'=>576.88],
        ['id_member'=>113,'amount'=>1048.01],
        ['id_member'=>612,'amount'=>309.1],
        ['id_member'=>383,'amount'=>1198.49],
        ['id_member'=>281,'amount'=>734.55],
        ['id_member'=>389,'amount'=>978.11],
        ['id_member'=>312,'amount'=>491.27],
        ['id_member'=>145,'amount'=>267.07],
        ['id_member'=>27,'amount'=>1036.74],
        ['id_member'=>22,'amount'=>297.64],
        ['id_member'=>394,'amount'=>929.49],
        ['id_member'=>146,'amount'=>801.59],
        ['id_member'=>147,'amount'=>263.02],
        ['id_member'=>166,'amount'=>304.93],
        ['id_member'=>397,'amount'=>337.21],
        ['id_member'=>750,'amount'=>293.81],
        ['id_member'=>751,'amount'=>53.74],
        ['id_member'=>57,'amount'=>438.15],
        ['id_member'=>180,'amount'=>812.89],
        ['id_member'=>313,'amount'=>245.47],
        ['id_member'=>4,'amount'=>1140.26],
        ['id_member'=>402,'amount'=>365.25],
        ['id_member'=>752,'amount'=>228.98],
        ['id_member'=>249,'amount'=>608.43],
        ['id_member'=>96,'amount'=>789.08],
        ['id_member'=>198,'amount'=>381.67],
        ['id_member'=>405,'amount'=>895.47],
        ['id_member'=>105,'amount'=>241.36],
        ['id_member'=>407,'amount'=>554.17],
        ['id_member'=>282,'amount'=>35.23],
        ['id_member'=>410,'amount'=>635.32],
        ['id_member'=>413,'amount'=>757.09],
        ['id_member'=>207,'amount'=>289.49],
        ['id_member'=>571,'amount'=>668.21],
        ['id_member'=>572,'amount'=>207.25],
        ['id_member'=>417,'amount'=>1011.15],
        ['id_member'=>201,'amount'=>1157.86],
        ['id_member'=>421,'amount'=>850.3],
        ['id_member'=>753,'amount'=>1140.11],
        ['id_member'=>423,'amount'=>703.23],
        ['id_member'=>325,'amount'=>1298.49],
        ['id_member'=>525,'amount'=>1670.51],
        ['id_member'=>88,'amount'=>262.76],
        ['id_member'=>425,'amount'=>176.04],
        ['id_member'=>89,'amount'=>532.11],
        ['id_member'=>589,'amount'=>336.58],
        ['id_member'=>190,'amount'=>761.37],
        ['id_member'=>426,'amount'=>80.17],
        ['id_member'=>634,'amount'=>194.14],
        ['id_member'=>162,'amount'=>634.61],
        ['id_member'=>649,'amount'=>302.41],
        ['id_member'=>647,'amount'=>232.61],
        ['id_member'=>427,'amount'=>1213.18],
        ['id_member'=>362,'amount'=>584.66],
        ['id_member'=>181,'amount'=>295.32],
        ['id_member'=>651,'amount'=>12.64],
        ['id_member'=>428,'amount'=>460.44],
        ['id_member'=>754,'amount'=>186.17],
        ['id_member'=>138,'amount'=>856.92],
        ['id_member'=>174,'amount'=>879.86],
        ['id_member'=>526,'amount'=>760.15],
        ['id_member'=>206,'amount'=>196.05],
        ['id_member'=>527,'amount'=>344.11],
        ['id_member'=>429,'amount'=>1688.86],
        ['id_member'=>333,'amount'=>362.55],
        ['id_member'=>199,'amount'=>562.28],
        ['id_member'=>348,'amount'=>512.64],
        ['id_member'=>433,'amount'=>959.09],
        ['id_member'=>215,'amount'=>724.88],
        ['id_member'=>220,'amount'=>1571.43],
        ['id_member'=>148,'amount'=>150.28],
        ['id_member'=>294,'amount'=>668.83],
        ['id_member'=>573,'amount'=>267.02],
        ['id_member'=>580,'amount'=>993.25],
        ['id_member'=>590,'amount'=>613.11],
        ['id_member'=>424,'amount'=>1333.4],
        ['id_member'=>611,'amount'=>309.1],
        ['id_member'=>755,'amount'=>37.82],
        ['id_member'=>56,'amount'=>1005.99],
        ['id_member'=>756,'amount'=>292.29],
        ['id_member'=>288,'amount'=>601.46],
        ['id_member'=>338,'amount'=>875.13],
        ['id_member'=>100,'amount'=>407.17],
        ['id_member'=>334,'amount'=>232.79],
        ['id_member'=>98,'amount'=>156.6],
        ['id_member'=>62,'amount'=>352.65],
        ['id_member'=>95,'amount'=>346.48],
        ['id_member'=>381,'amount'=>900.73],
        ['id_member'=>648,'amount'=>81.98],
        ['id_member'=>575,'amount'=>551.72],
        ['id_member'=>436,'amount'=>204.09],
        ['id_member'=>437,'amount'=>378.73],
        ['id_member'=>214,'amount'=>111.75],
        ['id_member'=>277,'amount'=>224.7],
        ['id_member'=>108,'amount'=>386.74],
        ['id_member'=>438,'amount'=>377.83],
        ['id_member'=>18,'amount'=>247.78],
        ['id_member'=>757,'amount'=>263.85],
        ['id_member'=>630,'amount'=>358.18],
        ['id_member'=>243,'amount'=>22.59],
        ['id_member'=>758,'amount'=>256.24],
        ['id_member'=>171,'amount'=>521.58],
        ['id_member'=>528,'amount'=>338.11],
        ['id_member'=>259,'amount'=>1308.5],
        ['id_member'=>260,'amount'=>1143.82],
        ['id_member'=>696,'amount'=>227.56],
        ['id_member'=>615,'amount'=>496.39],
        ['id_member'=>759,'amount'=>268.1],
        ['id_member'=>255,'amount'=>554.45],
        ['id_member'=>244,'amount'=>470.67],
        ['id_member'=>760,'amount'=>29.41],
        ['id_member'=>242,'amount'=>219.71],
        ['id_member'=>761,'amount'=>70.3],
        ['id_member'=>439,'amount'=>938.3],
        ['id_member'=>591,'amount'=>568.85],
        ['id_member'=>440,'amount'=>328.68],
        ['id_member'=>659,'amount'=>270.19],
        ['id_member'=>762,'amount'=>20.03],
        ['id_member'=>304,'amount'=>254.38],
        ['id_member'=>441,'amount'=>318.35],
        ['id_member'=>109,'amount'=>1059.8],
        ['id_member'=>110,'amount'=>697.51],
        ['id_member'=>326,'amount'=>772.82],
        ['id_member'=>51,'amount'=>1146.32],
        ['id_member'=>529,'amount'=>711.42],
        ['id_member'=>305,'amount'=>179.86],
        ['id_member'=>308,'amount'=>675.48],
        ['id_member'=>306,'amount'=>500.37],
        ['id_member'=>763,'amount'=>174.74],
        ['id_member'=>363,'amount'=>631.38],
        ['id_member'=>115,'amount'=>1056.72],
        ['id_member'=>530,'amount'=>569.71],
        ['id_member'=>531,'amount'=>165.03],
        ['id_member'=>532,'amount'=>300.34],
        ['id_member'=>533,'amount'=>793.25],
        ['id_member'=>172,'amount'=>425.69],
        ['id_member'=>534,'amount'=>164.24],
        ['id_member'=>346,'amount'=>919.44],
        ['id_member'=>339,'amount'=>582.21],
        ['id_member'=>535,'amount'=>249.63],
        ['id_member'=>267,'amount'=>257.95],
        ['id_member'=>536,'amount'=>109.93],
        ['id_member'=>576,'amount'=>586.4],
        ['id_member'=>371,'amount'=>429.11],
        ['id_member'=>537,'amount'=>703.61],
        ['id_member'=>764,'amount'=>74.64],
        ['id_member'=>631,'amount'=>501.23],
        ['id_member'=>582,'amount'=>1068.54],
        ['id_member'=>538,'amount'=>1283.45],
        ['id_member'=>539,'amount'=>1267.75],
        ['id_member'=>63,'amount'=>259.51],
        ['id_member'=>340,'amount'=>974.15],
        ['id_member'=>540,'amount'=>265.78],
        ['id_member'=>182,'amount'=>1068.64],
        ['id_member'=>542,'amount'=>734.25],
        ['id_member'=>543,'amount'=>760.27],
        ['id_member'=>442,'amount'=>194.84],
        ['id_member'=>541,'amount'=>365.89],
        ['id_member'=>521,'amount'=>402.74]


        );
        dd($members);
        DB::table('temp_cbu_retention')
        ->insert($members);

        return;



        $JV = new JournalVoucherController();
        foreach($members as $m){
            $r = [
                'jv_parent' =>[
                    "jv_type" => "1",
                    "date" => "2026-03-18",
                    "payee_type" => "2",
                    "payee_reference" => $m['id_member'],
                    "address" => DB::table('member')->select('address')->where('id_member',$m['id_member'])->first()->address ?? null,
                    "id_branch" => "1",
                    "reference" => null,
                    "description" => "TO RECORD CBU ADJUSTMENT",
                    "type" => 11
                ],
                'chart_entry'=>[
                    [
                      "id_chart_account" => "19",
                      "debit" => $m['amount'],
                      "credit" => "0",
                      "details" => null
                    ],
                    [
                      "id_chart_account" => "28",
                      "debit" => "0",
                      "credit" => $m['amount'],
                      "details" => null
                    ]
                ],
                "opcode"=>0,
                "id_journal_voucher"=>0

            ];
            $JV->post(new Request($r));
        }

        dd("SUCCESS");



        dd($r);
    }
}


