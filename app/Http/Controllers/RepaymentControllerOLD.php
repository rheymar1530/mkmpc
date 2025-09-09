<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Loan;
use App\Member;
use App\MySession;
use App\CredentialModel;
use Dompdf\Dompdf;
use App\JVModel;
use App\CRVModel;
use PDF;
class RepaymentController extends Controller
{
    // public function generate_payments(){
    //     $loan_dues = DB::select("SELECT * FROM (
    //     SELECT loan.loan_token,loan.id_loan,lt.due_date,lt.term_code,
    //     lt.repayment_amount-ifnull(SUM(rl.paid_principal),0) as principal_bal,   
    //     lt.interest_amount-ifnull(SUM(rl.paid_interest),0) as interest_bal,
    //     lt.fees-ifnull(SUM(rl.paid_fees),0) as fees_bal         
    //     FROM loan 
    //     left join loan_table as lt on lt.id_loan = loan.id_loan
    //     left JOIN repayment_loans as rl on rl.id_loan =lt.id_loan AND rl.term_code = lt.term_code and rl.status <> 10 AND rl.id_repayment_transaction <> 0
    //     where loan_token =  '770709202211293915784'
    //     GROUP BY loan_token,lt.term_code) as act_due
    //     WHERE (principal_bal+interest_bal+fees_bal > 0)
    //     ORDER BY due_date ASC;");

    //     $amount_to_pay = 10000;
    //     $output['payments'] = array();
    //     $amount_paid =0;

    //     foreach($loan_dues as $c){
    //         $temp = array();
    //         $temp['term_code'] = $c->term_code;

    //         $temp['paid_interest'] = $this->calculate($amount_to_pay,$c->interest_bal);
    //         $amount_to_pay -=$temp['paid_interest'];
    //         $temp['paid_fees'] = $this->calculate($amount_to_pay,$c->fees_bal);
    //         $amount_to_pay -=$temp['paid_fees'];
    //         $temp['paid_principal'] = $this->calculate($amount_to_pay,$c->principal_bal);
    //         $amount_to_pay -=$temp['paid_principal'];
    //         $temp['is_advance'] = 1;
    //         $temp['id_loan'] = $c->id_loan;

    //         if(($temp['paid_interest']+$temp['paid_fees']+$temp['paid_principal']) > 0){
    //             $amount_paid += $temp['paid_interest']+$temp['paid_fees']+$temp['paid_principal'];
    //             array_push($output['payments'],$temp);
    //         }
    //     } 

