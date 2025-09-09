<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\MySession;
use App\WebHelper;
use App\CredentialModel;
use PDF;
use App\Loan;

class RepaymentStatementController extends Controller
{
    public function index(Request $request){
        $data['credential']= CredentialModel::GetCredential(MySession::myPrivilegeId());
        if(!$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }   

        $data['statements'] = DB::table('repayment_statement as rs')
                              ->select(DB::raw("rs.id_repayment_statement,rs.id_baranggay_lgu,rs.id_repayment_statement,date,due_date,bl.name as baranggay_lgu,if(bl.type=1,'Barangay','LGU') as group_,DATE_FORMAT(due_date,'%m%Y') as month_year,DATE_FORMAT(rs.date,'%m/%d/%Y') as statement_date,DATE_FORMAT(due_date,'%M %Y') as month_due,DATE_FORMAT(rs.date_created,'%m/%d/%Y %h:%i %p') as date_created,SUM(rsd.loan_due) as total_due,getStatementPayment(rs.id_repayment_statement) as amount_paid,rs.status,if(rs.status=1,'Paid',if(rs.status=2,'Deposited',if(rs.status=10,'Cancelled',''))) as status_description,if(rs.status=1,'primary',if(rs.status=2,'success',if(rs.status=10,'danger',''))) as status_class"))
                              ->leftJoin('baranggay_lgu as bl','bl.id_baranggay_lgu','rs.id_baranggay_lgu')
                              ->leftJoin('repayment_statement_details as rsd','rsd.id_repayment_statement','rs.id_repayment_statement')
                              ->groupBy('rs.id_repayment_statement')
                              ->orderBy('rs.id_repayment_statement','DESC')
                              ->get();

        return view('repayment-statement.index',$data);
    }
    public function create(Request $request){
       $data['branches']  = DB::table('baranggay_lgu')
        ->select(DB::raw("id_baranggay_lgu,name,if(type=1,'Barangay','LGU') as type"))
        ->orderByRaw("type,name")
        ->get();

        $data['selected_branch'] = $request->br ?? $data['branches'][0]->id_baranggay_lgu;

        $data['opcode'] = 0;



        $g = new GroupArrayController();
        $data['branches'] = $g->array_group_by($data['branches'],['type']);

        $data['date'] = $request->date ?? MySession::current_date();
        $data['date_due'] = WebHelper::ConvertDatePeriod($data['date']);

        // $data['']

        $cur_statement = DB::table('repayment_statement')
                     ->select(DB::raw("DATE_FORMAT(due_date,'%m%Y') as month_year,id_repayment_statement"))
                     ->where('id_baranggay_lgu',$data['selected_branch'])
                     ->where('due_date',$data['date_due'])
                     ->where('status','<>',10)
                     ->first();

        $data['with_statement'] = false;

        


        $param = ['branch'=>$data['selected_branch'],'date1'=>$data['date_due'],'date2'=>$data['date_due']];
        $g = new GroupArrayController();
        $loans = $this->ActiveLoans($param,0);

        $data['loans'] = $g->array_group_by($loans,['id_member']);

    
        return view('repayment-statement.form',$data);
    }

    public function edit($id_repayment_statement,Request $request){
        $details=$data['details'] = DB::table('repayment_statement')
                           ->where('id_repayment_statement',$id_repayment_statement)
                           ->first();

        if($details->status != 0){
            $url = "/repayment-statement/view/$id_repayment_statement?".http_build_query($request->all());
            return redirect()->to($url);            
        }

        $data['branches']  = DB::table('baranggay_lgu')
        ->select(DB::raw("id_baranggay_lgu,name,if(type=1,'Barangay','LGU') as type"))
        ->orderByRaw("type,name")
        ->get();

        $data['selected_branch'] = $details->id_baranggay_lgu;

        $data['opcode'] = 1;

        $g = new GroupArrayController();
        $data['branches'] = $g->array_group_by($data['branches'],['type']);

        $data['date'] = $details->date;
        $data['date_due'] = WebHelper::ConvertDatePeriod($data['date']);
        $data['with_statement'] = false;


        $param = ['branch'=>$data['selected_branch'],'date1'=>$data['date_due'],'date2'=>$data['date_due']];
        $g = new GroupArrayController();
        $loans = $this->ActiveLoans($param,$id_repayment_statement);

        // dd($loans);
        $data['loans'] = $g->array_group_by($loans,['id_member']);

        $data['selected_member'] = DB::table('repayment_statement_details')
                                   ->select('id_member')
                                   ->where('id_repayment_statement',$id_repayment_statement)
                                   ->groupBy('id_member')
                                   ->get()->pluck('id_member')->toArray();

        return view('repayment-statement.form',$data);
    }

