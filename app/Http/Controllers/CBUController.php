<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MySession;
use DB;
use App\Loan;
use App\Member;
use App\CredentialModel;
use DateTime;
use PDF;

use Excel;
use App\Exports\CBUExport;

class CBUController extends Controller
{
    public function index(Request $request){    
        $data['isAdmin'] = MySession::isAdmin();
        $data['id_member'] = MySession::myId();

        if($data['isAdmin']){
            $data['id_member'] = $request->id_member ?? $data['id_member'];
        }
        $data['head_title'] = "Capital Build-Up";
        $data['start_date'] = $request->start_date ??date_format(date_create(MySession::current_date()),"Y-m-01");
        $data['end_date'] = $request->end_date ?? date_format(date_create(MySession::current_date()),"Y-m-t");

        $data['cbu_ledger'] = DB::select("CALL getMemberCBULedger2(?,?,?);",[$data['id_member'],$data['start_date'],$data['end_date']]);

        $data['selected_member'] =DB::table('member as m')
        ->select(DB::raw("concat(membership_id,' || ',FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)) as member_name,id_member"))
        ->where('id_member',$data['id_member'])
        ->first();

        return view('cbu.index',$data);
    }

    public function CBUAccountExport(Request $request){
        $id_member_req = $request->id_member ?? MySession::myId();
        $data['id_member'] = (MySession::isAdmin())?$id_member_req:MySession::myId();
        $data['start_date'] = $request->start_date ??date_format(date_create(MySession::current_date()),"Y-m-01");
        $data['end_date'] = $request->end_date ?? date_format(date_create(MySession::current_date()),"Y-m-t");

        // dd($data);

        $data['file_name'] = DB::table('member')->select(DB::raw("concat('CBU - ',last_name,' ',first_name) as file_name"))->where('id_member',$data['id_member'])->first()->file_name ?? '';
        $data['cbu_ledger'] = DB::select("CALL getMemberCBULedger2(?,?,?);",[$data['id_member'],$data['start_date'],$data['end_date']]);        
 

        return Excel::download(new CBUExport($data['cbu_ledger'],1), $data['file_name'].".xlsx");


        return view('cbu.export_excel',$data);
    }

    public function CBUReportController(Request $request){

        $data['credential']= CredentialModel::GetCredential(MySession::myPrivilegeId());
        if(!$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }   
        $data['head_title'] = "CBU Report";
        $date=$data['date'] = $request->date ?? MySession::current_date();


        $data['cbu'] = $this->parseCBUReportData($date);



        return view("cbu.report",$data);
        return $data;
    }

    public function CBUReportExportExcel(Request $request){
        $date=$data['date'] = $request->date ?? MySession::current_date();
        $data['cbu'] = $this->parseCBUReportData($date);
        $data['file_name'] = "CBU ".date("F d, Y", strtotime($date));

        return Excel::download(new CBUExport($data['cbu'],2), $data['file_name'].".xlsx");
    }

