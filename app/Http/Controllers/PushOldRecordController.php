<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Loan;
use DateTime;
use App\JVModel;
use App\CRVModel;

class PushOldRecordController extends Controller
{
    public function updateAsset(){
        // SELECT aa.total,ad.depreciation_amount FROM asset_depreciation as ad 
        // LEFT JOIN (SELECT year as ayear,SUM(depreciation_amount) as total,id_asset as iasset FROM asset_depreciation_month as ad
        // where id_asset = 3
        // GROUP BY year) as aa on aa.ayear = ad.year AND aa.iasset = ad.id_asset
        // where ad.id_asset = 3;

        /***********UPDATE YEARLY DEPRECIATION***************/
        // UPDATE asset_depreciation as ad 
        // LEFT JOIN (SELECT year as ayear,SUM(depreciation_amount) as total,id_asset as iasset FROM asset_depreciation_month as ad
        // where id_asset = 3
        // GROUP BY year) as aa on aa.ayear = ad.year AND aa.iasset = ad.id_asset
        // SET ad.depreciation_amount = aa.total
        // where ad.id_asset = 3;
        $id_asset = [2,3];
        $type = [1,2];
        // $type = [1];
        $db = "smestcc";
        foreach($id_asset as $id){
            foreach($type as $t){
                if($t == 1){
                    $dep = DB::table("$db.asset_depreciation")
                           ->select(DB::raw("*,id_asset_depreciation as id"))
                           ->where('id_asset',$id)
                           ->orDerby("id_asset_depreciation",'ASC')
                           ->get();                    
                }else{
                    $dep = DB::table("$db.asset_depreciation_month")
                           ->select(DB::raw("*,id_asset_depreciation_month as id"))
                           ->where('id_asset',$id)
                           ->orDerby("id_asset_depreciation_month",'ASC')
                           ->get();                     
                } 

                $start_book_value = $dep[0]->start_book_value;
                $accumulated_depreciation = 0;
                $update = array();
                foreach($dep as $d){
                    $temp = array();
                    $temp['start_book_value'] = $start_book_value;
                    $temp['depreciation_amount'] = $d->depreciation_amount;

                    $accumulated_depreciation += $d->depreciation_amount;
                    $accumulated_depreciation = ROUND($accumulated_depreciation,2);
                    if($t == 2){
                        $temp['accumulated_depreciation'] = $accumulated_depreciation;
                    }
                    $start_book_value -= $d->depreciation_amount;
                    $start_book_value = ROUND($start_book_value,2);
                    $temp['end_book_value'] = $start_book_value;


                    DB::table("$db.asset_depreciation".(($t==1)?"":"_month"))
                    ->where("id_asset_depreciation".(($t==1)?"":"_month"),$d->id)
                    ->update($temp);

                    array_push($update,$temp);
                }
            }
        }
        return $update;

        return $start_book_value;
    }
    public function UpdateLoanEntry(){
        $loans = DB::select("select * FROm loan where loan.id_cash_disbursement > 0;");
        foreach($loans as $l){
            Loan::GenerateCDV($l->id_loan);
        }
        return "success";
    }

    public function UpdateRepaymentEntry(){
        $repayments = DB::select("SELECT * FROM smestcc.repayment_transaction where (id_journal_voucher > 0 or id_cash_receipt_voucher > 0) and total_loan_payment > 0;");


        foreach($repayments as $r){
            if($r->id_journal_voucher > 0){
                JVModel::RepaymentJV($r->id_repayment_transaction,true,false);
            }else{
                CRVModel::RepaymentCRV($r->id_repayment_transaction,true,false);
            }
        }

        return "success";

    }

