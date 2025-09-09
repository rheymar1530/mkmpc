<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use DateTime;
use App\CredentialModel;
use App\MySession as MySession;
use Dompdf\Dompdf;
use PDF;
class CashReceiptController extends Controller
{
    public function current_date(){
        date_default_timezone_set('Asia/Manila');
        $dt = new DateTime();
        return $dt->format('Y-m-d');
    }
    public function index(Request $request){
        $data['head_title'] = "Cash Receipts";
       
        $data['credential']= CredentialModel::GetCredential(MySession::myPrivilegeId());
        if(!$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }

        $data['filter_type'] = $request->filter_type ?? 1;
        $data['date_end'] = $request->date_end ?? $this->current_date();
        $data['date_start'] = $request->date_start ?? date("Y-m-d", strtotime("-1 month",strtotime(date($data['date_end']))));
        $data['or_no'] = $request->or_no ?? '';
        $data['receive_from'] = $request->receive_from ?? 1;
        $data['id_member'] = $request->id_member ?? 0;
        if(isset($request->id_member) && $data['receive_from'] == 2){
            $data['member_selected'] =  DB::table('member')
            ->select(DB::raw("concat(membership_id,' || ',FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)) as tag_value,id_member as tag_id"))
            ->where('id_member',$request->id_member)
            ->first();           
        }
        // return $data['receive_from'];   
        $data['lists'] = $this->cash_receipt_data($data['filter_type'],$data['date_start'],$data['date_end'],$data['or_no'],$data['receive_from'],$data['id_member']);

        // return $data;
        return view('cash_receipt.index',$data);
        // return view('cash_receipt'); 
    }
    public function add(){

        $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/cash_receipt');
        if(!$data['credential']->is_create){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
        $data['head_title'] = "Create Cash Receipt";
        $data['payment_type'] = DB::table('tbl_payment_type')->where('type',1)->get();
        $data['paymode'] = DB::table('tbl_paymode')->get();
        $data['current_date'] = $this->current_date();
        $data['banks'] = DB::table('tbl_bank')->get();
        $data['opcode'] = 0;


        return view('cash_receipt.cash_receipt_form',$data); 
    }
    public function cash_receipt_data($filter_type,$date_start,$date_end,$or_no,$id_payee_type,$id_member){
        $data = DB::table("cash_receipt as cr")
        ->select(DB::raw("cr.id_cash_receipt,DATE_FORMAT(cr.date_received,'%M %d, %Y') as date_received,if(cr.payee_type=1,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix),payee_text) as payee,ifnull(m.membership_id,'-') as member_code,
            cr.or_no,pay.description as paymode,format(cr.total_payment,2) as total_payment,DATE_FORMAT(cr.date_Created,'%M %d, %Y %r') as date_created,cr.status"))
        ->leftJoin('tbl_paymode as pay','pay.id_paymode','cr.id_paymode')
        ->leftJoin('member as m','m.id_member','cr.id_member')
        ->where(function($query) use($filter_type,$date_start,$date_end,$or_no){
            if($filter_type == 1){ // Date received
                $query->where("date_received", ">=",$date_start)
                ->where("date_received","<=",$date_end);
            }elseif($filter_type == 2){
                $query->where(DB::raw("DATE(date_received)"), ">=",$date_start)
                ->where(DB::raw("DATE(date_received)"),"<=",$date_end);                
            }elseif($filter_type == 3){
                $query->where('cr.or_no',$or_no);
            }
        })
        ->where(function($query) use($filter_type,$id_payee_type,$id_member){
            if($filter_type != 3){ // if not by OR
                if($id_payee_type ==2 ){ // if per member
                    $query->where('cr.id_member',$id_member);
                }elseif($id_payee_type == 3){ // if per non member
                    $query->where('cr.payee_type',2);
                }
            }
        })
        ->where('cr.type',1)
        ->orderBy('cr.id_cash_receipt','DESC')
        ->get();

        // $data = DB::select("
        //     FROM cash_Receipt as cr
        //     LEFT JOIN tbl_paymode as pay on pay.id_paymode = cr.id_paymode
        //     LEFT JOIN member as m on m.id_member = cr.id_member");
        return $data;
    }
    public function view($id_cash_receipt){

        $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/cash_receipt');
        if(!$data['credential']->is_view && !$data['credential']->is_edit){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
        $data['head_title'] = "Cash Receipt #$id_cash_receipt";
        $data['payment_type'] = DB::table('tbl_payment_type')->where('type',1)->get();
        $data['paymode'] = DB::table('tbl_paymode')->get();
        $data['current_date'] = $this->current_date();
        $data['details'] = DB::table('cash_receipt')->where('id_cash_receipt',$id_cash_receipt)->first();
        $data['opcode'] = 1;
        $data['banks'] = DB::table('tbl_bank')->get();
        $data['member_selected'] =  DB::table('member as m')
        ->select(DB::raw("concat(membership_id,' || ',FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)) as tag_value,id_member as tag_id"))
        ->where('id_member',$data['details']->id_member)
        ->first();

        $data['payments'] = DB::table('cash_receipt_details')->where('id_cash_receipt',$id_cash_receipt)->get();

            // return $data;
        return view('cash_receipt.cash_receipt_form',$data); 
        return $data;
    }
    public function search_member(Request $request){
        if($request->ajax()){
            $search = $request->term;   
            if(strlen($search) < 3){
                $data['accounts'] = array();
                return response($data);
            }
            $data['accounts'] = DB::table('member as m')
            ->select(DB::raw("concat(membership_id,' || ',FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)) as tag_value,id_member as tag_id"))
            ->where(function ($query) use ($search){
                $query->where(DB::raw("concat(first_name,' ',last_name)"), 'like', "%$search%");
            })
            ->where('m.status',1)
            ->get();
            return response($data);
        }
    }
    public function post(Request $request){
        if($request->ajax()){
            $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/cash_receipt');
            $parent_fields = $request->fields;
            $payments = $request->payments;
            $opcode = $request->opcode;
            $id_cash_receipt = $request->id_cash_receipt;

            $mem_app_status_id_payments = [1,3];


            // return $id_cash_receipt;

            // return $parent_fields;

            $total_amount = $this->compute_total_amount($payments);
            $parent_fields['total_payment']  = $total_amount;

            //Validate OR
            $or_record = DB::table('cash_receipt')
            ->where(function($query) use($opcode,$id_cash_receipt,$parent_fields){
                            if($opcode == 0){ //if insert
                                $query->where('or_no',$parent_fields['or_no']);
                            }else{
                                $query->where('or_no',$parent_fields['or_no'])->where('id_cash_receipt','<>',$id_cash_receipt);
                            }
                        })
            ->count();

                        // return $or_record;
            if($or_record > 0){
                $data['RESPONSE_CODE'] = "INVALID_OR";
                $data['message'] = "OR #".$parent_fields['or_no']." already exists";

                return response($data);
            }

            if($opcode == 0){ // Add
                if(!$data['credential']->is_create){
                    $data['RESPONSE_CODE'] = "CREDENTIAL_ERROR";
                    $data['message'] = "You dont have a privilege to save this";
                    return response($data);
                }
                DB::table('cash_receipt')
                ->insert($parent_fields);              
                $id_cash_receipt = DB::table('cash_receipt')->max('id_cash_receipt');
            }else{ // Edit
                if(!$data['credential']->is_edit){
                    $data['RESPONSE_CODE'] = "CREDENTIAL_ERROR";
                    $data['message'] = "You dont have a privilege to save this";
                    return response($data);
                }
                DB::table('cash_receipt')
                ->where('id_cash_receipt',$id_cash_receipt)
                ->update($parent_fields);

                //Remove payments
                DB::table('cash_receipt_details')
                ->where('id_cash_receipt',$id_cash_receipt)
                ->delete();

            }
            $contains_app_status_payment = false;
            foreach($payments as $p){
                $payments_arr[]= [
                    'id_cash_receipt' => $id_cash_receipt,
                    'id_payment_type' => $p['id_payment_type'],
                    'amount' => $p['amount'],
                ];
                if(in_array($p['id_payment_type'],$mem_app_status_id_payments)){
                    $contains_app_status_payment = true;
                }
            }

            DB::table('cash_receipt_details')
            ->insert($payments_arr);
            
            if($parent_fields['payee_type'] == 1){ // paid by member
                //Check if new member
                $member_application_status  = DB::table('member')->select('application_status')->where('id_member',$parent_fields['id_member'])->first();

                if($member_application_status->application_status == 0){ // If new member
                    if($contains_app_status_payment){
                        DB::table('member')->where('id_member',$parent_fields['id_member'])->update(['application_status'=>1]);
                    }
                }else{
                    $count_payment = DB::table('cash_receipt as cr')
                    ->LeftJoin('cash_receipt_details as crd','crd.id_cash_receipt','cr.id_cash_receipt')
                    ->where('cr.payee_type',1)
                    ->where('cr.id_member',$parent_fields['id_member'])
                    ->whereIn('crd.id_payment_type',$mem_app_status_id_payments)
                    ->where('cr.status','<>',10)
                    ->count();
                    if($count_payment == 0){
                        DB::table('member')->where('id_member',$parent_fields['id_member'])->update(['application_status'=>0]);
                    }
                }            
            }
            DB::table('cash_receipt')
            ->where('id_cash_receipt',$id_cash_receipt)
            ->update([
                'id_cash_receipt_voucher'=>$this->GenerateCRV($id_cash_receipt )
            ]);
            $data['id_cash_receipt'] = $id_cash_receipt;
            $data['RESPONSE_CODE'] = "SUCCESS";
            return response($data);
        }
    }
    public function cancel(Request $request){
        if($request->ajax()){
            $id_cash_receipt = $request->id_cash_receipt;
            $reason = $request->cancel_reason;

            $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/cash_receipt');
            if(!$data['credential']->is_cancel){
                $data['RESPONSE_CODE'] = "CREDENTIAL_ERROR";
                $data['message'] = "You dont have a privilege to save this";
                return response($data);
            }
            $validation = DB::table('cash_receipt')
            ->select("status","id_cash_receipt_voucher")
            ->where('id_cash_receipt',$id_cash_receipt)
            ->first();
            if($validation->status == 10){
                $data['RESPONSE_CODE'] = "ERROR";
                $data['message'] = "Cash Receipt is already cancelled";

                return response($data);
            }

            DB::table('cash_receipt')
            ->where('id_cash_receipt',$id_cash_receipt)
            ->where('status','<>',10)
            ->update(['cancel_reason'=>$reason,
               'date_cancelled' => DB::raw('now()'),
               'status' => 10]);

            DB::table('cash_receipt_voucher')
            ->where('id_cash_receipt_voucher',$validation->id_cash_receipt_voucher)
            ->update(['status'=>10,'description'=>DB::raw("CONCAT(description,' [CANCELLED]')")]);

            $data['RESPONSE_CODE'] = "SUCCESS";

            return response($data);
        }
    }
    public function print(Request $request){
        if(!isset($request->reference)){
            return response("INVALID REQUEST");
        }


        $d = DB::table('cash_receipt as cr')
                                ->select(DB::raw("if(cr.payee_type=1,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix),payee_text) as member_name,m.tin,or_no,DATE_format(date_received,'%m/%d/%Y') as transaction_date,total_payment,cr.id_cash_receipt,payment_remarks,cr.status,id_paymode,m.address,cr.engaged_in,concat('OR# ',cr.or_no) as or_number,concat('CHK# ',cr.check_no) as check_no"))
                                ->leftJoin('member as m','m.id_member','cr.id_member')
                                ->where('cr.id_cash_receipt',$request->reference)
                                ->first();


        if($d->status == 10){
            return;
        }

        $items = DB::table('cash_receipt_details as crd')
                                        ->select(DB::raw("pt.description as payment_description,crd.amount "))
                                        ->leftJoin("tbl_payment_type as pt","pt.id_payment_type","crd.id_payment_type")
                                        ->where('crd.id_cash_receipt',$request->reference)
                                        ->get();

        $data['paymentDetails'] = [
            "payee" => $d->member_name,
            "address" => $d->address,
            "tin" => $d->tin,
            "total_amount" => $d->total_payment,
            "date" => $d->transaction_date,
            "or_number" => $d->or_number,
            "check_no" => $d->check_no
        ];

        $Transactions = array();
        foreach($items as $item){
            $Transactions[]= [
                'description'=>$item->payment_description,
                'amount'=>$item->amount
            ];
        }

        $data['Transactions'] =$Transactions;

        return view('cash_receipt.mk-or',$data);

       return view('cash_receipt.new_or',$data);

       return view('cash_receipt.cash_receipt_print',$data);

                                return $data;
        $data['details']  = DB::table('cash_receipt')->where('id_cash_receipt',$request->reference)->first();

        if(!isset($data['details'])){
            return "INVALID REQUEST";
        }

        echo "<h3>THIS IS THE PRINT VIEW OF CASH RECEIPT ID ".$request->reference."</h3><br>";
        return $data;
    }
    public function compute_total_amount($payments){
        $total = 0;
        foreach($payments as $p){
            $total += $p['amount'];
        }

        return $total;
    }

    public function GenerateCRV($id_cash_receipt){
        $id_cdv = DB::table('cash_receipt')->select('id_cash_receipt_voucher')->where('id_cash_receipt',$id_cash_receipt)->first()->id_cash_receipt_voucher;

        if($id_cdv == 0){

            DB::select("INSERT INTO cash_receipt_voucher (date,paymode,type,description,id_member,payee,reference,status,total_amount,payee_type,id_user,or_no)
                        SELECT date_received,id_paymode as paymode,1,concat('CASH RECEIPT #',id_cash_receipt,' (OR# ',cr.or_no,')'),cr.id_member,
                        CASE 
                            WHEN cr.id_member is not null THEN FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)
                            ELSE payee_text END as payee,
                        id_cash_receipt as reference,0 as status,total_payment as total,if(payee_type=1,2,4) as payee_type,?,or_no
                        FROM cash_receipt as cr
                        LEFT JOIN member as m on m.id_member = cr.id_member
                        WHERE cr.id_cash_receipt=?;",[MySession::mySystemUserId(),$id_cash_receipt]);

            $id_cdv = DB::table('cash_receipt_voucher')->where('id_user',MySession::mySystemUserId())->max('id_cash_receipt_voucher');
        }else{
            DB::select("UPDATE cash_receipt as cr
                        LEFT JOIN cash_receipt_voucher as crv on crv.id_cash_receipt_voucher = cr.id_cash_receipt_voucher
                        LEFT JOIN member as m on m.id_member = cr.id_member
                        SET crv.date=date_received,crv.paymode=id_paymode,crv.description=concat('CASH RECEIPT #',id_cash_receipt,' (OR# ',cr.or_no,')'), crv.id_member=cr.id_member,crv.payee=CASE 
                            WHEN cr.id_member is not null THEN FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)
                            ELSE payee_text END ,crv.total_amount=total_payment,crv.payee_type=if(cr.payee_type=1,2,4),crv.or_no = cr.or_no
                        WHERE cr.id_cash_receipt = ?;",[$id_cash_receipt]);
            DB::table('cash_receipt_voucher_details')->where('id_cash_receipt_voucher',$id_cdv)->delete();
        }


        //INSERT ENTRIES
        DB::select("INSERT INTO cash_receipt_voucher_details (id_cash_receipt_voucher,id_chart_account,account_code,description,debit,credit,details,reference)
                    SELECT ? as id_cash_receipt_voucher,ca.id_chart_account,ca.account_code,ca.description,total_payment as debit,0 as credit,'' as reference,
                    id_cash_receipt
                    FROM cash_receipt as cr
                    LEFT JOIN tbl_bank as tb on tb.id_bank = cr.id_bank
                    LEFT JOIN chart_account as ca on ca.id_chart_account= if((cr.id_paymode=1 OR cr.id_paymode=2),1,tb.id_chart_account)
                    where cr.id_cash_receipt = ?
                    UNION ALL
                    SELECT ? as id_cash_receipt, ca.id_chart_account,ca.account_code,ca.description,0 as debit,amount as credit,tp.description,id_cash_receipt_details
                    FROM cash_receipt_details as crd
                    LEFT JOIN tbl_payment_type as tp on tp.id_payment_type = crd.id_payment_type
                    LEFT JOIN chart_account as ca on ca.id_chart_account = tp.id_chart_account
                    WHERE crd.id_cash_receipt = ?",[$id_cdv,$id_cash_receipt,$id_cdv,$id_cash_receipt]);

        return $id_cdv;
    }

    public function printCRV($id_cash_receipt_voucher){
        if(!MySession::isAdmin()){
            return "INVALID";
            // return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
        $data['prepared_by'] = MySession::myName();
        $data['crv_details'] = DB::table('cash_receipt_voucher as crv')
        ->select(DB::raw("id_cash_receipt_voucher,payee,branch_name,description,total_amount,DATE_FORMAT(date,'%m/%d/%Y') as date,crv.address,if(crv.paymode=1,'Cash',if(crv.paymode=2,'Check','Bank')) as paymode,crv.or_no"))
        ->leftJoin('tbl_branch','tbl_branch.id_branch','crv.id_branch')
        ->where('id_cash_receipt_voucher',$id_cash_receipt_voucher)
        ->first();

        $data['entries'] = DB::table('cash_receipt_voucher_details')
                          ->select('account_code','description','debit','credit','description','details')
                          ->where('id_cash_receipt_voucher',$id_cash_receipt_voucher)
                          ->get();
        // return $data;


        $html = view('cash_receipt.print_crv_new',$data);
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
    }
}




// SELECT * FROM (
// SELECT id_cash_receipt_details,pt.type,
// CASE WHEN pt.type = 5 THEN concat('Payment for ',ls.name,' (ID#',crd.id_loan,')') 
// ELSE concat('Payment for ',ls.name,' (ID#',crd.id_loan,' Previous)')  END as 'payment_description' ,
// SUM(crd.amount) as amount
// FROM cash_receipt_details as crd
// LEFT JOIN tbl_payment_type as pt on pt.id_payment_type = crd.id_payment_type
// LEFT JOIN cash_receipt as cr on cr.id_cash_receipt = crd.id_cash_receipt
// LEFT JOIN loan on loan.id_loan =crd.id_loan
// LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
// where crd.id_cash_receipt= 38 and pt.type in (5,6)
// GROUP BY type,crd.id_loan
// UNION ALL
// SELECT id_cash_receipt_details,pt.type,pt.description as 'payment_description' ,
// crd.amount
// FROM cash_receipt_details as crd
// LEFT JOIN tbl_payment_type as pt on pt.id_payment_type = crd.id_payment_type
// where crd.id_cash_receipt= 38 and pt.type not in (5,6)) as k
// ORDER BY id_cash_receipt_details;
