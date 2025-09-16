<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\MySession;
use App\CredentialModel;
use App\CDVModel;
use PDF;
use App\WebHelper;
class CBUWithdrawalController extends Controller
{
    public function recurssion($n=1){
        $random = rand(1,20);

        $desired_val = 8;
        echo "Attempt $n: $random <br>";
        if($random != $desired_val){
            
            $this->recurssion($n+1);
        }else{
            echo "No of attempt(s) : $n";
        }
        
    }
    public function index(){
        // $this->recurssion();
        // return;

        $data['credential']= CredentialModel::GetCredential(MySession::myPrivilegeId());
        $data['head_title'] = "CBU Withdrawal";
        if(!$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
        $data['withdrawals'] = DB::table('cbu_withdrawal as cbu')
                               ->select(DB::raw("DATE_FORMAT(cbu.date_released,'%m/%d/%Y') as date_released,cbu.id_cbu_withdrawal,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member,if(cbu.reason < 4,cr.description,cbu.other_reason) as reason,cbu.amount,if(cbu.status=0,'Draft',if(cbu.status=1,'Approved, For Releasing',if(cbu.status=2,'Released',if(cbu.status=5,'Disapproved','Cancelled')))) as status,cbu.id_cash_disbursement,cbu.status as status_code,DATE_FORMAT(cbu.date_created,'%m/%d/%Y') as date_created,
                                if(cbu.id_bank = 0,'Cash',tb.bank_name) as mode"))
                               ->leftJoin('member as m','m.id_member','cbu.id_member')
                               ->leftJoin('cbu_withdrawal_reason as cr','cr.id_cbu_withdrawal_reason','cbu.reason')
                               ->leftJoin('tbl_bank as tb','tb.id_bank','cbu.id_bank')
                               ->where(function($query){
                                    if(!MySession::isAdmin()){
                                        $query->where('cbu.id_member',MySession::myId());
                                    }
                               })
                               ->orDerby('cbu.id_cbu_withdrawal','DESC')
                               ->get();

        $data['current_date'] = MySession::current_date();
        return view('cbu_withdraw.index',$data);

        return $data;
    }

    public function create(){
        $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/cbu_withdraw');
        $data['head_title'] = "Create CBU Withdrawal";
        if(!$data['credential']->is_create){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }

        // $data['sidebar'] = "sidebar-collapse";
        $data['opcode'] = 0;
        $data['current_date'] = MySession::current_date();
        $data['allow_post'] = true;

        $data['reasons'] = DB::table('cbu_withdrawal_reason')->get();

        if(!MySession::isAdmin()){
            $data['current_cbu'] = $this->parseMemberCBU(MySession::myId(),0);
        }


        return view('cbu_withdraw.form',$data);
    }

    public function post(Request $request){
        if($request->ajax()){
            $data['RESPONSE_CODE'] = "SUCCESS";
            $opcode = $request->opcode;
            $id_cbu_withdrawal = $request->id_cbu_withdrawal;


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




            $check_request = DB::table('cbu_withdrawal')
                            ->where('id_member',$id_member)
                            ->where('status',0)
                            ->first();


            if(isset($check_request) && $opcode == 0){
                $data['RESPONSE_CODE'] = "ERROR";
                $data['message'] = "Existing Pending Request";

                return response($data);
            }

            $reason =  $request->reason;





            // VALIDATE CBU REMAINING

            $remaining_cbu = $this->parseMemberCBU($id_member,0);

            $withdrawal_amt = ($reason == 1)?$remaining_cbu:$request->amount;



            if($withdrawal_amt > $remaining_cbu){
                $data['RESPONSE_CODE'] = "ERROR";
                $data['message'] = "Withdrawal Amount is greater than remaining CBU (".number_format($remaining_cbu,2).")";

                return response($data);
            }
            if($opcode == 0){

                //VALIDATIONS
                DB::table('cbu_withdrawal')
                ->insert([
                    'id_member' => $id_member,
                    'amount' => $withdrawal_amt,
                    'reason'=> $request->reason,
                    'other_reason'=>$request->others
                ]);

                $data['id_cbu_withdrawal'] = DB::table('cbu_withdrawal')->max('id_cbu_withdrawal');                
            }else{
                DB::table('cbu_withdrawal')
                ->where('id_cbu_withdrawal',$id_cbu_withdrawal)
                ->update([
                    'amount' => $withdrawal_amt,
                    'reason'=> $request->reason,
                    'other_reason'=>$request->others
                ]);
                $data['id_cbu_withdrawal'] = $id_cbu_withdrawal;
            }


            // $data['RESPONSE_CODE'] = "ERROR";
            // $data['message'] = "this is the message";
            // return response($data);

            return response($data);
        }
    }

    public function parseMemberCBU($id_member,$id_cash_disbursement){
            $data['cbu'] = DB::select("SELECT ifnull(SUM(amount),0) as amount   FROM (
            SELECT ifnull(amount,0) as amount,id_member 
            FROM cash_receipt_details as cd
            LEFT JOIN cash_receipt as c on c.id_cash_receipt = cd.id_cash_receipt
            LEFT JOIN tbl_payment_type as pt on pt.id_payment_type = cd.id_payment_type
            WHERE pt.is_cbu = 1 AND pt.type in (1) AND status <> 10  and c.id_member = ?
            UNION ALL
            SELECT ifnull(lc.calculated_charge,0) as amount,id_member from loan_charges as lc
            LEFT JOIN loan on loan.id_loan = lc.id_loan
            LEFT JOIN tbl_payment_type as pt on pt.reference = lc.id_loan_fees and pt.type = 2
            WHERE  pt.is_cbu = 1 AND (loan.status in (6) or loan.loan_status in (1)) and pt.type =2
            and loan.id_member = ?
            UNION ALL
            SELECT ifnull(rf.amount,0),id_member FROM repayment_fees as rf
            LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rf.id_repayment_transaction
            LEFT JOIN tbl_payment_type as pt on pt.id_payment_type = rf.id_payment_type
            WHERE pt.is_cbu = 1 and pt.type =3 AND rt.status <> 10 AND rt.id_member = ?
            UNION ALL
            SELECT ifnull(amount,0),id_member FROM cbu_beginning where id_member = ?
            UNION ALL
            SELECT (cdd.debit*-1) as amount,cd.id_member FROM cash_disbursement as cd
            LEFT JOIN cash_disbursement_details as cdd on cdd.id_cash_disbursement = cd.id_cash_disbursement
            LEFT JOIN chart_account as ca on ca.id_chart_account = cdd.id_chart_account
            WHERE  ca.iscbu = 1 and cd.status <> 10 AND cd.id_member = ? and cd.id_cash_disbursement <> $id_cash_disbursement
            ) as CBU
        LEFT JOIN member as m on m.id_member = CBU.id_member
        GROUP BY CBU.id_member",[$id_member,$id_member,$id_member,$id_member,$id_member]);


        return (count($data['cbu']) > 0)?$data['cbu'][0]->amount:0;

    }

    public function view($id_cbu_withdrawal){
        $data['opcode'] = 1;
        $data['current_date'] = MySession::current_date();
        $data['head_title'] = "CBU Withdrawal #$id_cbu_withdrawal";
        $data['details'] = DB::table('cbu_withdrawal as cbu')
                         ->select(DB::raw("cbu.*,if(cbu.status=0,'Draft',if(cbu.status=1,'Approved, For Releasing',if(cbu.status=2,'Released',if(cbu.status=5,'Disapproved','Cancelled')))) as status_desc,if(cbu.id_bank=0,'Cash',tb.bank_name) as mode"))
                         ->leftJoin('tbl_bank as tb','tb.id_bank','cbu.id_bank')
                         ->where('id_cbu_withdrawal',$id_cbu_withdrawal)->first();


        if(!MySession::isAdmin() && $data['details']->id_member != MySession::myId()){
             return redirect('/redirect/error')->with('message', "privilege_access_invalid");   
        }


        $data['selected_member'] = DB::table('member as m')
        ->select(DB::raw("concat(membership_id,' || ',FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)) as tag_value,id_member as tag_id"))
        ->where('id_member',$data['details']->id_member)
        ->first();

        $data['allow_post'] = ($data['details']->status==0)?true:false;

        $data['reasons'] = DB::table('cbu_withdrawal_reason')->get();
        $data['current_cbu'] = $this->parseMemberCBU($data['details']->id_member,0);
        $data['banks'] = DB::table('tbl_bank')->get();

        return view('cbu_withdraw.form',$data);

        return $data;
    }

    public function post_status(Request $request){
        if($request->ajax()){
            $data['RESPONSE_CODE'] = "SUCCESS";
            $data['show_print'] = false;

        
            $details = DB::table('cbu_withdrawal')->where('id_cbu_withdrawal',$request->id_cbu_withdrawal)->first();

            if($details->status == 10){
                $data['RESPONSE_CODE'] = "ERROR";
                $data['message'] = "Request already cancelled";

                return response($data);
            }


            $status = $request->status;

            if(MySession::isAdmin() && $status <= 2){
                $remaining_cbu = $this->parseMemberCBU($details->id_member,0);

                if($details->amount > $remaining_cbu){
                    $data['RESPONSE_CODE'] = "ERROR";
                    $data['message'] = "Withdrawal Amount is greater than remaining CBU (".number_format($remaining_cbu,2).")";

                    return response($data);
                }

                if($status == 2){
                    // update to released
                    if($details->status == 0){
                        $data['RESPONSE_CODE'] = "ERROR";
                        $data['message'] = "Request not yet confirmed";

                        return response($data);
                    }


                    DB::table('cbu_withdrawal')
                    ->where('id_cbu_withdrawal',$request->id_cbu_withdrawal)
                    ->update(['status'=>$status,'date_released'=>$request->date,'id_bank'=>$request->id_bank]);

                    CDVModel::CBUWithdrawalCDV($request->id_cbu_withdrawal);
                    $data['show_print'] = true;


                    if($details->reason == 1){
                        DB::table('member')
                        ->where('id_member',$details->id_member)
                        ->update(['status'=>0]);
                    }


                }else{
                    // update to approved
                     DB::table('cbu_withdrawal')
                    ->where('id_cbu_withdrawal',$request->id_cbu_withdrawal)
                    ->update(['status'=>$status]);
                }

               
            }else{
                // cancel
                     DB::table('cbu_withdrawal')
                    ->where('id_cbu_withdrawal',$request->id_cbu_withdrawal)
                    ->update(['status'=>$status,'date_cancelled'=>DB::raw("now()"),'cancellation_reason'=>$request->reason]);
            }


            return response($data);
        }
    }

    public function get_member_cbu(Request $request){
        if($request->ajax()){
            $data['cbu_amount'] = $this->parseMemberCBU($request->id_member,0);
            return response($data);
        }
    }

    public function export_cbu_withdrawal($date_start,$date_end){

        // $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/cbu_withdraw');
        if(!MySession::isAdmin()){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }


        $data['cbu_withdrawals'] = DB::table('cbu_withdrawal as cbu')
                                   ->select(DB::raw('cbu.id_cbu_withdrawal,DATE_FORMAT(cbu.date_released,"%m/%d/%Y") as date_released,cbu.id_cash_disbursement,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member,amount,if(cbu.reason < 4,cr.description,cbu.other_reason) as reason'))
                                   ->leftJoin('member as m','m.id_member','cbu.id_member')
                                   ->leftJoin('cbu_withdrawal_reason as cr','cr.id_cbu_withdrawal_reason','cbu.reason')
                                   ->where('cbu.status',2)
                                   ->where('date_released','>=',$date_start)
                                   ->where('date_released','<=',$date_end)
                                   ->get();
        $data['date'] = WebHelper::ReportDateFormatter($date_start,$date_end);

        // return $data['date'];

        $html = view('cbu_withdraw.cbu_export',$data);
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
        return view('cbu_withdraw.cbu_export',$data);
        return $data;
    }
}
