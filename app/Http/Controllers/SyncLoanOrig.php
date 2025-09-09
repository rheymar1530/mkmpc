<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Loan;
use DateTime;
use App\JVModel;
use App\CRVModel;

class SyncLoanOrig extends Controller
{
    public function LoanSyncIndex(Request $request){

        $data['sidebar'] = "sidebar-collapse";
        $data['membership_types'] = DB::table('membership_type')->where('id_membership_type','>',0)->get();

        $data['loan_services'] = DB::table('loan_service')
                                 ->orDerby('name')
                                 ->get();
        $data['selected_type'] = $request->q ?? 1;
        $members = DB::select("SELECT m.id_member,concat(FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)) as member,SUM(CASE WHEN lb.id_loan_beg is not null THEN 1 ELSE 0 END ) as count,ifnull(bl.name,'') as brgy_lgu FROM member as m
        LEFT JOIN loan_beg as lb on lb.id_member = m.id_member
        LEFT JOIN baranggay_lgu as bl on bl.id_baranggay_lgu = m.id_baranggay_lgu
        -- LEFT JOIN membership_type as mt
        -- ->leftJoin('membership_type as mt','m.memb_type','mt.id_membership_type')
        WHERE m.status = 1 AND m.memb_type=?
        GROUP BY m.id_member
        ORDER BY brgy_lgu,member;",[$data['selected_type']]);
        $g = new GroupArrayController();
        $data['members'] = $g->array_group_by($members,['brgy_lgu']);
        // dd($data);
        return view('loan-sync.index',$data);
    }

    public function ParseMemberLoan(Request $request){
        $id_member = $request->id_member;
        $data['member_details'] = DB::table('member as m')
                          ->select(DB::raw("concat(FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)) as member,m.id_member"))
                          ->where('m.id_member',$id_member)
                          ->first();

        $data['loans'] = DB::table('loan_beg as lb')
                         ->select(DB::raw('ifnull(loan.id_loan,0) as id_loan,lb.id_member,lb.id_loan_service,lb.term,lb.principal_amount,lb.interest_rate,lb.paid_principal,lb.paid_interest,lb.date_approved'))
                         ->leftJoin('loan','loan.id_dum','lb.id_loan_beg')
                         ->leftJoin('repayment_loans',function($join){
                            $join->on('repayment_loans.id_loan','loan.id_loan')
                                  ->where('repayment_loans.status','<>',10);
                         })
                         // ->leftJoin('repayment_transaction',function($join){
                         //    $join->on('repayment_transaction.id_repayment_transaction','repayment_loans.id_repayment_loans')
                         //          ->where('repayment_transaction.status','<>',10)
                         //          ->where('repayment_transaction.id_cash_receipt_voucher','>');
                         // })
                         ->where('lb.id_member',$id_member)
                         ->groupBy('lb.id_loan_beg')
                         ->orderBy('lb.id_loan_beg')
                         ->get();
        
        return response($data);
    }

    public function post(Request $request){

        $id_member = $request->id_member ?? 0;

        $loans = $request->loans ?? [];

        $required_field = ['principal_amount','interest_rate','term'];

        $invalid = array();

        if($id_member == 0){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Please Select Member";

            return response($data);
        }
        foreach($loans as $c=>$loan){
            $v = array();
            //validate
            foreach($required_field as $rf){
                if($loan[$rf] == 0){
                    array_push($v,$rf);
                }
            }

            if(count($v) > 0){
                $invalid[$c] = $v;
                // array_push($invalid,$v);
            }
            $loans[$c]['id_member'] = $id_member;
        }

        if(count($invalid) > 0){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Please fill required fields";
            $data['invalid'] = $invalid;

            return response($data);
        }





        DB::beginTransaction();
        try{
            $newLoan = array();
            foreach($loans as $c=>$loan){
                // dd($loan);
                if(($loan['id_loan'] ?? 0) > 0){
                    $id_loan_beg = DB::table('loan')->select("id_dum")->where('id_loan',$loan['id_loan'])->first()->id_dum;
                    // dd($loan);
                    $id_loan = $loan['id_loan'];

                    unset($loans[$c]['id_loan']);
                    $upData = $loan;


                    unset($upData['id_loan']);

                    DB::table('loan_beg')
                    ->where('id_loan_beg',$id_loan_beg)
                    ->update($upData);

                    $this->UpdateLoan($id_loan,$id_loan_beg);
                    DB::table('loan_table')->where('id_loan',$id_loan)->delete();
                    DB::table('loan_charges')->where('id_loan',$id_loan)->delete();

                }else{
                    $upData = $loan;
                    unset($upData['id_loan']);

                    array_push($newLoan,$upData);
                }
            }

            







            $id_loan_begs = DB::table('loan_beg')
                            ->select("id_loan_beg")
                            ->where('id_member',$id_member)
                            ->get()->pluck('id_loan_beg')->toArray();

            // DB::table('loan_beg')
            // ->where('id_member',$id_member)
            // ->delete();

            DB::table('loan_beg')
            ->insert($newLoan);

            /*SYNC TO LOANS*/

            DB::table('repayment_transaction')
            ->where('id_member',$id_member)
            ->where('id_cash_receipt_voucher',0)
            ->where('id_journal_voucher',0)
            ->delete();


            // DB::table('loan')
            // // ->whereIn('id_dum',$id_loan_begs)
            // ->where('id_member',$id_member)
            // ->delete();

            $this->GenerateLoan($id_member);

            $this->generateLoanTable($id_member);

            $this->repayment_autofill2($id_member);




            if((count($request->deleted) ?? []) > 0 ){
                foreach($request->deleted as $DelLoan){
                    $id_loan_beg = DB::table('loan')->select("id_dum")->where('id_loan',$DelLoan)->first()->id_dum;

                    DB::table('loan_beg')->where('id_loan_beg',$id_loan_beg)->delete();
                    DB::table('loan')->where('id_loan',$DelLoan)->delete();

                    //delete repayment 
                }
            }

            DB::commit();

            /********************************/            

        }catch(\Exception $e){
            DB::rollback();
            dd($e->getMessage());
        }






        $data['RESPONSE_CODE'] = "SUCCESS";
        $data['message'] = "Loan Successfully Posted";


        $data['show_check'] = (count($loans) > 0)?true:false;

        return response($data);
    }



    public function GenerateLoan($id_member){
        DB::select("INSERT INTO LOAN (id_member,loan_token,id_loan_service,id_disbursement_type,
        id_interest_method,id_loan_payment_type,id_term_period,
        id_interest_period,id_repayment_period,start_month_period,
        end_month_period,repayment_schedule,id_charges_group,
        interest_rate,terms,period,principal_amount,
        terms_token,loan_protection_rate,loan_remarks,created_by,
        is_deduct_cbu,deduct_interest,loan_status,status,total_deductions,cbu_deficient,total_loan_proceeds,loan_protection_amount,not_deducted_charges,loan_amount,prev_loan_balance,prev_loan_rebates,date_released,id_dum,id_membership_type,id_baranggay_lgu,id_one_time_type)
        SELECT k.* FROM (
        SELECT 
        beg.id_member,concat(DATE_FORMAT(now(),'%m%d%Y%H%i%s'),concat(LEFT(MD5(NOW()), 5)),beg.id_loan_beg) as loan_token,beg.id_loan_service,
        ls.id_disbursement_type,ls.id_interest_method,ls.id_loan_payment_type,ls.id_term_period,ls.id_interest_period,ls.id_repayment_period,
        ls.start_month_period,ls.end_month_period,ls.repayment_schedule,ls.id_charges_group,beg.interest_rate,terms.terms,terms.period,
        beg.principal_amount,terms.terms_token,0 as loan_protection_rate,'' as loan_remarks,0 as created_by,ls.is_deduct_cbu,ls.deduct_interest,1 as loan_status, 3 as status,
        if(ls.deduct_interest=1,(ROUND(beg.principal_amount*(beg.interest_rate/100),2))*terms.terms,0) as total_deductions,0 as cbu_deficient,beg.principal_amount-if(ls.deduct_interest=1,(ROUND(beg.principal_amount*(beg.interest_rate/100),2))*terms.terms,0) as total_loan_proceeds,0 as loan_protection_amount,0 as not_deducted_charges,
        beg.principal_amount+if(ls.deduct_interest=0,((ROUND(beg.principal_amount*(beg.interest_rate/100),2))*terms),0) as loan_amount,
        0 as prev_loan_balance,0 as prev_loan_rebates,date_approved,id_loan_beg,m.memb_type as id_membership_type,m.id_baranggay_lgu,ls.id_one_time_type
        FROM loan_beg beg
        LEFT JOIN loan_service as ls on ls.id_loan_service = beg.id_loan_service
        LEFT JOIN terms on terms.id_loan_service = beg.id_loan_service AND beg.TERM = terms.terms
        LEFT JOIN member as m on m.id_member = beg.id_member
        WHERE beg.id_member= ?
        GROUP BY id_loan_beg) as k
        LEFT JOIN loan on loan.id_dum = k.id_loan_beg
        WHERE loan.id_loan is null;",[$id_member]);
    }
    public function UpdateLoan($id_loan,$id_loan_beg){
        DB::select("UPDATE LOAN
        LEFT JOIN loan_beg beg ON LOAN.id_dum = beg.id_loan_beg
        LEFT JOIN loan_service ls ON ls.id_loan_service = beg.id_loan_service
        LEFT JOIN terms ON terms.id_loan_service = beg.id_loan_service AND beg.TERM = terms.terms
        LEFT JOIN member m ON m.id_member = beg.id_member
        SET 
        LOAN.id_loan_service = beg.id_loan_service,
        LOAN.id_disbursement_type = ls.id_disbursement_type,
        LOAN.id_interest_method = ls.id_interest_method,
        LOAN.id_loan_payment_type = ls.id_loan_payment_type,
        LOAN.id_term_period = ls.id_term_period,
        LOAN.id_interest_period = ls.id_interest_period,
        LOAN.id_repayment_period = ls.id_repayment_period,
        LOAN.start_month_period = ls.start_month_period,
        LOAN.end_month_period = ls.end_month_period,
        LOAN.repayment_schedule = ls.repayment_schedule,
        LOAN.id_charges_group = ls.id_charges_group,
        LOAN.interest_rate = beg.interest_rate,
        LOAN.terms = terms.terms,
        LOAN.period = terms.period,
        LOAN.principal_amount = beg.principal_amount,
        LOAN.terms_token = terms.terms_token,
        LOAN.loan_protection_rate = 0,
        LOAN.loan_remarks = '',
        LOAN.created_by = 0,
        LOAN.is_deduct_cbu = ls.is_deduct_cbu,
        LOAN.deduct_interest = ls.deduct_interest,
        LOAN.loan_status = 1,
        LOAN.status = 3,
        LOAN.total_deductions = IF(ls.deduct_interest=1, ROUND(beg.principal_amount*(beg.interest_rate/100),2) * terms.terms, 0),
        LOAN.cbu_deficient = 0,
        LOAN.total_loan_proceeds = beg.principal_amount - IF(ls.deduct_interest=1, ROUND(beg.principal_amount*(beg.interest_rate/100),2) * terms.terms, 0),
        LOAN.loan_protection_amount = 0,
        LOAN.not_deducted_charges = 0,
        LOAN.loan_amount = beg.principal_amount + IF(ls.deduct_interest=0, (ROUND(beg.principal_amount*(beg.interest_rate/100),2) * terms.terms), 0),
        LOAN.prev_loan_balance = 0,
        LOAN.prev_loan_rebates = 0,
        LOAN.date_released = beg.date_approved,
        LOAN.id_dum = beg.id_loan_beg,
        LOAN.id_membership_type = m.memb_type,
        LOAN.id_baranggay_lgu = m.id_baranggay_lgu,
        LOAN.id_one_time_type = ls.id_one_time_type
        WHERE LOAN.id_dum is not null AND LOAN.id_dum = ?;",[$id_loan_beg]);
    }


    public function generateLoanTable($id_member){ //<----- THIS IS THE FUNCTION
         // $this->update_loan_token();

         // return $this->push_member();
        // return $this->repayment_autofill2(); //<----- FUNCTION TO PUSH REPAYMENT
        // return $this->repayment_autofill3(); //<----- FUNCTION TO PUSH REPAYMENT
        // return $this->generate_repayment2();

        // $loans = DB::table('loan')
        // ->Select(DB::raw("*,concat(YEAR(curdate())+if(MONTH(curdate()) > ls.end_month_period,0,0),'-',ls.end_month_period,'-',ls.repayment_schedule) as form_due_date"))
        // ->leftJoin('loan_service as ls','ls.id_loan_service','loan.id_loan_service')
        // ->whereNotNull('id_dum')
        // ->whereIn('id_loan',$loanss_id)
        // ->get();

        $loans = DB::table('loan')
        ->Select(DB::raw("*,concat(YEAR(date_released),'-',ls.end_month_period,'-',ls.repayment_schedule) as form_due_date"))
        ->leftJoin('loan_service as ls','ls.id_loan_service','loan.id_loan_service')
        ->whereNotNull('id_dum')
        ->where('loan.id_member',$id_member)
        // ->where('loan.id_one_time_type',2)
        ->get();


      


        foreach($loans as $l){
            if($l->id_one_time_type == 2){
                $duration = Loan::OneTimeOpen(['id_loan_service'=>$l->id_loan_service,'dt'=>$l->date_released]);
                $int = $duration['duration']*$l->interest_rate;
                $updateOBJ = [
                    'interest_rate'=> $int,
                    'total_deductions'=>ROUND($l->principal_amount* ($int/100),2),
                    'total_loan_proceeds'=>$l->principal_amount-ROUND($l->principal_amount* ($int/100),2),
                    'month_duration'=>$duration['duration'],
                    'interest_show'=>$l->interest_rate
                ];

                DB::table('loan')
                ->where('id_loan',$l->id_loan)
                ->update($updateOBJ);

                // DB::table('loan_charges')
                // ->where('id_loan',$id->loan)
                // ->where('id_loan_fees',12)
                // ->update([
                //     'value'=>ROUND($l->principal_amount* ($int/100),2),
                //     'calculated_charge'=>ROUND($l->principal_amount* ($int/100),2)
                // ]);
            }

            // dd("123");


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

            if($l->id_one_time_type == 2){
                DB::table('loan_charges')
                ->where('id_loan',$l->id_loan)
                ->where('id_loan_fees',12)
                ->update([
                    'value'=>ROUND($l->principal_amount* ($int/100),2),
                    'calculated_charge'=>ROUND($l->principal_amount* ($int/100),2)
                ]);
            }


            DB::table('loan_charges')
            ->where('id_loan',$l->id_loan)
            ->where('id_loan_fees',3)
            ->delete();


        }

        // DB::table('loan_charges')->wherein('id_loan',$disc)->delete();

        return $loans;
    }

    public function generateRepayment(){
        $this->generateLoanTable();
        $id_members = DB::table('loan')->select('id_member')->groupby('id_member')->whereIn('id_member',[2])->get()->pluck('id_member')->toArray();

        foreach($id_members as $id_member){
            $loans = DB::table('loan_beg')
            ->select(DB::raw("loan.*,loan_beg.paid_interest+loan_beg.paid_principal as total_payment"))
            ->leftJoin('loan','loan.id_dum','loan_beg.id_loan_beg')
            ->where('loan_beg.id_member',$id_member)
            ->where('loan.id_loan',1)
            ->get();
      
            foreach($loans as $loan){

                $temp_rp = $this->GenerateRepaymentLoans($loan->id_loan,$loan->total_payment);
                dd($temp_rp);

            }
        }
    }

    public function GenerateRepaymentLoans($id_loan,$payment){

        if($payment > 0){
            $loan_table = DB::table('loan_table')->where('id_loan',$id_loan)->get();
            // dd($loan_table);
            $total_payment = $payment;

            $applied_payment = array();
            foreach($loan_table as $lt){

                if($total_payment > 0){
                    $t = array();
                    $paid_interest = $this->calculate($total_payment,$lt->interest_amount);
                    $total_payment -=$paid_interest;
                    $paid_principal = $this->calculate($total_payment,$lt->repayment_amount);
                    $total_payment -= $paid_principal;
                }

            }

            dd($loan_table);
        }
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
    public function repayment_autofill2($id_member){
        ini_set('memory_limit', '-1');
        set_time_limit(-1);







        // $dis = $this->loan_with_discrepancy();

        // $members = DB::table('dummy_act_loan')
        // ->select("MemID")   
        // ->whereNotIn('id_dummy_act_loan',$dis)
        // // ->where('id_dummy_act_loan',127)
        // // ->whereiN('id_dummy_act_loan',[34,123])
        //            // ->where('MemID','=',69)
        // ->groupby('MemID')
      
        // // ->limit(10)
        // ->get();

        $members = DB::table('loan_beg')
                   ->select(DB::raw('id_member as MemID'))
                   ->where('id_member',$id_member)
                   ->groupBy('id_member')
                   ->get();


        // dd($members);




        // return $

        // return $members;
        // return 123;

        $g = new GroupArrayController();

        foreach($members as $mem){
            // $transaction_dates = DB::select("SELECT dll.date,dal.id_dummy_act_loan,dll.LoanPayprincipal,dll.LoanPayinterest FROM dummy_loan_ledger as dll
            //     LEFT JOIN dummy_act_loan as dal on dal.id_dummy_act_loan = dll.id_parent_dummy_loan
            //     WHERE dll.id_parent_dummy_loan > 0 and dal.LoanService > 0 AND MemID=? and dal.id_dummy_act_loan not in (".implode(',',$dis).")  ORDER BY dll.date ASC;",[$mem->MemID]);

            // $transaction_dates = DB::select("SELECT '2024-09-29' as date,id_loan_beg as id_dummy_act_loan,paid_principal as LoanPayprincipal,
            // paid_interest as LoanPayinterest
            // FROM loan_beg where id_loan_beg = 298 AND id_member=?",[$mem->MemID]);

            $transaction_dates = DB::select("SELECT '2024-09-29' as date,loan_beg.id_loan_beg as id_dummy_act_loan,loan_beg.paid_principal as LoanPayprincipal,
            loan_beg.paid_interest as LoanPayinterest
            FROM loan_beg 
            LEFT JOIN loan as l on l.id_dum = loan_beg.id_loan_beg
            where l.id_dum is not null AND loan_beg.id_member=?",[$mem->MemID]);

            // dd($transaction_dates);

            // dd($transaction_dates);

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
