<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\MySession;
use App\CredentialModel;
use App\CDVModel;
use PDF;
use App\WebHelper;

class PrimeWithdrawalController extends Controller
{
    public function index(Request $request){
        // $this->recurssion();
        // return;


        $data['credential']= CredentialModel::GetCredential(MySession::myPrivilegeId());
        $data['head_title'] = "Prime Withdrawal";
        if(!$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }

        $data['withdrawals'] = DB::table('prime_withdrawal_batch as prime')
                               ->select(DB::raw("prime.status as status_code,DATE_FORMAT(prime.transaction_date,'%m/%d/%Y') as transaction_date,prime.id_prime_withdrawal_batch,if(prime.reason < 5,cr.description,prime.other_reason) as reason,if(prime.status=0,'Draft',if(prime.status=1,'Approved, For Releasing',if(prime.status=2,'Released',if(prime.status=5,'Disapproved','Cancelled')))) as status,DATE_FORMAT(prime.date_created,'%m/%d/%Y') as date_created,DATE_FORMAT(prime.date_released,'%m/%d/%Y') as date_released,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member,prime.total_amount"))
                               ->leftJoin('prime_withdrawal_reason as cr','cr.id_prime_withdrawal_reason','prime.reason')
                               ->leftJoin('member as m','m.id_member','prime.id_member')
                               ->where(function($query){
                                    if(!MySession::isAdmin()){
                                        $query->where('prime.id_member',MySession::myId());
                                    }
                               })
                               ->orDerby('prime.id_prime_withdrawal_batch','DESC')
                               ->get();


        // $data['withdrawals'] = DB::table('prime_withdrawal as prime')
        //                        ->select(DB::raw("DATE_FORMAT(prime.date_released,'%m/%d/%Y') as date_released,prime.id_prime_withdrawal,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member,if(prime.reason < 4,cr.description,prime.other_reason) as reason,prime.amount,if(prime.status=0,'Draft',if(prime.status=1,'Approved, For Releasing',if(prime.status=2,'Released',if(prime.status=5,'Disapproved','Cancelled')))) as status,prime.id_cash_disbursement,prime.status as status_code,DATE_FORMAT(prime.date_created,'%m/%d/%Y') as date_created"))
        //                        ->leftJoin('member as m','m.id_member','prime.id_member')
        //                        ->leftJoin('prime_withdrawal_reason as cr','cr.id_prime_withdrawal_reason','prime.reason')
        //                        ->where(function($query){
        //                             if(!MySession::isAdmin()){
        //                                 $query->where('prime.id_member',MySession::myId());
        //                             }
        //                        })
        //                        ->orDerby('prime.id_prime_withdrawal','DESC')
        //                        ->get();

        $data['current_date'] = MySession::current_date();
        return view('prime_withdraw.index',$data);

        return $data;
    }