    public function parseCBUReportData($date){
        return DB::select("SELECT m.id_member,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member,SUM(amount) as amount   FROM (
            SELECT ifnull(amount,0) as amount,id_member 
            FROM cash_receipt_details as cd
            LEFT JOIN cash_receipt as c on c.id_cash_receipt = cd.id_cash_receipt
            LEFT JOIN tbl_payment_type as pt on pt.id_payment_type = cd.id_payment_type
            WHERE pt.is_cbu = 1 AND pt.type in (1) AND status <> 10  and c.date_received <= ?
            UNION ALL
            SELECT ifnull(lc.calculated_charge,0) as amount,id_member from loan_charges as lc
            LEFT JOIN loan on loan.id_loan = lc.id_loan
            LEFT JOIN tbl_payment_type as pt on pt.reference = lc.id_loan_fees and pt.type = 2
            WHERE  pt.is_cbu = 1 AND (loan.status in (6) or loan.loan_status in (1)) and pt.type =2
            and loan.date_released <= ?
            UNION ALL
            SELECT ifnull(rf.amount,0),id_member FROM repayment_fees as rf
            LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rf.id_repayment_transaction
            LEFT JOIN tbl_payment_type as pt on pt.id_payment_type = rf.id_payment_type
            WHERE pt.is_cbu = 1 and pt.type =3 AND rt.status <> 10 AND rt.transaction_date <= ?
            UNION ALL
            SELECT ifnull(amount,0),id_member FROM cbu_beginning where date <= ?
            UNION ALL
            SELECT (cdd.debit*-1) as amount,cd.id_member FROM cash_disbursement as cd
            LEFT JOIN cash_disbursement_details as cdd on cdd.id_cash_disbursement = cd.id_cash_disbursement
            LEFT JOIN chart_account as ca on ca.id_chart_account = cdd.id_chart_account
            WHERE ca.iscbu = 1 and cd.status <> 10 AND cd.date <= ?
            UNION ALL
            SELECT (jvd.credit-jvd.debit) as amount,jv.id_member FROM journal_voucher as jv
            LEFT JOIN journal_voucher_details as jvd on jvd.id_journal_voucher = jv.id_journal_voucher
            LEFT JOIN chart_account as ca on ca.id_chart_account = jvd.id_chart_account
            WHERE ca.iscbu = 1 and jv.status <> 10 AND jv.date <= ? AND jv.type = 1
            ) as CBU
        LEFT JOIN member as m on m.id_member = CBU.id_member
        GROUP BY CBU.id_member
        ORDER BY member;",[$date,$date,$date,$date,$date,$date]);
    }

    public function CBUMonthlyIndex(Request $request){

        $credential= CredentialModel::GetCredential(MySession::myPrivilegeId());
        if(!$credential->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }

        date_default_timezone_set('Asia/Manila');
        $dt = new DateTime();
        $year = $request->year ?? $dt->format('Y');


        $m = ($year == $dt->format('Y'))?MySession::current_month():12;

        $data = $this->parseCBUMonthly(1,$m,$year);
        $data['head_title'] = "Monthly CBU";
        $data['credential'] = $credential;

        return view('reports.cbu_monthly',$data);
    }

