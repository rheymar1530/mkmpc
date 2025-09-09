<?php

namespace App;
use DB;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\GroupArrayController;
use App\Http\Controllers\RepaymentController;
use App\Member;
use App\WebHelper;
use Carbon;

class Loan extends Model
{
    public static function OneTimeOpen($param){


        $id_loan_service = $param['id_loan_service'];
        $dt = $param['dt'] ?? null;
        $due_year = $param['due_year'] ?? MySession::current_year();
  
        // id_loan_service,$dt=null

        $ser = DB::table('loan_service')->where('id_loan_service',$id_loan_service)->first();

        if($ser->id_loan_payment_type == 2 && $ser->id_one_time_type == 2){

            $current_date = (is_null($dt))?MySession::current_date():$dt;


            // $current_date = MySession::current_date();
            $current_day = date('j',strtotime($current_date));
            $month_applied =date('n',strtotime($current_date));
            $year_applied = date('Y',strtotime($current_date));
          
            $end_month = $ser->end_month_period;

            $currentYear = MySession::current_year();
            
            if(env('ONE_TIME_GRACE_PERIOD') == 0){
                $month_count_start = intval($month_applied);
            }else{
                $month_count_start =intval((intval($current_day) >= env('ONE_TIME_GRACE_PERIOD'))?($month_applied+1):$month_applied);
            }

            if(!is_null($dt)){
                $start_year =date('Y',strtotime($dt));
            }else{
                $start_year = MySession::current_year();
            }


            $startObj = [
                'month'=>($month_count_start >= 10)?$month_count_start:"0{$month_count_start}",
                'year'=>$start_year
            ];



          

            $endObj = [
                'end_month'=>($end_month >= 10)?$end_month:"0$end_month"
            ];

            $year_add = ($month_count_start <= $end_month)?0:1;


         
            if(isset($param['due_year'])){
                // dd($param['due_year']);
                if($param['due_year'] <= $year_applied){
                    $year_end = $year_applied+$year_add;
                }else{
                    $year_end = $param['due_year'];
                }
               
            }else{
                $year_end = $year_applied+$year_add;
            }

            $endObj['end_year'] = $year_end;



    

            $startTemp = "{$startObj['year']}-{$startObj['month']}-01";

            // $endTemp = "{$currentYear}-{$endObj['end_month']}-01";

            // if($startTemp > $endTemp){
            //     $minYear = $currentYear+1;
            // }else{
            //     $minYear = $currentYear;
            // }
            // // dd($minYear,$due_year);

            // $maturityYear = ($due_year >= $minYear)?$due_year:$minYear;
            $maturity_date = date('Y-m-d',strtotime("{$year_end}-{$end_month}-{$ser->repayment_schedule}"));

            // dd($maturity_date);

            $arrayMonths = self::generateMonthlyDates($startTemp,$maturity_date);

            // dd($arrayMonths);
            $duration = count($arrayMonths);

            $out['maturity_date'] = $maturity_date;





            // dd($minYear);
            // // if($startObj['month'])

            // dd($startObj,$endObj);

            // dd($endObj);
            // dd($month_count_start,$end_month);

            // dd($month_count_start);
            // if($month_count_start < $end_month){
            //     // dd($month_count_start,$end_month);
            //     $duration = ($end_month-$month_count_start)+1;
            // }
            // elseif($month_count_start == $end_month){
            //     $duration = 1;
            // }else{
            //     $duration = (12-$month_count_start)+$end_month+1;
            // }
        }else{
            $duration = 1;
        }


        $out['duration'] = $duration;

        // dd($out);

        // dd($out);
        return $out;
        $interest_multiplier = $duration;
        dd($interest_multiplier);
    }

    public static function generateMonthlyDates($startDate, $endDate){

        $dates = [];
        $currentDate = Carbon\Carbon::parse($startDate);
        $endDate = Carbon\Carbon::parse($endDate);

        while ($currentDate->lte($endDate)) {
            $dates[] = $currentDate->format('Y-m-d');
            $currentDate->addMonth();
        }

        return $dates;
    }