    //     return $output;
    // }
    public function generate_payments(){
        $loan_dues = DB::select("SELECT * FROM (
                                SELECT loan.loan_token,loan.id_loan,lt.due_date,lt.term_code,
                                lt.repayment_amount-ifnull(SUM(rl.paid_principal),0) as principal_bal,   
                                lt.interest_amount-ifnull(SUM(rl.paid_interest),0) as interest_bal,
                                lt.fees-ifnull(SUM(rl.paid_fees),0) as fees_bal         
                                FROM loan 
                                left join loan_table as lt on lt.id_loan = loan.id_loan
                                left JOIN repayment_loans as rl on rl.id_loan =lt.id_loan AND rl.term_code = lt.term_code and rl.status <> 10 AND rl.id_repayment_transaction <> 0
                                where loan_token =  '80709202211293915784'
                                GROUP BY loan_token,lt.term_code) as act_due
                                WHERE (principal_bal+interest_bal+fees_bal > 0)
                                ORDER BY due_date ASC;");

        $paid_principal = 100000;
        $paid_interest = 2400;
        $paid_fees = 0;
        $output['payments'] = array();
        $output['amount_paid'] = 0;
        $amount_paid =0;

        foreach($loan_dues as $c){
            $temp = array();
            $temp['term_code'] = $c->term_code;

            $temp['paid_interest'] = $this->calculate($paid_interest,$c->interest_bal);
            $paid_interest -=$temp['paid_interest'];

            $temp['paid_fees'] = $this->calculate($paid_fees,$c->fees_bal);
            $paid_fees -=$temp['paid_fees'];

            $temp['paid_principal'] = $this->calculate($paid_principal,$c->principal_bal);
            $paid_principal -=$temp['paid_principal'];

            $temp['is_advance'] = 1;
            $temp['id_loan'] = $c->id_loan;

            if(($temp['paid_interest']+$temp['paid_fees']+$temp['paid_principal']) > 0){
                $amount_paid += $temp['paid_interest']+$temp['paid_fees']+$temp['paid_principal'];
                array_push($output['payments'],$temp);
            }
        }

        $output['amount_paid'] = $amount_paid;
        return $output;
    }
    // public function calculate($amount,$due){
    //     if($amount >= $due){
    //         $p_amount = $due;
    //     }elseif($amount < $due && $amount > 0){
    //         $p_amount =$amount;
    //     }else{
    //         $p_amount = 0;
    //     } 
    //     return ROUND($p_amount,2);
    // }
    public function due_dates(Request $request){
        // return $request;
        $data['dues'] = DB::table('loan')
        ->select(DB::raw("lt.due_date,DATE_format(lt.due_date,'%M %d, %Y') as due_date_text"))
        ->leftJoin("loan_table as lt","lt.id_loan","=","loan.id_loan")
        ->Where("loan.loan_status",1)
        ->whereIn("lt.is_paid",[0,2])
        ->where('loan.id_member',$request->id_member)
        // ->where('lt.due_date','>=',date("Y-m-t", strtotime($request->transaction_date)))
        ->where('lt.due_date','>=',date("Y-m-d", strtotime($request->transaction_date)))
        ->groupBy('due_date')
        ->orDerby('due_date')
        ->get();

        // $data['def_due'] = $this->decodeDueDate($request->transaction_date);
        $data['def_due'] = $this->decodeDueDate($data['dues'][0]->due_date??$request->transaction_date);
        return $data;
    }
    public function index(Request $request){
        // return Loan::LoanDetails(24);
        // return Member::getCBU(20);

        // return $this->GenerateRepaymentCashReceiptData(2510,1);
        $data['current_date'] = MySession::current_date();
        $start = date('Y-m-d', strtotime('-7 days'));
        $end = MySession::current_date();

        $data['start_date'] = $request->start_date ?? $start;
        $data['end_date'] = $request->end_date ?? $end;
        $data['selected_id_member'] = $request->id_member ?? 0;

        $data['selected_member'] =DB::table('member')
        ->select(DB::raw("concat(membership_id,' || ',first_name,' ',last_name) as member_name,id_member"))
        ->where('id_member',$request->id_member)
        ->first();


        $data['credential']= CredentialModel::GetCredential(MySession::myPrivilegeId());
        if(!$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
        $data['repayment_list'] = DB::table('repayment_transaction as rt')
        ->select(DB::raw("repayment_token,id_repayment_transaction,DATE_FORMAT(rt.transaction_date,'%M %d, %Y') as repayment_date,DATE_FORMAT(rt.date,'%M %d, %Y') as loan_due_date,concat(m.first_name,' ',m.last_name) as member_name,
            FORMAT(swiping_amount,2) as swiping_amount,FORMAT(total_payment,2) as total_payment,FORMAT(`change`,2) as 'change',
            if(transaction_type =1,'Cash','ATM Swipe') as paymode,rt.or_no,
            DATE_FORMAT(rt.date_created,'%M %d, %Y %r') as date_created,rt.status"))
        ->leftJoin('member as m','m.id_member','rt.id_member')
        ->where('repayment_type',1)
        ->where('rt.transaction_date','>=',$data['start_date'])
        ->where('rt.transaction_date','<=',$data['end_date'])
        ->where(function($query) use($data){
            if($data['selected_id_member'] > 0){
                $query->where('rt.id_member',$data['selected_id_member']);
            }
        })
        ->orDerby('rt.id_repayment_transaction','DESC')
        ->get();

        return view('repayment.index',$data);
    }
    public function create(){

        // return 
        $data['opcode'] = 0;
        // $data['repayment_fee'] = DB::table('repayment_fee_type')->where('show','=',1)->get();
        $data['repayment_fee'] = DB::table('tbl_payment_type')->select('id_payment_type','description','default_amount as amount')->where('type','=',3)->get();
        $data['current_date'] = MySession::current_date();
        $data['penalties'] = DB::table('tbl_payment_type')->select('id_payment_type','description')->where('type','=',4)->get();
        $data['allow_post'] = true;
        $data['banks']= DB::table('tbl_bank')->get();

        return view('repayment.repayment_form',$data);
    }
    public function view($token){

        $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/repayment');
        $data['opcode'] = 1;
        $data['allow_post'] = true;
        $data['repayment_transaction'] = DB::table('repayment_transaction as rt')
        ->select("transaction_type","id_repayment_transaction","repayment_token",DB::raw("concat(membership_id,' || ',first_name,' ',last_name) as selected_member,member.id_member,rt.date,rt.swiping_amount,rt.remarks,rt.transaction_date,date_format(rt.date,'%M %d, %Y') as due_date_text,rt.or_no,rt.change,rt.status,rt.cancel_reason,rt.id_bank,rt.id_journal_voucher,rt.id_cash_receipt_voucher"))
        ->leftJoin('member','member.id_member','rt.id_member')
        ->where('repayment_token',$token)
        ->first();

        $id_member = $data['repayment_transaction']->id_member;
        $date = $data['repayment_transaction']->date;
        $id_repayment_transaction = $data['repayment_transaction']->id_repayment_transaction;


        $loan_dues = $this->parseLoanDues($id_member,$date,$id_repayment_transaction,$data['repayment_transaction']->status,$data['repayment_transaction']->transaction_date);
        
        // return $loan_dues;  

        // return $loan_dues;
        $g = new GroupArrayController();
        $data['loan_dues'] = $g->array_group_by($loan_dues['dues_converted'],['type']);

        



        $data['total_loan_due'] = $loan_dues['total_loan_due'];
        $data['banks']= DB::table('tbl_bank')->get();

        $data['repayment_penalty']  = DB::table('repayment_penalty as rp')
        ->select('rp.id_payment_type','rp.amount','pt.description')
        ->leftJoin('tbl_payment_type as pt','pt.id_payment_type','rp.id_payment_type')
        ->where('rp.id_repayment_transaction',$id_repayment_transaction)
        ->orDerby('rp.id_repayment_penalty')
        ->get();

        // return $data['repayment_penalty'];
        if($data['repayment_transaction']->status == 10){
            $view = 'repayment.repayment_view';
        }else{
            // $view = 'repayment.repayment_view';
            $view = 'repayment.repayment_form';
        }
        //CHECK IF THERE IS REPAYMENT MADE NEXT MONTH
        $date_check = $this->decodeDueDate($data['repayment_transaction']->date);
        $transaction_next = DB::table('repayment_transaction as rt')
        ->where('rt.date','>',$date_check)
        ->where('rt.id_member',$data['repayment_transaction']->id_member)
        ->count();

        if($transaction_next > 0){
         $data['allow_post'] = true;
     }

     $data['repayment_fee'] = DB::table('tbl_payment_type')->select('id_payment_type','description','default_amount as amount')->where('type','=',3)->get();
        // $data['repayment_fee'] = DB::table('repayment_fee_type')->where('show','=',1)->get();
     $data['current_date'] = MySession::current_date();
     $data['penalties'] = DB::table('tbl_payment_type')->select('id_payment_type','description')->where('type','=',4)->get();

       // return $loan_dues;

     $data['member_info'] = DB::table('member')->select(DB::raw("concat(first_name,' ',last_name) as name,id_member,member_code"))->where('id_member',$id_member)->first();
     $data['repayment_date'] = $date;
     $data['is_valid_loan'] = (count($data['loan_dues']) > 0)?true:false;
     $data['due_date'] =  $this->decodeDueDate($date);

     $data['repayment_fee_val'] = DB::table('repayment_fees as rf')
     ->select('rf.id_payment_type','rf.amount')
     ->where('rf.id_repayment_transaction',$id_repayment_transaction)
     ->get();
     $data['repayment_fee_val'] = DB::table('tbl_payment_type as tp')
     ->select('tp.id_payment_type','tp.description',DB::raw("ifnull(rf.amount,0) as amount"))
     ->leftJoin('repayment_fees as rf',function($join) use($id_repayment_transaction){
        $join->on('rf.id_payment_type','tp.id_payment_type');
        $join->on('rf.id_repayment_transaction',DB::raw("$id_repayment_transaction"));
    })
     ->where('tp.type','=',3)
     ->get();

     $data['repayment_change'] = DB::table('repayment_change as rc')
     ->select(DB::raw("rc.id_repayment_change,DATE_FORMAT(rc.date,'%m/%d/%Y') as date,rc.amount"))
     ->where('id_repayment_transaction',$data['repayment_transaction']->id_repayment_transaction)
     ->where('rc.status','<>',10)
     ->get();

     $count_payroll = 0;

     if($data['repayment_transaction']->transaction_type==3){
        $count_payroll = DB::table('payroll_ca as pc')
                     ->leftJoin("payroll as p","p.id_payroll","pc.id_payroll")
                     ->where("pc.id_journal_voucher",$data['repayment_transaction']->id_journal_voucher)
                     ->where('p.status','<>',10)
                     ->count();        
     }

    if(count($data['repayment_change']) > 0 || $data['repayment_transaction']->status >=10 || $count_payroll > 0){
        $data['allow_post'] = false;
    }



    return view($view,$data);

    return $data;
}
public function decodeDueDate($date){
    $month = date("m", strtotime("$date"));

    if($month == 2){
        return date("Y-m-t", strtotime($date));
    }else{
        return date("Y-m-30", strtotime($date));
    }
}
public function get_loan_due(Request $request){
        // if($request->ajax()){
    $id_member = $request->id_member;
    $date = $request->date;
    $transaction_date = $request->transaction_date;
            // $id_repayment_transaction = $request->id_repayment_transaction;

            // return $date;
    $loan_dues = $this->parseLoanDues($id_member,$date,0,0,$transaction_date);
    // $data['loan_dues'] = $loan_dues['dues_converted'];
    $g = new GroupArrayController();
    $data['loan_dues'] = $g->array_group_by($loan_dues['dues_converted'],['type']);
    $data['total_loan_due'] = $loan_dues['total_loan_due'];
    $data['member_info'] = DB::table('member')->select(DB::raw("concat(first_name,' ',last_name) as name,id_member,member_code"))->where('id_member',$id_member)->first();
    $data['repayment_date'] = $date;
    $data['is_valid_loan'] = (count($data['loan_dues']) > 0)?true:false;
    $data['due_date'] =  $this->decodeDueDate($date);

    return response($data);
        // }
}

public function set_validation_param($payments){
    $for_validation = array();
    if(isset($payment)){
        foreach($payments as $loan_token=>$payment){
            if(($payment['principal'] + $payment['interest']+$payment['fees']) > 0){
                $for_validation[$loan_token] = $payment['principal'];
            }       
        }   
    }


    return $for_validation;

}
public function validate_payment($current,$previous,$date,$id_repayment_transaction){
    $invalid['current'] = array();
    $invalid['previous'] = array();
    $invalid['isValid'] =true;

    $param['current'] = $this->set_validation_param($current);
    $param['previous'] = $this->set_validation_param($previous);


    $loan_tokens = array();

    foreach($param['current'] as $loan_token=>$val){
        array_push($loan_tokens,$loan_token);
    }
    foreach($param['previous'] as $loan_token=>$val){
        array_push($loan_tokens,$loan_token);
    }

    $loan_tokens = array_unique(array_unique($loan_tokens));


    $search_token = "'".implode("','",$loan_tokens)."'";

    $validator = DB::select("SELECT loan_token, 
                            SUM(CASE WHEN due_date >= ? THEN principal_balance ELSE 0 END) as current,
                            SUM(CASE WHEN due_date < ? THEN principal_balance ELSE 0 END) as previous
                            FROM (
                            SELECT loan.loan_token,lt.due_date,lt.term_code,
                            lt.repayment_amount-ifnull(SUM(rl.paid_principal),0) as principal_balance
                            FROM loan 
                            LEFT JOIN loan_table as lt on lt.id_loan = loan.id_loan
                            LEFT JOIN repayment_loans as rl on rl.id_loan = lt.id_loan AND lt.term_code = rl.term_code and rl.status <> 10 and rl.id_repayment_transaction <> $id_repayment_transaction
                            where loan_token in ($search_token)
                            GROUP BY loan_token,lt.term_code) as ll
                            GROUP BY loan_token;",[$date,$date]);
    $g = new GroupArrayController();

    $validator = $g->array_group_by($validator,['loan_token']);


    foreach($param['current'] as $loan_token=>$val){
        $act_balance = $validator[$loan_token][0]->current;

        if($val > $act_balance){
            array_push($invalid['current'],$loan_token);
            $invalid['isValid'] = false;
        }
    }
    foreach($param['previous'] as $loan_token=>$val){
        $act_balance = $validator[$loan_token][0]->previous;

        if($val > $act_balance){
            array_push($invalid['previous'],$loan_token);
            $invalid['isValid'] = false;
        }
    }

    return $invalid;


    return $validator;

    return $search_token;
    return $loan_tokens;

}

public function post(Request $request){


    $date = $request->repayment_date;
    $current_payments = $request->current_payments;
    $previous_payments = $request->previous_payments;
    // return $previous_payments;

    $payments = $current_payments;
    $data['invalids'] = null;




    $id_member = $request->id_member;
    $swiping_amount = $request->swiping_amount ?? 0;
    $fees = $request->fees ?? [];
    $id_bank = $request->id_bank ?? 0;

    $transaction_type = $request->transaction_type;

    $penalties = $request->penalties ?? [];
    
    $repayment_token = $request->repayment_token;
    $opcode = $request->opcode;

    if($opcode == 0){
        $id_repayment_transaction = 0;
    }else{
        $rep =DB::table('repayment_transaction')->where('repayment_token',$repayment_token)->first();
        $id_repayment_transaction = $rep->id_repayment_transaction;
        $transaction_type = $rep->transaction_type;
          // SELECT * FROM payroll_ca as pc
          //           LEFT JOIN payroll as p on p.id_payroll = pc.id_payroll
          //           WHERE pc.id_journal_voucher = 20 AND p.status <> 10
    }
    $validation_payment = $this->validate_payment($current_payments,$previous_payments,$date,$id_repayment_transaction);

    if(!$validation_payment['isValid']){
        $data['RESPONSE_CODE'] = "ERROR";
        $data['message'] = "Invalid Amount";
        $data['invalids'] = $validation_payment;

        return response($data);
    }
    
    // $id_repayment_transaction = DB::table('repayment_transaction')->where('repayment_token',$repayment_token)->first()->id_repayment_transaction ?? 0;

    $total_penalties = $this->parseTotalPenalties($penalties);
    $fees = $this->parseFees($fees);

    $fees_insert = $fees['FEES'];
    $total_fees = $fees['TOTAL'];

    $previous =  $this->populate_previous_payments($previous_payments ?? [],$date,$id_repayment_transaction ?? 0);

    $current = $this->populate_current_payments($current_payments??[],$date,$id_repayment_transaction ?? 0);

    // //VALIDATE TOTALS
    // $push = $current['PUSH_DATA'];

    // $push_ar = array();

    // foreach($push as $p){
    //     $push_ar[$p['id_loan']]['principal'] = 0;
    // }

    // foreach($push as $p){
    //     $push_ar[$p['id_loan']]['principal'] += $p['paid_principal'];
    //     $push_ar[$p['id_loan']]['principal']  = ROUND($push_ar[$p['id_loan']]['principal'],2);
    // }

    // return $push_ar;

    

    // return $previous;
    $total_loan_due = $previous['TOTAL'] + $current['TOTAL'];
    $total_payment = $total_penalties + $total_fees + $total_loan_due;


    $change = ($transaction_type == 1 || $transaction_type == 3)?0:($swiping_amount - $total_payment);


    $loan_tokens = array_values(array_unique(array_merge($previous['LOAN_TOKENS'],$current['LOAN_TOKENS'])));

    $dues = array_merge($previous['PUSH_DATA'],$current['PUSH_DATA']);
    $rt_ispaid = array_merge($previous['IS_PAID'],$current['IS_PAID']);
    $edited = false;

    if($change < 0){

        return $total_payment;
        return "OOPS";
    }
    if($opcode == 0){
            //INSERT REPAYMENT TRANSACTION
        DB::table('repayment_transaction')
        ->insert([
            'repayment_token'=> DB::raw("concat(DATE_FORMAT(now(),'%m%d%Y%H%i%s'),concat(LEFT(MD5(NOW()), 5)))"),
            'transaction_type' => $transaction_type,
            'transaction_date' => $request->transaction_date,
            'date' => $date,
            'id_member' => $id_member,
            'total_loan_payment' => $total_loan_due,
            'swiping_amount' => $swiping_amount,
            'total_fees' => $total_fees,
            'total_penalty' => $total_penalties,
            'total_payment' => $total_payment,
            'change' => $change,
            'id_bank' => $id_bank,
            'remarks' => $request->remarks
        ]);
        $repayment_transaction = DB::table('repayment_transaction')->select('id_repayment_transaction','repayment_token')->where('id_member',$id_member)->orderBy('id_repayment_transaction','DESC')->first();

        $id_repayment_transaction = $repayment_transaction->id_repayment_transaction;
        $repayment_token = $repayment_transaction->repayment_token;

    }else{
        $update_data = [
            'transaction_date' => $request->transaction_date,
            'transaction_type' => $transaction_type,
            'total_loan_payment' => $total_loan_due,
            'swiping_amount' => $swiping_amount,
            'total_fees' => $total_fees,
            'total_penalty' => $total_penalties,
            'total_payment' => $total_payment,
            'change' => $change,
            'id_bank' => $id_bank,
            'remarks' => $request->remarks
        ];

        // return $update_data;
        $changes = $this->validate_data_post($id_repayment_transaction,$update_data,$dues,$fees_insert,$penalties);



        // $repayment_
        // return $changes;
        // if(!$changes['valid']){
        //     $data['RESPONSE_CODE'] = "ERROR";
        //     $data['message'] = "No Changes";
        //     return response($data);
        // }
        $edited = true;
        $update_data['date_edited'] = DB::raw("now()");
        DB::table('repayment_transaction')
        ->where('repayment_token',$repayment_token)
        ->update($update_data);  

        DB::table('repayment_loans')->where('id_repayment_transaction',$id_repayment_transaction)->delete();
        DB::table('repayment_fees')->where('id_repayment_transaction',$id_repayment_transaction)->delete();
        DB::table('repayment_penalty')->where('id_repayment_transaction',$id_repayment_transaction)->delete();
    }

    

    

    for($i=0;$i<count($dues);$i++){
        $dues[$i]['id_repayment_transaction'] = $id_repayment_transaction;
    }


    for($i=0;$i<count($penalties);$i++){
        $penalties[$i]['id_repayment_transaction'] = $id_repayment_transaction;
    }
    for($i=0;$i<count($fees_insert);$i++){
        $fees_insert[$i]['id_repayment_transaction'] = $id_repayment_transaction;
    }
    DB::table('repayment_loans')
    ->insert($dues);

        //UPDATE IS PAID ON REPAYMENT TABLE
    // DB::table('loan_table')
    // ->where('id_loan',$table['id_loan'])
    $temp_id_loan = array();
    for($k=0;$k<count($rt_ispaid);$k++){
        $table = $rt_ispaid[$k];
        array_push($temp_id_loan,$table['id_loan']);
        // DB::table('loan_table')
        // ->where('id_loan',$table['id_loan'])
        // ->where('term_code',$table['term_code'])
        // ->update(['is_paid'=>$table['is_paid']]); 
    }
    $imp = implode(",",array_unique($temp_id_loan));

    DB::select("UPDATE loan_Table
                LEFT JOIN repayment_loans as rl on rl.term_code = loan_table.term_code and rl.id_loan = loan_table.id_loan and rl.id_repayment_Transaction = $id_repayment_transaction
                SET is_paid =CASE
                WHEN ifnull(getLoanTotalTermPayment(loan_table.id_loan,loan_table.term_code),0) = 0 THEN 0
                WHEN (total_due-ifnull(getLoanTotalTermPayment(loan_table.id_loan,loan_table.term_code),0)) <=0 THEN 1
                WHEN (loan_table.repayment_amount-ifnull(getLoanTotalTermPaymentType(loan_table.id_loan,loan_table.term_code,1),0)=0 AND rl.is_advance=1) THEN 1
                ELSE 2 END
                where loan_table.id_loan in ($imp) and rl.id_repayment_transaction is not null;");


    //Making sure all partially previous will tag as no payment
    DB::table('loan_table')
    ->whereIn('id_loan',array_values(array_unique($temp_id_loan)))
    ->where('is_paid',2)
    ->update(['is_paid'=>DB::raw("CASE
        WHEN ifnull(getLoanTotalTermPayment(id_loan,term_code),0) = 0 THEN 0
        WHEN (total_due-ifnull(getLoanTotalTermPayment(id_loan,term_code),0)) =0 THEN 1
        ELSE 2 END")]);

    DB::table('repayment_fees')
    ->insert($fees_insert);


    DB::table('repayment_penalty')
    ->insert($penalties);


    for($i=0;$i<count($loan_tokens);$i++){
        $count_not_paid = DB::table('loan')
        ->leftJoin('loan_table as lt','lt.id_loan','loan.id_loan')
        ->where('loan_token',$loan_tokens[$i])
        ->whereIn('is_paid',[0,2])
        ->count();

            if($count_not_paid == 0){ // All repayment is paid (UPDATE TO CLOSE STATUS)
                DB::table('loan')
                ->where('loan_token',$loan_tokens[$i])
                ->where('loan_status',1)
                ->wherE('status',3)
                ->update([
                    'status'=>6,
                    'loan_status' => 2
                ]);
            }else{
                DB::table('loan')
                ->where('loan_token',$loan_tokens[$i])
                ->update([
                    'status'=>3,
                    'loan_status' => 1
                ]);
            }
        }

        if($transaction_type == 2 || $transaction_type ==3){ // if transaction type is ATM Swipe
            //POST JV
            $jv = JVModel::RepaymentJV($id_repayment_transaction,$edited,false);
            if($jv['status'] == "SUCCESS"){
                DB::table('repayment_transaction')
                ->where('id_repayment_transaction',$id_repayment_transaction)
                ->update(['id_journal_voucher'=>$jv['id_journal_voucher']]);
            }            
        }else{
            //POST CRV
            $crv = CRVModel::RepaymentCRV($id_repayment_transaction,$edited,false);
            if($crv['status'] == "SUCCESS"){
                DB::table('repayment_transaction')
                ->where('id_repayment_transaction',$id_repayment_transaction)
                ->update(['id_cash_receipt_voucher'=>$crv['id_cash_receipt_voucher']]);
            }
        }

        if($opcode == 1){
            $or_number = DB::table('repayment_transaction')->select("or_no")->where('id_repayment_transaction',$id_repayment_transaction)->first();
            if(isset($or_number)){
                $or_no = $or_number->or_no;
                $this->GenerateRepaymentCashReceiptData($id_repayment_transaction,$or_no);
            }
        }



        $data['RESPONSE_CODE'] = "SUCCESS";
        $data['REPAYMENT_TOKEN'] = $repayment_token;
        $data['ID_REPAYMENT_TRANSACTION'] = $id_repayment_transaction;




        // return $dues;
        return response($data);

        return "success";
    }
    public function parseTotalPenalties($penalties){
        $total = 0;
        for($i=0;$i<count($penalties);$i++){
            $total += $penalties[$i]['amount'];
        }

        return $total;
    }
    public function parseFees($fees){
        $fee_db = DB::table('tbl_payment_type')
        ->Select("id_payment_type")
        ->where('type',3)
        ->get();

        $output["FEES"] = array();
        $output["TOTAL"] = 0;
        foreach($fee_db as $fdb){
            $f['id_payment_type'] = $fdb->id_payment_type;
            $f['amount'] = $fees[$fdb->id_payment_type] ?? 0; 
            $output["TOTAL"] += $f['amount'];
            array_push($output["FEES"],$f);
        }
        return $output;
    }
    public function map_repayment_data($repayments){
        // return $repayments;
        $repayment_post_data = array();
        $g = new GroupArrayController();
        // return $repayment_post_data['2020211209231714'];
        return $repayment_post_data;
    }
    public function parseTotalFees($fees){
        $total = 0;
        for($i=0;$i<count($fees);$i++){
            $total += $fees[$i]['amount'];
        }
        return $total;
    }

    public function parseLoanDues($id_member,$date,$id_repayment_transaction,$status,$transaction_date){
        $date = $this->decodeDueDate($date);
        $transaction_date = $this->decodeDueDate($transaction_date);

        if($status >= 10){
            $sql = "SELECT lt.id_loan_table,loan.loan_token,lt.due_date,loan.id_loan,concat(getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms)) as loan_service_name ,
            1 as principal,1 as interest,1 as fees,SUM(rl.paid_principal) as paid_principal,SUM(rl.paid_interest) as paid_interest,SUM(rl.paid_fees) as paid_fees,if(rl.type = 1,'current','previous') as type,if(rl.type=1,rl.id_repayment_loans,'xx') as identifier
            ,if(loan.status =6,'closed','') as loan_status,lt.interest_amount as act_interest,lt.fees as act_fees
            FROM repayment_transaction as rt
            LEFT JOIN repayment_loans as rl on rl.id_repayment_transaction = rt.id_repayment_transaction
            LEFT JOIN loan on loan.id_loan = rl.id_loan
            LEFT JOIN loan_service as ls on ls.id_loan_service  = loan.id_loan_service
            LEFT JOIN loan_table as lt on lt.id_loan = rl.id_loan AND lt.term_code = rl.term_code
            where rt.id_Repayment_transaction =$id_repayment_transaction
            GROUP BY rl.id_loan,type
            ORDER BY rl.type DESC,lt.id_loan;";
        }else{
            // ,lt.interest_amount as act_interest,lt.fees as act_fees
            // SUM(CASE WHEN due_date < '$transaction_date' THEN interest ELSE 0 END) as interest
            // SUM(interest) as interest2,
            // 
            $add_sql = ($id_repayment_transaction > 0)?" AND  rlp.id_repayment_transaction = $id_repayment_transaction":"";
            $loan_status_cond  = ($id_repayment_transaction > 0)?" OR loan.status=6":"";


 // SELECT lt.id_loan_table,loan.loan_token,lt.due_date,loan.id_loan,concat(getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms)) as loan_service_name,
 //                 (lt.repayment_amount - ifnull(SUM(if(lt.term_code=rlp.term_code,0,rl.paid_principal)),0)) as principal,
 //                (lt.interest_amount - ifnull(SUM(rl.paid_interest),0)) as interest,
 //                (lt.fees - ifnull(SUM(rl.paid_fees),0)) as fees,
 //                ifnull(SUM(rlp.paid_principal),0) as paid_principal,
 //                ifnull(SUM(rlp.paid_interest),0) as paid_interest,
 //                ifnull(SUM(rlp.paid_fees),0) as paid_fees,
 //                'current' as type,1 as type_order,if(loan.status =6,'closed','') as loan_status,lt.interest_amount as act_interest,lt.fees as act_fees
 //                FROM loan
 //                LEFT JOIN loan_table as lt on lt.id_loan = loan.id_loan
 //                LEFT JOIN loan_service as ls on ls.id_loan_service  = loan.id_loan_service
 //                LEFT JOIN repayment_loans as rl on rl.id_loan = lt.id_loan and rl.term_code = lt.term_code and rl.id_repayment_transaction <> $id_repayment_transaction and rl.status < 10
 //                LEFT JOIN repayment_loans as rlp on rlp.id_loan = lt.id_loan and rlp.term_code = lt.term_code and rlp.id_repayment_transaction = $id_repayment_transaction and rlp.status < 10 AND rlp.type = 1
 //                WHERE id_member = ? and (loan_status = 1 $loan_status_cond) and  (due_date = ? OR rlp.type = 1) $add_sql
 //                GROUP BY loan.id_loan


            $sql = "SELECT * FROM (
                SELECT lt.id_loan_table,loan.loan_token,lt.due_date,loan.id_loan,concat(getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms)) as loan_service_name,
                (lt.repayment_amount - ifnull(getLoanTotalTermPaymentTypeEx(lt.id_loan,lt.term_code,1,$id_repayment_transaction),0)) as principal,
                (lt.interest_amount - ifnull(getLoanTotalTermPaymentTypeEx(lt.id_loan,lt.term_code,2,$id_repayment_transaction),0)) as interest,
                (lt.fees - ifnull(getLoanTotalTermPaymentTypeEx(lt.id_loan,lt.term_code,3,$id_repayment_transaction),0)) as fees,
                ifnull(SUM(rlp.paid_principal),0) as paid_principal,
                ifnull(SUM(rlp.paid_interest),0) as paid_interest,
                ifnull(SUM(rlp.paid_fees),0) as paid_fees,
                'current' as type,1 as type_order,if(loan.status =6,'closed','') as loan_status,lt.interest_amount as act_interest,lt.fees as act_fees,getLoanOverallBalance(loan.id_loan,1)+ifnull(SUM(rlp.paid_principal),0) as principal_balance
                FROM loan
                LEFT JOIN loan_table as lt on lt.id_loan = loan.id_loan
                LEFT JOIN loan_service as ls on ls.id_loan_service  = loan.id_loan_service
                LEFT JOIN repayment_loans as rlp on rlp.id_loan = lt.id_loan and rlp.term_code = lt.term_code and rlp.id_repayment_transaction = $id_repayment_transaction and rlp.status < 10 AND rlp.type = 1
                WHERE id_member = ? and (loan_status = 1 $loan_status_cond) and  (due_date = ? OR rlp.type = 1) $add_sql
                GROUP BY loan.id_loan
                UNION ALL
                SELECT id_loan_table,loan_token,due_date,id_loan,loan_service_name,SUM(principal) as principal
                ,SUM(interest) as interest,SUM(fees) as fees,SUM(paid_principal) as paid_principal,SUM(paid_interest) as paid_interest,SUM(paid_fees) as paid_fees,type,0 as type_order,loan_status,act_interest,act_fees,principal_balance FROM (
                    SELECT lt.id_loan_table,loan.loan_token,lt.due_date,loan.id_loan,concat(getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms)) as loan_service_name,
                    (lt.repayment_amount - ifnull(SUM(rl.paid_principal),0)) as principal,
                    (lt.interest_amount - ifnull(SUM(rl.paid_interest),0)) as interest,
                    (lt.fees - ifnull(SUM(rl.paid_fees),0)) as fees,
                    ifnull(rlp.paid_principal,0) as paid_principal,
                    ifnull(rlp.paid_interest,0) as paid_interest,
                    ifnull(rlp.paid_fees,0) as paid_fees,
                    'previous' as type,if(loan.status =6,'closed','') as loan_status,lt.interest_amount as act_interest,lt.fees as act_fees,getLoanOverallBalance(loan.id_loan,1)+ ifnull(rlp.paid_principal,0) as principal_balance
                    FROM loan
                    LEFT JOIN loan_table as lt on lt.id_loan = loan.id_loan
                    LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
                    LEFT JOIN repayment_loans as rl on rl.id_loan = lt.id_loan and rl.term_code = lt.term_code and rl.id_repayment_transaction <> $id_repayment_transaction and rl.status < 10
                    LEFT JOIN repayment_loans as rlp on rlp.id_loan = lt.id_loan and rlp.term_code = lt.term_code and rlp.id_repayment_transaction = $id_repayment_transaction and rlp.status < 10
                    WHERE loan.id_member = ? and (loan_status = 1 $loan_status_cond) and  due_date < ? $add_sql
                    GROUP BY loan.id_loan,lt.term_code) as previous
                GROUP BY id_loan
                ) as loan_due
            WHERE loan_due.principal > 0 OR loan_due.interest > 0 OR loan_due.fees > 0
            ORDER BY type_order,loan_due.id_loan;";      
            // return $sql;      
        }
        $dues = DB::select($sql,[$id_member,$date,$id_member,$date]);
        $output = array();
        $total_loan_due =0;
        $dues_converted = $dues;

        foreach($dues as $d){
            $total_loan_due += $d->principal+$d->interest+$d->fees;
        }

        $output['dues_converted'] = $dues;
        $output['total_loan_due'] = ROUND($total_loan_due,2);


        return $output;

        // return $dues;
        $output = array();
        $total_loan_due = 0;
        $dues_converted = array();

        foreach($dues as $d){
            // return 123;
            $temp_loan = array();
            if($d->principal > 0){
                $temp = array();

                $temp['type'] = $d->type;
                $temp['loan_token'] = $d->loan_token;
                $temp['id_loan_table'] = $d->id_loan_table;
                $temp['due_date'] = $d->due_date;
                $temp['loan_service'] = $d->loan_service_name." Principal";
                $temp['amount'] = $d->principal;
                $temp['amount_type'] = "principal";
                // $temp['service_remarks'] = ($temp['type'] == "previous")?"Previous ".date("m/d/Y",strtotime($d->due_date)):"";
                $temp['service_remarks'] = ($temp['type'] == "previous")?"Previous Balance":"";
                $temp['paid_amount'] = $d->paid_principal;
                $temp['loan_status'] = $d->loan_status;
                array_push($temp_loan,$temp);    
            }
            if($d->interest > 0){
                $temp = array();
                $temp['type'] =$d->type;
                $temp['loan_token'] = $d->loan_token;
                $temp['id_loan_table'] = $d->id_loan_table;
                $temp['due_date'] = $d->due_date;
                $temp['loan_service'] = $d->loan_service_name." Interest";
                $temp['amount'] = $d->interest;
                $temp['amount_type'] = "interest";
                // $temp['service_remarks'] = ($temp['type'] == "previous")?"Previous ".date("m/d/Y",strtotime($d->due_date)):"";
                $temp['service_remarks'] = ($temp['type'] == "previous")?"Previous Balance":"";
                $temp['paid_amount'] = $d->paid_interest;
                $temp['loan_status'] = $d->loan_status;
                array_push($temp_loan,$temp);                
            }
            if($d->fees > 0){
                $temp = array();
                $temp['type'] =$d->type;
                $temp['loan_token'] = $d->loan_token;
                $temp['id_loan_table'] = $d->id_loan_table;
                $temp['due_date'] = $d->due_date;
                $temp['loan_service'] = $d->loan_service_name." Fees";
                $temp['amount'] = $d->fees;
                $temp['amount_type'] = "fees";
                // $temp['service_remarks'] = ($temp['type'] == "previous")?"Previous Balance ".date("m/d/Y",strtotime($d->due_date)):"";
                $temp['service_remarks'] = ($temp['type'] == "previous")?"Previous Balance":"";
                $temp['paid_amount'] = $d->paid_fees;
                $temp['loan_status'] = $d->loan_status;
                array_push($temp_loan,$temp);                
            }

            $total_loan_due += $d->principal+$d->interest+$d->fees;

            array_push($dues_converted,$temp_loan);
        }
        $output['dues_converted'] = $dues_converted;
        $output['total_loan_due'] = $total_loan_due;
        return $output;
        $g = new GroupArrayController();

        return $dues;
    }


    public function populate_current_payments($current_payments,$date,$id_repayment_transaction){
        // $add_sql = ($id_repayment_transaction > 0)?" AND  rlp.id_repayment_transaction = $id_repayment_transaction":"";
        $add_sql = ($id_repayment_transaction > 0)?"OR loan.status = 6":"";
        $tokens = array();
        $date = $this->decodeDueDate($date);
        $is_paid_object = array();
        foreach($current_payments as $token=>$v){
            array_push($tokens,$token);
        }
        $imp = "('".implode("','",$tokens)."')";

        $sql = "    SELECT lt.id_loan_table,loan.loan_token,lt.due_date,loan.id_loan,getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms) as loan_service_name,
        (lt.repayment_amount - ifnull(SUM(rl.paid_principal),0)) as principal,
        (lt.interest_amount - ifnull(SUM(rl.paid_interest),0)) as interest,
        (lt.fees - ifnull(SUM(rl.paid_fees),0)) as fees,
        'current' as type,1 as type_order,lt.term_code
        ,lt.repayment_amount as act_principal
        ,lt.interest_amount as act_interest 
        ,lt.fees as act_fees
        FROM loan
        LEFT JOIN loan_table as lt on lt.id_loan = loan.id_loan
        LEFT JOIN loan_service as ls on ls.id_loan_service  = loan.id_loan_service
        LEFT JOIN repayment_loans as rl on rl.id_loan = lt.id_loan and rl.term_code = lt.term_code and rl.id_repayment_transaction <> ? and rl.status < 10
        WHERE (loan_status = 1 $add_sql) and  due_date = ? and loan.loan_token in $imp
        GROUP BY loan.id_loan";

        $current = DB::select($sql,[$id_repayment_transaction,$date]);
        // return $current;
        $g = new GroupArrayController();
        $grouped_current = $g->array_group_by($current,['loan_token']);
        $output = array();
        $output['TOTAL'] = 0;
        $push_object = array();
        $output['LOAN_TOKENS'] = $tokens;


        $rem_prin = 0;
        $rem_int=0;
        $rem_fees = 0;


        foreach($current_payments as $token=>$data){
            $temp= array();

            if(isset($grouped_current[$token][0])){
                $temp['id_loan'] = $grouped_current[$token][0]->id_loan;
                $temp['term_code'] = $grouped_current[$token][0]->term_code;
                $temp['paid_principal'] = $data['principal'] ?? 0;

                // return $temp['paid_principal'];
                $temp['paid_interest'] = $data['interest'] ?? 0;
                $temp['paid_fees'] = $data['fees'] ?? 0;
                $temp['type'] = 1;


                $temp['paid_principal'] = ($temp['paid_principal'] > $grouped_current[$token][0]->principal)?$grouped_current[$token][0]->principal:$temp['paid_principal'];

                $temp['paid_interest'] = ($temp['paid_interest'] > $grouped_current[$token][0]->interest)?$grouped_current[$token][0]->interest:$temp['paid_interest'];
                $temp['paid_fees'] = ($temp['paid_fees'] > $grouped_current[$token][0]->fees)?$grouped_current[$token][0]->fees:$temp['paid_fees'];

                $total_paid = $temp['paid_principal']+$temp['paid_interest']+$temp['paid_fees'];
                $total_dues = $grouped_current[$token][0]->principal+$grouped_current[$token][0]->interest+$grouped_current[$token][0]->fees;

                $total_term_total_payment =$grouped_current[$token][0]->act_principal+$grouped_current[$token][0]->act_interest+$grouped_current[$token][0]->act_fees;

                $output['TOTAL'] += $total_paid;
                // if($total_paid > 0){
                $temp_is_paid = array();
                $temp_is_paid["term_code"] = $grouped_current[$token][0]->term_code;
                $temp_is_paid["id_loan"] = $grouped_current[$token][0]->id_loan;


                if($total_dues == $total_term_total_payment && $total_paid == 0){
                    $temp_is_paid["is_paid"] = 0;    

                }else{
                    $temp_is_paid["is_paid"] = ($total_paid >= $total_dues)?1:2;    
                }

                $temp['is_advance'] = 0;
                $temp['on_term_code'] = $grouped_current[$token][0]->term_code;

                

                array_push($is_paid_object,$temp_is_paid);
                array_push($push_object,$temp); 

                //remainings
                $rem_prin =    $data['principal'] - $temp['paid_principal'];
                $rem_int =     $data['interest'] - $temp['paid_interest'];
                $rem_fees =    $data['fees'] - $temp['paid_fees'];

                // return $rem_prin;
                // }

                //PRINCIPAL MORE THAN
                $payments_over_prin = array();
                if($rem_prin > 0){
                   $over = $this->generate_advance_payment(0,$id_repayment_transaction,$grouped_current[$token][0]->id_loan,$rem_prin,0,0,$grouped_current[$token][0]->term_code,$date);
                   // return $over;
                    $payments_over_prin = $over['payments'];
                    $output['TOTAL'] += $over['total'];
                }

                $payments_over_in_fees = array();
                if($rem_int > 0 || $rem_fees > 0){
                    $over = $this->generate_advance_payment(1,$id_repayment_transaction,$grouped_current[$token][0]->id_loan,0,$rem_int,$rem_fees,$grouped_current[$token][0]->term_code,$date);

                    $payments_over_in_fees = $over['payments'];
                    $output['TOTAL'] += $over['total'];
                    foreach($payments_over_in_fees as $key=>$items){
                        if(isset($payments_over_prin[$key])){
                            $payments_over_prin[$key]['paid_interest'] += $payments_over_in_fees[$key]['paid_interest'];
                            unset($payments_over_in_fees[$key]);
                        }
                    }
                }

                foreach($payments_over_prin as $items){
                    array_push($push_object,$items);
                }

                foreach($payments_over_in_fees as $items){
                    array_push($push_object,$items);
                }
            }
        }



        $output['PUSH_DATA'] = $push_object;

        $output['IS_PAID'] = $is_paid_object;
        return $output;
    }

    public function generate_advance_payment($type,$id_repayment_transaction,$id_loan,$paid_principal,$paid_interest,$paid_fees,$term_code,$due_date){
        $out = array();

        // return $due_date;
        $terms = DB::table('repayment_loans')
                ->select("term_code")
                ->where('is_advance',1)
                ->where('id_repayment_transaction',$id_repayment_transaction)
                ->get();
                         // return $terms;

        $order = ($type==0)?"DESC":"ASC";


        $loan_table = DB::table('loan_table')
        ->select(DB::raw("*,(repayment_amount-getLoanTotalTermPaymentTypeEx(id_loan,term_code,1,$id_repayment_transaction)) as principal_balance,(interest_amount-getLoanTotalTermPaymentTypeEx(id_loan,term_code,2,$id_repayment_transaction)) as interest_balance"))
        ->where('id_loan',$id_loan)
        ->where(function($query) use($type,$due_date){
            // if($type == 1){
                $query->where('due_date','>',$due_date);
            // }
        })
        ->where(function($query) use($id_repayment_transaction){
            $query->where('is_paid','<>',1);
            if($id_repayment_transaction > 0){
                $terms = DB::table('repayment_loans')
                         ->select("term_code")
                         ->where('is_advance',1)
                         ->where('id_repayment_transaction',$id_repayment_transaction)
                         ->get();

                $t = array();
                foreach($terms as $te){
                    array_push($t,$te->term_code);
                }
                $query->OrwhereIn('term_code',$t);
            }
        })
        ->orDerby('due_date',$order)
        ->get(); 


        // return $loan_table;

        $insert_rp_loan = array();

        $insert_temp_loan = array();
        $total_paid = 0;

        foreach($loan_table as $lt){
            $temp = array();

            $p_principal = $this->calculate($paid_principal,$lt->principal_balance);
            $p_interest = $this->calculate($paid_interest,$lt->interest_balance);


            $temp['paid_principal'] = $p_principal;
            $temp['paid_interest'] = $p_interest;
            $temp['paid_fees'] = $paid_fees;
            $temp['is_advance'] = 1;
            $temp['on_term_code'] = $term_code;
            $temp['type'] = 1;
            $paid_principal = ROUND($paid_principal-$temp['paid_principal'],2);
            $paid_interest = ROUND($paid_interest-$temp['paid_interest'],2);

            $temp['term_code'] = $lt->term_code;
            $temp['id_loan'] = $lt->id_loan;

            if($temp['paid_principal']+$temp['paid_interest'] > 0){
                $due_date = $lt->due_date;
                $insert_temp_loan[$lt->term_code] = $temp;
                array_push($insert_rp_loan,$temp);  
            }

            $total_paid += ($temp['paid_principal']+$temp['paid_interest']);   
        }

        // $out['payments'] = $insert_rp_loan;
        $out['payments'] = $insert_temp_loan;
        $out['total'] = ROUND($total_paid,2);
        return $out;        

        return $loan_table;
    }


    public function calculate($amount,$due){
        if($amount >= $due){
            $p_amount = $due;
        }elseif($amount < $due && $amount > 0){
            $p_amount =$amount;
        }else{
            $p_amount = 0;
        } 

        return ROUND($p_amount,2);
    }
    public function populate_previous_payments($previous_payments,$date,$id_repayment_transaction){
        $add_sql = ($id_repayment_transaction > 0)?"OR loan.status = 6":"";

        $tokens = array();
        $date = $this->decodeDueDate($date);
        foreach($previous_payments as $token=>$v){
            array_push($tokens,$token);
        }
        
        // return $previous_payments;
        $imp = "('".implode("','",$tokens)."')";   
        $push_object = array();
        $is_paid_object = array();
        $output = array();
        $output['TOTAL'] = 0;
        $output['LOAN_TOKENS'] = $tokens;
        $sql="SELECT * FROM (
            SELECT lt.id_loan_table,loan.loan_token,lt.due_date,loan.id_loan,getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms) as loan_service_name,
            (lt.repayment_amount - ifnull(SUM(rl.paid_principal),0)) as principal,
            (lt.interest_amount - ifnull(SUM(rl.paid_interest),0)) as interest,
            (lt.fees - ifnull(SUM(rl.paid_fees),0)) as fees,
            'previous' as type,lt.term_code,lt.repayment_amount as act_principal
            ,lt.interest_amount as act_interest
            ,lt.fees as act_fees
            FROM loan
            LEFT JOIN loan_table as lt on lt.id_loan = loan.id_loan
            LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
            LEFT JOIN repayment_loans as rl on rl.id_loan = lt.id_loan and rl.term_code = lt.term_code and rl.id_repayment_transaction <> ? and rl.status < 10
            WHERE (loan_status = 1 $add_sql) and  due_date < ? and loan.loan_token in $imp
            GROUP BY loan.id_loan,lt.term_code) as previous
        WHERE previous.principal > 0 OR previous.interest > 0 OR previous.fees > 0
        ORDER BY previous.due_date";
        $previous = DB::select($sql,[$id_repayment_transaction,$date]);

        // return $previous;
        $g = new GroupArrayController();
        $grouped_previous = $g->array_group_by($previous,['loan_token']);

        foreach($previous_payments as $token=>$data){
            $paid_principal = $data['principal'] ?? 0;
            $paid_interest = $data['interest'] ?? 0;
            $paid_fees = $data['fees'] ?? 0;

            $gr_prev =$grouped_previous[$token] ?? [];

            foreach($gr_prev as $dues){

                if($paid_principal > 0){
                    $temp_paid_principal = $paid_principal;
                    $paid_principal = $paid_principal - $dues->principal;
                    $p_amount = ($paid_principal >= 0)?$dues->principal:$temp_paid_principal;
                }else{
                    $p_amount =0;
                }

                if($paid_interest > 0){
                    $temp_paid_interest = $paid_interest;
                    $paid_interest = $paid_interest - $dues->interest;
                    $i_amount = ($paid_interest >= 0)?$dues->interest:$temp_paid_interest;
                }else{
                    $i_amount =0;
                }

                if($paid_fees > 0){
                    $temp_paid_fees = $paid_fees;
                    $paid_fees = $paid_fees - $dues->fees;
                    $f_amount = ($paid_fees >= 0)?$dues->fees:$temp_paid_fees;
                }else{
                    $f_amount =0;
                }


                $temp= array();
                $temp['id_loan'] = $dues->id_loan;
                $temp['term_code'] = $dues->term_code;
                $temp['paid_principal'] = $p_amount ?? 0;
                $temp['paid_interest'] = $i_amount ?? 0;
                $temp['paid_fees'] = $f_amount ?? 0;
                $temp['type'] = 2;

                $total_term_total_payment =$dues->act_principal+$dues->act_interest+$dues->act_fees;
                $total_dues = $dues->principal+$dues->interest+$dues->fees;
                $total_paid = $temp['paid_principal']+ $temp['paid_interest']+$temp['paid_fees'];

                    // return $total_paid;

                    // if($total_paid > 0){
                $temp_is_paid = array();
                $temp_is_paid["term_code"] = $dues->term_code;
                $temp_is_paid["id_loan"] = $dues->id_loan;
                if($total_dues == $total_term_total_payment && $total_paid == 0){
                    $temp_is_paid["is_paid"] = 0;     
                }else{
                    $temp_is_paid["is_paid"] = ($total_paid >= $total_dues)?1:2;     
                }

                $temp['is_advance'] = 0;
                $temp['on_term_code'] = $grouped_previous[$token][0]->term_code;
                array_push($is_paid_object,$temp_is_paid);    
                array_push($push_object,$temp);     
                $output['TOTAL'] += $temp['paid_principal']+$temp['paid_interest']+$temp['paid_fees'];         
                    // }
            }

        }   
        $output['PUSH_DATA'] = $push_object;
        $output['IS_PAID'] = $is_paid_object;
        return $output;

        

        // foreach()
        return $previous;
    }
    public function check_or(Request $request){
        if($request->ajax()){
            $repayment_token = $request->repayment_token;

            $repayment = $d['test'] = DB::table('repayment_transaction')
            ->select('repayment_token','id_repayment_transaction','or_no')
            ->where('repayment_token',$repayment_token)
            ->first();
            if(isset($repayment)){
                if($repayment->or_no == null){
                    $response['RESPONSE_CODE'] = "SHOW_OR_ENTRY";

                }else{
                    $response['RESPONSE_CODE'] = "SHOW_PRINT";
                    $response['PRINT_REFERENCE'] = $repayment->id_repayment_transaction;
                }

                return response($response);
            }
            return response($d);
        }
    }
    public function post_or(Request $request){
        if($request->ajax()){
            $repayment_token = $request->repayment_token;
            $or_no = $request->or_no;
            $or_opcode = $request->or_opcode;

            $data['RESPONSE_CODE'] = "SUCCESS";

            $repayment = $d['test'] = DB::table('repayment_transaction')
            ->select('repayment_token','id_repayment_transaction','or_no','id_cash_receipt_voucher')
            ->where('repayment_token',$repayment_token)
            ->first();

            // Check OR NO
            $or_record = DB::table('cash_receipt')
            ->where(function($query) use ($or_opcode,$repayment){
                if($or_opcode == 1){
                    $query->where('type',3)
                    ->where('reference_no','<>',$repayment->id_repayment_transaction);
                }
            })
            ->where('or_no',$or_no)
            ->count();


            if($or_record > 0){
                $data['RESPONSE_CODE'] = "ERROR_POST";
                $data['message'] = "OR # $or_no already exists";
                return response($data);
            }


            if($repayment->or_no == null || $or_opcode == 1){
                DB::table('repayment_transaction')->where('repayment_token',$repayment_token)->update(['or_no'=>$or_no]);
                if($or_opcode == 1){
                    DB::table('cash_receipt')->where('type',3)->where('reference_no',$repayment->id_repayment_transaction)->update(['status'=> 10]);
                }
                $this->GenerateRepaymentCashReceiptData($repayment->id_repayment_transaction,$or_no);

                DB::table('cash_receipt_voucher')
                ->where('reference',$repayment->id_repayment_transaction)
                ->where('type',2)
                ->update([
                    'or_no'=>$or_no
                ]);
            }
            return response($data);
        }
    }
    public function print_repayment_or($id_repayment_transaction){

        $data['cash_receipt'] = DB::table('cash_receipt as cr')
        ->select(DB::raw("concat(first_name,' ',last_name,' ',suffix) as member_name,m.tin,or_no,DATE_format(date_received,'%m/%d/%Y') as transaction_date,total_payment,cr.id_cash_receipt,payment_remarks,m.address,cr.id_paymode"))
        ->leftJoin('member as m','m.id_member','cr.id_member')
        ->where('cr.reference_no',$id_repayment_transaction)
        ->where('cr.status','<>',10)
        ->where('cr.type',3)
        ->first();
        $details_count = DB::table('cash_receipt_details')->where('id_cash_receipt',$data['cash_receipt']->id_cash_receipt)->count();
        // return $data['cash_receipt']->id_cash_receipt;
        $id_cash_receipt = $data['cash_receipt']->id_cash_receipt;
        if($details_count <= 10){ // DISPLAY THE DETAILED REPAYMENT OR
            $sql = "SELECT crd.id_cash_receipt_details,pt.type,ifnull(crd.description,pt.description) as payment_description,crd.amount
            FROM cash_receipt_details as crd
            LEFT JOIN tbl_payment_type as pt on pt.id_payment_type = crd.id_payment_type
            WHERE crd.id_cash_receipt = $id_cash_receipt
            ORDER BY id_cash_receipt_details;";
        }else{
            $sql = "SELECT * FROM (
                SELECT id_cash_receipt_details,pt.type,
                CASE WHEN pt.type = 5 THEN concat('',getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms),' (ID#',crd.id_loan,')') 
                ELSE concat('',getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms),' (ID#',crd.id_loan,' Previous)')  END as 'payment_description' ,
                SUM(crd.amount) as amount
                FROM cash_receipt_details as crd
                LEFT JOIN tbl_payment_type as pt on pt.id_payment_type = crd.id_payment_type
                LEFT JOIN cash_receipt as cr on cr.id_cash_receipt = crd.id_cash_receipt
                LEFT JOIN loan on loan.id_loan =crd.id_loan
                LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
                where crd.id_cash_receipt= $id_cash_receipt and pt.type in (5,6)
                GROUP BY type,crd.id_loan
                UNION ALL
                SELECT id_cash_receipt_details,pt.type,pt.description as 'payment_description' ,
                crd.amount
                FROM cash_receipt_details as crd
                LEFT JOIN tbl_payment_type as pt on pt.id_payment_type = crd.id_payment_type
                where crd.id_cash_receipt= $id_cash_receipt and pt.type not in (5,6)) as k
            ORDER BY id_cash_receipt_details;";

                     // return 1321;
        }

        $data['cash_receipt_details'] = DB::select($sql);

        // return view('test2');
        return view('cash_receipt.cash_receipt_print',$data);
        return view("repayment.repayment_or_print",$data);
        return $data;

        return $details_count;


        return $data;
        return $id_repayment_transaction;
    }

