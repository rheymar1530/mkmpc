<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Loan;
use App\WebHelper;
use App\MySession;
use Carbon;

class LoanAccruedController extends Controller
{
    public function getAccruedLoans(){
        $dateAsOf = WebHelper::ConvertDatePeriod(MySession::current_date());

        $accruedLoan = DB::select("SELECT id_loan,date_released,maturity_date,principal_amount,if(id_loan_payment_type=1,interest_rate,interest_show) as interest_rate ,ROUND(principal_amount*(if(id_loan_payment_type=1,interest_rate,interest_show)/100),2) as interest_amt 
        FROM loan
        WHERE loan_status = 1 AND maturity_date < ?  AND loan.id_loan_payment_type = 1  ;",[$dateAsOf]);

        dd($accruedLoan);
        foreach($accruedLoan as $ac){
            $this->LoanAccrued($ac->id_loan,$ac->maturity_date,$ac->interest_amt);
        }  

        dd("SUCCESS");
    }
    
    public function LoanAccrued($id_loan,$maturity_date,$interest_amt){

        $dateAsOf = WebHelper::ConvertDatePeriod(MySession::current_date());

        $LoanTable = DB::table('loan_table')->where('id_loan',$id_loan)->get();
        $cLoanTable = count($LoanTable);
        $startDateAc = $LoanTable[$cLoanTable-1]->due_date;


        $MonthsAccrued = Loan::generateMonthlyDates(date("Y-m-01", strtotime("$startDateAc")),date("Y-m-01", strtotime("$dateAsOf")));
        // dd($MonthsAccrued);

        unset($MonthsAccrued[0]);
        $MonthsAccrued = array_values($MonthsAccrued);
        $MonthOBJ = array();

        foreach($MonthsAccrued as $c=>$month){
            $count = $cLoanTable+($c+1);
            $MonthOBJ[]=[
                'id_loan'=>$id_loan,
                'due_date'=> date("Y-m-t", strtotime("$month")),
                'count'=>$count,
                'term_code'=>"P{$count}",
                'repayment_amount'=>0,
                'interest_amount'=>$interest_amt,
                'fees'=>0,
                'is_paid'=>0,
                'accrued'=>1,
                'total_due'=>$interest_amt
            ];
        }

        DB::table('loan_table')
        ->insert($MonthOBJ);

        return ;

        dd("SUCCESS");
        dd($MonthOBJ);

        dd($MonthsAccrued);
    }

    public function getOverDueLoans(){
        $dateAsOf = WebHelper::ConvertDatePeriod(MySession::current_date());

        // $accruedLoan = DB::select("SELECT id_loan,getTotalDueTypeRepaymentX(loan.id_loan,'$dateAsOf',0,1) as principal_balance,
        //     getTotalDueTypeRepaymentX(loan.id_loan,'$dateAsOf',0,2) as interest_balance
        // FROM loan
        // WHERE loan_status = 1 AND maturity_date < ?  AND loan.id_loan_payment_type = 1  ;",[$dateAsOf]);

        $accruedLoan = DB::select("SELECT loan.id_loan,MIN(lt.due_date) as date_start,loan.maturity_date as date_end
        FROM loan
        LEFT JOIN loan_table as lt on lt.id_loan = loan.id_loan
        WHERE loan_status = 1 AND maturity_date < ?  AND  lt.accrued = 0  GROUP BY loan.id_loan;",[$dateAsOf]);

    
        foreach($accruedLoan as $al){
            $SurchargeStart =   Carbon\Carbon::parse($al->date_end);     
            $SurchargeStart = $SurchargeStart->addMonth(2)->format('Y-m-d');

            $MonthsAccrued = Loan::generateMonthlyDates(date("Y-m-01", strtotime("$SurchargeStart")),date("Y-m-01", strtotime("$dateAsOf")));

            $debug = array();

            $loanTable = DB::table('loan_table')
                         ->select(DB::raw("MAX(due_date) as last_date,COUNT(*) as c"))
                         ->where('id_loan',$al->id_loan)
                         ->first();

            $count = $loanTable->c;
            //check if there is a surcharge
            if(count($MonthsAccrued) > 0){
                $MonthOBJ = array();
                foreach($MonthsAccrued as $mo){
                  


                    $month_due = date("Y-m-t", strtotime("$mo"));

                    if($month_due > $loanTable->last_date){
                        $count++;
                        $monthCheck = Carbon\Carbon::parse($mo)->addMonth(-1)->format('Y-m-d');
                        $monthCheck= date("Y-m-t", strtotime("$monthCheck"));

                        $loanBalance = $this->getLoanBalanceAsOfMonth($al->id_loan,$monthCheck);

                        $balance = $loanBalance->balance;


                        $surcharge = round($balance*0.02,2);

                        $MonthOBJ[]=[
                            'id_loan'=>$al->id_loan,
                            'due_date'=> date("Y-m-t", strtotime("$month_due")),
                            'count'=>$count,
                            'term_code'=>"P{$count}",
                            'repayment_amount'=>0,
                            'interest_amount'=>0,
                            'fees'=>0,
                            'is_paid'=>0,
                            'accrued'=>1,
                            'surcharge'=>$surcharge,
                            'total_due'=>$surcharge,
                            'interest_r'=>$loanBalance->interest_balance,
                            'principal_r'=>$loanBalance->principal_balance
                        ];
                    }
                }

                DB::table('loan_table')
                ->insert($MonthOBJ);
            }
        }

        dd("--SUCCESS--");

        dd($accruedLoan);

        dd($dateAsOf);
    }

    public function getLoanBalanceAsOfMonth($id_loan,$date){
        //Interest and Principal Only
        $p = [
            'id_loan1'=>$id_loan,
            'id_loan2'=>$id_loan,
            'date'=>$date
        ];
    
        $out = DB::select("WITH loan_total as (
        SELECT SUM(repayment_amount) as principal,
        SUM(interest_amount) as interest,
        SUM(fees) as fees,
        id_loan
        FROM loan_table
        WHERE  id_loan =:id_loan1),
        payments as (
        SELECT rl.id_loan,
        SUM(ifnull(paid_principal,0)) as paid_principal,
        SUM(ifnull(paid_interest,0)) as paid_interest,
        SUM(ifnull(paid_fees,0)) as paid_fees
        FROM repayment_loans as rl
        LEFT JOIN loan_table as lt on lt.id_loan = rl.id_loan AND lt.term_code = rl.term_code
        LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rl.id_repayment_transaction
        WHERE rl.id_loan = :id_loan2 AND rt.status <> 10 AND rt.transaction_date <= :date
        )
        SELECT 
        principal-ifnull(paid_principal,0) as principal_balance,
        interest-ifnull(paid_interest,0) as interest_balance,
        fees-ifnull(paid_fees,0) as fees_balance,
        (principal+interest+fees)-(ifnull(paid_principal,0)+ifnull(paid_interest,0)+ifnull(paid_fees,0)) as balance
        FROM loan_total
        LEFT JOIN payments as p on p.id_loan = loan_total.id_loan",$p)[0];

        return $out;
    }

    public function SurchargeMaintenance(Request $request){

        $id_member = $request->id_member;

        $data = array();
        

        if(isset($request->id_member)){
            $data['selected_member'] =DB::table('member as m')
            ->select(DB::raw("concat(membership_id,' || ',FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)) as member_name,id_member"))
            ->where('id_member',$request->id_member)
            ->first();

            $data['loans'] = DB::select("SELECT loan.id_loan,loan.loan_token,
            getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms) as loan_name,
            SUM(lt.surcharge) as surcharge
            FROM loan
            LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
            LEFT JOIN loan_table as lt on lt.id_loan = loan.id_loan
            WHERE loan.id_member = ? AND loan.loan_status = 1
            GROUP BY loan.id_loan;",[$request->id_member]);

            // dd($data);

        }

        return view('surcharge_maintenance.index',$data);
    }
    public function parseSurcharge(Request $request){
        $loan_token = $request->loan_token;

        $data['details'] = DB::table('loan')
                           ->select(DB::raw("loan.id_loan,DATE_FORMAT(min(due_date),'%m/%d/%Y') as date_from,DATE_FORMAT(max(due_date),'%m/%d/%Y') as date_to,loan.loan_token"))
                           ->leftJoin('loan_table as lt','lt.id_loan','loan.id_loan')
                           ->where('loan_token',$loan_token)
                           ->where('accrued',0)
                           ->first();
        $data['surcharges'] = DB::select("SELECT DATE_FORMAT(lt.due_date,'%m/%d/%Y') as date,surcharge,getLoanTotalTermPaymentType(loan.id_loan,lt.term_code,4) as payment,lt.id_loan_table
        FROM loan 
        LEFT JOIN loan_table as lt on lt.id_loan = loan.id_loan
        WHERE loan_token = ? AND lt.surcharge > 0
        ORDER BY due_date ASC;",[$loan_token]);

        return $data;
        return response($data);
    }

    public function postSurchargeTable(Request $request){
        $g = new GroupArrayController();

        $loan_table = $request->surcharges ?? [];

        if(count($loan_table) == 0){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Invalid Request";

            return response($data);
        }


        $loanTableForm = $g->array_group_by($loan_table,['id_loan_table']);


        $r = new Request(['loan_token'=>$request->loanToken]);


        // dd($request->all());

        $ValidationData = $this->parseSurcharge($r);

        $ValidationData = $g->array_group_by($ValidationData['surcharges'],['id_loan_table']);

        $updateOBJ = array();
        $invalidSurcharge = array();
        foreach($ValidationData as $id=>$val){
            $val  = $val[0];
            $balance = $val->surcharge - $val->payment;
            if($balance == 0){
                continue;
            }else{
                $surcharge = $loanTableForm[$id][0]['amount'];
                if(ROUND($surcharge,2) - ROUND($val->payment,2) < 0){
                    array_push($invalidSurcharge,$id);
                }else{
                    $updateOBJ[]=[
                        'id_loan_table'=>$id,
                        'surcharge'=>$surcharge
                    ];
                }
            }
        }

        if(count($invalidSurcharge) > 0){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Invalid Amount";
            $data['id_loan_table'] = $invalidSurcharge;

            return response($data);
        }
        foreach($updateOBJ as $up){
            DB::table('loan_table')
            ->where('id_loan_table',$up['id_loan_table'])
            ->update(['surcharge'=>$up['surcharge'],
                      'total_due'=>$up['surcharge']]);
        }

        $data['RESPONSE_CODE'] = "SUCCESS";
        $data['message'] = "Surcharge successfully saved";
        return response($data);
        dd($updateOBJ);
        dd($ValidationData);
        dd($request->all());


    }
}