    public function CBUMonthlyexport(Request $request){
        date_default_timezone_set('Asia/Manila');
        $dt = new DateTime();
        $year = $request->year ?? $dt->format('Y');
        $m = ($year == $dt->format('Y'))?MySession::current_month():12;

        $data = $this->parseCBUMonthly(1,$m,$year);
        $html =  view('reports.cbu_export',$data);

        // return $html;
        $pdf = PDF::loadHtml($html);
        $pdf->setOption("encoding","UTF-8");
        $pdf->setOption('margin-bottom', '0.7480in');
        $pdf->setOption('margin-top', '0.7480in');
        $pdf->setOption('margin-right', '0.33in');
        $pdf->setOption('margin-left', '0.42in');
        $pdf->setOption('header-left', 'Page [page] of [toPage]');
    
        $pdf->setOption('header-font-size', 8);
        $pdf->setOption('header-font-name', 'Calibri');
        $pdf->setOrientation('landscape');


        return $pdf->stream();




        return $data;
    }
    public function CBUMonthlyexportExcel(Request $request){
        date_default_timezone_set('Asia/Manila');
        $dt = new DateTime();
        $year = $request->year ?? $dt->format('Y');
        $m = ($year == $dt->format('Y'))?MySession::current_month():12;     

        // dd((int)$m);
        $data = $this->parseCBUMonthly(1,$m,$year);
        $data['file_name'] = "CBU YEAR $year";
        return Excel::download(new CBUExport($data['cbus'],3,(int)$m), $data['file_name'].".xlsx");


    }
    public function parseCBUMonthly($start_month,$end_month,$year,$dashboard = false){
        $data['selected_year'] = $year;
        $data['end_month'] = $end_month;
        $month_query= "";
        $month_query_ar = array();       
        $period_start = date("Y-m-01", strtotime("$year-$start_month-01"));
        array_push($month_query_ar,"SUM(CASE WHEN transaction_date < '$period_start' THEN amount ELSE 0 END) as '".($year-1)."'");

        for($i=$start_month;$i<=$end_month;$i++){
            $dt_s = date("Y-m-01", strtotime("$year-$i-01"));
            $dt_e = date("Y-m-t", strtotime($dt_s));
            $month_text = date("M", strtotime($dt_s));
            if(!$dashboard){
                $q = "SUM(CASE WHEN transaction_date >= '$dt_s' AND transaction_date <= '$dt_e' THEN amount ELSE 0 END) as '$month_text'";
            }else{
                $q = "SUM(CASE WHEN transaction_date <= '$dt_e' THEN amount ELSE 0 END) as '$month_text'";
            }
            
            
            array_push($month_query_ar,$q);
        }

        $data['title_range'] = date("F t, Y", strtotime("$year-$end_month-01"));

        $dt_query_end = date("Y-m-t", strtotime("$year-$end_month-01"));
        $month_query = implode(",",$month_query_ar);
        $group_order = (!$dashboard)?"GROUP BY CBU.id_member Order By Name;":"";

        $sql_query="SELECT FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as Name,
        $month_query
        ,SUM(amount)  as 'Total'   FROM (
            SELECT c.date_received as transaction_date,ifnull(amount,0) as amount,id_member 
            FROM cash_receipt_details as cd
            LEFT JOIN cash_receipt as c on c.id_cash_receipt = cd.id_cash_receipt
            LEFT JOIN tbl_payment_type as pt on pt.id_payment_type = cd.id_payment_type
            WHERE pt.is_cbu = 1 AND pt.type in (1) AND status <> 10  and c.date_received <= ?
            UNION ALL
            SELECT loan.date_released,ifnull(lc.calculated_charge,0) as amount,id_member from loan_charges as lc
            LEFT JOIN loan on loan.id_loan = lc.id_loan
            LEFT JOIN tbl_payment_type as pt on pt.reference = lc.id_loan_fees and pt.type = 2
            WHERE  pt.is_cbu = 1 AND (loan.status in (6) or loan.loan_status in (1)) and pt.type =2
            and loan.date_released  <= ?
            UNION ALL
            SELECT rt.transaction_date,ifnull(rf.amount,0),id_member FROM repayment_fees as rf
            LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rf.id_repayment_transaction
            LEFT JOIN tbl_payment_type as pt on pt.id_payment_type = rf.id_payment_type
            WHERE pt.is_cbu = 1 and pt.type =3 AND rt.status <> 10 AND rt.transaction_date  <= ?
            UNION ALL
            SELECT date,ifnull(amount,0),id_member FROM cbu_beginning where date  <= ?
            UNION ALL
            SELECT cd.date,(cdd.debit*-1) as amount,cd.id_member FROM cash_disbursement as cd
            LEFT JOIN cash_disbursement_details as cdd on cdd.id_cash_disbursement = cd.id_cash_disbursement
            LEFT JOIN chart_account as ca on ca.id_chart_account = cdd.id_chart_account
            WHERE ca.iscbu = 1 and cd.status <> 10 AND cd.date  <= ?
            UNION ALL
            SELECT jv.date,(jvd.credit-jvd.debit) as amount,jv.id_member FROM journal_voucher as jv
            LEFT JOIN journal_voucher_details as jvd on jvd.id_journal_voucher = jv.id_journal_voucher
            LEFT JOIN chart_account as ca on ca.id_chart_account = jvd.id_chart_account
            WHERE ca.iscbu = 1 and jv.status <> 10 AND jv.date  <= ? AND jv.type = 1
            UNION ALL
            SELECT curdate(),0,id_member FROM member where status=1
        ) as CBU
        LEFT JOIN member as m on m.id_member = CBU.id_member
        $group_order
        
        ";


        $data['cbus'] = DB::select($sql_query,[$dt_query_end,$dt_query_end,$dt_query_end,$dt_query_end,$dt_query_end,$dt_query_end]);

        return $data;
    }
}
