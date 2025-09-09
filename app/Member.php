<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\MySession;
use App\WebHelper;
class Member extends Model
{
    public static function getCBU($id_member){
        // $cbu = DB::table('cash_receipt_details as cd')
        // ->select(DB::raw("ifnull(SUM(amount),0) as cbu_amount"))
        // ->leftJoin("cash_receipt as c","c.id_cash_receipt","cd.id_cash_receipt")
        // ->where('id_payment_type',3)
        // ->where('id_member',$id_member)
        // ->where('status','<>',10)
        // ->first()->cbu_amount;


        $cbu = DB::select("
                            SELECT SUM(amount) as amount FROM (
                            SELECT ifnull(amount,0) as amount
                            FROM cash_receipt_details as cd
                            LEFT JOIN cash_receipt as c on c.id_cash_receipt = cd.id_cash_receipt
                            LEFT JOIN tbl_payment_type as pt on pt.id_payment_type = cd.id_payment_type
                            WHERE pt.is_cbu = 1 AND pt.type in (1) AND status <> 10 AND id_member = $id_member
                            UNION ALL
                            SELECT ifnull(lc.calculated_charge,0) as amount from loan_charges as lc
                            LEFT JOIN loan on loan.id_loan = lc.id_loan
                            LEFT JOIN tbl_payment_type as pt on pt.reference = lc.id_loan_fees and pt.type = 2
                            WHERE id_member = $id_member and pt.is_cbu = 1 AND (loan.status in (6) or loan.loan_status in (1)) and pt.type =2
                            UNION ALL
                            SELECT ifnull(rf.amount,0) FROM repayment_fees as rf
                            LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rf.id_repayment_transaction
                            LEFT JOIN tbl_payment_type as pt on pt.id_payment_type = rf.id_payment_type
                            WHERE id_member = $id_member AND pt.is_cbu = 1 and pt.type =3 AND rt.status <> 10
                            UNION ALL
                            SELECT ifnull(amount,0) FROM cbu_beginning
                            WHERE id_member = $id_member
                            UNION ALL
                            SELECT (cdd.debit*-1) as amount FROM cash_disbursement as cd
                            LEFT JOIN cash_disbursement_details as cdd on cdd.id_cash_disbursement = cd.id_cash_disbursement
                            LEFT JOIN chart_account as ca on ca.id_chart_account = cdd.id_chart_account
                            WHERE ca.iscbu = 1 and cd.status <> 10 and cd.id_member = $id_member
                            UNION ALL
                            SELECT (jvd.credit-jvd.debit) as amount FROM journal_voucher as jv
                            LEFT JOIN journal_voucher_details as jvd on jvd.id_journal_voucher = jv.id_journal_voucher
                            LEFT JOIN chart_account as ca on ca.id_chart_account = jvd.id_chart_account
                            WHERE ca.iscbu = 1 AND jv.status <> 10 AND jv.type = 1 AND jv.id_member = $id_member

                        ) as CBU;")[0]->amount ?? 0;
        return $cbu;
    }
    public static function CheckCBU($required_amount,$id_member){
        $total_capital_buildup = self::getCBU($id_member);
        $output['is_capital_buildup_valid'] = true;
        $output['difference'] = 0;
        $difference = $required_amount - $total_capital_buildup;
        if($difference > 0){
            $output['is_capital_buildup_valid'] = false;
            $output['difference'] = $difference;
        }

        return $output;   
    }
    public static function CheckAgeCondition($id_loan_service,$id_member){
        $age_condition = DB::table('loan_service')->select("avail_age")->where('id_loan_service',$id_loan_service)->first()->avail_age;
        $applicant_age = self::getAge($id_member);
        
        $output['isValid'] = true;
        if($applicant_age > $age_condition && $age_condition > 0){
            $output['isValid'] = false;
            $output['ERROR_MESSAGE'] = "Required Age Limit : $age_condition, Applicant Age: $applicant_age";
        }

        return $output;
    }

    public static function CheckRepaymentCondition($id_loan_service,$id_member){
        $minimum_repayment = DB::table('loan_service')->select('renew_payments')->where('id_loan_service',$id_loan_service)->first()->renew_payments;
        $current_loan_repayment_count = self::LoanRepaymentCount($id_loan_service,$id_member);
        $output['isValid'] = true;

        if($current_loan_repayment_count == 0){
            return $output;
        }
        if($current_loan_repayment_count < $minimum_repayment){
            $output['isValid'] = false;
            $output['ERROR_MESSAGE'] = "Required Loan Payment Count : $minimum_repayment, Current Loan Payment Count: $current_loan_repayment_count";             
        }

        return $output;
    }
    public static function CheckOutstandingOverdue($id_member){
        $output['isValid'] = true;
        $member_outstanding_overdue = self::getOutstandingOverdue($id_member);

        if($member_outstanding_overdue > 0){
            $output['isValid'] = false;
            $output['ERROR_MESSAGE'] = "You are not qualified to renew this loan because you have an outstanding overdue account. Outstanding Overdue : ₱".number_format($member_outstanding_overdue,2);   
        }
        return $output;
    }
    public static function CheckFirstLoan($id_member,$id_loan_service){
        $count = DB::table('loan')
                 ->where('id_member',$id_member)
                 ->where('id_loan_service',$id_loan_service)
                 ->whereNotIn('status',[0,1,2,5])
                 ->count();

                 // return $id_loan_service;
                 // return $count;
        return ($count > 0)?false:true;
    }
    public static function CheckPendingLoanApplication($id_member,$id_loan_service){
        $loan = DB::table('loan')
                 ->select("loan_token","id_loan")
                 ->where('id_member',$id_member)
                 ->where('id_loan_service',$id_loan_service)
                 ->whereIn('status',[0,1,2])
                 ->first();
        $output = array();
        if(isset($loan)){
            $output['has_pending_application'] = true;
            $output['loan_token'] = $loan->loan_token;
            $output['id_loan'] = $loan->id_loan;
        }else{
            $output['has_pending_application'] = false;
        }

        return $output;

                 // return $id_loan_service;
                 // return $count;
        return ($count > 0)?true:false;
    }
    public static function getAge($id_member){
        $member_age = DB::table('member')
                     ->select(DB::raw("TRUNCATE(DATEDIFF(curdate(),date_of_birth)/365,0) as age"))
                     ->where('id_member',$id_member)
                     ->first()->age;
        return $member_age;
    }
    public static function LoanRepaymentCount($id_loan_service,$id_member){
        $latest_loan = DB::table('loan')->select("id_loan","loan_protection_rate","principal_amount","terms")->where('id_loan_service',$id_loan_service)->where('id_member',$id_member)->where('loan_status',1)->first();  
       if(!isset($latest_loan)){ // if no active loan
           
            return 0;
        }   
        $count = DB::table('loan_table')
                ->where('id_loan',$latest_loan->id_loan)
                ->where('is_paid',1)
                ->count();
        return $count;
    }
    public static function getOutstandingOverdue($id_member){
        return 0;
    }
    public static function CheckPreviousLoan($id_loan_service,$id_member,$terms,$date=null){
        $latest_loan = DB::table('loan')->select("id_loan","loan_protection_rate","principal_amount","terms","maturity_date")->where('id_loan_service',$id_loan_service)->where('id_member',$id_member)->where('terms_token',$terms)->where('loan_status',1)->first(); 



        // return $terms;
        $date = $date ?? MySession::current_date();

        $date_fil = WebHelper::ConvertDatePeriod($date);

        $date_fil2 = env('REPAYMENT_INTEREST_FULL_CONTRACT')?"if('$date' > loan.maturity_date,'$date',loan.maturity_date)":"'$date'";
        // $date_fil2 = (env('RENEWAL_INTEREST_FULL_CONTRACT')&&isset($latest_loan))?$latest_loan->maturity_date:WebHelper::ConvertDatePeriod2($date);
        
        $ls = DB::table('loan_service')->select('is_multiple','id_loan_payment_type')->where('id_loan_service',$id_loan_service)->first();

        $allowed_multiple = $ls->is_multiple;

        if(!isset($latest_loan) || $allowed_multiple){ // if no active loan
            $output['LOAN_BALANCE'] =0;
            $output['REBATES'] = 0;
            $output['LATEST_LOAN_ID']= 0;

            return $output;
        }


        $output['LATEST_LOAN_ID'] = $latest_loan->id_loan;
        // $output['LOAN_BALANCE'] = DB::select("SELECT ifnull(SUM(loan_amount-getLoanTotalPayment(loan.id_loan)),0) as balance
        //                                       FROM loan
        //                                       WHERE id_loan_service = ? and id_member =? and loan_status = 1 and terms_token=?;",[$id_loan_service,$id_member,$terms])[0]->balance;
        $loanBalance = DB::select("SELECT ifnull(SUM(principal_amount-getLoanTotalPaymentType(loan.id_loan,1)),0)+getInterestBalanceAsOf(loan.id_loan,$date_fil2) as balance,getSurchargeBalanceAsOf(loan.id_loan,$date_fil2) as surcharge
                                              FROM loan
                                              WHERE id_loan_service = ? and id_member =? and loan_status = 1 and terms_token=?;",[$id_loan_service,$id_member,$terms]);


        $output['LOAN_BALANCE'] = $loanBalance[0]->balance;
        $output['SURCHARGE'] = $loanBalance[0]->surcharge;

        $terms = ($ls->id_loan_payment_type==1)?$latest_loan->terms:1;
        $loan_protection_rate = $latest_loan->loan_protection_rate;
        $principal_amount = $latest_loan->principal_amount;

        $lp_month = (($principal_amount)*($loan_protection_rate/100))/$terms;

        $remaining_repayment_count = DB::table('loan_table as lt')
                                     ->where('id_loan',$latest_loan->id_loan)
                                     ->where('due_date','<=',$date_fil)
                                     // ->where('is_paid','<>',1)
                                     ->count();

        $remaining_repayment_count = $terms-$remaining_repayment_count;

        // $output['REBATES'] = ROUND(($lp_month*$remaining_repayment_count),2);   
        $output['REBATES'] = 0;       
                  
        return $output; 
    }
}