    public function create(Request $request){
        $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/prime_withdraw');
        $data['head_title'] = "Create Prime Withdrawal";
        if(!$data['credential']->is_create){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }

        $data['opcode'] = 0;
        $data['current_date'] = MySession::current_date();
        $data['allow_post'] = true;

        $data['admin_view'] = MySession::isAdmin(); 

        $data['reasons'] = DB::table('prime_withdrawal_reason')->get();



        if(!MySession::isAdmin()){
            $data['prime_list'] = $this->parseMemberPrime([MySession::myId()],0);
            $data['balance'] = isset($data['prime_list'])?$data['prime_list'][MySession::myId()][0]->amount:0;      

        }else{
            $pc = new PrimeController();
            $data['prime_list'] = $pc->prime_data($data['current_date'],true);      

        }


        return view('prime_withdraw.form_bulk',$data);
    }
    public function parseMemberPrime($id_member,$id_prime_withdrawal_batch){
        // $param = array_fill(0,4,$id_member);



        $where_id_member = implode(",",$id_member);

        $data['prime'] = DB::select("SELECT prime_summary.id_member,SUM(credit-debit) as amount,ifnull(pw.amount,0) as withdraw FROM (
        SELECT cd.id_member,concat('CDV# ',cd.id_cash_disbursement) as reference,debit,credit
        FROM cash_disbursement_details as cdv
        LEFT JOIN cash_disbursement as cd on cd.id_cash_disbursement = cdv.id_cash_disbursement
        LEFT JOIN chart_account as ca on ca.id_chart_account = cdv.id_chart_account
        LEFT JOIN cdv_type as ct on ct.id_cdv_type = cd.type
        WHERE cd.status <> 10 and ca.isprime=1  and cd.id_member in ($where_id_member)
        UNION ALL
        SELECT jv.id_member,concat('JV# ',jv.id_journal_voucher) as reference,debit,credit
        FROM journal_voucher_details as jvd
        LEFT JOIN journal_voucher as jv on jv.id_journal_voucher = jvd.id_journal_voucher
        LEFT JOIN chart_account as ca on ca.id_chart_account = jvd.id_chart_account
        LEFT JOIN jv_type as jt on jt.id_jv_type = jv.type
        WHERE jv.status <> 10 and ca.isprime=1 and jv.id_member in ($where_id_member)
        UNION ALL
        SELECT crv.id_member,concat('CRV# ',crv.id_cash_receipt_voucher) as reference,debit,credit
        FROM cash_receipt_voucher_details as crvd
        LEFT JOIN cash_receipt_voucher as crv on crv.id_cash_receipt_voucher = crvd.id_cash_receipt_voucher
        LEFT JOIN chart_account as ca on ca.id_chart_account = crvd.id_chart_account
        LEFT JOIN crv_type as ct on ct.id_crv_type = crv.type
        WHERE crv.status <> 10 and ca.isprime=1 and crv.id_member in ($where_id_member)
        UNION ALL
        SELECT id_member,'-' as reference,0 as debit,amount as credit 
        FROM prime_beginning
        WHERE id_member in ($where_id_member)
        UNION ALL
        SELECT id_member,'-' as reference,amount as debit,0 as credit
        FROM prime_withdrawal as pw
        WHERE pw.status = 1 and id_member in ($where_id_member)
        ) as prime_summary
        LEFT JOIN prime_withdrawal as pw on pw.id_prime_withdrawal_batch = $id_prime_withdrawal_batch AND pw.id_member = prime_summary.id_member and pw.status <> 10
        GROUP BY prime_summary.id_member;");

        // dd($data);



        $g = new GroupArrayController();

        return $g->array_group_by($data['prime'],['id_member']);


        return (count($data['prime']) > 0)?$data['prime'][0]->amount:0;

    }

    public function get_member_prime(Request $request){
        if($request->ajax()){
            $data['prime_amount'] = $this->parseMemberPrime($request->id_member,0);
            return response($data);
        }
    }


