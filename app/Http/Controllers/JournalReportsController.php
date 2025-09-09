<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\MySession;
use App\WebHelper;
use PDF;
use App\CredentialModel;
use Excel;
use App\Exports\VoucherExport;
class JournalReportsController extends Controller
{
    public $books = ['journal_entries','cash_disbursement','cash_receipt'];

    public $books_title = ['General Journal','Cash-out Summary','Cash-in Summary'];
    public function index($type,Request $request){
        if(!in_array($type,$this->books)){
            abort(404);
        }
        $credential= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),"/journal/report/$type");
        if(!$credential->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
        $data =$this->parseData($type,$request);
        $data['credential'] = $credential;
        $data['head_title'] = $data['description'];


        return view('journal_report.index',$data);


        return $data;
    }

    public function parseData($type,Request $request){

        $data = array();

        $data['fil_type'] = $type;
        $data['sel_cash_bank'] =$request->cash_bank;

        $data['show_cancel'] = $request->show_cancel ?? 0;

        $data['start_date'] = $request->start_date ?? MySession::current_date();
        $data['end_date'] = $request->end_date ?? MySession::current_date();
        $data['selected_type'] = 1;
        $data['export_type'] = $request->export_type ?? 1;
        $g = new GroupArrayController();
        if(isset($request->cash_bank)){
            // $books = json_decode($request->books,true);
            $books = [array_search($type, $this->books)+1];
        }else{
            $books = [];
        }

        // return $books;
        $data['books'] = $books;
        $data['transactions'] = array();
        $data['description'] =  $this->books_title[array_search($type, $this->books)];


        if(($request->cash_bank ?? 1) == 1){
            $data['cb'] = "Cash on Hand";
        }else{
            $data['cb'] = "Cash in Bank";
        }

        foreach($data['books'] as $b){
            if($data['selected_type'] == 1){
                $trans = $this->GetTransaction($b,$data['start_date'],$data['end_date'],$data['show_cancel'], $data['sel_cash_bank']);
            }
            $temp = [];
            foreach($trans as $key=>$obj){
                $temp[$key] = $obj;
            }
           
            if($data['selected_type'] == 1){
                $data['transactions'][$trans['description']]= $trans['transactions']; 
            }
        }

        //DATE ON HEADER FORMAT
        $data['date'] = WebHelper::ReportDateFormatter($data['start_date'],$data['end_date']);

        return $data;        
    }

    public function GetTransaction($type,$date_start,$date_end,$cancel,$cash_bank){
        $description = "";
        $cash_bank = ($cash_bank==1)?1:2; //to prevent SQL injection
        $can2 = ($cancel==0)?"AND jv.status <> 10":"";
        switch($type){
            case   1:

            $can = ($cancel==0)?"AND jv.status <> 10":"";
            $sql="SELECT DATE_FORMAT(date,'%m/%d/%Y') as date,jt.description as type,payee,jv.description,concat('JV# ',jv.id_journal_voucher) as reference,if(jv.status=10,0,total_amount) as amount,if(jv.status=10,concat('[',FORMAT(jv.total_amount,2),'] [CANCELLED] [',CONVERT(jv.cancellation_reason USING utf8),']'),'') as remarks,jv.status
            FROM journal_voucher as jv
            LEFT JOIN jv_type as jt on jt.id_jv_type = jv.type
            WHERE date >= ? and date <= ? $can ORDER BY jv.id_journal_voucher ASC;";
            $description = "Journal Entries";
            break;

            case 2:
            $can = ($cancel==0)?"AND cv.status <> 10":"";
            $can3 = ($cancel==0)?"AND crv.status <> 10":"";

            // - if(cv.type in (2,3,4),cv.description,concat('# ',cv.reference))
            // - if(jv.type in (1),jv.description,concat('# ',jv.reference))
            // - concat(if(crv.or_no is null,'',concat('OR # ',crv.or_no,' - ')),'(',crv.reference,')')



            $sql="
            SELECT * FROM (
            SELECT date as a_date,DATE_FORMAT(date,'%m/%d/%Y') as date,ct.description as type,payee,concat(if(cv.type in (2,3,4),concat('[',UPPER(ct.description),'] '),''),cv.description) as description,concat('CDV# ',cv.id_cash_disbursement) as reference,SUM(if(cv.status=10,0,credit)) as amount,if(cv.status=10,concat('[',FORMAT(SUM(cvd.credit),2),'] [CANCELLED] [',CONVERT(cv.cancellation_reason USING utf8),']'),'') as remarks,cv.status
            FROM cash_disbursement as cv
            LEFT JOIN cdv_type as ct on ct.id_cdv_type = cv.type
            LEFT JOIN cash_disbursement_details as cvd on cvd.id_cash_disbursement = cv.id_cash_disbursement
            LEFT JOIN chart_account as ca on ca.id_chart_account = cvd.id_chart_account
            WHERE date >= ? and date <= ? and ca.id_chart_account_category =$cash_bank and cvd.credit > 0 $can
            GROUP BY cv.id_cash_disbursement
            UNION ALL
            SELECT date as a_date,DATE_FORMAT(date,'%m/%d/%Y') as date,ct.description as type,payee,concat(if(jv.type=1,concat('[',if(jv.jv_type=1,'NORMAL',if(jv.jv_type=2,'REVERSAL','ADJUSTMENT')),'] '),''),jv.description ) as description,concat('JV# ',jv.id_journal_voucher) as reference,SUM(if(jv.status=10,0,credit)) as amount,if(jv.status=10,concat('[',FORMAT(jvd.credit,2),'] [CANCELLED] [',CONVERT(jv.cancellation_reason USING utf8),']'),'') as remarks,jv.status
            FROM journal_voucher as jv
            LEFT JOIN jv_type as ct on ct.id_jv_type = jv.type
            LEFT JOIN journal_voucher_details as jvd on jvd.id_journal_voucher = jv.id_journal_voucher
            LEFT JOIN chart_account as ca on ca.id_chart_account = jvd.id_chart_account
            WHERE date >= ? and date <= ? and ca.id_chart_account_category =$cash_bank and jvd.credit > 0 $can2
            GROUP BY jv.id_journal_voucher
            UNION ALL
            SELECT date as a_date,DATE_FORMAT(date,'%m/%d/%Y') as date,ct.description as type,payee,crv.description as description,concat('CRV# ',crv.id_cash_receipt_voucher) as reference,SUM(if(crv.status=10,0,credit)) as amount,if(crv.status=10,concat('[',FORMAT(crvd.credit,2),'] [CANCELLED] [',CONVERT(crv.cancellation_reason USING utf8),']'),'') as remarks,crv.status
            FROM cash_receipt_voucher as crv
            LEFT JOIN crv_type as ct on ct.id_crv_type = crv.type
            LEFT JOIN cash_receipt_voucher_details as crvd on crvd.id_cash_receipt_voucher = crv.id_cash_receipt_voucher
            LEFT JOIN chart_account as ca on ca.id_chart_account = crvd.id_chart_account
            WHERE date >= ? and date <= ? and ca.id_chart_account_category =$cash_bank and crvd.credit > 0 $can3
            GROUP BY crv.id_cash_receipt_voucher
        ) as g ORDER BY a_date;";


            $description = "Cash-out Summary";
            break;

            case 3:
                // ifnull(concat(' / OR# ',crv.or_no),'')


            $can3 = ($cancel==0)?"AND cdv.status <> 10":"";
            $can = ($cancel==0)?"AND crv.status <> 10":"";


            // -concat(if(crv.or_no is null,'',concat('OR # ',crv.or_no,' - ')),'(',crv.reference,')')
            // -if(jv.type in (1),jv.description,concat('# ',jv.reference))
            // -if(cdv.type in (2,3,4),cdv.description,concat('# ',cdv.reference))

            $sql = "
            SELECT * FROM (
            SELECT date as a_date,DATE_FORMAT(date,'%m/%d/%Y') as date,ct.description as type,payee,crv.description description,concat('CRV# ',crv.id_cash_receipt_voucher) as reference,SUM(if(crv.status=10,0,debit)) as amount,if(crv.status=10,concat('[',FORMAT(SUM(crvd.debit),2),'] [CANCELLED] [',CONVERT(crv.cancellation_reason USING utf8),']'),'') as remarks,
            crv.or_no,crv.status
            FROM cash_receipt_voucher as crv
            LEFT JOIN crv_type as ct on ct.id_crv_type = crv.type
            LEFT JOIN cash_receipt_voucher_details as crvd on crvd.id_cash_receipt_voucher = crv.id_cash_receipt_voucher
            LEFT JOIN chart_account as ca on ca.id_chart_account = crvd.id_chart_account
            WHERE date >= ? and date <= ? and ca.id_chart_account_category =$cash_bank and crvd.debit > 0 $can
            GROUP BY crv.id_cash_receipt_voucher
            UNION ALL
            SELECT date as a_date,DATE_FORMAT(date,'%m/%d/%Y') as date,ct.description as type,payee,concat(if(jv.type=1,concat('[',if(jv.jv_type=1,'NORMAL',if(jv.jv_type=2,'REVERSAL','ADJUSTMENT')),'] '),''),jv.description ) as description,concat('JV# ',jv.id_journal_voucher) as reference,SUM(if(jv.status=10,0,debit)) as amount,if(jv.status=10,concat('[',FORMAT(jvd.debit,2),'] [CANCELLED] [',CONVERT(jv.cancellation_reason USING utf8),']'),'') as remarks,
            '' as or_no,jv.status
            FROM journal_voucher as jv
            LEFT JOIN jv_type as ct on ct.id_jv_type = jv.type
            LEFT JOIN journal_voucher_details as jvd on jvd.id_journal_voucher = jv.id_journal_voucher
            LEFT JOIN chart_account as ca on ca.id_chart_account = jvd.id_chart_account
            WHERE date >= ? and date <= ? and ca.id_chart_account_category =$cash_bank and jvd.debit > 0 $can2
            GROUP BY jv.id_journal_voucher
            UNION ALL
            SELECT date as a_date,DATE_FORMAT(date,'%m/%d/%Y') as date,ct.description as type,payee,concat(if(cdv.type in (2,3,4),concat('[',UPPER(ct.description),'] '),''),cdv.description) as description,concat('CDV# ',cdv.id_cash_disbursement) as reference,SUM(if(cdv.status=10,0,debit)) as amount,if(cdv.status=10,concat('[',FORMAT(cdvd.debit,2),'] [CANCELLED] [',CONVERT(cdv.cancellation_reason USING utf8),']'),'') as remarks,
            '' as or_no,cdv.status
            FROM cash_disbursement as cdv
            LEFT JOIN cdv_type as ct on ct.id_cdv_type = cdv.type
            LEFT JOIN cash_disbursement_details as cdvd on cdvd.id_cash_disbursement = cdv.id_cash_disbursement
            LEFT JOIN chart_account as ca on ca.id_chart_account = cdvd.id_chart_account
            WHERE date >= ? and date <= ?  and ca.id_chart_account_category =$cash_bank and cdvd.debit > 0 $can3
            GROUP BY cdv.id_cash_disbursement)  as g ORDER BY a_date;";




            $description = "Cash-in Summary";
            break;
            default:
            $sql ="";
            break;
        }
        $output = array();
        $output['description'] = $description;

        $output['transactions'] = array();
        if($sql != ""){
            $output['transactions'] = DB::select($sql,[$date_start,$date_end,$date_start,$date_end,$date_start,$date_end]);
        }

        return $output;
    }


    public function GetTransactionEntry($type,$date_start,$date_end,$cancel,$fil_type,$cash_bank){

        if($fil_type == "cash_receipt"){
            $deb_cred = "debit";
        }else{
            $deb_cred= "credit";
        }
        $cash_bank = ($cash_bank==1)?1:2; //to prevent SQL injection

        $can2 = ($cancel==0)?"AND jv.status <> 10":"";

        $description = "";
        switch($type){
            case   1:
            $can = ($cancel==0)?"AND jv.status <> 10":"";
            $sql="SELECT DATE_FORMAT(date,'%m/%d/%Y') as date,jt.description as type,payee,jv.description as entry_description,concat('JV# ',jv.id_journal_voucher) as reference,if(jv.status=10,0,total_amount) as amount,if(jv.status=10,concat('[',FORMAT(jv.total_amount,2),'] [CANCELLED] [',CONVERT(jv.cancellation_reason USING utf8),']'),'') as remarks,
            concat(ca.account_code,' - ',ca.description) as account,ca.account_code,ca.description as account_name,jvd.details,jvd.debit,jvd.credit,jv.status
            FROM journal_voucher as jv
            LEFT JOIN jv_type as jt on jt.id_jv_type = jv.type
            LEFT JOIN journal_voucher_details as jvd on jvd.id_journal_voucher = jv.id_journal_voucher
            LEFT JOIN chart_account as ca on ca.id_chart_account = jvd.id_chart_account
            WHERE date >= ? and date <=  ? $can ORDER BY jv.id_journal_voucher ASC,jv.date ASC";
            $description = "Journal Entries";
            break;

            case 2:
            $can = ($cancel==0)?"AND cdv.status <> 10":"";

            $sql="
            SELECT * FROM (
                SELECT date as a_date,DATE_FORMAT(date,'%m/%d/%Y') as date,ct.description as type,payee,cdv.description as entry_description,concat('CDV# ',cdv.id_cash_disbursement) as reference,if(cdv.status=10,0,total) as amount,if(cdv.status=10,concat('[',FORMAT(cdv.total,2),'] [CANCELLED] [',CONVERT(cdv.cancellation_reason USING utf8),']'),'') as remarks,
                concat(ca.account_code,' - ',ca.description) as account,ca.account_code,ca.description as account_name,cdvd.details,cdvd.debit,cdvd.credit,cdv.status
                FROM cash_disbursement as cdv
                LEFT JOIN cdv_type as ct on ct.id_cdv_type = cdv.type
                LEFT JOIN cash_disbursement_details as cdvd on cdvd.id_cash_disbursement = cdv.id_cash_disbursement
                LEFT JOIN chart_account as ca on ca.id_chart_account = cdvd.id_chart_account
                WHERE date >= ? and date <= ? $can
                UNION ALL
                SELECT date,DATE_FORMAT(date,'%m/%d/%Y') as date,jt.description as type,payee,jv.description as entry_description,concat('JV# ',jv.id_journal_voucher) as reference,if(jv.status=10,0,total_amount) as amount,if(jv.status=10,concat('[',FORMAT(jv.total_amount,2),'] [CANCELLED] [',CONVERT(jv.cancellation_reason USING utf8),']'),'') as remarks,
                concat(ca.account_code,' - ',ca.description) as account,ca.account_code,ca.description as account_name,jvd.details,jvd.debit,jvd.credit,jv.status 
                FROM journal_voucher as jv
                LEFT JOIN jv_type as jt on jt.id_jv_type = jv.type
                INNER JOIN journal_voucher_details as jvd_2 on jvd_2.id_journal_voucher = jv.id_journal_voucher
                INNER JOIN journal_voucher_details as jvd on jvd.id_journal_voucher = jvd_2.id_journal_voucher
                LEFT JOIN chart_account as ca_v on ca_v.id_chart_account = jvd_2.id_chart_account
                LEFT JOIN chart_account as ca on ca.id_chart_account = jvd.id_chart_account
                WHERE jv.date >= ? AND  jv.date <= ? AND ca_v.id_chart_account_category = $cash_bank and jvd_2.$deb_cred > 0 $can2) as tt ORDER BY a_date ASC,reference ASC";
            $description = "Cash Disbursement";
            break;
            case 3:
            $can = ($cancel==0)?"AND crv.status <> 10":"";
            $sql="
            SELECT * FROM (
               SELECT date as a_date,DATE_FORMAT(date,'%m/%d/%Y') as date,ct.description as type,payee,crv.description as entry_description,concat('CRV# ',crv.id_cash_receipt_voucher) as reference,if(crv.status=10,0,total_amount) as amount,if(crv.status=10,concat('[',FORMAT(crv.total_amount,2),'] [CANCELLED] [',CONVERT(crv.cancellation_reason USING utf8),']'),'') as remarks,concat(ca.account_code,' - ',ca.description) as account,ca.account_code,ca.description as account_name,crvd.details,crvd.debit,crvd.credit,crv.or_no,crv.status
               FROM cash_receipt_voucher as crv
               LEFT JOIN crv_type as ct on ct.id_crv_type = crv.type
               LEFT JOIN cash_receipt_voucher_details as crvd on crvd.id_cash_receipt_voucher = crv.id_cash_receipt_voucher
               LEFT JOIN chart_account as ca on ca.id_chart_account = crvd.id_chart_account
               WHERE date >= ? and date <= ? $can 
               UNION ALL
               SELECT date,DATE_FORMAT(date,'%m/%d/%Y') as date,jt.description as type,payee,jv.description as entry_description,concat('JV# ',jv.id_journal_voucher) as reference,if(jv.status=10,0,total_amount) as amount,if(jv.status=10,concat('[',FORMAT(jv.total_amount,2),'] [CANCELLED] [',CONVERT(jv.cancellation_reason USING utf8),']'),'') as remarks,
               concat(ca.account_code,' - ',ca.description) as account,ca.account_code,ca.description as account_name,jvd.details,jvd.debit,jvd.credit,'' as or_no,jv.status 
               FROM journal_voucher as jv
               LEFT JOIN jv_type as jt on jt.id_jv_type = jv.type
               INNER JOIN journal_voucher_details as jvd_2 on jvd_2.id_journal_voucher = jv.id_journal_voucher
               INNER JOIN journal_voucher_details as jvd on jvd.id_journal_voucher = jvd_2.id_journal_voucher
               LEFT JOIN chart_account as ca_v on ca_v.id_chart_account = jvd_2.id_chart_account
               LEFT JOIN chart_account as ca on ca.id_chart_account = jvd.id_chart_account
               WHERE jv.date >= ? AND  jv.date <= ? AND ca_v.id_chart_account_category = $cash_bank and jvd_2.$deb_cred > 0 $can2 ) as tt 
            ORDER BY a_date ASC,reference ASC";


                    // SELECT date as a_date,DATE_FORMAT(date,'%m/%d/%Y') as date,ct.description as type,payee,crv.description as entry_description,concat('CRV# ',crv.id_cash_receipt_voucher) as reference,SUM(if(crv.status=10,0,debit)) as amount,if(crv.status=10,concat('[',FORMAT(SUM(crvd.debit),2),'] [CANCELLED] [',CONVERT(crv.cancellation_reason USING utf8),']'),'') as remarks,
                    // crv.or_no,crv.status
                    // FROM cash_receipt_voucher as crv
                    // LEFT JOIN crv_type as ct on ct.id_crv_type = crv.type
                    // LEFT JOIN cash_receipt_voucher_details as crvd on crvd.id_cash_receipt_voucher = crv.id_cash_receipt_voucher
                    // LEFT JOIN chart_account as ca on ca.id_chart_account = crvd.id_chart_account
                    // WHERE date >= '2022-07-01' and date <= '2022-09-03' and ca.id_chart_account_category =1 and crvd.debit > 0
                    // GROUP BY crv.id_cash_receipt_voucher
                    // UNION ALL
                    // SELECT date as a_date,DATE_FORMAT(date,'%m/%d/%Y') as date,ct.description as type,payee,jv.description as entry_description,concat('JV# ',jv.id_journal_voucher) as reference,SUM(if(jv.status=10,0,debit)) as amount,if(jv.status=10,concat('[',FORMAT(jvd.debit,2),'] [CANCELLED] [',CONVERT(jv.cancellation_reason USING utf8),']'),'') as remarks,
                    // '' as or_no,jv.status
                    // FROM journal_voucher as jv
                    // LEFT JOIN jv_type as ct on ct.id_jv_type = jv.type
                    // LEFT JOIN journal_voucher_details as jvd on jvd.id_journal_voucher = jv.id_journal_voucher
                    // LEFT JOIN chart_account as ca on ca.id_chart_account = jvd.id_chart_account
                    // WHERE date >= '2022-07-01' and date <= '2022-09-03' and ca.id_chart_account_category =1 and jvd.debit > 0
                    // GROUP BY jv.id_journal_voucher;


            $description = "Cash Receipt";
            break;
            default:
            $sql ="";
            break;
        }
        $output = array();
        $output['description'] = $description;

        $output['transactions'] = array();
        if($sql != ""){
            $output['transactions'] = DB::select($sql,[$date_start,$date_end,$date_start,$date_end]);
        }

        return $output;        
    }

    public function export($type,Request $request){
        $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/journal/report/'.$type);
        if(!$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
        $data =$this->parseData($type,$request);

        if(!in_array($type,$this->books)){
            abort(404);
        }
        $data['file_name'] = $data['description']." - ".$data['date'];
        // dd($data);
   
        if($data['export_type'] == 2){
            return Excel::download(new VoucherExport($data,1), $data['file_name'].".xlsx");
        }
        // return $data;
        $html =  view('journal_report.export',$data);

        $pdf = PDF::loadHtml($html);
        $pdf->setOption("encoding","UTF-8");
        $pdf->setOption('margin-bottom', '5mm');
        $pdf->setOption('margin-top', '7mm');
        $pdf->setOption('margin-right', '5mm');
        $pdf->setOption('margin-left', '5mm');
        $pdf->setOption('header-left', 'Page [page] of [toPage]');

        $pdf->setOption('header-font-size', 8);
        $pdf->setOption('header-font-name', 'Calibri');
        $pdf->setOrientation('landscape');

        return $pdf->stream();




        return $data;
    }
}










