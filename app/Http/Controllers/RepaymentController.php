<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

use DB;
use App\Loan;
use App\Member;
use App\MySession;
use App\CredentialModel;
use Dompdf\Dompdf;
use App\JVModel;
use App\CRVModel;
use PDF;
use App\WebHelper;

use App\Mail\RepaymentMail;
use App\Mail\LoanConfirmationMail;

use Carbon\Carbon;
// use Illuminate\Support\Facades\Request;

class RepaymentController extends Controller
{
    public function test_mail(Request $request){

        dd($request->getSchemeAndHttpHost());
        // info("TEST");

        dd(env('DEBUG_EMAIL'));
        // $id_repayment_transaction = 3544;
        // Mail::send(new RepaymentMail($id_repayment_transaction));

        // return;

        // $t = DB::select("SELECT id_repayment_transaction FROM repayment_transaction
        // WHERE transaction_type is not null
        // ORDER BY id_repayment_transaction DESC LIMIT 20;");
        // //

        // foreach($t as $d){
        //      Mail::send(new RepaymentMail($d->id_repayment_transaction)); 
        // }

        if (filter_var("caluzarheymar@gmail.com", FILTER_VALIDATE_EMAIL)){
          $emailErr = "Invalid email format";
          dd($emailErr);
        }

        return;

        $data['details'] = DB::table('repayment_transaction as rt')
                            ->select(DB::raw("rt.id_repayment_transaction,DATE_FORMAT(rt.transaction_date,'%M %d, %Y') as transaction,rt.swiping_amount,rt.total_payment,rt.change,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member_name,if(rt.transaction_type =1,'Cash','ATM Swipe') as paymode,rt.transaction_type"))
                           ->leftJoin('member as m','m.id_member','rt.id_member')
                           ->where('rt.id_repayment_transaction',$id_repayment_transaction)
                           ->first();

        return view('emails.repayment',$data);

        dd($data);
    }
    public function due_dates(Request $request){
        $dt = WebHelper::ConvertDatePeriod($request->transaction_date);

        $data['dues'] = DB::select(
            "SELECT * FROM (
            SELECT if(loan.maturity_date < '$dt','$dt',lt.due_date) as due_date,DATE_format(if(loan.maturity_date < '$dt','$dt',lt.due_date),'%M %d, %Y') as due_date_text
             FROM loan
             LEFT JOIN loan_table as lt on lt.id_loan = loan.id_loan
             WHERE loan.loan_status=1 AND lt.is_paid in (0,2) and loan.id_member=? AND (lt.due_date >= ? OR loan.maturity_date <= ? )) as t
             GROUP BY due_date ORDER BY due_date",[$request->id_member,$dt,$dt] 

        );
        // $data['dues'] = DB::table('loan')
        // ->select(DB::raw("if(loan.maturity_date < '$dt','$dt',lt.due_date) as due_date,DATE_format(if(loan.maturity_date < '$dt','$dt',lt.due_date),'%M %d, %Y') as due_date_text"))
        // ->leftJoin("loan_table as lt","lt.id_loan","=","loan.id_loan")
        // ->Where("loan.loan_status",1)
        // ->whereIn("lt.is_paid",[0,2])
        // ->where('loan.id_member',$request->id_member)

        // ->where(function($query) use($dt){
        //     $query->where('lt.due_date','>=',$dt);
        //     $query->orWhere('loan.maturity_date','<',$dt);
        // })
        
        // ->groupBy('due_date')
        // ->orDerby('due_date')
        // ->get();

        // $data['def_due'] = $this->decodeDueDate($data['dues'][0]->due_date??$request->transaction_date);
        // $data['def_due'] = WebHelper::ConvertDatePeriod($data['dues'][0]->due_date??$request->transaction_date);



        if(count($data['dues']) == 0){
            $active_loans = DB::table('loan')
                            ->where('id_member',$request->id_member)
                            ->where('loan_status',1)
                            ->count();
            if($active_loans > 0){
                $o['due_date'] = $dt;
                $o['due_date_text'] = date("F d, Y", strtotime($o['due_date']));

                $data['dues'][0] = $o;
            }    
        }else{
            $d = array();
            $match_date= false;
            // array_push($d,array('due_date'=>$dt,'due_date_text'=>date("F d, Y", strtotime($dt))));
            foreach($data['dues'] as $du){

                if($dt == $du->due_date){
                    $match_date = true;
                }
                array_push($d,(array)$du);
            }

            if(!$match_date){
                array_unshift($d, array('due_date'=>$dt,'due_date_text'=>date("F d, Y", strtotime($dt))));
            }
            $data['dues'] = $d;
        }

        if(count($data['dues']) > 0){
            $data['def_due'] = WebHelper::ConvertDatePeriod($data['dues'][0]['due_date']);
        }
        