    public function post(Request $request){
        $id_reason = $request->id_reason;
        $date = $request->date;
        $withdrawals = $request->withdrawals ?? [];
        $opcode = $request->opcode;

        $id_prime_withdrawal_batch  = $request->id_prime_withdrawal_batch ?? 0; 


        if(!MySession::isAdmin()){
            $withdrawals[0]['id_member'] = MySession::myId();
        }


        $data['RESPONSE_CODE'] = "SUCCESS";

        if(count($withdrawals) == 0){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Please Select atleast 1 withdrawal transaction";

            return response($data);
        }
      
        $validator = $this->parseMemberPrime(array_column($withdrawals,'id_member'),0);


        $invalid = array();
        $post_withdrawal = array();

        $total_batch_withdrawal = 0;

        foreach($withdrawals as $w){
            $id_member = $w['id_member'];
            $balance = floatval($validator[$id_member][0]->amount);
            $amount = floatval($w['amount']);

            if($amount > $balance || $amount <=0){
                array_push($invalid,$id_member);
            }else{
                $temp = array();
                $temp['id_member'] = $id_member;
                $temp['amount'] = $amount;
                $temp['id_prime_withdrawal_batch'] =0;
                $temp['status'] = 0;

                $total_batch_withdrawal += $amount;

                array_push($post_withdrawal,$temp);
            }
        }

        if(count($invalid) > 0){
            $data['RESPONSE_CODE'] = "INVALID_WITHDRAWAL";
            $data['message'] = "Please check the amount";
            $data['invalids'] = $invalid;

            return response($data);
        }

        $batch_obj = [
            'transaction_date'=>$date,
            'reason'=>$id_reason,
            'other_reason'=>$request->others,
            'status'=>0,
            'total_amount'=>$total_batch_withdrawal
        ];

        if(!MySession::isAdmin()){
            $batch_obj['id_member'] = MySession::myId();
        }

        if($opcode == 0){
            DB::table('prime_withdrawal_batch')
            ->insert($batch_obj);

            $id_prime_withdrawal_batch = DB::table('prime_withdrawal_batch')->max('id_prime_withdrawal_batch');
        }else{
            unset($batch_obj['status']);
            DB::table('prime_withdrawal_batch')
            ->where('id_prime_withdrawal_batch',$id_prime_withdrawal_batch)
            ->update($batch_obj);

            DB::table('prime_withdrawal')
            ->where('id_prime_withdrawal_batch',$id_prime_withdrawal_batch)
            ->delete();
        }


        for($i=0;$i<count($post_withdrawal);$i++){
            $post_withdrawal[$i]['id_prime_withdrawal_batch'] = $id_prime_withdrawal_batch;
        }

        DB::table('prime_withdrawal')
        ->insert($post_withdrawal);

        $data['id_prime_withdrawal_batch'] = $id_prime_withdrawal_batch;



        return response($data);
        

        return response($request->all());
    }
    public function post2(Request $request){
        if($request->ajax()){
            $data['RESPONSE_CODE'] = "SUCCESS";
            $opcode = $request->opcode;
            $id_prime_withdrawal = $request->id_prime_withdrawal;


            if(!isset($request->amount) || $request->amount == 0){
                $data['RESPONSE_CODE'] = "ERROR";
                $data['message'] = "Invalid Amount";

                return response($data);
            }

            if(MySession::isAdmin()){
                $id_member = $request->id_member;
            }else{
                $id_member = MySession::myId();
            }

            $check_request = DB::table('prime_withdrawal')
                            ->where('id_member',$id_member)
                            ->where('status',0)
                            ->first();


            if(isset($check_request) && $opcode == 0){
                $data['RESPONSE_CODE'] = "ERROR";
                $data['message'] = "Existing Pending Request";

                return response($data);
            }

            $reason =  $request->reason;

            // VALIDATE Prime REMAINING

            $remaining_prime = $this->parseMemberPrime($id_member,0);

            // $withdrawal_amt = ($reason == 1)?$remaining_prime:$request->amount;
            $withdrawal_amt = $request->amount;

            if($withdrawal_amt > $remaining_prime){
                $data['RESPONSE_CODE'] = "ERROR";
                $data['message'] = "Withdrawal Amount is greater than remaining Prime (".number_format($remaining_prime,2).")";

                return response($data);
            }
            if($opcode == 0){

                //VALIDATIONS
                DB::table('prime_withdrawal')
                ->insert([
                    'id_member' => $id_member,
                    'amount' => $withdrawal_amt,
                    'reason'=> $request->reason,
                    'other_reason'=>$request->others
                ]);

                $data['id_prime_withdrawal'] = DB::table('prime_withdrawal')->max('id_prime_withdrawal');                
            }else{
                DB::table('prime_withdrawal')
                ->where('id_prime_withdrawal',$id_prime_withdrawal)
                ->update([
                    'amount' => $withdrawal_amt,
                    'reason'=> $request->reason,
                    'other_reason'=>$request->others
                ]);
                $data['id_prime_withdrawal'] = $id_prime_withdrawal;
            }


            return response($data);
        }
    }
    public function view($id_prime_withdrawal_batch){
        $data['opcode'] = 1;



        // $data['withdrawals'] = DB::table('prime_withdrawal_batch as prime')
        //                        ->select(DB::raw("prime.status as status_code,DATE_FORMAT(prime.transaction_date,'%m/%d/%Y') as transaction_date,prime.id_prime_withdrawal_batch,if(prime.reason < 4,cr.description,prime.other_reason) as reason,if(prime.status=0,'Draft',if(prime.status=1,'Approved, For Releasing',if(prime.status=2,'Released',if(prime.status=5,'Disapproved','Cancelled')))) as status,DATE_FORMAT(prime.date_created,'%m/%d/%Y') as date_created"))
        //                        ->leftJoin('prime_withdrawal_reason as cr','cr.id_prime_withdrawal_reason','prime.reason')
        //                        ->orDerby('prime.id_prime_withdrawal_batch','DESC')
        //                        ->get();



        $data['current_date'] = MySession::current_date();
        $data['head_title'] = "Prime Withdrawal #$id_prime_withdrawal_batch";
        $data['details'] = DB::table('prime_withdrawal_batch as prime')
                         ->select(DB::raw("prime.*,if(prime.status=0,'Draft',if(prime.status=1,'Approved, For Releasing',if(prime.status=2,'Released',if(prime.status=5,'Disapproved','Cancelled')))) as status_desc,if(prime.reason < 5,cr.description,prime.other_reason) as reason,DATE_FORMAT(transaction_date,'%m/%d/%Y') as trans_date,prime.id_member,prime.reason as reason_code"))
                         ->leftJoin('prime_withdrawal_reason as cr','cr.id_prime_withdrawal_reason','prime.reason')
                         ->where('prime.id_prime_withdrawal_batch',$id_prime_withdrawal_batch)
                         ->first();

                     

        $data['admin_view'] = isset($data['details']->id_member) ? false:true;




        $data['withdrawals'] = DB::table('prime_withdrawal as p')
                               ->select(DB::raw("p.id_prime_withdrawal,m.id_member,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as name,p.amount,p.id_cash_disbursement,p.status"))
                               ->leftJoin('member as m','m.id_member','p.id_member')
                               ->where('p.id_prime_withdrawal_batch',$id_prime_withdrawal_batch)
                               ->get();



        $data['allow_post'] = ($data['details']->status==0)?true:false;


        if(!$data['allow_post']){
            return view('prime_withdraw.view',$data);
        }

        $data['reasons'] = DB::table('prime_withdrawal_reason')->get();

        if($data['admin_view']){
            $pc = new PrimeController();
            $data['prime_list'] = $pc->prime_data($data['current_date'],true);            
        }else{
            $data['prime_list'] = $this->parseMemberPrime([MySession::myId()],0);
            $data['balance'] = isset($data['prime_list'])?$data['prime_list'][MySession::myId()][0]->amount:0;  
        }


        // if(!MySession::isAdmin() && $data['details']->id_member != MySession::myId()){
        //      return redirect('/redirect/error')->with('message', "privilege_access_invalid");   
        // }


        // $data['selected_member'] = DB::table('member as m')
        // ->select(DB::raw("concat(membership_id,' || ',FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)) as tag_value,id_member as tag_id"))
        // ->where('id_member',$data['details']->id_member)
        // ->first();

        
        // $data['current_prime'] = $this->parseMemberPrime($data['details']->id_member,0);

        // if(MySession::isSuperadmin()){
        //     dd($data);
        // }
        return view('prime_withdraw.form_bulk',$data);

        return $data;
    }
    public function post_status(Request $request){
        // if($request->ajax()){
            $data['RESPONSE_CODE'] = "SUCCESS";
            $data['show_print'] = false;

            $details = DB::table('prime_withdrawal_batch')->where('id_prime_withdrawal_batch',$request->id_prime_withdrawal_batch)->first();


            if($details->status == 10){
                $data['RESPONSE_CODE'] = "ERROR";
                $data['message'] = "Request already cancelled";

                return response($data);
            }


            $status = $request->status;

            if(MySession::isAdmin() && $status <= 2){
                // $remaining_prime = $this->parseMemberPrime($details->id_member,0);

                // if($details->amount > $remaining_prime){
                //     $data['RESPONSE_CODE'] = "ERROR";
                //     $data['message'] = "Withdrawal Amount is greater than remaining Prime (".number_format($remaining_prime,2).")";

                //     return response($data);
                // }
                $withdrawals = DB::table('prime_withdrawal')
                               ->select(DB::raw("id_prime_withdrawal,id_member,amount"))
                               ->where('id_prime_withdrawal_batch',$request->id_prime_withdrawal_batch)
                               ->get();
                if($status == 2){
                    // update to released
                    if($details->status == 0){
                        $data['RESPONSE_CODE'] = "ERROR";
                        $data['message'] = "Request not yet confirmed";

                        return response($data);
                    }

                    DB::table('prime_withdrawal_batch')
                    ->where('id_prime_withdrawal_batch',$request->id_prime_withdrawal_batch)
                    ->update(['status'=>$status,'date_released'=>$request->date,'status_date'=>DB::raw("now()")]);

                    foreach($withdrawals as $w){
                        DB::table('prime_withdrawal')
                        ->where('id_prime_withdrawal',$w->id_prime_withdrawal)
                        ->update(['status'=>$status]);

                        CDVModel::PrimeWithdrawalCDV($w->id_prime_withdrawal);
                    }


                    // CDVModel::PrimeWithdrawalCDV($request->id_prime_withdrawal);
                    // $data['show_print'] = true;


                }else{



                    $validator = $this->parseMemberPrime(array_column($withdrawals->toArray(),'id_member'),$request->id_prime_withdrawal_batch);
                    $invalid = array();
                    foreach($validator as $id_member=>$val){
                        if($val[0]->withdraw > $val[0]->amount){
                            array_push($invalid,$id_member);
                        }
                    }

                    if(count($invalid) > 0){
                        $data['RESPONSE_CODE'] = "INVALID_WITHDRAWAL";
                        $data['message'] = "Please check the amount";
                        $data['invalids'] = $invalid;

                        return response($data);
                    }


                    // update to approved
                     DB::table('prime_withdrawal')
                    ->where('id_prime_withdrawal_batch',$request->id_prime_withdrawal_batch)
                    ->update(['status'=>$status]);

                     DB::table('prime_withdrawal_batch')
                    ->where('id_prime_withdrawal_batch',$request->id_prime_withdrawal_batch)
                    ->update(['status'=>$status,'status_date'=>DB::raw("now()")]);
                }
            }else{
                // cancel
                DB::table('prime_withdrawal_batch')
                ->where('id_prime_withdrawal_batch',$request->id_prime_withdrawal_batch)
                ->update(['status'=>$status,'status_date'=>DB::raw("now()"),'cancellation_reason'=>$request->reason]);

                DB::table('prime_withdrawal')
                ->where('id_prime_withdrawal_batch',$request->id_prime_withdrawal_batch)
                ->update(['status'=>$status]);
            }


            return response($data);
        // }
    }