    public function GenerateRepaymentCashReceiptData($id_repayment_transaction,$or_no){

        // $validate_cash_receipt = DB::table('cash_receipt')->where('type',3)->where('reference_no',$id_repayment_transaction)->count();

        $cr_data = DB::table('cash_receipt')->select("id_cash_receipt")->where('type',3)->where('reference_no',$id_repayment_transaction)->first();


        // if($validate_cash_receipt > 0){
        //     $response['RESPONSE_CODE'] = "ERROR";
        //     $response['message'] = "Loan Payment ID# $id_repayment_transaction has a cash receipt.";

        //     return $response;
        // }

        // $loan_repayments = DB::select("SELECT id_repayment_loans,rl.id_loan,concat(ls.name,' (ID#',rl.id_loan,')') as 'loan_name',term_code,paid_principal,paid_interest,paid_fees,type 
        // FROM repayment_loans as rl
        // LEFT JOIN loan on loan.id_loan = rl.id_loan
        // LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
        // where id_repayment_transaction = ?
        // AND (paid_principal+paid_interest+paid_fees) > 0;",[$id_repayment_transaction]);

        // $loan_repayments = DB::select("SELECT * FROM (
        //     SELECT id_repayment_loans,rl.id_loan,concat(ls.name,' (ID#',rl.id_loan,')') as 'loan_name',term_code,SUM(paid_principal) as paid_principal,
        //     SUM(paid_interest) as paid_interest,SUM(paid_fees) as paid_fees,type 
        //     FROM repayment_loans as rl
        //     LEFT JOIN loan on loan.id_loan = rl.id_loan
        //     LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
        //     where id_repayment_transaction = $id_repayment_transaction
        //     GROUP BY rl.id_loan,rl.type) as k
        // WHERE  (paid_principal+paid_interest+paid_fees) > 0;");


        $loan_repayments = DB::select("SELECT * FROM (
            SELECT id_repayment_loans,rl.id_loan,concat(getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms),' (ID#',rl.id_loan,')') as 'loan_name',term_code,SUM(paid_principal) as paid_principal,
            SUM(paid_interest) as paid_interest,SUM(paid_fees) as paid_fees,1 as type 
            FROM repayment_loans as rl
            LEFT JOIN loan on loan.id_loan = rl.id_loan
            LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
            where id_repayment_transaction = $id_repayment_transaction
            GROUP BY rl.id_loan) as k
        WHERE  (paid_principal+paid_interest+paid_fees) > 0;");
        