    public function view($id_repayment_statement,Request $request){
        // $this->PushRepaymentBulk($id_repayment_statement);
        // return;
        // $url = '/test_rr?'.http_build_query($request->all());
        // return redirect()->to($url);

        $data['details'] = DB::table('repayment_statement as rs')
                          ->select(DB::raw("rs.id_repayment_statement,rs.id_baranggay_lgu,rs.id_repayment_statement,date,due_date,bl.name as baranggay_lgu,if(bl.type=1,'Barangay','LGU') as group_,DATE_FORMAT(due_date,'%m%Y') as month_year,DATE_FORMAT(rs.date,'%m/%d/%Y') as statement_date,DATE_FORMAT(due_date,'%M %Y') as month_due,remarks,total_amount,date_received,bl.treasurer,rs.status,rs.status_remarks,DATE_FORMAT(rs.status_date,'%m/%d/%Y %h:%i %p') as status_date,if(rs.status=1,'Paid',if(rs.status=2,'Deposited',if(rs.status=10,'Cancelled',''))) as status_description,if(rs.status=1,'primary',if(rs.status=2,'success',if(rs.status=10,'danger',''))) as status_class"))
                          ->leftJoin('baranggay_lgu as bl','bl.id_baranggay_lgu','rs.id_baranggay_lgu')
                          ->where('rs.id_repayment_statement',$id_repayment_statement)
                          ->first();

        $loans = $this->StatementLoans($id_repayment_statement);

        $g = new GroupArrayController();
        $data['loans'] = $g->array_group_by($loans,['id_member']);
        $data['opcode'] = 1;
        $data['selected_branch'] = $data['details']->id_baranggay_lgu;
        $data['date'] = $data['details']->date;
        $data['date_due'] = $data['details']->due_date;
        

        $data['allow_post'] = true;     

        if($data['details']->status > 1){
            $data['allow_post'] = false;
        }           
        return view('repayment-statement.view',$data);
    }