    public function cancel_prime_individual(Request $request){
        if($request->ajax()){
            $id_prime_withdrawal = $request->id_prime_withdrawal;

            $details = DB::table('prime_withdrawal')
                      ->where('id_prime_withdrawal',$id_prime_withdrawal)
                      ->first();

            $data['RESPONSE_CODE'] = "SUCCESS";

            $reason = "[CANCELLED]";

            DB::table('cash_disbursement')
            ->where('id_cash_disbursement',$details->id_cash_disbursement)
            ->where('status','<>',10)
            ->update(['cancellation_reason'=>$reason,
               'date_cancelled' => DB::raw('now()'),
               'status' => 10,
               'description'=>DB::raw("concat(description,' [CANCELLED]')")]);

            DB::table('prime_withdrawal')
            ->where('id_prime_withdrawal',$id_prime_withdrawal)
            ->update(['status'=>10]);


            $w_status_count = DB::table('prime_withdrawal')
                            ->where('id_prime_withdrawal_batch',$details->id_prime_withdrawal_batch)
                            ->where('status','<>',10)
                            ->count();

            if($w_status_count == 0){
                DB::table('prime_withdrawal_batch')
                ->where('id_prime_withdrawal_batch',$details->id_prime_withdrawal_batch)
                ->update(['status'=>10]);                
            }



            return response($data);

        }

    }

