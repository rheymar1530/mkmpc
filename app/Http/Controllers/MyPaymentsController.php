<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\MySession;
use App\CredentialModel;
class MyPaymentsController extends Controller
{
    public function index(Request $request){
        // $data['credential']= CredentialModel::GetCredential(MySession::myPrivilegeId());
        // if(!$data['credential']->is_view){
        //     return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        // }

        $data['current_date'] = MySession::current_date();


        $start = date("Y-01-01",strtotime(MySession::current_date()));
        $end = date("Y-12-t",strtotime(MySession::current_date()));




        $data['start_date'] = $request->start_date ?? $start;
        $data['end_date'] = $request->end_date ?? $end;
        $data['head_title'] = "My Payments";


        $data['selected_id_member'] = MySession::myId();



        $data['repayment_list'] = DB::table('repayment_transaction as rt')
        ->select(DB::raw("repayment_token,id_repayment_transaction,DATE_FORMAT(rt.transaction_date,'%M %d, %Y') as repayment_date,DATE_FORMAT(rt.date,'%M %d, %Y') as loan_due_date,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member_name,
            FORMAT(swiping_amount,2) as swiping_amount,FORMAT(total_payment,2) as total_payment,FORMAT(`change`,2) as 'change',
            if(transaction_type =1,'Cash','ATM Swipe') as paymode,rt.or_no,
            DATE_FORMAT(rt.date_created,'%M %d, %Y') as date_created,rt.status"))
        ->leftJoin('member as m','m.id_member','rt.id_member')
        ->where('repayment_type',1)
        ->where('rt.transaction_date','>=',$data['start_date'])
        ->where('rt.transaction_date','<=',$data['end_date'])
        ->where(function($query) use($data){
            if($data['selected_id_member'] > 0){
                $query->where('rt.id_member',$data['selected_id_member']);
            }
        })
        ->where('rt.status','<>',10)
        ->orDerby('rt.id_repayment_transaction','DESC')
        ->get();


        return view('payments.index',$data);
        return $data;
    }
    public function view($id_repayment_transaction){
        // $id_repayment_transaction = 2992;
        $data['head_title'] = "Payment #$id_repayment_transaction";
        $data['repayment_transaction'] = DB::table("repayment_transaction as rt")
                       ->select(DB::raw("id_repayment_transaction,(total_fees+total_penalty) as other_fees_charges,total_rebates,swiping_amount,`change`,transaction_type,DATE_FORMAT(rt.transaction_date,'%m/%d/%Y') as date,if(rt.transaction_type=1,'Cash',if(rt.transaction_type=2,'ATM Swipe','Payroll')) as transaction_type,ifnull(rt.or_no,'-') as or_no"))
                       ->where('id_repayment_transaction',$id_repayment_transaction)
                       ->where(function($query){
                           if(!MySession::isAdmin()){
                                $query->where('rt.id_member',MySession::myId());
                           }
                       })
                       ->first();
        if(!isset($data['repayment_transaction'])){
             return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }



        $data['loan_payments'] = DB::select("SELECT  t.id_loan,concat('ID#',t.id_loan,' ',getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms)) as loan_service_name,
        SUM(paid_principal) as paid_principal,SUM(paid_interest) as paid_interest,SUM(paid_fees) as paid_fees,SUM(surcharges) as surcharges
        FROM (
        select rl.id_loan,SUM(paid_principal) as paid_principal,SUM(paid_interest) as paid_interest,SUM(paid_fees) as paid_fees,0 as surcharges
        FROM repayment_loans as rl
        WHERE id_repayment_transaction =?
        GROUP BY rl.id_loan
        UNION ALL
        SELECT id_loan,0,0,0,sum(amount) as surcharges FROM repayment_loan_surcharges as rls
        WHERE rls.id_repayment_transaction = ?
        GROUP BY id_loan
        ) as t
        LEFT JOIN loan on loan.id_loan = t.id_loan
        LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
        GROUP BY t.id_loan
        HAVING (SUM(paid_principal)+SUM(paid_interest)+SUM(paid_fees)+SUM(surcharges)) > 0;",[$id_repayment_transaction,$id_repayment_transaction]);



// transaction date, reference, transaction type, or no

        $data['other_fees_charges'] = DB::select("SELECT t.reference,tp.description,t.amount FROM (
                                        select concat('RF',id_repayment_fees) as reference,id_payment_type,amount FROM repayment_fees
                                        WHERE id_repayment_transaction = ?
                                        UNION ALL
                                        SELECT concat('RP',id_repayment_penalty) as reference,id_payment_type,amount FROM repayment_penalty
                                        WHERE id_repayment_transaction = ?) as t
                                        LEFT JOIN tbl_payment_type as tp on tp.id_payment_type = t.id_payment_type
                                        ORDER BY t.reference;",[$id_repayment_transaction,$id_repayment_transaction]);

        return view('payments.view',$data);
    
        return $data;
        // SELECT (total_fees+total_penalty) as other_fees_charges,total_rebates,swiping_amount,`change`,transaction_type FROM repayment_transaction 
        // where id_repayment_transaction = 2995;
    }
}

