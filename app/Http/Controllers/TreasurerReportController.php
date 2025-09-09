<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\MySession;
use Carbon\Carbon;
use PDF;
use Excel;
use App\Exports\TreasurerReportExport;

class TreasurerReportController extends Controller
{
    public function CatCode(){
        return [
            'LP'=>'Loan Payments',
            'PR'=>'Payments from Renewal of Loans',
            'PLT'=>"Payments from Loan Transactions",
            'CRT'=>"Cash Receipts Transactions",
            "LR"=>"Loan Released",
            "EX"=>"Expenses",
            "OD"=>"Other Disbursement"
        ];
    }
    public function index(Request $request){
 
        $data = $this->RPTParams($request);

        

        // dd($data);
        return view('treasurer_report.index',$data);
        dd($data);
    }
    public function export($type,Request $request){
        $data = $this->RPTParams($request);

        $data['export_type'] = $type=="excel"?2:1;



        if($type == 'excel'){
            // dd("Export to Excel");
            return Excel::download(new TreasurerReportExport($data,1), $data['file_name'].".xlsx");
        }
        $html = view('treasurer_report.pdf',$data);
        $pdf = PDF::loadHtml($html);
        $pdf->setOption("encoding","UTF-8");

        $pdf->setOption('margin-bottom', '0.35in');
        $pdf->setOption('margin-top', '0.35in');
        $pdf->setOption('margin-right', '0.33in');
        $pdf->setOption('margin-left', '0.33in');
        $pdf->setOption('header-left', 'Page [page] of [toPage]');
    
        $pdf->setOption('header-font-size', 8);
        $pdf->setOption('header-font-name', 'Calibri');

        $pdf->setOption('page-width','215.9mm');
        $pdf->setOption('page-height','330.2mm');

        // $pdf->setPaper('Legal');


        return $pdf->stream($data['file_name']);



    }