    public function UpdateLoanRecompute(){
         // $this->update_loan_token();

         // return $this->push_member();
        // return $this->repayment_autofill2();
        // return $this->generate_repayment2();




        $loans = DB::table('loan')
        ->Select("*")
        ->whereNotNull('id_dum')
        ->where('id_loan',54)
        ->get();

        // return $loans;



        // $disc = $this->loan_with_discrepancy();


        // return $loans;


        foreach($loans as $l){
            $charges_output['DEDUCTED'] =  [];
            $charges_output['NOT_DEDUCTED'] =  [];

            $charges_output['DEDUCTED_TOTAL'] = 0;
            $charges_output['NOT_DEDUCTED_TOTAL'] = 0;


            $charges_output['NOT_DEDUCTED_FIXED_TOTAL'] = 0;
            $charges_output['NOT_DEDUCTED_DIVIDED_TOTAL'] = 0;
            $loan_parameter = [
                'cbu_deficient' => $l->cbu_deficient,
                'charges' => $charges_output,
                'principal_amount' =>$l->principal_amount,
                'interest_rate'=> $l->interest_rate,
                'terms' => $l->terms,
                'term_period' => $l->id_term_period,
                'interest_pediod'=>$l->id_interest_period,
                'interest_method' => $l->id_interest_method,
                'is_cbu_deduct' => $l->is_deduct_cbu,
                'loan_protection_rate' => 0,
                'payment_type' => $l->id_loan_payment_type,
                'previous_loan' => 0,
                'deduct_interest'=>$l->deduct_interest,
                'id_loan_service'=>$l->id_loan_service
            ];

            $loan_details = Loan::ComputeLoan($loan_parameter);

            DB::table('loan')
            ->where('id_loan',$l->id_loan)
            ->update([
                'total_deductions' => $loan_details['TOTAL_DEDUCTED_CHARGES'],
             
                'total_loan_proceeds' => $loan_details['TOTAL_LOAN_PROCEED'],
                'loan_protection_amount' => $loan_details['LOAN_PROTECTION_AMOUNT'],
                'not_deducted_charges' => $loan_details['TOTAL_NOT_DEDUCTED_CHARGES'],
                'loan_amount'=>$loan_details['TOTAL_AMOUNT_DUE'],
                'prev_loan_balance' => $loan_details['LOAN_BALANCE'],
                'prev_loan_rebates' => $loan_details['REBATES']
            ]);
            $loan_table = $loan_details['LOAN_TABLE'];
            $loan_table_param = array();
            foreach($loan_table as $tb){
                $loan_table_param[]=[
                    'id_loan'=>$l->id_loan,
                    'count' => $tb['count'],
                    'term_code'=>$tb['term_code'],
                    'repayment_amount'=>$tb['repayment_amount'],
                    'interest_amount'=>$tb['interest_amount'],
                    'fees' => $tb['fees'],
                    'total_due' => $tb['total_due'],
                    'due_date' => ($l->id_loan_payment_type == 2)?$l->form_due_date:null
                ];
            }


            // DB::table('loan_table')
            // ->insert($loan_table_param);

            $loanController = new LoanApprovalController();
            $due_dates = $loanController->getLoanDueDate($l->date_released,$l->terms);

            $loanController->add_loan_due_date($l->id_loan,$due_dates);

            $other_charges_param = array();
            foreach($loan_details['OTHER_DEDUCTIONS'] as $oth){
                $temp = array();
                foreach($oth as $key=>$val){
                    $temp[$key]=$val;
                }
                $temp['id_loan'] = $l->id_loan;
                array_push($other_charges_param,$temp);
            }
            DB::table('loan_charges')
            ->where('id_loan',$l->id_loan)
            ->where('id_loan_fees',12)
            ->delete();

            DB::table('loan_charges')
            ->insert($other_charges_param);

            DB::table('loan_charges')
            ->where('id_loan',$l->id_loan)
            ->where('id_loan_fees',3)
            ->delete();
        }

        // DB::table('loan_charges')->wherein('id_loan',$disc)->delete();

        return $loans;
    }


    public function generateLoanTable(){ //<----- THIS IS THE FUNCTION
         // $this->update_loan_token();

         // return $this->push_member();
        // return $this->repayment_autofill2(); //<----- FUNCTION TO PUSH REPAYMENT
        // return $this->repayment_autofill3(); //<----- FUNCTION TO PUSH REPAYMENT
        // return $this->generate_repayment2();

        $loanss_id = [69,114,153,278]; 


        // $loans = DB::table('loan')
        // ->Select(DB::raw("*,concat(YEAR(curdate())+if(MONTH(curdate()) > ls.end_month_period,0,0),'-',ls.end_month_period,'-',ls.repayment_schedule) as form_due_date"))
        // ->leftJoin('loan_service as ls','ls.id_loan_service','loan.id_loan_service')
        // ->whereNotNull('id_dum')
        // ->whereIn('id_loan',$loanss_id)
        // ->get();

        $loans = DB::table('loan')
        ->Select(DB::raw("*,concat(YEAR(dal.MATURITYDATE),'-',ls.end_month_period,'-',ls.repayment_schedule) as form_due_date"))
        ->leftJoin('loan_service as ls','ls.id_loan_service','loan.id_loan_service')
        ->leftJoin('dummy_act_loan as dal','dal.id_dummy_act_loan','loan.id_loan')
        ->whereNotNull('id_dum')
        ->whereIn('id_loan',$loanss_id)
        ->get();

        //DISABLE FIRST THE

        foreach($loans as $l){
            $charges_output['DEDUCTED'] =  [];
            $charges_output['NOT_DEDUCTED'] =  [];

            $charges_output['DEDUCTED_TOTAL'] = 0;
            $charges_output['NOT_DEDUCTED_TOTAL'] = 0;


            $charges_output['NOT_DEDUCTED_FIXED_TOTAL'] = 0;
            $charges_output['NOT_DEDUCTED_DIVIDED_TOTAL'] = 0;
            $loan_parameter = [
                'cbu_deficient' => $l->cbu_deficient,
                'charges' => $charges_output,
                'principal_amount' =>$l->principal_amount,
                'interest_rate'=> $l->interest_rate,
                'terms' => $l->terms,
                'term_period' => $l->id_term_period,
                'interest_pediod'=>$l->id_interest_period,
                'interest_method' => $l->id_interest_method,
                'is_cbu_deduct' => $l->is_deduct_cbu,
                'loan_protection_rate' => 0,
                'payment_type' => $l->id_loan_payment_type,
                'previous_loan' => 0,
                'deduct_interest'=>$l->deduct_interest,
                'id_loan_service'=>$l->id_loan_service,
                'id_member'=>$l->id_member
            ];

            $loan_details = Loan::ComputeLoan($loan_parameter);

            // dd($loan_details);
            $loan_table = $loan_details['LOAN_TABLE'];


            $loan_table_param = array();
            foreach($loan_table as $tb){
                $loan_table_param[]=[
                    'id_loan'=>$l->id_loan,
                    'count' => $tb['count'],
                    'term_code'=>$tb['term_code'],
                    'repayment_amount'=>$tb['repayment_amount'],
                    'interest_amount'=>$tb['interest_amount'],
                    'fees' => $tb['fees'],
                    'total_due' => $tb['total_due'],
                    'due_date' => ($l->id_loan_payment_type == 2)?$l->form_due_date:null
                ];
            }

            // dd($loan_table_param);
            DB::table('loan_table')
            ->insert($loan_table_param);

            $loanController = new LoanApprovalController();
            $due_dates = $loanController->getLoanDueDate($l->date_released,$l->terms);

            if($l->id_loan_payment_type == 1){
                $loanController->add_loan_due_date($l->id_loan,$due_dates);
            }
            

            $other_charges_param = array();
            foreach($loan_details['OTHER_DEDUCTIONS'] as $oth){
                $temp = array();
                foreach($oth as $key=>$val){
                    $temp[$key]=$val;
                }
                $temp['id_loan'] = $l->id_loan;
                array_push($other_charges_param,$temp);
            }

            DB::table('loan_charges')
            ->insert($other_charges_param);

            DB::table('loan_charges')
            ->where('id_loan',$l->id_loan)
            ->where('id_loan_fees',3)
            ->delete();
        }

        // DB::table('loan_charges')->wherein('id_loan',$disc)->delete();

        return $loans;
    }

