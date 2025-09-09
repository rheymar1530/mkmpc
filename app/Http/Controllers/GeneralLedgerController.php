<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\MySession;
use App\WebHelper;
use PDF;
use App\CredentialModel;
use Excel;
use App\Exports\AccountingExports;

class GeneralLedgerController extends Controller
{
    public function index($type,Request $request){
        $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),"/accounting/{$type}");
    
        if(!$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }

        // dd($request->all());
        $data['sidebar'] = "sidebar-collapse";
        $data['accounts'] = DB::table('chart_account as ca')
                            ->select(DB::raw("ca.id_chart_account,concat(ca.account_code,' | ',ca.description,' || ',ifnull(cf.description,'')) as account_name"))
                            ->leftJoin('cash_flow as cf','cf.id_cash_flow','ca.id_cash_flow')
                            ->get();
        $data['head_title'] = "General Ledger";

        // $data['filter_type']=$filter_type = $request->filter_type ?? 2;  
        $data['filter_type'] =$filter_type= ($type == "general_ledger")?2:1;

        //1 - Trial Balance; 2 - GL
        $data['head_title'] =$data['title_head'] = ($filter_type==2)?'General Ledger':'Trial Balance';

        $start = date('Y-m-d', strtotime('-30 days'));
        $end = MySession::current_date();

        $data['start_date'] = $request->start_date ?? $start;
        $data['end_date'] = $request->end_date ?? $end;

        $data['show_cancel'] = $request->show_cancel ?? 0;

        if(isset($request->books)){
            $books = json_decode($request->books,true);
        }else{
            $books = [];
        }

        if(isset($request->accounts)){
            $accounts = json_decode($request->accounts,true);
        }else{
            $accounts = [];
        }

        $books = [1,2,3];

        $data['books_selected'] = $books;
        $data['acc_selected'] = $accounts;
        // return $data;

        // if(count($books) ==0 || (count($accounts)  == 0 && $filter_type == 2)){
        if(count($request->all()) == 0){
            $data['general_ledger'] = [];
        }else{
            $data['general_ledger'] = $this->parseData($data,$filter_type);
            
        }
        
        return view('general_ledger.index',$data);
    }

    public function exportGL($type,Request $request){
  
        $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/general_ledger');
        if(!$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }

        $data['filter_type'] =$filter_type= ($type == "general_ledger")?2:1;
        $data['head_title'] =$data['title_head'] = ($filter_type==2)?'General Ledger':'Trial Balance';
        if(isset($request->books)){
            $books = json_decode($request->books,true);
        }else{
            $books = [];
        }
        $books = [1,2,3];


        if(isset($request->accounts)){
            $accounts = json_decode($request->accounts,true);
        }else{
            $accounts = [];
        }

        $start = date('Y-m-d', strtotime('-30 days'));
        $end = MySession::current_date();
        // $data['filter_type']=$filter_type = $request->filter_type ?? 2; 
        $data['books_selected'] = $books;
        $data['acc_selected'] = $accounts;
        $data['start_date'] = $request->start_date ?? $start;
        $data['end_date'] = $request->end_date ?? $end;
        $data['show_cancel'] = $request->show_cancel ?? 0;

        $data['date'] = WebHelper::ReportDateFormatter($data['start_date'],$data['end_date']);

        // count($books) ==0 || 
        if(count($request->all())  == 0){
            $data['general_ledger'] = [];
        }else{
            // return $this->parseData($data);
            $data['general_ledger'] = $this->parseData($data,$filter_type);

        }
        $data['file_name'] = $data['head_title']." - ".$data['date'];
        // $data['export_type']=2;
        $data['export_type'] = $request->export_type;


        if($data['export_type'] == 2){
            return Excel::download(new AccountingExports($data), $data['file_name'].".xlsx");
        }
        $html =  view('general_ledger.export_pdf',$data);


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
        return $data;



    }

    public function parseData($data,$filter_type){

    
   
        $placeholder_account = implode(', ', array_fill(0, count($data['acc_selected']), '?'));

        if($filter_type == 2){


            $jv_query = "
            SELECT cb.date as a_date,DATE_FORMAT(cb.date,'%m/%d/%Y') as date,concat(ca.account_code,' | ',ca.description)  as account,'BEGINNING' as description,CONCAT('BEG #',cb.id_chart_beginning) as post_reference,
            cb.credit,cb.debit,'' as remarks,ca.account_code,ca.description as ac_description
            FROM chart_beginning as cb
            LEFT JOIN chart_account as ca on ca.id_chart_account = cb.id_chart_account
            WHERE cb.date >= ? and cb.date <= ? and ca.id_chart_account in ($placeholder_account) AND cb.status <> 10
            UNION ALL
            SELECT jv.date as a_date,date_format(jv.date,'%m/%d/%Y') as date,concat(ca.account_code,' | ',ca.description)  as account,jv.description,concat('JV# ',jv.id_journal_voucher) as post_reference,
            if(jv.status =10,0,jvd.credit) as credit,if(jv.status =10,0,jvd.debit) as debit,if(jv.status=10,concat('[Cancelled Amount ',FORMAT(if(credit>0,credit,debit),2),']'),'') as remarks,ca.account_code,ca.description as ac_description
            FROM journal_voucher as jv
            LEFT JOIN journal_voucher_details as jvd on jvd.id_journal_voucher = jv.id_journal_voucher
            LEFT JOIN chart_account as ca on ca.id_chart_account = jvd.id_chart_account
            WHERE jv.date >= ? and jv.date <= ? and ca.id_chart_account in ($placeholder_account)".(($data['show_cancel']==0)?" AND jv.status <> 10":"");


            $cv_query = "SELECT cv.date as a_date,date_format(cv.date,'%m/%d/%Y') as date,concat(ca.account_code,' | ',ca.description)  as account,cv.description,concat('CDV# ',cv.id_cash_disbursement) as post_reference,
            if(cv.status =10,0,cdv.credit) as credit,if(cv.status =10,0,cdv.debit) as debit,if(cv.status=10,concat('[Cancelled Amount ',FORMAT(if(credit>0,credit,debit),2),']'),'') as remarks,ca.account_code,ca.description as ac_description
            FROM cash_disbursement as cv
            LEFT JOIN cash_disbursement_details as cdv on cdv.id_cash_disbursement = cv.id_cash_disbursement
            LEFT JOIN chart_account as ca on ca.id_chart_account = cdv.id_chart_account
            WHERE cv.date >= ? and cv.date <= ? and ca.id_chart_account in ($placeholder_account)".(($data['show_cancel']==0)?" AND cv.status <> 10":"");


            $crv_query = "SELECT crv.date as a_date,date_format(crv.date,'%m/%d/%Y') as date,concat(ca.account_code,' | ',ca.description)  as account,crv.description,concat('CRV# ',crv.id_cash_receipt_voucher) as post_reference,
            if(crv.status =10,0,crvd.credit) as credit,if(crv.status =10,0,crvd.debit) as debit,if(crv.status=10,concat('[Cancelled Amount ',FORMAT(if(credit>0,credit,debit),2),']'),'') as remarks,ca.account_code,ca.description as ac_description
            FROM cash_receipt_voucher as crv
            LEFT JOIN cash_receipt_voucher_details as crvd on crvd.id_cash_receipt_voucher = crv.id_cash_receipt_voucher
            LEFT JOIN chart_account as ca on ca.id_chart_account = crvd.id_chart_account
            WHERE crv.date >= ? and crv.date <= ? and ca.id_chart_account in ($placeholder_account)".(($data['show_cancel']==0)?" AND crv.status <> 10":"");
        }else{
            $jv_query = "
            SELECT ca.id_chart_account,concat(ca.account_code,' | ',ca.description)  as account,SUM(cb.credit) as credit,SUM(cb.debit) as debit,ca.account_code
            FROM chart_beginning as cb
            LEFT JOIN chart_account as ca on ca.id_chart_account = cb.id_chart_account
            WHERE  cb.date <= ? AND cb.status <> 10
            GROUP BY cb.id_chart_account
            UNION ALL
            SELECT ca.id_chart_account,concat(ca.account_code,' | ',ca.description)  as account,
            SUM(jvd.credit) as credit,SUM(jvd.debit) as debit,ca.account_code
            FROM journal_voucher as jv
            LEFT JOIN journal_voucher_details as jvd on jvd.id_journal_voucher = jv.id_journal_voucher
            LEFT JOIN chart_account as ca on ca.id_chart_account = jvd.id_chart_account
            WHERE jv.date <= ?  AND jv.status <> 10
            GROUP BY jvd.id_chart_account";

            $cv_query = "SELECT ca.id_chart_account,concat(ca.account_code,' | ',ca.description)  as account,SUM(cdv.credit) as credit,SUM(cdv.debit) as debit,ca.account_code
            FROM cash_disbursement as cv
            LEFT JOIN cash_disbursement_details as cdv on cdv.id_cash_disbursement = cv.id_cash_disbursement
            LEFT JOIN chart_account as ca on ca.id_chart_account = cdv.id_chart_account
            WHERE cv.date <= ?  AND cv.status <> 10
            GROUP BY cdv.id_chart_account";

            $crv_query = "SELECT ca.id_chart_account,concat(ca.account_code,' | ',ca.description)  as account,SUM(crvd.credit) as credit,SUM(crvd.debit) as debit,ca.account_code
            FROM cash_receipt_voucher as crv
            LEFT JOIN cash_receipt_voucher_details as crvd on crvd.id_cash_receipt_voucher = crv.id_cash_receipt_voucher
            LEFT JOIN chart_account as ca on ca.id_chart_account = crvd.id_chart_account
            WHERE crv.date <= ? AND crv.status <> 10
            GROUP BY crvd.id_chart_account";

        }

        $sql_array = array();

        $parameter  = array();

        foreach($data['books_selected'] as $book){
            if($filter_type == 2){
                array_push($parameter,$data['start_date']);
            }
            
            array_push($parameter,$data['end_date']);


            if($filter_type == 2){

                foreach($data['acc_selected'] as $val){
                    array_push($parameter,$val);
                }                
            }



            if($book == 1){
                if($filter_type == 2){
                    array_push($parameter,$data['start_date']);
                }
                array_push($parameter,$data['end_date']);


                foreach($data['acc_selected'] as $val){
                    array_push($parameter,$val);
                }
                $p_sql = $jv_query;
            }elseif($book == 2){
                $p_sql = $cv_query;
            }else{
                $p_sql = $crv_query;
            }
            array_push($sql_array,$p_sql);
            
        }


        $sql_query = implode(" UNION ALL ",$sql_array);


        if($filter_type == 2){

     
            $sql="
            SELECT * FROM (
                $sql_query

            ) as t order by t.account_code,t.a_date";
            $output = DB::select($sql,$parameter);
            $g = new GroupArrayController();

            $res = $g->array_group_by($output,['account']);
        }else{
            $sql="SELECT id_chart_account,account,SUM(credit) as credit,SUM(debit) as debit,account_code FROM 
            ( $sql_query ) as t GROUP BY id_chart_account order by account_code";

            $output = DB::select($sql,$parameter);
            $res = $output;
        }




        



        return $res;
    }
}