    public static function ComputeLoan($loan_parameter){

        // $loan_parameter = [
        //     'cbu_deficient' => 1000,
        //     'charges' => $charges,
        //     'principal_amount' =>20000,
        //     'interest_rate'=> 5,
        //     'terms' => 6,
        //     'term_period' => 3,
        //     'interest_pediod'=>3,
        //     'interest_method' => 1,
        //     'is_cbu_deduct' => 1
        // ];
        // dd($loan_parameter);
        $payment_type = $loan_parameter['payment_type'];
        $is_cbu_deduct = $loan_parameter['is_cbu_deduct'];
        $cbu_deficient = ($is_cbu_deduct == 1)?$loan_parameter['cbu_deficient']:0;
        $charges = $loan_parameter['charges'];
        $principal_amount =$loan_parameter['principal_amount'];
        $interest_rate= $loan_parameter['interest_rate'];
        $terms =  ($payment_type == 1)?$loan_parameter['terms']:1;
        $term_period = $loan_parameter['term_period'];
        $interest_pediod= $loan_parameter['interest_pediod'];
        $interest_method = $loan_parameter['interest_method'];
        $id_member = $loan_parameter['id_member'];
        $interest_multiplier = $loan_parameter['interest_multiplier'] ?? 1;

        $id_loan_service = $loan_parameter['id_loan_service'] ?? 0;

        $loan_payment = isset($loan_parameter['loan_payment'])?$loan_parameter['loan_payment']['active_loan']:[];


        $deduct_interest = $loan_parameter['deduct_interest'];

        $loan_protection_rate = $loan_parameter['loan_protection_rate'];
        $previous_loan = $loan_parameter['previous_loan'];


        $charges_non_deducted_fixed = $charges['NOT_DEDUCTED_FIXED_TOTAL'] ?? 0;

        // return $charges_non_deducted_fixed;
        $charges_non_deducted_divided = $charges['NOT_DEDUCTED_DIVIDED_TOTAL'] ?? 0;

        $output['OTHER_DEDUCTIONS'] = array();

        $output['LOAN_PROTECTION_AMOUNT'] =  $loan_protection_amount = $principal_amount * ($loan_protection_rate/100);

        $deducted_charges = json_decode(json_encode($loan_parameter['charges']['DEDUCTED']),true);

        $output['offset_cbu'] = false;
        $output['prime_cbu_amt'] = 0;
        $prime_amt = 0;
        $totalManualDeduction =0;

        $other_deduction = $loan_parameter['other_deductions'] ?? [];

        // dd($other_deduction);
        // $other_deduction = array(
        //     ['id_loan_fees'=>15,'amount'=>1000,'remarks'=>'Test Interest'],
        //     ['id_loan_fees'=>16,'amount'=>200,'remarks'=>'Test Loan Payment']
        // );
        // dd();

        // PRIME VALIDATION
        if(env('PRIME_ENABLE')){
            $ls_prime = DB::table('loan_service')->select(DB::raw("with_prime,prime_min_cbu,prime_min_loan,prime_calc,prime_val"))->where('id_loan_service',$id_loan_service)->first();

            if($ls_prime->with_prime == 1){
                $prime_data = self::PrimeData($ls_prime,$id_member,$principal_amount);

                if($prime_data['is_prime_applied']){
                    array_push($output['OTHER_DEDUCTIONS'],$prime_data['OTHER_DEDUCTIONS']);
                    array_push($deducted_charges,$prime_data['DEDUCTED_CHARGES_DISP']);
                    $prime_amt = $prime_data['prime_amount'];
                }
               
                if($prime_data['offset_cbu']){
                    for($x=0;$x<count($deducted_charges);$x++){
                        if($deducted_charges[$x]['id_loan_fees'] == 2){
                            $charges['DEDUCTED_TOTAL'] -=$deducted_charges[$x]['calculated_charge'];
                            unset($deducted_charges[$x]);
                        }
                    }

                    if($prime_data['cbu_amount'] > 0){
                        array_push($output['OTHER_DEDUCTIONS'],$prime_data['CBU_DEDUCTION']);
                        array_push($deducted_charges,$prime_data['DEDUCTED_CHARGES_DISP_CBU']);
                        $charges['DEDUCTED_TOTAL'] += $prime_data['cbu_amount'];

                        $prime_amt += $prime_data['cbu_amount'];
                    }
                }

                $output['offset_cbu'] = $prime_data['offset_cbu'];
            }
        }

        if($loan_protection_rate > 0){
            $c = [
                'charge_complete_details' => "Insurance ($loan_protection_rate%)",
                'calculated_charge' => $loan_protection_amount
            ];
            $loan_protection_deductions = [
                'id_loan_fees' => config('variables.id_loan_protection'),
                'id_fee_calculation' => 2,
                'value' => $loan_protection_amount,
                'is_deduct' => 1,
                'application_fee_type' => 1,
                'calculated_charge'=> $loan_protection_amount,
                'is_loan_charges'=> 0
            ];
            array_push($output['OTHER_DEDUCTIONS'],$loan_protection_deductions);
            array_push($deducted_charges,$c);           
        }

        if($cbu_deficient > 0){
            $c = [
                'charge_complete_details' => 'CBU deficient amount',
                'calculated_charge' => $cbu_deficient
            ];
            $cbu_deductions = [
                'id_loan_fees' => config('variables.id_cbu_deficient'),
                'id_fee_calculation' => 2,
                'value' => $cbu_deficient,
                'is_deduct' => 1,
                'application_fee_type' => 1,
                'calculated_charge'=> $cbu_deficient,
                'is_loan_charges'=> 0
            ];
            array_push($output['OTHER_DEDUCTIONS'],$cbu_deductions);
            array_push($deducted_charges,$c);
        }

        //deducted interest
        $interest_deduct = 0;
        // if($deduct_interest == 1 && $payment_type == 2){
        if($deduct_interest == 1){

            $interest_deduct = $principal_amount*($interest_rate/100)*$terms;
            $c = [
                'charge_complete_details' => 'Interest',
                'calculated_charge' => $interest_deduct
            ];

            $interest_deductions = [
                'id_loan_fees' => config('variables.interest'),
                'id_fee_calculation' => 2,
                'value' => $interest_deduct,
                'is_deduct' => 1,
                'application_fee_type' => 1,
                'calculated_charge'=> $interest_deduct,
                'is_loan_charges'=> 1
            ];

            array_push($output['OTHER_DEDUCTIONS'],$interest_deductions);
            array_push($deducted_charges,$c);            // return "POTA";
        }

        $notarial_amount = 0;

        // if($principal_amount >= 50000){
        //     $notarial_amount = 100;
        //     $c = [
        //         'charge_complete_details' => 'Notarial Fee',
        //         'calculated_charge' => $notarial_amount
        //     ];
        //     $notarial_deduction = [
        //         'id_loan_fees' => config('variables.id_notarial_fee'),
        //         'id_fee_calculation' => 2,
        //         'value' => $notarial_amount,
        //         'is_deduct' => 1,
        //         'application_fee_type' => 1,
        //         'calculated_charge'=> $notarial_amount,
        //         'is_loan_charges'=> 1
        //     ];
        //     array_push($output['OTHER_DEDUCTIONS'],$notarial_deduction);
        //     array_push($deducted_charges,$c);           
        // }else{
        //     $notarial_amount = 0;
        // }

        
        // return $output['OTHER_DEDUCTIONS'];
        $otherDeductionObj = array();
        $id_loan_fees_ = collect($other_deduction)->pluck('id_loan_fees')->toArray();
        $g = new GroupArrayController();

        $paymentTypeDes = DB::table('loan_fees')->select('id_loan_fees','name')->whereIn('id_loan_fees',$id_loan_fees_)->get();

        // dd($paymentTypeDes);
        $paymentTypeDes = $g->array_group_by($paymentTypeDes,['id_loan_fees']);

        for($i=0;$i<count($other_deduction);$i++){
            $c = [
                'charge_complete_details' => $paymentTypeDes[$other_deduction[$i]['id_loan_fees']][0]->name." (".$other_deduction[$i]['remarks'].")",
                'calculated_charge' => $other_deduction[$i]['amount']
            ];
            $otherDeductionObj[]=[
                'id_loan_fees' => $other_deduction[$i]['id_loan_fees'],
                'id_fee_calculation' => 2,
                'value' => $other_deduction[$i]['amount'],
                'is_deduct' => 1,
                'application_fee_type' => 1,
                'calculated_charge'=> $other_deduction[$i]['amount'],
                'is_loan_charges'=> 0,
                'manual'=>1,
                'remarks'=>$other_deduction[$i]['remarks']
            ];
            $totalManualDeduction+=$other_deduction[$i]['amount'];
            array_push($deducted_charges,$c);
        }

        $output['OTHER_DEDUCTIONS'] = array_merge($output['OTHER_DEDUCTIONS'],$otherDeductionObj);        

        
        $output['DEDUCTED_CHARGES'] = $deducted_charges;




        $output['LOAN_BALANCE'] = $previous_loan['LOAN_BALANCE'] ?? 0;
        $output['SURCHARGE_BALANCE_RENEW'] =$previous_loan['SURCHARGE'] ?? 0;

        $output['REBATES'] = $previous_loan['REBATES'] ?? 0;
        $output['TOTAL_LOAN_BALANCE'] = $output['LOAN_BALANCE']+$output['SURCHARGE_BALANCE_RENEW'] - $output['REBATES'];

        //previous loan
        $output['PREV_LOAN_OFFSET'] = $loan_payment;
        $output['TOTAL_LOAN_OFFSET_REBATES'] = 0;
        $total_prev_loan_offset = 0;
        foreach($loan_payment as $lp){
            $total_prev_loan_offset += $lp->payment;

            if($lp->rebates > 0){
                $output['TOTAL_LOAN_OFFSET_REBATES']+=$lp->rebates;
            }
        }   
        $output['TOTAL_LOAN_OFFSET'] = $total_prev_loan_offset-$output['TOTAL_LOAN_OFFSET_REBATES'];
       // return $output;

        $output['TOTAL_DEDUCTED_CHARGES'] = $charges['DEDUCTED_TOTAL']+$loan_protection_amount+$cbu_deficient+$output['TOTAL_LOAN_BALANCE']+$notarial_amount+$interest_deduct+$output['TOTAL_LOAN_OFFSET']+$prime_amt+$totalManualDeduction;
        $not_deducted_amount = $output['TOTAL_NOT_DEDUCTED_CHARGES'] = ($charges_non_deducted_fixed*$terms)+$charges_non_deducted_divided;


        // +49500
        // echo "<b>Total Deductions: ".$output['TOTAL_CHARGES']."</b>";
        // return ;
        $output['CBU_DEDUCTED'] = ($is_cbu_deduct == 1)?$cbu_deficient:0;
        $output['PRINCIPAL_AMOUNT'] = $principal_amount;

        $output['TOTAL_LOAN_RECEIVABLE'] = $principal_amount - $charges['DEDUCTED_TOTAL'] - (($is_cbu_deduct == 1)?$cbu_deficient:0);

        $repayment_amount = round($principal_amount/$terms,2);
        $cbu_amount = ($is_cbu_deduct == 0)?round($cbu_deficient/$terms,2):0;
        $nd_amount = round($charges_non_deducted_divided/$terms,2);
        $repayment = array();
        $nd = array();
        // $cbu = array();
        $total_cbu = 0;
        $total_rep =0;

        $total_nd=0;

        for($i=1;$i<=$terms;$i++){
            if($i != $terms){
                $repayment["P".$i] = $repayment_amount;
                $total_rep += $repayment_amount;

                $nd["P".$i] =$nd_amount + $charges_non_deducted_fixed;
                $total_nd += $nd_amount;
                // $cbu["P".$i] = $cbu_amount;
                // $total_cbu += $cbu_amount;
            }else{
                $repayment["P".$i] = round($principal_amount-$total_rep,2);
                $nd["P".$i] = round($charges_non_deducted_divided-$total_nd,2)+$charges_non_deducted_fixed;

                // $cbu["P".$i] = ($is_cbu_deduct == 0)?round($cbu_deficient-$total_cbu,2):0;
            }
        }
        $output['REPAYMENTS'] = $repayment;
        $output['FEES'] = $nd;

        // $output['CBU'] = $cbu;
        $interest = array();
        if($interest_method == 1){// Flat Rate
            for($i=1;$i<=$terms;$i++){
                $interest["P".$i] = ROUND($principal_amount * ($interest_rate/100),2);
            }
        }else{
            $current_balance = $principal_amount;
            for($i=1;$i<=$terms;$i++){
                $interest["P".$i] = ROUND($current_balance * ($interest_rate/100),2);
                $current_balance = $current_balance-$repayment["P".$i];
            }
        }
        $output['INTERESTS'] = $interest;
        $output['TOTAL_INTEREST'] = 0;
        $output['TOTAL_AMOUNT_DUE'] = 0;
        $loan_table = array();
        for($i=1;$i<=$terms;$i++){
            $term_code = "P".$i;
            // if($deduct_interest == 1 && $payment_type == 2){
            if($deduct_interest == 1){
                $interest[$term_code] = 0;
            }
            
            $temp_table = array();
            $temp_table["count"] = $i;
            $temp_table["term_code"] = $term_code;
            $temp_table["repayment_amount"] = $repayment[$term_code];
            $temp_table["interest_amount"] = $interest[$term_code];
            $temp_table["total_due"] = $repayment[$term_code]+$interest[$term_code]+$nd[$term_code];
            $temp_table["fees"] = $nd[$term_code];
            array_push($loan_table,$temp_table);

            $output['TOTAL_INTEREST'] += $interest[$term_code];
            $output['TOTAL_AMOUNT_DUE'] += $temp_table["total_due"];
        }
        $output["LOAN_TABLE"] = $loan_table;
        $output['repaymentCount'] = count($loan_table);
        $output['TOTAL_LOAN_PROCEED'] =$output['PRINCIPAL_AMOUNT'] - $output['TOTAL_DEDUCTED_CHARGES'];

        $output['MANUAL_DEDUCTION'] = array();

        for($k=0;$k<count($output['OTHER_DEDUCTIONS']);$k++){
            if(!isset($output['OTHER_DEDUCTIONS'][$k]['manual'])){
                $output['OTHER_DEDUCTIONS'][$k]['manual']=0;
                $output['OTHER_DEDUCTIONS'][$k]['remarks']=null;
            }elseif($output['OTHER_DEDUCTIONS'][$k]['manual'] == 1){

            }
        }
        // dd($output['OTHER_DEDUCTIONS']);


        return $output;
    }

