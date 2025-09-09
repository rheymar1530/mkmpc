<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Loan;
use App\MySession;
use App\CredentialModel;
use Dompdf\Dompdf;
use App\JVModel;
use App\CRVModel;
use PDF;
use App\WebHelper;
use Carbon\Carbon;
class BulkRepaymentController extends Controller
{
    public function index(Request $request){
        $data['credential']= CredentialModel::GetCredential(MySession::myPrivilegeId());
        if(!$data['credential']->is_view){
            // return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }  
        $data['repayments'] = DB::table('repayment as r')
                              ->select(DB::raw("r.id_repayment,DATE_FORMAT(r.date,'%m/%d/%Y') as date,r.total_amount,DATE_FORMAT(r.date_created,'%m/%d/%Y') as date_created,
                            CASE WHEN r.status = 10 THEN 'Cancelled'
                            ELSE '' END as status_description,r.status,b.name as baranggay_lgu,if(b.type=1,'Barangay','LGU') as group_"))
                              ->leftJoin('baranggay_lgu as b','b.id_baranggay_lgu','r.id_baranggay_lgu')
                              ->orderBy('r.id_repayment','DESC')
                              ->get();

        return view('bulk-repayment.index',$data);

        dd($data);
    }
    public function create(Request $request){
        $data['sidebar'] = "sidebar-collapse";

        $data['banks'] = DB::table('tbl_bank')
                         ->select(DB::raw("id_bank,bank_name"))
                         ->get();
        $data['branches']  = DB::table('baranggay_lgu')
        ->select(DB::raw("id_baranggay_lgu,name,if(type=1,'Barangay','LGU') as type"))
        ->orderByRaw("type,name")
        ->get();

        $data['selected_branch'] = $request->br ?? $data['branches'][0]->id_baranggay_lgu;


        $data['opcode'] = 0;
        $g = new GroupArrayController();
        $data['branches'] = $g->array_group_by($data['branches'],['type']);

        $data['date'] = MySession::current_date();
        $data['date_due'] = WebHelper::ConvertDatePeriod($data['date']);

        $param = ['due_date'=>$data['date_due'],'id_cbu'=>config('variables.default_cbu'),'date2'=>$data['date_due']];
        $loans = $this->ActiveLoans($data['selected_branch'],$data['date_due'],0);

        $data['def_cbu'] = DB::table('tbl_payment_type')->select('default_amount')->where('id_payment_type',config('variables.default_cbu'))->first()->default_amount;


        // dd($loans);
        
        $data['loans'] = $g->array_group_by($loans,['id_member']);  
        return view('bulk-repayment.form',$data);
    }
    public function edit($id_repayment,Request $request){
        $data['sidebar'] = "sidebar-collapse";
        $data['opcode'] = 1;
        $data['details'] = DB::table('repayment as r')
                          ->select(DB::raw("r.*,b.type as br_type"))
                          ->leftJoin('baranggay_lgu as b','b.id_baranggay_lgu','r.id_baranggay_lgu')
                          ->where('r.id_repayment',$id_repayment)
                          ->first();
        $data['selected_branch'] = $data['details']->id_baranggay_lgu;
        $data['branches']  = DB::table('baranggay_lgu')
        ->select(DB::raw("id_baranggay_lgu,name,if(type=1,'Barangay','LGU') as type"))
        ->where('id_baranggay_lgu',$data['selected_branch'])
        ->orderByRaw("type,name")
        ->get();
        $g = new GroupArrayController();
        $data['branches'] = $g->array_group_by($data['branches'],['type']);
        

        if($data['details']->status == 10){
            return $this->view($id_repayment,$request);
        }

        $data['date'] = $data['details']->date;
        $data['date_due'] = WebHelper::ConvertDatePeriod($data['date']);  
        $loans = $this->ActiveLoans($data['selected_branch'],$data['date_due'],$id_repayment);     

        $data['loans'] = $g->array_group_by($loans,['id_member']);  
        // $data['def_cbu'] = 0;
        $data['def_cbu'] = DB::table('tbl_payment_type')->select('default_amount')->where('id_payment_type',config('variables.default_cbu'))->first()->default_amount;
        $data['banks'] = DB::table('tbl_bank')
                         ->select(DB::raw("id_bank,bank_name"))
                         ->get();


        $data['rt_reference'] = array();
        $rt = DB::table('repayment_transaction')
              ->select('id_member','id_repayment_transaction')
              ->where('id_repayment',$id_repayment)
              ->get();

        foreach($rt as $r){
            $data['rt_reference'][$r->id_member] = $r->id_repayment_transaction;
        }

        $cbus =DB::table('repayment as r')
                        ->select('id_member','rf.amount')
                        ->leftJoin('repayment_transaction as rt','rt.id_repayment','r.id_repayment')
                        ->leftJoin('repayment_fees as rf','rf.id_repayment_transaction','rt.id_repayment_transaction')
                        ->where('rt.status','<>',10)
                        ->where('r.id_repayment',$id_repayment)
                        ->get();
        $data['cbus'] = array();

        foreach($cbus as $c){
            $data['cbus'][$c->id_member]= $c->amount;
        }

        return view('bulk-repayment.form',$data);
    }

    public function view($id_repayment,Request $request){
        $data['details'] = DB::table('repayment as r')
                         ->select(DB::raw("r.id_repayment,tb.bank_name,if(r.id_paymode=1,'Cash','Check') as paymode,if(r.id_check_type=1,'On-date','Post dated') as check_type,DATE_FORMAT(r.date,'%m/%d/%Y') as date,DATE_FORMAT(r.check_date,'%m/%d/%Y') as check_date,r.check_no,r.remarks,r.total_amount,r.id_paymode,r.status,DATE_FORMAT(r.status_date,'%m/%d/%Y') as status_date,r.reason,
                            CASE WHEN r.status = 10 THEN 'Cancelled'
                            ELSE '' END as status_description,b.name as br,b.type as br_type,b.name as baranggay_lgu,if(b.type=1,'Barangay','LGU') as group_,r.or_number"))
                          ->leftJoin('tbl_bank as tb','tb.id_bank','r.id_bank')
                          ->leftJoin('baranggay_lgu as b','b.id_baranggay_lgu','r.id_baranggay_lgu')

                           ->where('r.id_repayment',$id_repayment)
                           ->first();

        $loans = DB::select("SELECT loan.id_loan,loan.loan_token,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member, getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms) as loan_name,payment.payment,ifnull(payment.penalty,0) as penalty,ifnull(payment.rebates,0) as rebates,loan.id_member,id_cash_receipt_voucher
        FROM (
        SELECT rl.id_loan,SUM(paid_principal+paid_interest+paid_fees) as payment,rls.amount as penalty,rr.amount as rebates,rt.id_cash_receipt_voucher FROM repayment_transaction as rt
        LEFT JOIN repayment_loans as rl on rl.id_repayment_transaction = rt.id_repayment_transaction
        LEFT JOIN repayment_loan_surcharges as rls on rls.id_loan = rl.id_loan AND rls.id_repayment_transaction = rt.id_repayment_transaction
        LEFT JOIN repayment_rebates as rr on rr.id_loan = rl.id_loan AND rr.id_repayment_transaction = rt.id_repayment_transaction
        WHERE rt.id_repayment = ? 
        GROUP BY rl.id_loan) as payment
        LEFT JOIN loan on loan.id_loan = payment.id_loan
        LEFT JOIN member as m on m.id_member = loan.id_member
        LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
        ORDER BY member;",[$id_repayment]);


        $g = new GroupArrayController();
        $data['loans'] = $g->array_group_by($loans,['id_member']);

        $cbus =DB::table('repayment as r')
                        ->select('id_member',DB::raw('ifnull(rf.amount,0) as amount'))
                        ->leftJoin('repayment_transaction as rt','rt.id_repayment','r.id_repayment')
                        ->leftJoin('repayment_fees as rf','rf.id_repayment_transaction','rt.id_repayment_transaction')
                        ->where('r.id_repayment',$id_repayment)

                        ->get();

        $data['cbus'] = array();

        foreach($cbus as $c){
            $data['cbus'][$c->id_member]= $c->amount;
        }
 

        return view('bulk-repayment.view',$data);

        dd($data);

    }
    public function ActiveLoans($br,$due_date,$id_repayment){
            $param = [
                'br'=>$br,
                'due1'=>$due_date,
                'due2'=>$due_date,
                'due3'=>$due_date,
                'id_repayment'=>$id_repayment,
                'id_repayment2'=>$id_repayment

            ];
            // loan.id_member in (9,10,11,12,130)
            $payment = ($id_repayment > 0)?"0 ":"@cur_due";

            $loans = DB::select("SELECT k.*,loan.loan_token,loan.date_released,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member,
            getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms) as loan_name FROM (
            SELECT loan.id_loan,loan.id_member,getTotalDueAsOfEx(loan.id_loan,loan.maturity_date,0) as balance,
            @cur_due:=getTotalDueAsOfEx(loan.id_loan,:due1,0) as current_due,$payment as payment,getLoanRebates(loan.id_loan,:due2) as rebates,0 as penalty
            FROM loan
        
            WHERE loan.loan_status = 1 AND loan.id_baranggay_lgu = :br AND loan.id_loan not in 
            (SELECT rl.id_loan
             FROM repayment_transaction as rt
             LEFT JOIN repayment_loans as rl on rl.id_repayment_transaction = rt.id_repayment_transaction
             WHERE rt.id_repayment = :id_repayment2 AND rl.id_loan is not null AND rt.status <> 10
             GROUP BY rl.id_loan
            )
            UNION ALL
            SELECT rl.id_loan,rt.id_member,
            getTotalDueAsOfEx(loan.id_loan,loan.maturity_date,rt.id_repayment_transaction) as balance,
            getTotalDueAsOfEx(loan.id_loan,:due3,rt.id_repayment_transaction) as current_due,
            SUM(ifnull(paid_principal+paid_interest+paid_fees,0)) as payment
            ,ifnull(rr.amount,0) as rebates,ifnull(rs.amount,0) as penalty
            FROM repayment as r
            LEFT JOIN repayment_transaction as rt on rt.id_repayment = r.id_repayment and rt.status <> 10
            LEFT JOIN repayment_loans as rl on rl.id_repayment_transaction = rt.id_repayment_transaction
            LEFT JOIN repayment_loan_surcharges as rs on rs.id_repayment_transaction = rt.id_repayment_transaction AND rl.id_loan = rs.id_loan
            LEFT JOIN repayment_rebates as rr on rr.id_loan = rl.id_loan and rr.id_repayment_transaction = rt.id_repayment_transaction
            LEFT JOIN loan on loan.id_loan = rl.id_loan
            WHERE r.id_repayment = :id_repayment
            GROUP BY rl.id_loan
            ) as k
            LEFT JOIN loan on loan.id_loan = k.id_loan
            LEFT JOIN member as m on m.id_member = loan.id_member
            LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
            ORDER BY member,date_released ASC",$param);

            return $loans;

    }
    public function post(Request $request){
        $opcode = $request->opcode ?? 0;

        $payments = $request->loans;

        $transaction_date = $request->date_deposited ?? MySession::current_date();

        $statement_date = $request->statement_date ??MySession::current_date();

        $date =   WebHelper::ConvertDatePeriod($statement_date);

        $id_repayment = $request->id_repayment ?? 0;

        $paymode = $request->paymode;

        $RepaymentController = new RepaymentController();
        $data['invalid_inputs'] = array();
        
        $repayment_transaction =array();

        $AppPaymentCompile = array();

        $tokenList = array();
        $repayment_parent_loans = array();

        $field = array();

        $postOBJ = array(
            'id_check_type'=>null,
            'id_bank'=>null,
            'check_date'=>null,
            'check_no'=>null
        );

        //compilation of payment mode details
        if($paymode['paymode'] == 4){
            //Check
            $field = array(
                'check_type'=>['key'=>'id_check_type','required'=>true],
                'check_bank'=>['key'=>'check_bank','required'=>true],
                'bank'=>['key'=>'id_bank','required'=>true],
                'check_date'=>['key'=>'check_date','required'=>true],
                'check_no'=>['key'=>'check_no','required'=>true]
            );
        }
        $field['paymode']=['key'=>'id_paymode','required'=>true];
        $field['transaction_date']=['key'=>'date','required'=>true];
        $field['remarks']=['key'=>'remarks','required'=>false];
        $field['or_number'] = ['key'=>'or_number','required'=>true];




        $invalid_field = array();


        foreach($field as $key=>$f){
            if($f['required'] && (!isset($paymode[$key]) || $paymode[$key] == "" )){
                array_push($invalid_field,$key);
            }
            $postOBJ[$f['key']] = $paymode[$key];
        }

        if(count($invalid_field) > 0){
            // $data['RESPONSE_CODE'] = "ERROR";
            // $data['message'] = "Please fill required fields";
            // $data['invalid_fields'] = $invalid_field;
            // return response($data);
        }

        foreach($payments as $c=>$payment){
            $fully_paid_loan = array();
            $rebatesObj = array();
            $total_rebates = 0;


            foreach($payment['loan_payment'] as $token=>$lpayment){
                array_push($tokenList,$token);
                if($lpayment['loan_payment'] + $lpayment['penalty'] == 0){
                    //remove payment without application
                    unset($payments[$c]['loan_payment'][$token]);
                }else{

                    $app_payment = $RepaymentController->PopulatePaymentAuto($token,$lpayment['loan_payment'],$payment['id_repayment_transaction'],$date);
                    $AppPaymentCompile[$token] = $app_payment;
                    if(ROUND($lpayment['loan_payment'],2) > ROUND($app_payment['remaining_total'],2)){
                        if(!isset($data['invalid_inputs']['amt_paid'])){
                            $data['invalid_inputs']['amt_paid'] = array();
                        }
                        array_push($data['invalid_inputs']['amt_paid'],$token);
                    }
                }
            }  
        }

        
        // $loan_ids = DB::table('loan')->select('id_loan')->whereIn('loan_token',$tokenList)->get()->pluck('id_loan')->toArray();  
        // $this->UpdateLoanStatus($loan_ids,$tokenList,$date);

        // dd($tokenList);
        $postData = $this->CompileRepaymentTransaction($date,$payments,$AppPaymentCompile);


        //validation that check if there is payment on loan (return error if false)
        $total_batch_payment = 0;
        foreach($postData as $p){
            $total_batch_payment += $p['repayment_transaction']['total_loan_payment'];
        }
        if($total_batch_payment == 0){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Please apply payment at least on one loan";
            return $data;
        }

        $addFields = [
            'transaction_type' => $postOBJ['id_paymode'],
            'transaction_date'=>$postOBJ['date'],
            'date'=>$date,
            'id_user'=>MySession::myId()
        ];
        $updateFields = [
            'email_sent'=>DB::raw("if(email_sent=0,0,1)")
        ];  

        if(!env('REPAYMENT_EMAIL_NOTIF')){
            $addFields['email_sent'] = $updateFields['email_sent'] = 2;
        }   

        if($opcode == 0){
            $postOBJ['id_user'] = MySession::myId();
            $postOBJ['id_baranggay_lgu'] = $request->br;
            DB::table('repayment')
            ->insert([$postOBJ]);

            $id_repayment = DB::table('repayment')->max('id_repayment');            
        }else{
            DB::table('repayment')
            ->where('id_repayment',$id_repayment)
            ->update($postOBJ);
        }

        // dd($postData);
        foreach($postData as $i=>$post){

            $additionalFields = ($post['id_repayment_transaction'] > 0)?$updateFields:$addFields;

            $rt_opcode = ($post['id_repayment_transaction'] > 0)?1:0;

            $postData[$i]['repayment_transaction'] = $postData[$i]['repayment_transaction']+$additionalFields;

            $postData[$i]['repayment_transaction']['or_no'] = $postOBJ['or_number'];    
            $postData[$i]['repayment_transaction']['id_bank'] = $postOBJ['id_bank'];    

            $for_cancellation = false;
            if($rt_opcode == 0){
                $edited = false;
                if($postData[$i]['repayment_transaction']['total_payment'] == 0){
                    continue;
                }

                $addToken = $this->generateRandomString(5);
                $postData[$i]['repayment_transaction']['repayment_token'] = DB::raw("concat(DATE_FORMAT(now(),'%m%d%Y%H%i%s'),concat(LEFT(MD5(NOW()), 5)),'$addToken')");

                $postData[$i]['repayment_transaction']['id_repayment'] = $id_repayment;

                //push repayment transaction
                DB::table('repayment_transaction')
                ->insert($postData[$i]['repayment_transaction']);

                $id_repayment_transaction = DB::table('repayment_transaction')->max('id_repayment_transaction');
            }else{
                $id_repayment_transaction = $post['id_repayment_transaction'];

                $for_cancellation = ($postData[$i]['repayment_transaction']['total_payment'] > 0)?false:true;
                $edited = true;

                if(!$for_cancellation){


                    $postData[$i]['repayment_transaction']['status'] = 0;
                    DB::table('repayment_loans')
                    ->where('id_repayment_transaction',$id_repayment_transaction)
                    ->delete();

                    DB::table('repayment_loan_surcharges')
                    ->where('id_repayment_transaction',$id_repayment_transaction)
                    ->delete();

                    DB::table('repayment_fees')
                    ->where('id_repayment_transaction',$id_repayment_transaction)
                    ->delete();

                    DB::table('repayment_rebates')
                    ->where('id_repayment_transaction',$id_repayment_transaction)
                    ->delete();
                }else{
                    $postData[$i]['repayment_transaction']['status'] = 10;
                }
                // dd($postData[$i]['repayment_transaction']);
                DB::table('repayment_transaction')
                ->where('id_repayment_transaction',$id_repayment_transaction)
                ->update($postData[$i]['repayment_transaction']);

            }
            $canceled =$for_cancellation;
            if(!$for_cancellation){

                //push repayment_lons
                foreach($post['repayment_loans'] as $c=>$rp){
                    $postData[$i]['repayment_loans'][$c]['id_repayment_transaction'] = $id_repayment_transaction;
                }

                DB::table('repayment_loans')
                ->insert($postData[$i]['repayment_loans']);

                //push repayment loan surcharges 
                foreach($post['repayment_loan_surcharges'] as $c=>$rp){
                    $postData[$i]['repayment_loan_surcharges'][$c]['id_repayment_transaction'] = $id_repayment_transaction;
                }

                DB::table('repayment_loan_surcharges')
                ->insert($postData[$i]['repayment_loan_surcharges']);


                //push repayment fees
                foreach($post['repayment_fees'] as $c=>$rp){
                    $postData[$i]['repayment_fees'][$c]['id_repayment_transaction'] = $id_repayment_transaction;
                }

                DB::table('repayment_fees')
                ->insert($postData[$i]['repayment_fees']);


                //push repayment rebates
                foreach($post['rebates'] as $c=>$rp){
                    $postData[$i]['rebates'][$c]['id_repayment_transaction'] = $id_repayment_transaction;
                }

                DB::table('repayment_rebates')
                ->insert($postData[$i]['rebates']);      


                $this->CRV($id_repayment_transaction,0);
                $crv = CRVModel::RepaymentCRV($id_repayment_transaction,$edited,$canceled);
                $token = DB::table('repayment_transaction')->select('repayment_token')->where('id_repayment_transaction',$id_repayment_transaction)->first()->repayment_token;
                if($rt_opcode == 0){
                    $RepaymentController->post_or(new Request(['repayment_token'=>$token,
                                                                'or_no'=>$postOBJ['or_number'],
                                                                'or_opcode'=>0]));
                }else{
                    $RepaymentController->GenerateRepaymentCashReceiptData($id_repayment_transaction,$postOBJ['or_number']);
                }
                DB::table('repayment_transaction')
                ->where('id_repayment_transaction',$id_repayment_transaction)
                ->update(['id_cash_receipt_voucher'=>$crv['id_cash_receipt_voucher']]);

            }else{
                 $this->CRV($id_repayment_transaction,10);
            }

            //Loan Status Update
            $loan_ids = DB::table('loan')->select('id_loan')->whereIn('loan_token',$tokenList)->get()->pluck('id_loan')->toArray();  

            $this->UpdateLoanStatus($loan_ids,$tokenList,$date);

            $repayment_parent_loans = array_merge($repayment_parent_loans,$loan_ids);


        }
        $repayment_parent_loans = array_values(array_unique($repayment_parent_loans));
        $rparent_loan = array();

        foreach($repayment_parent_loans as $rl){
            $rparent_loan[]=[
                'id_repayment'=>$id_repayment,
                'id_loan'=>$rl
            ];
        }


        DB::table('repayment_parent_loan')
        ->where('id_repayment',$id_repayment)
        ->delete();

        DB::table('repayment_parent_loan')
        ->insert($rparent_loan);

        DB::select("UPDATE (
        SELECT id_repayment,SUM(total_payment) as total
        FROM repayment_transaction
        WHERE id_repayment = ?) as k
        LEFT JOIN repayment as r on r.id_repayment = k.id_repayment
        SET r.total_amount = total;",[$id_repayment]);


        //Generate JV
        $id_repayment_transactions = DB::table('repayment_transaction as rt')
                           ->select('id_repayment_transaction','id_journal_voucher')
                            ->where('status','<>',10)
                            ->where('id_repayment',$id_repayment)
                            ->get();
        foreach($id_repayment_transactions as $id){
            // JVModel::RepaymentJV($id->id_repayment_transaction,($id->id_journal_voucher > 0)?true:false,false);
        }

        $data['ID_REPAYMENT'] = $id_repayment;
        $data['RESPONSE_CODE'] = "SUCCESS";
        return $data;
        dd("SUCCESS");
        
        
        // DB::beginTransaction();
        try{



            DB::commit();
        }catch(\Exception $e){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Something went wrong";
            $data['error'] = $e->getMessage();
            return $data;
        }

    }

    public function CRV($id_repayment_transaction,$status){
        DB::table('cash_receipt_voucher')
        ->where('type',2)
        ->where('reference',$id_repayment_transaction)
        ->update(['status'=>$status]);

        DB::table('cash_receipt')
        ->where('type',3)
        ->where('reference_no',$id_repayment_transaction)
        ->update(['status'=>$status,"cancel_reason"=>""]);
    }

    public function CompileRepaymentTransaction($date,$payments,$AppPaymentCompile){
  
        $PostOBJ = array();
        foreach($payments as $pay){
            $tempPostOBJ=array();

            $tempPostOBJ['id_repayment_transaction'] = $pay['id_repayment_transaction'];
            $tempPostOBJ['repayment_loans'] = array();
            $tempPostOBJ['repayment_loan_surcharges'] = array();
            $tempPostOBJ['repayment_fees'] = array();

            $fully_paid_loan = array(); 
            $rebatesObj = array();
            $total_rebates = 0;
            $total_penalty=0;

            $total_loan_payment = 0;
            foreach($pay['loan_payment'] as $token=>$lp){
                $app_payment = $AppPaymentCompile[$token];
    

                $total_loan_payment += $lp['loan_payment'];
                if(ROUND($lp['loan_payment'],2) == ROUND($app_payment['remaining_total'],2)){
                    array_push($fully_paid_loan,$token);
                }

                $tempPostOBJ['repayment_loans'] = array_merge($tempPostOBJ['repayment_loans'],$app_payment['payments']);

                //penalty
                if($lp['penalty'] > 0){
                    $tempPostOBJ['repayment_loan_surcharges'][] = [
                        'id_loan'=>$app_payment['id_loan'],
                        'amount'=>$lp['penalty']
                    ];
                    $total_penalty += $lp['penalty'];
                }
            }
            //rebates
            if(count($fully_paid_loan) > 0 && env('WITH_REBATES_ON_FULL')){
                $rebates = DB::table('loan')
                ->select(DB::raw("loan.id_loan,loan_token,getLoanRebates(loan.id_loan,'$date') as rebates"))
                ->whereIn('loan_token',$fully_paid_loan)
                ->get();

                foreach($rebates as $rb){
                    if($rb->rebates > 0){
                        $rebatesObj[]=[
                            'id_repayment_transaction'=>0,
                            'id_loan'=>$rb->id_loan,
                            'amount'=>$rb->rebates
                        ];
                        $total_rebates += $rb->rebates;
                    }
                }
            }
            $tempPostOBJ['rebates'] = $rebatesObj;

            //CBU
            if($pay['cbu'] > 0){
                $tempPostOBJ['repayment_fees'][]=[
                    'id_payment_type'=>config('variables.default_cbu'),
                    'amount'=>$pay['cbu']
                ];
            }

            $tempPostOBJ['repayment_transaction'] = [
                'id_member'=>$pay['id_member'],
                'total_loan_payment'=>$total_loan_payment,
                'total_rebates'=>$total_rebates,
                'swiping_amount'=>0,
                'total_penalty'=>0,
                'total_fees'=>$pay['cbu'],
                'change'=>0,
                'total_payment'=>$total_loan_payment+$pay['cbu']+$total_penalty-$total_rebates,
                'input_mode'=>1
            ];


            array_push($PostOBJ,$tempPostOBJ);
        } 


        return $PostOBJ;
    }
    public function UpdateLoanStatus($loan_ids,$loan_tokens,$date){
        DB::table('loan_table')
        ->whereIn('id_loan',$loan_ids)
        ->update(['is_paid'=>DB::raw("CASE
            WHEN ifnull(getLoanTotalTermPayment(id_loan,term_code),0) = 0 THEN 0
            WHEN (total_due-ifnull(getLoanTotalTermPayment(id_loan,term_code),0)) =0 THEN 1
            ELSE 2 END")]);

        for($i=0;$i<count($loan_tokens);$i++){
            $count_not_paid = DB::table('loan')
            ->leftJoin('loan_table as lt','lt.id_loan','loan.id_loan')
            ->where('loan_token',$loan_tokens[$i])
            ->whereIn('is_paid',[0,2])
            ->count();

            if($count_not_paid == 0){ // All repayment is paid (UPDATE TO CLOSE STATUS)
                DB::table('loan')
                ->where('loan_token',$loan_tokens[$i])
                ->where('loan_status',1)
                ->where('status',3)
                ->update([
                    'status'=>6,
                    'loan_status' => 2
                ]);
            }else{
                $dt = WebHelper::ConvertDatePeriod($date);
                $dt_query = env('REPAYMENT_INTEREST_FULL_CONTRACT')?'loan.maturity_date':"'$dt'";

      
                $balance_as_of = DB::table('loan')
                                 ->select(DB::raw("getLoanBalanceAsOf(id_loan,$dt_query) as bal"))
                                 ->where('loan_token',$loan_tokens[$i])
                                 ->first();
                                 
                if(isset($balance_as_of) && $balance_as_of->bal <= 0){
                    //close the loan if the principal and current interest and fees are paid
                    DB::table('loan')
                    ->where('loan_token',$loan_tokens[$i])
                    ->where('loan_status',1)
                    ->where('status',3)
                    ->update([
                        'status'=>6,
                        'loan_status' => 2
                    ]);
                }else{
                    DB::table('loan')
                    ->where('loan_token',$loan_tokens[$i])
                    ->update([
                        'status'=>3,
                        'loan_status' => 1
                    ]);
                }
            }
        }        
    }

    public function updateStatus(Request $request){
        $id_repayment  = $request->id_repayment;
        $cancel_reason = $request->cancel_reason;

        $details = DB::table('repayment')->where('id_repayment',$id_repayment)->first();

        if($details->status == 10){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Loan Payment already cancelled";

            return response($data);
        }

        $id_repayment_transactions = DB::table('repayment_transaction as rt')
                                   ->select('id_repayment_transaction')
                                    ->where('status','<>',10)
                                    ->where('id_repayment',$id_repayment)
                                    ->get()->pluck('id_repayment_transaction')->toArray();

        $repayment = new RepaymentController();
        foreach($id_repayment_transactions as $id_repayment_transaction){
            $r = new Request(['id_repayment_transaction'=>$id_repayment_transaction,'cancel_repayment'=>$cancel_reason,'no_entry'=>false]);

            $repayment->cancel_repayment($r);
            
        }

        DB::table('repayment')->where('id_repayment',$id_repayment)->update(['status'=>10,'status_user'=>MySession::myId(),'status_date'=>now(),'reason'=>$cancel_reason]);
        $data['RESPONSE_CODE'] = "SUCCESS";
        return response($data);

    }
    public function generateRandomString($length = 8) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}