        return $data;
    }
    public function index(Request $request){
        // return Loan::LoanDetails(24);
        // return Member::getCBU(20);
        // $r = new Request([
        //     'id_repayment_transaction'=>298
        // ]);
        // // $this->cancel_repayment($r);
        // // dd(123);

        // $d = $this->PopulatePaymentAuto('10012024105015952f5313',50000,0,'2024-11-30',false);

        // dd("");


        // return $this->GenerateRepaymentCashReceiptData(2510,1);
        $data['current_date'] = MySession::current_date();
        // $start = date('Y-m-d', strtotime('-1 year'));
        $start = date('Y-m-d', strtotime('-30 days'));
        $end = MySession::current_date();

        $data['start_date'] = $request->start_date ?? $start;
        $data['end_date'] = $request->end_date ?? $end;
        $data['selected_id_member'] = $request->id_member ?? 0;
        $data['head_title'] = "Repayments";

        $data['selected_member'] =DB::table('member as m')
        ->select(DB::raw("concat(membership_id,' || ',FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)) as member_name,id_member"))
        ->where('id_member',$request->id_member)
        ->first();

        $data['credential']= CredentialModel::GetCredential(MySession::myPrivilegeId());
        if(!$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }

        $data['repayment_list'] = DB::table('repayment_transaction as rt')
        ->select(DB::raw("repayment_token,id_repayment_transaction,DATE_FORMAT(rt.transaction_date,'%M %d, %Y') as repayment_date,DATE_FORMAT(rt.date,'%M %d, %Y') as loan_due_date,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member_name,
            FORMAT(swiping_amount,2) as swiping_amount,FORMAT(total_payment,2) as total_payment,FORMAT(`change`,2) as 'change',
            CASE WHEN transaction_type =1 THEN 'Cash'
            WHEN transaction_type=2 THEN 'ATM Swipe'
            WHEN transaction_type = 3 THEN 'Payroll'
            WHEN transaction_type = 4 THEN 'Check' END as paymode,rt.or_no,
            DATE_FORMAT(rt.date_created,'%M %d, %Y %r') as date_created,rt.status"))
        ->leftJoin('member as m','m.id_member','rt.id_member')
        ->where('repayment_type',1)
        ->where('rt.transaction_date','>=',$data['start_date'])
        ->where('rt.transaction_date','<=',$data['end_date'])
        ->whereRaw("rt.id_journal_voucher + rt.id_cash_receipt_voucher > 0")
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
        $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/repayment');
        if(!$data['credential']->is_create){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
        $data['opcode'] = 0;
        // $data['repayment_fee'] = DB::table('repayment_fee_type')->where('show','=',1)->get();
        $data['repayment_fee'] = DB::table('tbl_payment_type')->select('id_payment_type','description','default_amount as amount')->where('type','=',3)->get();
        $data['current_date'] = MySession::current_date();
        $data['penalties'] = DB::table('tbl_payment_type')->select('id_payment_type','description')->where('type','=',4)->get();
        $data['allow_post'] = true;
        $data['banks']= DB::table('tbl_bank')->get();

        $data['min_date'] = date("Y-m-d", strtotime("-300 days"));
        $data['head_title'] = "Create Loan Payment";

        


        return view('repayment.repayment_form',$data);
    }

    public function view($token){
        $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/repayment');
        if(!$data['credential']->is_edit && !$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }

        $data['opcode'] = 1;
        $data['allow_post'] = true;
        $data['min_date'] = date("Y-m-d", strtotime("-300 days"));
        $data['repayment_transaction'] = DB::table('repayment_transaction as rt')
        ->select("transaction_type","id_repayment_transaction","repayment_token",DB::raw("rt.input_mode,concat(membership_id,' || ',FormatName(first_name,middle_name,last_name,suffix)) as selected_member,member.id_member,rt.date,rt.swiping_amount,rt.remarks,rt.transaction_date,date_format(rt.date,'%M %d, %Y') as due_date_text,rt.or_no,rt.change,rt.status,rt.cancel_reason,rt.id_bank,rt.id_journal_voucher,rt.id_cash_receipt_voucher,ifnull(rt.id_repayment,0) as id_repayment"))
        ->leftJoin('member','member.id_member','rt.id_member')
        ->where('repayment_token',$token)
        ->first();



        $id_member = $data['repayment_transaction']->id_member;
        $date = $data['repayment_transaction']->date;
        $id_repayment_transaction = $data['repayment_transaction']->id_repayment_transaction;

        $data['head_title'] = "Loan Payment #$id_repayment_transaction";

        if($data['repayment_transaction']->status < 10 && $data['repayment_transaction']->id_repayment == 0){
            $loan_dues = $this->parseLoanDues($id_member,$date,$id_repayment_transaction,$data['repayment_transaction']->status,$data['repayment_transaction']->transaction_date);

        }else{
            $loan_dues = DB::select("SELECT loan.loan_token,loan.id_loan,concat(getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms)) as 'loan_name',
                    SUM(paid_principal) as paid_principal,SUM(paid_interest) as paid_interest,SUM(paid_fees) as paid_fees,SUM(ifnull(rls.amount,0)) as surcharges
                    FROM repayment_loans as rl
                    LEFT JOIN loan on loan.id_loan = rl.id_loan
                    left JOin loan_service as ls on ls.id_loan_service = loan.id_loan_service
                    LEFT JOIN repayment_loan_surcharges as rls on rls.id_loan = rl.id_loan AND rls.id_repayment_transaction = rl.id_repayment_transaction
                    WHERE rl.id_repayment_transaction = ?
                    GROUP BY rl.id_loan;",[$id_repayment_transaction]);
        }
        
        // return $loan_dues;

        $data['active_loans'] = $loan_dues;
        


        
        $data['banks']= DB::table('tbl_bank')->get();

        $data['repayment_penalty']  = DB::table('repayment_penalty as rp')
        ->select('rp.id_payment_type','rp.amount','pt.description')
        ->leftJoin('tbl_payment_type as pt','pt.id_payment_type','rp.id_payment_type')
        ->where('rp.id_repayment_transaction',$id_repayment_transaction)
        ->orDerby('rp.id_repayment_penalty')
        ->get();

        // return $data['repayment_penalty'];
        if($data['repayment_transaction']->status == 10 || $data['repayment_transaction']->id_repayment > 0){
            $view = 'repayment.repayment_view';
        }else{
            // $view = 'repayment.repayment_view';
            $view = 'repayment.repayment_form';
        }
        //CHECK IF THERE IS REPAYMENT MADE NEXT MONTH
        // $dt =
        // $date_check = $this->decodeDueDate($data['repayment_transaction']->date);
        $date_check = WebHelper::ConvertDatePeriod($data['repayment_transaction']->date);
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
     $data['member_info'] = DB::table('member as m')->select(DB::raw("FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as name,id_member,member_code"))->where('id_member',$id_member)->first();
     $data['repayment_date'] = $date;
     $data['is_valid_loan'] = (count($data['active_loans']) > 0)?true:false;
     // $data['due_date'] =  $this->decodeDueDate($date);
     $data['due_date'] =  WebHelper::ConvertDatePeriod($date);

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

    if(count($data['repayment_change']) > 0 || $data['repayment_transaction']->status >=10 || $count_payroll > 0 || $data['repayment_transaction']->id_repayment > 0){
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

    $loan_dues = $this->parseLoanDues($id_member,$date,0,0,$transaction_date);
    // $data['loan_dues'] = $loan_dues['dues_converted'];
    $data['active_loans'] = $loan_dues['active_loans'];
    $data['member_info'] = DB::table('member as m')->select(DB::raw("FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as name,id_member,member_code"))->where('id_member',$id_member)->first();
    $data['repayment_date'] = $date;
    $data['is_valid_loan'] = (count($data['active_loans']) > 0)?true:false;
    $data['due_date'] =  $this->decodeDueDate($date);
    // $data['due_date'] =  $this->decodeDueDate($date);

    return response($data);
        // }
}

public function set_validation_param($payments){
    $for_validation = array();
    foreach($payments as $loan_token=>$payment){
        if(($payment['principal'] + $payment['interest']+$payment['fees']) > 0){
            $for_validation[$loan_token] = $payment['principal'];
        }       
    }

    return $for_validation;

}
public function validate_payment($loan_payment,$date,$id_repayment_transaction){
    $invalid['loan_payment'] = array();
   
    $invalid['isValid'] =true;

    $param['loan_payment'] = $this->set_validation_param($loan_payment);
  

    $loan_tokens = array();

    foreach($param['loan_payment'] as $loan_token=>$val){
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
// 
    // return $validator;
    $g = new GroupArrayController();

    $validator = $g->array_group_by($validator,['loan_token']);


    foreach($param['loan_payment'] as $loan_token=>$val){
        $act_balance = $validator[$loan_token][0]->current+$validator[$loan_token][0]->previous;

        if($val > $act_balance){
            array_push($invalid['loan_payment'],$loan_token);
            $invalid['isValid'] = false;
        }
    }
   

    return $invalid;

    return $validator;

    return $search_token;
    return $loan_tokens;

}

public function PopulatePaymentAuto($loan_token,$amount_paid,$id_repayment_transaction,$date,$fromOffset=false){

    $rl_query_string = gettype($id_repayment_transaction)=="array"? (" not in (".implode(",",$id_repayment_transaction).")"): " <> $id_repayment_transaction";

    // dd($rl_query_string);


    $dues = DB::select("SELECT * FROM (
    SELECT loan.loan_token,loan.id_loan,lt.due_date,lt.term_code,
    lt.repayment_amount-ifnull(SUM(rl.paid_principal),0) as principal_bal,   
    lt.interest_amount-ifnull(SUM(rl.paid_interest),0) as interest_bal,
    lt.fees-ifnull(SUM(rl.paid_fees),0) as fees_bal,
    if(lt.due_date <= ?,1,0) as type,loan.date_released,loan.id_loan_payment_type
    FROM loan 
    left join loan_table as lt on lt.id_loan = loan.id_loan
    left JOIN repayment_loans as rl on rl.id_loan =lt.id_loan AND rl.term_code = lt.term_code and rl.status <> 10 AND rl.id_repayment_transaction $rl_query_string
    where loan_token =  ? and accrued = 0
    GROUP BY loan_token,lt.term_code) as act_due
    WHERE (principal_bal+interest_bal+fees_bal > 0)
    ORDER BY due_date ASC;",[$date,$loan_token]);




    $loan_dues = array();
    $output['loan_date'] = $dues[0]->date_released;
    $output['payment_type'] = $dues[0]->id_loan_payment_type;
    $output['id_loan'] = $dues[0]->id_loan;

    $g = new GroupArrayController();
    $dues = $g->array_group_by($dues,['type']);

    // dd($dues);
    // format dues
    foreach($dues as $type=>$due){
        foreach($due as $d){
            array_push($loan_dues,$d);
         }

        // if($type == 1){
        //     foreach($due as $d){
        //        array_push($loan_dues,$d);
        //     }
        // }else{
        //     $c = count($due)-1;
        //     for($i=$c;$i>=0;$i--){
        //         array_push($loan_dues,$due[$i]);
        //     }
        // }
    }
    // dd($loan_dues);

    // return $loan_dues;

    $amount_to_pay = $amount_paid;
    $output['payments'] = array();
    $amount_paid =0;

    $output['remaining_total'] = 0;

    $total_interest = 0;
    $total_fees = 0;
    $total_principal = 0;


    //INTEREST AND FEES

    foreach($loan_dues as $c){
        $temp = array();
        $temp['term_code'] = $c->term_code;
        $total_principal += $c->principal_bal;
        $temp['paid_principal']=0;
        if($c->type == 1){
            $temp['paid_interest'] = $this->calculate($amount_to_pay,$c->interest_bal);
            $amount_to_pay -=$temp['paid_interest'];
            $temp['paid_fees'] = $this->calculate($amount_to_pay,$c->fees_bal);
            $amount_to_pay -=$temp['paid_fees'];
            $total_interest += $c->interest_bal;
            $total_fees += $c->fees_bal;
        }else{
            $temp['paid_interest'] = 0;
            $temp['paid_fees'] = 0;
        }
        $temp['is_advance'] = 1;
        $temp['id_loan'] = $c->id_loan;

        if(($temp['paid_interest']+$temp['paid_fees']) > 0){
            $amount_paid += $temp['paid_interest']+$temp['paid_fees'];
            array_push($output['payments'],$temp);
        }
    }

    $g = new GroupArrayController();


    if($amount_to_pay > 0){
        // PRINCIPAL
        foreach($loan_dues as $c){
            $temp = array();
            $temp['term_code'] = $c->term_code;

            
            $temp['paid_fees'] = 0;
            $temp['paid_principal'] = $this->calculate($amount_to_pay,$c->principal_bal);
            $amount_to_pay -=$temp['paid_principal'];


            if((env('REPAYMENT_INTEREST_FULL_CONTRACT') || $fromOffset) && $c->type == 0){
                // $temp['paid_interest'] = $this->calculate($amount_to_pay,$c->interest_bal);
                // $amount_to_pay -=$temp['paid_interest'];
                // $amount_paid += $temp['paid_interest'];
                // $total_interest += $c->interest_bal;
                $temp['paid_interest'] = 0;
            }else{
                $temp['paid_interest'] = 0;
            }


            $temp['is_advance'] = 1;
            $temp['id_loan'] = $c->id_loan;



            if($temp['paid_principal']+$temp['paid_interest'] > 0){
                $amount_paid += $temp['paid_principal'];
                array_push($output['payments'],$temp);
            }
        } 
    }


    if($amount_to_pay > 0){
        //Interest
        foreach($loan_dues as $c){
            $temp = array();
            $temp['term_code'] = $c->term_code;

            
            $temp['paid_fees'] = 0;
            $temp['paid_principal'] = 0;
            $amount_to_pay -=$temp['paid_principal'];


            if((env('REPAYMENT_INTEREST_FULL_CONTRACT') || $fromOffset) && $c->type == 0){
                $temp['paid_interest'] = $this->calculate($amount_to_pay,$c->interest_bal);
                $amount_to_pay -=$temp['paid_interest'];
                $amount_paid += $temp['paid_interest'];
                $total_interest += $c->interest_bal;
           
            }else{
                $temp['paid_interest'] = 0;
            }


            $temp['is_advance'] = 1;
            $temp['id_loan'] = $c->id_loan;



            if($temp['paid_principal']+$temp['paid_interest'] > 0){
                $amount_paid += $temp['paid_principal'];
                array_push($output['payments'],$temp);
            }
        } 
    }

    if($amount_to_pay > 0){
        // Accrued
        $accrued = DB::select("SELECT * FROM (
        SELECT loan.loan_token,loan.id_loan,lt.due_date,lt.term_code,
        lt.repayment_amount-ifnull(SUM(rl.paid_principal),0) as principal_bal,   
        lt.interest_amount-ifnull(SUM(rl.paid_interest),0) as interest_bal,
        lt.fees-ifnull(SUM(rl.paid_fees),0) as fees_bal,
        if(lt.due_date <= ?,1,0) as type,loan.date_released,loan.id_loan_payment_type
        FROM loan 
        left join loan_table as lt on lt.id_loan = loan.id_loan
        left JOIN repayment_loans as rl on rl.id_loan =lt.id_loan AND rl.term_code = lt.term_code and rl.status <> 10 AND rl.id_repayment_transaction $rl_query_string
        where loan_token =  ? and accrued = 1
        GROUP BY loan_token,lt.term_code) as act_due
        WHERE (principal_bal+interest_bal+fees_bal > 0)
        ORDER BY due_date ASC;",[$date,$loan_token]);

        foreach($accrued as $c){
            $temp = array();
            $temp['term_code'] = $c->term_code;

            
            $temp['paid_fees'] = 0;
            $temp['paid_principal'] = 0;
            $amount_to_pay -=$temp['paid_principal'];


            $temp['paid_interest'] = $this->calculate($amount_to_pay,$c->interest_bal);
            $amount_to_pay -=$temp['paid_interest'];
            $amount_paid += $temp['paid_interest'];
            $total_interest += $c->interest_bal;


            $temp['is_advance'] = 1;
            $temp['id_loan'] = $c->id_loan;



            if($temp['paid_principal']+$temp['paid_interest'] > 0){
                $amount_paid += $temp['paid_principal'];
                array_push($output['payments'],$temp);
            }
        }
     
    }

    //sort the data based on term code
    usort($output['payments'], function($a, $b) { //Sort the array using a user defined function
        return preg_replace('/[^0-9.]/','',$a['term_code'])  <preg_replace('/[^0-9.]/','',$b['term_code']) ? -1 : 1; //Compare the term code (extracted the number)
    });   

    $temp_obj = $g->array_group_by($output['payments'],['term_code']);

    $merged_repayment = array();

    foreach($temp_obj as $term=>$payments){
        $temp = array();
        $temp['term_code'] = $term;
        $temp['is_advance'] = $payments[0]['is_advance'];
        $temp['id_loan'] = $payments[0]['id_loan'];
        $temp['paid_principal'] = 0;
        $temp['paid_interest'] =0 ;
        $temp['paid_fees'] = 0;
        foreach($payments as $p){
            $temp['paid_principal']+= $p['paid_principal'];
            $temp['paid_interest']+= $p['paid_interest'];
            $temp['paid_fees']+= $p['paid_fees'];
        }

        array_push($merged_repayment,$temp);
    }
    $output['payments'] = $merged_repayment;
    $output['remaining_total'] = $total_interest+$total_fees+$total_principal;

    $output['amount_paid'] = $amount_paid; 


    return $output;
}
public function PopulatePaymentManual($loan_token,$principal,$interest,$fees,$id_repayment_transaction,$date){
    $type = env('REPAYMENT_INTEREST_FULL_CONTRACT') ? 1 : 0;
    $dues = DB::select("SELECT * FROM (
                            SELECT loan.loan_token,loan.id_loan,lt.due_date,lt.term_code,
                            lt.repayment_amount-ifnull(SUM(rl.paid_principal),0) as principal_bal,   
                            lt.interest_amount-ifnull(SUM(rl.paid_interest),0) as interest_bal,
                            lt.fees-ifnull(SUM(rl.paid_fees),0) as fees_bal,
                            if(lt.due_date <= ?,1,$type) as type,loan.date_released,loan.id_loan_payment_type     
                            FROM loan 
                            left join loan_table as lt on lt.id_loan = loan.id_loan
                            left JOIN repayment_loans as rl on rl.id_loan =lt.id_loan AND rl.term_code = lt.term_code and rl.status <> 10 AND rl.id_repayment_transaction <> $id_repayment_transaction
                            where loan_token =  ?
                            GROUP BY loan_token,lt.term_code) as act_due
                            WHERE (principal_bal+interest_bal+fees_bal > 0)
                            ORDER BY due_date ASC;",[$date,$loan_token]);

    $loan_dues = array();
    $output['loan_date'] = $dues[0]->date_released;
    $output['payment_type'] = $dues[0]->id_loan_payment_type;
    $g = new GroupArrayController();
    $dues = $g->array_group_by($dues,['type']);

    // dd($dues);
    // format dues
    foreach($dues as $type=>$due){
        foreach($due as $d){
            array_push($loan_dues,$d);
         }
         
        // if($type == 1){
        //     foreach($due as $d){
        //        array_push($loan_dues,$d);
        //     }
        // }else{
        //     $c = count($due)-1;
        //     for($i=$c;$i>=0;$i--){
        //         array_push($loan_dues,$due[$i]);
        //     }
        // }
    }




    $paid_principal = $principal;
    $paid_interest = $interest;
    $paid_fees = $fees;
    $output['payments'] = array();
    $output['amount_paid'] = 0;

    $output['remaining_principal'] = 0;
    $output['remaining_interest'] = 0;
    $output['remaining_fees'] = 0;

    $amount_paid =0;

    foreach($loan_dues as $c){
        $temp = array();
        $temp['term_code'] = $c->term_code;


        if($c->type == 1){
            $temp['paid_interest'] = $this->calculate($paid_interest,$c->interest_bal);
            $paid_interest -=$temp['paid_interest'];

            $temp['paid_fees'] = $this->calculate($paid_fees,$c->fees_bal);
            $paid_fees -=$temp['paid_fees'];
        }else{
            $temp['paid_interest'] =0;
            $temp['paid_fees']=0;
        }



        $temp['paid_principal'] = $this->calculate($paid_principal,$c->principal_bal);
        $paid_principal -=$temp['paid_principal'];

        $temp['is_advance'] = 1;
        $temp['id_loan'] = $c->id_loan;

        if(($temp['paid_interest']+$temp['paid_fees']+$temp['paid_principal']) > 0){
            $amount_paid += $temp['paid_interest']+$temp['paid_fees']+$temp['paid_principal'];
            array_push($output['payments'],$temp);
        }
        $output['remaining_principal'] += $c->principal_bal;

        if($c->type == 1){
            $output['remaining_interest'] += $c->interest_bal;
            $output['remaining_fees'] += $c->fees_bal;            
        }

    }

    $output['amount_paid'] = $amount_paid;

    return $output;
}
public function PopulatePaymentMatured($loan_token,$amount_paid,$id_repayment_transaction,$date,$surChargeDiscount){

    $rl_query_string = gettype($id_repayment_transaction)=="array"? (" not in (".implode(",",$id_repayment_transaction).")"): " <> $id_repayment_transaction";

    // dd($rl_query_string);


    $dues = DB::select("SELECT * FROM (
    SELECT loan.loan_token,loan.id_loan,lt.due_date,lt.term_code,
    lt.repayment_amount-ifnull(SUM(rl.paid_principal),0) as principal_bal,   
    lt.interest_amount-ifnull(SUM(rl.paid_interest),0) as interest_bal,
    lt.fees-ifnull(SUM(rl.paid_fees),0) as fees_bal,
    lt.surcharge-ifnull(SUM(rl.paid_surcharge),0) as surcharge_bal,
    if(lt.due_date <= ?,1,0) as type,loan.date_released,loan.id_loan_payment_type
    FROM loan 
    left join loan_table as lt on lt.id_loan = loan.id_loan
    left JOIN repayment_loans as rl on rl.id_loan =lt.id_loan AND rl.term_code = lt.term_code and rl.status <> 10 AND rl.id_repayment_transaction $rl_query_string
    where loan_token =  ?
    GROUP BY loan_token,lt.term_code) as act_due
    WHERE (principal_bal+interest_bal+fees_bal+surcharge_bal)  > 0
    ORDER BY due_date ASC;",[$date,$loan_token]);

    $interestBalance = collect($dues)->sum('interest_bal');
    $surchargeBalance = collect($dues)->sum('surcharge_bal')-$surChargeDiscount;
    $principalBalance = collect($dues)->sum('principal_bal');


   


    $loan_dues = array();
    $output['loan_date'] = $dues[0]->date_released;
    $output['payment_type'] = $dues[0]->id_loan_payment_type;
    $output['id_loan'] = $dues[0]->id_loan;

    $g = new GroupArrayController();
    $dues = $g->array_group_by($dues,['type']);

    foreach($dues as $type=>$due){
        foreach($due as $d){
            array_push($loan_dues,$d);
        }
    }

    $amount_to_pay = $amount_paid;
    $output['payments'] = array();
    $amount_paid =0;

    $output['remaining_total'] = 0;

    $total_interest = 0;
    $total_fees = 0;
    $total_principal = 0;
    $total_surcharge= 0;


    // INTEREST
    $InterestForApplication = ($amount_to_pay >= $interestBalance)?$interestBalance:$amount_to_pay;
    foreach($loan_dues as $c){
        if($amount_to_pay <= 0) continue;

        if($InterestForApplication > 0){
            $temp = array();
            $temp['term_code'] = $c->term_code;
            $total_interest += $c->interest_bal;
         
            $temp['paid_principal']=0;
            $temp['paid_fees'] = 0;
            $temp['paid_surcharge'] = 0;
            $temp['paid_interest'] = $this->calculate($InterestForApplication,$c->interest_bal);


            $amount_to_pay -=$temp['paid_interest'];
            $InterestForApplication -= $temp['paid_interest'];

            $temp['is_advance'] = 0;
            $temp['id_loan'] = $c->id_loan;

            if($temp['paid_interest'] > 0){
                $amount_paid += $temp['paid_interest'];
                array_push($output['payments'],$temp);
            }    
        }
    }       

    // Surcharge
    $SurchargeForApplication = ($amount_to_pay >= $surchargeBalance)?$surchargeBalance:$amount_to_pay;
    foreach($loan_dues as $c){
        if($amount_to_pay <= 0) continue;

        if($SurchargeForApplication > 0){
            $temp = array();
            $temp['term_code'] = $c->term_code;
            $total_surcharge += $c->surcharge_bal;
         
            $temp['paid_principal']=0;
            $temp['paid_fees'] = 0;
            $temp['paid_surcharge'] = $this->calculate($SurchargeForApplication,$c->surcharge_bal);


            $temp['paid_interest'] = 0;
            $amount_to_pay -=$temp['paid_surcharge'];

            $SurchargeForApplication -= $temp['paid_surcharge'];


            $temp['is_advance'] = 0;
            $temp['id_loan'] = $c->id_loan;

            if($temp['paid_surcharge'] > 0){
                $amount_paid += $temp['paid_surcharge'];
                array_push($output['payments'],$temp);
            }
        }
    }

    // Principal
    $PrincipalForApplication = ($amount_to_pay >= $principalBalance)?$principalBalance:$amount_to_pay;

    foreach($loan_dues as $c){
        if($amount_to_pay <= 0) continue;

        if($PrincipalForApplication > 0){
            $temp = array();
            $temp['term_code'] = $c->term_code;
            $total_principal += $c->principal_bal;
         
            $temp['paid_principal']=$this->calculate($PrincipalForApplication,$c->principal_bal);
            $temp['paid_fees'] = 0;
            $temp['paid_surcharge'] = 0;
            $temp['paid_interest'] = 0;
            $amount_to_pay -=$temp['paid_principal'];
            $PrincipalForApplication -= $temp['paid_principal'];

            $temp['is_advance'] = 0;
            $temp['id_loan'] = $c->id_loan;

            if($temp['paid_principal'] > 0){
                $amount_paid += $temp['paid_principal'];
                array_push($output['payments'],$temp);
            }

        }
    }

    $g = new GroupArrayController();
    

    // dd([
    //     'principal'=>collect($output['payments'])->sum('paid_principal'),
    //     'interest'=>collect($output['payments'])->sum('paid_interest'),
    //     'surcharge'=>collect($output['payments'])->sum('paid_surcharge'),
    //             'grandtotal'=>collect($output['payments'])->sum('paid_principal')+collect($output['payments'])->sum('paid_interest')+collect($output['payments'])->sum('paid_surcharge'),
    // ]);

    //sort the data based on term code
    usort($output['payments'], function($a, $b) { //Sort the array using a user defined function
        return preg_replace('/[^0-9.]/','',$a['term_code'])  <preg_replace('/[^0-9.]/','',$b['term_code']) ? -1 : 1; //Compare the term code (extracted the number)
    });   

    $temp_obj = $g->array_group_by($output['payments'],['term_code']);

    $merged_repayment = array();

    foreach($temp_obj as $term=>$payments){
        $temp = array();
        $temp['term_code'] = $term;
        $temp['is_advance'] = $payments[0]['is_advance'];
        $temp['id_loan'] = $payments[0]['id_loan'];
        $temp['paid_principal'] = 0;
        $temp['paid_interest'] =0 ;
        $temp['paid_fees'] = 0;
        $temp['paid_surcharge'] = 0;
        foreach($payments as $p){
            $temp['paid_principal']+= $p['paid_principal'];
            $temp['paid_interest']+= $p['paid_interest'];
            $temp['paid_fees']+= $p['paid_fees'];
            $temp['paid_surcharge']+= $p['paid_surcharge'];
        }

        array_push($merged_repayment,$temp);
    }

    $output['payments'] = $merged_repayment;
    $output['remaining_total'] = $total_interest+$total_fees+$total_principal+$total_surcharge;

    $output['amount_paid'] = $amount_paid; 
    $output['discount'] = ($output['discount'] ?? 0) + ($surChargeDiscount ?? 0);


    return $output;
}

public function post(Request $request){

    // dd($request->all());
    $date = $request->repayment_date;
    $loan_payments = $request->loan_payments;

    $date_diff = date_diff(date_create(MySession::current_date()),date_create($request->transaction_date),false);

    $date_diff = $date_diff->format('%R%a');

    if($date_diff < -300){
        $response['RESPONSE_CODE'] = "ERROR";
        $response['message'] = "Invalid Date";
        return response($response);
    }


    // dd($request->all());

    $input_mode = $request->input_mode;


    // return $previous_payments;

    // $payments = $current_payments;
    $data['invalids'] = null;


    //FORMAT THE LOAN PAYMENTS
    if($input_mode == 2){ // AUTO
        foreach($loan_payments as $token=>$lp){
            $loan_payments[$token]['amt_paid'] = $lp['amt_paid'] ?? 0;
            $loan_payments[$token]['surcharges'] = $lp['surcharges'] ?? 0;
        }
    }else{
        foreach($loan_payments as $token=>$lp){
            $loan_payments[$token]['principal'] = $lp['principal'] ?? 0;
            $loan_payments[$token]['interest'] = $lp['interest'] ?? 0;
            $loan_payments[$token]['fees'] = $lp['fees'] ?? 0;
            $loan_payments[$token]['surcharges'] = $lp['surcharges'] ?? 0;
        }        
    }


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

        if($transaction_type == 1){

            if(!isset($request->or_no)){
                $data['RESPONSE_CODE'] = "ERROR";
                $data['message'] = "Please Enter OR No";

                return response($data);                
            }

            // Check OR NO
            $or_record = DB::table('cash_receipt')
            ->where('or_no',$request->or_no)
            ->where('status','<>',10)
            ->count();

            if($or_record > 0){
                $data['RESPONSE_CODE'] = "ERROR";
                $data['message'] = "OR # $request->or_no already exists";
                return response($data);
            }
        }
    }else{
        $rep =DB::table('repayment_transaction')->where('repayment_token',$repayment_token)->first();
        $id_repayment_transaction = $rep->id_repayment_transaction;
        $transaction_type = $rep->transaction_type;
          // SELECT * FROM payroll_ca as pc
          //           LEFT JOIN payroll as p on p.id_payroll = pc.id_payroll
          //           WHERE pc.id_journal_voucher = 20 AND p.status <> 10
    }

    /***************PAYMENT VALIDATION***********************/
    // $validation_payment = $this->validate_payment($current_payments,$date,$id_repayment_transaction);

    // if(!$validation_payment['isValid']){
    //     $data['RESPONSE_CODE'] = "ERROR";
    //     $data['message'] = "Invalid Amount";
    //     $data['invalids'] = $validation_payment;

    //     return response($data);
    // }
    /***********************************************************/
    
    // $id_repayment_transaction = DB::table('repayment_transaction')->where('repayment_token',$repayment_token)->first()->id_repayment_transaction ?? 0;

    $total_penalties = $this->parseTotalPenalties($penalties);
    $fees = $this->parseFees($fees);

    $fees_insert = $fees['FEES'];
    $total_fees = $fees['TOTAL'];


    $repayment_loans = array();
    $total_loan_due = 0;
    $loan_tokens = array();
    $id_loans = array();
    $rep_surcharges = array();
    $total_surcharges=0;

    $data['invalid_inputs'] = array();

    $fully_paid_loan = array();
    $fully_paid_dates = array();

    $fields_payment = array();
    foreach($loan_payments as $token=>$lp){
        if($input_mode == 2){ // AUTO
            $payments_temp = $this->PopulatePaymentAuto($token,$lp['amt_paid'],$id_repayment_transaction,$date);


            $fields_payment = ['amt_paid'];


            // return $payments_temp;
            //Validation
            if(ROUND($lp['amt_paid'],2) > ROUND($payments_temp['remaining_total'],2)){
                if(!isset($data['invalid_inputs']['amt_paid'])){
                    $data['invalid_inputs']['amt_paid'] = array();
                }
                array_push($data['invalid_inputs']['amt_paid'],$token);
            }

            if(ROUND($lp['amt_paid'],2) == ROUND($payments_temp['remaining_total'],2)){
                array_push($fully_paid_loan,$token);
                if($payments_temp['payment_type'] == 1){
                    $fully_paid_dates[$token] = $payments_temp['loan_date'];
                }
            }

            // return $payments_temp;

        }else{ // Manual
            $payments_temp = $this->PopulatePaymentManual($token,$lp['principal'],$lp['interest'],$lp['fees'],$id_repayment_transaction,$date);


            $fields_payment = ['principal','interest','txt_fees'];
            // return;
           

            // return $payments_temp;
            //Validation PRincipal
            if(ROUND($lp['principal'],2) > ROUND($payments_temp['remaining_principal'],2)){
                if(!isset($data['invalid_inputs']['principal'])){
                    $data['invalid_inputs']['principal'] = array();
                }
                array_push($data['invalid_inputs']['principal'],$token);
            }

            //Validation Interest
            if(ROUND($lp['interest'],2) > ROUND($payments_temp['remaining_interest'],2)){
                if(!isset($data['invalid_inputs']['interest'])){
                    $data['invalid_inputs']['interest'] = array();
                }
                array_push($data['invalid_inputs']['interest'],$token);
            }

            //Validation Fees
            if(ROUND($lp['fees'],2) > ROUND($payments_temp['remaining_fees'],2)){      
                if(!isset($data['invalid_inputs']['txt_fees'])){
                    $data['invalid_inputs']['txt_fees'] = array();
                }
                array_push($data['invalid_inputs']['txt_fees'],$token);
            }

            //FULLY PAID VALIDATION
            $total_pd = ROUND($lp['principal']+$lp['interest']+$lp['fees'],2);
            $total_remaining = ROUND($payments_temp['remaining_principal']+$payments_temp['remaining_interest']+$payments_temp['remaining_fees'],2);

            if($total_pd == $total_remaining){
                array_push($fully_paid_loan,$token);
                if($payments_temp['payment_type'] == 1){
                    $fully_paid_dates[$token] = $payments_temp['loan_date'];
                }
            }
        }

        $invalid_no_month = false;
        // foreach($fully_paid_dates as $token=>$loan_date){
        //     $loan_date = WebHelper::ConvertDatePeriod($loan_date);
        //     $transaction_date = WebHelper::ConvertDatePeriod($date);

        //     $startDate = Carbon::parse($loan_date);
        //     $endDate = Carbon::parse($transaction_date);
        //     $monthsDifference = $startDate->diffInMonths($endDate);    

        //     if ($monthsDifference <= env('MIN_MONTH_TO_FULLY_PAID')){
        //         $invalid_no_month = true;
        //         foreach($fields_payment as $f){
        //             if(!isset($data['invalid_inputs'][$f])){
        //                 $data['invalid_inputs'][$f] = array();
        //             }
        //             array_push($data['invalid_inputs'][$f],$token);
        //         }
        //     }     
        // }

        $rep_temp = $payments_temp['payments'];

        if(count($rep_temp)){
            foreach($rep_temp as $rp){
                array_push($repayment_loans,$rp);
            }            
            $total_loan_due += $payments_temp['amount_paid'];
            array_push($loan_tokens,$token);
            array_push($id_loans,$rep_temp[0]['id_loan']);
        }

        if($lp['surcharges']  > 0){
            $temp_sur = array();
            $id_loan_t = DB::table('loan')->where('loan_token',$token)->max('id_loan');
            $temp_sur['id_repayment_transaction'] = 0;
            $temp_sur['id_loan'] = $id_loan_t;
            $temp_sur['amount'] = $lp['surcharges'];
            $total_surcharges += $lp['surcharges'];

            array_push($rep_surcharges,$temp_sur);
        }
    }   

    // if(count($data['invalid_inputs'])){
    //     $data['RESPONSE_CODE'] = "ERROR";
    //     $data['message'] = ($invalid_no_month)?("You can't fully repay a loan released within ".env('MIN_MONTH_TO_FULLY_PAID')." Month(s)"):"Invalid Amount";
    //     return response($data);
    // } 

    $total_rebates = 0;
    $rebates = [];
    if(count($fully_paid_loan) > 0 && env('WITH_REBATES_ON_FULL')){

        $sub = DB::table('loan')
        ->select(DB::raw("loan.id_loan,loan.loan_protection_rate,getMonthLapsed(loan.id_loan,'$date') as lapsed,terms,principal_amount,
        (principal_amount*(loan_protection_rate/100))/terms as prot_month"))
           ->whereIn('loan_token',$fully_paid_loan);

        $rebates = DB::table(DB::raw("({$sub->toSql()}) as sub"))
                   ->select(DB::raw("0 as id_repayment_transaction,id_loan,ROUND(prot_month*(terms-lapsed),2) as amount"))
                   ->mergeBindings($sub)
                   ->whereRaw("prot_month*(terms-lapsed) > 0")
                   ->get();

        for($r=0;$r<count($rebates);$r++){
            $total_rebates +=  $rebates[$r]->amount;
        }
    }

    $rebates = json_decode(json_encode($rebates),true);





    $total_payment = $total_penalties + $total_fees + $total_loan_due+ $total_surcharges-$total_rebates;

    if($transaction_type == 2){
        $change = $swiping_amount - $total_payment;
    }else{
        $change = 0;
    }
    $dues =$repayment_loans;


    $edited = false;

    if($change < 0){

        return $total_payment;
        return "OOPS";
    }

    //APPLICABLE ON EDIT, UNDO LOAN IF FULLY PAID AND EDITED BY SETTING THE AMOUNT TO 0
    if($opcode == 1){
        $r_loans = json_decode(DB::table('repayment_loans')
                   ->select('id_loan')
                   ->where('id_repayment_transaction',$id_repayment_transaction)
                   ->whereNotIn('id_loan',$id_loans)
                   ->groupBy('id_loan')
                   ->get()->pluck('id_loan'),true);
        
        DB::table('loan')
        ->whereIn('id_loan',$r_loans)
        ->update(['status'=>3,'loan_status'=>1]);
    }




    if($opcode == 0){
        //INSERT REPAYMENT TRANSACTION
        $insert_data = [
            'repayment_token'=> DB::raw("concat(DATE_FORMAT(now(),'%m%d%Y%H%i%s'),concat(LEFT(MD5(NOW()), 5)))"),
            'transaction_type' => $transaction_type,
            'transaction_date' => $request->transaction_date,
            'date' => $date,
            'id_member' => $id_member,
            'total_loan_payment' => $total_loan_due,
            'total_rebates' => $total_rebates,
            'swiping_amount' => $swiping_amount,
            'total_fees' => $total_fees,
            'total_penalty' => $total_penalties,
            'total_payment' => $total_payment,
            'change' => $change,
            'id_bank' => $id_bank,
            'remarks' => $request->remarks,
            'input_mode'=>$input_mode
        ];
        if(!env('REPAYMENT_EMAIL_NOTIF')){
            $insert_data['email_sent'] = 2;
        }
        DB::table('repayment_transaction')
        ->insert($insert_data);

        $repayment_transaction = DB::table('repayment_transaction')->select('id_repayment_transaction','repayment_token')->where('id_member',$id_member)->orderBy('id_repayment_transaction','DESC')->first();

        $id_repayment_transaction = $repayment_transaction->id_repayment_transaction;
        $repayment_token = $repayment_transaction->repayment_token;

    }else{
        $update_data = [
            'transaction_date' => $request->transaction_date,
            'transaction_type' => $transaction_type,
            'total_rebates' => $total_rebates,
            'total_loan_payment' => $total_loan_due,
            'swiping_amount' => $swiping_amount,
            'total_fees' => $total_fees,
            'total_penalty' => $total_penalties,
            'total_payment' => $total_payment,
            'change' => $change,
            'id_bank' => $id_bank,
            'remarks' => $request->remarks,
            'input_mode'=>$input_mode,
            'email_sent'=>DB::raw("if(email_sent=0,0,1)")
        ];

        if(!env('REPAYMENT_EMAIL_NOTIF')){
            $update_data['email_sent'] = 2;
        }

        // return $update_data;
        // $changes = $this->validate_data_post($id_repayment_transaction,$update_data,$dues,$fees_insert,$penalties);


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
        DB::table('repayment_loan_surcharges')->where('id_repayment_transaction',$id_repayment_transaction)->delete();
        DB::table('repayment_rebates')->where('id_repayment_transaction',$id_repayment_transaction)->delete();
    }



    for($i=0;$i<count($dues);$i++){
        $dues[$i]['id_repayment_transaction'] = $id_repayment_transaction;
    }

    for($i=0;$i<count($rep_surcharges);$i++){
        $rep_surcharges[$i]['id_repayment_transaction'] = $id_repayment_transaction;
    }



    for($i=0;$i<count($penalties);$i++){
        $penalties[$i]['id_repayment_transaction'] = $id_repayment_transaction;
    }

    for($i=0;$i<count($fees_insert);$i++){
        $fees_insert[$i]['id_repayment_transaction'] = $id_repayment_transaction;
    }

    for($r=0;$r<count($rebates);$r++){
        $rebates[$r]['id_repayment_transaction'] = $id_repayment_transaction;
    }

    DB::table('repayment_loans')
    ->insert($dues);

    //UPDATE IS PAID ON REPAYMENT TABLE
    // DB::table('loan_table')
    // ->where('id_loan',$table['id_loan'])
    $temp_id_loan = $id_loans;

    $imp = implode(",",array_unique($temp_id_loan));

    // DB::select("UPDATE loan_Table
    //             LEFT JOIN repayment_loans as rl on rl.term_code = loan_table.term_code and rl.id_loan = loan_table.id_loan and rl.id_repayment_Transaction = $id_repayment_transaction
    //             SET is_paid =CASE
    //             WHEN ifnull(getLoanTotalTermPayment(loan_table.id_loan,loan_table.term_code),0) = 0 THEN 0
    //             WHEN (total_due-ifnull(getLoanTotalTermPayment(loan_table.id_loan,loan_table.term_code),0)) <=0 THEN 1
    //             WHEN (loan_table.repayment_amount-ifnull(getLoanTotalTermPaymentType(loan_table.id_loan,loan_table.term_code,1),0)=0 AND rl.is_advance=1) THEN 1
    //             ELSE 2 END
    //             where loan_table.id_loan in ($imp) and rl.id_repayment_transaction is not null;");

    //Making sure all partially previous will tag as no payment
    DB::table('loan_table')
    ->whereIn('id_loan',array_values(array_unique($temp_id_loan)))
    ->update(['is_paid'=>DB::raw("CASE
        WHEN ifnull(getLoanTotalTermPayment(id_loan,term_code),0) = 0 THEN 0
        WHEN (total_due-ifnull(getLoanTotalTermPayment(id_loan,term_code),0)) =0 THEN 1
        ELSE 2 END")]);


    DB::table('repayment_fees')
    ->insert($fees_insert);

    DB::table('repayment_loan_surcharges')
    ->insert($rep_surcharges);

    DB::table('repayment_penalty')
    ->insert($penalties);

    DB::table('repayment_rebates')
    ->insert($rebates);


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
                ->where('status',3)
                ->update([
                    'status'=>6,
                    'loan_status' => 2
                ]);
            }else{
                $dt = WebHelper::ConvertDatePeriod($date);
                // $dt_query = env('REPAYMENT_INTEREST_FULL_CONTRACT')?'loan.maturity_date':"'$dt'";
                $dt_query = env('REPAYMENT_INTEREST_FULL_CONTRACT')?"if('$dt' > loan.maturity_date,'$dt',loan.maturity_date)":"'$dt'";
      
                $balance_as_of = DB::table('loan')
                                 ->select(DB::raw("getLoanBalanceAsOf(id_loan,$dt_query) as bal"))
                                 ->where('loan_token',$loan_tokens[$i])
                                 ->first();
                                 
                if(isset($balance_as_of) && $balance_as_of->bal <= 0){
                    //close the loan if the principal and current interest and fees are paid
                    DB::table('loan')
                    ->where('loan_token',$loan_tokens[$i])
                    ->where('loan_status',1)
                    ->where('status',3)
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
        }

        if($transaction_type == 2 || $transaction_type ==3 ||  $transaction_type ==4){ // if transaction type is ATM Swipe
            //POST JV
            $jv = JVModel::RepaymentJV($id_repayment_transaction,$edited,false);
            if($jv['status'] == "SUCCESS"){
                DB::table('repayment_transaction')
                ->where('id_repayment_transaction',$id_repayment_transaction)
                ->update(['id_journal_voucher'=>$jv['id_journal_voucher']]);
            }     
            $data['entry_type'] = "JV #".$jv['id_journal_voucher'];       
        }elseif($transaction_type == 1){
            //POST CRV
            $crv = CRVModel::RepaymentCRV($id_repayment_transaction,$edited,false);
            if($crv['status'] == "SUCCESS"){
                DB::table('repayment_transaction')
                ->where('id_repayment_transaction',$id_repayment_transaction)
                ->update(['id_cash_receipt_voucher'=>$crv['id_cash_receipt_voucher']]);
            }
            $data['entry_type'] = "CRV #".$crv['id_cash_receipt_voucher'];     
        }

        if($opcode == 1){
            $or_number = DB::table('repayment_transaction')->select("or_no")->where('id_repayment_transaction',$id_repayment_transaction)->first();
            if(isset($or_number)){
                $or_no = $or_number->or_no;
                $this->GenerateRepaymentCashReceiptData($id_repayment_transaction,$or_no);
            }
        }else{
            if($transaction_type == 1){
                // Auto push OR Number
                $req = new Request([
                    'repayment_token'=>$repayment_token,
                    'or_no'=>$request->or_no,
                    'or_opcode'=>0
                ]);

                $this->post_or($req);
            }
        }
        
        $data['RESPONSE_CODE'] = "SUCCESS";
        $data['REPAYMENT_TOKEN'] = $repayment_token;
        $data['ID_REPAYMENT_TRANSACTION'] = $id_repayment_transaction;

        // DB::table('repayment_transaction as rt')
        // ->leftJoin('member as m','m.id_member','rt.id_member')
        // ->where('rt.repayment_token',$repayment_token)
        // ->update(['rt.id_branch_rt'=>DB::raw('m.id_branch')]);
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

    public function ActiveLoans($id_member,$transaction_date,$id_repayment_transaction){
        // $transaction
        // fnull(rebate_amount,ROUND((terms-month_lapsed)*rebates_fac,2))
        // return DB::select("SELECT *,if($id_repayment_transaction=0,ROUND((terms-month_lapsed)*rebates_fac,2),ifnull(rebate_amount,0)) as rebates FROM (
            $rebates = "if(rebate_amount=0,ROUND((terms-month_lapsed)*rebates_fac,2),rebate_amount)";
            if(!env('WITH_REBATES_ON_FULL')){
                $rebates = 0;
            }


            $surcharges = DB::table('repayment_loan_surcharges')
                          ->select('id_loan')
                          ->where('id_repayment_transaction',$id_repayment_transaction)

                          ->get()->pluck('id_loan')->toArray();
            $imp_sur = "";
            if(count($surcharges) > 0){
                $imp_sur = " OR loan.id_loan in (".implode(",",$surcharges).")";
            }   

            $onwards_i = (env('REPAYMENT_INTEREST_FULL_CONTRACT')) ? "SUM(CASE WHEN due_date > '$transaction_date' AND loan_status = 1 THEN interest_balance ELSE 0 END) ":"0";


            return DB::select("SELECT *,$rebates as rebates FROM (
                        SELECT ll.id_loan,loan_token,date_released,loan_name,getLoanOverallBalance(ll.id_loan,1)+SUM(paid_principal) as principal_balance, getLoanOverallBalance(ll.id_loan,2)+SUM(paid_interest) as grand_interest_balance,
                        SUM(CASE WHEN due_date < '$transaction_date' THEN principal_balance ELSE 0 END) as previous_p,
                        SUM(CASE WHEN due_date = '$transaction_date' THEN principal_balance ELSE 0 END) as current_p,
                        SUM(CASE WHEN due_date > '$transaction_date' THEN principal_balance ELSE 0 END) as onwards_p,

                        SUM(CASE WHEN due_date < '$transaction_date' AND loan_status = 1 THEN interest_balance ELSE 0 END) as previous_i,
                        SUM(CASE WHEN due_date = '$transaction_date' AND loan_status = 1 THEN interest_balance ELSE 0 END) as current_i,
                        $onwards_i as onwards_i, 
                        
                        SUM(CASE WHEN due_date < '$transaction_date' THEN fees_balance ELSE 0 END) as previous_f,
                        SUM(CASE WHEN due_date = '$transaction_date' THEN fees_balance ELSE 0 END) as current_f,
                        SUM(paid_principal) as paid_principal,SUM(paid_interest) as paid_interest,SUM(paid_fees) as paid_fees,act_interest,act_fees,surcharges,
                        if(loan_status =1,PERIOD_DIFF(EXTRACT(YEAR_MONTH FROM '$transaction_date'), EXTRACT(YEAR_MONTH FROM MAX(due_date))),0) as overdue,rebates_fac,
                        getMonthLapsed(ll.id_loan,'$transaction_date') as month_lapsed,terms,ifnull(rb.amount,0) as rebate_amount,principal_amt,interest_amt
                        FROM (
                        SELECT loan.loan_status,loan.id_loan,loan.terms,DATE_FORMAT(loan.date_released,'%m/%d/%Y') as date_released,concat(getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms)) as 'loan_name',loan.loan_token,lt.due_date,lt.term_code,
                        lt.repayment_amount-ifnull(SUM(rl.paid_principal),0) as principal_balance,
                        lt.interest_amount-ifnull(SUM(rl.paid_interest),0) as interest_balance,
                        lt.fees-ifnull(SUM(rl.paid_fees),0) as fees_balance,
                        lt.interest_amount as act_interest,
                        lt.fees as act_fees,
                        ifnull(rlp.paid_principal,0) as paid_principal,
                        ifnull(rlp.paid_interest,0) as paid_interest,
                        ifnull(rlp.paid_fees,0) as paid_fees,
                        ifnull(rls.amount,0) as surcharges,
                        (loan.principal_amount*(loan.loan_protection_rate/100))/loan.terms as rebates_fac,lt.repayment_amount as principal_amt,lt.interest_amount as interest_amt
                        FROM loan 
                        LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
                        LEFT JOIN loan_table as lt on lt.id_loan = loan.id_loan
                        LEFT JOIN repayment_loans as rl on rl.id_loan = lt.id_loan AND lt.term_code = rl.term_code and rl.status <> 10 and rl.id_repayment_transaction <> ?
                        LEFT JOIN repayment_loans as rlp on rlp.id_loan = lt.id_loan AND lt.term_code = rlp.term_code and rlp.status <> 10 and rlp.id_repayment_transaction =?
                        LEFT JOIN repayment_loan_surcharges as rls on rls.id_repayment_transaction = $id_repayment_transaction AND rls.id_loan = loan.id_loan
                        where loan.id_member = ? and ((loan.loan_status  =1 OR rlp.id_loan is not null $imp_sur) )
                        GROUP BY loan_token,lt.term_code) as ll
                        LEFT JOIN repayment_rebates as rb on rb.id_loan = ll.id_loan AND rb.id_repayment_transaction = $id_repayment_transaction
                        GROUP BY loan_token

                        ) as lt
                        ORDER BY id_loan
                        ;",[$id_repayment_transaction,$id_repayment_transaction,$id_member]);
// [$transaction_date,$transaction_date,$transaction_date,$transaction_date,$transaction_date,$transaction_date,$transaction_date,$id_repayment_transaction,$id_repayment_transaction,$id_member]

        return $data;
    }

    public function parseLoanDues($id_member,$date,$id_repayment_transaction,$status,$transaction_date){

        $date = $this->decodeDueDate($date);
        $dt = WebHelper::ConvertDatePeriod($transaction_date);
        $transaction_date = $dt;

        if($status < 10){
            $data['active_loans'] = $this->ActiveLoans($id_member,$date,$id_repayment_transaction);
        }
        
        return $data;

    }
    public function calculate($amount,$due){
        if($amount >= $due){
            $p_amount = $due;
        }elseif($amount < $due && $amount > 0){
            $p_amount =$amount;
        }else{
            $p_amount = 0;
        } 

        return $p_amount;
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
        // if($request->ajax()){
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
        // }
    }
    public function print_repayment_or($id_repayment_transaction){

        $data['cash_receipt'] = DB::table('cash_receipt as cr')
        ->select(DB::raw("FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member_name,m.tin,or_no,DATE_format(date_received,'%m/%d/%Y') as transaction_date,total_payment,cr.id_cash_receipt,payment_remarks,m.address,cr.id_paymode"))
        ->leftJoin('member as m','m.id_member','cr.id_member')
        ->where('cr.reference_no',$id_repayment_transaction)
        ->where('cr.status','<>',10)
        ->where('cr.type',3)
        ->first();

        // return $data;
        $details_count = DB::table('cash_receipt_details')->where('id_cash_receipt',$data['cash_receipt']->id_cash_receipt)->count();
        // return $data['cash_receipt']->id_cash_receipt;
        $id_cash_receipt = $data['cash_receipt']->id_cash_receipt;
        if($details_count <= 10){ // DISPLAY THE DETAILED REPAYMENT OR
            $sql = "SELECT crd.id_cash_receipt_details,pt.type,ifnull(crd.description,pt.description) as payment_description,crd.amount
            FROM cash_receipt_details as crd
            LEFT JOIN tbl_payment_type as pt on pt.id_payment_type = crd.id_payment_type
            WHERE crd.id_cash_receipt = $id_cash_receipt
            ORDER BY id_cash_receipt_details";
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
            ORDER BY id_cash_receipt_details";

                     // return 1321;
        }
        // dd($sql);
        $sql = "SELECT * FROM ($sql) as or_data WHERE amount > 0";
        $data['cash_receipt_details'] = DB::select($sql);
        dd($data);
        // return view('test2');
        return view('cash_receipt.mk-or',$data);
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


        $loan_surcharges = config("variables.loan_surcharges");
        $surcharges = DB::select("select ? as id_payment_type,SUM(amount) as amount,id_repayment_transaction as reference FROM repayment_loan_surcharges
        WHERE id_repayment_transaction = ?;",[$loan_surcharges,$id_repayment_transaction]);

        foreach($surcharges as $p){
            $temp = array();
            $temp['id_payment_type'] = $p->id_payment_type;
            $temp['amount'] = $p->amount;
            $temp['description'] = 'Loan Surcharges';
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
            $id_repayment_transaction = $request->id_repayment_transaction;
            $no_entry = $request->no_entry ?? false;


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


            if(!$no_entry){
                if($details->transaction_type == 1 || $details->transaction_type == 4){ // CASH OR CHECK
                    CRVModel::RepaymentCRV($id_repayment_transaction,false,true);
                }else{ // ATM SWIPE
                    JVModel::RepaymentJV($id_repayment_transaction,false,true);
                }                 
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
        $dt = WebHelper::ConvertDatePeriod($transaction_date);
        $summary = DB::select("CALL RepaymentSummary2(?,?,?)",[$transaction_date,$dt,$transaction_type]);
        // return $summary;

        $g = new GroupArrayController();
        $data['repayment_summary'] = $g->array_group_by($summary,['borrower']);
        $data['transaction_date'] = date("F d,Y", strtotime($transaction_date));


        switch($transaction_type){
            case 1:
                $data['trans'] = "Cash";
                break;
            case 2:
                $data['trans'] ="ATM Swipe";
                break;
            case 3:
                $data['trans'] = "Payroll";
                break;
            case 4:
                $data['trans'] = "Check";
                break;
        }
        // $data['trans'] = ($transaction_type == 1)?"Cash":"ATM Swipe";




        // 
        if($transaction_type == 1 || $transaction_type == 4){ // CASH
            $transaction_summary = DB::select("SELECT crvd.description,SUM(if(debit>0,debit,credit)) as amount,id_chart_account_category,if(ca.id_chart_account_category <=2,1,if(ca.id_chart_account_category=21,999,if(ca.id_chart_account_category=33,5,ca.id_chart_account_category))) as payment FROM repayment_transaction as rt
            LEFT JOIN cash_receipt_voucher_details as crvd on crvd.id_cash_receipt_voucher = rt.id_cash_receipt_voucher
            LEFT JOIN chart_account as ca on ca.id_chart_account = crvd.id_chart_account
            WHERE rt.transaction_date = ? and rt.transaction_type = ? and rt.status <> 10
            GROUP BY crvd.id_chart_account
            ORDER BY payment,crvd.id_chart_account;",[$transaction_date,$transaction_type]);
        }else{ // BANK
            $transaction_summary = DB::select("SELECT jvd.description,SUM(if(debit>0,debit,credit)) as amount,id_chart_account_category,if(ca.id_chart_account_category <=2,1,if(ca.id_chart_account_category=21,999,if(ca.id_chart_account_category=33,5,ca.id_chart_account_category))) as payment FROM repayment_transaction as rt
            LEFT JOIN journal_voucher_details as jvd on jvd.id_journal_voucher = rt.id_journal_voucher
            LEFT JOIN chart_account as ca on ca.id_chart_account = jvd.id_chart_account
            WHERE rt.transaction_date = ? and rt.transaction_type = ? and rt.status <> 10
            GROUP BY jvd.id_chart_account
            ORDER BY payment,jvd.id_chart_account;",[$transaction_date,$transaction_type]);
        }
        // return $transaction_summary;

        $data['transaction_summary'] = $g->array_group_by($transaction_summary,['description']);


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
        // $pdf->setOrientation('landscape');

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