    public function RPTParams($request){
        $data['current_year'] = MySession::current_year();
        $data['current_month'] = MySession::current_month();

        $data['selected_year']=$year = $request->year ?? $data['current_year'];
        $data['selected_month']=$month= $request->month ?? $data['current_month'];
        $data['selected_type'] = $request->type ?? 1;

        $data['selected_day'] = $request->date ?? MySession::current_date();

        $data['year_start'] = 2025;

        $date = date("Y-m-t", strtotime("{$data['selected_year']}-{$data['selected_month']}-01"));


        if($data['selected_type'] == 1){
            if($data['current_month']  == $data['selected_month'] && $data['current_year']  == $data['selected_year']){
                $currentAsOf = MySession::current_date();
            }else{
                $currentAsOf = "{$data['selected_year']}-{$data['selected_month']}-01";
                $currentAsOf = date("Y-m-t", strtotime($currentAsOf));
            }
            $data['currentAsOf'] =date("F d, Y", strtotime($currentAsOf)); 


            // monthly
            $monthStart = date('Y-m-01',strtotime("{$data['selected_year']}-{$data['selected_month']}-01"));
            
            $dtParam = [
                'startDate' => $monthStart,
                'endDate' => date("Y-m-t", strtotime($monthStart)),
                'prevDate'=>Carbon::parse($monthStart)->subDay()->toDateString()
            ];

            $data['DateDesc'] = date("F Y", strtotime($monthStart));
        }else{
            $data['DateDesc'] = $data['currentAsOf'] =date("F d, Y", strtotime($data['selected_day'])); 
            $dtParam = [
                'startDate' => $data['selected_day'],
                'endDate' => $data['selected_day'],
                'prevDate'=>Carbon::parse($data['selected_day'])->subDay()->toDateString()
            ];            
        }
        $data['report_output'] = $this->parseData($dtParam);
        $data['catCode'] = $this->CatCode();

        $typeDesc =($data['selected_type'] == 1)?"Monthly":"Daily";

        $data['RPTType'] = "Treasurer's Report ($typeDesc)";



 
        $data['file_name'] = "Treasurers Report ($typeDesc) ".$data['DateDesc'];
        return $data;

    }
public function parseData($dtParam){
        // Collections

        $startDate = $dtParam['startDate'];
        $endDate = $dtParam['endDate'];

        $g = new GroupArrayController();

        $loan_payments = DB::select("SELECT ls.name as description,SUM(payment) as amount FROM (
        SELECT rl.id_loan,SUM(paid_principal) as payment FROM repayment_transaction as rt
        LEFT JOIN repayment_loans as rl on rl.id_repayment_transaction = rt.id_repayment_transaction
        WHERE (rt.id_cash_receipt_voucher + rt.id_journal_voucher) > 0 AND rt.status <> 10 AND rl.status <> 10
        AND rt.transaction_date >= ? AND rt.transaction_date <= ?
        GROUP BY rl.id_loan) as payments
        LEFT JOIN loan on loan.id_loan = payments.id_loan
        LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
        GROUP BY loan.id_loan_service
        ORDER BY description;",[$startDate,$endDate]);

        // pb.previous_principal+pb.previous_interest-loan.prev_loan_rebates
        // +p_interest+p_fees-rebates

        $renewal = DB::select("SELECT ls.name as description,SUM(renewal.amount) as amount FROM (
        SELECT loan.id_loan_service,SUM(pb.previous_principal) as amount
        FROM loan
        LEFT JOIN paid_previous_balance as pb on loan.id_loan = pb.id_loan_current
        WHERE loan.date_released >= ? AND loan.date_released <= ? AND loan.loan_status > 0 
        AND pb.id_loan_current is not null
        GROUP BY loan.id_loan_service
        UNION ALL
        SELECT loan2.id_loan_service,SUM(p_principal) as amount FROM loan
        LEFT JOIN loan_offset as lo on lo.id_loan = loan.id_loan
        LEFT JOIN loan as loan2 on loan2.id_loan = lo.id_loan_to_pay
        WHERE loan.date_released >= ? AND loan.date_released <= ?  AND loan.loan_status > 0 AND lo.id_loan is not null
        GROUP BY loan2.id_loan_service) as renewal
        LEFT JOIN loan_service as ls on ls.id_loan_service = renewal.id_loan_service
        GROUP BY renewal.id_loan_service
        ORDER BY description;",[$startDate,$endDate,$startDate,$endDate]);

        // $payments_from_loan_transaction = DB::select("SELECT ca.description,SUM(calculated_charge) as amount FROM loan
        // LEFT JOIN loan_charges as lc on lc.id_loan = loan.id_loan
        // LEFT JOIN loan_fees as lf on lf.id_loan_fees = lc.id_loan_fees
        // LEFT JOIN chart_account as ca on ca.id_chart_account = lf.id_chart_account
        // WHERE loan.date_released >= ? AND loan_status > 0
        // GROUP BY lf.id_chart_account 
        // ORDER BY description;",[$endDate]);

        $payments_from_loan_transaction = DB::select("
        SELECT t.description,SUM(amount) as amount FROM (
            SELECT ca.id_chart_account,ca.description,SUM(credit) as amount FROM cash_disbursement as cd
            LEFT JOIN cash_disbursement_details as cdd on cdd.id_cash_disbursement = cd.id_cash_disbursement
            LEFT JOIN chart_account as ca on ca.id_chart_account = cdd.id_chart_account
            WHERE cd.status <> 10 AND cd.type = 1 AND cd.date >= ? AND cd.date <= ?  AND credit > 0 AND ca.id_chart_account_category not in (1,2) AND cdd.id_chart_account not in (5)
            GROUP BY cdd.id_chart_account
            UNION ALL
            SELECT ca.id_chart_account,ca.description,SUM(credit) as amount FROM cash_receipt_voucher as crv
            LEFT JOIN cash_receipt_voucher_details as crvd on crvd.id_cash_receipt_voucher = crv.id_cash_receipt_voucher
            LEFT JOIN chart_account as ca on ca.id_chart_account = crvd.id_chart_account
            WHERE crv.status <> 10 AND crv.type = 2 AND crv.date >= ?  AND crv.date <= ? AND credit > 0 AND ca.id_chart_account_category not in (1,2) AND crvd.id_chart_account not in (5)
            GROUP BY crvd.id_chart_account
            UNION ALL
            SELECT ca.id_chart_account,ca.description,SUM(calculated_charge) as amount FROM loan
            LEFT JOIN loan_charges as lc on lc.id_loan = loan.id_loan
            LEFT JOIN loan_fees as lf on lf.id_loan_fees = lc.id_loan_fees
            LEFT JOIN chart_account as ca on ca.id_chart_account = lf.id_chart_account
            WHERE loan.date_released >= ? AND loan.date_released <= ? AND loan_status > 0 AND  lc.id_loan_fees = 15
            GROUP BY lf.id_chart_account 
            UNION ALL
            SELECT ca.id_chart_account,ca.description,SUM(credit) as amount
            FROM cash_receipt_voucher as crv
            LEFT JOIN cash_receipt_voucher_details as crvd on crvd.id_cash_receipt_voucher = crv.id_cash_receipt_voucher
            LEFT JOIN chart_account as ca on ca.id_chart_account = crvd.id_chart_account
            WHERE crv.status <> 10 AND crv.date >= ? AND crv.date <= ? AND crvd.id_chart_account = 23
            GROUP BY ca.id_chart_account 
        ) as t
        GROUP BY t.id_chart_account
        ORDER BY t.description;",[$startDate,$endDate,$startDate,$endDate,$startDate,$endDate,$startDate,$endDate]);

        // $cash_receipt_transaction = DB::select("SELECT concat('[OR# ',or_no,']',ifnull(concat(' - ',remarks),'')) as remarks,id_cash_receipt_voucher,total_payment as amount
        // FROM cash_receipt as cr
        // WHERE cr.type = 1 AND cr.status <> 10 
        // AND cr.date_received >= ?;",[$endDate]);

        ////CASH RECEIPT FOR DEBUGGING
        $cash_receipt_transaction = DB::select("SELECT cr.id_cash_receipt_voucher,ca.description,SUM(crd.amount) as amount 
        FROM cash_receipt as cr
        LEFT JOIN cash_receipt_details as crd on crd.id_cash_receipt = cr.id_cash_receipt
        LEFT JOIN tbl_payment_type as pt on pt.id_payment_type = crd.id_payment_type
        LEFT JOIN chart_account as ca on ca.id_chart_account = pt.id_chart_account
        WHERE cr.type = 1 AND cr.status <> 10 AND cr.date_received >= ? AND cr.date_received <= ?
        GROUP BY ca.id_chart_account
        ORDER BY remarks;",[$startDate,$endDate]);

        //  Disbursement
        $loan_released = DB::select("SELECT ls.name as description,SUM(principal_amount) as amount,SUM(total_loan_proceeds) as amount2
        FROM loan
        LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
        WHERE loan.date_released >= ? AND loan.date_released <= ? AND loan_status > 0
        GROUP BY ls.id_loan_service
        ORDER BY description",[$startDate,$endDate]);
        
        $expenses = DB::select("SELECT cdv_type as description,SUM(debit) as amount,cat FROM (
        SELECT
            CASE 
            WHEN ca.id_chart_account_type = 5 THEN ca.description
            WHEN cd.type in (9,12) THEN ct.description
            ELSE 'Cash Disbursement Others' end as cdv_type
        ,CASE 
        WHEN ca.id_chart_account_type = 5 THEN 1
        WHEN cd.type in (9,12) THEN 2
        ELSE 3 end as data_order
        ,if(ca.id_chart_account_type = 5,'Expenses','Others') as cat,  debit-credit as debit
        FROM cash_disbursement as cd
        LEFT JOIN cash_disbursement_details as cdd on cdd.id_cash_disbursement = cd.id_cash_disbursement
        LEFT JOIN chart_account as ca on ca.id_chart_account = cdd.id_chart_account
        LEFT JOIN cdv_type as ct on ct.id_cdv_type = cd.type
        WHERE cd.date >= ? AND cd.date <= ? AND cd.status <> 10 AND cd.type <> 1 AND ca.id_chart_account_category not in (1,2)) as dis
        GROUP BY cdv_type
        ORDER BY data_order,cdv_type;",[$startDate,$endDate]);


        // $expenses = DB::select("SELECT ca.description,SUM(debit-credit) as amount,if(ca.id_chart_account_type=5,'Expenses','Others') as cat
        // FROM cash_disbursement as cdv
        // LEFT JOIN cash_disbursement_details as cdvd on cdvd.id_cash_disbursement = cdv.id_cash_disbursement
        // LEFT JOIN chart_account as ca on ca.id_chart_account = cdvd.id_chart_account
        // WHERE cdv.status <> 10 AND cdv.date >= ? AND cdv.date <= ? AND cdv.type not in (1)  AND ca.id_chart_account_category not in (1,2)
        // GROUP BY cdvd.id_chart_account
        // ORDER BY description;",[$startDate,$endDate]);

    //     $expenses = DB::select("WITH cdv as (
    //  SELECT ca.id_chart_account,
    //         CASE 
    //         WHEN ca.id_chart_account_type = 5 THEN ca.description
    //         WHEN cd.type in (9,12) THEN ct.description
    //         ELSE 'Cash Disbursement Others' end as cdv_type
    //     ,CASE 
    //     WHEN ca.id_chart_account_type = 5 THEN 1
    //     WHEN cd.type in (9,12) THEN 2
    //     ELSE 3 end as data_order
    //     ,if(ca.id_chart_account_type = 5,'Expenses','Others') as cat,  SUM(debit-credit) as amount
    //     FROM cash_disbursement as cd
    //     LEFT JOIN cash_disbursement_details as cdd on cdd.id_cash_disbursement = cd.id_cash_disbursement
    //     LEFT JOIN chart_account as ca on ca.id_chart_account = cdd.id_chart_account
    //     LEFT JOIN cdv_type as ct on ct.id_cdv_type = cd.type
    //     WHERE cd.date >= '2025-01-01' AND cd.date <= '2025-08-02' AND cd.status <> 10 AND cd.type <> 1 AND ca.id_chart_account_category not in (1,2)
    //     GROUP BY cdd.id_chart_account
    // )
    // SELECT cdv_type as description,amount+SUM(jv_amt) as amount,cat FROM (
    // SELECT cdv.cdv_type,data_order,cat,cdv.amount as amount,(if(jv.id_journal_voucher is not null,jvd.debit-jvd.credit,0)) as jv_amt FROM cdv
    // LEFT JOIN journal_voucher_details as jvd on jvd.id_chart_account = cdv.id_chart_account
    // LEFT JOIN journal_voucher as jv on jv.id_journal_voucher = jvd.id_journal_voucher AND jv.cash = 0 AND jv.status <> 10 AND jv.date >='2025-01-01' AND jv.date <= '2025-08-02') as dis
    // GROUP BY cdv_type;");    


        $expExpenses = $g->array_group_by($expenses,['cat']);

        $adjustments = DB::select("SELECT cat.name as description,SUM(debit-credit) as amount 
        FROM journal_voucher as jv
        LEFT JOIN journal_voucher_details as jvd on jvd.id_journal_voucher = jv.id_journal_voucher
        LEFT JOIN chart_account as ca on ca.id_chart_account = jvd.id_chart_account
        LEFT JOIN chart_account_category as cat on cat.id_chart_account_category = ca.id_chart_account_category
        WHERE jv.status <> 10 AND jv.date >= ? AND jv.date <= ? AND ca.id_chart_account_category in (1,2)
        GROUP BY ca.id_chart_account_category;",[$startDate,$endDate]);

        $adjustmentsAccount = DB::select("SELECT ca.description,SUM(debit-credit) as amount,normal
                                        FROM journal_voucher as jv
                                        LEFT JOIN journal_voucher_details as jvd on jvd.id_journal_voucher = jv.id_journal_voucher
                                        LEFT JOIN chart_account as ca on ca.id_chart_account = jvd.id_chart_account
                                        WHERE jv.status <> 10 AND jv.date >= ? AND jv.date <= ? AND jv.cash = 0
                                        GROUP BY ca.id_chart_account;",[$startDate,$endDate]);


        $output = [];
        $output['entries'] = [
            'Collection'=>[
                'LP'=>$loan_payments,
                'PR'=>$renewal,
                'PLT'=>$payments_from_loan_transaction,
                'CRT'=>$cash_receipt_transaction
            ],
            'Disbursement'=>[
                'LR'=>$loan_released,
                "EX"=>$expExpenses['Expenses'] ?? [],
                "OD"=>$expExpenses['Others'] ?? [],
            ],
        ];

        // $changePayables = DB::select("SELECT ca.description,SUM(credit) as amount
        // FROM cash_receipt_voucher as crv
        // LEFT JOIN cash_receipt_voucher_details as crvd on crvd.id_cash_receipt_voucher = crv.id_cash_receipt_voucher
        // LEFT JOIN chart_account as ca on ca.id_chart_account = crvd.id_chart_account
        // WHERE crv.status <> 10 AND crv.date >= ? AND crv.date <= ? AND crvd.id_chart_account = 23;",[$startDate,$endDate])[0] ?? [];


        // $output['changePayables'] = $changePayables->amount ?? 0;



        $output['adjustments'] = $adjustments;
        $output['adjustmentsAccount']=$adjustmentsAccount;

        $csh =$this->PrevCash($startDate);
        $output['Cash'] = $csh['cash'];
        $output['TotalCash'] = $csh['total'];
        $output['Checks'] = $this->CheckLess($startDate,$endDate);

        $asOf = Carbon::parse($startDate)->subDay()->toDateString();

        $output['asOf'] = date("F d, Y", strtotime($asOf));



        return $output;
    }

    public function PrevCash($startDate){

        $cash = DB::select("SELECT cat.name as category,
        if(cash.id_chart_account_category =1,cash.description,tb.bank_name) as description,
        SUM(debit-credit) as amount FROM (
        SELECT ca.id_chart_account_category,jvd.id_chart_account,ca.account_code,ca.description,jvd.debit,jvd.credit 
        FROM journal_voucher as jv
        LEFT JOIN journal_voucher_details as jvd on jvd.id_journal_voucher = jv.id_journal_voucher
        LEFT JOIN chart_account as ca on ca.id_chart_account = jvd.id_chart_account
        WHERE jv.status <> 10 AND jv.date < ? AND ca.id_chart_account_category in (1,2)
        UNION ALL
        SELECT ca.id_chart_account_category,crvd.id_chart_account,ca.account_code,ca.description,crvd.debit,crvd.credit 
        FROM cash_receipt_voucher as crv
        LEFT JOIN cash_receipt_voucher_details as crvd on crvd.id_cash_receipt_voucher = crv.id_cash_receipt_voucher
        LEFT JOIN chart_account as ca on ca.id_chart_account = crvd.id_chart_account
        WHERE crv.status <> 10 AND crv.date < ? AND ca.id_chart_account_category in (1,2)
        UNION ALL
        SELECT ca.id_chart_account_category,cdvd.id_chart_account,ca.account_code,ca.description,cdvd.debit,cdvd.credit 
        FROM cash_disbursement as cdv
        LEFT JOIN cash_disbursement_details as cdvd on cdvd.id_cash_disbursement = cdv.id_cash_disbursement
        LEFT JOIN chart_account as ca on ca.id_chart_account = cdvd.id_chart_account
        WHERE cdv.status <> 10 AND cdv.date < ? AND ca.id_chart_account_category in (1,2)
        UNION ALL
        SELECT ca.id_chart_account_category,cb.id_chart_account,ca.account_code,ca.description,cb.debit,cb.credit 
        FROM chart_beginning as cb
        LEFT JOIN chart_account as ca on ca.id_chart_account = cb.id_chart_account
        WHERE cb.status <> 10 AND cb.date < ? AND ca.id_chart_account_category in (1,2)) as cash
        LEFT JOIN chart_account_category as cat on cat.id_chart_account_category = cash.id_chart_account_category
        LEFT JOIN tbl_bank as tb on tb.id_chart_account = cash.id_chart_account
        GROUP BY cash.id_chart_account
        HAVING SUM(debit-credit) <> 0
        ORDER BY cash.id_chart_account_category
        ;",[$startDate,$startDate,$startDate,$startDate]);

        $g = new GroupArrayController();

        $totalCash = collect($cash)->sum('amount');
        $cash = $g->array_group_by($cash,['category']);

        return [
            'total'=>$totalCash,
            'cash'=>$cash
        ];

        return $cash;
    }

    public function CheckLess($startDate,$endDate){

        $checks = DB::select("SELECT description,SUM(debit-credit) as amount FROM (
        SELECT jvd.id_chart_account,jvd.description,jvd.debit,jvd.credit 
        FROM journal_voucher as jv
        LEFT JOIN journal_voucher_details as jvd on jvd.id_journal_voucher = jv.id_journal_voucher
        WHERE jv.status <> 10  AND jvd.id_chart_account = 81 AND jv.date >= ? AND jv.date <= ?
        UNION ALL
        SELECT cvd.id_chart_account,cvd.description,cvd.debit,cvd.credit 
        FROM cash_disbursement as cv
        LEFT JOIN cash_disbursement_details as cvd on cvd.id_cash_disbursement = cv.id_cash_disbursement
        WHERE cv.status <> 10  AND cvd.id_chart_account = 81 AND cv.date >= ? AND cv.date <= ?
        UNION ALL
        SELECT crvd.id_chart_account,crvd.description,crvd.debit,crvd.credit 
        FROM cash_receipt_voucher as crv
        LEFT JOIN cash_receipt_voucher_details as crvd on crvd.id_cash_receipt_voucher = crv.id_cash_receipt_voucher
        WHERE crv.status <> 10  AND crvd.id_chart_account = 81 AND crv.date >= ? AND crv.date <= ?
        UNION ALL
        SELECT cb.id_chart_account,ca.description,cb.debit,cb.credit 
        FROM chart_beginning as cb
        LEFT JOIN chart_account as ca on ca.id_chart_account = cb.id_chart_account
        WHERE cb.status <> 10  AND cb.id_chart_account = 81 AND date >= ? AND date <= ?
        ) as checks;",[$startDate,$endDate,$startDate,$endDate,$startDate,$endDate,$startDate,$endDate]);

        return $checks;
    }
}
