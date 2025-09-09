<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\MySession;
use App\CDVModel;
use PDF;


class InvestmentWithdrawalController extends Controller
{
    public function index(Request $request){

        // $data['investment_withdrawals'] = DB::table('investment_withdrawal as iw')
        //                                   ->select(DB::raw("iw.id_investment_withdrawal,iw.id_investment,iw.amount,ip.product_name,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as investor,DATE_FORMAT(iw.date_released,'%m/%d/%Y') as date_released,DATE_FORMAT(iw.date_created,'%m/%d/%Y %r') as date_created,iw.status,if(iw.status=0,'Draft',if(iw.status=1,'Released','Cancelled')) as status_description,iw.id_cash_disbursement"))
        //                                   ->leftJoin('investment as i','i.id_investment','iw.id_investment')
        //                                   ->leftJoin('investment_product as ip','ip.id_investment_product','i.id_investment_product')
        //                                   ->leftJoin('member as m','m.id_member','iw.id_member')
        //                                   ->orderBy('iw.id_investment_withdrawal','DESC')
        //                                   ->get();

        $data['investment_withdrawal'] = DB::table('investment_withdrawal_batch as iw')
                                         ->select(DB::raw("iw.id_investment_withdrawal_batch,iw.total_amount,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as investor,DATE_FORMAT(iw.date_created,'%m/%d/%Y') as date_created,iw.status,if(iw.status=0,'Draft',if(iw.status=1,'Released','Cancelled')) as status_description,DATE_FORMAT(iw.date_released,'%m/%d/%Y') as date_released,if(type=2,'Early Termination','') as note"))
                                         ->leftJoin('member as m','m.id_member','iw.id_member')
                                         ->where('type','<',3)
                                         ->orderBy('iw.id_investment_withdrawal_batch','DESC')
                                         ->get();

        $data['head_title'] = "Investor Withdrawal";
        // dd($data['investment_withdrawal']);
        return view('investment-withdrawal.index',$data);
    }
    public function create(Request $request){
        // $cur_date = MySession::current_date();
        $data['opcode'] = 0;
        $data['cur_date']=$cur_date = MySession::current_date();
        // $param = array(
        //     'date1'=>$cur_date,
        //     'date2'=>$cur_date,
        //     'mat_date1'=>$cur_date,
        //     'mat_date2'=>$cur_date
        // );
        // $withdrawables = DB::select("
        // SELECT *,(investment_balance-withdrawn) as withdrawable FROM (
        // SELECT i.id_member,i.id_investment,ip.product_name,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as investor,SUM(interest_amount) as total_interest,i.amount as investment_amount,max(it.date) as i_date,
        // CASE WHEN i.id_withdrawable_part = 1 THEN (i.amount + if(:mat_date1 >= i.maturity_date,SUM(interest_amount),0))
        // WHEN i.id_withdrawable_part = 2 THEN (SUM(interest_amount) + if(:mat_date2 >= i.maturity_date,i.amount,0))
        // ELSE SUM(interest_amount)+i.amount
        // END AS investment_balance,getInvestmentTotalWithdraw(i.id_investment,0,0,2) as withdrawn_principal,DATE_FORMAT(i.maturity_date,'%m/%d/%Y') as maturity_date,i.amount as principal,SUM(interest_amount) as interest_balance
        // from investment as i 
        // LEFT JOIN investment_table as it on it.id_investment = i.id_investment
        // LEFT JOIN investment_product as ip on ip.id_investment_product = i.id_investment_product
        // LEFT JOIN member as m on m.id_member = i.id_member
        // where i.status = 2 AND i.renewal_status = 0 AND (i.withdraw_before_maturity=1 OR (i.withdraw_before_maturity = 0 AND i.maturity_date <= :date1 )) AND it.date <= :date2
        // GROUP BY i.id_investment) as inv
        // WHERE (investment_balance-withdrawn) > 0;",$param);
        $data['head_title'] = "Create Investment Withdrawal";
        $param = array_fill(0,4,$cur_date);
        $withdrawables = DB::select("SELECT id_member,id_investment,product_name,investor,maturity_date,investment_amount,
                        (principal_avail-withdrawn_principal) as withdrawable_principal,(interest_avail-withdrawn_interest) as withdrawable_interest,
                        ((principal_avail+interest_avail) - (withdrawn_principal+withdrawn_interest)) as withdrawable
                        FROM (  
                            SELECT i.id_member,i.id_investment,ip.product_name,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as investor,i.amount as investment_amount,SUM(interest_amount) as total_interest,max(it.date) as i_date,
                            CASE 
                                WHEN i.id_withdrawable_part = 1 THEN (i.amount)
                                WHEN i.id_withdrawable_part = 2 THEN (if(? >= i.maturity_date,i.amount,0))
                                ELSE i.amount
                            END AS principal_avail,    
                            CASE 
                                WHEN i.id_withdrawable_part = 1 THEN (if(? >= i.maturity_date,SUM(interest_amount),0))
                                WHEN i.id_withdrawable_part = 2 THEN (SUM(interest_amount))
                                ELSE SUM(interest_amount)
                            END AS interest_avail,    
                            getInvestmentTotalWithdraw(i.id_investment,0,0,2) as withdrawn_principal,getInvestmentTotalWithdraw(i.id_investment,0,0,3) as withdrawn_interest,DATE_FORMAT(i.maturity_date,'%m/%d/%Y') as maturity_date
                            from investment as i 
                            LEFT JOIN investment_table as it on it.id_investment = i.id_investment
                            LEFT JOIN investment_product as ip on ip.id_investment_product = i.id_investment_product
                            LEFT JOIN member as m on m.id_member = i.id_member
                            where i.status = 2 AND i.renewal_status = 0 AND (i.withdraw_before_maturity=1 OR (i.withdraw_before_maturity = 0 AND i.maturity_date <= ? )) AND it.date <= ?
                            GROUP BY i.id_investment
                        ) as investment
                        WHERE ((principal_avail+interest_avail) - (withdrawn_principal+withdrawn_interest)) > 0",$param);


        $g = new GroupArrayController();
        $data['withdrawables'] = $g->array_group_by($withdrawables,['id_member']);

        return view('investment-withdrawal.form',$data);
    }

    public function view($id_investment_withdrawal_batch,Request $request){
        $data['cur_date']=$cur_date = MySession::current_date();

        $data['opcode'] = 1;

        $param = array(
            'mat_date1'=>$cur_date,
            'mat_date2'=>$cur_date,
            'date1'=>$cur_date,
            'date2'=>$cur_date,
            'id_investment_withdrawal_batch'=>$id_investment_withdrawal_batch
        );


        $data['batch_details'] = DB::table('investment_withdrawal_batch')
                                 ->where('id_investment_withdrawal_batch',$id_investment_withdrawal_batch)
                                 ->first();
        $data['head_title'] = "Investment withdrawal [Batch $id_investment_withdrawal_batch]";
                                 // dd($data);
        $data['withdrawals'] = DB::table('investment_withdrawal as iw')
                               ->select(DB::raw("iw.id_investment_withdrawal,iw.id_investment,iw.id_member,iw.total_amount as amount,ip.product_name,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as investor,iw.id_cash_disbursement,iw.status,i.maturity_date,if(iw.status=1,'Released','Cancelled') as status_description,iw.status"))
                               ->leftJoin('investment as i','i.id_investment','iw.id_investment')
                               ->leftJoin('investment_product as ip','ip.id_investment_product','i.id_investment_product')
                               ->leftJoin('member as m','m.id_member','i.id_member')
                               ->where('iw.id_investment_withdrawal_batch',$id_investment_withdrawal_batch)
                               ->get();


        if($data['batch_details']->status == 0 && $data['batch_details']->close_request == 0){

            $withdrawables = DB::select("SELECT id_member,id_investment,product_name,investor,maturity_date,investment_amount,
                        (principal_avail-withdrawn_principal) as withdrawable_principal,(interest_avail-withdrawn_interest) as withdrawable_interest,
                        ((principal_avail+interest_avail) - (withdrawn_principal+withdrawn_interest)) as withdrawable,this_withdrawal
                        FROM (  
                            SELECT i.id_member,i.id_investment,ip.product_name,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as investor,i.amount as investment_amount,SUM(interest_amount) as total_interest,max(it.date) as i_date,
                            CASE 
                                WHEN i.id_withdrawable_part = 1 THEN (i.amount)
                                WHEN i.id_withdrawable_part = 2 THEN (if(:mat_date1 >= i.maturity_date,i.amount,0))
                                ELSE i.amount
                            END AS principal_avail,    
                            CASE 
                                WHEN i.id_withdrawable_part = 1 THEN (if(:mat_date2 >= i.maturity_date,SUM(interest_amount),0))
                                WHEN i.id_withdrawable_part = 2 THEN (SUM(interest_amount))
                                ELSE SUM(interest_amount)
                            END AS interest_avail,    
                            getInvestmentTotalWithdraw(i.id_investment,ifnull(iw.id_investment_withdrawal,0),0,2) as withdrawn_principal,getInvestmentTotalWithdraw(i.id_investment,ifnull(iw.id_investment_withdrawal,0),0,3) as withdrawn_interest,DATE_FORMAT(i.maturity_date,'%m/%d/%Y') as maturity_date,ifnull(iw.total_amount,0) as this_withdrawal
                            from investment as i 
                            LEFT JOIN investment_table as it on it.id_investment = i.id_investment
                            LEFT JOIN investment_product as ip on ip.id_investment_product = i.id_investment_product
                            LEFT JOIN member as m on m.id_member = i.id_member
                            LEFT JOIN investment_withdrawal as iw on  iw.id_investment =i.id_investment and iw.id_investment_withdrawal_batch = :id_investment_withdrawal_batch
                            where i.status = 2 AND i.renewal_status = 0 AND (i.withdraw_before_maturity=1 OR (i.withdraw_before_maturity = 0 AND i.maturity_date <= :date1 )) AND it.date <= :date2
                            GROUP BY i.id_investment
                        ) as investment
                        WHERE ((principal_avail+interest_avail) - (withdrawn_principal+withdrawn_interest)) > 0",$param);
            // dd($withdrawables);

            $g = new GroupArrayController();
            $data['withdrawables'] = $g->array_group_by($withdrawables,['id_member']);         



            return view('investment-withdrawal.form',$data);   
        }else{
            $data['allow_cancel_ind'] = (MySession::isSuperAdmin())? true:false;
            $data['allow_status_close_request'] = (MySession::isAdmin() && $data['batch_details']->close_request == 1)?true:false;
        }

        return view('investment-withdrawal.form-view-batch',$data);


        dd($data);
    }

    public function post(Request $request){
        $data['RESPONSE_CODE'] = "SUCCESS";
        $post_data = $request->post_data ?? [];

        // dd($request->all());
        $opcode = $request->opcode;
        $data['OPCODE'] = $opcode;
        $id_investment_withdrawal_batch = $request->id_investment_withdrawal_batch ?? 0;

        if(count($post_data) == 0){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Invalid Request";
            return response($data);
        }

        $id_investments = array();
  
        foreach($post_data as $pd=>$item){
            array_push($id_investments,$item['id_investment']);
        }  
        $validation_data = $this->validation_data($id_investments,$id_investment_withdrawal_batch);
        // dd($validation_data);
      
        $posted_withdrawal = array();
        $invalid_withrawal = array();

        $total_batch_withdrawal = 0;

        foreach($post_data as $item){
            $v = $validation_data[$item['id_investment']][0] ?? null;


            if(isset($v)){
                $total_withdrawable = round(($v->withdrawable_principal+$v->withdrawable_interest),2);
            }

            


            if(isset($v) && round($item['amount'],2) <= $total_withdrawable){
                $item['id_member'] = $v->id_member;

                $total_batch_withdrawal += $item['amount'];
                if($opcode == 0){
                    $item['id_user'] = MySession::myId();
                }

                //mapping the interest and principal
                $amt = $item['amount'];
                if($v->withdrawable_interest > 0){
                    $interest_val = ($amt >= ROUND($v->withdrawable_interest,2))?ROUND($v->withdrawable_interest,2):$amt;
                }else{
                    $interest_val = 0;
                }
             
                $principal_val = $amt-$interest_val;

                $item['total_amount'] = $item['amount'];
                unset($item['amount']);
                $item['interest'] = $interest_val;
                $item['principal'] = $principal_val;

                array_push($posted_withdrawal,$item);

            }else{
                $t = array();
                $t['id_investment'] = $item['id_investment'];
                $t['valid_amount'] = $total_withdrawable ?? 0;
                $t['amount_input'] = round($item['amount'],2);
                array_push($invalid_withrawal,$t);
            }
        }

        if(count($invalid_withrawal) > 0){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Invalid Amount. Please Check the Entered Withdrawal Amount";
            $data['INVALID_INPUTS'] = $invalid_withrawal;

            return response($data);
        }

        $batch_obj['id_member'] = count($posted_withdrawal) > 1 ? 0 : $posted_withdrawal[0]['id_member'];
        $batch_obj['total_amount'] = $total_batch_withdrawal;


        if($opcode == 0){
            // insert withdrawal batch
            $batch_obj['id_user'] = MySession::myId();

            DB::table("investment_withdrawal_batch")
            ->insert($batch_obj);

            $id_investment_withdrawal_batch = DB::table('investment_withdrawal_batch')->max('id_investment_withdrawal_batch');
        }else{
            DB::table('investment_withdrawal_batch')
            ->where('id_investment_withdrawal_batch',$id_investment_withdrawal_batch)
            ->update($batch_obj);

            DB::table('investment_withdrawal')
            ->where('id_investment_withdrawal_batch',$id_investment_withdrawal_batch)
            ->delete();
        }

        for($i=0;$i<count($posted_withdrawal);$i++){
            $posted_withdrawal[$i]['id_investment_withdrawal_batch'] = $id_investment_withdrawal_batch;
        }

        DB::table('investment_withdrawal')
        ->insert($posted_withdrawal);


        $data['id_investment_withdrawal_batch'] = $id_investment_withdrawal_batch;

        return response($data);
    }

    public function post_status(Request $request){
        if($request->ajax()){
            $id_investment_withdrawal_batch = $request->id_investment_withdrawal_batch;
            $status = $request->status;
            $reason = $request->reason;
            $date_released = $request->date_released;



            $data['RESPONSE_CODE'] = "SUCCESS";
            $data['is_confirmed'] = false;

            $inv_details = DB::table('investment_withdrawal_batch')->where('id_investment_withdrawal_batch',$id_investment_withdrawal_batch)->first();

  


            $investments = DB::table('investment_withdrawal')
                           ->select(DB::raw('id_investment,id_investment_withdrawal'))
                           ->where('id_investment_withdrawal_batch',$id_investment_withdrawal_batch)
                           ->get();

            // dd($investments);
            if($inv_details->status > 0){
                $data['RESPONSE_CODE'] = "ERROR";
                $data['message'] = "Invalid Request";
                return response($data);
            }
            DB::table('investment_withdrawal_batch')
            ->where('id_investment_withdrawal_batch',$id_investment_withdrawal_batch)
            ->update([
                'status'=>$status,
                'id_user_status'=>MySession::myId(),
                'status_date'=>DB::raw('now()'),
                'cancellation_remarks'=>$reason,
                'date_released'=>$date_released
            ]);
            DB::table('investment_withdrawal')
            ->where('id_investment_withdrawal_batch',$id_investment_withdrawal_batch)
            ->update([
                'status'=>$status
            ]);
           
            if($status == 1){
                foreach($investments as $inv){
                    CDVModel::InvestmentWithdrawalCDV($inv->id_investment_withdrawal);
                }
                
                $data['is_confirmed'] = true;
            }
            foreach($investments as $inv){
                
                $date_closed = MySession::current_date();
                if($inv_details->close_request == 1){
                    //force withdrawal
                    if($status == 1){

                        DB::table('investment')
                        ->where('id_investment',$inv->id_investment)
                        ->update(['status'=>5,'close_request'=>3]);
                        
                        // date created when close withdrawal request made
                        $date_closed =  DB::table('investment_close_request')
                                        ->select(DB::raw("DATE(date_created) as date"))
                                        ->where('id_investment_withdrawal_batch',$id_investment_withdrawal_batch)
                                        ->first()->date;

                        DB::table('investment_close_request')
                        ->where('id_investment',$inv->id_investment)
                        ->where('status',1)
                        ->where('id_investment_withdrawal_batch',$id_investment_withdrawal_batch)
                        ->update(['status'=>2]);                       
                    }else{
                        $investment_c = new InvestmentController();
                        $investment_c->cancel_withdrawal_request($inv->id_investment,$reason);
                    }

                }else{
                    //normal withdrawal
                    $this->update_investment_status($inv->id_investment,$inv_details->close_request);
                }

                DB::table('investment')
                ->where('id_investment',$inv->id_investment)
                ->update(['date_closed'=>DB::raw("if(status=5,'$date_closed',null)")]);
            }

            return response($data);
        }
    }   

    public function update_investment_status($id_investment){

            DB::select("UPDATE  (
            SELECT i.id_investment,i.amount+SUM(it.interest_amount) as total_inv_amount FROM investment as i
            LEFT JOIN investment_table as it on it.id_investment = i.id_investment
            WHERE i.id_investment = ?
            GROUP BY i.id_investment) as h
            LEFT JOIN investment as i on i.id_investment = h.id_investment
            SET i.status = if(getInvestmentTotalWithdraw(i.id_investment,0,1,1) = h.total_inv_amount,5,2);",[$id_investment]);    

    }

    public function validation_data($id_investments,$id_investment_withdrawal_batch){
        $data['cur_date']=$cur_date = MySession::current_date();

        $param = [$cur_date,$cur_date,$id_investment_withdrawal_batch,$cur_date,$cur_date];
        $placeholder_id_investments = implode(', ', array_fill(0, count($id_investments), '?'));

        foreach($id_investments as $i){
            array_push($param,$i);
        }

        // $withdrawables = DB::select("SELECT i.id_member,i.id_investment,SUM(interest_amount) as total_interest,i.amount as investment_amount,max(it.date) as i_date,
        // CASE WHEN i.id_withdrawable_part = 1 THEN (i.amount + if(? >= i.maturity_date,SUM(interest_amount),0))
        // WHEN i.id_withdrawable_part = 2 THEN (SUM(interest_amount) + if(? >= i.maturity_date,i.amount,0))
        // ELSE SUM(interest_amount)+i.amount
        // END - getInvestmentTotalWithdraw(i.id_investment,iw.id_investment_withdrawal,0) AS withdrawable
        // from investment as i 
        // LEFT JOIN investment_table as it on it.id_investment = i.id_investment
        // LEFT JOIN investment_withdrawal as iw on iw.id_investment_withdrawal_batch = ? AND iw.id_investment = i.id_investment
        // where i.status = 2 AND i.renewal_status = 0 AND (i.withdraw_before_maturity=1 OR (i.withdraw_before_maturity = 0 AND i.maturity_date <= ? )) AND it.date <= ? AND i.id_investment in ($placeholder_id_investments)
        // GROUP BY i.id_investment;",$param);   
        $withdrawables = DB::select("SELECT i.id_member,i.id_investment,SUM(interest_amount) as total_interest,i.amount as investment_amount,max(it.date) as i_date,
                                        CASE 
                                            WHEN i.id_withdrawable_part = 1 THEN (i.amount)
                                            WHEN i.id_withdrawable_part = 2 THEN (if(? >= i.maturity_date,i.amount,0))
                                            ELSE i.amount
                                        END - getInvestmentTotalWithdraw(i.id_investment,ifnull(iw.id_investment_withdrawal,0),0,2) AS withdrawable_principal,    
                                        CASE 
                                            WHEN i.id_withdrawable_part = 1 THEN (if(? >= i.maturity_date,SUM(interest_amount),0))
                                            WHEN i.id_withdrawable_part = 2 THEN (SUM(interest_amount))
                                            ELSE SUM(interest_amount)
                                        END - getInvestmentTotalWithdraw(i.id_investment,ifnull(iw.id_investment_withdrawal,0),0,3) AS withdrawable_interest
                                    from investment as i 
                                    LEFT JOIN investment_table as it on it.id_investment = i.id_investment
                                    LEFT JOIN investment_withdrawal as iw on iw.id_investment_withdrawal_batch = ? AND iw.id_investment = i.id_investment
                                    where i.status = 2 AND i.renewal_status = 0 AND (i.withdraw_before_maturity=1 OR (i.withdraw_before_maturity = 0 AND i.maturity_date <= ? )) AND it.date <= ? AND i.id_investment in ($placeholder_id_investments)
                                    GROUP BY i.id_investment;",$param);

        $g = new GroupArrayController();

        return $g->array_group_by($withdrawables,['id_investment']);  
    }

    public function generate_summary(){
        $data['withdrawal_request'] = DB::select("SELECT iw.id_investment_withdrawal,concat(FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)) as member,concat(ip.product_name,' [ID# ',iw.id_investment,']') as product,iw.amount
        FROM investment_withdrawal as iw
        LEFT JOIN member as m on m.id_member = iw.id_member
        LEFT JOIN investment as i on i.id_investment = iw.id_investment
        LEFT JOIN investment_product as ip on ip.id_investment_product = i.id_investment_product;");
    }

    public function cancel_ind(Request $request){
        if($request->all()){
            $details = DB::table('investment_withdrawal')->where('id_investment_withdrawal',$request->id_investment_withdrawal)->first();
            $data['RESPONSE_CODE'] = "SUCCESS";
            if($details->status == 10){
                $data['RESPONSE_CODE'] = "ERROR";
                $data['message'] = "Withdrawal is already cancelled";

                return response($data);
            }

            DB::table('investment_withdrawal')
            ->where('id_investment_withdrawal',$request->id_investment_withdrawal)
            ->update(['status'=>10,
                      'status_date'=>DB::raw("now()"),
                      'cancellation_remarks'=>$request->reason,
                      'id_user_status'=>MySession::myId()]);

            DB::table('cash_disbursement')
            ->where('id_cash_disbursement',$details->id_cash_disbursement)
            ->update([
                'status'=>10,
                'cancellation_reason'=>$request->reason,
                'date_cancelled'=>DB::raw("now()"),
                'description'=>DB::raw("concat(description,' [CANCELLED]')")
            ]);

            $batch_count = DB::table('investment_withdrawal')
                           ->where('id_investment_withdrawal_batch',$details->id_investment_withdrawal_batch)
                           ->where('status','<>',10)
                           ->count();

            if($batch_count == 0){
                DB::table('investment_withdrawal_batch')
                ->where('id_investment_withdrawal_batch',$details->id_investment_withdrawal_batch)
                ->update(['status'=>10,
                          'cancellation_remarks'=>" [CANCELLED INDIVIDUALLY]",
                          'status_date'=>DB::raw("now()"),
                          'id_user_status'=>MySession::myId()]);
            }

            $this->update_investment_status($details->id_investment);

            return response($data);
        }
    }
    public function batch_summary($id_batch){
        if(!MySession::isAdmin()){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }        


        // $data['prime_withdrawals'] = DB::table('prime_withdrawal_batch as pb')
        //                              ->select(DB::raw('prime.id_prime_withdrawal,prime.id_cash_disbursement,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member,amount'))
        //                             ->leftJoin('prime_withdrawal as prime','prime.id_prime_withdrawal_batch','pb.id_prime_withdrawal_batch')
        //                             ->leftJoin('member as m','m.id_member','prime.id_member')
        //                             ->where('prime.status',2)
        //                             ->where('pb.id_prime_withdrawal_batch',$id_batch)
        //                             ->orDerby('pb.transaction_date','ASC')
        //                             ->orDerby('id_prime_withdrawal')
        //                             ->get();    
        // dd($data);  

        $data['details'] = DB::table('investment_withdrawal_batch as iw')
                           ->select(DB::raw("iw.id_investment_withdrawal_batch,DATE_FORMAT(iw.date_released,'%m/%d/%Y') as date_released"))
                           ->where('status',1)
                           ->where('id_investment_withdrawal_batch',$id_batch)
                           ->first();


        $withdrawals = DB::select("SELECT iw.id_investment_withdrawal,iw.id_investment,ip.product_name,UPPER(FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)) as investor,iw.principal,iw.interest,iw.total_amount,iw.id_cash_disbursement
        FROM investment_withdrawal_batch as iwb
        LEFT JOIN investment_withdrawal as iw on iw.id_investment_withdrawal_batch = iwb.id_investment_withdrawal_batch
        LEFT JOIN member as m on m.id_member = iw.id_member
        LEFT JOIN investment as i on i.id_investment = iw.id_investment
        LEFT JOIN investment_product as ip on ip.id_investment_product = i.id_investment_product
        WHERE iwb.id_investment_withdrawal_batch=? AND iw.status <> 10 AND iwb.status =1
        ORDER BY investor,iw.id_investment;",[$id_batch]);

        $g = new GroupArrayController();
        $data['withdrawals'] = $g->array_group_by($withdrawals,['investor']);
    // dd($data);

        $html = view('investment-withdrawal.investment-summary',$data);
        $pdf = PDF::loadHtml($html);
        $pdf->setOption("encoding","UTF-8");
        $pdf->setOption('margin-bottom', '5mm');
        $pdf->setOption('margin-top', '7mm');
        $pdf->setOption('margin-right', '5mm');
        $pdf->setOption('margin-left', '5mm');
        $pdf->setOption('header-left', 'Page [page] of [toPage]');

        $pdf->setOption('header-font-size', 8);
        $pdf->setOption('header-font-name', 'Calibri');

        return $pdf->stream();  
        dd($data); 
    }
}



