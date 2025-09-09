<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MySession;
use DB;
use PDF;
use App\CredentialModel;
use Excel;
use App\Exports\FSExport;

class CashFlowController extends Controller
{
    public function index(Request $request){

        $data['credential']= CredentialModel::GetCredential(MySession::myPrivilegeId());
        if(!$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }    

        $data = $this->parseData($request);

        // return $data;
        // $html =  view('cash_flow.export',$data);


        // $pdf = PDF::loadHtml($html);
        // $pdf->setOption("encoding","UTF-8");
        // $pdf->setOption('margin-bottom', '0.39in');
        // $pdf->setOption('margin-top', '0.4in');
        // $pdf->setOption('margin-right', '0.33in');
        // $pdf->setOption('margin-left', '0.33in');
        // $pdf->setOption('header-left', 'Page [page] of [toPage]');
    
        // $pdf->setOption('header-font-size', 8);
        // $pdf->setOption('header-font-name', 'Calibri');


        // return $pdf->stream();
        // return $data;
        return view('cash_flow.index',$data);
    }


    public function parseData($request){
        $data['type'] = $type = $request->type ?? 2;



 

        $data['current_month'] = date("n", strtotime(MySession::current_date()));
        $data['current_year'] = date("Y", strtotime(MySession::current_date()));

        $data['month'] = $request->month ?? $data['current_month'];
        $data['year'] = $request->year ?? $data['current_year'];

        $data['export_type'] = $request->export_type ?? 1;

        // dd($data);


        // $data['title'] = "Cash Flow";
        $data['mod_title'] = "Cash Flow";
        $data['end_year'] = $data['year'];
     


        $data['comp_date'] = date("F d, Y", strtotime(($data['year']-1)."-12-31"));

        // dd($data);

        if($data['year'] == env('BEGINNING_YEAR')){
            $data['cash_beg_bal'] = date("F d, Y", strtotime(env('BEGINNING_YEAR')."-".env('BEGINNING_MONTH')."-01"));

            if($data['current_year'] == env('BEGINNING_YEAR')){
                $data['ending'] = date("F d, Y", strtotime(MySession::current_date()));
            }else{
                $data['ending'] =  date("F d, Y", strtotime(env('BEGINNING_YEAR')."-12-31"));;
            }
            
        }else{
            if($data['year'] > $data['current_year']){
                $data['ending'] = $data['cash_beg_bal'] = date("F d, Y", strtotime($data['year']."-01-01"));

            }elseif($data['year'] == $data['current_year']){
                $data['cash_beg_bal'] = "January 1, ".$data['year'];
                $data['ending'] = date("F d, Y", strtotime(MySession::current_date()));
            }else{
                $data['cash_beg_bal'] = "January 1, ".$data['year'];
                $data['ending'] = date("F d, Y", strtotime($data['year']."-12-31"));

            }
        }

        // dd($data);

        if($type == 2){ // yearly
            $month = 12;
            $year = $data['year'];
            $query_start =  ($data['year']-1)."-01-01";
            $query_start_i = ($data['year']-2)."-01-01";

            // $query_end = 
            if($request->year == $data['current_year']){
                $query_end = MySession::current_date();
            }else{
                $query_end =  ($data['year'])."-12-31";
            }
       
        }else{ // month

        }

        $data['head_label']['A'] = $year;
        $data['head_label']['B'] = $year-1;

        $params = [
            // 'jv_start_date'=>$query_start,
            // 'jv_start_date_i'=>$query_start_i,
            'jv_end_date'=>$query_end,

            // 'cv_start_date'=>$query_start,
            // 'cv_start_date_i'=>$query_start_i,
            'cv_end_date'=>$query_end,
            

            // 'crv_start_date'=>$query_start,
            // 'crv_start_date_i'=>$query_start_i,
            'crv_end_date'=>$query_end,
            
            // 'beg_start_date'=>$query_start,
            // 'beg_start_date_i'=>$query_start_i,
            // 'beg_end_date'=>$query_end,
            

            'jv_net_dt'=>$query_end,
            'cv_net_dt'=>$query_end,
            'crv_net_dt'=>$query_end,
            // 'beg_net_dt'=>$query_end,
        ];
        // return $this->init_sql($month,$year);  

        $cash_flow = DB::select($this->init_sql($month,$year),$params);

        // dd($cash_flow);

        $g = new GroupArrayController();
        $data['cash_flow']  = $g->array_group_by($cash_flow,['type','sub_type']);
        // dd($data);
        return $data;
    }

