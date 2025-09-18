<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Loan;
use App\MySession;
use App\CredentialModel;
use Dompdf\Dompdf;
use App\JVModel;
use App\CRVModel;
use PDF;
use App\WebHelper;
use Carbon\Carbon;
class RepaymentBulkController extends Controller
{
    /*Loan Payment Statement Status
        0 - Draft
        1 - Partially Paid
        2 - Fully Paid
        10 - Cancelled
    */

    public function TestORPrint(){
        $r = new RepaymentController();
        return $r->print_repayment_or(3248);
    }

    public function PrintRepaymentOR($id_repayment){
        $details = DB::table('repayment')
                   ->select('payment_for',DB::raw("DATE_FORMAT(date,'%m/%d/%Y') as date,change_payable"))
                   ->where('id_repayment',$id_repayment)
                   ->first();
        $ItemMax = 6;

        if($details->payment_for == 1){
            // Individual
            $d = DB::select("SELECT 
            concat(m.last_name,', ',m.first_name) as payee,concat(ls.name,' - ',loan.id_loan) as description ,SUM(paid_principal+paid_interest+paid_fees) as amount,bl.name as barangay_lgu,m.address,
            r.change_payable
            FROM repayment as r
            LEFT JOIN repayment_transaction as rt on rt.id_repayment = r.id_repayment AND rt.status <> 10
            LEFT JOIN member as m on m.id_member = rt.id_member
            LEFT JOIN repayment_loans as rl on rl.id_repayment_transaction = rt.id_repayment_transaction AND rl.status <> 10
            LEFT JOIN loan on loan.id_loan = rl.id_loan
            LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
            LEFT JOIN baranggay_lgu as bl on bl.id_baranggay_lgu = m.id_baranggay_lgu
            WHERE r.payment_for = 1 AND r.status <> 10 AND r.id_repayment = ?
            GROUP BY rt.id_member,rl.id_loan;",[$id_repayment]);

            $data['paymentDetails']  = [];
            $g = new GroupArrayController();

            $uniquePayee = array_unique(collect($d)->pluck('payee')->toArray());
            $uniqueBrgyLGU = array_unique(collect($d)->pluck('barangay_lgu')->toArray());

            $PayeeConditionCount = 2;
            if(count($uniquePayee) <= $PayeeConditionCount){
                // if to show payee names
                $data['paymentDetails']['payee'] = implode(", ",$uniquePayee);
                $data['paymentDetails']['address'] = (count($uniquePayee) == 1)?$d[0]->address:'';
                $data['paymentDetails']['tin'] = '';
                $data['paymentDetails']['total_amount'] = collect($d)->sum('amount');
                
                if(count($d) > $ItemMax){
                    //if loan payment is more than max items
                    if(count($uniquePayee) > $ItemMax){
                        // if selected Brgy/LGU is more than max items
                        $trans[] = [
                            'description'=>'Loan Payment',
                            'amount'=>collect($d)->sum('amount')
                        ];
                    }else{
                        $perGroup = $g->array_group_by($d,['payee']);
                        foreach($perGroup as $name=>$items){
                            $trans[]= [
                                'description' =>$name." Loan Payment",
                                'amount'=>collect($items)->sum('amount')
                            ];
                        }
                    }
                }else{
                    foreach($d as $transaction){
                        $trans[] = [
                            'description'=>$transaction->description,
                            'amount'=>$transaction->amount
                        ];
                    }
                }

            }else{
                // if to show BRGY/LGU
                $data['paymentDetails']['payee'] = implode(", ",$uniqueBrgyLGU);
                $data['paymentDetails']['address'] = '';
                $data['paymentDetails']['tin']  = '';
                $data['paymentDetails']['total_amount'] = collect($d)->sum('amount');

                if(count($d) > $ItemMax){
                    //if loan payment is more than max items
                    if(count($uniqueBrgyLGU) > $ItemMax){
                        // if selected Brgy/LGU is more than max items
                        $trans[] = [
                            'description'=>'Loan Payment',
                            'amount'=>collect($d)->sum('amount')
                        ];
                    }else{
                        $perGroup = $g->array_group_by($d,['barangay_lgu']);
                        foreach($perGroup as $name=>$items){
                            $trans[]= [
                                'description' =>$name." Loan Payment",
                                'amount'=>collect($items)->sum('amount')
                            ];
                        }
                    }
                }else{
                    foreach($d as $transaction){
                        $trans[] = [
                            'description'=>$transaction->description,
                            'amount'=>$transaction->amount
                        ];
                    }
                }
            }

            $data['Transactions'] = $trans; 
        }else{
            //Per statement
            $d = DB::select("SELECT 
            concat(if(type=1,'Brgy. ','LGU '), bl.name) as payee,DATE_FORMAT(statement_date,'%m-%Y') as statement_ref,SUM(total_payment) as amount
            FROM (
            SELECT SUM(total_payment) as total_payment,rs.id_baranggay_lgu,rs.date as statement_date,rs.id_repayment_statement
            FROM repayment as r
            LEFT JOIN repayment_transaction as rt on rt.id_repayment = r.id_repayment AND rt.status <> 10
            LEFT JOIN repayment_statement as rs on rs.id_repayment_statement = rt.id_repayment_statement
            WHERE r.id_repayment = ?
            GROUP BY rs.id_repayment_statement,rs.id_baranggay_lgu) as rep
            LEFT JOIN baranggay_lgu as bl on bl.id_baranggay_lgu = rep.id_baranggay_lgu
            GROUP BY rep.id_repayment_statement;",[$id_repayment]);

            $payee = array_values(array_unique(collect($d)->pluck('payee')->toArray()));

            $data['paymentDetails'] = [
                'payee' => implode(", ", $payee),
                'tin' => '',
                'total_amount'=> collect($d)->sum('amount'),
                'address'=>''
            ]; 

            $trans = array();
    
            foreach($d as $tr){
                $addDesc = count($payee) > 1 ? ($tr->payee." ") : '';
                $trans[]= [
                    'description' => $addDesc."Statement ".$tr->statement_ref,
                    'amount' => $tr->amount
                ];
            }

            $data['Transactions'] = $trans;
        }

        $data['Transactions'][] = [
            'description' => 'CHANGE',
            'amount' => $details->change_payable
        ];

        $data['paymentDetails']['date'] = $details->date;

        $addDetails = DB::table('repayment as r')
                      ->select(DB::raw("concat('OR# ',r.or_number) as or_number,concat('CHK# ',group_concat(rp.check_no)) as check_no"))
                      ->leftJoin('repayment_payment as rp','rp.id_repayment','r.id_repayment')
                      ->where('r.id_repayment',$id_repayment)
                      ->first();

        $data['paymentDetails']['or_number'] = $addDetails->or_number;
        $data['paymentDetails']['check_no'] = $addDetails->check_no;

        $data['paymentDetails']['total_amount'] += $details->change_payable;
 


        return view('cash_receipt.mk-or',$data);
     
        dd($details);
    }
    public function index(){
        $data['credential']= CredentialModel::GetCredential(MySession::myPrivilegeId());
        if(!$data['credential']->is_view){
            // return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }  
        // $data['repayments'] = DB::table('repayment as r')
        //                       ->select(DB::raw("r.id_repayment,DATE_FORMAT(r.date,'%m/%d/%Y') as date,r.total_amount,DATE_FORMAT(r.date_created,'%m/%d/%Y') as date_created,
        //                     CASE 
        //                     WHEN r.status = 0 THEN 'Posted'
        //                     WHEN r.status = 1 THEN 'Deposited'
        //                     WHEN r.status = 10 THEN 'Cancelled'

        //                     ELSE '' END as status_description,r.status,if(r.payment_for=1,'Individual','Statement') as payment_for"))
                             
        //                       // ->where('r.id_repayment_statement',0)
        //                       ->orderBy('r.id_repayment','DESC')

        //                       ->get();
        $data['repayments'] = DB::table('repayment as r')
                              ->select(DB::raw("r.id_repayment,RepaymentDescription(r.payment_for,r.id_repayment) as description,DATE_FORMAT(r.date,'%m/%d/%Y') as date,r.total_amount,DATE_FORMAT(r.date_created,'%m/%d/%Y') as date_created,
                            CASE 
                            WHEN r.status = 0 THEN 'Posted'
                            WHEN r.status = 1 THEN 'Deposited'
                            WHEN r.status = 10 THEN 'Cancelled'

                            ELSE '' END as status_description,r.status,if(r.payment_for=1,'Individual','Statement') as payment_for,r.or_number"))
                             
                              // ->where('r.id_repayment_statement',0)
                              ->orderBy('r.id_repayment','DESC')

                              ->get();


        return view('repayment-bulk.index',$data);

        dd($data);        
    }
    public function create(Request $request){
        $data['opcode'] = 0;
        $data['sidebar'] = "sidebar-collapse";

        $data['repayment_types'] = [
            1 =>'Individual',
            2 => 'Statement(s)'
        ];
        $brgy_lgu = DB::table('baranggay_lgu')
                    ->select(DB::raw("id_baranggay_lgu,if(type=1,'Barangay','LGU') as type,name as brgy_lgu"))
                    ->orderBy('type','ASC')
                    ->orderBy('name','ASC')
                    ->get();
        $g = new GroupArrayController();
        $data['brgy_lgu'] = $g->array_group_by($brgy_lgu,['type']);


        return view('repayment-bulk.form',$data);

        $data['branches']  = DB::table('baranggay_lgu')
        ->select(DB::raw("id_baranggay_lgu,name,if(type=1,'Barangay','LGU') as type"))
        ->orderByRaw("type,name")
        ->get();

        $data['selected_branch'] = $request->br ?? $data['branches'][0]->id_baranggay_lgu;

        
        $g = new GroupArrayController();
        $data['branches'] = $g->array_group_by($data['branches'],['type']);

        $data['date'] = $request->date ?? MySession::current_date();
        $data['date_due'] = WebHelper::ConvertDatePeriod($data['date']);

        $param = ['branch'=>$data['selected_branch'],'date1'=>$data['date_due'],'date2'=>$data['date_due'],'id_repayment'=>0,'id_repayment2'=>0];

        // $loans = $this->ActiveLoans($param);
        // // dd($loans);

   
        // $data['loans'] = $g->array_group_by($loans,['id_member']);

        $data['banks'] = DB::table('tbl_bank')
                         ->select(DB::raw("id_bank,bank_name"))
                         ->get();

        return view('bulk-repayment.form',$data);
    }

    public function edit($id_repayment,Request $request){


        $data['opcode'] = 1;
        $data['sidebar'] = "sidebar-collapse";

        $data['repayment_types'] = [
            1 =>'Individual',
            2 => 'Statement(s)'
        ];
        $brgy_lgu = DB::table('baranggay_lgu')
                    ->select(DB::raw("id_baranggay_lgu,if(type=1,'Barangay','LGU') as type,name as brgy_lgu"))
                    ->orderBy('type','ASC')
                    ->orderBy('name','ASC')
                    ->get();
        $g = new GroupArrayController();
        $data['brgy_lgu'] = $g->array_group_by($brgy_lgu,['type']);

        $data['details'] = DB::table('repayment')
                   ->select(DB::raw("id_repayment,payment_for,date,or_number,id_paymode"))
                   ->where('id_repayment',$id_repayment)
                   ->first();

        $data['repayment_types'] =[$data['details']->payment_for=>$data['repayment_types'][$data['details']->payment_for]];


        $currentDueDate = WebHelper::ConvertDatePeriod(MySession::current_date());

        if($data['details']->payment_for == 1){

            $dateCond = "if('$currentDueDate' > loan.maturity_date,'$currentDueDate',loan.maturity_date)";

            $data['Loans'] = DB::select("SELECT * FROM (
            SELECT 
            sd.id_loan,sd.id_member,

            getTotalDueTypeRepaymentX(loan.id_loan,$dateCond,?,1) as principal_balance,
            getTotalDueTypeRepaymentX(loan.id_loan,$dateCond,?,2) as interest_balance,
            getTotalDueTypeRepaymentX(loan.id_loan,$dateCond,?,4) as surcharge_balance,
            getTotalDueAsOfRepaymentEx(loan.id_loan,$dateCond,?) as balance,
            @cur_due:=getTotalDueAsOfRepaymentEx(loan.id_loan,sd.payment_dueDate,?) as current_due,payment,
            FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member,
            getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms) as loan_name,loan.loan_token,sd.ref_amount as ref_amount
            FROM (
            SELECT rl.id_loan,rt.id_member,
            SUM(paid_principal+paid_interest+paid_fees+paid_surcharge) as payment ,rt.date as payment_dueDate,lt.repayment_amount+lt.interest_amount as ref_amount
            FROM repayment_transaction as rt
            LEFT JOIN repayment_loans as rl on rl.id_repayment_transaction = rt.id_repayment_transaction
            LEFT JOIN loan_table as lt on lt.id_loan = rl.id_loan AND rl.term_code = lt.term_code
            WHERE rt.id_repayment = ?
            GROUP BY rl.id_loan
            HAVING SUM(paid_principal+paid_interest+paid_surcharge)) as sd
            LEFT JOIN member as m on m.id_member  = sd.id_member
            LEFT JOIN loan on loan.id_loan = sd.id_loan
            LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service) as d
            WHERE balance > 0
            ORDER BY member",[$id_repayment,$id_repayment,$id_repayment,$id_repayment,$id_repayment,$id_repayment,$id_repayment]);
     


    
     
        }else{
            $id_repayment_statements = DB::table('repayment_transaction')
                                       ->select('id_repayment_statement')
                                       ->where('id_repayment',$id_repayment)
                                       ->groupBy('id_repayment_statement')
                                       ->get()->pluck('id_repayment_statement')->toArray();
                                       
            $r = new Request([
                'id_repayment'=>$id_repayment,
                'id_repayment_statement'=>$id_repayment_statements
            ]);
            $statementData = $this->getStatement($r);    
     


            $data['StatementData'] = $statementData;    
        }

        $data['Payments'] = DB::table('repayment_payment')
                            ->where('id_repayment',$id_repayment)
                            ->get();

        // dd($data);
        return view('repayment-bulk.form',$data);
    }

    public function ParseMemberLoans(Request $request){
        $id_repayment = $request->id_repayment ?? 0;
        $id_member = $request->id_member;

        $dt = MySession::current_date();


        $currentDueDate = WebHelper::ConvertDatePeriod($dt);

        $param = [
            'id_member'=>$id_member,
            'id_repayment1'=>$id_repayment,
            'id_repayment2'=>$id_repayment,
            'id_repayment3'=>$id_repayment,
            'id_repayment4'=>$id_repayment,
            'id_repayment5'=>$id_repayment,
            'date1'=>$currentDueDate
        ];



        // $loans = DB::select("SELECT sd.*,
        //     FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member,
        //     concat(loan.id_loan,' - ',getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms)) as loan_name,loan.loan_token,
        //     ref_amt+surcharge as ref_amount
        //     FROM (
        //     SELECT loan.id_loan,loan.id_member,
        //     getTotalDueAsOfRepaymentEx(loan.id_loan,if('$currentDueDate' > loan.maturity_date,'$currentDueDate',loan.maturity_date),:id_repayment) as balance,

        //     @cur_due:=getTotalDueAsOfRepaymentEx(loan.id_loan,:date1,:id_repayment2) as current_due,ROUND(@cur_due,2) as payment,
        //     0 rebates,getLoanOverallBalance(loan.id_loan,4) as surcharge,interest_amount+repayment_amount as ref_amt
        //     FROM loan
        //     LEFT JOIN loan_table as lt on lt.id_loan = loan.id_loan
        //     WHERE loan.id_member = :id_member AND loan.loan_status =1
        //     GROUP BY loan.id_loan) as sd
        //     LEFT JOIN member as m on m.id_member  = sd.id_member
        //     LEFT JOIN loan on loan.id_loan = sd.id_loan
        //     LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
        //     WHERE balance > 0 
        //     ORDER BY member",$param);

        $dateCond = "if('$currentDueDate' > loan.maturity_date,'$currentDueDate',loan.maturity_date)";


        $loans = DB::select("SELECT sd.*,
            FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member,
            concat(loan.id_loan,' - ',getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms)) as loan_name,loan.loan_token,
            ref_amt+surcharge_balance as ref_amount
            FROM (
            SELECT loan.id_loan,loan.id_member,
            getTotalDueTypeRepaymentX(loan.id_loan,$dateCond,:id_repayment1,1) as principal_balance,
            getTotalDueTypeRepaymentX(loan.id_loan,$dateCond,:id_repayment2,2) as interest_balance,
            getTotalDueTypeRepaymentX(loan.id_loan,$dateCond,:id_repayment3,4) as surcharge_balance,
            getTotalDueAsOfRepaymentEx(loan.id_loan,$dateCond,:id_repayment4) as balance,
            @cur_due:=getTotalDueAsOfRepaymentEx(loan.id_loan,:date1,:id_repayment5) as current_due,ROUND(@cur_due,2) as payment,
            0 rebates,interest_amount+repayment_amount as ref_amt
            FROM loan
            LEFT JOIN loan_table as lt on lt.id_loan = loan.id_loan
            WHERE loan.id_member = :id_member AND loan.loan_status =1
            GROUP BY loan.id_loan) as sd
            LEFT JOIN member as m on m.id_member  = sd.id_member
            LEFT JOIN loan on loan.id_loan = sd.id_loan
            LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
            WHERE balance > 0 
            ORDER BY member",$param);

        if(count($loans) > 0){
            $data['loans'] = $loans;
            $data['RESPONSE_CODE'] = "SUCCESS";
            $data['id_member'] = $loans[0]->id_member;
            $data['member'] = $loans[0]->member;
        }else{
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "No Active Loan Found";

            return response($data);
        }
        

        return response($data);

       
    }

    public function ParseStatements(Request $request){
        $id_repayment = $request->id_repayment ?? 0;
        // $data['STATEMENTS'] = DB::select("SELECT rs.id_repayment_statement,SUM(rsd.loan_due) as due ,concat(if(bl.type=1,'Brgy. ','LGU -'),bl.name) as brgy_lgu
        //                                     FROM repayment_statement as rs
        //                                   LEFT JOIN baranggay_lgu as bl on bl.id_baranggay_lgu = rs.id_baranggay_lgu
        //                                   LEFT JOIN repayment_statement_details as rsd on rsd.id_repayment_statement = rs.id_repayment_statement
        //                                   WHERE rs.status = 0
        //                                   GROUP BY rs.id_repayment_statement
        //                                   ORDER BY rs.id_repayment_statement");

        $param = [
            'id_repayment'=>$id_repayment,
            'id_baranggay_lgu'=>$request->id_brgy_lgu ?? 0
        ];
        $data['STATEMENTS'] = DB::select("SELECT DISTINCT * FROM (
                SELECT rs.id_repayment_statement,SUM(rsd.loan_due)-getSatementPayment(rs.id_repayment_statement,$id_repayment) as due ,concat(if(bl.type=1,'Brgy. ','LGU -'),bl.name) as brgy_lgu,DATE_FORMAT(rs.date,'%m-%Y') as statement_ref
                FROM repayment_statement as rs
                LEFT JOIN baranggay_lgu as bl on bl.id_baranggay_lgu = rs.id_baranggay_lgu
                LEFT JOIN repayment_statement_details as rsd on rsd.id_repayment_statement = rs.id_repayment_statement
                WHERE rs.status = 0 AND rs.id_baranggay_lgu = :id_baranggay_lgu
                GROUP BY rs.id_repayment_statement
                UNION ALL
                SELECT rs.id_repayment_statement,SUM(rsd.loan_due)-getSatementPayment(rs.id_repayment_statement,$id_repayment) as due ,concat(if(bl.type=1,'Brgy. ','LGU -'),bl.name) as brgy_lgu,DATE_FORMAT(rs.date,'%m-%Y') as statement_ref FROM (
                SELECT id_repayment_statement 
                FROM repayment_transaction as rt
                WHERE rt.id_repayment = :id_repayment
                GROUP BY rt.id_repayment_statement) as k
                LEFT JOIN repayment_statement as rs on rs.id_repayment_statement = k.id_repayment_statement
                LEFT JOIN baranggay_lgu as bl on bl.id_baranggay_lgu = rs.id_baranggay_lgu
                LEFT JOIN repayment_statement_details as rsd on rsd.id_repayment_statement = rs.id_repayment_statement
                GROUP BY rs.id_repayment_statement) as k
                ORDER BY id_repayment_statement;",$param);

        if(count($data['STATEMENTS']) > 0){
            $data['RESPONSE_CODE'] = "SUCCESS";
            return response($data);
        }else{
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "No Loan Statement Available";

            return response($data);
        }
        return response($data);
    }

    public function getStatement(Request $request){
        $id_repayment_statement = $request->id_repayment_statement ?? [];
        $id_repayment = $request->id_repayment;

        if(count($id_repayment_statement) == 0){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Please select at least one statement";
            return response($data);
        } 

        $statements = $this->StatementLoans($id_repayment,$id_repayment_statement);


        // DB::table('repayment_statement_details as rsd')
        //                         ->select(DB::raw("rsd.id_repayment_statement,rsd.id_repayment_statement_details,rsd.id_loan,loan.loan_token,rsd.id_member,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member,getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms) as loan_name,rsd.loan_due as current_due,getTotalDueAsOfRepaymentEx(loan.id_loan,loan.maturity_date,$id_repayment) as balance,rt.id_cash_receipt_voucher"))
        //                         ->leftJoin('member as m','m.id_member','rsd.id_member')
        //                         ->leftJoin('loan','loan.id_loan','rsd.id_loan')
        //                         ->leftJoin('loan_service as ls','ls.id_loan_service','loan.id_loan_service')
        //                         ->leftJoin('repayment_transaction as rt','rt.id_repayment_transaction','rsd.id_repayment_transaction')
        //                         ->whereIn('rsd.id_repayment_statement',$id_repayment_statement)
        //                         ->orderBy('rsd.id_repayment_statement')
        //                         ->orderBy('member','ASC')
        //                         ->get();

        $data['statement_details'] = DB::table('repayment_statement as rs')
                                     ->select(DB::raw("rs.id_repayment_statement,SUM(rsd.loan_due) as due ,concat(if(bl.type=1,'Brgy. ','LGU -'),bl.name) as brgy_lgu,rs.id_baranggay_lgu,DATE_FORMAT(rs.date,'%m-%Y') as statement_ref"))
                                     ->leftJoin('baranggay_lgu as bl','bl.id_baranggay_lgu','rs.id_baranggay_lgu')
                                     ->leftJoin('repayment_statement_details as rsd','rsd.id_repayment_statement','rs.id_repayment_statement')
                                     ->whereIn('rs.id_repayment_statement',$id_repayment_statement)
                                     ->groupBy('rs.id_repayment_statement')
                                     ->get();
                                     
        $data['selected_brgy_lgu'] = $data['statement_details'][0]->id_baranggay_lgu ?? 0;
        $g = new GroupArrayController();
        $data['STATEMENTS'] = $g->array_group_by($statements,['id_repayment_statement','id_member']);

        // dd($data);
        $data['statement_details'] = $g->array_group_by($data['statement_details'],['id_repayment_statement']);

        $data['RESPONSE_CODE'] = "SUCCESS";
       


        return $data;

        // dd($data);

        return response($data);
    }


    public function StatementLoans($id_repayment,$id_repayment_statements){
        $currentDueDate = WebHelper::ConvertDatePeriod(MySession::current_date());
        // $subquery = DB::table('repayment_statement_details as rsd')
        //                         ->select(DB::raw("rsd.id_repayment_statement,rsd.id_repayment_statement_details,rsd.id_loan,loan.loan_token,rsd.id_member,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member,getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms) as loan_name,if($id_repayment =0,rsd.loan_due,getRepaymentStatementPaid($id_repayment,rsd.id_loan,rsd.id_repayment_statement)) as current_due,getTotalDueAsOfRepaymentEx(loan.id_loan,loan.maturity_date,$id_repayment) as balance,rt.id_cash_receipt_voucher,rsd.loan_due-getSatementLoanPayment(rsd.id_repayment_statement,$id_repayment,loan.id_loan) as loan_due_,rsd.loan_due as ref_amount"))
        //                         ->leftJoin('member as m','m.id_member','rsd.id_member')
        //                         ->leftJoin('loan','loan.id_loan','rsd.id_loan')
        //                         ->leftJoin('loan_service as ls','ls.id_loan_service','loan.id_loan_service')
        //                         ->leftJoin('repayment_transaction as rt','rt.id_repayment_transaction','rsd.id_repayment_transaction')
        //                         ->whereIn('rsd.id_repayment_statement',$id_repayment_statements);
        $subquery = DB::table('repayment_statement_details as rsd')
        ->select(DB::raw("rsd.id_repayment_statement,rsd.id_repayment_statement_details,rsd.id_loan,loan.loan_token,rsd.id_member,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member,getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms) as loan_name,if($id_repayment =0,rsd.loan_due,getRepaymentStatementPaid($id_repayment,rsd.id_loan,rsd.id_repayment_statement)) as current_due,getTotalDueAsOfRepaymentEx(loan.id_loan,if('$currentDueDate' > loan.maturity_date,'$currentDueDate',loan.maturity_date),$id_repayment) as balance,rt.id_cash_receipt_voucher,rsd.loan_due-getSatementLoanPayment(rsd.id_repayment_statement,$id_repayment,loan.id_loan) as loan_due_,rsd.loan_due as ref_amount,
            getTotalDueTypeRepaymentX(loan.id_loan,'$currentDueDate',$id_repayment,1) as principal,
            getTotalDueTypeRepaymentX(loan.id_loan,'$currentDueDate',$id_repayment,2) as interest,
            getTotalDueTypeRepaymentX(loan.id_loan,'$currentDueDate',$id_repayment,4) as surcharge"))
        ->leftJoin('member as m','m.id_member','rsd.id_member')
        ->leftJoin('loan','loan.id_loan','rsd.id_loan')
        ->leftJoin('loan_service as ls','ls.id_loan_service','loan.id_loan_service')
        ->leftJoin('repayment_transaction as rt','rt.id_repayment_transaction','rsd.id_repayment_transaction')
        ->whereIn('rsd.id_repayment_statement',$id_repayment_statements);     
        $statement = DB::table(DB::raw("({$subquery->toSql()}) as statement"))
                     ->mergeBindings($subquery)
                     ->where('statement.balance','>',0)
                     ->get();

        // dd($statement);


            // getTotalDueTypeRepaymentX(loan.id_loan,$dateCond,:id_repayment1,1) as principal_balance,
            // getTotalDueTypeRepaymentX(loan.id_loan,$dateCond,:id_repayment2,2) as interest_balance,
            // getTotalDueTypeRepaymentX(loan.id_loan,$dateCond,:id_repayment3,4) as surcharge_balance,
            // getTotalDueAsOfRepaymentEx(loan.id_loan,$dateCond,:id_repayment4) as balance,





        if($id_repayment > 0){
            $statementRepayment = DB::table('repayment_transaction')
                                  ->select('id_repayment_statement')
                                  ->where('id_repayment',$id_repayment)
                                  ->groupby('id_repayment_statement')->get()->pluck('id_repayment_statement')->toArray();



            for($i=0;$i<count($statement);$i++){

                if(!in_array($statement[$i]->id_repayment_statement,$statementRepayment)){
                    $statement[$i]->current_due = $statement[$i]->loan_due_;
                }
                
            }
                 
        }else{
            for($i=0;$i<count($statement);$i++){
                $statement[$i]->current_due = ($statement[$i]->loan_due_ >= 0)? $statement[$i]->loan_due_:0;
            }
        }




        return $statement;

    }

    public function ActiveLoans($param){
        $currentDueDate = WebHelper::ConvertDatePeriod(MySession::current_date());
        $loans = DB::select("SELECT sd.*,
                            FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member,
                            getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms) as loan_name,loan.loan_token,ref_amt+surcharge as ref_amount FROM (
                            SELECT loan.id_loan,loan.id_member,getTotalDueAsOfRepaymentEx(loan.id_loan,if('$currentDueDate' > loan.maturity_date,'$currentDueDate',loan.maturity_date),:id_repayment) as balance,
                            @cur_due:=getTotalDueAsOfRepaymentEx(loan.id_loan,:date1,:id_repayment2) as current_due,ROUND(@cur_due,2) as payment,
                            getLoanRebates(loan.id_loan,:date2) as rebates,getLoanOverallBalance(loan.id_loan,4) as surcharge,interest_amount+repayment_amount as ref_amt
                            FROM loan
                            WHERE loan.id_baranggay_lgu = :branch) as sd
                            LEFT JOIN member as m on m.id_member  = sd.id_member
                            LEFT JOIN loan on loan.id_loan = sd.id_loan
                            LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
                            WHERE balance > 0
                            ORDER BY member",$param);
        
        return $loans;
    }

    public function MemberLoanValidation($id_repayment,$id_loans,$date){
        $subquery = DB::table('loan')
                    ->select(DB::raw("id_loan,loan_token,id_member,getTotalDueAsOfRepaymentEx(loan.id_loan,if('$date' > loan.maturity_date,'$date',loan.maturity_date),$id_repayment) as balance,getTotalDueTypeRepaymentX(loan.id_loan,'$date',$id_repayment,4) as surchargeBalance"))
                    ->whereIn('id_loan',$id_loans)
                    ->get();

        return $subquery;
        dd($subquery);
    }

    public function post(Request $request){
        $opcode = $request->opcode ?? 0;
        $mode = $request->Mode ?? 1;
        $payments  = $request->Payments ?? [];
        $id_repayment = $request->id_repayment ?? 0;

        $PaymentDetails =$request->PaymentDetails ?? [];
        $RepaymentDetails= $request->RepaymentDetails;



        $g = new GroupArrayController();
        $RepaymentController = new RepaymentController();


        $date = $RepaymentDetails['date'] ?? MySession::current_date();

        $currentDueDate = WebHelper::ConvertDatePeriod($date);

        $LoanData = array();
        $LoanBalanceHolder = array();
        $TokenHolder = array();
        $LoanStatementHolder = array();


        $LoanSurchargeDiscount = array();
        $SurchargeHolder = array();

      
        if(count($payments) == 0){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Select loan payment";
            return response($data);
        }


        if(count($PaymentDetails) == 0){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Please add payment";

            return response($data);
        }

        $CompiledPaymentType = $this->compilePayments($RepaymentDetails,$PaymentDetails);

    
        //validate inputs
        $RepaymentDetailsValidator = array(
            "date" => ['required'=>true,'number'=>false],
            "or_number" =>['required'=>true,'number'=>false]
        );
        $invalidRepaymentInput = array();
        foreach($RepaymentDetailsValidator as $field=>$prop){

            if($RepaymentDetails[$field] == null){
                array_push($invalidRepaymentInput,$field);
            }
        }

        if(count($invalidRepaymentInput) > 0){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Invalid Payment Value";
            $data['errorField'] = $invalidRepaymentInput;

            return response($data);
        }

        //validate payment inputs
        if($RepaymentDetails['paymode'] == 4){
            //check
            $PaymentInputs = array(
                "check_bank" => ['required'=>true,'number'=>false],
                "check_date" =>['required'=>true,'number'=>false],
                "check_no" =>['required'=>true,'number'=>false],
                "amount" =>['required'=>true,'number'=>true]
            );
        }else{
            //cash
            $PaymentInputs = array(
                "amount" =>['required'=>true,'number'=>true]
            );
        }
        $InvalidPayment = array();
        foreach($PaymentDetails as $i=>$p){
            $invalidTemp = [];
            foreach($PaymentInputs as $key=>$prop){
                $invalidVal = ($prop['number'])?0:null;
                if($p[$key] == $invalidVal){
                    array_push($invalidTemp,$key);
                }
            }
            if(count($invalidTemp) > 0){
                $InvalidPayment[$i] = $invalidTemp;
  
            }
        }

       if(count($InvalidPayment) > 0){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Invalid Payment Value";
            $data['errorPayment'] = $InvalidPayment;   

            return response($data);     
       }





        // dd($CompiledPaymentType);

        $CompiledPaymentMethod = $CompiledPaymentType['RepaymentPayment'];

        // dd($request->all());


        $statements_id = [0];
        if($mode == 2){
            // per statement
            $statements_id = array();
            foreach($payments as $p){
                array_push($statements_id,$p['id_repayment_statement']);
            }
            $statements_id = array_values(array_unique($statements_id));
            $statementLoan = $this->StatementLoans($id_repayment,$statements_id);




    
            foreach($statementLoan as $sl){
                $LoanData[$sl->id_repayment_statement][$sl->id_loan]=[
                    'id_repayment_statement'=>$sl->id_repayment_statement,
                    'id_loan' => $sl->id_loan,
                    'balance' => $sl->balance,
                    'loan_token' => $sl->loan_token,
                    'id_member'=>$sl->id_member
                ];
                $TokenHolder[$sl->id_loan] = $sl->loan_token;
                if(!isset($LoanBalanceHolder[$sl->id_loan])){
                    $LoanBalanceHolder[$sl->id_loan] =0;
                    $LoanStatementHolder[$sl->id_loan] = array();
                }
                $LoanBalanceHolder[$sl->id_loan]= floatval($sl->balance);
                $SurchargeHolder[$sl->id_loan] = floatval($sl->surcharge);
                
                array_push($LoanStatementHolder[$sl->id_loan],$sl->id_repayment_statement);
            }
        }else{
            $id_loans = array();
            foreach($payments as $p){
                array_push($id_loans,$p['id_loan']);
            }

            $MemberLoans = $this->MemberLoanValidation($id_repayment,$id_loans,$currentDueDate);
            foreach($MemberLoans as $m){
                $LoanData[0][$m->id_loan]=[
                    'id_repayment_statement'=>0,
                    'id_loan' => $m->id_loan,
                    'balance' => $m->balance,
                    'loan_token' => $m->loan_token,
                    'id_member'=>$m->id_member
                ];

                $TokenHolder[$m->id_loan] = $m->loan_token;
                if(!isset($LoanBalanceHolder[$m->id_loan])){
                    $LoanBalanceHolder[$m->id_loan] =0;
                    // $LoanStatementHolder[$m->id_loan] = array();
                }
                $LoanBalanceHolder[$m->id_loan]= floatval($m->balance);
                $SurchargeHolder[$m->id_loan] = floatval($m->surchargeBalance);

                // array_push($LoanStatementHolder[$m->id_loan],$m->id_repayment_statement);  
            }
        }

        //Compile Payment (PER STATEMENT) && COMPILE DATA FOR LOAN VALIDATION
        $LoanPayment = array();
        $LoanPaymentMerged = array() ; //holds the total compiled payment of loan (grouped per loan regardless of statement)

        foreach($payments as $p){
            if(!isset($LoanPayment[$p['id_repayment_statement']])){
                $LoanPayment[$p['id_repayment_statement']] = array();
            }
            $LoanPayment[$p['id_repayment_statement']][$p['id_loan']]=floatval($p['amount_paid']);

            if(!isset($LoanPaymentMerged[$p['id_loan']])){
                $LoanPaymentMerged[$p['id_loan']] = 0;
            }

            $LoanPaymentMerged[$p['id_loan']] += floatval($p['amount_paid']);
        }

        //Payment Validation
        $invalidLoans = array();
        foreach($LoanPaymentMerged as $idLoan=>$amountPaid){
            $balance = $LoanBalanceHolder[$idLoan] ?? 0;
            $surcharge = $SurchargeHolder[$idLoan] ?? 0;

            $balanceWithoutSurcharge = $balance-$surcharge;

            $discount = ROUND($surcharge*0.02,2);

            $discountedBalance = $balance - $discount;

            if(ROUND($amountPaid,2) == ROUND($discountedBalance,2)){
                $LoanSurchargeDiscount[$idLoan] = $discount;
            }
    
            if($amountPaid > $balance){
                array_push($invalidLoans,$idLoan);
            }
        }

        if(count($invalidLoans) > 0){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Invalid Payment Amount";
            $data['invalidLoans'] = $invalidLoans;
            return response($data);
        }

        $CompiledPayment = array();

        foreach($LoanPaymentMerged as $idLoan=>$amount){
            $statements = $LoanStatementHolder[$idLoan] ?? [0];
            $id_member = $LoanData[$statements[0]][$idLoan]['id_member'];

            $idRepaymentTransactions = DB::table('repayment_transaction')
                                       ->select(DB::raw('id_repayment_transaction,id_member,ifnull(id_repayment_statement,0) as id_repayment_statement'))
                                       ->where('id_member',$id_member)
                                       ->where('id_repayment',$id_repayment)
                                       ->whereIn('id_repayment_statement',$statements)
                                       ->get();

            $idRepaymentTransactionsPlucked = collect($idRepaymentTransactions)->pluck('id_repayment_transaction')->toArray();

            if(count($idRepaymentTransactionsPlucked) == 0){
                $idRepaymentTransactionsPlucked = [0];
            }

            $id_repayment_transaction = 0;

            // check if loan is mature upon payment
            $maturityValidation  = DB::table('loan')
                                   ->select('maturity_date')
                                   ->where('id_loan',$idLoan)
                                   ->first()->maturity_date;
                                     // $app_payment = $RepaymentController->PopulatePaymentAuto($TokenHolder[$idLoan],$amount,$idRepaymentTransactionsPlucked,$currentDueDate);

                                     // dd($app_payment);
            if($maturityValidation < $currentDueDate){
                // if loan is already matured
                $app_payment = $RepaymentController->PopulatePaymentMatured($TokenHolder[$idLoan],$amount,$idRepaymentTransactionsPlucked,$currentDueDate,$LoanSurchargeDiscount[$idLoan] ?? 0);


            }else{
                // if not matured
                $app_payment = $RepaymentController->PopulatePaymentAuto($TokenHolder[$idLoan],$amount,$idRepaymentTransactionsPlucked,$currentDueDate);
            }

            
            $app_payment['id_member'] = $id_member;

            if(count($statements) > 1){
                $PaymentPerStatement = array();
                //if loan exists on multipe statement selected
                foreach($statements as $s){
                    $StatementPayment = $LoanPayment[$s][$idLoan];
                    $PaymentPerStatement[$s] = $StatementPayment;
                   
                }
                $SeparatedPayment = $this->ApplyStatementPayment($PaymentPerStatement,$app_payment['payments']);
                // dd($SeparatedPayment);
                foreach($SeparatedPayment as $statementID=>$s){
                    $t = $app_payment;

                    $t['amount_paid'] = $s['total'];
                    $t['payments'] = $s['loan'];
                    $t['id_repayment_statement'] = $statementID;
                    array_push($CompiledPayment,$t);
                }
            }else{
                $app_payment['id_repayment_statement'] = $statements[0];
                array_push($CompiledPayment,$app_payment);
            }
        }

        //Main Object for Loan Payment posting
        $CompiledPerStatement = $g->array_group_by($CompiledPayment,['id_repayment_statement','id_member']);

        //get Loan Payment Transaction Reference
        foreach($CompiledPerStatement as $idStatement=>$members){
            foreach($members as $id_member=>$m){
                $id_repayment_transaction = DB::table('repayment_transaction')->where('id_member',$id_member)->where('id_repayment',$id_repayment)->where('id_repayment_statement',$idStatement)->max('id_repayment_transaction') ?? 0;

   

                for($i=0;$i<count($CompiledPerStatement[$idStatement][$id_member]);$i++){
                    $CompiledPerStatement[$idStatement][$id_member][$i]['id_repayment_transaction'] = $id_repayment_transaction;
                }
            }
        }



        $postData = array();

        foreach($CompiledPerStatement as $idStatement=>$MemberTransactions){
            $tempPost = $this->CompileRepaymentTransaction($date,$MemberTransactions,$idStatement);
            $postData = array_merge($postData,$tempPost);
        }
        // dd($postData);


        $TotalPayment = collect($postData)->sum('totalPayment');
        //check if there is payment made
        if($TotalPayment == 0){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Invalid Request";

            return response($data);
        }

        $Paymode = $request->RepaymentDetails['paymode'];
        
        $AppliedPayment = collect($CompiledPaymentMethod)->sum('amount');

        
        if(ROUND($AppliedPayment,2) < ROUND($TotalPayment,2)){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Insufficient Payment amount";
            return response($data);
        }elseif(ROUND($AppliedPayment,2) > ROUND($TotalPayment,2) && $Paymode == 1){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Invalid Payment Amount";
            return response($data);            
        }




        $RepaymentPostOBJ = [
            'payment_for'=>$mode,
            'date'=>$date,
            'or_number'=>$RepaymentDetails['or_number'],
            'remarks'=>"",
            'total_amount'=>$TotalPayment,
            'id_paymode'=>$RepaymentDetails['paymode']
        ];


        $addFields = [
            'transaction_type' => $RepaymentDetails['paymode'], //paymode
            'transaction_date'=>$RepaymentPostOBJ['date'],
            'date'=>$currentDueDate,
            'id_user'=>MySession::myId()
        ];
        $updateFields = [
            'transaction_type' => $RepaymentDetails['paymode'],
            'transaction_date'=>$RepaymentPostOBJ['date'],
            'email_sent'=>DB::raw("if(email_sent=0,0,1)")
        ];  


        // $id_repayment = 18;

        DB::beginTransaction();
        try{
            if($opcode == 0){
                $RepaymentPostOBJ['id_user'] = MySession::myId();
                // $RepaymentPostOBJ['id_baranggay_lgu'] = $request->br;
                DB::table('repayment')
                ->insert([$RepaymentPostOBJ]);

                $id_repayment = DB::table('repayment')->max('id_repayment');            
            }else{
                $this->updateRepaymentStatementStatus($id_repayment,0);
                DB::table('repayment')
                ->where('id_repayment',$id_repayment)
                ->update($RepaymentPostOBJ);
            }
            foreach($postData as $i=>$post){
                $additionalFields = ($post['id_repayment_transaction'] > 0)?$updateFields:$addFields;
                $rt_opcode = ($post['id_repayment_transaction'] > 0)?1:0;

                $postData[$i]['repayment_transaction'] = $postData[$i]['repayment_transaction']+$additionalFields;


                $postData[$i]['repayment_transaction']['or_no'] = $RepaymentPostOBJ['or_number'];  
                // $postData[$i]['repayment_transaction']['id_bank'] = $RepaymentPostOBJ['id_bank'];


                $for_cancellation = false;
                if($rt_opcode == 0){ //ADD
                    $edited = false;

                    if($postData[$i]['repayment_transaction']['total_payment'] == 0){
                        // if there is no payment made
                        continue;
                    }

                    $addToken = $this->generateRandomString(5);
                    $postData[$i]['repayment_transaction']['repayment_token'] = DB::raw("concat(DATE_FORMAT(now(),'%m%d%Y%H%i%s'),concat(LEFT(MD5(NOW()), 5)),'$addToken')");

                    $postData[$i]['repayment_transaction']['id_repayment'] = $id_repayment;

                    //push repayment transaction
                    DB::table('repayment_transaction')
                    ->insert($postData[$i]['repayment_transaction']);

                    $id_repayment_transaction = DB::table('repayment_transaction')->max('id_repayment_transaction');
                }else{ // EDIT
                    $id_repayment_transaction = $post['id_repayment_transaction'];
                    $for_cancellation = ($postData[$i]['repayment_transaction']['total_payment'] > 0)?false:true;
                    $edited = true;

                    if(!$for_cancellation){

                        $previousTransactionLoans = DB::table('repayment_loans')->select('id_loan')->where('id_repayment_transaction',$id_repayment_transaction)->groupby('id_loan')->get()->pluck('id_loan')->toArray();

                        $postData[$i]['repayment_transaction']['status'] = 0;
                        DB::table('repayment_loans')
                        ->where('id_repayment_transaction',$id_repayment_transaction)
                        ->delete();

                        DB::table('repayment_loan_surcharges')
                        ->where('id_repayment_transaction',$id_repayment_transaction)
                        ->delete();

                        DB::table('repayment_fees')
                        ->where('id_repayment_transaction',$id_repayment_transaction)
                        ->delete();

                        DB::table('repayment_rebates')
                        ->where('id_repayment_transaction',$id_repayment_transaction)
                        ->delete();



                        DB::table('repayment_loan_discount')
                        ->where('id_repayment_transaction',$id_repayment_transaction)
                        ->delete();


                        //UNDO THE PAYMENT STATUS OF LOANS ON PREVIOUS RECORD
                        DB::table('loan_table')
                        ->whereIn('id_loan',$previousTransactionLoans)
                        ->update(['is_paid'=>DB::raw("CASE
                        WHEN ifnull(getLoanTotalTermPayment(id_loan,term_code),0) = 0 THEN 0
                        WHEN (total_due-ifnull(getLoanTotalTermPayment(id_loan,term_code),0)) =0 THEN 1
                        ELSE 2 END")]);

                        DB::table('loan')
                        ->whereIn('id_loan',$previousTransactionLoans)
                        ->update(['status'=>3,'loan_status'=>1]);

                    }else{
                        $postData[$i]['repayment_transaction']['status'] = 10;
                    }

                    //UPDATE REPAYMENT TRANSACTION
                    DB::table('repayment_transaction')
                    ->where('id_repayment_transaction',$id_repayment_transaction)
                    ->update($postData[$i]['repayment_transaction']);
                }

                $canceled =$for_cancellation;
                if(!$for_cancellation){
                     //push repayment_lons
                    foreach($post['repayment_loans'] as $c=>$rp){
                        $postData[$i]['repayment_loans'][$c]['id_repayment_transaction'] = $id_repayment_transaction;

                    }


                    $idLoanUnq = array_unique(collect($postData[$i]['repayment_loans'])->pluck('id_loan')->toArray());

                    $idLoanUnq = array_values($idLoanUnq);

                    $repaymentLoanDiscount = array();
                    foreach($idLoanUnq as $unq){
                        if(isset($LoanSurchargeDiscount[$unq])){
                            $repaymentLoanDiscount[]=[
                                'id_repayment_transaction'=>$id_repayment_transaction,
                                'id_loan'=>$unq,
                                'amount'=>$LoanSurchargeDiscount[$unq]
                            ];
                        }
                    }

                    DB::table('repayment_loans')
                    ->insert($postData[$i]['repayment_loans']);

                    DB::table('repayment_loan_discount')
                    ->insert($repaymentLoanDiscount);





                    // //push repayment loan surcharges 
                    // foreach($post['repayment_loan_surcharges'] as $c=>$rp){
                    //     $postData[$i]['repayment_loan_surcharges'][$c]['id_repayment_transaction'] = $id_repayment_transaction;
                    // }

                    // DB::table('repayment_loan_surcharges')
                    // ->insert($postData[$i]['repayment_loan_surcharges']);


                    // //push repayment fees
                    // foreach($post['repayment_fees'] as $c=>$rp){
                    //     $postData[$i]['repayment_fees'][$c]['id_repayment_transaction'] = $id_repayment_transaction;
                    // }

                    // DB::table('repayment_fees')
                    // ->insert($postData[$i]['repayment_fees']);


                    // //push repayment rebates
                    // foreach($post['rebates'] as $c=>$rp){
                    //     $postData[$i]['rebates'][$c]['id_repayment_transaction'] = $id_repayment_transaction;
                    // }

                    // DB::table('repayment_rebates')
                    // ->insert($postData[$i]['rebates']);

                    $this->CRV($id_repayment_transaction,0);
                    $crv = CRVModel::RepaymentCRV($id_repayment_transaction,$edited,$canceled);
                    $token = DB::table('repayment_transaction')->select('repayment_token')->where('id_repayment_transaction',$id_repayment_transaction)->first()->repayment_token;
                    if($rt_opcode == 0){
                        $RepaymentController->post_or(new Request(['repayment_token'=>$token,
                                                                    'or_no'=>$RepaymentPostOBJ['or_number'],
                                                                    'or_opcode'=>0]));
                    }else{
                        $RepaymentController->GenerateRepaymentCashReceiptData($id_repayment_transaction,$RepaymentPostOBJ['or_number']);
                    }
                    DB::table('repayment_transaction')
                    ->where('id_repayment_transaction',$id_repayment_transaction)
                    ->update(['id_cash_receipt_voucher'=>$crv['id_cash_receipt_voucher']]);               
                }else{
                    $this->CRV($id_repayment_transaction,10);
                }

                //Loan Status Update
                $LoanPaymentApplied = DB::table('repayment_loans as rl')
                                      ->select(DB::raw('loan.id_loan,loan.loan_token'))
                                      ->leftJoin('loan','loan.id_loan','rl.id_loan')
                                      ->where('rl.id_repayment_transaction',$id_repayment_transaction)
                                      ->groupBy('rl.id_loan')
                                      ->get();

                $loan_ids = array();
                $tokenList = array();
                foreach($LoanPaymentApplied as $lp){
                    array_push($loan_ids,$lp->id_loan);
                    array_push($tokenList,$lp->loan_token);
                }

                $this->UpdateLoanStatus($loan_ids,$tokenList,$date);


                // $repayment_parent_loans = array_merge($repayment_parent_loans,$loan_ids);
            }
            //push payments
            DB::table('repayment_payment')
            ->where('id_repayment',$id_repayment)
            ->delete();

            for($i=0;$i<count($CompiledPaymentMethod);$i++){
                $CompiledPaymentMethod[$i]['id_repayment'] = $id_repayment;
            }

            DB::table('repayment_payment')
            ->insert($CompiledPaymentMethod);

            if($mode == 2){
                //if per statement update the repayment statement status
                $this->updateRepaymentStatementStatus($id_repayment,1);
                // dd("Q");
            }



            //set Loan with Discount as Closed
            foreach($LoanSurchargeDiscount as $idLoan=>$val){
                DB::table('loan')
                ->where('id_loan',$idLoan)
                ->update(['status'=>6,'loan_status'=>2]);
            }

            //SET CHANGE ON CHECK PAYMENT
            DB::select("UPDATE (
            SELECT r.id_repayment,r.total_amount,SUM(rp.amount) as total_payment FROM repayment as r
            LEFT JOIN repayment_payment as rp on rp.id_repayment = r.id_repayment
            WHERE r.id_repayment=? AND r.id_paymode = 4) as k
            LEFT JOIN repayment as rp on rp.id_repayment = k.id_repayment AND rp.id_paymode = 4
            SET rp.change_payable=if(k.total_payment > k.total_amount,k.total_payment-k.total_amount,0);",[$id_repayment]);


            //Change CDV

            $details = DB::table('repayment')
                       ->select(DB::raw("change_payable,ifnull(id_cash_receipt_voucher,0) as id_cash_receipt_voucher"))
                       ->where('id_repayment',$id_repayment)
                       ->first();

            if($details->change_payable > 0 || $details->id_cash_receipt_voucher > 0){
                CRVModel::ChangePayableCRV($id_repayment,$details->id_cash_receipt_voucher);
            }

            DB::commit();

            $data['ID_REPAYMENT'] = $id_repayment;
            $data['RESPONSE_CODE'] = "SUCCESS";
            $data['message'] = "Loan Payment Successfully Posted";

            return response($data);
        }catch(\Illuminate\Database\QueryException $ex){
            DB::rollback();
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "SOMETHING WENT WRONG";
            $data['error_message'] = $ex->getMessage();
            return response($data);
        }catch(\Exception $ex){
            DB::rollback();
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "SOMETHING WENT WRONG";
            $data['error_message'] = $ex->getMessage();
            return response($data);
        }


        dd($postData);  



    }
    public function updateRepaymentStatementStatus($id_repayment,$mode){
        //mode = 0 - revert status to not paid; 1 - fully paid

        $ConditionalString = ($mode==0)?"0":"if(k.applied_payment >= k.total_due,1,0)";
        DB::select("UPDATE (
        SELECT rsd.id_repayment_statement,SUM(rsd.loan_due) as total_due,getSatementPayment(rsd.id_repayment_statement,0) as applied_payment FROM (
        SELECT rs.id_repayment_statement FROM repayment as r
        LEFT JOIN repayment_transaction as rt on rt.id_repayment = r.id_repayment
        LEFT JOIN repayment_statement as rs on rs.id_repayment_statement = rt.id_repayment_statement
        WHERE r.id_repayment=?
        GROUP BY id_repayment_statement) as r
        LEFT JOIN repayment_statement_details as rsd on rsd.id_repayment_statement = r.id_repayment_statement
        GROUP BY rsd.id_repayment_statement) as k
        LEFT JOIN repayment_statement as rs on rs.id_repayment_statement = k.id_repayment_statement
        SET rs.status = $ConditionalString;",[$id_repayment]);        
    }

    public function compilePayments($RepaymentDetails,$Payments){
        $id_paymode = $RepaymentDetails['paymode'];

        $out['RepaymentPayment'] = array();
        foreach($Payments as $payment){
            $temp = array();
            $temp['id_repayment'] = 0;
            foreach($payment as $key=>$value){
                $temp[$key] = $value;
            }
            array_push($out['RepaymentPayment'],$temp);
        }
        return $out;


        dd($Payments);


        dd($id_paymode);
    }

    public function ApplyStatementPayment($amounts,$paymentLoan){

        // dd($amounts,$paymentLoan);
        $output = array();

        foreach($amounts as $statementID=>$amount){
            $currentAmt = $amount;
            $output[$statementID] = array();
            $output[$statementID]['total'] = $amount;
            $output[$statementID]['loan'] = array();
            $L = array_values($paymentLoan);

            for($i=0;$i<count($L);$i++){

                if(ROUND($currentAmt) > 0){
                    $totalPaid = $L[$i]['paid_principal']+$L[$i]['paid_interest'];
                    if($currentAmt >= $totalPaid){
                        array_push($output[$statementID]['loan'],$L[$i]);
                        $currentAmt -= ROUND($totalPaid,2);
                        unset($paymentLoan[$i]);
                    }else{
                        $cur = $L[$i];

                        if($currentAmt >= $L[$i]['paid_principal']){
                            $paid_principal = ROUND($L[$i]['paid_principal'],2);
                        }else{
                            $paid_principal = ROUND($currentAmt,2);
                        }
                        $paid_principal = ROUND($paid_principal,2);

                        $currentAmt -= ROUND($paid_principal,2);



                        if($currentAmt >= $L[$i]['paid_interest']){
                            $paid_interest = ROUND($L[$i]['paid_interest'],2);
                        }else{
                            $paid_interest = ROUND($currentAmt,2);
                        }

                        $currentAmt -= ROUND($paid_interest,2);

              

                        $paymentLoan[$i]['paid_principal'] -= $paid_principal;
                        $paymentLoan[$i]['paid_interest'] -= $paid_interest;

                        $cur['paid_principal'] = $paid_principal;
                        $cur['paid_interest'] = $paid_interest;  

                        array_push($output[$statementID]['loan'],$cur);                   

                    }                    
                }

            }
        }
        // dd($output);

        return $output;

        dd($L);
        dd($amounts,$paymentLoan);
    }

    public function CompileRepaymentTransaction($date,$payments,$idStatement){


    
        $PostOBJ = array();
        foreach($payments as $pay){
            $tempPostOBJ=array();


    
            $tempPostOBJ['id_repayment_transaction'] = $pay[0]['id_repayment_transaction'];
            $tempPostOBJ['repayment_loans'] = array();


            $fully_paid_loan = array(); 
            $rebatesObj = array();
            $total_rebates = 0;
            $total_penalty=0;

            $total_loan_payment = 0;

            foreach($pay as $p){
                $total_loan_payment += $p['amount_paid'];
                foreach($p['payments'] as $LoanPayment){
                    array_push($tempPostOBJ['repayment_loans'],$LoanPayment);
                }
            }

            $tempPostOBJ['repayment_transaction'] = [
                'id_member'=>$pay[0]['id_member'],
                'total_loan_payment'=>$total_loan_payment,
                'total_rebates'=>$total_rebates,
                'swiping_amount'=>0,
                'total_penalty'=>0,
                'total_fees'=>0,
                'change'=>0,
                'total_payment'=>$total_loan_payment+$total_penalty-$total_rebates,
                'input_mode'=>1,
                'id_repayment_statement'=> $idStatement
            ];
            $tempPostOBJ['totalPayment'] =$tempPostOBJ['repayment_transaction']['total_payment'];

            array_push($PostOBJ,$tempPostOBJ);
        } 
       
        return $PostOBJ;
    }
    public function CRV($id_repayment_transaction,$status){
        DB::table('cash_receipt_voucher')
        ->where('type',2)
        ->where('reference',$id_repayment_transaction)
        ->update(['status'=>$status]);

        DB::table('cash_receipt')
        ->where('type',3)
        ->where('reference_no',$id_repayment_transaction)
        ->update(['status'=>$status,"cancel_reason"=>""]);
    }

    public function UpdateLoanStatus($loan_ids,$loan_tokens,$date){
        DB::table('loan_table')
        ->whereIn('id_loan',$loan_ids)
        ->update(['is_paid'=>DB::raw("CASE
            WHEN ifnull(getLoanTotalTermPayment(id_loan,term_code),0) = 0 THEN 0
            WHEN (total_due-ifnull(getLoanTotalTermPayment(id_loan,term_code),0)) =0 THEN 1
            ELSE 2 END")]);

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
    }

    public function generateRandomString($length = 8) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function view($id_repayment,Request $request){

        $data = $this->viewData($id_repayment);


        $deposits = DB::select("Select cdd.id_repayment_payment,cdd.id_check_deposit,DATE_FORMAT(cd.date_deposited,'%m/%d/%Y') as date_deposit,tb.bank_name,cd.remarks
        from check_deposit_details as cdd
        LEFT JOIN check_deposit as cd on cd.id_check_deposit = cdd.id_check_deposit
        LEFT JOIN tbl_bank as tb on tb.id_bank = cd.id_bank
        WHERE cdd.id_repayment = ? AND cd.status <> 10;",[$id_repayment]);

        // dd($data);

        $g = new GroupArrayController();

        $data['deposits'] = $g->array_group_by($deposits,['id_repayment_payment']);
        $data['changes'] = DB::table('change_payable as cp')
                           ->select(DB::raw("cp.id_change_payable,DATE_FORMAT(cp.date,'%m/%d/%Y') as change_date,cp.total_amount,cp.remarks,DATE_FORMAT(cp.date_created,'%m/%d/%Y %h:%i %p') as date_posted"))
                           ->where('cp.id_repayment',$id_repayment)
                           ->where('cp.status','<>',10)
                           ->orDerby('cp.date','DESC')
                           ->orDerby('cp.id_change_payable','DESC')
                           ->get();
        // dd($data);
        return view('repayment-bulk.view',$data);
    }
    public function print($id_repayment){
        $data = $this->viewData($id_repayment);
        $data['file_name'] = "Loan Payment ID# {$id_repayment}";
        $html = view('repayment-bulk.print',$data);
        $pdf = PDF::loadHtml($html);
        $pdf->setOption("encoding","UTF-8");
        $pdf->setOption('margin-bottom', '0.33in');
        $pdf->setOption('margin-top', '0.33in');
        $pdf->setOption('margin-right', '0.33in');
        $pdf->setOption('margin-left', '0.42in');
        $pdf->setOption('header-left', 'Page [page] of [toPage]');
        // $pdf->setOption('header-right', 'No.: '.$data['details']->month_year.'-'.$data['details']->id_repayment_statement);
    
        $pdf->setOption('header-font-size', 8);
        $pdf->setOption('header-font-name', 'Calibri');

        return $pdf->stream("{$data['file_name']}.pdf",array('Attachment'=>1));
      

        dd($data);
    }
    public function viewData($id_repayment){
        $data['details'] = DB::table('repayment as r')
                            ->select(DB::raw("r.id_repayment,DATE_FORMAT(r.date,'%m/%d/%Y') as date,if(r.payment_for=1,'Individual','Statement') as payment_for,r.or_number,if(r.id_paymode=1,'Cash','Check') as paymode,r.status,r.total_amount,r.payment_for as payment_for_code,
                                CASE 
                                WHEN r.status = 0 THEN 'Posted'
                                WHEN r.status = 1 THEN 'Deposited'
                                WHEN r.status = 10 THEN 'Cancelled'
                                ELSE '' END as status_description,r.remarks,r.id_paymode,DATE_FORMAT(r.status_date,'%m/%d/%Y') as status_date,r.reason,r.deposit_status,r.id_cash_receipt_voucher,r.change_payable"))
                            ->where('r.id_repayment',$id_repayment)
                            ->first();
                            // dd($data);

        $g = new GroupArrayController();
        if($data['details']->payment_for_code == 2){
            $statements = DB::select("SELECT rs.id_repayment_statement,rt.id_member,concat(if(bl.type=1,'Brgy. ','LGU -'),bl.name,' (Statement No. ',rs.id_repayment_statement,')') as statement,
            FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member, getLoanServiceName(l.id_loan_payment_type,ls.name,l.terms) as loan_name,
            SUM(rl.paid_principal+paid_interest+paid_fees+paid_surcharge) as payment,rt.id_cash_receipt_voucher,l.id_loan,l.loan_token
            FROM repayment_transaction as rt
            LEFT JOIN repayment_statement as rs on rs.id_repayment_statement = rt.id_repayment_statement
            LEFT JOIN baranggay_lgu as bl on bl.id_baranggay_lgu = rs.id_baranggay_lgu
            LEFT JOIN repayment_loans as rl on rl.id_repayment_transaction = rt.id_repayment_transaction
            LEFT JOIN loan as l on l.id_loan = rl.id_loan
            LEFT JOIN member as m on m.id_member = l.id_member
            LEFT JOIN loan_service as ls on ls.id_loan_service = l.id_loan_service
            WHERE rt.id_repayment = ?
            GROUP BY rl.id_loan;",[$id_repayment]);

            $data['statamentData'] = $g->array_group_by($statements,['statement','id_member']);

          

        }else{
            $loans = DB::select("SELECT rt.id_member,
            FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member, getLoanServiceName(l.id_loan_payment_type,ls.name,l.terms) as loan_name,
            SUM(rl.paid_principal+paid_interest+paid_fees+paid_surcharge) as payment,rt.id_cash_receipt_voucher,l.id_loan,l.loan_token
            FROM repayment_transaction as rt
            LEFT JOIN repayment_loans as rl on rl.id_repayment_transaction = rt.id_repayment_transaction AND rl.status <> 10
            LEFT JOIN loan as l on l.id_loan = rl.id_loan
            LEFT JOIN member as m on m.id_member = l.id_member
            LEFT JOIN loan_service as ls on ls.id_loan_service = l.id_loan_service
            WHERE rt.id_repayment = ?
            GROUP BY rl.id_loan;",[$id_repayment]);

            $data['Loans'] = $g->array_group_by($loans,['id_member']);
        }

        $data['paymentDetails'] = DB::table('repayment_payment as rp')
                                 ->select(DB::raw("if(rp.check_type=1,'On-dated','Post Dated') as check_type,rp.check_bank,DATE_FORMAT(rp.check_date,'%m/%d/%Y') as check_date,rp.check_no,rp.amount,rp.remarks,rp.id_repayment_payment"))
                                 ->where('id_repayment',$id_repayment)
                                 ->get();

        return $data;

    }



    public function updateStatus(Request $request){
        $id_repayment  = $request->id_repayment;
        $cancel_reason = $request->cancel_reason;

        $details = DB::table('repayment')->where('id_repayment',$id_repayment)->first();

        if($details->status == 10){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Loan Payment already cancelled";

            return response($data);
        }

        $this->updateRepaymentStatementStatus($id_repayment,0);



        $id_repayment_transactions = DB::table('repayment_transaction as rt')
                                   ->select('id_repayment_transaction')
                                    ->where('status','<>',10)
                                    ->where('id_repayment',$id_repayment)
                                    ->get()->pluck('id_repayment_transaction')->toArray();

        $repayment = new RepaymentController();
        foreach($id_repayment_transactions as $id_repayment_transaction){
            $r = new Request(['id_repayment_transaction'=>$id_repayment_transaction,'cancel_repayment'=>$cancel_reason,'no_entry'=>false]);

            $repayment->cancel_repayment($r);
            
        }

        DB::table('repayment')->where('id_repayment',$id_repayment)->update(['status'=>10,'status_user'=>MySession::myId(),'status_date'=>now(),'reason'=>$cancel_reason]);
        $data['RESPONSE_CODE'] = "SUCCESS";

        $this->updateRepaymentStatementStatus($id_repayment,0);
        return response($data);

    }

        
}
