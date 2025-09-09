<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use DateTime;
use App\CredentialModel;
use App\MySession as MySession;
use App\JVModel;
use App\CDVModel;
use PDF;
use App\WebHelper;

class ChangeController extends Controller
{
    public function index(Request $request){
        $data['credential']= CredentialModel::GetCredential(MySession::myPrivilegeId());
        if(!$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }

        $data['current_date'] = MySession::current_date();
        $start = date('Y-m-d', strtotime('-1 year'));
        $end = MySession::current_date();

        $data['start_date'] = $request->start_date ?? $start;
        $data['end_date'] = $request->end_date ?? $end;
        $data['selected_id_member'] = $request->id_member ?? 0;

        $data['selected_member'] =DB::table('member as m')
        ->select(DB::raw("concat(membership_id,' || ',FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)) as member_name,id_member"))
        ->where('id_member',$request->id_member)
        ->first();

        $data['change_list'] = DB::table('repayment_change as rc')
        ->select(DB::raw("rc.id_repayment_change,DATE_FORMAT(rc.date,'%m/%d/%Y') as date,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as payee,concat('Change for Loan Payment #',rc.id_repayment_transaction) as description,FORMAT(amount,2) as amount, DATE_FORMAT(rc.date_created,'%m/%d/%Y %r') as date_created,if(rc.status < 10,'','Cancelled') as status,rc.id_cash_disbursement"))
        ->leftJoin('member as m','m.id_member','rc.id_member')
        ->where('rc.date','>=',$data['start_date'])
        ->where('rc.date','<=',$data['end_date'])
        ->where(function($query) use($data){
            if($data['selected_id_member'] > 0){
                $query->where('rc.id_member',$data['selected_id_member']);
            }
        })
        ->orDerby('rc.id_repayment_change','DESC')
        ->get();

        $data['head_title'] = "Change Payables";
        // return $data['change_list'];
        return view('change.index',$data);

        return $data;
    }
    public function create(){
        $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/change');
        if(!$data['credential']->is_create){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
        $data['opcode'] = 0;
        $data['current_date'] = MySession::current_date();
        $data['change_list'] = $this->parseChanges();

        $data['allow_post'] = true;
        $data['head_title'] = "Create Change";

        // return $data;
        return view('change.change_form',$data);
    }
    public function parse_change(Request $request){
        if($request->ajax()){
            $id_member = $request->id_member;

            $data['with_change'] = $this->parseChanges($id_member);


            return response($id_member);
        }
    }

    // public function parseChanges($id_member){
    //     $changes = DB::select("SELECT *,(`change`-change_released) as remaining_change FROM (
    //                 SELECT rt.id_member,getRepaymentTransactionIDLoans(rt.id_repayment_transaction) as loan_ids,DATE_FORMAT(rt.transaction_date,'%m/%d/%Y') as transaction_date,rt.id_repayment_transaction ,rt.swiping_amount,rt.total_payment,rt.change,ifnull(SUM(rc.amount),0) as change_released
    //                 FROM repayment_transaction as rt 
    //                 LEFT JOIN repayment_change as rc on rc.id_repayment_transaction = rt.id_repayment_transaction
    //                 where change_status < 2 and repayment_type = 1 and transaction_type = 2 AND rt.id_member = ?
    //                 GROUP BY id_repayment_transaction) as change_table 
    //                 WHERE (`change`-change_released) > 0;",[$id_member]);

    //     return $changes;
    // }
    public function parseChanges(){
        $changes = DB::select("SELECT *,(`change`-change_released) as remaining_change FROM (
            SELECT rt.id_member,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as borrower,getRepaymentTransactionIDLoans(rt.id_repayment_transaction) as loan_ids,DATE_FORMAT(rt.transaction_date,'%m/%d/%Y') as transaction_date,rt.id_repayment_transaction ,rt.swiping_amount,rt.total_payment,rt.change,ifnull(SUM(rc.amount),0) as change_released
            FROM repayment_transaction as rt 
            LEFT JOIN repayment_change as rc on rc.id_repayment_transaction = rt.id_repayment_transaction and rc.status <> 10
            LEFT JOIN member as m on m.id_member = rt.id_member
            where change_status < 2 and repayment_type = 1 and transaction_type = 2  and rt.status <> 10
            GROUP BY id_repayment_transaction) as change_table 
        WHERE (`change`-change_released) > 0 ORDER BY borrower,id_repayment_transaction;");

        return $changes;
    }
    public function view($type,$id_repayment_change){
        $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/change');
        if(!$data['credential']->is_edit && !$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
        $data['opcode'] = 1;
        $data['head_title'] = "Change #$id_repayment_change";

        $data['type'] = ($type=="view")?1:2;
        $id_repayment_change_function = ($type=="view")?0:$id_repayment_change;
        $data['repayment_change'] = DB::table('repayment_change as rc')
        ->select(DB::raw("rc.id_repayment_change,rc.id_repayment_transaction,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as payee,getRepaymentTransactionIDLoans(rt.id_repayment_transaction) as loan_ids,DATE_FORMAT(rt.transaction_date,'%m/%d/%Y') as transaction_date,rc.date,rc.id_repayment_transaction,rt.swiping_amount,rt.total_payment,rt.change,@q:=getTotalChange(rc.id_repayment_transaction,$id_repayment_change_function) as change_released,`change`-@q as remaining_change,rc.amount,rc.status,rc.cancellation_reason,rc.id_journal_voucher,rc.id_cash_disbursement"))
        ->leftJoin('member as m','m.id_member','rc.id_member')
        ->leftJoin('repayment_transaction as rt','rt.id_repayment_transaction','rc.id_repayment_transaction')
        ->where('rc.id_repayment_change',$id_repayment_change)
        ->first();
        $data['allow_post'] = ($data['repayment_change']->status == 10 || $data['type'] == 1)?false:true;
        
                                    // return $data;
        return view('change.change_form',$data);
        return $id_repayment_change;
    }
    function post(Request $request){
        if($request->ajax()){
            $change_post = $request->change_post;

            $date = $request->date;
            $opcode = $request->opcode;
            $id_repayment_change = $request->id_repayment_change;

            if(!isset($request->date)){
                $data['RESPONSE_CODE'] = "ERROR";
                $data['message'] = "Invalid Date";

                return response($data);
            }
            
            $response['RESPONSE_CODE'] = "success";

            $valid_input = true;
            $input_with_error = array();
            $repayment_transaction_change_completed = array();
            $repayment_transaction_change_not_completed = array();

            $edited = false;

            if(!isset($change_post)){
                $response['RESPONSE_CODE'] = "ERROR";
                $response['message'] = "Please select atleast 1 transaction";

                return response($response);
            }
            $validated_repayment = array();
            for($i=0;$i<count($change_post);$i++){

                if(!in_array($change_post[$i]['id_repayment_transaction'],$validated_repayment)){

                    $details = $this->parseRepaymentTransactionInfo($change_post[$i]['id_repayment_transaction'],$opcode,$id_repayment_change);

                // return $details;
                    $id_member = $details->id_member;
                    $remaining_change = $details->remaining_change;


                    if($change_post[$i]['amount'] > $remaining_change){
                        $valid_input = false;
                        array_push($input_with_error,$change_post[$i]['id_repayment_transaction']);
                    }

                    $change_post[$i]['id_member'] = $id_member;
                    $change_post[$i]['date'] = $date;
                    if($remaining_change == $change_post[$i]['amount']){
                        array_push($repayment_transaction_change_completed,$change_post[$i]['id_repayment_transaction']);
                    }else{
                        array_push($repayment_transaction_change_not_completed,$change_post[$i]['id_repayment_transaction']);   
                    }
                    if($opcode == 0){
                        $change_post[$i]['id_user'] = MySession::mySystemUserId();
                    }

                    array_push($validated_repayment,$change_post[$i]['id_repayment_transaction']);
                }
            }
            if(!$valid_input){
                $response['RESPONSE_CODE'] = "INVALID_INPUT";
                $response['message'] = "Invalid Amount !";
                $response['inputs'] = $input_with_error;

                return response($response);
            }
            $id_repayment_change_array = [];
            if($opcode == 0){

                foreach($change_post as $cp){
                    if(isset($cp['id_member'])){
                        DB::table('repayment_change')
                        ->insert($cp);    

                        $id_m = DB::table('repayment_change')->max('id_repayment_change');
                        array_push($id_repayment_change_array,$id_m);                          
                    }              
                }

            }else{

                $validate_change = $this->validate_changes($id_repayment_change,$change_post[0]);

                if(!$validate_change['valid']){
                    $data['RESPONSE_CODE'] = "ERROR";
                    $data['message'] = "No Changes";
                    return response($data);
                }
                $edited = true;
                DB::table('repayment_change')
                ->where('id_repayment_change',$id_repayment_change)
                ->update($change_post[0]);
                $id_repayment_change_array = [$id_repayment_change];
            }
            if(count($repayment_transaction_change_completed) > 0){
                DB::table('repayment_transaction')
                ->whereIn('id_repayment_transaction',$repayment_transaction_change_completed)
                ->update(['change_status'=>1]);
            }
            if(count($repayment_transaction_change_not_completed) > 0){
                DB::table('repayment_transaction')
                ->whereIn('id_repayment_transaction',$repayment_transaction_change_not_completed)
                ->update(['change_status'=>0]);  
            }
            for($i=0;$i<count($id_repayment_change_array);$i++){
                // $jv = JVModel::ChangeJV($id_repayment_change_array[$i],$edited,false);
                // if($jv['status'] == "SUCCESS"){
                //     DB::table('repayment_change')
                //     ->where('id_repayment_change',$id_repayment_change_array[$i])
                //     ->update(['id_journal_voucher'=>$jv['id_journal_voucher']]);
                // }

                $cdv = CDVModel::ChangeCDV($id_repayment_change_array[$i],$edited,false);

                $response['id_cdv'] = $cdv['id_cash_disbursement'];
                $response['id_repayment_change'] = $id_repayment_change_array[$i];
                if($cdv['status'] == "SUCCESS"){
                    DB::table('repayment_change')
                    ->where('id_repayment_change',$id_repayment_change_array[$i])
                    ->update(['id_cash_disbursement'=>$cdv['id_cash_disbursement']]);
                }
            }

            $response['array_count'] = count($id_repayment_change_array);
            

            return response($response);
        }

        return [1=>$repayment_transaction_change_completed,2=>$repayment_transaction_change_not_completed];
    }

    function parseRepaymentTransactionInfo($id_repayment_transaction,$opcode,$id_repayment_change){
        $details = DB::table('repayment_transaction')
        ->select(DB::raw("`change`-getTotalChange(id_repayment_transaction,$id_repayment_change) as remaining_change,id_member"))
        ->where('id_repayment_transaction',$id_repayment_transaction)
        ->first();

        return $details;
    }
    public function post_status(Request $request){
        if($request->ajax()){
            // $status = $request->status;
            $status = 10;
            $reason  = $request->cancellation_reason;
            $id_repayment_change = $request->id_repayment_change;
            $details = DB::table('repayment_change')->select('id_repayment_change','id_repayment_transaction')->where('id_repayment_change',$id_repayment_change)->first();
            if($status == 10){
                DB::table('repayment_change')
                ->where('id_repayment_change',$id_repayment_change)
                ->update(['status'=>$status,
                  'cancellation_reason'=>$reason,
                  'cancelled_at'=>DB::raw("now()"),
                  'id_user_cancelled'=>MySession::mySystemUserId()]);       
                DB::table('repayment_transaction')
                ->where('id_repayment_transaction',$details->id_repayment_transaction)
                ->update(['change_status'=>0]);      

                CDVModel::ChangeCDV($id_repayment_change,false,true);
                // JVModel::ChangeJV($id_repayment_change,false,true);   
            }

            $response['RESPONSE_CODE'] = "success";
            return response($response);
            return $request;
        }
    }
    public function validate_changes($id_repayment_change,$data){
        $output['valid'] = false;
        $count = DB::table('repayment_change')
        ->where('id_repayment_change',$id_repayment_change)
        ->where('amount',$data['amount'])
        ->where('date',$data['date'])
        ->where('id_repayment_transaction',$data['id_repayment_transaction'])
        ->count();
        if($count == 0){
            $output['valid'] = true;
            return $output;
        }
        return $output;
    }

    public function ChangeSummary(){
        
        $data['changes'] = $this->parseChanges();

        $html = view('change.change_summary',$data);
        $pdf = PDF::loadHtml($html);
        $pdf->setOption("encoding","UTF-8");
        $pdf->setOption('margin-bottom', '5mm');
        $pdf->setOption('margin-top', '7mm');
        $pdf->setOption('margin-right', '5mm');
        $pdf->setOption('margin-left', '5mm');
        $pdf->setOption('header-left', 'Page [page] of [toPage]');

        $pdf->setOption('header-font-size', 8);
        $pdf->setOption('header-font-name', 'Calibri');
        // $pdf->setOrientation('landscape');

        return $pdf->stream();        
    }
    public function ChangeCreatedSummary($date_start,$date_end,$cancel){
        $data['changes'] = DB::table('repayment_change as rc')
        ->select(DB::raw("rc.id_repayment_change,rc.id_repayment_transaction,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as borrower,getRepaymentTransactionIDLoans(rt.id_repayment_transaction) as loan_ids,DATE_FORMAT(rt.transaction_date,'%m/%d/%Y') as transaction_date,DATE_FORMAT(rc.date,'%m/%d/%Y') as date_released,rc.id_repayment_transaction,rt.swiping_amount,rt.total_payment,rt.change,@q:=getTotalChange(rc.id_repayment_transaction,rc.id_repayment_change) as change_released,`change`-@q as remaining_change,rc.amount,rc.status,rc.cancellation_reason,rc.id_journal_voucher,rc.id_cash_disbursement"))
        ->leftJoin('member as m','m.id_member','rc.id_member')
        ->leftJoin('repayment_transaction as rt','rt.id_repayment_transaction','rc.id_repayment_transaction')
        ->where('rc.date','>=',$date_start)
        ->where('rc.date','<=',$date_end)
        ->where(function($q) use($cancel){
            if($cancel == 0){
                $q->where('rc.status','<>',10);
            }
        })
        ->orDerby('rc.id_repayment_change','ASC')
        ->get();


        $data['date'] = WebHelper::ReportDateFormatter($date_start,$date_end);

        // return $data['date'];

        $html = view('change.change_summary_created',$data);
        $pdf = PDF::loadHtml($html);
        $pdf->setOption("encoding","UTF-8");
        $pdf->setOption('margin-bottom', '5mm');
        $pdf->setOption('margin-top', '7mm');
        $pdf->setOption('margin-right', '5mm');
        $pdf->setOption('margin-left', '5mm');
        $pdf->setOption('header-left', 'Page [page] of [toPage]');

        $pdf->setOption('header-font-size', 8);
        $pdf->setOption('header-font-name', 'Calibri');
        // $pdf->setOrientation('landscape');

        return $pdf->stream();  
        return $data;
    }
}


