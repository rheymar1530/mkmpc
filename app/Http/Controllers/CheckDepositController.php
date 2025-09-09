<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MySession;
use DB;
use App\Loan;
use App\Member;
use App\CredentialModel;
use DateTime;
use PDF;
use App\CRVModel;

class CheckDepositController extends Controller
{
    public function index(Request $request){
        $data['credential']= CredentialModel::GetCredential(MySession::myPrivilegeId());
        if(!$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }  
        $data['deposits'] = DB::table('check_deposit as cd')
                            ->select(DB::raw("cd.id_check_deposit,DATE_FORMAT(cd.date_deposited,'%m/%d/%Y') as date_deposited,bank.bank_name,cd.remarks,cd.amount,DATE_FORMAT(cd.date_created,'%m/%d/%Y') as date_created,
                                CASE WHEN cd.status = 0 THEN 'Posted'
                                     WHEN cd.status = 10 THEN 'Cancelled'
                                     ELSE ''
                                     END as status_description,cd.status,group_concat(rp.check_no SEPARATOR ', ') as check_nos"))
                            ->leftJoin('check_deposit_details as cdd','cdd.id_check_deposit','cd.id_check_deposit')
                            ->leftJoin('repayment_payment as rp','rp.id_repayment_payment','cdd.id_repayment_payment')
                            ->leftJoin('tbl_bank as bank','bank.id_bank','cd.id_bank')
                            ->groupBy('cd.id_check_deposit')
                            ->orderBy('cd.id_check_deposit','DESC')
                            ->get();

    
        
        return view('check_deposit.index',$data);
    }
    public function create(Request $request){
        $data['checks'] = $this->ParseChecks(-1);
        $data['opcode'] = 0;
        $data['banks'] = DB::table('tbl_bank')
                         ->select(DB::raw("id_bank,bank_name"))
                         ->get();

        return view('check_deposit.form',$data);
    }

    public function edit($id_check_deposit,Request $request){

        $data['details'] = DB::table('check_deposit')->where('id_check_deposit',$id_check_deposit)->first();

        if( $data['details']->status != 0){
            $url = "/check-deposit/view/$id_check_deposit?".http_build_query($request->all());
            return redirect()->to($url);            
        }
        $data['checks'] = $this->ParseChecks($id_check_deposit);
        $data['opcode'] = 1;
        $data['banks'] = DB::table('tbl_bank')
                         ->select(DB::raw("id_bank,bank_name"))
                         ->get();

        return view('check_deposit.form',$data);
    }

    public function view($id_check_deposit,Request $request){
        $data= $this->viewData($id_check_deposit);

        return view('check_deposit.view',$data);
    }
    public function print($id_check_deposit){
        $data= $this->viewData($id_check_deposit);

        // dd($data);
        $data['file_name'] = "Check Deposit ID# {$id_check_deposit}";
        $html = view('check_deposit.print',$data);
        $pdf = PDF::loadHtml($html);
        $pdf->setOption("encoding","UTF-8");
        $pdf->setOption('margin-bottom', '0.33in');
        $pdf->setOption('margin-top', '0.33in');
        $pdf->setOption('margin-right', '0.33in');
        $pdf->setOption('margin-left', '0.42in');
        $pdf->setOption('header-left', 'Page [page] of [toPage]');
        // $pdf->setOption('header-right', 'No.: '.$data['details']->month_year.'-'.$data['details']->id_repayment_statement);
    
        $pdf->setOption('header-font-size', 8);
        $pdf->setOption('header-font-name', 'Calibri');

        return $pdf->stream("{$data['file_name']}.pdf",array('Attachment'=>1));
    }

    public function viewData($id_check_deposit){
        $data['details'] = DB::table('check_deposit as cd')
                         ->select(DB::raw("cd.id_check_deposit,DATE_FORMAT(cd.date_deposited,'%m/%d/%Y') as date,tb.bank_name,remarks,cd.amount,cd.status,DATE_FORMAT(cd.date_created,'%m/%d/%Y %h:%i %p') as date_created,DATE_FORMAT(cd.status_date,'%m/%d/%Y %h:%i %p') as status_date,cd.reason,cd.id_cash_receipt_voucher, CASE WHEN cd.status = 0 THEN 'Posted'
                                     WHEN cd.status = 10 THEN 'Cancelled'
                                     ELSE ''
                                     END as status_description"))
                          ->leftJoin('tbl_bank as tb','tb.id_bank','cd.id_bank')
                          ->where('cd.id_check_deposit',$id_check_deposit)
                          ->first();

        $data['deposits'] = DB::select("SELECT r.id_repayment,rp.id_repayment_payment,r.date,DATE_FORMAT(r.date,'%m/%d/%Y') as transaction_date,RepaymentDescription(r.payment_for,r.id_repayment) as description,concat('Bulk Loan Payment No.',r.id_repayment) as reference,
        rp.check_no,rp.check_bank,DATE_FORMAT(rp.check_date,'%m/%d/%Y') as check_date,rp.amount,1 as selected
        FROM check_deposit_details as cdd
        LEFT JOIN repayment_payment as rp on rp.id_repayment_payment = cdd.id_repayment_payment
        LEFT JOIN repayment as r on r.id_repayment = rp.id_repayment
        WHERE cdd.id_check_deposit = ?
        ORDER BY r.date DESC",[$id_check_deposit]);

        return $data;
    }

    public function ParseChecks($id_check_deposit){
    //     $payments = DB::select("
    //     SELECT * FROM (
    //         SELECT DATE_FORMAT(r.date_received,'%m/%d/%Y') as date,r.id_repayment as data_id,UPPER(concat(if(bl.type=1,'Brgy. ',''),bl.name,' REPAYMENT')) as description,if(r.id_repayment_statement > 0,concat('Loan Payment Statement No. ',r.id_repayment_statement),concat('Bulk Loan Payment No. ',r.id_repayment)) as reference,
    //         r.check_no,DATE_FORMAT(r.check_date,'%m/%d/%Y') as check_date,r.check_bank, r.total_amount,'R' as type,0 as selected ,if(r.id_repayment_statement >0,1,2) as sub_type
    //         FROM repayment as r
    //         LEFT JOIN baranggay_lgu as bl on bl.id_baranggay_lgu = r.id_baranggay_lgu
    //         WHERE r.status = 0 AND r.id_check_deposit = 0 and r.id_cash_receipt_voucher is null and r.total_amount is not null AND r.id_paymode = 4
    //         UNION ALL
    //         SELECT DATE_FORMAT(r.date_received,'%m/%d/%Y') as date,r.id_repayment as data_id,UPPER(concat(if(bl.type=1,'Brgy. ',''),bl.name,' REPAYMENT')) as description,if(r.id_repayment_statement > 0,concat('Loan Payment Statement No. ',r.id_repayment_statement),concat('Bulk Loan Payment No. ',r.id_repayment)) as reference,
    //         r.check_no,DATE_FORMAT(r.check_date,'%m/%d/%Y') as check_date,r.check_bank, r.total_amount,'R' as type,1 as selected ,if(r.id_repayment_statement >0,1,2) as sub_type
    //         FROM repayment as r
    //         LEFT JOIN baranggay_lgu as bl on bl.id_baranggay_lgu = r.id_baranggay_lgu
    //         WHERE r.id_check_deposit = ?

    // ) as payments
    // ORDER BY payments.date;",[$id_check_deposit]);

        $payments = DB::select("SELECT DISTINCT * FROM (
        SELECT r.id_repayment,rp.id_repayment_payment,r.date,DATE_FORMAT(r.date,'%m/%d/%Y') as transaction_date,RepaymentDescription(r.payment_for,r.id_repayment) as description,concat('Bulk Loan Payment No.',r.id_repayment) as reference,
        rp.check_no,rp.check_bank,DATE_FORMAT(rp.check_date,'%m/%d/%Y') as check_date,rp.amount,0 as selected
        FROM repayment as r
        LEFT JOIN repayment_payment as rp on rp.id_repayment = r.id_repayment
        WHERE r.id_paymode = 4 AND rp.id_repayment is not null AND r.status <> 10 AND rp.status =0
        UNION ALL
        SELECT r.id_repayment,rp.id_repayment_payment,r.date,DATE_FORMAT(r.date,'%m/%d/%Y') as transaction_date,RepaymentDescription(r.payment_for,r.id_repayment),concat('Bulk Loan Payment No.',r.id_repayment) as reference,
        rp.check_no,rp.check_bank,DATE_FORMAT(rp.check_date,'%m/%d/%Y') as check_date,rp.amount,1 as selected
        FROM check_deposit_details as cdd
        LEFT JOIN repayment_payment as rp on rp.id_repayment_payment = cdd.id_repayment_payment
        LEFT JOIN repayment as r on r.id_repayment = rp.id_repayment
        WHERE cdd.id_check_deposit = ?) as checks
        ORDER BY checks.date DESC;",[$id_check_deposit]);

        return $payments;
    }

    public function post(Request $request){
        $opcode = $request->opcode ?? 0;
        $checks = $request->postChecks ?? [];
        
        $id_check_deposit = $request->id_check_deposit ?? 0;
        
        $DepositDetails = $request->DepositDetails;

        $g = new GroupArrayController();
        /*VALIDATION*/


        /*END VALIDATION*/

        $repaymentChecks = array();

        foreach($checks as $c){
           array_push($repaymentChecks,$c['DATA_ID']);
        }

        // $checks = $g->array_group_by($checks,['TYPE']);
        $checkTotal = 0;

        $repayments = array();
        $chkDepositDetails = array();

        $repaymentPaymentCheck =json_decode(DB::table('repayment_payment')
                                ->select('id_repayment_payment','id_repayment','amount')
                                 ->whereIn('id_repayment_payment',$repaymentChecks)
                                 // ->where('status',0)
                                 ->get(),true);


        $checkTotal = collect($repaymentPaymentCheck)->sum('amount');
        $repayment_paymentIds = collect($repaymentPaymentCheck)->pluck('id_repayment_payment')->toArray();


        
        // dd($checkTotal);

    

        // foreach($checks as $type=>$check){

        //     if($type == "R"){
        //         $id_repayments = collect($check)->pluck('DATA_ID')->toArray();
        //         $repayments = DB::table('repayment')
        //         ->where('status','<>',10)
        //         ->whereIn('id_repayment',$id_repayments)
        //         ->where(function($query) use($opcode){
        //             if($opcode == 0){
        //                 $query->where('id_check_deposit','=',0);
        //             }
        //         })
        //         // ->where('id_check_deposit','<>',$id_check_deposit)
        //         ->get();
                
        //         $checkTotal += collect($repayments)->sum('total_amount');
        //         $repaymentsIDS = collect($repayments)->pluck('id_repayment')->toArray();

        //         foreach($repayments as $r){

        //             $chkDepositDetails[]=[
        //                 'id_check_deposit'=>0,
        //                 'id_repayment'=>$r->id_repayment
        //             ];
        //         }
        //     }
        // }

        $postObj = [
            'date_deposited'=>$DepositDetails['date_deposited'],
            'id_bank'=>$DepositDetails['bank'],
            'remarks'=>$DepositDetails['remarks'],
            'amount'=>$checkTotal
        ];

   
        if($opcode == 0){
            DB::table('check_deposit')
            ->insert($postObj);

            $id_check_deposit = DB::table('check_deposit')->max('id_check_deposit');
            // $id_check_deposit =
        }else{

            $this->UpdateStatus($id_check_deposit,0);

            DB::table('check_deposit')
            ->where('id_check_deposit',$id_check_deposit)
            ->update($postObj);

            DB::table('check_deposit_details')->where('id_check_deposit',$id_check_deposit)->delete();
        }

        for($i=0;$i<count($repaymentPaymentCheck);$i++){
            unset($repaymentPaymentCheck[$i]['amount']);
            $repaymentPaymentCheck[$i]['id_check_deposit'] = $id_check_deposit;
        }


        DB::table('check_deposit_details')
        ->insert($repaymentPaymentCheck);

        $this->UpdateStatus($id_check_deposit,1);


        //Update Loan Payment and Loan Payment Statement

        // DB::table('repayment')
        // ->whereIn('id_repayment',$repaymentsIDS)
        // ->update(['id_check_deposit'=>$id_check_deposit,'status'=>1]);


        // DB::table('repayment_statement')
        // ->whereIn('id_repayment',$repaymentsIDS)
        // ->update([
        //         // 'id_check_deposit'=>$id_check_deposit,
        //     'status'=>2
        //     ]);


        CRVModel::CheckDepositCRV($id_check_deposit);


        $data['RESPONSE_CODE'] = "SUCCESS";
        $data['ID_CHECK_DEPOSIT'] = $id_check_deposit;

        return response($data);
        dd($postObj);   
            

        dd($request->all());
    
    }
    public function UpdateStatus($id_check_deposit,$status){
        // DB::table('repayment_statement as rs')
        // ->leftJoin('repayment as r','r.id_repayment','rs.id_repayment')
        // ->where('r.id_check_deposit',$id_check_deposit)
        // ->update(['rs.status'=>1]);
        
        // DB::table('repayment')
        // ->where('id_check_deposit',$id_check_deposit)
        // ->update(['status'=>$status,'id_check_deposit'=>$id_check_deposit_new]);

        DB::select("UPDATE check_deposit_details as cdd
        LEFT JOIN repayment_payment as rpp on rpp.id_repayment = cdd.id_repayment AND cdd.id_repayment_payment = rpp.id_repayment_payment
        SET rpp.status =$status
        WHERE id_check_deposit = ?;",[$id_check_deposit]);

        DB::select("UPDATE  (
        SELECT r.id_repayment,COUNT(*) as check_count,SUM(CASE WHEN rp.status=1 THEN 1 ELSE 0 END) as deposited_count 
        FROM (
        SELECT cdd.id_repayment FROM check_deposit_details as cdd
        LEFT JOIN repayment_payment as rpp on rpp.id_repayment = cdd.id_repayment AND cdd.id_repayment_payment = rpp.id_repayment_payment
        WHERE id_check_deposit = ?
        GROUP BY id_repayment) as r
        LEFT JOIN repayment_payment as rp on rp.id_repayment = r.id_repayment
        GROUP BY r.id_repayment) as k
        LEFT JOIN repayment as r on r.id_repayment = k.id_repayment
        SET r.deposit_status = if(deposited_count=0,0,if(check_count=deposited_count,2,1));",[$id_check_deposit]);



    }

    public function postStatus(Request $request){
        $id_check_deposit = $request->id_check_deposit;
        $status = 10;
        $reason = $request->reason;

        $details = DB::table('check_deposit')->where('id_check_deposit',$id_check_deposit)->first();

        if($details->status == 10){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Invalid Request";
            return response($data);
        }


        DB::table('check_deposit')
        ->where('id_check_deposit',$id_check_deposit)
        ->update(['status'=>$status,'reason'=>$reason,'status_date'=>DB::raw("now()")]);


        $this->UpdateStatus($id_check_deposit,0,0);

        DB::table('cash_receipt_voucher')
        ->where('id_cash_receipt_voucher',$details->id_cash_receipt_voucher)
        ->update(['status'=>10,'description'=>DB::raw("concat(description,' [CANCELLED]')"),'date_cancelled'=>DB::raw("now()"),'cancellation_reason'=>$reason]);


        $data['RESPONSE_CODE'] = "SUCCESS";
        return response($data);
    }
}
