<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\MySession;
use App\CredentialModel;
use PDF;

use Excel;
use App\Exports\FSExport;

class ChangesEquityController extends Controller
{
    public function index(Request $request){
        $data['credential']= CredentialModel::GetCredential(MySession::myPrivilegeId());
        if(!$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }    
        $data = $this->parseData($request);



            
        // return $data;


        return view('changes_equity.index',$data);
    }
    public function parseData($request){

        $data['current_year'] = date("Y", strtotime(MySession::current_date()));        
        $data['current_month'] = date("n", strtotime(MySession::current_date()));
        
        $data['type'] = 2;
        $data['year'] = $request->year ?? $data['current_year'];

        $data['year'] = ((int)$data['year']==0)?$data['current_year']:$data['year'];

        $data['mod_title'] = "Changes in Equity";
        $data['end_year'] = $data['year'];

        $data['export_type'] = $request->export_type ?? 1;

        $start_date = ($data['year'] == env('BEGINNING_YEAR'))?env('BEGINNING_DATE'):$data['year']."-01-01";
        $end_date =$data['year']."-12-31";

        $data['head_label']['A'] = $data['year'];
        $data['head_label']['B'] = $data['year']-1;


        $start_prev = ($data['year']-1)."-01-01";
        $end_prev = ($data['year']-1)."-12-31";

        $sql="SELECT if(ca.iscbu > 0,'SHARE CAPITAL','STATUTORY FUND') as `groups`,ca.description,if(ca.iscbu > 1,ca.iscbu,ca.id_chart_account) as cc,
                SUM(CASE WHEN date <= '$start_date' THEN (credit-debit) ELSE 0 END) as current_beg,
                SUM(CASE WHEN date > '$start_date' AND date <= '$end_date' THEN (if(ca.iscbu > 0 OR net_surplus=1,credit,0) ) ELSE 0 END) as current_add,
                SUM(CASE WHEN date >= '$start_date' AND date <= '$end_date' THEN (if(ca.iscbu > 0,debit,if(net_surplus=0,credit-debit,0)) )  ELSE 0 END) as current_adj,
                SUM(credit-debit) as current_total,

                SUM(CASE WHEN date < '$start_prev' THEN (credit-debit) ELSE 0 END) as prev_beg,
                SUM(CASE WHEN date >= '$start_prev' AND date <= '$end_prev' THEN (if(ca.iscbu > 0 OR net_surplus=1,credit,0) ) ELSE 0 END) as prev_add,
                SUM(CASE WHEN date >= '$start_prev' AND date <= '$end_prev' THEN (if(ca.iscbu > 0,debit,if(net_surplus=0,credit-debit,0)) )  ELSE 0 END) as prev_adj,
                SUM(CASE WHEN date <= '$end_prev' THEN (credit-debit) ELSE 0 END) as prev_total,
                SUM(credit-debit) as current_total
                
                FROM (
                SELECT cd.date,cd.id_cash_disbursement,cdd.id_chart_account,cdd.description,cdd.debit,cdd.credit,0 as net_surplus
                FROM cash_disbursement as cd
                LEFT JOIN cash_disbursement_details as cdd on cdd.id_cash_disbursement = cd.id_cash_disbursement
                LEFT JOIN chart_account as ca on ca.id_chart_account = cdd.id_chart_account
                WHERE  status <> 10 AND (ca.iscbu > 0 OR ca.id_chart_account_line_item = 6)
                UNION ALL
                SELECT jv.date,jv.id_journal_voucher,jvd.id_chart_account,jvd.description,jvd.debit,jvd.credit,if(jvd2.id_journal_voucher is not null,1,0) as net_surplus
                FROM journal_voucher as jv
                LEFT JOIN journal_voucher_details as jvd on jvd.id_journal_voucher = jv.id_journal_voucher
                LEFT JOIN chart_account as ca on ca.id_chart_account = jvd.id_chart_account
                LEFT JOIN journal_voucher_details as jvd2 on jvd2.id_journal_voucher = jv.id_journal_voucher AND jvd2.id_chart_account = 34
                WHERE  status <> 10 and (ca.iscbu > 0 OR ca.id_chart_account_line_item = 6)
                UNION ALL
                SELECT crv.date,crv.id_cash_receipt_voucher,crvd.id_chart_account,crvd.description,crvd.debit,crvd.credit,0 as net_surplus
                FROM cash_receipt_voucher as crv
                LEFT JOIN cash_receipt_voucher_details as crvd on crvd.id_cash_receipt_voucher = crv.id_cash_receipt_voucher
                LEFT JOIN chart_account as ca on ca.id_chart_account = crvd.id_chart_account
                WHERE  status <> 10  AND (ca.iscbu > 0 OR ca.id_chart_account_line_item = 6)
                UNION ALL
                SELECT date,id_chart_beginning,beg.id_chart_account,'BEG',debit,credit,0 as net_surplus
                FROM chart_beginning as beg
                LEFT JOIN chart_account as ca on ca.id_chart_account = beg.id_chart_account
                WHERE  (ca.iscbu > 0 OR ca.id_chart_account_line_item = 6) and status <> 10
                ) as k
                LEFT JOIN chart_account as ca on ca.id_chart_account = k.id_chart_account
                GROUP BY cc;";

   

        $g = new GroupArrayController();

        $data['equity'] = $g->array_group_by(DB::select($sql),['groups']);

        return $data;
    }

    public function export(Request $request){
        $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/financial_statement/comparative');
        if(!$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }    
        $data = $this->parseData($request);

        $data['file_name'] = $data['mod_title']." - ".$data['year'];
        
        if($data['export_type'] == 2){
            return Excel::download(new FSExport($data,3), $data['file_name'].".xlsx");
        }
        $html =  view('changes_equity.export',$data);

        // return $html;
        $pdf = PDF::loadHtml($html);
        $pdf->setOption("encoding","UTF-8");
        // $pdf->setOption('margin-bottom', '0.7480in');
        // $pdf->setOption('margin-top', '0.7480in');
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
// date >= '$start_date' and