    // public function generate_repayment2(){
    //     $disc = $this->loan_with_discrepancy();
    //     $loans = DB::table('loan')
    //     ->Select("*")
    //     ->whereNotNull('id_dum')
    //     ->whereNotIn('id_dum',$disc)
    //     ->get();


    //     foreach($loans as $l){
    //         $loan_table = DB::table('loan_table')->where('id_loan',$l->id_loan)->get();

    //         return $loan_table;
    //     }

    //     return $loans;
    // }

    public function repayment_autofill(){
        $disc = $this->loan_with_discrepancy();
        $loans = DB::table('loan')
        ->Select("*")
        ->whereNotNull('id_dum')
        ->whereNotIn('id_dum',$disc)
        // ->where('id_loan',44)
        ->get();     

        foreach($loans as $l){
            $output= $this->generate_repayment1($l->id_loan);

            if($output['total_paid'] > 0){


                $repayment = array(
                    'transaction_type'=>2,
                    'id_bank' => 2,
                    'repayment_token' => DB::raw("concat(DATE_FORMAT(now(),'%m%d%Y%H%i%s'),concat(LEFT(MD5(NOW()), 5)))"),
                    'transaction_date' => DB::raw('curdate()'),
                    'date' =>$output['last_date'],
                    'id_member'=>$l->id_member,
                    'total_loan_payment'=>$output['total_paid'],
                    'total_fees'=>0,
                    'total_penalty'=>0,
                    'swiping_amount'=>$output['total_paid'],
                    'change'=>0,
                    'total_payment'=>$output['total_paid'],
                    'change_status'=>1,
                    'email_sent'=>2
                );

                DB::table('repayment_transaction')
                ->insert($repayment);


                $id_repayment_transaction = DB::table('repayment_transaction')->max('id_repayment_transaction');
                for($i=0;$i<count($output['loan_table']);$i++){
                    $output['loan_table'][$i]['id_repayment_transaction'] = $id_repayment_transaction;
                }

                DB::table('repayment_loans')
                ->insert($output['loan_table']);

                DB::table('loan_table')
                ->where('id_loan',$l->id_loan)
                ->update(['is_paid'=>DB::raw("CASE
                    WHEN ifnull(getLoanTotalTermPayment(id_loan,term_code),0) = 0 THEN 0
                    WHEN (total_due-ifnull(getLoanTotalTermPayment(id_loan,term_code),0)) =0 THEN 1
                    ELSE 2 END")]);
            }

        }
        return "success";
        return $loans;   
    }

    public function generate_repayment1($id_loan){
        $loan_details =DB::table('loan')->where('id_loan',$id_loan)->first();

        $paid = DB::select("SELECT SUM(LoanPayprincipal) as total_principal,SUM(LoanPayinterest) as total_interest FROM dummy_loan_ledger 
            WHERE id_parent_dummy_loan =?",[$loan_details->id_dum])[0];

        $total_paid_principal = $paid->total_principal;

        $total_paid_interest = $paid->total_interest; //366

        $loan_table = DB::table('loan_table')
        ->where('id_loan',$id_loan)
        ->get();
        $repayment_loan_obj = array();
        $output = array();
        $last_date = "";
        $total_paid = 0;
        foreach($loan_table as $lt){
            $temp = array();

            if($total_paid_principal >= $lt->repayment_amount){
                $p_principal = $lt->repayment_amount;


            }elseif($total_paid_principal < $lt->repayment_amount && $total_paid_principal > 0){
                $p_principal = $total_paid_principal;
                
            }else{
                $p_principal = 0;
            }
            $temp['id_loan'] = $lt->id_loan;
            $temp['term_code'] = $lt->term_code;
            $temp['paid_fees'] = 0;
            $temp['type'] = 1;
            $temp['paid_principal'] = $p_principal;
            $total_paid_principal = ROUND($total_paid_principal-$temp['paid_principal'],2);


            if($total_paid_interest >= $lt->interest_amount){
                $p_interest = $lt->interest_amount;
                
            }elseif($total_paid_interest < $lt->interest_amount && $total_paid_interest > 0){
                $p_interest = $total_paid_interest;
            }else{
                $p_interest = 0;
            }
            $temp['paid_interest'] = $p_interest;
            $total_paid_interest = ROUND($total_paid_interest-$temp['paid_interest'],2);      


            if($temp['paid_principal'] > 0 || $temp['paid_interest'] > 0){
                $last_date = $lt->due_date;
            }

            $total_paid += ($temp['paid_principal']+$temp['paid_interest']);    
            if($temp['paid_principal']+$temp['paid_interest'] > 0){
                array_push($repayment_loan_obj,$temp);
            }
        }   

        $output['loan_table'] = $repayment_loan_obj;
        $output['last_date'] =$last_date;
        $output['total_paid'] = ROUND($total_paid,2);
        return $output;
    }

    public function push_member(){
        ini_set('memory_limit', '-1');
        set_time_limit(-1);
        $jsonString=file_get_contents(public_path("/member.json")) ;


        // return $jsonString;


        $key = array();
        $member = json_decode($jsonString,true);

        $insert_obj = array();
        // $id_members = [2,8,10,20,21,22,23,25,26,34,36,41,46,47,48,50,51,56,57,61,63,64,65,67,68,70,71,72,74,75,76,78,79,80,81,82,83,84,85,86,87,96,98,99,100,101,102,104,105,107,110,112,115,116,119,120,124,125,126,128,129,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,155,157,158,159,160,161,162,164,165,166,167,168,169,172,173,174,175,176,177,178,179,180,181,182,183,184,185,186,188,189,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,206,207,208,209,210,211,213,214,215,216,217,218,219,221,222,223,224,225,226,227,228,229,230,231,232,234,235,237,239,240,241,242,243,244,245,246,247,248,249,251,252,254,257];

        foreach($member as $mem){

            $temp = array();

            // if(in_array($mem['id_member'],$id_members)){



                foreach($mem as $key=>$m){
                    $temp[$key] = $m;
                }

                // if($temp['MemNonMember'] == "Member"){
                //     $temp['memb_type'] =1;
                // }else{
                //     $temp['memb_type'] =0;
                // }


                if($temp['id_civil_status'] == "Married"){
                    $temp['id_civil_status'] =2;
                }elseif($temp['id_civil_status'] == "Single"){
                    $temp['id_civil_status'] =1;
                }elseif($temp['id_civil_status'] == "Widowed"){
                    $temp['id_civil_status'] =5;
                } else{
                    $temp['id_civil_status'] =0;
                }

                if($temp['status'] == "Active"){
                    $temp['status'] =1;
                }else{
                    $temp['status'] =0;
                }

                if($temp['membership_date'] == ""){
                    $temp['membership_date'] = null;
                }

                if($temp['date_of_birth'] == ""){
                    $temp['date_of_birth'] = null;
                }
                // unset($temp['MemNonMember']);
                // unset($temp['CivilStat']);
                // unset($temp['Status']);

                array_push($insert_obj,$temp);
            // }
        }


        // return $insert_obj;

        DB::table('member')
        ->insert($insert_obj);
        return "success";
    }

    public function loan_with_discrepancy(){
        $data = DB::select("SELECT * FROM (
            SELECT dal.id_dummy_act_loan,dal.recno as 'Loan Rec No',dal.PRINCIPALBALANCE,(LOANRELEASE-SUM(LoanPayPrincipal)) as ledger_principal_bal,
            LOANRELEASE,SUM(LoanPayPrincipal) as total_paid_principal,SUM(LoanPayinterest) as total_paid_interest
            FROM dummy_act_loan as dal
            LEFT JOIN dummy_loan_ledger as dll on dll.id_parent_dummy_loan = dal.id_dummy_act_loan
            WHERe dal.id_dummy_act_loan not in (35,47,130,134,174,186,221,238,288,362,393,440)
            GROUP BY dal.id_dummy_act_loan) as g
        WHERE g.PRINCIPALBALANCE <> ledger_principal_bal;");

        $output = array();
        // $output=[153,206];
        // $output =[35];
        $output = [0];

        // foreach($data as $k){
        //     array_push($output,$k->id_dummy_act_loan);
        // }

        return $output;
    }

    public function update_loan_token(){
        $disc = $this->loan_with_discrepancy();
        $loans = DB::table('loan')
        ->Select("*")
        ->whereNotNull('id_dum')
        // ->whereNotIn('id_dum',$disc)
        ->get();    

        foreach($loans as $l){
            DB::table('loan')
            ->where('id_loan',$l->id_loan)
            ->update(['loan_token'=>DB::raw("concat(id_loan,DATE_FORMAT(now(),'%m%d%Y%H%i%s'),concat(LEFT(MD5(NOW()), 5)))")]);
        }
    }
    public function repayment_autofill3(){
        ini_set('memory_limit', '-1');
        set_time_limit(-1);
        $dis = $this->loan_with_discrepancy();

        $members = DB::table('dummy_act_loan')
        ->select("MemID")   
        ->whereNotIn('id_dummy_act_loan',$dis)
        // ->where('id_dummy_act_loan',127)
        // ->whereiN('id_dummy_act_loan',[34,123])
                   // ->where('MemID','=',69)
        ->groupby('MemID')
        ->whereIn('id_dummy_act_loan',[35,345])
        // ->limit(10)
        ->get();

        $loansss = [35,345];





        // return $

        // return $members;
        // return 123;

        $g = new GroupArrayController();

        foreach($members as $mem){
            $transaction_dates = DB::select("SELECT dll.date,dal.id_dummy_act_loan,dll.LoanPayprincipal,dll.LoanPayinterest FROM dummy_loan_ledger as dll
                LEFT JOIN dummy_act_loan as dal on dal.id_dummy_act_loan = dll.id_parent_dummy_loan
                WHERE dll.id_parent_dummy_loan > 0 and dal.LoanService > 0 AND MemID=? and dal.id_dummy_act_loan  in (".implode(',',$loansss).")  ORDER BY dll.date ASC;",[$mem->MemID]);

           
                // and dal.id_dummy_act_loan=127
 // $transaction_dates = DB::select("SELECT dll.date,dal.id_dummy_act_loan,dll.LoanPayprincipal,dll.LoanPayinterest FROM dummy_loan_ledger as dll
 //                LEFT JOIN dummy_act_loan as dal on dal.id_dummy_act_loan = dll.id_parent_dummy_loan
 //                WHERE dll.id_parent_dummy_loan > 0 and dal.LoanService > 0 AND MemID=? and dal.id_dummy_act_loan  in (34,123)  ORDER BY dll.date ASC;",[$mem->MemID]);



                $transactions = $g->array_group_by($transaction_dates,['date']);


                // return $transactions;

                foreach($transactions as $dates=>$transaction){
                    // dd($transaction);
                    $id_repayment_transaction = DB::table('rep_up_ref')->select('id_repayment_transaction')->where('id_loan',$transaction[0]->id_dummy_act_loan)->where('transaction_date',$transaction[0]->date)->first()->id_repayment_transaction;

             
                    foreach($transaction as $tr){
                        $id_loan = DB::table('loan')->where('id_dum',$tr->id_dummy_act_loan)->first()->id_loan;

                        $mat_count = DB::table('loan_table')
                                     ->where('id_loan',$id_loan)
                                     ->where('due_date','>=',date("Y-m-t", strtotime($dates)))
                                     ->count();

                        $ord = ($mat_count==0)?"ASC":"DESC";



                        $paid_principal = $tr->LoanPayprincipal;
                        $paid_interest = $tr->LoanPayinterest;
                        $total_paid= 0;

                        $insert_rp_loan = array();

                        $loan_table = DB::table('loan_table')
                        ->select(DB::raw("*,(repayment_amount-getLoanTotalTermPaymentType(id_loan,term_code,1)) as principal_balance,(interest_amount-getLoanTotalTermPaymentType(id_loan,term_code,2)) as interest_balance"))
                        ->where('id_loan',$id_loan)
                        ->where('due_date','<=',date("Y-m-t", strtotime($dates)))
                        ->orDerby('due_date',$ord)
                        ->get();                       

                           

                        // $loan_table = DB::table('loan_table')
                        // ->select(DB::raw("*,(repayment_amount-getLoanTotalTermPaymentType(id_loan,term_code,1)) as principal_balance,(interest_amount-getLoanTotalTermPaymentType(id_loan,term_code,2)) as interest_balance"))
                        // ->where('id_loan',$id_loan)
                        // ->where('due_date','<=',date("Y-m-t", strtotime($dates)))
                        // ->orDerby('due_date','DESC')
                        // ->get();

                        $due_date = $loan_table[0]->due_date ?? '';
                         //DISTRIBUTE PAYMENT <= DUE DATE OF PAYMENT

                        foreach($loan_table as $lt){
                            $temp = array();

                            $p_principal = $this->calculate($paid_principal,$lt->principal_balance);
                            $p_interest = $this->calculate($paid_interest,$lt->interest_balance);


                            $temp['paid_principal'] = $p_principal;
                            $temp['paid_interest'] = $p_interest;
                            $temp['paid_fees'] = 0;

                            $paid_principal = ROUND($paid_principal-$temp['paid_principal'],2);
                            $paid_interest = ROUND($paid_interest-$temp['paid_interest'],2);

                            $temp['term_code'] = $lt->term_code;
                            $temp['id_loan'] = $lt->id_loan;


                            $total_paid += ($temp['paid_principal']+$temp['paid_interest']);   
                            if(($temp['paid_principal']+$temp['paid_interest']) > 0){
                                array_push($insert_rp_loan,$temp);
                            }                        
                        }
                        
                        // IF PAYMENT <= DUE DATE, APPLY THE PAYMENT ON >= DUE DATE
                        if($paid_principal > 0 || $paid_interest > 0){
                            $loan_table = DB::table('loan_table')
                            ->select(DB::raw("*,(repayment_amount-getLoanTotalTermPaymentType(id_loan,term_code,1)) as principal_balance,(interest_amount-getLoanTotalTermPaymentType(id_loan,term_code,2)) as interest_balance"))
                            ->where('id_loan',$id_loan)
                            ->where('due_date','>',date("Y-m-t", strtotime($dates)))
                            ->orDerby('due_date','ASC')
                            ->get();     

                            foreach($loan_table as $lt){
                                $temp = array();

                                $p_principal = $this->calculate($paid_principal,$lt->principal_balance);
                                $p_interest = $this->calculate($paid_interest,$lt->interest_balance);


                                $temp['paid_principal'] = $p_principal;
                                $temp['paid_interest'] = $p_interest;
                                $temp['paid_fees'] = 0;

                                $paid_principal = ROUND($paid_principal-$temp['paid_principal'],2);
                                $paid_interest = ROUND($paid_interest-$temp['paid_interest'],2);

                                $temp['term_code'] = $lt->term_code;
                                $temp['id_loan'] = $lt->id_loan;

                                if($temp['paid_principal']+$temp['paid_interest'] > 0){
                                    $due_date = $lt->due_date;
                                    array_push($insert_rp_loan,$temp);  
                                }

                                $total_paid += ($temp['paid_principal']+$temp['paid_interest']);   
                            }
                        }

                        // if($paid_interest > 0){
                        //     $paid_principal = $paid_principal+$paid_interest;
                        //     $paid_interest = 0;

                        //     $loan_table = DB::table('loan_table')
                        //     ->select(DB::raw("*,(repayment_amount-getLoanTotalTermPaymentType(id_loan,term_code,1)) as principal_balance,(interest_amount-getLoanTotalTermPaymentType(id_loan,term_code,2)) as interest_balance"))
                        //     ->where('id_loan',$id_loan)
                        //     ->where('due_date','<=',date("Y-m-t", strtotime($dates)))
                        //     ->orDerby('due_date','ASC')
                        //     ->get();     

                        //     foreach($loan_table as $lt){
                        //         $temp = array();

                        //         $p_principal = $this->calculate($paid_principal,$lt->principal_balance);
                        //         $p_interest = $this->calculate($paid_interest,$lt->interest_balance);


                        //         $temp['paid_principal'] = $p_principal;
                        //         $temp['paid_interest'] = $p_interest;
                        //         $temp['paid_fees'] = 0;

                        //         $paid_principal = ROUND($paid_principal-$temp['paid_principal'],2);
                        //         $paid_interest = ROUND($paid_interest-$temp['paid_interest'],2);

                        //         $temp['term_code'] = $lt->term_code;
                        //         $temp['id_loan'] = $lt->id_loan;

                        //         if($temp['paid_principal']+$temp['paid_interest'] > 0){
                        //             $due_date = $lt->due_date;
                        //             array_push($insert_rp_loan,$temp);  
                        //         }
                        //         $total_paid += ($temp['paid_principal']+$temp['paid_interest']);   
                        //     }
                        //     // return $insert_rp_loan;                           
                        // }


                    //PUSH REPAYMENT TRANSACTION
                        if($total_paid > 0){
                            date_default_timezone_set('Asia/Manila');
                            $dt = new DateTime();

                            $repayment = array(
                                'id_repayment_transaction'=>$id_repayment_transaction,
                                'transaction_type'=>2,
                                'id_bank' => 2,
                                'repayment_token' => DB::raw("concat('".$dt->format('YmdHisu')."',concat(LEFT(MD5(NOW()), 5)))"),
                                'transaction_date' => $dates,
                                'date' =>$due_date,
                                'id_member'=>$mem->MemID,
                                'total_loan_payment'=>$total_paid,
                                'total_fees'=>0,
                                'total_penalty'=>0,
                                'swiping_amount'=>$total_paid,
                                'change'=>0,
                                'total_payment'=>$total_paid,
                                'change_status'=>1,
                                'email_sent'=>2
                            );


                            DB::table('repayment_transaction')
                            ->insert($repayment);

                           
                            for($i=0;$i<count($insert_rp_loan);$i++){

                                $insert_rp_loan[$i]['id_repayment_transaction'] = $id_repayment_transaction;
                            }


                            DB::table('repayment_loans')
                            ->insert($insert_rp_loan);

                            DB::table('loan_table')
                            ->where('id_loan',$id_loan)
                            ->update(['is_paid'=>DB::raw("CASE
                                WHEN ifnull(getLoanTotalTermPayment(id_loan,term_code),0) = 0 THEN 0
                                WHEN (total_due-ifnull(getLoanTotalTermPayment(id_loan,term_code),0)) =0 THEN 1
                                ELSE 2 END")]);
                        }

                    }

                }
                }

                return $members;
            }
    public function repayment_autofill2(){
        ini_set('memory_limit', '-1');
        set_time_limit(-1);
        $dis = $this->loan_with_discrepancy();

        $members = DB::table('dummy_act_loan')
        ->select("MemID")   
        ->whereNotIn('id_dummy_act_loan',$dis)
        // ->where('id_dummy_act_loan',127)
        // ->whereiN('id_dummy_act_loan',[34,123])
                   // ->where('MemID','=',69)
        ->groupby('MemID')
      
        // ->limit(10)
        ->get();





        // return $

        // return $members;
        // return 123;

        $g = new GroupArrayController();

        foreach($members as $mem){
            $transaction_dates = DB::select("SELECT dll.date,dal.id_dummy_act_loan,dll.LoanPayprincipal,dll.LoanPayinterest FROM dummy_loan_ledger as dll
                LEFT JOIN dummy_act_loan as dal on dal.id_dummy_act_loan = dll.id_parent_dummy_loan
                WHERE dll.id_parent_dummy_loan > 0 and dal.LoanService > 0 AND MemID=? and dal.id_dummy_act_loan not in (".implode(',',$dis).")  ORDER BY dll.date ASC;",[$mem->MemID]);
                // and dal.id_dummy_act_loan=127
 // $transaction_dates = DB::select("SELECT dll.date,dal.id_dummy_act_loan,dll.LoanPayprincipal,dll.LoanPayinterest FROM dummy_loan_ledger as dll
 //                LEFT JOIN dummy_act_loan as dal on dal.id_dummy_act_loan = dll.id_parent_dummy_loan
 //                WHERE dll.id_parent_dummy_loan > 0 and dal.LoanService > 0 AND MemID=? and dal.id_dummy_act_loan  in (34,123)  ORDER BY dll.date ASC;",[$mem->MemID]);



                $transactions = $g->array_group_by($transaction_dates,['date']);


                // return $transactions;

                foreach($transactions as $dates=>$transaction){
                    foreach($transaction as $tr){
                        $id_loan = DB::table('loan')->where('id_dum',$tr->id_dummy_act_loan)->first()->id_loan;

                        $mat_count = DB::table('loan_table')
                                     ->where('id_loan',$id_loan)
                                     ->where('due_date','>=',date("Y-m-t", strtotime($dates)))
                                     ->count();

                        $ord = ($mat_count==0)?"ASC":"DESC";



                        $paid_principal = $tr->LoanPayprincipal;
                        $paid_interest = $tr->LoanPayinterest;
                        $total_paid= 0;

                        $insert_rp_loan = array();

                        $loan_table = DB::table('loan_table')
                        ->select(DB::raw("*,(repayment_amount-getLoanTotalTermPaymentType(id_loan,term_code,1)) as principal_balance,(interest_amount-getLoanTotalTermPaymentType(id_loan,term_code,2)) as interest_balance"))
                        ->where('id_loan',$id_loan)
                        ->where('due_date','<=',date("Y-m-t", strtotime($dates)))
                        ->orDerby('due_date',$ord)
                        ->get();                       

                           

                        // $loan_table = DB::table('loan_table')
                        // ->select(DB::raw("*,(repayment_amount-getLoanTotalTermPaymentType(id_loan,term_code,1)) as principal_balance,(interest_amount-getLoanTotalTermPaymentType(id_loan,term_code,2)) as interest_balance"))
                        // ->where('id_loan',$id_loan)
                        // ->where('due_date','<=',date("Y-m-t", strtotime($dates)))
                        // ->orDerby('due_date','DESC')
                        // ->get();

                        $due_date = $loan_table[0]->due_date ?? '';
                         //DISTRIBUTE PAYMENT <= DUE DATE OF PAYMENT

                        foreach($loan_table as $lt){
                            $temp = array();

                            $p_principal = $this->calculate($paid_principal,$lt->principal_balance);
                            $p_interest = $this->calculate($paid_interest,$lt->interest_balance);


                            $temp['paid_principal'] = $p_principal;
                            $temp['paid_interest'] = $p_interest;
                            $temp['paid_fees'] = 0;

                            $paid_principal = ROUND($paid_principal-$temp['paid_principal'],2);
                            $paid_interest = ROUND($paid_interest-$temp['paid_interest'],2);

                            $temp['term_code'] = $lt->term_code;
                            $temp['id_loan'] = $lt->id_loan;


                            $total_paid += ($temp['paid_principal']+$temp['paid_interest']);   
                            if(($temp['paid_principal']+$temp['paid_interest']) > 0){
                                array_push($insert_rp_loan,$temp);
                            }                        
                        }
                        
                        // IF PAYMENT <= DUE DATE, APPLY THE PAYMENT ON >= DUE DATE
                        if($paid_principal > 0 || $paid_interest > 0){
                            $loan_table = DB::table('loan_table')
                            ->select(DB::raw("*,(repayment_amount-getLoanTotalTermPaymentType(id_loan,term_code,1)) as principal_balance,(interest_amount-getLoanTotalTermPaymentType(id_loan,term_code,2)) as interest_balance"))
                            ->where('id_loan',$id_loan)
                            ->where('due_date','>',date("Y-m-t", strtotime($dates)))
                            ->orDerby('due_date','ASC')
                            ->get();     

                            foreach($loan_table as $lt){
                                $temp = array();

                                $p_principal = $this->calculate($paid_principal,$lt->principal_balance);
                                $p_interest = $this->calculate($paid_interest,$lt->interest_balance);


                                $temp['paid_principal'] = $p_principal;
                                $temp['paid_interest'] = $p_interest;
                                $temp['paid_fees'] = 0;

                                $paid_principal = ROUND($paid_principal-$temp['paid_principal'],2);
                                $paid_interest = ROUND($paid_interest-$temp['paid_interest'],2);

                                $temp['term_code'] = $lt->term_code;
                                $temp['id_loan'] = $lt->id_loan;

                                if($temp['paid_principal']+$temp['paid_interest'] > 0){
                                    $due_date = $lt->due_date;
                                    array_push($insert_rp_loan,$temp);  
                                }

                                $total_paid += ($temp['paid_principal']+$temp['paid_interest']);   
                            }
                        }

                        // if($paid_interest > 0){
                        //     $paid_principal = $paid_principal+$paid_interest;
                        //     $paid_interest = 0;

                        //     $loan_table = DB::table('loan_table')
                        //     ->select(DB::raw("*,(repayment_amount-getLoanTotalTermPaymentType(id_loan,term_code,1)) as principal_balance,(interest_amount-getLoanTotalTermPaymentType(id_loan,term_code,2)) as interest_balance"))
                        //     ->where('id_loan',$id_loan)
                        //     ->where('due_date','<=',date("Y-m-t", strtotime($dates)))
                        //     ->orDerby('due_date','ASC')
                        //     ->get();     

                        //     foreach($loan_table as $lt){
                        //         $temp = array();

                        //         $p_principal = $this->calculate($paid_principal,$lt->principal_balance);
                        //         $p_interest = $this->calculate($paid_interest,$lt->interest_balance);


                        //         $temp['paid_principal'] = $p_principal;
                        //         $temp['paid_interest'] = $p_interest;
                        //         $temp['paid_fees'] = 0;

                        //         $paid_principal = ROUND($paid_principal-$temp['paid_principal'],2);
                        //         $paid_interest = ROUND($paid_interest-$temp['paid_interest'],2);

                        //         $temp['term_code'] = $lt->term_code;
                        //         $temp['id_loan'] = $lt->id_loan;

                        //         if($temp['paid_principal']+$temp['paid_interest'] > 0){
                        //             $due_date = $lt->due_date;
                        //             array_push($insert_rp_loan,$temp);  
                        //         }
                        //         $total_paid += ($temp['paid_principal']+$temp['paid_interest']);   
                        //     }
                        //     // return $insert_rp_loan;                           
                        // }


                    //PUSH REPAYMENT TRANSACTION
                        if($total_paid > 0){
                            date_default_timezone_set('Asia/Manila');
                            $dt = new DateTime();

                            $repayment = array(
                                'transaction_type'=>2,
                                'id_bank' => 2,
                                'repayment_token' => DB::raw("concat('".$dt->format('YmdHisu')."',concat(LEFT(MD5(NOW()), 5)))"),
                                'transaction_date' => $dates,
                                'date' =>$due_date,
                                'id_member'=>$mem->MemID,
                                'total_loan_payment'=>$total_paid,
                                'total_fees'=>0,
                                'total_penalty'=>0,
                                'swiping_amount'=>$total_paid,
                                'change'=>0,
                                'total_payment'=>$total_paid,
                                'change_status'=>1,
                                'email_sent'=>2
                            );


                            DB::table('repayment_transaction')
                            ->insert($repayment);

                            $id_repayment_transaction = DB::table('repayment_transaction')->max('id_repayment_transaction');
                            for($i=0;$i<count($insert_rp_loan);$i++){

                                $insert_rp_loan[$i]['id_repayment_transaction'] = $id_repayment_transaction;
                            }


                            DB::table('repayment_loans')
                            ->insert($insert_rp_loan);

                            DB::table('loan_table')
                            ->where('id_loan',$id_loan)
                            ->update(['is_paid'=>DB::raw("CASE
                                WHEN ifnull(getLoanTotalTermPayment(id_loan,term_code),0) = 0 THEN 0
                                WHEN (total_due-ifnull(getLoanTotalTermPayment(id_loan,term_code),0)) =0 THEN 1
                                ELSE 2 END")]);
                        }

                    }

                }
                }

                return $members;
            }

            public function calculate($amount,$due){
                if($amount >= $due){
                    $p_amount = $due;
                }elseif($amount < $due && $amount > 0){
                    $p_amount =$amount;
                }else{
                    $p_amount = 0;
                } 

                return $p_amount;
            }
        }
