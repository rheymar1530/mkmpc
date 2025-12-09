<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\MySession;
use App\WebHelper;
use PDF;
use App\Exports\LoanDeliquentExport;
use Excel;
use Carbon\Carbon;

class LoanDeliquentController extends Controller
{
    public function index(Request $request){

        $data = $this->parseData($request);

        $data['exportMode'] = 0;
        $data['sidebar']= "sidebar-collapse";

      
        return view('loan-deliquent.index',$data);

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
        $data['head_title'] = "Loan Delinquent";
        $data['date'] = "As of ".date("m/d/Y", strtotime($data['dt']));

        $data['asOf'] = date("F d, Y", strtotime($data['dt']));

        if($type == "pdf"){
            $data['exportMode'] = 1;
            $html =  view('loan-deliquent.pdf',$data);

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
     
            return Excel::download(new LoanDeliquentExport($data), "{$data['head_title']} {$d}.xlsx");
        }
    }

    public function parseOverDue($date,$type){


        $query_date = WebHelper::ConvertDatePeriod($date);
        $start_date = date("Y-m-01",strtotime("$query_date"));

        $param = [
            'end1' => $query_date,
            'end2' => $query_date,
            'end3' => $query_date
        ];

        $order = ($type == 1)?"member":(($type==2?"loan_service ASC,member ASC":"ordering_ ASC")); 


        $lsType = ($type==1)?"concat(getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms))":"ls.name";


        $overdues = DB::select("SELECT loans.*,principal_due+interest_due as total_due, (principal_due+interest_due)-total_payment as balance,
        FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member,
        $lsType as loan_service FROM (
        SELECT loan.id_member,loan.loan_token,loan.id_loan,SUM(repayment_amount) as principal_due,SUM(interest_amount) as interest_due,date_released,
        min(lt.due_date) as start_period,maturity_date,max(lt.due_date) as current_month,COUNT(*) as elapsed_month,getLoanTotalPaymentAsOfWithDue(loan.id_loan,LAST_DAY(:end1)) as total_payment,loan.principal_amount,
         if(loan.id_baranggay_lgu is null,'Regular',concat(if(bl.type=1,'Brgy. ','LGU - '),bl.name)) as dataGroupings,
            if(loan.id_baranggay_lgu is null,3,bl.type) as ordering_
        FROM loan
        LEFT JOIN loan_table as lt on lt.id_loan = loan.id_loan
        LEFT JOIN baranggay_lgu as bl on bl.id_baranggay_lgu = loan.id_baranggay_lgu
        WHERE loan_status = 1 AND loan.status <> 10
        AND loan.maturity_date > :end2 AND lt.due_date <= :end3
        GROUP BY loan.id_loan) as loans
        LEFT JOIN loan on loan.id_loan = loans.id_loan
        LEFT JOIN member as m on m.id_member = loan.id_member
        LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
        WHERE (principal_due+interest_due)-total_payment > 0
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
