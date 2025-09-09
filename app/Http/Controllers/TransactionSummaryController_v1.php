<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\MySession;
use App\WebHelper;
use PDF;

class TransactionSummaryController extends Controller
{
    public function index(Request $request){

        $data =$this->parseData($request);

        return view('transaction_summary.index',$data);

        return $data;
    }
    public function parseData(Request $request){
        $data = array();

        $data['show_cancel'] = $request->show_cancel ?? 0;

        // return $data['show_cancel'];
        $data['start_date'] = $request->start_date ?? MySession::current_date();
        $data['end_date'] = $request->end_date ?? MySession::current_date();
        $data['selected_type'] = $request->type ?? 2;
        $g = new GroupArrayController();
        if(isset($request->books)){
            $books = json_decode($request->books,true);
        }else{
            $books = [];
        }

        $data['books'] = $books;
        $data['transactions'] = array();

        foreach($data['books'] as $b){
            if($data['selected_type'] == 1){
                $trans = $this->GetTransaction($b,$data['start_date'],$data['end_date'],$data['show_cancel']);
            }else{
                $trans = $this->GetTransactionEntry($b,$data['start_date'],$data['end_date'],$data['show_cancel']);
            }
            
            $temp = [];

            foreach($trans as $key=>$obj){
                $temp[$key] = $obj;
            }

            if($data['selected_type'] == 1){
                $data['transactions'][$trans['description']]= $trans['transactions']; 
            }else{
                $data['transactions'][$trans['description']]= $g->array_group_by($trans['transactions'],['reference']); 
            }
        }

        //DATE ON HEADER FORMAT
        $data['date'] = WebHelper::ReportDateFormatter($data['start_date'],$data['end_date']);


        return $data;        
    }

    public function GetTransaction($type,$date_start,$date_end,$cancel){
        $description = "";
        switch($type){
            case   1:

                $can = ($cancel==0)?"AND jv.status <> 10":"";
                $sql="SELECT DATE_FORMAT(date,'%m/%d/%Y') as date,jt.description as type,payee,jv.description,concat('JV# ',jv.id_journal_voucher) as reference,if(jv.status=10,0,total_amount) as amount,if(jv.status=10,concat('[',FORMAT(jv.total_amount,2),'] [CANCELLED] [',CONVERT(jv.cancellation_reason USING utf8),']'),'') as remarks,jv.status
                FROM journal_voucher as jv
                LEFT JOIN jv_type as jt on jt.id_jv_type = jv.type
                WHERE date >= ? and date <= ? $can ORDER BY jv.id_journal_voucher ASC;";
                $description = "Journal Voucher";
                break;

            case 2:
                $can = ($cancel==0)?"AND cdv.status <> 10":"";
                $sql="SELECT DATE_FORMAT(date,'%m/%d/%Y') as date,ct.description as type,payee,cdv.description,concat('CDV# ',cdv.id_cash_disbursement) as reference,if(cdv.status=10,0,total) as amount,if(cdv.status=10,concat('[',FORMAT(cdv.total,2),'] [CANCELLED] [',CONVERT(cdv.cancellation_reason USING utf8),']'),'') as remarks,cdv.status
                        FROM cash_disbursement as cdv
                        LEFT JOIN cdv_type as ct on ct.id_cdv_type = cdv.type
                        WHERE date >= ? and date <= ? $can ORDER BY cdv.id_cash_disbursement ASC";
                $description = "Cash Disbursement Voucher";
                break;

            case 3:
                // ifnull(concat(' / OR# ',crv.or_no),'')
                $can = ($cancel==0)?"AND crv.status <> 10":"";
                $sql="SELECT DATE_FORMAT(date,'%m/%d/%Y') as date,ct.description as type,payee,crv.description,concat('CRV# ',crv.id_cash_receipt_voucher) as reference,if(crv.status=10,0,total_amount) as amount,if(crv.status=10,concat('[',FORMAT(crv.total_amount,2),'] [CANCELLED] [',CONVERT(crv.cancellation_reason USING utf8),']'),'') as remarks,crv.or_no,crv.status
                    FROM cash_receipt_voucher as crv
                    LEFT JOIN crv_type as ct on ct.id_crv_type = crv.type
                    WHERE date >= ? and date <= ? $can ORDER BY crv.id_cash_receipt_voucher ASC;";
                $description = "Cash Receipt Voucher";
                break;
            default:
                $sql ="";
                break;
        }
        $output = array();
        $output['description'] = $description;

        $output['transactions'] = array();
        if($sql != ""){
            $output['transactions'] = DB::select($sql,[$date_start,$date_end]);
        }

        return $output;
    }