        // return $loan_repayments;
        // return config('variables.repayment_type_principal');

        $loan_repayments_obj = array();
        foreach($loan_repayments as $lr){
            //variables
            $repayment_type_principal = config("variables.repayment_type_principal".(($lr->type == 2)?"_previous":""));
            $repayment_type_interest = config("variables.repayment_type_interest".(($lr->type == 2)?"_previous":""));
            $repayment_type_fees = config("variables.repayment_type_fees".(($lr->type == 2)?"_previous":""));

            if($lr->paid_principal > 0){
                $temp = array();
                $temp['id_payment_type'] = $repayment_type_principal;
                $temp['amount'] = $lr->paid_principal;
                $temp['description'] = $lr->loan_name." Principal Amount".(($lr->type == 2)?" Previous":"");
                $temp['id_loan'] = $lr->id_loan;
                $temp['reference'] = $lr->id_repayment_loans;

                array_push($loan_repayments_obj,$temp);
            }
            if($lr->paid_interest > 0){
                $temp = array();
                $temp['id_payment_type'] = $repayment_type_interest;
                $temp['amount'] = $lr->paid_interest;
                $temp['description'] = $lr->loan_name." Interest Amount".(($lr->type == 2)?" Previous":"");
                $temp['id_loan'] = $lr->id_loan;
                $temp['reference'] = $lr->id_repayment_loans;

                array_push($loan_repayments_obj,$temp);
            }
            if($lr->paid_fees > 0){
                $temp = array();
                $temp['id_payment_type'] = $repayment_type_fees;
                $temp['amount'] = $lr->paid_fees;
                $temp['description'] = $lr->loan_name." Fees Amount".(($lr->type == 2)?" Previous":"");
                $temp['id_loan'] = $lr->id_loan;
                $temp['reference'] = $lr->id_repayment_loans;

                array_push($loan_repayments_obj,$temp);
            }
        }