// SELECT date_format(jv.date,'%m/%d/%Y') as date,concat(ca.account_code,' | ',ca.description)  as account,jv.description,concat('JV# ',jv.id_journal_voucher) as post_reference,
// if(jv.status =10,0,jvd.credit) as credit,if(jv.status =10,0,jvd.debit) as debit,if(jv.status=10,concat('[Cancelled Amount ',FORMAT(if(credit>0,credit,debit),2),']'),'') as remarks
// FROM journal_voucher as jv
// LEFT JOIN journal_voucher_details as jvd on jvd.id_journal_voucher = jv.id_journal_voucher
// LEFT JOIN chart_account as ca on ca.id_chart_account = jvd.id_chart_account
// WHERE jv.date >= '2022-04-01' and jv.date <= '2022-04-31'
// UNION ALL
// SELECT date_format(cv.date,'%m/%d/%Y') as date,concat(ca.account_code,' | ',ca.description)  as account,cv.description,concat('CDV# ',cv.id_cash_disbursement) as post_reference,
// if(cv.status =10,0,cdv.credit) as credit,if(cv.status =10,0,cdv.debit) as debit,if(cv.status=10,concat('[Cancelled Amount ',FORMAT(if(credit>0,credit,debit),2),']'),'') as remarks
// FROM cash_disbursement as cv
// LEFT JOIN cash_disbursement_details as cdv on cdv.id_cash_disbursement = cv.id_cash_disbursement
// LEFT JOIN chart_account as ca on ca.id_chart_account = cdv.id_chart_account
// WHERE cv.date >= '2022-04-01' and cv.date <= '2022-04-31'
// UNION ALL

// SELECT date_format(crv.date,'%m/%d/%Y') as date,concat(ca.account_code,' | ',ca.description)  as account,crv.description,concat('CRV# ',crv.id_cash_receipt_voucher) as post_reference,
// if(crv.status =10,0,crvd.credit) as credit,if(crv.status =10,0,crvd.debit) as debit,if(crv.status=10,concat('[Cancelled Amount ',FORMAT(if(credit>0,credit,debit),2),']'),'') as remarks
// FROM cash_receipt_voucher as crv
// LEFT JOIN cash_receipt_voucher_details as crvd on crvd.id_cash_receipt_voucher = crv.id_cash_receipt_voucher
// LEFT JOIN chart_account as ca on ca.id_chart_account = crvd.id_chart_account
// WHERE crv.date >= '2022-04-01' and crv.date <= '2022-06-31';