    public function GetTransactionEntry($type,$date_start,$date_end,$cancel){
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
                WHERE date >= ? and date <=  ? $can ORDER BY jv.id_journal_voucher ASC";
                $description = "Journal Voucher";
                break;

            case 2:
                    $can = ($cancel==0)?"AND cdv.status <> 10":"";
                    $sql="SELECT DATE_FORMAT(date,'%m/%d/%Y') as date,ct.description as type,payee,cdv.description as entry_description,concat('CDV# ',cdv.id_cash_disbursement) as reference,if(cdv.status=10,0,total) as amount,if(cdv.status=10,concat('[',FORMAT(cdv.total,2),'] [CANCELLED] [',CONVERT(cdv.cancellation_reason USING utf8),']'),'') as remarks,
                    concat(ca.account_code,' - ',ca.description) as account,ca.account_code,ca.description as account_name,cdvd.details,cdvd.debit,cdvd.credit,cdv.status
                    FROM cash_disbursement as cdv
                    LEFT JOIN cdv_type as ct on ct.id_cdv_type = cdv.type
                    LEFT JOIN cash_disbursement_details as cdvd on cdvd.id_cash_disbursement = cdv.id_cash_disbursement
                    LEFT JOIN chart_account as ca on ca.id_chart_account = cdvd.id_chart_account
                    WHERE date >= ? and date <= ? $can ORDER BY cdv.id_cash_disbursement ASC";
                $description = "Cash Disbursement Voucher";
                break;

            case 3:
                $can = ($cancel==0)?"AND crv.status <> 10":"";
                $sql="SELECT DATE_FORMAT(date,'%m/%d/%Y') as date,ct.description as type,payee,crv.description as entry_description,concat('CRV# ',crv.id_cash_receipt_voucher) as reference,if(crv.status=10,0,total_amount) as amount,if(crv.status=10,concat('[',FORMAT(crv.total_amount,2),'] [CANCELLED] [',CONVERT(crv.cancellation_reason USING utf8),']'),'') as remarks,concat(ca.account_code,' - ',ca.description) as account,ca.account_code,ca.description as account_name,crvd.details,crvd.debit,crvd.credit,crv.or_no,crv.status
                    FROM cash_receipt_voucher as crv
                    LEFT JOIN crv_type as ct on ct.id_crv_type = crv.type
                    LEFT JOIN cash_receipt_voucher_details as crvd on crvd.id_cash_receipt_voucher = crv.id_cash_receipt_voucher
                    LEFT JOIN chart_account as ca on ca.id_chart_account = crvd.id_chart_account
                    WHERE date >= ? and date <= ? $can ORDER BY crv.id_cash_receipt_voucher ASC;";
                $description = "Cash Receipt Voucher";
                break;
            default:
                $sql ="";
                break;
        }
        $output = array();
        $output['description'] = $description;

        $output['transactions'] = array();
        if($sql != ""){
            $output['transactions'] = DB::select($sql,[$date_start,$date_end]);
        }

        return $output;        
    }

    public function export(Request $request){
        $data = $this->parseData($request);


        // return $data;
        $html =  view('transaction_summary.export',$data);

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
        $pdf->setOrientation('landscape');

        return $pdf->stream();




        return $data;
    }
}










