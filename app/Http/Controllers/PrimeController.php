<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\MySession;
use App\CredentialModel;
use PDF;
use Excel;
use App\Exports\PrimeExport;
use DateTime;

class PrimeController extends Controller
{
    public function index(Request $request){    
        $data['isAdmin'] = MySession::isAdmin();
        $data['id_member'] = MySession::myId();

        if($data['isAdmin']){
            $data['id_member'] = $request->id_member ?? $data['id_member'];
        }
        $data['head_title'] = "Prime";
        $data['start_date'] = $request->start_date ??date_format(date_create(MySession::current_date()),"Y-m-01");
        $data['end_date'] = $request->end_date ?? date_format(date_create(MySession::current_date()),"Y-m-t");

        $data['prime_ledger'] = DB::select("CALL getMemberPrime(?,?,?);",[$data['id_member'],$data['start_date'],$data['end_date']]);

        $data['selected_member'] =DB::table('member as m')
        ->select(DB::raw("concat(membership_id,' || ',FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)) as member_name,id_member"))
        ->where('id_member',$data['id_member'])
        ->first();

        return view('prime.index',$data);
    }
    public function PrimeAccountExport(Request $request){
        $id_member_req = $request->id_member ?? MySession::myId();
        $data['id_member'] = (MySession::isAdmin())?$id_member_req:MySession::myId();
        $data['start_date'] = $request->start_date ??date_format(date_create(MySession::current_date()),"Y-m-01");
        $data['end_date'] = $request->end_date ?? date_format(date_create(MySession::current_date()),"Y-m-t");

        // dd($data);

        $data['file_name'] = DB::table('member')->select(DB::raw("concat('Prime - ',last_name,' ',first_name) as file_name"))->where('id_member',$data['id_member'])->first()->file_name ?? '';
        $data['prime_ledger'] = DB::select("CALL getMemberPrime(?,?,?);",[$data['id_member'],$data['start_date'],$data['end_date']]);        
 
  
        return Excel::download(new PrimeExport($data['prime_ledger'],1), $data['file_name'].".xlsx");


        return view('cbu.export_excel',$data);
    }
    public function PrimeReport(Request $request){
        // $data['credential']= CredentialModel::GetCredential(MySession::myPrivilegeId());
        // if(!$data['credential']->is_view){
        //     return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        // }   
        $data['head_title'] = "Prime Report";
        $date=$data['date'] = $request->date ?? MySession::current_date();
        $data['prime'] = $this->prime_data($date);

        return view("prime.report",$data);
        dd($data);

    }
    public function PrimeReportExportExcel(Request $request){
        $date=$data['date'] = $request->date ?? MySession::current_date();
        $data['prime'] = $this->prime_data($date);
        $data['file_name'] = "Prime ".date("F d, Y", strtotime($date));
        // dd($data);
        return Excel::download(new PrimeExport($data['prime'],2), $data['file_name'].".xlsx");
       
    }
    public function prime_data($date,$withdraw = false){
        $union = "";
        if($withdraw){
            $union = "UNION ALL
                        SELECT id_member,'-' as date,'-' as reference,'With' as description,amount as debit,0 as credit
                        FROM prime_withdrawal as pw
                        WHERE pw.status = 1";
        }

        $param = array_fill(0,4,$date);
        $prime = DB::select("SELECT m.id_member,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member,SUM(credit-debit) as amount FROM (
        SELECT cd.id_member,
        cd.date as transaction_date,concat('CDV# ',cd.id_cash_disbursement) as reference,if(cdv.details is null OR cdv.details='',cd.description,cdv.details) as description,debit,credit
        FROM cash_disbursement_details as cdv
        LEFT JOIN cash_disbursement as cd on cd.id_cash_disbursement = cdv.id_cash_disbursement
        LEFT JOIN chart_account as ca on ca.id_chart_account = cdv.id_chart_account
        LEFT JOIN cdv_type as ct on ct.id_cdv_type = cd.type
        WHERE cd.status <> 10 and ca.isprime=1  and cd.date <= ?
        UNION ALL
        SELECT jv.id_member,
        jv.date as transaction_date,concat('JV# ',jv.id_journal_voucher) as reference,if(jvd.details is null OR jvd.details='',jv.description,jvd.details) as description,debit,credit
        FROM journal_voucher_details as jvd
        LEFT JOIN journal_voucher as jv on jv.id_journal_voucher = jvd.id_journal_voucher
        LEFT JOIN chart_account as ca on ca.id_chart_account = jvd.id_chart_account
        LEFT JOIN jv_type as jt on jt.id_jv_type = jv.type
        WHERE jv.status <> 10 and ca.isprime=1 and jv.date <= ?
        UNION ALL
        SELECT crv.id_member,
        crv.date as transaction_date,concat('CRV# ',crv.id_cash_receipt_voucher) as reference,if(crvd.details is null OR crvd.details='',crv.description,crvd.details) as description,debit,credit
        FROM cash_receipt_voucher_details as crvd
        LEFT JOIN cash_receipt_voucher as crv on crv.id_cash_receipt_voucher = crvd.id_cash_receipt_voucher
        LEFT JOIN chart_account as ca on ca.id_chart_account = crvd.id_chart_account
        LEFT JOIN crv_type as ct on ct.id_crv_type = crv.type
        WHERE crv.status <> 10 and ca.isprime=1 and crv.date <= ?
        UNION ALL
        SELECT id_member,date,'-' as reference,'Beginning' as description,0 as debit,amount as credit 
        FROM prime_beginning
        WHERE date <= ?
        $union


        ) as prime_summary
        LEFT JOIN member as m on m.id_member = prime_summary.id_member
        GROUP BY prime_summary.id_member
        HAVING SUM(credit-debit) <> 0
        ORDER BY member;",$param);

        return $prime;
    }
    public function PrimeMonthlyIndex(Request $request){

        // $credential= CredentialModel::GetCredential(MySession::myPrivilegeId());
        // if(!$credential->is_view){
        //     return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        // }

        date_default_timezone_set('Asia/Manila');
        $dt = new DateTime();
        $year = $request->year ?? $dt->format('Y');


        $m = ($year == $dt->format('Y'))?MySession::current_month():12;

        $data = $this->parsePrimeMonthly(1,$m,$year);
        $data['head_title'] = "Monthly Prime";
        // $data['credential'] = $credential;



        return view('reports.prime_monthly',$data);
    }
    public function parsePrimeMonthly($start_month,$end_month,$year,$dashboard = false){
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
            SELECT cd.date as transaction_date,credit-debit as amount,cd.id_member
            FROM cash_disbursement_details as cdv
            LEFT JOIN cash_disbursement as cd on cd.id_cash_disbursement = cdv.id_cash_disbursement
            LEFT JOIN chart_account as ca on ca.id_chart_account = cdv.id_chart_account
            LEFT JOIN cdv_type as ct on ct.id_cdv_type = cd.type
            WHERE cd.status <> 10 and ca.isprime=1  and cd.date <= ?
            UNION ALL
            SELECT jv.date as transaction_date,credit-debit as amount,jv.id_member
            FROM journal_voucher_details as jvd
            LEFT JOIN journal_voucher as jv on jv.id_journal_voucher = jvd.id_journal_voucher
            LEFT JOIN chart_account as ca on ca.id_chart_account = jvd.id_chart_account
            LEFT JOIN jv_type as jt on jt.id_jv_type = jv.type
            WHERE jv.status <> 10 and ca.isprime=1 and jv.date <= ?
            UNION ALL
            SELECT crv.date as transaction_date,credit-debit as amount,crv.id_member
            FROM cash_receipt_voucher_details as crvd
            LEFT JOIN cash_receipt_voucher as crv on crv.id_cash_receipt_voucher = crvd.id_cash_receipt_voucher
            LEFT JOIN chart_account as ca on ca.id_chart_account = crvd.id_chart_account
            LEFT JOIN crv_type as ct on ct.id_crv_type = crv.type
            WHERE crv.status <> 10 and ca.isprime=1 and crv.date <= ?
            UNION ALL
            SELECT date,amount,id_member FROM prime_beginning
        ) as CBU
        LEFT JOIN member as m on m.id_member = CBU.id_member
        $group_order
        ";

        $data['primes'] = DB::select($sql_query,[$dt_query_end,$dt_query_end,$dt_query_end]);

        return $data;
    }

    public function PrimeMonthlyexport(Request $request){
        date_default_timezone_set('Asia/Manila');
        $dt = new DateTime();
        $year = $request->year ?? $dt->format('Y');
        $m = ($year == $dt->format('Y'))?MySession::current_month():12;

        $data = $this->parsePrimeMonthly(1,$m,$year);
        $html =  view('reports.prime_export',$data);

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
    public function PrimeMonthlyexportExcel(Request $request){
        date_default_timezone_set('Asia/Manila');
        $dt = new DateTime();
        $year = $request->year ?? $dt->format('Y');
        $m = ($year == $dt->format('Y'))?MySession::current_month():12;     

        // dd((int)$m);
        $data = $this->parsePrimeMonthly(1,$m,$year);
        $data['file_name'] = "Prime YEAR $year";
        return Excel::download(new PrimeExport($data['primes'],3,(int)$m), $data['file_name'].".xlsx");


    }
}
