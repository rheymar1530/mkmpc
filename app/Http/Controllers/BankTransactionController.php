<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use DateTime;
use App\CredentialModel;
use App\MySession as MySession;
use App\JVModel;
use App\CDVModel;
use App\CRVModel;
class BankTransactionController extends Controller
{
    public function current_date(){
        date_default_timezone_set('Asia/Manila');
        $dt = new DateTime();
        return $dt->format('Y-m-d');
    }
    public function index(){
        $data['credential']= CredentialModel::GetCredential(MySession::myPrivilegeId());
        if(!$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
        $data['head_title'] = "Bank Transaction";
        $data['lists'] = DB::table('bank_transaction as bt')
                        ->select(DB::raw("id_bank_transaction,DATE_FORMAT(bt.date,'%M %d, %Y') as date,if(bt.type=1,'Deposit',if(bt.type=2,'Withdraw','Transfer')) as type,m.membership_id as member_code,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as name,bt.name as name,bank_name,bt.reference,FORMAT(bt.amount,2) as amount,bt.type as id_type,bt.date_created,bt.status"))
                        ->leftJoin('member as m','m.id_member','bt.id_member')
                        ->leftJoin('tbl_bank','tbl_bank.id_bank','bt.id_bank')
                        ->orDerby('id_bank_transaction','DESC')
                        ->get();
        return view('bank_transaction.index',$data);
        return $data;   
    }
    public function create(){
        $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/bank_transaction');
        if(!$data['credential']->is_create){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
        $data['opcode'] = 0;
        $data['banks'] = DB::table('tbl_bank')->get();
        $data['current_date'] = $this->current_date();
        $data['head_title'] = "Create Bank Transaction";
        
        // return  $data;
        return view("bank_transaction.bank_transaction_form",$data);
    }
    public function view($id_bank_transaction){
        $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/bank_transaction');
        if(!$data['credential']->is_view && !$data['credential']->is_edit ){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
        $data['head_title'] = "Bank Transaction #$id_bank_transaction";
        $data['opcode'] = 1;
        $data['banks'] = DB::table('tbl_bank')->get();
        $data['transactions'] = DB::table('bank_transaction')->where('id_bank_transaction',$id_bank_transaction)->first();
        $data['current_date'] = $this->current_date();
        $data['member_selected'] =  DB::table('member')
        ->select(DB::raw("concat(membership_id,' || ',first_name,' ',last_name) as tag_value,id_member as tag_id"))
        ->where('id_member',$data['transactions']->id_member)
        ->first();


        // if($data['transactions']->id_cash_disbursement > 0){
        //     $data['entry'] = "jv"
        // }

        return view("bank_transaction.bank_transaction_form",$data);        
    }
    public function post(Request $request){
        if($request->ajax()){
            $transactions = $request->transactions;
            $id_bank_to = array();
            foreach($transactions as $t){
                if(isset($t['id_bank_transfer_to'])){
                    array_push($id_bank_to,$t['id_bank_transfer_to']);
                }
            }

            if(count($id_bank_to) > 0){
                $banks =  $this->parseBankName($id_bank_to);
                for($i=0;$i<count($transactions);$i++){
                    if(isset($transactions[$i]['id_bank_transfer_to'])){
                        $transactions[$i]['name'] = $banks[$transactions[$i]['id_bank_transfer_to']][0]->bank_name;
                    }else{
                        $transactions[$i]['id_bank_transfer_to'] = 0;
                    }
                }
            }

     

            // return response($request);

            $opcode = $request->opcode;
            $id_bank_transaction = $request->id_bank_transaction;

            $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/bank_transaction');

            if(count($transactions) > 0){
                if($opcode == 0){ // Add
                    if(!$data['credential']->is_create){
                        $data['RESPONSE_CODE'] = "CREDENTIAL_ERROR";
                        $data['message'] = "You dont have a privilege to save this";
                        return response($data);
                    }

                    for($i=0;$i<count($transactions);$i++){
                        DB::table('bank_transaction')
                        ->insert($transactions[$i]);

                        $id_bank_transaction = DB::table('bank_transaction')->max('id_bank_transaction');


                        if($transactions[$i]['type'] == 1){
                            CDVModel::BankTransactionCDV($id_bank_transaction);
                        }elseif($transactions[$i]['type'] == 2){
                            CRVModel::BankTransactionCRV($id_bank_transaction);
                        }else{
                            JVModel::BankTransactionJV($id_bank_transaction);
                        }
                       

                    
                        // $id_journal_voucher = $this->postJV($opcode,$id_bank_transaction);

                        // DB::table('bank_transaction')->where('id_bank_transaction',$id_bank_transaction)->update(['id_journal_voucher'=>$id_journal_voucher]);

                    }
      

                    $id_bank_transaction = DB::table('bank_transaction')->max('id_bank_transaction');            
                }else{ // Edit
                    if(!$data['credential']->is_edit){
                        $data['RESPONSE_CODE'] = "CREDENTIAL_ERROR";
                        $data['message'] = "You dont have a privilege to save this";
                        return response($data);
                    }
                    DB::table('bank_transaction')
                    ->where('id_bank_transaction',$id_bank_transaction)
                    ->update($transactions[0]);

                    if($transactions[0]['type'] == 1){
                        CDVModel::BankTransactionCDV($id_bank_transaction);
                    }elseif($transactions[0]['type'] == 2){
                        CRVModel::BankTransactionCRV($id_bank_transaction);
                    }else{
                        JVModel::BankTransactionJV($id_bank_transaction);
                    }
                }
                $data['RESPONSE_CODE'] = "SUCCESS";
                $data['COMMAND'] = ($opcode == 1 || count($transactions) == 1)?"RELOAD":"LIST";
                $data['redirect_id'] = $id_bank_transaction;
            }


            return response($data);
        }
    }
    public function parseBankName($id_banks){
        $banks = DB::table('tbl_bank')
                ->select("id_bank",'bank_name')
                ->whereIn('id_bank',$id_banks)
                ->get();
        $g = new GroupArrayController();

        return $g->array_group_by($banks,['id_bank']);
        return $banks;
    }
    public function cancel(Request $request){
        if($request->ajax()){
            $id_bank_transaction = $request->id_bank_transaction;
            $reason = $request->cancel_reason;

            
            $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/bank_transaction');
            if(!$data['credential']->is_cancel){
                $data['RESPONSE_CODE'] = "CREDENTIAL_ERROR";
                $data['message'] = "You dont have a privilege to save this";
                return response($data);
            }
            $validation = DB::table('bank_transaction')->where('id_bank_transaction',$id_bank_transaction)->first();
            if($validation->status == 10){
                $data['RESPONSE_CODE'] = "ERROR";
                $data['message'] = "Bank Transaction is already cancelled";

                return response($data);
            }

            DB::table('bank_transaction')
            ->where('id_bank_transaction',$id_bank_transaction)
            ->where('status','<>',10)
            ->update(['cancellation_reason'=>$reason,
               'date_cancelled' => DB::raw('now()'),
               'status' => 10]);

            DB::table('journal_voucher')
            ->where('id_journal_voucher',$validation->id_journal_voucher)
            ->update(['status'=>10,'description'=>DB::raw("concat(description,' [CANCELLED]')"),'date_cancelled'=>DB::raw("now()"),'cancellation_reason'=>$reason]);


            DB::table('cash_disbursement')
            ->where('id_cash_disbursement',$validation->id_cash_disbursement)
            ->update(['status'=>10,'description'=>DB::raw("concat(description,' [CANCELLED]')"),'date_cancelled'=>DB::raw("now()"),'cancellation_reason'=>$reason]);


            DB::table('cash_receipt_voucher')
            ->where('id_cash_receipt_voucher',$validation->id_cash_receipt_voucher)
            ->update(['status'=>10,'description'=>DB::raw("concat(description,' [CANCELLED]')"),'date_cancelled'=>DB::raw("now()"),'cancellation_reason'=>$reason]);

            $data['RESPONSE_CODE'] = "SUCCESS";

            return response($data);
        }
    }

    public function postJV($opcode,$id_bank_transaction){
        if($opcode == 0){ // insert

            DB::select("INSERT INTO journal_voucher (date,type,description,id_member,payee,reference,status,total_amount,id_branch,address,payee_type)
            SELECT date,4 as type,concat('Bank Transaction ID# ',id_bank_transaction,' ',if(bt.type=1,concat('Deposit to ', tb.bank_name),if(bt.type=2,concat('Withdrawal from ',tb.bank_name),concat('Transfer from ',tb.bank_name,' to ',tb_trans.bank_name)))) as description,bt.id_member,
            if(bt.type=3,tb_trans.bank_name,bt.name) as payee,id_bank_transaction,0 as status,bt.amount,m.id_branch,m.address,4 as payee_type
            FROM bank_transaction as bt
            LEFT JOIN tbl_bank as tb on tb.id_bank = bt.id_bank
            LEFT JOIN tbl_bank as tb_trans on tb_trans.id_bank = bt.id_bank_transfer_to
            LEFT JOIN member as m on m.id_member = bt.id_member
            WHERE bt.id_bank_transaction = ?;",[$id_bank_transaction]);

            $id_journal_voucher = DB::table('journal_voucher')->where('type',4)->max('id_journal_voucher');

        }else{

        }

        DB::select("
            INSERT INTO journal_voucher_details (id_journal_voucher,id_chart_account,account_code,description,debit,credit,details,reference)
            SELECT $id_journal_voucher as id_journal_voucher,tb.id_chart_account,ca.account_code,ca.description,amount as debit,0 as credit,'',id_bank_transaction from bank_transaction as bt
        LEFT JOIN tbl_bank as tb on tb.id_bank = bt.id_bank
        LEFT JOIN chart_account as ca on ca.id_chart_account = tb.id_chart_account
        where id_bank_transaction = ?
        UNION ALL
        SELECT $id_journal_voucher as id_journal_voucher,ca.id_chart_account,ca.account_code,ca.description,0 as debit,amount as credit,'',id_bank_transaction from bank_transaction as bt
        LEFT JOIN tbl_bank as tb on tb.id_bank = bt.id_bank_transfer_to
        LEFT JOIN chart_account as ca on ca.id_chart_account = if(bt.type=3,tb.id_chart_account,1)
        where id_bank_transaction = ?;",[$id_bank_transaction,$id_bank_transaction]);


        return $id_journal_voucher;

    }
}


// /*****date,type,description,id_member,payee,reference,status,total_amount,id_branch,address,payee_typ********/
// SELECT date,4 as type,concat('Bank Transaction ID# ',id_bank_transaction,' ',if(bt.type=1,'Deposit',if(bt.type=2,'Withdrawal','Transfer'))) as description,tb.bank_name
// FROM bank_transaction as bt
// LEFT JOIN tbl_bank as tb on tb.id_bank = bt.id_bank 
// WHERE bt.id_bank_transaction = 2;



// INSERT INTO journal_voucher (date,type,description,id_member,payee,reference,status,total_amount,id_branch,address,payee_type);
// SELECT date,4 as type,concat('Bank Transaction ID# ',id_bank_transaction,' ',if(bt.type=1,'Deposit',if(bt.type=2,'Withdrawal','Transfer'))) as description,bt.id_member,
// concat(m.first_name,' ',m.last_name) as payee,tb.bank_name,id_bank_transaction,0 as status,bt.amount,m.id_branch,m.address,1 as payee_type
// FROM bank_transaction as bt
// LEFT JOIN tbl_bank as tb on tb.id_bank = bt.id_bank
// LEFT JOIN member as m on m.id_member = bt.id_member
// WHERE bt.id_bank_transaction = 2;




// insert INTO journal_voucher_details (id_journal_voucher,id_chart_account,account_code,description,debit,credit,details,reference)
// SELECT 1 as id_journal_voucher,tb.id_chart_account,ca.account_code,ca.description,amount as debit,0 as credit,'',id_bank_transaction from bank_transaction as bt
// LEFT JOIN tbl_bank as tb on tb.id_bank = bt.id_bank
// LEFT JOIN chart_account as ca on ca.id_chart_account = tb.id_chart_account
// where id_bank_transaction = 2
// UNION ALL
// SELECT 1 as id_journal_voucher,ca.id_chart_account,ca.account_code,ca.description,0 as debit,amount as credit,'',id_bank_transaction from bank_transaction as bt
// LEFT JOIN tbl_bank as tb on tb.id_bank = bt.id_bank_transfer_to
// LEFT JOIN chart_account as ca on ca.id_chart_account = if(bt.type=3,tb.id_chart_account,1)
// where id_bank_transaction = 2;