    public function ActiveLoans($param,$id_repayment_statement){
        $param['date3'] = $param['date4'] =$param['date2'];



        // $loans = DB::select("WITH statement as (
        //     SELECT loan.id_loan,loan.id_member,
        //     getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms) as loan_name,
        //     FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member,
        //     ifnull(getTotalDueTypeRepaymentX(loan.id_loan,:date1,0,1),0) as principal,
        //     ifnull(getTotalDueTypeRepaymentX(loan.id_loan,:date2,0,2),0) as interest,
        //     ifnull(getTotalDueTypeRepaymentX(loan.id_loan,:date3,0,4),0) as surcharge,
        //     getTotalAmortization(loan.id_loan,loan.maturity_date) as amtz,
        //     if(:date4 > maturity_date,1,0) as matured
        //     FROM loan
        //     LEFT JOIN member as m on m.id_member = loan.id_member
        //     LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
        //     WHERE loan.loan_status = 1 AND m.id_baranggay_lgu = :branch
        // )
        // SELECT s.id_loan,s.id_member,s.loan_name,s.member,
        // @due:=CASE 
        //       WHEN matured = 1 THEN (principal+interest)
        //       ELSE amtz END as due,
        // surcharge,
        // @dueFinal := @due+surcharge as total_due,
        // ifnull(rsd.)
        // FROM statement as s
        // LEFT JOIN repayment_statement_details as rsd on rsd.id_repayment_statement=$id_repayment_statement AND rsd.id_loan = s.id_loan
        // WHERE (s.principal+s.interest+s.surcharge) > 0
        // ORDER BY member;",$param);

        // $loans = DB::select("SELECT sd.*,
        //                     FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member,
        //                     getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms) as loan_name,loan.loan_token,
        //                     @q:=ifnull(rsd.loan_due,if($id_repayment_statement=0,current_due,0)) as loan_due,if(@q > actual_balance,actual_balance,@q) as loan_due
        //                     FROM (
        //                     SELECT loan.id_loan,loan.id_member,getTotalAmortization(loan.id_loan,loan.maturity_date) as balance,
        //                     @cur_due:=getTotalAmortization(loan.id_loan,:date1) as current_due,ROUND(@cur_due,2) as payment,
        //                     getLoanRebates(loan.id_loan,:date2) as rebates,0 as penalty,getTotalDueAsOfRepaymentEx(loan.id_loan,if(:date3 > loan.maturity_date,:date4,loan.maturity_date),0) as actual_balance
        //                     FROM loan
        //                     LEFT JOIN member as m on m.id_member = loan.id_member
        //                     WHERE loan.loan_status = 1 AND m.id_baranggay_lgu = :branch) as sd
        //                     LEFT JOIN member as m on m.id_member  = sd.id_member
        //                     LEFT JOIN loan on loan.id_loan = sd.id_loan
        //                     LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
        //                     LEFT JOIN repayment_statement_details as rsd on rsd.id_repayment_statement=$id_repayment_statement AND rsd.id_loan = loan.id_loan
        //                     LEFT JOIN repayment_statement_details as rsd on rsd.id_repayment_statement = :id AND rsd.id_loan = loan.id_loan
        //                     WHERE current_due > 0
        //                     ORDER BY member",$param);



        $param['branch2'] = $param['branch'];
        $param['date5'] = $param['date6'] = $param['date7']=$param['date8'] =$param['date3'];

        $stDate = date('Y-m-01',strtotime($param['date1']));

        $param['stDate1'] = $param['stDate2'] = $stDate;


        $param['id_repayment_statement'] = $id_repayment_statement;

        $loans = DB::select("WITH loans AS (
            SELECT loan.id_loan,
            SUM(CASE WHEN lt.due_date < :date1 THEN lt.repayment_amount + lt.interest_amount + lt.fees ELSE 0 END) as previous_balance,
            SUM(CASE WHEN lt.due_date >= :stDate1 AND lt.due_date <= :date2 THEN lt.repayment_amount + lt.interest_amount + lt.fees ELSE 0 END) as current_balance,
            SUM(lt.surcharge) as surcharge,
            if(:date3 > maturity_date,1,0) as matured,
            maturity_date
            FROM loan
            LEFT JOIN loan_table as lt on lt.id_loan = loan.id_loan
            WHERE loan.id_baranggay_lgu = :branch AND loan.loan_status = 1 AND lt.due_date <= :date4
            GROUP BY loan.id_loan
        ),
        payments as (
            SELECT loan.id_loan,
            SUM(CASE WHEN lt.due_date < :date5 THEN rl.paid_principal + rl.paid_interest + rl.paid_fees ELSE 0 END) as paid_previous,
            SUM(CASE WHEN lt.due_date >= :stDate2 AND lt.due_date <= :date6 THEN rl.paid_principal + rl.paid_interest + rl.paid_fees ELSE 0 END) as paid_current,
            SUM(rl.paid_surcharge) as paid_surcharge
            FROM loan
            LEFT JOIN loan_table as lt on lt.id_loan = loan.id_loan
            LEFT JOIN repayment_loans as rl on rl.id_loan = lt.id_loan AND rl.term_code = lt.term_code
            LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rl.id_repayment_transaction
            WHERE loan.id_baranggay_lgu = :branch2 AND loan.loan_status = 1 AND lt.due_date <= :date7 AND rt.status <> 10
            GROUP BY loan.id_loan
        )
        SELECT loans.id_loan,
        @prev:=previous_balance - ifnull(paid_previous,0) as previous,
        @current:=current_balance - ifnull(paid_current,0) as current,
        @surcharge:=surcharge - ifnull(paid_surcharge,0) as surcharge,
        @balance:=ROUND(@prev+@current+@surcharge,2) as balance,
        ROUND(ifnull(rsd.loan_due,@balance),2) as statement_amount,
        getTotalDueAsOfRepaymentEx(loans.id_loan,:date8,0) as actual_balance,
        FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member,
        getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms) as loan_name,loan.loan_token,loan.id_member
        FROM loans
        LEFT JOIN payments on payments.id_loan = loans.id_loan
        LEFT JOIN loan on loan.id_loan = loans.id_loan
        LEFT JOIN member as m on m.id_member = loan.id_member
        LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
        LEFT JOIN repayment_statement_details as rsd on rsd.id_repayment_statement = :id_repayment_statement AND rsd.id_loan = loan.id_loan
        ORDER BY member;",$param);