    public function export_prime_withdrawal($date_start,$date_end){

        // $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/prime_withdraw');
        if(!MySession::isAdmin()){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }



        $data['prime_withdrawals'] = DB::table('prime_withdrawal_batch as pb')
                                     ->select(DB::raw('prime.id_prime_withdrawal,DATE_FORMAT(pb.transaction_date,"%m/%d/%Y") as date_released,prime.id_cash_disbursement,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member,amount,if(pb.reason < 5,cr.description,pb.other_reason) as reason'))
                                    ->leftJoin('prime_withdrawal as prime','prime.id_prime_withdrawal_batch','pb.id_prime_withdrawal_batch')
                                    ->leftJoin('member as m','m.id_member','prime.id_member')
                                    ->leftJoin('prime_withdrawal_reason as cr','cr.id_prime_withdrawal_reason','pb.reason')
                                    ->where('prime.status',2)
                                    ->where('pb.transaction_date','>=',$date_start)
                                    ->where('pb.transaction_date','<=',$date_end)
                                    ->orDerby('pb.transaction_date','ASC')
                                    ->orDerby('id_prime_withdrawal')
                                    ->get();               
        $data['date'] = WebHelper::ReportDateFormatter($date_start,$date_end);

        // return $data['date'];

        $html = view('prime_withdraw.prime_export',$data);
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
        return view('prime_withdraw.prime_export',$data);
        return $data;
    }
    public function batch_summary($id_batch){
        if(!MySession::isAdmin()){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }        
        $data['details'] = DB::table('prime_withdrawal_batch as pb')
                            ->select(DB::raw("pb.id_prime_withdrawal_batch,DATE_FORMAT(pb.transaction_date,'%m/%d/%Y') as date_released,if(pb.reason < 5,cr.description,pb.other_reason) as reason"))
                            ->leftJoin('prime_withdrawal_reason as cr','cr.id_prime_withdrawal_reason','pb.reason')
                            ->where('pb.id_prime_withdrawal_batch',$id_batch)
                            ->first();

        $data['prime_withdrawals'] = DB::table('prime_withdrawal_batch as pb')
                                     ->select(DB::raw('prime.id_prime_withdrawal,prime.id_cash_disbursement,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member,amount'))
                                    ->leftJoin('prime_withdrawal as prime','prime.id_prime_withdrawal_batch','pb.id_prime_withdrawal_batch')
                                    ->leftJoin('member as m','m.id_member','prime.id_member')
                                    ->where('prime.status',2)
                                    ->where('pb.id_prime_withdrawal_batch',$id_batch)
                                    ->orDerby('pb.transaction_date','ASC')
                                    ->orDerby('id_prime_withdrawal')
                                    ->get();    
        // dd($data);  
        $html = view('prime_withdraw.prime_export_batch',$data);
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
