<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Excel;
use Carbon\Carbon;
use App\MySession;
use App\WebHelper;
use PDF;
use App\Exports\LoanPaymentExport;


class RepaymentReportController extends Controller
{
    public function index(Request $request){

        $data = array();
        $d = $this->parseData($request);

        $data['exportMode'] = 0;

        $data = $data + $d;
        $data['sidebar']= "sidebar-collapse";
       

        return view('repayment-report.index',$data);
        dd($data);

        dd($d);
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


        // dd($data);

        $month_start = date("Y-m-01", strtotime($date));
        $month_end = date("Y-m-t", strtotime($date));

        $param = array();
        for($i=1;$i<7;$i++){
            $param["month_start{$i}"] = $month_start;
            if($i <= 5){
                $param["month_end{$i}"] = $month_end;
            }
        }

        // dd($param);
        // unset($param['month_end2']);
        $repayment_data = DB::select("
        WITH current_payment as (
          -- SELECT rl.id_loan,SUM(rl.paid_principal+rl.paid_interest+rl.paid_fees) as payment
          -- FROM repayment_transaction as rt
          -- LEFT JOIN repayment_loans as rl on rl.id_repayment_transaction = rt.id_repayment_transaction AND rl.status <> 10
          -- WHERE rt.transaction_date >= :month_start AND rt.transaction_date <= :month_end 
          -- AND rt.status <> 10 AND (rt.id_cash_receipt_voucher + rt.id_journal_voucher) > 0
          -- GROUP BY rl.id_loan
            SELECT id_loan,SUM(payment) as payment FROM (
            SELECT rl.id_loan,SUM(rl.paid_principal+rl.paid_interest+rl.paid_fees) as payment
            FROM repayment_transaction as rt
            LEFT JOIN repayment_loans as rl on rl.id_repayment_transaction = rt.id_repayment_transaction AND rl.status <> 10
            WHERE rt.transaction_date >= :month_start1 AND rt.transaction_date <= :month_end1
            AND rt.status <> 10 AND (rt.id_cash_receipt_voucher + rt.id_journal_voucher) > 0
            GROUP BY rl.id_loan
            UNION ALL
            SELECT id_loan,0 as payment FROM loan
            WHERE loan_status = 1 
             -- AND loan.date_released <= :month_end2
            ) as l
            GROUP BY id_loan
        ),
        previous_payment as (
          SELECT  rl.id_loan,
          SUM(CASE WHEN lt.due_date < :month_start2 THEN rl.paid_principal+rl.paid_interest+rl.paid_fees ELSE 0 END) as prev_due_payment,
          SUM(CASE WHEN lt.due_date >= :month_start3 THEN rl.paid_principal+rl.paid_interest+rl.paid_fees ELSE 0 END) as current_due_payment,
          ifnull(SUM(rl.paid_principal+rl.paid_interest+rl.paid_fees),0) as total_prev_payment 
          FROM repayment_loans as rl
          LEFT JOIN loan_table as lt on lt.id_loan = rl.id_loan AND lt.term_code = rl.term_code
          LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rl.id_repayment_transaction
          WHERE rl.id_loan in (SELECT id_loan FROM current_payment) 
          AND rl.status <> 10 and rt.status <> 10 AND rt.transaction_date < :month_start4
          AND (rt.id_cash_receipt_voucher + rt.id_journal_voucher) > 0  AND lt.due_date <= :month_end3
          GROUP BY rl.id_loan
        ),
        loan_dues as (
            SELECT lt.id_loan,
            SUM(CASE WHEN lt.due_date < :month_start5 THEN repayment_amount+interest_amount+fees ELSE 0 END) as prev_due,
            SUM(CASE WHEN lt.due_date >= :month_start6 AND lt.due_date <= :month_end4 THEN ifnull(repayment_amount+interest_amount+fees,0) ELSE 0 END) as current_due,
            SUM(CASE WHEN lt.due_date > :month_end5 THEN repayment_amount+interest_amount+fees ELSE 0 END) as next_dues,
            SUM(repayment_amount+interest_amount+fees) as total_balance
            FROM loan_table as lt
            WHERE lt.id_loan in (SELECT id_loan FROM current_payment)
            GROUP BY lt.id_loan
        )
        SELECT ls.name as service_name,loan.terms,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member_name,
        DATE_FORMAT(MIN(lt.due_date),'%m/%d/%Y') as date_released,DATE_FORMAT(loan.maturity_date,'%m/%d/%Y') as maturity_date,loan.loan_token,ld.id_loan,
        prev_due,ifnull(pp.prev_due_payment,0) as prev_payment,prev_due-ifnull(pp.prev_due_payment,0) as prev_due_balance,
        current_due,ifnull(pp.current_due_payment,0) as current_offset,current_due-ifnull(pp.current_due_payment,0) as cur_due_balance,cp.payment,
        if(loan.loan_status = 2,'Closed','Active') as loan_status,
            if(loan.id_baranggay_lgu is null,'Regular',concat(if(bl.type=1,'Brgy. ','LGU - '),bl.name)) as dataGroupings,
                if(loan.id_baranggay_lgu is null,3,bl.type) as ordering_
        FROM loan_dues as ld
        LEFT JOIN loan on loan.id_loan = ld.id_loan
        LEFT JOIN previous_payment as pp on pp.id_loan = ld.id_loan
        LEFT JOIN current_payment as cp on cp.id_loan = ld.id_loan
        LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
        LEFT JOIN member as m on m.id_member = loan.id_member
        LEFT JOIN baranggay_lgu as bl on bl.id_baranggay_lgu = loan.id_baranggay_lgu
        LEFT JOIN loan_table as lt on lt.id_loan = ld.id_loan
        WHERE loan.id_loan_payment_type = 1 OR (loan.id_loan_payment_type = 2 AND loan.maturity_date <= :month_end2)
        GROUP BY ld.id_loan
        ORDER BY ordering_,dataGroupings,member_name;",$param);

            
        $g = new GroupArrayController();

        $data['repayment_data'] = $g->array_group_by($repayment_data,['dataGroupings']);
        $data['asOf'] = date("F Y", strtotime($data['dt']));

        // dd($data);

        return $data;


    }

    public function export($type,Request $request){
        $data = $this->parseData($request);
        $data['head_title'] = "Loan Payment Report";
        $data['date'] = "As of ".date("m/d/Y", strtotime($data['dt']));
        $data['asOf'] = date("F Y", strtotime($data['dt']));

        if($type == "pdf"){
            $data['exportMode'] = 1;
            $html =  view('repayment-report.pdf',$data);

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
            // $d = $data['date']= str_replace("/","_",$data['date']);
            $d = $data['asOf'];

            
     
            return Excel::download(new LoanPaymentExport($data), "{$data['head_title']} {$d}.xlsx");
            // return view('loan-overdue.excel',$data);
        }
    }
}
