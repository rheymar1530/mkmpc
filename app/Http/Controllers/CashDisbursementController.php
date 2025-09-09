<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Member;
use App\Loan;
use App\MySession;
use Dompdf\Dompdf;
use App\CredentialModel;
use Storage;

use PDF;

class CashDisbursementController extends Controller
{
    private $cdv_files_path = '/uploads/cdv_attachments/';
    public function index($type,Request $request){
        $data['route'] = $type;
        if($type == "expenses"){
            $data['title_mod'] = "Expenses";
            $data['alt_title'] = "Expense";   
            $cdv_type = 2; 
        }elseif($type=="asset_purchase"){
            $data['title_mod'] = "Asset Purchase";
            $data['alt_title'] = "Asset";
            $cdv_type = 3;
        }elseif($type == "others"){
            $data['title_mod'] = "Cash Disbursement (Others)";
            $data['alt_title'] = "Others";
            $cdv_type = 4;
        }else{
            abort(404);
        }

        $data['head_title'] = $data['title_mod'];
        $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/cdv/'.$type);
        if(!$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }

        // return $data;
        $start = date('Y-m-d', strtotime('-30 days'));
        $end = MySession::current_date();

        $data['date_from'] = $request->date_from ?? $start;
        $data['date_to'] = $request->date_to ?? $end;
        $data['fil_type'] = $request->fil_type ?? 1;
        $data['cdv_search'] = $request->cdv_search;


        // return $data;
        $data['cdv_list'] = DB::table('cash_disbursement as cdv')
        ->select(DB::raw("cdv.id_cash_disbursement,DATE_FORMAT(cdv.date,'%m/%d/%Y') as date,
            CASE 
            WHEN cdv.payee_type = 1 THEN 'Supplier'
            WHEN cdv.payee_type = 2 THEN 'Member'
            WHEN cdv.payee_type = 3 THEN 'Employee'
            WHEN cdv.payee_type = 4 THEN 'Others'
            ELSE '' END as payee_type,
            cdv.payee,cdv.reference,
            cdv.description,cdv.total,cdv.status
            "))
        ->where('cdv.type',$cdv_type)
        ->where(function($query) use($data){
            if($data['fil_type'] == 1){
                $query->where('cdv.date','>=',$data['date_from'])
                ->where('cdv.date','<=',$data['date_to']);
            }elseif($data['fil_type'] == 2){
                $query->where('cdv.id_cash_disbursement','=',$data['cdv_search']);
            }
        })
        ->orDerby('id_cash_disbursement','DESC')
        ->get();
        return view('cash_disbursement.index',$data);
        return $data;
    }
    public function cdv_type_data($type){
        if($type == "expenses"){
            $data['title_mod'] = "Expenses";
            $data['alt_title'] = "Expenses";

            $data['entry_table'] = false;
            $data['charts'] = DB::table('chart_account')
            ->select('id_chart_account','account_code','description')
            ->where('id_chart_account_type',5)
            ->get();            
        }elseif($type=="asset_purchase"){
            $data['title_mod'] = "Asset Purchase";
            $data['alt_title'] = "Asset Purchase";
            $data['entry_table'] = false;
            $data['charts'] = DB::table('chart_account')
            ->select('id_chart_account','account_code','description')
            ->where('id_chart_account_type',1)
            ->get();               
        }elseif($type == "others"){
            $data['title_mod'] = "CDV (Others)";
            $data['alt_title'] = "CDV (Others)"; 
            $data['entry_table'] = false;  
            $data['charts'] = DB::table('chart_account')
            ->select('id_chart_account','account_code','description')
            ->get();            
        }else{
            abort(404);
        }
        $data['route'] = $type;
        $data['branches'] = DB::table('tbl_branch')->get();
        $data['chart_cash'] =  DB::table('chart_account')
        ->select('id_chart_account','account_code','description')
        ->whereIn('id_chart_account_category',[1,2])
        ->get();
        $data['chart_check'] =  DB::table('chart_account')
        ->select('id_chart_account','account_code','description')
        ->where('id_chart_account_category',2)
        ->get();            
        $data['current_date'] = MySession::current_date(); 
        return $data;
    }
    public function create($type){
        // return Loan::GenerateCDV(34);
        $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/cdv/'.$type);
        if(!$data['credential']->is_create){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
        $data = $this->cdv_type_data($type);
        $data['allow_post'] = 1;
        $data['opcode'] = 0;

        $data['head_title'] = "Create CDV - ".$data['title_mod'];


        // dd($data);

        return view("cash_disbursement.cdv_form",$data);
    }
    public function post($type,Request $request){
        // if($request->ajax()){
        $e_parent = json_decode($request->entry_parent,true);



        $attachment_length = $request->attachment_length;
        $entry_parent = array();
        $opcode = $request->opcode;
        $id_cash_disbursement = $request->id_cash_disbursement ?? 0;



        $key_reference ='';
        switch($e_parent['payee_type']){
            case '1':
            $key_reference = "id_supplier";
            break;
            case '2':
            $key_reference = "id_member";
            break;
            case '3':
            $key_reference = "id_employee";
            break;
            default:
        }
        switch($type){
            case 'expenses':
            $cdv_type = 2;
            break;
            case 'asset_purchase':
            $cdv_type = 3;
            break;
            case 'others':
            $cdv_type = 4;
            break;
            default:
        }

        if($opcode == 1){
            $count_validator = 0;
            if($type == 'others'){
                $count_validator = DB::table('payroll_ca')
                            ->leftJoin('payroll as p','p.id_payroll','payroll_ca.id_payroll')
                            ->where('payroll_ca.id_cash_disbursement',$id_cash_disbursement)
                            ->where('status','<>',10)
                            ->count();
                $error_message = "Cash advance is already deducted in payroll";


            }elseif($type == "asset_purchase"){
                $count_validator = DB::table('asset')
                            ->where('id_cash_disbursement',$id_cash_disbursement)
                            ->where('status','<>',10)
                            ->count();
                $error_message = "Asset Cash Disbursement is already in inventory";
            }     
            if($count_validator > 0){
                $data['RESPONSE_CODE'] = "ERROR";
                $data['message'] = $error_message;

                return response($data);
            }       
        }


        $entry_parent = [
            'id_member'=>0,
            'id_supplier'=>0,
            'id_employee'=>0,
            'date'   => $e_parent['date'],
            'payee_type' => $e_parent['payee_type'],
            'description' => $e_parent['description'],
            'type' => $cdv_type,
            'id_user' =>  MySession::mySystemUserId(),
            'address' =>$e_parent['address'],
            'reference' => $e_parent['reference'],
            'id_branch'=>$e_parent['id_branch'],
            'paymode' =>$e_parent['paymode'],
            'paymode_account'=>$e_parent['paymode_account'] ?? 0,
            'check_no'=>$e_parent['check_no'] ?? null,
            'check_date'=>$e_parent['check_date'] ?? null
        ];



        if($key_reference != ""){
            $entry_parent[$key_reference] = $e_parent['payee_reference'];
            $JvController = new JournalVoucherController();
            $entry_payee_det = $JvController->parsePayeeDetails($e_parent['payee_type'],$e_parent['payee_reference']);
            $entry_parent['payee'] = $entry_payee_det->name;
            
            // $jv_parent['address'] = $jv_payee_det->address;
        }else{
            $entry_parent['payee'] = $e_parent['payee'];
            // $jv_parent['address'] = $j_parent['address'];
        }


        $entry_account = json_decode($request->entry_account,true);

        // 
        if($cdv_type <= 4){
            array_push($entry_account,['id_chart_account' => $e_parent['paymode_account'], 'credit'=>$e_parent['amount'],'remarks'=>""]);
        }

    
        // return $entry_account;
        $entries_com = $this->populate_charts($entry_account);

       
        $balance = $entries_com['balance'];

        if(!$balance || $entries_com['total'] != $e_parent['amount']){
            $response['RESPONSE_CODE'] = "ERROR";
            $response['message'] = "Entry not balance";
            return $response;
        }

        $entries = $entries_com['entries'];


        $total_amount = $entries_com['total'];
        $entry_parent['total'] = $total_amount;

         // return $entry_parent;

        if($opcode == 0){
            DB::table('cash_disbursement')
            ->insert($entry_parent);

            $id_cash_disbursement = DB::table('cash_disbursement')->where('type',$cdv_type)->max('id_cash_disbursement');
        }else{
            unset($entry_parent['id_user']);

            DB::table('cash_disbursement')
            ->where('id_cash_disbursement',$id_cash_disbursement)
            ->update($entry_parent);

            DB::table('cash_disbursement_details')
            ->where('id_cash_disbursement',$id_cash_disbursement)
            ->delete();
        }

        for($i=0;$i<count($entries);$i++){
            $entries[$i]['id_cash_disbursement'] =$id_cash_disbursement;
            $entries[$i]['details'] = $entries[$i]['remarks'];
        }

        // return $entries;
        DB::table('cash_disbursement_details')
        ->insert($entries);

        // attachments
        if(isset($request->deleted_attachment)){
            $deleted_attachment = json_decode($request->deleted_attachment,true);
            if(count($deleted_attachment) > 0){
                $this->delete_attachment($deleted_attachment,$id_cash_disbursement);
            }
            // return $deleted_attachment;
        }
        if(isset($attachment_length)){
            if($attachment_length > 0){
                $this->upload_attachment($request,$id_cash_disbursement);
            }            
        }


        $response['RESPONSE_CODE'] = "SUCCESS";
        $response['id_cash_disbursement'] = $id_cash_disbursement;

        return $response;
    }

    public function print($id_cash_disbursement){
        
        if(!MySession::isAdmin()){
            return "INVALID";
            // return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
        // $data['prepared_by'] = MySession::myName();
        $data['prepared_by'] = config('variables.disbursement_prepapred');
        $data['cdv_details'] = DB::table('cash_disbursement')
        ->select(DB::raw("id_cash_disbursement,payee,branch_name,concat(description,if(cash_disbursement.status=10,'','')) as description,total,DATE_FORMAT(date,'%m/%d/%Y') as date,cash_disbursement.address,paymode,check_no,DATE_FORMAT(check_date,'%m/%d/%Y') as check_date,cash_disbursement.type,cash_disbursement.reference"))
        ->leftJoin('member as m','m.id_member','cash_disbursement.id_member')
        ->leftJoin('tbl_branch','tbl_branch.id_branch','cash_disbursement.id_branch')
        ->where('id_cash_disbursement',$id_cash_disbursement)
        ->first();


        $data['cdv_items'] = DB::table('cash_disbursement_details as cdv')
        ->select('id_cash_disbursement','account_code','description','debit','credit','remarks','details')
        ->where('id_cash_disbursement',$id_cash_disbursement)
        ->orDerby('id_cash_disbursement_details')
        ->get();

        $data['type'] = ($data['cdv_details']->type==1)?'LOAN':'CASH';
        if($data['cdv_details']->type == 1){
            $data['loanDetails'] = DB::table('loan_table')
                                   ->select(DB::raw("DATE_FORMAT(MIN(due_date),'%M %Y') as startDate,DATE_FORMAT(MAX(due_date),'%M %Y') as endDate,repayment_amount as principal,interest_amount as interest,total_due"))
                                   ->where('id_loan',$data['cdv_details']->reference)
                                   ->first();
            $data['prepared_by'] = config('variables.loan_disbursement_prepared');
           
        }


        // $html = view('cash_disbursement.print_cdv',$data);
        // $dompdf = new Dompdf();
        // $dompdf->loadHtml($html);
        // $dompdf->render();
        // $font = $dompdf->getFontMetrics()->get_font("serif");

        // $dompdf->getCanvas()->page_text(500, 50, "CDV No. $id_cash_disbursement", $font, 12, array(0,0,0));
        // $dompdf->getCanvas()->page_text(24, 18, "Page {PAGE_NUM} of {PAGE_COUNT}", $font, 8, array(0,0,0));
        // $canvas = $dompdf->getCanvas();



        // $dompdf->set_paper("A4", 'landscape');
        // $dompdf->getCanvas()->page_text(530, 5, "Page: {PAGE_NUM} of {PAGE_COUNT}", $font, 9, array(0,0,0));      
        // $dompdf->stream("Cash Disbursement Voucher No $id_cash_disbursement.pdf", array("Attachment" => false));


        // dd($data);

        $html = view('cash_disbursement.print_cdv_new',$data);

        $pdf = PDF::loadHtml($html);
        $pdf->setOption("encoding","UTF-8");
        $pdf->setOption('margin-bottom', '5mm');
        $pdf->setOption('margin-top', '7mm');
        $pdf->setOption('margin-right', '3mm');
        $pdf->setOption('margin-left', '3mm');
        $pdf->setOption('header-left', 'Page [page] of [toPage]');

        $pdf->setOption('header-font-size', 8);
        $pdf->setOption('header-font-name', 'Calibri');

        $pdf->setOption('enable-javascript', true);

        return $pdf->stream();  



        exit;
        return;
        return $data;
    }
    function populate_charts($chart_entry){
        $search_id_chart = array();
        foreach($chart_entry as $c){
            array_push($search_id_chart,$c['id_chart_account']);
        }

        $charts = DB::table('chart_account')
        ->Select('id_chart_account','account_code','description')
        ->whereIn('id_chart_account',$search_id_chart)
        ->get();


        $g = new GroupArrayController();
        $charts = $g->array_group_by($charts,['id_chart_account']);
        $total = 0;
        $total_debit = 0;
        $total_credit = 0;
        for($i=0;$i<count($chart_entry);$i++){
            $chart_entry[$i]['account_code'] = $charts[$chart_entry[$i]['id_chart_account']][0]->account_code;
            $chart_entry[$i]['description'] = $charts[$chart_entry[$i]['id_chart_account']][0]->description;

            if(!isset($chart_entry[$i]['credit'])){
                $chart_entry[$i]['credit'] = 0;
            }
            if(!isset($chart_entry[$i]['debit'])){
                $chart_entry[$i]['debit'] = 0;
            }           

            $total += $chart_entry[$i]['credit'] ?? 0;

            $total_credit += $chart_entry[$i]['credit'] ?? 0;
            $total_debit += $chart_entry[$i]['debit'] ?? 0;
        }
        if(ROUND($total_debit,2) != ROUND($total_credit,2)){
            $data['balance'] = false;

            return $data;
        }

        $data['balance'] = true;
        $data['entries'] = $chart_entry;
        $data['total'] = $total;
        return $data;
        return $chart_entry;

        return $search_id_chart;
    }
    public function view($type,$id_cash_disbursement){

        $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/cdv/'.$type);
        if(!$data['credential']->is_view && !$data['credential']->is_edit){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }

        $data = $this->cdv_type_data($type);
        $data['opcode'] = 1;



        $data['cdv_details'] = DB::table('cash_disbursement')
        ->where('id_cash_disbursement',$id_cash_disbursement)
        ->first();
                        // return $data['cdv_details']->payee_type;
        switch($data['cdv_details']->payee_type){
            case   '1':
            $data['selected_reference_payee'] = DB::table('supplier')->select('id_supplier as id','name')->where('id_supplier',$data['cdv_details']->id_supplier)->first();

            break;
            case   '2':
            $data['selected_reference_payee'] =DB::table('member as m')
            ->select(DB::raw("concat(membership_id,' || ',FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)) as name,id_member as id"))
            ->where('id_member',$data['cdv_details']->id_member)
            ->first();


            break;
            case   '3':
            $data['selected_reference_payee'] =DB::table('employee as e')
            ->select(DB::raw("concat(id_employee,' || ',FormatName(e.first_name,e.middle_name,e.last_name,e.suffix)) as name,id_employee as id"))
            ->where('id_employee',$data['cdv_details']->id_employee)
            ->first();
            break;
            case '4':
            $data['payee'] = $data['cdv_details']->payee;
            break;
            default:

                // $data['selected_emploee']
        }

        $data['entries'] = DB::table('cash_disbursement_details as cd')
        ->select('cd.id_chart_account','debit','credit','cd.remarks')
        ->leftJoin('chart_account as ca','ca.id_chart_account','cd.id_chart_account')
        ->where('id_cash_disbursement',$id_cash_disbursement)
        ->where('cd.id_chart_account','<>',$data['cdv_details']->paymode_account)
        ->orDerby('cd.id_cash_disbursement_details')
        ->get();

        //attachments
        $data['attachments'] = DB::table('cdv_attachments')
                               ->where('id_cash_disbursement',$id_cash_disbursement)
                               ->get();
        $data['allow_post'] = ($data['cdv_details']->status == 10)?0:1;

        $data['head_title'] = "CDV# $id_cash_disbursement (".$data['title_mod'].")";
                           // return  $data['entries'];
        return view("cash_disbursement.cdv_form",$data);
        return $data;
        return $data;    
    }
    public function upload_attachment($request,$id_cash_disbursement){

        $attachment_length = $request->attachment_length;
        for($i=0;$i<$attachment_length;$i++){
            $key = "attachment_$i";
            $file = $request->{$key};

            if($file != 'undefined' && $file != ""){
                $file_name = $file->getClientOriginalName();
                Storage::disk('local')->putFileAs($this->cdv_files_path.$id_cash_disbursement,$file,$file_name); 
                

                DB::table('cdv_attachments')
                ->insert([
                    'id_cash_disbursement'=>$id_cash_disbursement,
                    'path_file' =>$id_cash_disbursement."/".$file_name,
                    'file_name' => $file_name
                ]);
            }
        }
        return "OKAY";
    }
    public function delete_attachment($deleted_attachment,$id_cash_disbursement){
        $file_paths = DB::table('cdv_attachments')
                      ->select("path_file")
                      ->where('id_cash_disbursement',$id_cash_disbursement)
                      ->whereIn('id_cdv_attachments',$deleted_attachment)
                      ->get();
        foreach($file_paths as $path){
            Storage::disk('local')->delete($this->cdv_files_path.$path->path_file);
        }
        DB::table('cdv_attachments')
        ->whereIn('id_cdv_attachments',$deleted_attachment)
        ->where('id_cash_disbursement',$id_cash_disbursement)
        ->delete();
        
        return $file_paths;
    }
    public function cancel(Request $request){
        if($request->ajax()){
            $id_cash_disbursement = $request->id_cash_disbursement;
            $reason = $request->cancel_reason;
            $type = $request->type;


            
            // $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/bank_transaction');
            // if(!$data['credential']->is_cancel){
            //     $data['RESPONSE_CODE'] = "CREDENTIAL_ERROR";
            //     $data['message'] = "You dont have a privilege to save this";
            //     return response($data);
            // }

            $validation = DB::table('cash_disbursement')->where('id_cash_disbursement',$id_cash_disbursement)->first();
            if($validation->status == 10){
                $data['RESPONSE_CODE'] = "ERROR";
                $data['message'] = "Cash Disbursement is already cancelled";

                return response($data);
            }
            $count_validator = 0;
            if($type == 'others'){
                $count_validator = DB::table('payroll_ca')
                            ->leftJoin('payroll as p','p.id_payroll','payroll_ca.id_payroll')
                            ->where('payroll_ca.id_cash_disbursement',$id_cash_disbursement)
                            ->where('status','<>',10)
                            ->count();
                $error_message = "Cash advance is already deducted in payroll";

            }elseif($type == "asset_purchase"){
                $count_validator = DB::table('asset')
                            ->where('id_cash_disbursement',$id_cash_disbursement)
                            ->where('status','<>',10)
                            ->count();
                $error_message = "Asset Cash Disbursement is already in inventory";
            }     
            if($count_validator > 0){
                $data['RESPONSE_CODE'] = "ERROR";
                $data['message'] = $error_message;

                return response($data);
            }   

            DB::table('cash_disbursement')
            ->where('id_cash_disbursement',$id_cash_disbursement)
            ->where('status','<>',10)
            ->update(['cancellation_reason'=>$reason,
               'date_cancelled' => DB::raw('now()'),
               'status' => 10,
               'description'=>DB::raw("concat(description,' [CANCELLED]')")]);

            $data['RESPONSE_CODE'] = "SUCCESS";

            return response($data);
        }
    }
}