        return $loans;
    }

    public function validation($due_date,$id_baranggay_lgu,$id_repayment_statement){
        //validation
        $data = DB::table('repayment_statement')
                 ->where('due_date',$due_date)
                 ->where('id_baranggay_lgu',$id_baranggay_lgu)
                 ->where('status','<>',10)
                 ->where('id_repayment_statement','<>',$id_repayment_statement)
                 ->first() ?? null;
        return $data;
    }

    public function StatementLoans($id_repayment_statement){
        $loans = DB::table('repayment_statement_details as rsd')
                                ->select(DB::raw("rsd.id_repayment_statement_details,rsd.id_loan,loan.loan_token,rsd.id_member,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member,getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms) as loan_name,rsd.loan_due as statement_amount,rsd.previous_d as previous,rsd.current_d as current,rsd.penalty_d as surcharge"))
                                ->leftJoin('member as m','m.id_member','rsd.id_member')
                                ->leftJoin('loan','loan.id_loan','rsd.id_loan')
                                ->leftJoin('loan_service as ls','ls.id_loan_service','loan.id_loan_service')
                                ->leftJoin('repayment_transaction as rt','rt.id_repayment_transaction','rsd.id_repayment_transaction')
                                ->where('rsd.id_repayment_statement',$id_repayment_statement)
                                ->orderBy('member','ASC')
                                ->get();

                                // dd($loans);
        return $loans;        
    }
    public function post(Request $request){
        $date= $request->date ?? MySession::current_date();
        $id_baranggay_lgu = $request->id_barangay_lgu;
        $opcode = $request->opcode ?? 0;

        $loans = $request->loans ?? [];
        $id_repayment_statement = $request->id_repayment_statement ?? 0;
        $members = $request->members ?? [];

        $LoanAmounts = $request->LoanAmounts ?? [];


        if(count($members) == 0){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Please select at least 1 member";
            return response($data);
        }


        // dd($request->all());
        $due_date = WebHelper::ConvertDatePeriod($date);

        $loanFormatted = array();
        foreach($loans as $loan){
            $loanFormatted[$loan['id_loan']] = $loan;
        }

    
        $param = ['branch'=>$id_baranggay_lgu,'date1'=>$due_date,'date2'=>$due_date];

        $ActiveLoans = $this->ActiveLoans($param,0);



        $g = new GroupArrayController();
        $ActiveLoans = $g->array_group_by($ActiveLoans,['id_loan']);

 
        $loanOBJ = array();


        foreach($ActiveLoans as $id_loan=>$l){
            if(in_array($l[0]->id_member,$members)){

                $InputAmount = floatval($LoanAmounts[$l[0]->id_loan] ?? 0);

                if($InputAmount > 0){

                    if($InputAmount > floatval($l[0]->actual_balance)){
                        $loanBal = number_format($l[0]->actual_balance,2);
                        $data['RESPONSE_CODE'] = "ERROR";
                        $data['message'] = "Invalid Statement Amount on {$l[0]->loan_name} - {$l[0]->member}";
                        $data['message2'] = "Loan Balance : {$loanBal}";

                        // dd(1234);

                        return response($data);
                    }
                    $loanOBJ[]=[
                        'id_repayment_statement'=>0,
                        'id_member'=>$l[0]->id_member,
                        'id_loan'=>$id_loan,
                        'previous_d'=>$l[0]->previous,
                        'current_d'=>$l[0]->current,
                        'penalty_d'=>$l[0]->surcharge,
                        'loan_due'=>$InputAmount,
                        'penalty'=>$l[0]->surcharge
                        // 'penalty'=>($loanFormatted[$id_loan])?$loanFormatted[$id_loan]['penalty']:0
                    ];                       
                }
            }
        }
        // dd($loanOBJ);

        $RepaymentStatement = [
            'date' => $date,
            'due_date'=>$due_date,
            'id_baranggay_lgu'=>$id_baranggay_lgu,
            'user_id'=>MySession::myId(),
        ];

        DB::beginTransaction();
        try{

            if($opcode == 0){
                DB::table('repayment_statement')
                ->insert($RepaymentStatement);

                $id_repayment_statement = DB::table('repayment_statement')->max('id_repayment_statement');                
            }


            DB::table('repayment_statement_details')
            ->where('id_repayment_statement',$id_repayment_statement)
            ->delete();

            for($i=0;$i<count($loanOBJ);$i++){
                $loanOBJ[$i]['id_repayment_statement'] = $id_repayment_statement;
            }

            DB::table('repayment_statement_details')
            ->insert($loanOBJ);

            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            dd($e->getMessage());
        }

        $data['RESPONSE_CODE'] = "SUCCESS";
        $data['message'] = "Loan Payment Statement Successfully Posted";
        $data['ID_REPAYMENT_STATEMENT'] = $id_repayment_statement;

        return response($data);


        dd($loanOBJ);

    }

    public function PrintStatement($type,$id_repayment_statement){

        $data['details'] = DB::table('repayment_statement as rs')
        ->select(DB::raw("rs.id_repayment_statement,rs.id_baranggay_lgu,rs.id_repayment_statement,date,due_date,bl.name as baranggay_lgu,if(bl.type=1,'Barangay','LGU') as group_,DATE_FORMAT(due_date,'%m%Y') as month_year,DATE_FORMAT(rs.date,'%M %d, %Y') as statement_date,DATE_FORMAT(due_date,'%M, %Y') as month_due,remarks,total_amount,bl.treasurer,if(bl.type=1,'Brgy.','LGU') as group_shortcut,bl.type as type_code"))
        ->leftJoin('baranggay_lgu as bl','bl.id_baranggay_lgu','rs.id_baranggay_lgu')
        ->where('rs.id_repayment_statement',$id_repayment_statement)
        ->first();   

        // dd($data);

        $loans = $this->StatementLoans($id_repayment_statement);
        $g = new GroupArrayController();
        $data['loans'] = $g->array_group_by($loans,['id_member']);

        $view = ($type=="generated")?'print':'print_payment';


        $data['file_name'] = "TEST";

        $html =  view('repayment-statement.'.$view,$data);

        // return $html;
        $pdf = PDF::loadHtml($html);
        $pdf->setOption("encoding","UTF-8");
        $pdf->setOption('margin-bottom', '0.33in');
        $pdf->setOption('margin-top', '0.33in');
        $pdf->setOption('margin-right', '0.33in');
        $pdf->setOption('margin-left', '0.42in');
        $pdf->setOption('header-left', 'Page [page] of [toPage]');
        $pdf->setOption('header-right', 'No.: '.$data['details']->month_year.'-'.$data['details']->id_repayment_statement);
    
        $pdf->setOption('header-font-size', 8);
        $pdf->setOption('header-font-name', 'Calibri');

        return $pdf->stream("Manager Certification {$data['file_name']}.pdf",array('Attachment'=>1));
    }

    public function postStatus(Request $request){
        $id_repayment_statement = $request->id_repayment_statement ?? 0;
        $status = $request->status ?? 10;
        $reason = $request->reason;

        $details = DB::table('repayment_statement')
                   ->where('id_repayment_statement',$id_repayment_statement)
                   ->first();

      

        if($details->status == 10){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Invalid Request";

            return response($data);
        }

        if($details->status == 2){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Loan Payment is already remitted";

            return response($data);
        }


        DB::table('repayment_statement')
        ->where('id_repayment_statement',$id_repayment_statement)
        ->update(['status'=>10,
                  'status_date'=>DB::raw("now()"),
                  'id_user_status'=>MySession::myId(),
                  'status_remarks'=>$reason]);

        $data['RESPONSE_CODE'] = "SUCCESS";
        $data['message'] = "Status Successfully Updated";





        if($details->status == 1){
            $RepaymentBulk = new BulkRepaymentController();

            $requestObj = [
                'id_repayment'=>$details->id_repayment,
                'cancel_reason'=>$reason
            ];

            $sync_out = $RepaymentBulk->updateStatus(new Request($requestObj));            
        }


        return response($data);
    }
}