        //Fees and Penalties
        $fees_penalties = DB::select("SELECT id_payment_type,amount,id_repayment_fees as reference FROM repayment_fees where id_repayment_transaction =?
            and amount > 0
            UNION
            SELECT id_payment_type,amount,id_repayment_penalty FROM repayment_penalty where id_repayment_transaction =? and amount > 0;",[$id_repayment_transaction,$id_repayment_transaction]);

        foreach($fees_penalties as $p){
            $temp = array();
            $temp['id_payment_type'] = $p->id_payment_type;
            $temp['amount'] = $p->amount;
            $temp['description'] = null;
            $temp['id_loan'] = 0;
            $temp['reference'] = $p->reference;
            array_push($loan_repayments_obj,$temp);
        }

        // $or_no = 1234;
        $ar_no = null;


        if(!isset($cr_data)){
            $opcode = 0;
        }else{
            $opcode = 1;
        }




        if($opcode == 0){
            //Insert Cash Receipt Parent
            DB::select("INSERT INTO cash_receipt (date_received,id_paymode,payee_type,id_member,or_no,ar_no,reference_no,total_payment,type,payment_remarks)
                SELECT transaction_date as date_received,1 as paymode,1 as payee_type,id_member,? as or_no,? as ar_no,id_repayment_transaction as reference_no,
                total_payment,3 as type,concat('Loan ID(s) ',getRepaymentTransactionIDLoans(id_repayment_transaction)) FROM repayment_transaction where id_repayment_transaction = ?;",[$or_no,$ar_no,$id_repayment_transaction]);

            $id_cash_receipt = DB::table('cash_receipt')->where('type',3)->where('reference_no',$id_repayment_transaction)->max('id_cash_receipt');            
        }else{
            DB::select("UPDATE repayment_transaction  as rt 
                        LEFT JOIN cash_receipt as cr on cr.type = 3 and cr.reference_no = rt.id_repayment_transaction
                        SET cr.date_received = rt.transaction_date,cr.total_payment=rt.total_payment,cr.payment_remarks =concat('Loan ID(s) ',getRepaymentTransactionIDLoans(id_repayment_transaction))
                        where rt.id_repayment_transaction =?;",[$id_repayment_transaction]);

            $id_cash_receipt = $cr_data->id_cash_receipt;
            DB::table('cash_receipt_details')
            ->where('id_cash_receipt',$id_cash_receipt)
            ->delete();
        }


        for($i=0;$i<count($loan_repayments_obj);$i++){
            $loan_repayments_obj[$i]['id_cash_receipt'] = $id_cash_receipt;
        }
        DB::table('cash_receipt_details')
        ->insert($loan_repayments_obj);




        return $loan_repayments_obj;
    }
    public function cancel_repayment(Request $request){
        if($request->ajax()){
            $id_repayment_transaction = $request->id_repayment_transaction;


            $details = DB::table('repayment_transaction')->where('id_repayment_transaction',$id_repayment_transaction)->first();
            // $data['RESPONSE_CODE'] = "ERROR";
            // $data['message'] = "ERROR MESSAGE";

            DB::table('repayment_transaction')
            ->where('id_repayment_transaction',$id_repayment_transaction)
            ->update(['status'=>10,'cancel_reason'=>$request->cancel_reason,'date_cancelled'=>DB::raw("now()")]);

            $current_paid_status = DB::select("SELECT id_loan,term_code,
                CASE
                WHEN paid =0 THEN 0
                WHEN total_due = paid THEN 1
                ELSE 2 END as is_paid
                FROM (
                    select rl.id_loan,rl.term_code,total_due,getLoanTotalTermPayment(rl.id_loan,rl.term_code) as paid
                    FROM repayment_loans as rl 
                    LEFT JOIN loan_table as lt on lt.id_loan = rl.id_loan AND lt.term_code = rl.term_Code
                    where id_repayment_transaction = $id_repayment_transaction
                    GROUP BY rl.id_loan,rl.term_code) as k;");

            $cancelled_repayment_loans = array();
            foreach($current_paid_status as $cp){
                DB::table('loan_table')
                ->where('id_loan',$cp->id_loan)
                ->wherE('term_code',$cp->term_code)
                ->update(['is_paid'=>$cp->is_paid]);

                array_push($cancelled_repayment_loans,$cp->id_loan);
            }

            DB::table('loan')
            ->whereIn('id_loan',$cancelled_repayment_loans)
            ->update(['status'=>3,'loan_status'=>1]);
           
            if($details->transaction_type == 1){ // CASH
                CRVModel::RepaymentCRV($id_repayment_transaction,false,true);
            }else{ // ATM SWIPE
                JVModel::RepaymentJV($id_repayment_transaction,false,true);
            } 

            DB::table('cash_receipt')
            ->where('type',3)
            ->where('reference_no',$id_repayment_transaction)
            ->update(['status'=>"10","cancel_reason"=>$request->cancel_reason]);
            
            
            $data['RESPONSE_CODE'] = "SUCCESS";
            $data['REPAYMENT_TOKEN'] = $details->repayment_token;

            return response($data);

            return $id_repayment_transaction;
        }
    }
    public function validate_summary(Request $request){
        if($request->ajax()){
            $transaction_date = $request->transaction_date;
            $transaction_type = $request->transaction_type;

            // return $transaction_type;

            $count = DB::table('repayment_transaction')
            ->where('transaction_date',$transaction_date)
            ->where('transaction_type',$transaction_type)
            ->count();


            if($count == 0){
                $response['RESPONSE_CODE'] = "ERROR";
                $response['message'] = "No Transaction Found";

                return response($response);
            }

            $response['RESPONSE_CODE'] = "SUCCESS";
            $response['transaction_date'] = $transaction_date;
            $response['transaction_type'] = $transaction_type;

            return response($response);
        }
    }
    public function repayment_summary($transaction_date,$transaction_type){
        if(!isset($transaction_date)){
            return "INVALID REQUEST";
        }

        $summary = DB::select("CALL RepaymentSummary2(?,?,?)",[$transaction_date,$this->decodeDueDate($transaction_date),$transaction_type]);


        $g = new GroupArrayController();
        $data['repayment_summary'] = $g->array_group_by($summary,['borrower']);
        $data['transaction_date'] = date("F d,Y", strtotime($transaction_date));
        $data['trans'] = ($transaction_type == 1)?"Cash":"ATM Swipe";


        $html =  view('repayment.print_summary',$data);




        $pdf = PDF::loadHtml($html);
        $pdf->setOption("encoding","UTF-8");
        $pdf->setOption('margin-bottom', '5mm');
        $pdf->setOption('margin-top', '7mm');
        $pdf->setOption('margin-right', '5mm');
        $pdf->setOption('margin-left', '5mm');
        $pdf->setOption('header-left', 'Page [page] of [toPage]');
    
        $pdf->setOption('header-font-size', 8);
        $pdf->setOption('header-font-name', 'Calibri');
        $pdf->setOrientation('landscape');

        return $pdf->stream();


        
        $dompdf = new Dompdf();
        $dompdf->set_option("isRemoteEnabled",false);
        $dompdf->set_option("isPhpEnabled", true);

        $dompdf->loadHtml($html);
        $dompdf->render();
        $font = $dompdf->getFontMetrics()->get_font("helvetica","normal");
        $dompdf->getCanvas()->page_text(24, 18, "Page {PAGE_NUM} of {PAGE_COUNT}", $font, 8, array(0,0,0));
        $canvas = $dompdf->getCanvas();
        $canvas->page_script('
          if ($PAGE_NUM > 1) {
            $font = $fontMetrics->getFont("helvetica","normal");
            $current_page = $PAGE_NUM-1;
            $total_pages = $PAGE_COUNT-1;
            
        }
        ');

        // $pdf->text(480, 18, "Control No.: '.$data['details']->control_number.'", $font, 8, array(0,0,0));
        //     $pdf->text(480, 30, "Account No.: '.$data['details']->account_no.'", $font, 8, array(0,0,0));
        $dompdf->stream("repayment_summary.pdf", array("Attachment" => false));  



        exit;

        return $summary;
    }
    public function validate_data_post($id_repayment_transaction,$parent_data,$dues,$fees_insert,$penalties){
        // return $parent_data;
        $current_data = DB::table('repayment_transaction')
        ->select("transaction_date","transaction_type","total_loan_payment","swiping_amount","total_fees","total_penalty","total_payment","change","remarks","id_bank")
        ->where('id_repayment_transaction',$id_repayment_transaction)
        ->first();
        
        $output['valid'] = false;



        foreach($current_data as $key=>$val){
            if($val != $parent_data[$key]){
               $output['valid'] = true;
           }
       }
       if($output['valid']){
        return $output;
    }
        // VALIDATE CHANGES IN DUES
    foreach($dues as $d){
        $count = DB::table('repayment_loans')
        ->where('id_repayment_transaction',$id_repayment_transaction)
        ->where('term_code',$d['term_code'])
        ->where('paid_principal',$d['paid_principal'])
        ->where('paid_interest',$d['paid_interest'])
        ->where('paid_fees',$d['paid_fees'])
        ->where('type',$d['type'])
        ->count();
        if($count == 0){
           $output['valid'] =true;
           return $output;
           break;
       }
   }

        // VALIDATE FEES
   foreach($fees_insert as $fee){
    $count = DB::table('repayment_fees')
    ->where('id_payment_type',$fee['id_payment_type'])
    ->where('amount',$fee['amount'])
    ->where('id_repayment_transaction',$id_repayment_transaction)
    ->count();
    if($count == 0){
        $output['valid'] = true;
        return $output;
    }
}

        // VALIDATE PENALTIES
$penalty_count = DB::table('repayment_penalty')->where('id_repayment_transaction',$id_repayment_transaction)->count();
        // return $penalties;
if($penalty_count <> count($penalties)){
    $output['valid'] = true;
    return $output;
}


foreach($penalties as $pen){
    $count = DB::table('repayment_penalty')
    ->where('id_payment_type',$pen['id_payment_type'])
    ->where('amount',$pen['amount'])
    ->where('id_repayment_transaction',$id_repayment_transaction)
    ->count();
    if($count == 0){
        $output['valid'] = true;
        return $output;
    }
}

return $output;


}
}

// SELECT rt.id_repayment_transaction,loan.loan_token,rl.id_loan,ls.name as 'loan_service_name',loan.principal_amount,loan.interest_rate,concat(terms,' ',period.description) as terms,
//         loan.loan_amount,lt.term_code,(lt.count) as repayment_made
// FROM repayment_transaction as rt
// LEFT JOIN repayment_loans as rl on rl.id_repayment_transaction = rt.id_repayment_transaction
// LEFT JOIN loan on loan.id_loan = rl.id_loan
// LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
// LEFT JOIN period on period.id_period = loan.id_term_period
// LEFT JOIN loan_table as lt on lt.id_loan = rl.id_loan AND lt.term_code = rl.term_code
// WHERE id_repayment = 1;

// SELECT @total_amount_paid:=getLoanTotalPayment(loan.id_loan) as total_amount_paid,ROUND((loan.loan_amount-@total_amount_paid),2) as loan_balance,(lt.total_due-getLoanTotalTermPayment(loan.id_loan,lt.term_code)) as amount_due
