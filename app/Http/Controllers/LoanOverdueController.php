<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\MySession;
use App\WebHelper;
use PDF;
use App\Exports\LoanOverdueExport;
use Excel;
use Carbon\Carbon;

class LoanOverdueController extends Controller
{
    public function index(Request $request){

        $data = $this->parseData($request);

        $data['exportMode'] = 0;
        $data['sidebar']= "sidebar-collapse";

        // dd($data);
        return view('loan-overdue.index',$data);

        dd($data);
    }

    public function parseData(Request $request){
        $current_month = date("n", strtotime(MySession::current_date()));
        $current_year = date("Y", strtotime(MySession::current_date()));
        $data = array();

        $data['currentYear'] = $current_year;
        $data['selected_year']=$year = $request->year ?? $current_year;
        $data['selected_month']=$month= $request->month ?? $current_month;


        if($current_month == $data['selected_month'] && $current_year == $data['selected_year']){
            $date = MySession::current_date();
        }else{
            $date = "{$data['selected_year']}-{$data['selected_month']}-01";
            $date = date("Y-m-t", strtotime($date));
        }
       
        // $data['dt'] = WebHelper::ConvertDatePeriod($date);
        $data['dt'] = $date;

        // $date = Carbon::parse($date); // your variable
        // $date = $date->copy()->subMonthNoOverflow()->format('Y-m-t');
        // dd($date);
   
        $data['type'] = $type = $request->type ?? 1;

        // $date = MySession::current_date();

        $overdues = $this->parseOverDue($date,$type);


        $data['overdues'] = $overdues;     

        $data['asOf'] = date("F d, Y", strtotime($data['dt']));

        // dd($data);

        return $data;  
    }

    public function export($type,Request $request){
        $data = $this->parseData($request);
        $data['head_title'] = "Loan Overdue";
        $data['date'] = "As of ".date("m/d/Y", strtotime($data['dt']));
        $data['asOf'] = date("F d, Y", strtotime($data['dt']));

        if($type == "pdf"){
            $data['exportMode'] = 1;
            $html =  view('loan-overdue.pdf',$data);

            // return $html;
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
        }elseif($type == 'excel'){
            $data['exportMode'] = 2;
            $d = $data['date']= str_replace("/","_",$data['date']);
     
            return Excel::download(new LoanOverdueExport($data), "{$data['head_title']} {$d}.xlsx");
            // return view('loan-overdue.excel',$data);
        }
    }

    public function parseOverDue($date,$type){
        // $query_date = WebHelper::ConvertDatePeriod($date);
        $query_date = $date;
        $start_date = date("Y-m-01",strtotime("$query_date"));



        // $param = [
        //     'st1' => $start_date,
        //     'end1' => $query_date,
        //     'end2' => $query_date,
        //     'end3' => $query_date,
        //     'end4' => $query_date
        // ];
        // // dd($param);
        // $overdues = DB::select("SELECT loans.*,@current_payment:=getLoanTotalPaymentMonth(loans.id_loan,:st1,:end1) as current_payment,principal_balance+interest_balance as total_due,ROUND(@current_payment+(principal_balance+interest_balance),2) as month_total_due FROM (
        //     SELECT loan.id_member,loan.id_loan,concat(getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms)) as 'loan_name',MAX(due_date) as as_of,getPrincipalBalanceAsOf(loan.id_loan,:end2) as principal_balance,getInterestBalanceAsOf(loan.id_loan,:end3) as interest_balance,UPPER(FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)) as member_name,loan.loan_token,m.email
        //     FROM loan
        //     LEFT JOIN loan_table as lt on lt.id_loan = loan.id_loan
        //     LEFT JOIN member as m on m.id_member = loan.id_member
        //     LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
        //     WHERE lt.due_date <= :end4 AND loan.loan_status = 1
        //     GROUP BY loan.id_loan) as loans
        // WHERE (loans.principal_balance+interest_balance) > 0 
        // GROUP BY loans.id_loan
        // ORDER BY member_name,loans.id_loan;",$param);

        // $g = new GroupArrayController();

        // $overdues = $g->array_group_by($overdues,['id_member']);

        // return $overdues;
        // $type = 2;

        $param = [
            'end1' => $query_date,
            // 'end2' => $query_date,
            'end3' => $query_date
        ];
        // AND lt.due_date <= :end2




        $order = ($type == 1)?"member":(($type==2?"loan_service ASC,member ASC":"ordering_ ASC"));


        $lsType = "ls.name";
        // $lsType = ($type==1)?"concat(getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms))":"ls.name";

        $overdues = DB::select("SELECT loans.*,loans.principal_amount+interest_total+surcharge_total-payment as balance,loans.principal_amount+interest_total+surcharge_total as total,
        FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member,
        $lsType as 'loan_service'
        FROM (
        SELECT loan.id_member,loan.id_loan,loan.date_released,maturity_date,loan.principal_amount,loan.terms as month,
        if(loan.id_loan_payment_type=1,lt.interest_amount,loan.interest_show) as interest_amount,ifnull(SUM(interest_amount),0) as interest_total,ifnull(SUM(surcharge),0) as surcharge_total,
        getLoanTotalPaymentAsOf(loan.id_loan,:end1) as payment,loan.loan_token,MIN(lt.due_date) as start_period,
         if(loan.id_baranggay_lgu is null,'Regular',concat(if(bl.type=1,'Brgy. ','LGU - '),bl.name)) as dataGroupings,
            if(loan.id_baranggay_lgu is null,3,bl.type) as ordering_
        FROM loan
        LEFT JOIN loan_table as lt on lt.id_loan = loan.id_loan 
        LEFT JOIN baranggay_lgu as bl on bl.id_baranggay_lgu = loan.id_baranggay_lgu
        WHERE loan.loan_status = 1 AND loan.maturity_date <= :end3  AND loan.id_loan_payment_type = 1


        GROUP BY loan.id_loan) as loans
        LEFT JOIN loan on loan.id_loan = loans.id_loan
        LEFT JOIN member as m on m.id_member = loan.id_member
        LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
        ORDER BY $order;",$param);



   


        $g = new GroupArrayController();

        if($type == 1){
            $overdues = $g->array_group_by($overdues,['id_member']);
        }elseif($type == 2){
            $overdues = $g->array_group_by($overdues,['loan_service']);
        }else{
            $overdues = $g->array_group_by($overdues,['dataGroupings']);
        }
        
        

        return $overdues;
        dd($overdues);

    }

}
