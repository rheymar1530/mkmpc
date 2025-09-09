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
class TransactionSummaryController extends Controller
{
    public function index(Request $request){

        // return MySession::myPrivilegeId();
        $credential= CredentialModel::GetCredential(MySession::myPrivilegeId());
        if(!$credential->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
        $data =$this->parseData($request);

        // dd($request->all());
        $data['head_title'] = "Voucher Summary";
        // return $data;
        $data['credential'] = $credential;

        // return $data;

        return view('transaction_summary.index',$data);
        return $data;
    }
    public function parseData(Request $request){
        $data = array();

        $data['show_cancel'] = $request->show_cancel ?? 0;

        // return $data['show_cancel'];
        $data['start_date'] = $request->start_date ?? MySession::current_date();
        $data['end_date'] = $request->end_date ?? MySession::current_date();
        $data['selected_type'] = $request->type ?? 3;
        $data['export_type'] = $request->export_type ?? 1;
        $g = new GroupArrayController();
        if(isset($request->books)){
            $books = json_decode($request->books,true);
        }else{
            $books = [];
        }

        $data['books'] = $books;
        $data['transactions'] = array();

        $data['pay_to_from'] = $request->pay_to_from ?? 0;

        $data['ref'] = $request->reference ?? 0;
        switch($data['pay_to_from']){
            case   '1':
            $data['selected_reference_payee'] = DB::table('supplier')->select('id_supplier as id','name')->where('id_supplier',$data['ref'])->first();

            $data['rpt_description'] = explode(" || ",$data['selected_reference_payee']->name)[1]." - Supplier";

            break;
            case   '2':
            $data['selected_reference_payee'] =DB::table('member as m')
            ->select(DB::raw("concat(membership_id,' || ',FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)) as name,id_member as id"))
            ->where('id_member',$data['ref'])
            ->first();
            $data['rpt_description'] = explode(" || ",$data['selected_reference_payee']->name)[1]." - Member";


            break;
            case   '3':
            $data['selected_reference_payee'] =DB::table('employee as e')
            ->select(DB::raw("concat(id_employee,' || ',FormatName(e.first_name,e.middle_name,e.last_name,e.suffix)) as name,id_employee as id"))
            ->where('id_employee',$data['ref'])
            ->first();

            $data['rpt_description'] = explode(" || ",$data['selected_reference_payee']->name)[1]." - Employee";
            break;

            case '4':

            $data['rpt_description'] = "Others";
            break;

            default:

                // $data['selected_emploee']
        }

        foreach($data['books'] as $b){
            if($data['selected_type'] == 1){
                $trans = $this->GetTransaction($b,$data['start_date'],$data['end_date'],$data['show_cancel']);
            }elseif($data['selected_type'] == 2){
                $trans = $this->GetTransactionEntry($b,$data['start_date'],$data['end_date'],$data['show_cancel'],$data['pay_to_from'],$data['ref']);
            }elseif($data['selected_type'] == 3){

                $trans = $this->getTransactionAccount($b,$data['start_date'],$data['end_date'],$data['pay_to_from'],$data['ref'],$data['show_cancel']);
            }
            
            $temp = [];

            foreach($trans as $key=>$obj){
                $temp[$key] = $obj;
            }

            if($data['selected_type'] == 1){
                $data['transactions'][$trans['description']]= $trans['transactions']; 
            }else if($data['selected_type'] == 2){
                $data['transactions'][$trans['description']]= $g->array_group_by($trans['transactions'],['reference']); 
            }else{
                $data['transactions'][$trans['description']]= $g->array_group_by($trans['transactions'],['account_code']); 
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
                $sql="SELECT DATE_FORMAT(date,'%m/%d/%Y') as date,jt.description as type,payee,concat(if(jv.type=1,concat('[',if(jv.jv_type=1,'NORMAL',if(jv.jv_type=2,'REVERSAL','ADJUSTMENT')),'] '),''),jv.description ) as description,concat('JV# ',jv.id_journal_voucher) as reference,if(jv.status=10,0,total_amount) as amount,if(jv.status=10,concat('[',FORMAT(jv.total_amount,2),'] [CANCELLED] [',CONVERT(jv.cancellation_reason USING utf8),']'),'') as remarks,jv.status
                FROM journal_voucher as jv
                LEFT JOIN jv_type as jt on jt.id_jv_type = jv.type
                WHERE date >= ? and date <= ? $can ORDER BY jv.id_journal_voucher ASC;";
                $description = "Journal Voucher";
                break;

            case 2:
                $can = ($cancel==0)?"AND cdv.status <> 10":"";
                $sql="SELECT DATE_FORMAT(date,'%m/%d/%Y') as date,ct.description as type,payee,concat(if(cdv.type in (2,3,4),concat('[',UPPER(ct.description),'] '),''),cdv.description) as description,concat('CDV# ',cdv.id_cash_disbursement) as reference,if(cdv.status=10,0,total) as amount,if(cdv.status=10,concat('[',FORMAT(cdv.total,2),'] [CANCELLED] [',CONVERT(cdv.cancellation_reason USING utf8),']'),'') as remarks,cdv.status
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


    public function GetTransactionEntry($type,$date_start,$date_end,$cancel,$pay_to_from,$ref){
        $description = "";

        $reference_query = "";

        if($pay_to_from == 1){
            $reference_query = ".id_supplier = ?";


        }elseif($pay_to_from == 2){
            $reference_query = ".id_member = ?";

        }elseif($pay_to_from == 3){
            $reference_query = ".id_employee = ?";
        }else{
            $reference_query = ".payee_type =4";
        }




        switch($type){
            case   1:
                $can = ($cancel==0)?"AND jv.status <> 10":"";

                $sql="SELECT DATE_FORMAT(date,'%m/%d/%Y') as date,jt.description as type,payee,jv.description as entry_description,concat('JV# ',jv.id_journal_voucher) as reference,if(jv.status=10,0,total_amount) as amount,if(jv.status=10,concat('[',FORMAT(jv.total_amount,2),'] [CANCELLED] [',CONVERT(jv.cancellation_reason USING utf8),']'),'') as remarks,
                concat(ca.account_code,' - ',ca.description) as account,ca.account_code,ca.description as account_name,jvd.details,jvd.debit,jvd.credit,jv.status
                FROM journal_voucher as jv
                LEFT JOIN jv_type as jt on jt.id_jv_type = jv.type
                LEFT JOIN journal_voucher_details as jvd on jvd.id_journal_voucher = jv.id_journal_voucher
                LEFT JOIN chart_account as ca on ca.id_chart_account = jvd.id_chart_account
                WHERE date >= ? and date <=  ? $can ".(($pay_to_from > 0)?"AND jv$reference_query":"")." ORDER BY jv.id_journal_voucher ASC";
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
                    WHERE date >= ? and date <= ? $can ".(($pay_to_from > 0)?"AND cdv$reference_query":"")." ORDER BY cdv.id_cash_disbursement ASC";
                $description = "Cash Disbursement Voucher";
                break;

            case 3:
                $can = ($cancel==0)?"AND crv.status <> 10":"";
                $sql="SELECT DATE_FORMAT(date,'%m/%d/%Y') as date,ct.description as type,payee,crv.description as entry_description,concat('CRV# ',crv.id_cash_receipt_voucher) as reference,if(crv.status=10,0,total_amount) as amount,if(crv.status=10,concat('[',FORMAT(crv.total_amount,2),'] [CANCELLED] [',CONVERT(crv.cancellation_reason USING utf8),']'),'') as remarks,concat(ca.account_code,' - ',ca.description) as account,ca.account_code,ca.description as account_name,crvd.details,crvd.debit,crvd.credit,crv.or_no,crv.status
                    FROM cash_receipt_voucher as crv
                    LEFT JOIN crv_type as ct on ct.id_crv_type = crv.type
                    LEFT JOIN cash_receipt_voucher_details as crvd on crvd.id_cash_receipt_voucher = crv.id_cash_receipt_voucher
                    LEFT JOIN chart_account as ca on ca.id_chart_account = crvd.id_chart_account
                    WHERE date >= ? and date <= ? $can ".(($pay_to_from > 0)?"AND crv$reference_query":"")." ORDER BY crv.id_cash_receipt_voucher ASC;";
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
            $param = [$date_start,$date_end];

            if($pay_to_from > 0 && $pay_to_from < 4){
                array_push($param,$ref);
            }
            $output['transactions'] = DB::select($sql,$param);
        }

        return $output;        
    }


    public function getTransactionAccount($type,$date_start,$date_end,$pay_to_from,$ref,$cancel){


        $description = "";
        $reference_query = "";

        if($pay_to_from == 1){
            $reference_query = ".id_supplier = ?";


        }elseif($pay_to_from == 2){
            $reference_query = ".id_member = ?";

        }elseif($pay_to_from == 3){
            $reference_query = ".id_employee = ?";
        }else{
            $reference_query = ".payee_type =4";
        }
        switch($type){
            case 1 : //JV
                $can = ($cancel==0)?"AND jv.status <> 10":"";


                $sql="SELECT 
                jv.id_journal_voucher,concat(ca.account_code,' - ',ca.description) as account,ca.account_code,ca.description as account_name,SUM(jvd.debit) as debit,SUM(jvd.credit) as credit,
                if(jvd.id_chart_account=5 AND jv.type=2,concat('L',ls.id_loan_service),jvd.id_chart_account) as groupings,
                if(jvd.id_chart_account=5,ls.name,'') as reference
                FROM journal_voucher as jv
                LEFT JOIN jv_type as ct on ct.id_jv_type = jv.type
                LEFT JOIN journal_voucher_details as jvd on jvd.id_journal_voucher = jv.id_journal_voucher
                LEFT JOIN chart_account as ca on ca.id_chart_account = jvd.id_chart_account
                LEFT JOIN loan on loan.id_loan =  if(jvd.id_chart_account=5 AND jv.type=2,jvd.reference,0)
                LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service   
                WHERE date >= ? and date <= ? $can ".(($pay_to_from > 0)?"AND jv$reference_query":"")."
                GROUP BY groupings
                ORDER BY ca.account_code ASC,reference;";

                $description = "Journal Voucher";
            break;
            case 2 : //CDV
                $can = ($cancel==0)?"AND cdv.status <> 10":"";
                $sql="SELECT 
                concat(ca.account_code,' - ',ca.description) as account,ca.account_code,ca.description as account_name,cdvd.details,SUM(cdvd.debit) as debit,SUM(cdvd.credit) as credit,
                CASE 
                WHEN cdvd.id_chart_account=5 AND cdv.type=1 THEN concat('L',ifnull(loan.id_loan_service,'Others'))
                WHEN cdvd.id_chart_account=5 AND cdv.type=4 THEN 'CDV_OTHERS'
                ELSE cdvd.id_chart_account END as groupings,
                CASE 
                WHEN cdvd.id_chart_account=5 AND cdv.type=1 THEN ifnull(ls.name,'Others (Loan Offset)')
                WHEN cdvd.id_chart_account=5 AND cdv.type=4 THEN 'Others (CDV)'
                ELSE '' END as reference
                FROM cash_disbursement as cdv
                LEFT JOIN cdv_type as ct on ct.id_cdv_type = cdv.type
                LEFT JOIN cash_disbursement_details as cdvd on cdvd.id_cash_disbursement = cdv.id_cash_disbursement
                LEFT JOIN chart_account as ca on ca.id_chart_account = cdvd.id_chart_account
                LEFT JOIN loan on loan.id_loan = if(cdvd.id_chart_account=5 AND cdv.type=1,cdvd.reference,0)
                LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
                WHERE date >= ? and date <= ? $can ".(($pay_to_from > 0)?"AND cdv$reference_query":"")."
                GROUP BY groupings
                ORDER BY ca.account_code ASC,reference;";


        
                $description = "Cash Disbursement Voucher";
            break;
            case 3 : //CRV
                $can = ($cancel==0)?"AND crv.status <> 10":"";
                $sql="SELECT 
                crv.id_cash_receipt_voucher,concat(ca.account_code,' - ',ca.description) as account,ca.account_code,ca.description as account_name,SUM(crvd.debit) as debit,SUM(crvd.credit) as credit,
                    CASE 
                    WHEN crvd.id_chart_account=5 AND crv.type=2 THEN concat('L',ifnull(loan.id_loan_service,'Others'))
                    WHEN crvd.id_chart_account=5 AND crv.type=1 THEN 'OT'
                    ELSE crvd.id_chart_account END as groupings,
                    CASE 
                    WHEN crvd.id_chart_account=5 AND crv.type=2 THEN ifnull(ls.name,'Others')
                    WHEN crvd.id_chart_account=5 AND crv.type=1 THEN concat('Others - ',ct.description)
                    ELSE '' END as reference
                FROM cash_receipt_voucher as crv
                LEFT JOIN crv_type as ct on ct.id_crv_type = crv.type
                LEFT JOIN cash_receipt_voucher_details as crvd on crvd.id_cash_receipt_voucher = crv.id_cash_receipt_voucher
                LEFT JOIN chart_account as ca on ca.id_chart_account = crvd.id_chart_account
                LEFT JOIN loan on loan.id_loan =  if(crvd.id_chart_account=5 AND crv.type=2,crvd.reference,0)
                LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
                WHERE date >= ? and date <= ? $can ".(($pay_to_from > 0)?"AND crv$reference_query":"")."
                GROUP BY groupings
                ORDER BY ca.account_code ASC,reference;";



                $description = "Cash Receipt Voucher";
            break;

            default:
                $sql="";
                break;
        }
        
        // return $sql;
        $output = array();
        $output['description'] = $description;

        $output['transactions'] = array();
        if($sql != ""){
            $param = [$date_start,$date_end];

            if($pay_to_from > 0 && $pay_to_from < 4){
                array_push($param,$ref);
            }
            
            $output['transactions'] = DB::select($sql,$param);
        }

        return $output;
    }

    public function export(Request $request){
        $data = $this->parseData($request);

        if($data['export_type'] == 2){
            $data['file_name'] = "Voucher Summary ".$data['date'];
            return Excel::download(new VoucherExport($data,2), $data['file_name'].".xlsx");
        }

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

        if($data['selected_type'] < 3){
            $pdf->setOrientation('landscape');
        }
        

        return $pdf->stream();




        return $data;
    }
}