    public static function LoanDetails($id_loan,$date_released = null){     


        $output['service_details'] = DB::table('loan as ap_loan')
        ->select("ap_loan.id_loan","ap_loan.loan_token",DB::raw("getLoanServiceName(ap_loan.id_loan_payment_type,ls.name,ap_loan.terms) as name"),"ap_loan.id_loan_service","ap_loan.interest_rate","terms.terms","ap_loan.period","ls.cbu_amount","ap_loan.is_deduct_cbu",DB::raw("ap_loan.principal_amount,ap_loan.loan_protection_rate,ap_loan.id_term_period,ap_loan.id_interest_period,ap_loan.id_interest_method,if(ap_loan.id_loan_payment_type =1,concat(ap_loan.terms,' ',p.description),getMonth(terms.period)) as terms_desc,ap_loan.terms_token,ls.min_amount,ls.max_amount,ap_loan.id_loan_payment_type,concat(getMonth(ap_loan.end_month_period),' ',ap_loan.repayment_schedule,', ',YEAR(curdate())) as due_date,ap_loan.id_member,ap_loan.loan_protection_rate,ap_loan.id_charges_group,loan_protection_amount,cbu_deficient,total_deductions,ap_loan.not_deducted_charges,ap_loan.id_member,ap_loan.total_loan_proceeds,ap_loan.status,ap_loan.loan_remarks,LoanStatus(ap_loan.status) as loan_status,ap_loan.status as status_code,ap_loan.cancellation_reason,ap_loan.prev_loan_balance,ap_loan.prev_loan_rebates,ap_loan.loan_status as lstatus,ap_loan.id_cash_disbursement,getLoanTotalPaymentType(ap_loan.id_loan,1) as paid_principal,getLoanTotalPaymentType(ap_loan.id_loan,2) as paid_interest,getLoanTotalPaymentType(ap_loan.id_loan,3) as paid_fees,ap_loan.date_released,DATE_FORMAT(ap_loan.date_released,'%m/%d/%Y') as date_granted,month_duration,interest_show,ls.id_loan_payment_type,ls.id_one_time_type"))
        ->leftJoin('loan_service as ls','ls.id_loan_service','ap_loan.id_loan_service')
        ->leftJoin('terms','ap_loan.terms_token','terms.terms_token')
        ->where('ap_loan.id_loan',$id_loan)
        ->leftJoin('period as p','p.id_period','ls.id_term_period')
        ->first();

        $loan_offset = DB::table('loan_offset as lo')
        ->leftJoin('loan','loan.id_loan','lo.id_loan_to_pay')
        ->where('lo.id_loan',$id_loan)
        ->get();

        $loan_paid = array();
        foreach($loan_offset as $lo){
            $temp = array();
            $temp['loan_token'] = $lo->loan_token;
            $temp['amount'] = $lo->amount;

            array_push($loan_paid,$temp);
        }

        $loan_payment = array();
        
        if(count($loan_paid) > 0){
            // $date_rel = $date_released ?? MySession::current_date();
            if(isset($date_released)){
                $date_rel = $date_released;
            }elseif(isset($output['service_details']->date_released)){
                $date_rel = $output['service_details']->date_released;
            }else{
                $date_rel =MySession::current_date();
            }
            $loan_payment =self::parseExistingLoanBalance($output['service_details']->id_loan_service,$output['service_details']->terms_token,$output['service_details']->id_member,$date_rel,$loan_paid,2)['active_loan'];
            // return $loan_parameter;
        }

        $output['member_details'] = DB::table('member as m')->select(DB::raw("FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as name"))->where('id_member',$output['service_details']->id_member)->first();

        $charges = self::getLoanCharges($id_loan);


        // dd($charges);
        $deducted_charges = json_decode(json_encode($charges['DEDUCTED']),true);

        // dd($deducted_charges);
        $output['OTHER_DEDUCTIONS'] = array();

        //Check if there is loan protection
        if($output['service_details']->loan_protection_amount > 0){
            $loan_protection_rate = $output['service_details']->loan_protection_rate;
            $c = [
                'charge_complete_details' => "Insurance ($loan_protection_rate%)",
                'calculated_charge' => $output['service_details']->loan_protection_amount
            ];
            array_push($deducted_charges,$c);
            $loan_protection_deductions = [
                'id_loan' => $id_loan,
                'id_loan_fees' => config('variables.id_loan_protection'),
                'id_fee_calculation' => 2,
                'value' => $output['service_details']->loan_protection_amount,
                'is_deduct' => 1,
                'application_fee_type' => 1,
                'calculated_charge'=> $output['service_details']->loan_protection_amount,
                'is_loan_charges'=> 0
            ];  
            array_push($output['OTHER_DEDUCTIONS'],$loan_protection_deductions);
        }

        $manualDeduction = DB::table('loan_charges as lc')
                            ->select(DB::raw("lc.id_loan,lc.id_loan_fees,lc.id_fee_calculation,lc.value,lc.is_deduct,lc.application_fee_type,lc.calculated_charge,lc.is_loan_charges,lf.name,lc.manual,lc.remarks"))
                           ->leftJoin('loan_fees as lf','lf.id_loan_fees','lc.id_loan_fees')
                           ->where('lc.id_loan',$id_loan)
                           ->where('lc.manual',1)
                           ->get();
        $manualDeduction = json_decode(json_encode($manualDeduction),true);
        $totalManualDeduction = 0;
        foreach($manualDeduction as $co=>$m){
            // dd($m);
           $totalManualDeduction += $m['value'];
           $c = [
                'charge_complete_details' => $m['name']." (".$m['remarks'].")",
                'calculated_charge' => $m['value']
           ]; 
            array_push($deducted_charges,$c);
           unset($manualDeduction[$co]['name']);
           // dd($c);
        }

        // dd($manualDeduction);

        $output['OTHER_DEDUCTIONS'] = array_merge($output['OTHER_DEDUCTIONS'],$manualDeduction);
       


        if($output['service_details']->status_code == 3 || $output['service_details']->status_code == 6){
            $for_viewing = true;
            $cbu_deficient = $output['service_details']->cbu_deficient;  
        }else{
            $for_viewing = false;

            if($output['service_details']->is_deduct_cbu == 1){
                $cbu_deficient = Member::CheckCBU($output['service_details']->cbu_amount,$output['service_details']->id_member)['difference'];
            }else{
                $cbu_deficient = 0;
            }
        }
        
        //check if there is cbu deficient
        if($output['service_details']->cbu_deficient > 0){
            $c = [
                'charge_complete_details' => 'CBU deficient amount',
                'calculated_charge' => $cbu_deficient
            ];
            array_push($deducted_charges,$c);
            $cbu_deductions = [
                'id_loan' => $id_loan,
                'id_loan_fees' => config('variables.id_cbu_deficient'),
                'id_fee_calculation' => 2,
                'value' => $cbu_deficient,
                'is_deduct' => 1,
                'application_fee_type' => 1,
                'calculated_charge'=> $cbu_deficient,
                'is_loan_charges'=> 0
            ];
            array_push($output['OTHER_DEDUCTIONS'],$cbu_deductions);
        }
        $output['CBU_DEFICIENT_AMOUNT'] = $cbu_deficient;
        $output['DEDUCTED_CHARGES'] = $deducted_charges;

        if($for_viewing){
            $loan_payment = DB::select("SELECT concat('ID #',loan.id_loan,' ',getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms)) as loan,
                lo.amount as payment,lo.rebates
                FROM loan_offset as lo 
                LEFT JOIN loan on loan.id_loan = lo.id_loan_to_pay
                LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
                where lo.id_loan = $id_loan");
        }

        $output['PREV_LOAN_OFFSET'] = $loan_payment;
        $output['TOTAL_LOAN_OFFSET_REBATES'] = 0;
        $total_prev_loan_offset = 0;


        foreach($loan_payment as $lp){
            $total_prev_loan_offset += $lp->payment;
            if($lp->rebates > 0){
                $output['TOTAL_LOAN_OFFSET_REBATES']+=$lp->rebates;
            }
        }   
        $output['TOTAL_LOAN_OFFSET'] = $total_prev_loan_offset-$output['TOTAL_LOAN_OFFSET_REBATES'];

        //LOAN IS FOR VIEWING ONLY
        if($for_viewing){
            $output['LOAN_BALANCE'] = $output['service_details']->prev_loan_balance;
      
            $output['REBATES'] =$output['service_details']->prev_loan_rebates;      
            $output['TOTAL_LOAN_BALANCE'] = $output['LOAN_BALANCE']-$output['REBATES'];
            $output['TOTAL_DEDUCTED_CHARGES'] = $output['service_details']->total_deductions;
            $output['PRINCIPAL_AMOUNT'] = $output['service_details']->principal_amount;
            $output['TOTAL_NOT_DEDUCTED_CHARGES'] = $output['service_details']->not_deducted_charges;
            $output['TOTAL_LOAN_PROCEED'] = $output['service_details']->total_loan_proceeds;
            
            // $output['REPAYMENT_TABLE'] = DB::select("SELECT rt.repayment_token,rt.transaction_date as a_date,rt.id_repayment_transaction as reference,DATE_FORMAT(rt.transaction_date,'%m/%d/%Y') as transaction_date,
            //     SUM(paid_interest) as paid_interest,SUM(paid_fees) as paid_fees,SUM(paid_principal) as paid_principal
            //     FROM repayment_loans as rl 
            //     LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rl.id_repayment_transaction
            //     WHERE rl.id_loan = ? and rl.status <> 10 AND rt.status <> 10
            //     GROUP BY rl.id_repayment_transaction
            //     HAVING (SUM(paid_interest)+SUM(paid_fees)+SUM(paid_principal)) > 0
            //     ORDER BY a_date ASC;",[$id_loan]);
            // $output['REPAYMENT_TABLE'] = DB::select("SELECT * FROM (
            // SELECT rt.repayment_token,rt.transaction_date as a_date,rt.id_repayment_transaction as reference,DATE_FORMAT(rt.transaction_date,'%m/%d/%Y') as transaction_date,
            // SUM(paid_interest) as paid_interest,SUM(paid_fees) as paid_fees,SUM(paid_principal) as paid_principal,ifnull(rls.amount,0) as penalty
            // FROM repayment_loans as rl 
            // LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rl.id_repayment_transaction
            // LEFT JOIN repayment_loan_surcharges as rls on rls.id_repayment_transaction = rl.id_repayment_transaction AND rl.id_loan = rls.id_loan
            // WHERE rl.id_loan = ? and rl.status <> 10 AND rt.status <> 10
            // GROUP BY rl.id_repayment_transaction
            // HAVING (SUM(paid_interest)+SUM(paid_fees)+SUM(paid_principal)) > 0
            // UNION ALL
            // SELECT rt.repayment_token,rt.transaction_date,rt.id_repayment_transaction as reference,DATE_FORMAT(rt.transaction_date,'%m/%d/%Y') as transaction_date,
            // 0 as paid_interest,0 as paid_fees,0 as paid_principal,ifnull(rls.amount,0) as penalty FROM repayment_loan_surcharges as rls
            // LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rls.id_repayment_transaction
            // LEFT JOIN repayment_loans as rl on rls.id_loan = rl.id_loan AND rl.id_repayment_transaction = rt.id_repayment_transaction
            // WHERE rls.id_loan = ? AND rt.status <> 10 AND rl.id_loan is null) as k
            // ORDER BY a_date ASC;",[$id_loan,$id_loan]);


            $output['AMTZ_SCHED']=$amtz_sched = DB::select("SELECT term_code,DATE_FORMAT(due_date,'%m/%d/%Y') as date,repayment_amount as principal,interest_amount as interest,surcharge as surcharge,
                repayment_amount+interest_amount+surcharge as total,accrued
            FROM loan_table as lt
            LEFT JOIN loan on loan.id_loan = lt.id_loan
            WHERE lt.id_loan = ?
            ORDER BY due_date;",[$id_loan]);

            $amtz_payment = DB::select("SELECT concat('OR # ',rt.or_no) as or_no,rl.term_code,DATE_FORMAT(rt.transaction_date,'%m/%d/%Y') as date,rt.id_repayment_transaction,ifnull(rt.id_repayment,0) as id_repayment,
            rl.paid_principal as principal,rl.paid_interest as interest,paid_surcharge as surcharge,paid_principal+paid_interest+paid_surcharge+paid_fees as total,rt.repayment_token,
            CASE 
                WHEN rt.pay_on_id_loan > 0 THEN concat('Loan ID# ',rt.pay_on_id_loan)
                WHEN rt.id_cash_receipt_voucher = 0 AND rt.id_journal_voucher = 0 THEN 'Beginning'
                ELSE '' END as payment_reference
            FROM repayment_loans as rl
            LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rl.id_repayment_transaction
            LEFT JOIN loan_table as lt on lt.id_loan = rl.id_loan AND lt.term_code = rl.term_code
            WHERE rl.id_loan = ? AND rt.status <> 10 AND (paid_principal+paid_interest+paid_fees+paid_surcharge) > 0
            ORDER BY lt.count,rt.transaction_date;",[$id_loan]);

            $g = new GroupArrayController();
            
            $output['AMTZ_PAYMENT'] = $g->array_group_by($amtz_payment,['term_code']);

 

            $output['REPAYMENT_TABLE'] = DB::select("SELECT k.*,
            CASE 
            WHEN k.pay_on_id_loan > 0 THEN 'Loan'
            WHEN k.id_cash_receipt_voucher = 0 AND k.id_journal_voucher = 0 THEN 'Beginning'
            WHEN r.id_repayment is null THEN '-'
            WHEN r.payment_for = 1 THEN 'Individual'
            WHEN r.payment_for = 2 THEN 'Statement' 
            END as payment_reference,
            concat(if(bl.type=1,'Brgy. ','LGU - '),bl.name,' [',DATE_FORMAT(rs.date,'%m-%Y'),'] - ') as payment_source
            FROM (
            SELECT rt.id_repayment,rt.id_repayment_statement,rt.repayment_token,rt.transaction_date as a_date,rt.id_repayment_transaction as reference,DATE_FORMAT(rt.transaction_date,'%m/%d/%Y') as transaction_date,
            SUM(paid_interest) as paid_interest,SUM(paid_fees) as paid_fees,SUM(paid_principal) as paid_principal,SUM(paid_surcharge) as paid_surcharge,
            rt.id_cash_receipt_voucher,rt.id_journal_voucher,rt.pay_on_id_loan,l.loan_token
            FROM repayment_loans as rl
            LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rl.id_repayment_transaction
            LEFT JOIN loan as l on l.id_loan = rt.pay_on_id_loan
            LEFT JOIN repayment_loan_surcharges as rls on rls.id_repayment_transaction = rl.id_repayment_transaction AND rl.id_loan = rls.id_loan
            WHERE rl.id_loan = ? and rl.status <> 10 AND rt.status <> 10
            GROUP BY rl.id_repayment_transaction) as k
            LEFT JOIN repayment as r on r.id_repayment = k.id_repayment
            LEFT JOIN repayment_statement as rs on rs.id_repayment_statement = k.id_repayment_statement
            LEFT JOIN baranggay_lgu as bl on bl.id_baranggay_lgu = rs.id_baranggay_lgu
            UNION ALL
            SELECT null as id_repayment,0 as id_repayment_statement,rt.repayment_token,rt.transaction_date,rt.id_repayment_transaction as reference,DATE_FORMAT(rt.transaction_date,'%m/%d/%Y') as transaction_date,
            0 as paid_interest,0 as paid_fees,0 as paid_principal,ifnull(rls.amount,0) as penalty 
            ,0 as id_cash_receipt_voucher,0 as id_journal_voucher,0 as pay_on_id_loan,'' as loan_token,'-' as payment_reference,''  as payment_source
            FROM repayment_loan_surcharges as rls
            LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rls.id_repayment_transaction
            LEFT JOIN repayment_loans as rl on rls.id_loan = rl.id_loan AND rl.id_repayment_transaction = rt.id_repayment_transaction
            WHERE rls.id_loan = ? AND rt.status <> 10 AND rl.id_loan is null;",[$id_loan,$id_loan]);


            $dt = WebHelper::ConvertDatePeriod(MySession::current_date());

            $output['CURRENT_DUE'] = DB::table('loan')
                                     ->select(DB::raw("DATE_FORMAT(curdate(),'%M %d, %Y') as cur_date,getPrincipalBalanceAsOf(id_loan,'$dt') as prin,getInterestBalanceAsOf(id_loan,'$dt') as interest,getFeesBalanceAsOf(id_loan,'$dt') as fees,getSurchargeBalanceAsOf(id_loan,'$dt') as surcharge"))
                                     ->where('id_loan',$id_loan)
                                     ->first();

            $output['TOTAL_DUE'] = $output['CURRENT_DUE']->prin+$output['CURRENT_DUE']->interest+$output['CURRENT_DUE']->fees+$output['CURRENT_DUE']->surcharge;
        }else{

            $prev = Member::CheckPreviousLoan($output['service_details']->id_loan_service,$output['service_details']->id_member,$output['service_details']->terms_token,$date_released);
            $output['LOAN_BALANCE'] = $prev['LOAN_BALANCE'];
            $output['SURCHARGE_BALANCE_RENEW'] = $prev['SURCHARGE'] ?? 0;
            $output['REBATES'] = $prev['REBATES'];
            $output['PREVIOUS_LOAN_ID'] = $prev['LATEST_LOAN_ID'];

            $output['TOTAL_LOAN_BALANCE'] = $output['LOAN_BALANCE']-$output['REBATES']+$output['SURCHARGE_BALANCE_RENEW'];

            $output['TOTAL_DEDUCTED_CHARGES'] = $charges['DEDUCTED_TOTAL']+$output['service_details']->loan_protection_amount+$cbu_deficient+$output['TOTAL_LOAN_BALANCE']+$output['TOTAL_LOAN_OFFSET']+$totalManualDeduction;
            $output['PRINCIPAL_AMOUNT'] = $output['service_details']->principal_amount;
            $output['TOTAL_NOT_DEDUCTED_CHARGES'] = $output['service_details']->not_deducted_charges;
            $output['TOTAL_LOAN_PROCEED'] =$output['PRINCIPAL_AMOUNT'] - $output['TOTAL_DEDUCTED_CHARGES'];


        }
        //LOAN TABLE
        // $loan_table = json_decode(json_encode(DB::table('loan_table as lt')
        //               ->select('lt.*',DB::raw("DATE_FORMAT(lt.due_date,'%m/%d/%Y') as due_date,if(loan.loan_status > 0,if(lt.is_paid=0,0,if(lt.is_paid=1,lt.total_due,getLoanTotalTermPayment(lt.id_loan,lt.term_code))),0) as paid_amount,if(lt.is_paid=0,'',if(lt.is_paid=1,'Paid','Partially Paid')) as remarks"))
        //               ->leftJoin('loan','loan.id_loan','lt.id_loan')
        //               ->where('lt.id_loan',$id_loan)
        //               ->orDerby('lt.count')
        //               ->get()),true);
        // $loan_table = json_decode(json_encode(DB::table('loan_table as lt')
        //               ->select('lt.*',DB::raw("DATE_FORMAT(lt.due_date,'%m/%d/%Y') as due_date,if(loan.loan_status > 0,if(lt.is_paid=0,0,getLoanTotalTermPayment(lt.id_loan,lt.term_code)),0) as paid_amount,if(lt.is_paid=0,'',if(lt.is_paid=1,'Paid','Partially Paid')) as remarks"))
        //               ->leftJoin('loan','loan.id_loan','lt.id_loan')
        //               ->where('lt.id_loan',$id_loan)
        //               ->orDerby('lt.count')
        //               ->get()),true);
        // $loan_table = json_decode(json_encode(DB::select("SELECT * ,if(total_due-paid_amount >0 AND status=6,'Renewed',if(is_paid=0,'',if(is_paid=1,'Paid','Partially Paid'))) as remarks FROM (
        // SELECT lt.count,lt.term_code,lt.repayment_amount,lt.interest_amount,lt.fees,lt.total_due,DATE_FORMAT(lt.due_date,'%m/%d/%Y') as due_date,if(loan.loan_status > 0,if(lt.is_paid=0,0,getLoanTotalTermPayment(lt.id_loan,lt.term_code)),0) as paid_amount,lt.is_paid,loan.status
        // FROM loan_table as lt
        // LEFT JOIN loan on loan.id_loan = lt.id_loan
        // WHERE lt.id_loan = $id_loan
        // ORDER BY lt.count) as t;")),true);


        $loan_table = json_decode(json_encode(DB::select("SELECT * ,if(is_paid=3,'Renewed',if(is_paid=0,'',if(is_paid=1,'Paid','Partially Paid'))) as remarks FROM (
        SELECT lt.count,lt.term_code,lt.repayment_amount,lt.interest_amount,lt.fees,lt.total_due,DATE_FORMAT(lt.due_date,'%m/%d/%Y') as due_date,if(loan.loan_status > 0,if(lt.is_paid=0,0,getLoanTotalTermPayment(lt.id_loan,lt.term_code)),0) as paid_amount,lt.is_paid,loan.status,lt.accrued
        FROM loan_table as lt
        LEFT JOIN loan on loan.id_loan = lt.id_loan
        WHERE lt.id_loan = $id_loan
        ORDER BY lt.count) as t;")),true);

        $repaymentCount = collect($loan_table)->filter(function ($item) {
            return $item['accrued'] == 0;
        })->count();



        $output['repaymentCount'] = $repaymentCount;


        // dd($output);
        for($k=0;$k<count($output['OTHER_DEDUCTIONS']);$k++){
            if(!isset($output['OTHER_DEDUCTIONS'][$k]['manual'])){
                $output['OTHER_DEDUCTIONS'][$k]['manual']=0;
                $output['OTHER_DEDUCTIONS'][$k]['remarks']=null;
            }
        }
        $output["LOAN_TABLE"] = $loan_table;

        $totals = self::parseLoanTableTotals($loan_table);
        $output['TOTAL_INTEREST'] = $totals['TOTAL_INTEREST'];
        $output['TOTAL_AMOUNT_DUE'] = $totals['TOTAL_AMOUNT_DUE'];
        $output['TOTAL_PAID_AMOUNT'] = $totals['TOTAL_PAID_AMOUNT'];
        $output['CURRENT_LOAN_BALANCE'] = $totals['TOTAL_AMOUNT_DUE'] - $totals['TOTAL_PAID_AMOUNT'];
        // return $loan_table;
        
  
        return $output;
    }   

    public static function getLoanCharges($id_loan){
        $comp_charge_param = "if(c.id_fee_calculation=1,calculateChargeAmountPer(ap_loan.principal_amount,ap_loan.interest_rate,c.value,c.id_calculated_fee_base),c.value) as calculated_charge";

        // $comp_charge_param = "calculated_charge";

        $charges = DB::table('loan_charges as c')
        ->select(DB::raw("c.id_loan_fees,c.id_fee_calculation,c.value,concat(lf.name,if(c.id_fee_calculation=1,concat(' (',value,'%)'),'')) as charge_complete_details,lf.name as fee_name,c.id_calculated_fee_base,if(c.id_fee_calculation=1,'Percentage','Fixed') as fee_calculation,c.value,if(c.id_fee_calculation=1,concat(value,'% of ',cb.description),concat('₱',FORMAT(value,2))) as charge_description,if(c.is_deduct=1,'DEDUCTED','NOT_DEDUCTED') as is_deduct_text,c.is_deduct,c.application_fee_type,$comp_charge_param,if(c.non_deduct_option is null,'',if(c.non_deduct_option=1,'Fixed','Divided')) as non_deduct_option"))
        ->leftJoin('loan as ap_loan','ap_loan.id_loan','c.id_loan')
        ->leftJoin('loan_fees as lf','lf.id_loan_fees','c.id_loan_fees')
        ->LeftJoin('calculated_fee_base as cb','cb.id_calculated_fee_base','c.id_calculated_fee_base')
        ->where('ap_loan.id_loan',$id_loan)
        ->where('is_loan_charges',1)
        ->orDerby('id_loan_charges')
        ->get();


        $g = new GroupArrayController();
        $separated_charge = $g->array_group_by($charges,['is_deduct_text']);
        $charges_output = array();

        $charges_output['DEDUCTED'] = $separated_charge['DEDUCTED'] ?? [];
        $charges_output['NOT_DEDUCTED'] = $separated_charge['NOT_DEDUCTED'] ?? [];

        $charges_output['DEDUCTED_TOTAL'] = self::sum_charges($charges_output['DEDUCTED']);
        $charges_output['NOT_DEDUCTED_TOTAL'] = self::sum_charges($charges_output['NOT_DEDUCTED']);

        $non_deduct = $g->array_group_by($charges_output['NOT_DEDUCTED'],['non_deduct_option']);

        $charges_output['NOT_DEDUCTED_FIXED_TOTAL'] = self::sum_charges($non_deduct['Fixed'] ?? []);
        $charges_output['NOT_DEDUCTED_DIVIDED_TOTAL'] = self::sum_charges($non_deduct['Divided'] ?? []);

        return $charges_output;
    }

    public static function parseLoanTableTotals($loan_table){
        $output = array(
            'TOTAL_INTEREST' => 0,
            'TOTAL_AMOUNT_DUE' => 0,
            'TOTAL_PAID_AMOUNT' => 0,
            
        );
        for($i=0;$i<count($loan_table);$i++){
            $output['TOTAL_INTEREST'] += $loan_table[$i]['interest_amount'];
            $output['TOTAL_AMOUNT_DUE'] += $loan_table[$i]['total_due'];
            $output['TOTAL_PAID_AMOUNT'] += $loan_table[$i]['paid_amount'];
        }

        return $output;
    }

    public static function sum_charges($charges){
        //This will sum up array of charges
        $total = 0;
        foreach($charges as $c){
            $total += $c->calculated_charge;
        }
        return $total;
    }
    public static function PayPreviousLoan($id_loan_service,$id_member,$date,$terms_token,$new_id_loan){
        $date_con2 = WebHelper::ConvertDatePeriod2($date);

        $latest_loan = DB::table('loan')->select("id_loan",DB::raw("if('$date_con2' > loan.maturity_date,'$date_con2',maturity_date) as maturity_date"))->where('id_loan_service',$id_loan_service)->where('id_member',$id_member)->where('loan_status',1)->where("terms_token",$terms_token)->first(); 
        $id_loan = $latest_loan->id_loan;
        $date_fil = WebHelper::ConvertDatePeriod($date);
        $date_fil2 = (env('RENEWAL_INTEREST_FULL_CONTRACT')&&isset($latest_loan))?$latest_loan->maturity_date:$date_con2;



        $sql_term_balance = "SELECT lt.id_loan,lt.term_code,lt.due_date,
        (lt.repayment_amount - ifnull(SUM(rl.paid_principal),0)) as paid_principal,
        if(due_date <= '$date_fil2',lt.interest_amount-ifnull(SUM(rl.paid_interest),0),0) as paid_interest,
            0 as paid_fees,
        lt.surcharge - ifnull(SUM(rl.paid_surcharge),0) as paid_surcharge
        FROM loan_table as lt
        LEFT JOIN repayment_loans as rl on rl.id_loan = lt.id_loan AND lt.term_code =rl.term_code and rl.status < 10
        where lt.id_loan = ? and is_paid <> 1
        GROUP BY lt.id_loan,lt.term_code
        ORDER BY count;";

        $balances = DB::select($sql_term_balance,[$id_loan]);

        // return $balances;
        $repayment_loans = array();
        $total = 0;

        foreach($balances as $bal){
            $temp = array();
            $temp['id_loan'] = $bal->id_loan;
            $temp['term_code'] = $bal->term_code;
            $temp['paid_principal'] = $bal->paid_principal ?? 0;
            $temp['paid_interest'] = $bal->paid_interest ?? 0;
            $temp['paid_fees'] = $bal->paid_fees ?? 0;
            $temp['paid_surcharge'] = $bal->paid_surcharge ?? 0;

            $total += $temp['paid_principal']+$temp['paid_interest']+$temp['paid_fees']+$temp['paid_surcharge'];
            array_push($repayment_loans,$temp);
        }

        $repayment_transaction = [
            'repayment_token'=> DB::raw("concat(DATE_FORMAT(now(),'%m%d%Y%H%i%s'),concat(LEFT(MD5(NOW()), 5)))"),
            'date' => $date_fil,
            'transaction_date' => $date,
            'id_member' => $id_member,
            'total_loan_payment' => $total,
            'swiping_amount' => $total,
            'total_fees' => 0,
            'total_penalty' => 0,
            'total_payment' => $total,
            'change' => 0,
            'pay_on_id_loan'=>$new_id_loan,
            'repayment_type' => 2,
            'email_sent'=>2
        ];

        if(count($repayment_loans) > 0){
            DB::table('repayment_transaction')
            ->insert($repayment_transaction);

            $id_repayment_transaction = DB::table('repayment_transaction')->where('id_member',$id_member)->max('id_repayment_transaction');

            for($i=0;$i<count($repayment_loans);$i++){
                $repayment_loans[$i]['id_repayment_transaction'] = $id_repayment_transaction;
            } 

            DB::table('repayment_loans')
            ->insert($repayment_loans);

            DB::table('loan_table')->where('id_loan',$id_loan)->where('is_paid','<>',1)->update(['is_paid'=>1]);
            DB::table('loan')
            ->where('id_loan',$id_loan)
            ->update([
                'loan_status'=>2,
                'status'=>6
            ]);

            return $repayment_loans;           
        }
    }
    public static function GenerateCDV($id_loan){

        //Cancel the previous CDV if exist (for editing)
        // DB::table("cash_disbursement")
        // ->where('type',1)
        // ->where('reference',$id_loan)
        // ->update(['status'=>10]);


        $id_cash_disbursement = DB::table('loan')->select('id_cash_disbursement')->where('id_loan',$id_loan)->first()->id_cash_disbursement;

        if($id_cash_disbursement == 0){

            //Insert parent CDV
            DB::select("INSERT INTO cash_disbursement (date,type,description,id_member,payee,reference,status,total,id_branch,address,paymode,paymode_account,payee_type)
                SELECT date_released,1 as type,concat('Loan Release for ',getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms),' ID #',loan.id_loan) as description,loan.id_member,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as payee,
                loan.id_loan,0 as status,total_loan_proceeds,m.id_branch,m.address,1 as paymode,1 as paymode_account,2 as payee_type

                FROM loan 
                LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
                LEFT JOIN member as m on m.id_member = loan.id_member
                where loan.id_loan = ?;",[$id_loan]);

                $id_cash_disbursement = DB::table('cash_disbursement')->where('type',1)->where('reference',$id_loan)->max('id_cash_disbursement');
        }else{
            DB::select("UPDATE loan
            LEFT JOIN cash_disbursement as cd on cd.id_cash_disbursement = loan.id_cash_disbursement
            set cd.total = loan.total_loan_proceeds
            WHERE id_loan  = ?;",[$id_loan]);

            DB::table('cash_disbursement_details')
            ->where('id_cash_disbursement',$id_cash_disbursement)
            ->delete();
        }
        
        DB::select("INSERT INTO cash_disbursement_details (id_cash_disbursement,id_chart_account,account_code,description,debit,credit,details,reference,id_account_code_maintenance)
            SELECT $id_cash_disbursement as id_cash_disbursement,ca.id_chart_account,ca.account_code as account_code,ca.description,principal_amount as debit,0 as credit,concat('Loan ID#',loan.id_loan,' Principal Amount') as remarks,id_loan as reference,ac.id_account_code_maintenance
            FROM loan 
            LEFT JOIN account_code_maintenance as ac on ac.id_account_code_maintenance = 1
            LEFT JOIN chart_account as ca on ca.id_chart_account = ac.id_chart_account
            where loan.id_loan = ?
            UNION ALL
            SELECT $id_cash_disbursement as id_cash_disbursement,ca.id_chart_account,ca.account_code,ca.description,SUM(rebates) as debit,0 as credit,remarks,id_loan,ac.id_account_code_maintenance FROM (
                SELECT prev_loan_rebates as rebates,concat('Loan ID#',loan.id_loan,' Rebates') as remarks,id_loan
                FROM loan 
                LEFT JOIN account_code_maintenance as ac on ac.id_account_code_maintenance = 3
                LEFT JOIN chart_account as ca on ca.id_chart_account = ac.id_chart_account
                where loan.id_loan = ? and prev_loan_balance > 0 and (prev_loan_rebates > 0)
                UNION ALL
                SELECT rebates,concat('Loan ID#',lo.id_loan_to_pay,' Rebates') as remarks,id_loan FROM loan_offset as lo
                WHERE lo.id_loan = ? and rebates > 0) as rebates
            LEFT JOIN account_code_maintenance as ac on ac.id_account_code_maintenance = 3
            LEFT JOIN chart_account as ca on ca.id_chart_account = ac.id_chart_account
            HAVING SUM(rebates) > 0
            UNION ALL
            SELECT $id_cash_disbursement as id_cash_disbursement,lf.id_chart_account,account_code,ca.description,0 as debit,
            if(c.id_fee_calculation=1,calculateChargeAmountPer(ap_loan.principal_amount,ap_loan.interest_rate,
                c.value,c.id_calculated_fee_base),c.value) as credit,
                concat('Loan ID#',ap_loan.id_loan,' ',lf.name,if(c.id_fee_calculation=1,concat(' (',value,'%)'),'')) as remarks,c.id_loan_charges as reference,0 as id_account_code_maintenance
            FROM loan_charges as c
            LEFT JOIN loan as ap_loan on ap_loan.id_loan = c.id_loan
            LEFT JOIN loan_fees as lf on lf.id_loan_fees = c.id_loan_fees
            LEFT JOIN calculated_fee_base as cb on cb.id_calculated_fee_base = c.id_calculated_fee_base
            LEFT JOIN tbl_payment_type as pt on pt.reference = c.id_loan_fees and pt.type = 2
            LEFT JOIN chart_account as ca on ca.id_chart_account = lf.id_chart_account
            WHERE ap_loan.id_loan =  ? and non_deduct_option is null
            UNION ALL
            /**************PRINCIPAL******************/
            SELECT $id_cash_disbursement as id_cash_disbursement,ca.id_chart_account,ca.account_code,ca.description,0 as debit,credit as credit,remarks,id_loan,ac.id_account_code_maintenance FROM (
                SELECT previous_principal as credit,concat('Payment for Principal from previous loan (ID#',id_loan_previous,' ',getLoanServiceName(l.id_loan_payment_type,ls.name,l.terms),')') as remarks,id_loan_previous as id_loan
                FROM paid_previous_balance as pbb
                LEFT JOIN loan as l on l.id_loan = pbb.id_loan_previous
                LEFT JOIN loan_service as ls on ls.id_loan_service = l.id_loan_service
                where id_loan_current = ? and previous_principal > 0
                UNION ALL
                SELECT p_principal,concat('Payment for Principal (ID#',lo.id_loan_to_pay,' ',getLoanServiceName(l.id_loan_payment_type,ls.name,l.terms),')') as remarks,lo.id_loan_to_pay 
                FROM loan_offset as lo
                LEFT JOIN loan as l on l.id_loan = lo.id_loan_to_pay
                LEFT JOIN loan_service as ls on ls.id_loan_service = l.id_loan_service
                WHERE lo.id_loan = ? and p_principal > 0
            ) as principal
            LEFT JOIN account_code_maintenance as ac on ac.id_account_code_maintenance = 5
            LEFT JOIN chart_account as ca on ca.id_chart_account = ac.id_chart_account
            WHERE credit > 0
            UNION ALL
            /**************INTEREST******************/
            SELECT $id_cash_disbursement as id_cash_disbursement,ca.id_chart_account,ca.account_code,ca.description,0 as debit,credit as credit,remarks,id_loan,ac.id_account_code_maintenance FROM (
                SELECT previous_interest as credit,concat('Payment for Interest from previous loan (ID#',id_loan_previous,' ',getLoanServiceName(l.id_loan_payment_type,ls.name,l.terms),')') as remarks,id_loan_previous as id_loan
                FROM paid_previous_balance as pbb
                LEFT JOIN loan as l on l.id_loan = pbb.id_loan_previous
                LEFT JOIN loan_service as ls on ls.id_loan_service = l.id_loan_service
                where id_loan_current = ? and previous_interest > 0
                UNION ALL
                SELECT p_interest,concat('Payment for Interest (ID#',lo.id_loan_to_pay,' ',getLoanServiceName(l.id_loan_payment_type,ls.name,l.terms),')') as remarks,lo.id_loan_to_pay 
                FROM loan_offset as lo
                LEFT JOIN loan as l on l.id_loan = lo.id_loan_to_pay
                LEFT JOIN loan_service as ls on ls.id_loan_service = l.id_loan_service
                WHERE lo.id_loan = ? and p_interest > 0
            ) as interest
            LEFT JOIN account_code_maintenance as ac on ac.id_account_code_maintenance = 6
            LEFT JOIN chart_account as ca on ca.id_chart_account = ac.id_chart_account
            WHERE credit > 0
            UNION ALL
            /*****************FEES**************/
            SELECT $id_cash_disbursement as id_cash_disbursement,ca.id_chart_account,ca.account_code,ca.description,0 as debit,credit as credit,remarks,id_loan,ac.id_account_code_maintenance FROM (
                SELECT previous_fees as credit,concat('Payment for Fees from previous loan (ID#',id_loan_previous,' ',getLoanServiceName(l.id_loan_payment_type,ls.name,l.terms),')') as remarks,id_loan_previous as id_loan
                FROM paid_previous_balance as pbb
                LEFT JOIN loan as l on l.id_loan = pbb.id_loan_previous
                LEFT JOIN loan_service as ls on ls.id_loan_service = l.id_loan_service
                where id_loan_current = ? and previous_fees > 0
                UNION ALL
                SELECT p_fees,concat('Payment for Fees (ID#',lo.id_loan_to_pay,' ',getLoanServiceName(l.id_loan_payment_type,ls.name,l.terms),')') as remarks,lo.id_loan_to_pay 
                FROM loan_offset as lo
                LEFT JOIN loan as l on l.id_loan = lo.id_loan_to_pay
                LEFT JOIN loan_service as ls on ls.id_loan_service = l.id_loan_service
                WHERE lo.id_loan = ? and p_fees > 0
            ) as fees
            LEFT JOIN account_code_maintenance as ac on ac.id_account_code_maintenance = 7
            LEFT JOIN chart_account as ca on ca.id_chart_account = ac.id_chart_account
            WHERE credit > 0
            UNION ALL
            /**************SURCHARGE******************/
            SELECT $id_cash_disbursement as id_cash_disbursement,ca.id_chart_account,ca.account_code,ca.description,0 as debit,credit as credit,remarks,id_loan,0 FROM (
                SELECT previous_surcharge as credit,concat('Payment for Surcharge from previous loan (ID#',id_loan_previous,' ',getLoanServiceName(l.id_loan_payment_type,ls.name,l.terms),')') as remarks,id_loan_previous as id_loan
                FROM paid_previous_balance as pbb
                LEFT JOIN loan as l on l.id_loan = pbb.id_loan_previous
                LEFT JOIN loan_service as ls on ls.id_loan_service = l.id_loan_service
                where id_loan_current = ? and previous_surcharge > 0
                UNION ALL
                SELECT p_surcharge,concat('Payment for Surcharge (ID#',lo.id_loan_to_pay,' ',getLoanServiceName(l.id_loan_payment_type,ls.name,l.terms),')') as remarks,lo.id_loan_to_pay 
                FROM loan_offset as lo
                LEFT JOIN loan as l on l.id_loan = lo.id_loan_to_pay
                LEFT JOIN loan_service as ls on ls.id_loan_service = l.id_loan_service
                WHERE lo.id_loan = ? and p_surcharge > 0
            ) as surcharge
            LEFT JOIN chart_account as ca on ca.id_chart_account = 38
            WHERE credit > 0
            UNION ALL
            SELECT $id_cash_disbursement as id_cash_disbursement,ca.id_chart_account,ca.account_code,ca.description,0 as debit,total_loan_proceeds as credit,concat('Loan ID#',loan.id_loan,' Loan Proceeds') as remarks,id_loan ,ac.id_account_code_maintenance
            FROM loan 
            LEFT JOIN tbl_bank as tb on tb.id_bank = loan.disburse_mode
            LEFT JOIN account_code_maintenance as ac on ac.id_account_code_maintenance = 4
            LEFT JOIN chart_account as ca on ca.id_chart_account = ifnull(tb.id_chart_account,ac.id_chart_account)
            
            where loan.id_loan = ?",[$id_loan,$id_loan,$id_loan,$id_loan,$id_loan,$id_loan,$id_loan,$id_loan,$id_loan,$id_loan,$id_loan,$id_loan,$id_loan]);

            // LEFT JOIN account_code_maintenance as ac on ac.id_account_code_maintenance = 4
            // LEFT JOIN chart_account as ca on ca.id_chart_account = ac.id_chart_account
            //update cdv of loan
            DB::table('loan')
            ->where('id_loan',$id_loan)
            ->update(['id_cash_disbursement'=>$id_cash_disbursement]);

return $id_cash_disbursement;

}

public static function parseExistingLoanBalance($id_loan_service,$term_token,$id_member,$date,$loan_paid,$opcode){
    $date= WebHelper::ConvertDatePeriod($date);

    $and = "";
    $payments = array();
    $temp_token = array();


    $where_not = "AND (ls.is_multiple =1 OR (loan.terms_token <> '$term_token'))";

    if(count($loan_paid) > 0){
        foreach($loan_paid as $k){
            $payments[$k['loan_token']] = $k['amount'];
            array_push($temp_token,$k['loan_token']);
        }
        if($opcode ==2){
            if(count($temp_token) > 0){
                $imp = "'".implode("','",$temp_token)."'";
                $and = "AND loan.loan_token in ($imp)";                    
            }
        }
    }

    

    if(count($loan_paid) ==0 && $opcode == 2){
        $and = "AND loan.loan_token in (0)";
    }
    $interest_dt_query = env("RENEWAL_INTEREST_FULL_CONTRACT")?"if('$date' > loan.maturity_date,'$date',loan.maturity_date)":"'$date'";

    $data['active_loan'] = DB::select("SELECT loan.loan_token,loan.id_loan,concat('ID #',loan.id_loan,' ',getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms)) as loan,
        SUM(repayment_amount-(getLoanTotalTermPaymentType(loan.id_loan,term_Code,1))+if(due_date<=$interest_dt_query,interest_amount-getLoanTotalTermPaymentType(loan.id_loan,term_Code,2),0)+surcharge-getLoanTotalTermPaymentType(loan.id_loan,term_Code,4)) as balance,0 as payment,getLoanRebates(loan.id_loan,'$date') as rebates
        FROM loan 
        LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
        LEFT JOIN loan_table as lt on lt.id_loan = loan.id_loan
        where loan.id_member=? and loan.loan_status =1  $and $where_not
        GROUP BY loan.id_loan;",[$id_member]);


    for($i=0;$i<count($data['active_loan']);$i++){
        $payment_amt =$payments[$data['active_loan'][$i]->loan_token] ?? 0;
        $data['active_loan'][$i]->payment = ($payment_amt > $data['active_loan'][$i]->balance)?$data['active_loan'][$i]->balance:$payment_amt;

        $data['active_loan'][$i]->rebates= ($data['active_loan'][$i]->payment == $data['active_loan'][$i]->balance && env('WITH_REBATES_ON_FULL')) ?$data['active_loan'][$i]->rebates:0;
    }


    return $data;
}
public static function PayLoanOffset($id_loan,$date,$amount_to_pay){


    $loan_token = DB::table('loan')->select('loan_token')->where('id_loan',$id_loan)->first()->loan_token;
    $rep_con = new RepaymentController();

    $maturityValidation  = DB::table('loan')
                           ->select('maturity_date')
                           ->where('id_loan',$id_loan)
                           ->first()->maturity_date;
    
    if($maturityValidation < $date){
        // if loan is already matured
        $app_payment = $rep_con->PopulatePaymentMatured($loan_token,$amount_to_pay,0,$date,env('RENEWAL_INTEREST_FULL_CONTRACT'));
    }else{
        // if not matured
        $app_payment = $rep_con->PopulatePaymentAuto($loan_token,$amount_to_pay,0,$date,env('RENEWAL_INTEREST_FULL_CONTRACT'));
    }

    return $app_payment;


    $loan_details = DB::table('loan')->wherE('id_loan',$id_loan)->first();
    $sql="select id_loan,due_date,term_code,
    repayment_amount-getLoanTotalTermPaymentType(lt.id_loan,term_code,1) as principal_balance,
    if(due_date <= '$date',interest_amount-getLoanTotalTermPaymentType(lt.id_loan,term_code,2),0.00) as interest_balance,
    if(due_date <= '$date',fees-getLoanTotalTermPaymentType(lt.id_loan,term_code,3),0.00) as fee_balance,
    if(due_date <= '$date',1,2) as type
    FROM loan_table as lt
    where id_loan = ? and is_paid <> 1;";

    $loan_balance = DB::select($sql,[$id_loan]);

    $g = new GroupArrayController();

    $loan_balance = $g->array_group_by($loan_balance,['type']);

    $prev_current = $loan_balance[1]??[];
    $over = $loan_balance[2]??[];
    $over = array_reverse($over);

    $params['current'] = $prev_current;
    $params['over'] = $over;

    $output['payments'] = array();
    $amount_paid = 0;

    foreach($params as $loan_dues){
        foreach($loan_dues as $c){
            $temp = array();
            $temp['term_code'] = $c->term_code;

            $temp['paid_interest'] = self::calculate($amount_to_pay,$c->interest_balance);
            $amount_to_pay -=$temp['paid_interest'];
            $temp['paid_fees'] = self::calculate($amount_to_pay,$c->fee_balance);
            $amount_to_pay -=$temp['paid_fees'];
            $temp['paid_principal'] = self::calculate($amount_to_pay,$c->principal_balance);
            $amount_to_pay -=$temp['paid_principal'];
            $temp['is_advance'] = 1;
            $temp['id_loan'] = $c->id_loan;

            if(($temp['paid_interest']+$temp['paid_fees']+$temp['paid_principal']) > 0){
                $amount_paid += $temp['paid_interest']+$temp['paid_fees']+$temp['paid_principal'];
                array_push($output['payments'],$temp);
            }
        }            
    }
    $output['amount_paid'] = $amount_paid;
    return $output;
}
public static function generateRandomString($length = 5) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;
}
public static function GenerateOffsetRepayment($id_loan,$date){
    $loan_details = DB::table('loan')->where('id_loan',$id_loan)->first();
    $loan_offset = DB::table('loan_offset')
    ->where('id_loan',$id_loan)
    ->get();
    $aDate = $date;
    $date = WebHelper::ConvertDatePeriod($date);

    $id_loan_paid = array();

    if(count($loan_offset) > 0){
        $repayment_loans = array();
        $total_payment = 0;
        foreach($loan_offset as $lo){
            $out = self::PayLoanOffset($lo->id_loan_to_pay,$date,$lo->amount);
                // return $out;
            if(count($out['payments']) > 0){
                foreach($out['payments'] as $p){
                    array_push($repayment_loans,$p);
                }
                array_push($id_loan_paid,$lo->id_loan_to_pay);
            }

            $total_payment += $out['amount_paid'];
        }

        $imp = implode(",",$id_loan_paid);
        $add_token = self::generateRandomString(5);

        $repayment_transaction = [
            'repayment_token'=> DB::raw("concat(DATE_FORMAT(now(),'%m%d%Y%H%i%s'),concat(LEFT(MD5(NOW()), 5)),'$add_token')"),
            'date' => $date,
            'transaction_date' => $aDate,
            'id_member' => $loan_details->id_member,
            'total_loan_payment' => $total_payment,
            'swiping_amount' => $total_payment,
            'total_fees' => 0,
            'total_penalty' => 0,
            'total_payment' => $total_payment,
            'change' => 0,
            'repayment_type' =>3,
            'transaction_type'=>1,
            'pay_on_id_loan'=>$id_loan,
            'email_sent'=>2
        ];

        DB::table('repayment_transaction')
        ->insert($repayment_transaction);

        $id_repayment_transaction = DB::table('repayment_transaction')->where('repayment_type',3)->max('id_repayment_transaction');

        for($i=0;$i<count($repayment_loans);$i++){
            $repayment_loans[$i]['id_repayment_transaction'] = $id_repayment_transaction;
        }

        DB::table('repayment_loans')
        ->insert($repayment_loans);

        //UPDATE LOAN OFFSET TABLE
        $to_update = DB::select("SELECT rt.pay_on_id_loan,rl.id_loan,SUM(paid_principal) as p_principal,SUM(paid_interest) as p_interest,SUM(paid_fees) as p_fees,SUM(paid_surcharge) as p_surcharge FROM repayment_transaction as rt
            LEFT JOIN repayment_loans as rl on rl.id_repayment_transaction = rt.id_repayment_transaction
            WHERE repayment_type = 3 and (pay_on_id_loan = ?) and rt.status <> 10
            GROUP BY rl.id_loan;",[$id_loan]);
            // return $to_update;

        foreach($to_update as $tu){
            DB::table('loan_offset')
            ->where('id_loan',$tu->pay_on_id_loan)
            ->where('id_loan_to_pay',$tu->id_loan)
            ->update([
                'p_principal'=>$tu->p_principal,
                'p_interest'=>$tu->p_interest,
                'p_fees'=>$tu->p_fees,
                'p_surcharge'=>$tu->p_surcharge
            ]);
        }

        DB::table('loan_table')
        ->whereIn('id_loan',$id_loan_paid)
        ->update(['is_paid'=>DB::raw("CASE
        WHEN ifnull(getLoanTotalTermPayment(id_loan,term_code),0) = 0 THEN 0
        WHEN (total_due-ifnull(getLoanTotalTermPayment(id_loan,term_code),0)) =0 THEN 1
        ELSE 2 END")]);
        //UPDATE LOAN TABLE

            //UPDATE LOAN
        for($i=0;$i<count($id_loan_paid);$i++){
            $count_not_paid = DB::table('loan')
            ->leftJoin('loan_table as lt','lt.id_loan','loan.id_loan')
            ->where('lt.id_loan',$id_loan_paid[$i])
            ->whereIn('is_paid',[0,2])
            ->count();
                if($count_not_paid == 0){ // All repayment is paid (UPDATE TO CLOSE STATUS)
                    DB::table('loan')
                    ->where('id_loan',$id_loan_paid[$i])
                    ->where('loan_status',1)
                    ->where('status',3)
                    ->update([
                        'status'=>6,
                        'loan_status' => 2,
                        'paid_on_loan'=>$id_loan
                    ]);
                }else{
                    // DB::table('loan')
                    // ->where('id_loan',$id_loan_paid[$i])
                    // ->update([
                    //     'status'=>3,
                    //     'loan_status' => 1
                    // ]);
                    $int_q = env("RENEWAL_INTEREST_FULL_CONTRACT")?" if('$date' > loan.maturity_date,'$date',loan.maturity_date)":"'$date'";
                    $balance_as_of = DB::table('loan')
                                     ->select(DB::raw("getLoanBalanceAsOf(id_loan,$int_q) as bal"))
                                     ->where('id_loan',$id_loan_paid[$i])
                                     ->first();
                                     
                    if(isset($balance_as_of) && $balance_as_of->bal <= 0){
                        //close the loan if the principal and current interest and fees are paid
                        DB::table('loan')
                        ->where('id_loan',$id_loan_paid[$i])
                        ->where('loan_status',1)
                        ->where('status',3)
                        ->update([
                            'status'=>6,
                            'loan_status' => 2,
                            'paid_on_loan'=>$id_loan
                        ]);
                    }else{
                        DB::table('loan')
                        ->where('id_loan',$id_loan_paid[$i])
                        ->update([
                            'status'=>3,
                            'loan_status' => 1
                        ]);
                    }
                }
            }
            return $id_repayment_transaction;
        }
    }

    public static function calculate($amount,$due){
        if($amount >= $due){
            $p_amount = $due;
        }elseif($amount < $due && $amount > 0){
            $p_amount =$amount;
        }else{
            $p_amount = 0;
        } 
        return ROUND($p_amount,2);
    }

    public static function PrimeData($ls_prime,$id_member,$principal_amount){
        // dd($ls_prime);
        $cbu_total = Member::getCBU($id_member);
        $output['is_prime_applied'] = true;
        $output['prime_error'] = array();


        $offset_cbu = false;


        $output['offset_cbu'] = $offset_cbu;

        // if($cbu_total < $ls_prime->prime_min_cbu){
        //     array_push($output['prime_error'],"Minimum CBU not met (Minimum CBU: ".number_format($ls_prime->prime_min_cbu,2)." || Current CBU: ".number_format($cbu_total,2).")");

        //     $output['is_prime_applied'] = false;
        // }

        if($principal_amount < $ls_prime->prime_min_loan){
            array_push($output['prime_error'],"Minimum Loan Amount must be ".number_format($ls_prime->prime_min_loan,2));
            $output['is_prime_applied'] = false;
        }
        $prime_amount = 0;
        if($output['is_prime_applied']){
            $output['offset_cbu'] = true;
            if($ls_prime->prime_calc == 1){
                // Percentage
                $prime_amount = round($principal_amount*($ls_prime->prime_val/100));
                $prime_calc_des = "($ls_prime->prime_val%)";

                $prime_calc_des='';
            }else{
                $prime_amount = round($ls_prime->prime_val);
                $prime_calc_des='';
            }
        }
        $cbu_amt = 0;
        if($cbu_total < $ls_prime->prime_min_cbu){
            $f = env('IF_CBU_BELOW_PERCENTAGE')/100;
            $cbu_amt = ROUND($prime_amount*$f,2);
            $prime_amount = ROUND($prime_amount - $cbu_amt);
        }else{
            $f = env('IF_CBU_OVER_PERCENTAGE')/100;
            $cbu_amt = ROUND($prime_amount*0.2,2);
            $prime_amount = ROUND($prime_amount - $cbu_amt);            
        }
        // dd($cbu_amt,$prime_amount);
        $output['prime_amount'] = $prime_amount;
        $output['cbu_amount'] = $cbu_amt;



        if($output['prime_amount'] > 0){
            $c = [
                'charge_complete_details' => "Prime Amount $prime_calc_des",
                'calculated_charge' => $output['prime_amount']
            ];
            // $prime_deduction = [
            //     'id_loan_fees' => config('variables.prime'),
            //     'id_fee_calculation' => $ls_prime->prime_calc,
            //     'value' => $ls_prime->prime_val,
            //     'is_deduct' => 1,
            //     'application_fee_type' => 1,
            //     'calculated_charge'=> $output['prime_amount'],
            //     'is_loan_charges'=> 1
            // ];
            $prime_deduction = [
                'id_loan_fees' => config('variables.prime'),
                'id_fee_calculation' => 2,
                'value' => $output['prime_amount'],
                'is_deduct' => 1,
                'application_fee_type' => 1,
                'calculated_charge'=> $output['prime_amount'],
                'is_loan_charges'=> 1
            ];
            $output['OTHER_DEDUCTIONS'] =$prime_deduction;
            $output['DEDUCTED_CHARGES_DISP'] = $c;
        }

        if($output['cbu_amount'] > 0){
            $c = [
                'charge_complete_details' => "CBU Amount",
                'calculated_charge' => $output['cbu_amount']
            ];
            $cbu_deduction = [
                'id_loan_fees' => config('variables.cbu'),
                'id_fee_calculation' => 2,
                'value' => $output['cbu_amount'],
                'is_deduct' => 1,
                'application_fee_type' => 1,
                'calculated_charge'=> $output['cbu_amount'],
                'is_loan_charges'=> 1
            ];

            $output['CBU_DEDUCTION'] =$cbu_deduction;
            $output['DEDUCTED_CHARGES_DISP_CBU'] = $c;
        }
        // dd($output);
        return $output;

        // if($ls_prime->prime_min_cbu)
    }

    public static function ParseTermConditionDetails($id_terms_condition,$cbu_amount){
        $tc = DB::table('terms_conditions_details as tcd')
                              ->select(DB::raw("tcd.*,if(min_principal=max_principal,concat('₱',FORMAT(min_principal,2)),concat('₱',FORMAT(min_principal,2),' - ₱',FORMAT(max_principal,2))) as amount_range"))
                              ->where('id_terms_condition',$id_terms_condition)
                              ->whereRaw("$cbu_amount >= min_cbu")
                              ->whereRaw("$cbu_amount <= if(max_cbu=0,99999999999999999999999999,max_cbu)")
                              // ->where('max_cbu','<=',$cbu_amount)
                              ->first();
        

        return $tc;
    }

    public static function ParseTermCondition($id_member){

    }

    public static function LoanOverallBalance($id_loan,$id_repayment_transaction){
        $param = array();

        $imp_q = implode(',',array_fill(0,count($id_loan),'?'));


        $param = $id_loan;

        array_push($param,$id_repayment_transaction);
        
        $out= DB::select("SELECT k.id_loan,
                        principal_total-SUM(ifnull(paid_principal,0)) as principal_balance,
                        interest_total-SUM(ifnull(paid_interest,0)) as interest_balance,
                        surcharge_total-SUM(ifnull(paid_surcharge,0)) as surcharge_balance,
                        loan_total - SUM(ifnull(paid_principal,0)+ifnull(paid_interest,0)+ifnull(paid_surcharge,0)) as loan_balance 
        FROM (
        SELECT loan.id_loan,SUM(repayment_amount) as principal_total,SUM(interest_amount) as interest_total,SUM(surcharge) as surcharge_total,
        SUM(repayment_amount+interest_amount+surcharge) as loan_total FROM loan
        LEFT JOIN loan_table as lt on lt.id_loan = loan.id_loan
        WHERE loan.id_loan in ($imp_q)
        GROUP BY loan.id_loan) as k
        LEFT JOIN repayment_loans as rl on rl.id_loan =k.id_loan AND rl.status <> 10 AND rl.id_repayment_transaction <> ?
        GROUP BY k.id_loan;",$param);

        // dd($out);
        return $out;
    }

}