    public function init_sql($month,$year){
        $start_dts = array();
        $end_dts = array();
        $suf = "A";

        $current_year = date("Y", strtotime(MySession::current_date()));

        // return $current_year;
        for($i=0;$i<3;$i++){
            array_push($start_dts,($year-$i)."-01-01");
            if($current_year ==$year-$i && $i==0){
                array_push($end_dts,MySession::current_date());
            }else{
                array_push($end_dts,date("Y-m-t", strtotime(($year-$i)."-$month-01")));
            }
        }

        $col =array();

        for($i=0;$i<count($start_dts);$i++){
            // $c = "SUM(CASE WHEN date >= '".$start_dts[$i]."' AND date <= '".$end_dts[$i]."' THEN amount ELSE 0 END) as $suf";
            // $ = "SUM(CASE WHEN date <= '".$end_dts[$i]."' THEN amount ELSE 0 END) as $suf";
            $a = "SUM(CASE WHEN date <= '".$end_dts[$i]."' THEN amount ELSE 0 END)";
            $b = "SUM(CASE WHEN date >= '".$start_dts[$i]."' AND date <= '".$end_dts[$i]."' THEN amount ELSE 0 END)";

            $c = "if(inc_dec=0,$b,$a) as $suf";

            array_push($col,$c);
            $suf++;
        }

        array_push($col,"SUM(CASE WHEN  date < '".($year-1)."-01-01"."' THEN amount ELSE 0 END) as D");

        $col_imp = implode(", ",$col);

        $sql = "
        SELECT id_cash_flow,type,ifnull(sub_type,'') as sub_type,cash_flow_description,
        CASE 
            WHEN inc_dec =0 OR inc_dec = 3 THEN A
            WHEN inc_dec = 1 THEN A-B
            ELSE A-B END as 'A',
        CASE 
            WHEN inc_dec =0 OR inc_dec = 3 THEN B
            WHEN inc_dec = 1 THEN B-C
            ELSE B-C END as 'B',
        CASE 
             WHEN inc_dec=0 OR inc_dec = 3 THEN D
             WHEN inc_dec = 1 THEN D
             ELSE D END as 'D'
        FROM (
        SELECT *,$col_imp FROM (
        /**********JV**************/
        SELECT ca.id_cash_flow,cf.order as cash_flow_order,if(cf.type=1,'Operating Activities',if(cf.type=2,'Investing Activities','Financing Activities')) as type,cf.inc_dec,cf.sub_type,cf.description as cash_flow_description,jvd.id_chart_account,jv.date,ca.description as Account,credit,debit,
        (if(ca.normal=1,debit-credit,credit-debit)*if(id_chart_account_subtype=2,-1,1))*if(ca.id_chart_account_type in (1),-1,1)*if(ca.id_chart_account_type in (1,2) AND jv.cash=0,1,1) as amount
        FROM journal_voucher as jv
        LEFT JOIN journal_voucher_details as jvd on jvd.id_journal_voucher = jv.id_journal_voucher
        LEFT JOIN chart_account as ca on ca.id_chart_account = jvd.id_chart_account    
        LEFT JOIN cash_flow as cf on cf.id_cash_flow = ca.id_cash_flow 
        WHERE jv.status <> 10 and ca.id_cash_flow > 0 AND jv.date >= '2025-01-01' AND jv.date <= :jv_end_date
        UNION ALL
        /******CDV*********/
        SELECT ca.id_cash_flow,cf.order as cash_flow_order,if(cf.type=1,'Operating Activities',if(cf.type=2,'Investing Activities','Financing Activities')) as type,cf.inc_dec,cf.sub_type,cf.description as cash_flow_description,cvd.id_chart_account,cv.date,ca.description  as Account,credit,debit,
        (if(ca.normal=1,debit-credit,credit-debit)*if(id_chart_account_subtype=2,-1,1))*if(ca.id_chart_account_type in (1),-1,1) as amount
        FROM cash_disbursement as cv
        LEFT JOIN cash_disbursement_details as cvd on cvd.id_cash_disbursement = cv.id_cash_disbursement
        LEFT JOIN chart_account as ca on ca.id_chart_account = cvd.id_chart_account
        LEFT JOIN cash_flow as cf on cf.id_cash_flow = ca.id_cash_flow 
        WHERE cv.status <> 10 and ca.id_cash_flow > 0 AND cv.date >= '2025-01-01'  and cv.date <= :cv_end_date
        UNION ALL
        /*********CRV*********/ 
        SELECT ca.id_cash_flow,cf.order as cash_flow_order,if(cf.type=1,'Operating Activities',if(cf.type=2,'Investing Activities','Financing Activities')) as type,cf.inc_dec,cf.sub_type,cf.description as cash_flow_description,crvd.id_chart_account,crv.date,ca.description as Account,credit,debit,
        (if(ca.normal=1,debit-credit,credit-debit)*if(id_chart_account_subtype=2,-1,1))*if(ca.id_chart_account_type in (1),-1,1) as amount
        FROM cash_receipt_voucher as crv
        LEFT JOIN cash_receipt_voucher_details as crvd on crvd.id_cash_receipt_voucher = crv.id_cash_receipt_voucher
        LEFT JOIN chart_account as ca on ca.id_chart_account = crvd.id_chart_account
        LEFT JOIN cash_flow as cf on cf.id_cash_flow = ca.id_cash_flow 
        WHERE crv.status <> 10 and ca.id_cash_flow > 0 AND crv.date >= '2025-01-01' and crv.date <= :crv_end_date
        UNION ALL
        /*************BEG***********************/
        /**************
        SELECT ca.id_cash_flow,cf.order as cash_flow_order,if(cf.type=1,'Operating Activities',if(cf.type=2,'Investing Activities','Financingz Activities')) as type,cf.inc_dec,cf.sub_type,cf.description as cash_flow_description,chart_beginning.id_chart_account,date,ca.description  as Account,credit,debit,
        (if(ca.normal=1,debit-credit,credit-debit)*if(id_chart_account_subtype=2,-1,1))*if(ca.id_chart_account_type in (1,2),-1,1) as amount
        FROM chart_beginning 
        LEFT JOIN chart_account as ca on ca.id_chart_account = chart_beginning.id_chart_account
        LEFT JOIN cash_flow as cf on cf.id_cash_flow = ca.id_cash_flow 
        WHERE chart_beginning.status <> 10 and ca.id_cash_flow > 0 and chart_beginning.date <= :beg_end_date
        UNION ALL
        ********************/
        /**********FILL**************/

        SELECT ca.id_cash_flow,cf.order as cash_flow_order,if(cf.type=1,'Operating Activities',if(cf.type=2,'Investing Activities','Financing Activities')) as type,cf.inc_dec,cf.sub_type,cf.description as cash_flow_description,ca.id_chart_account,curdate(),ca.description as Account,0,0,0
        FROM chart_account as ca
        LEFT JOIN cash_flow as cf on cf.id_cash_flow = ca.id_cash_flow 
        WHERE  ca.id_cash_flow > 0

        /*********NET SURPLUS*************/
        UNION ALL
        SELECT cf.id_cash_flow,cf.order as cash_flow_order,if(cf.type=1,'Operating Activities',if(cf.type=2,'Investing Activities','Financing Activities')) as type,cf.inc_dec,cf.sub_type,cf.description as cash_flow_description,
        ca.id_chart_account,g.date as date,ca.description  as Account,
        0 as credit,0 as debit,
        SUM(if(g.normal=1,debit-credit,credit-debit)*if(g.id_chart_account_subtype=2,-1,1) * if(g.id_chart_account_type=4,1,-1)) as amount
        FROM (
        SELECT ca.normal,ca.id_chart_account_subtype,ca.id_chart_account_type,jv.date,credit,debit,YEAR(jv.date) as year,MONTH(jv.date) as month,ca.id_chart_account
        FROM journal_voucher as jv
        LEFT JOIN journal_voucher_details as jvd on jvd.id_journal_voucher = jv.id_journal_voucher
        LEFT JOIN chart_account as ca on ca.id_chart_account = jvd.id_chart_account
        LEFT JOIN chart_account_type as cat on cat.id_chart_account_type = ca.id_chart_account_type         
        WHERE jv.status <> 10 and cat.report_type = 2 AND jv.date >= '2025-01-01' AND jv.date <= :jv_net_dt
        UNION ALL
        /*************CV***************/
        SELECT ca.normal,ca.id_chart_account_subtype,ca.id_chart_account_type,cv.date,credit,debit,YEAR(cv.date) as year,MONTH(cv.date) as month,ca.id_chart_account
        FROM cash_disbursement as cv
        LEFT JOIN cash_disbursement_details as cvd on cvd.id_cash_disbursement = cv.id_cash_disbursement
        LEFT JOIN chart_account as ca on ca.id_chart_account = cvd.id_chart_account
        LEFT JOIN chart_account_type as cat on cat.id_chart_account_type = ca.id_chart_account_type         
        WHERE cv.status <> 10 and cat.report_type = 2 AND cv.date >= '2025-01-01' AND  cv.date <= :cv_net_dt
        UNION ALL
        /*************CRV***************/
        SELECT ca.normal,ca.id_chart_account_subtype,ca.id_chart_account_type,crv.date,credit,debit,YEAR(crv.date) as year,MONTH(crv.date) as month,ca.id_chart_account
        FROM cash_receipt_voucher as crv
        LEFT JOIN cash_receipt_voucher_details as crvd on crvd.id_cash_receipt_voucher = crv.id_cash_receipt_voucher
        LEFT JOIN chart_account as ca on ca.id_chart_account = crvd.id_chart_account
        LEFT JOIN chart_account_type as cat on cat.id_chart_account_type = ca.id_chart_account_type         
        WHERE crv.status <> 10 and cat.report_type = 2 AND crv.date >= '2025-01-01' AND crv.date <= :crv_net_dt

        -- UNION ALL
        -- /*************BEGINNING***************/
        -- SELECT ca.normal,ca.id_chart_account_subtype,ca.id_chart_account_type,date,credit,debit,YEAR(date) as year,MONTH(date) as month,ca.id_chart_account
        -- FROM chart_beginning 
        -- LEFT JOIN chart_account as ca on ca.id_chart_account = chart_beginning.id_chart_account
        -- LEFT JOIN chart_account_type as cat on cat.id_chart_account_type = ca.id_chart_account_type         
        -- WHERE cat.report_type = 2 AND date <= :beg_net_dt and chart_beginning.status <> 10

        ) 
        as g
        LEFT JOIN chart_account as ca on ca.id_chart_account = 34
        LEFT JOIN cash_flow as cf on cf.id_cash_flow = ca.id_cash_flow
        GROUP BY g.year,g.month
        /*************CASH FLOW BEGINNING*********************/
        /**************
        UNION ALL
        select cf.id_cash_flow,if(cf.type=1,'Operating Activities',if(cf.type=2,'Investing Activities','Financing Activities')) as type,
        cf.inc_dec,cf.sub_type,cf.description as cash_flow_description,0,cfb.date,'' as Account,0 as credit,0 as debit,cfb.amount
        FROM cash_flow_beg as cfb
        LEFT JOIN cash_flow as cf on cf.id_cash_flow = cfb.id_cash_flow
        *****************************/

        ) as cash_flow
        GROUP BY id_cash_flow
        ORDER BY cash_flow_order
    ) as cash_flow_f;";

        return $sql;

    }

    public function export(Request $request){
        $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/financial_statement/comparative');
        if(!$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }   
        $data = $this->parseData($request);

        $data['file_name'] = $data['mod_title']." - ".$data['year'];
        if($data['export_type'] == 2){
            return Excel::download(new FSExport($data,2), $data['file_name'].".xlsx");
        }
        $html =  view('cash_flow.export',$data);


        $pdf = PDF::loadHtml($html);
        $pdf->setOption("encoding","UTF-8");
        $pdf->setOption('margin-bottom', '0.39in');
        $pdf->setOption('margin-top', '0.4in');
        $pdf->setOption('margin-right', '0.33in');
        $pdf->setOption('margin-left', '0.33in');
        $pdf->setOption('header-left', 'Page [page] of [toPage]');
    
        $pdf->setOption('header-font-size', 8);
        $pdf->setOption('header-font-name', 'Calibri');
        // $pdf->setPaper('Legal');
        
        $pdf->setOption('page-width','215.9mm');
        $pdf->setOption('page-height','330.2mm');

        return $pdf->stream();        
    }
}








