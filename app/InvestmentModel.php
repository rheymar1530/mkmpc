<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\MySession;
use Carbon\Carbon;

class InvestmentModel extends Model
{
    public static function ComputeInvestment($id_member,$id_investment_product,$id_investment_product_terms,$id_investment,$amount,$with_table=true,$renewal=false){
        $output['STATUS'] = "SUCCESS";
        $output['WITH_TABLE'] = $with_table;


      

        // dd($output);
        if($id_investment == 0){ 
            $data['SHOW_MONTH_COL'] = true;
            $output['INVESTMENT_PROD']=$investment_prod = DB::table('investment_product as ip')
            ->select(DB::raw("ip.product_name,ip.id_investment_product,ip.min_amount,ip.max_amount,ip.id_interest_type,ip.id_interest_period,iitm.interest_rate,iitm.terms as terms,iit.description as interest_type,iip.description as interest_period,iwp.description as withdrawable_part,ip.id_withdrawable_part,ip.withdraw_before_maturity,ip.member_only,concat(iitm.terms,' ',iip.unit) as duration_text,iip.unit,iitm.id_investment_product_terms,ip.withdrawal_fee,ip.monthly_fee,ip.yearly_fee,ip.one_time"))
            ->leftJoin('inv_interest_type as iit','iit.id_interest_type','ip.id_interest_type')
            ->leftJoin('inv_interest_period as iip','iip.id_interest_period','ip.id_interest_period')
            ->leftJoin('inv_withdrawable_part as iwp','iwp.id_withdrawable_part','ip.id_withdrawable_part')
            ->leftJoin('investment_product_terms as iitm','iitm.id_investment_product','ip.id_investment_product')
            ->where('ip.id_investment_product',$id_investment_product)
            ->where('iitm.id_investment_product_terms',$id_investment_product_terms)
            ->first();
     
            // $output['FEES'] = self::fees($investment_prod);

            $output['INVESTMENT_AMOUNT'] = $amount;

            $output['FEES'] = DB::select("SELECT  ift.id_fee_type,ift.description as fee_name,ipf.value,ipf.id_fee_type,ipf.id_fee_calculation,ipf.id_calculated_fee_base,
            @calculated:=CASE 
            WHEN ipf.id_fee_calculation=2 THEN ipf.value
            WHEN ipf.id_calculated_fee_base = 1 THEN ROUND(? * (ipf.value/100),2)
            ELSE 0 END  as amount,
            CASE
            WHEN ipf.id_fee_calculation = 2 THEN ''
            ELSE concat(ipf.value,'% of ',cf.description)
            END as fee_description
            FROM investment_product_fees as ipf
            LEFT JOIN inv_fee_type as ift on ift.id_fee_type = ipf.id_fee_type
            LEFT JOIN calculated_fee_base as cf on cf.id_calculated_fee_base = ipf.id_calculated_fee_base
            WHERE ipf.id_investment_product = ?;",[$amount,$id_investment_product]);


            // dd($output['FEES']);


            // $output['FEES'] = DB::table("investment_product_fees as ipf")
            //                   ->select(DB::raw("ift.description as fee,ipf.amount"))
            //                   ->leftJoin('inv_fee_type as ift','ift.id_fee_type','ipf.id_fee_type')
            //                   ->where('id_investment_product',$id_investment_product)
            //                   ->where('amount','>',0)
            //                   ->get();

            $output['amt_range'] = "₱".number_format($investment_prod->min_amount,2)." - ₱".number_format($investment_prod->max_amount,2);
            if(!$output['WITH_TABLE']){
                return $output;
            }

            $investment_date = MySession::current_date();
            
            if(!$renewal){
                if($amount < $investment_prod->min_amount || $amount > $investment_prod->max_amount){
                    $output['STATUS'] = "ERROR";
                    $output['message'] = "Investment Amount must be within ".$output['amt_range'];
                    $output['WITH_TABLE'] = false;

                    return $output;
                }

                $member_type = DB::table('member')->select('memb_type')->where('id_member',$id_member)->first()->memb_type ?? 0;

                // dd($member_type);
                // $member_type = 0;
                // $member_only = 0;
                if($investment_prod->member_only == 1 && $member_type == 0){
                    $output['STATUS'] = "ERROR";
                    $output['message'] = "This Investment Product is for member only";
                    $output['WITH_TABLE'] = false;

                    return $output;
                }
            }
            // dd($member_only);



            $total_end_interest = ROUND($amount*($investment_prod->interest_rate/100),2);
            // Investment Table and Other Details
            if($investment_prod->id_interest_type == 1){
                // simple fixed interest
                $output['INTEREST_AMOUNT'] = ROUND($amount * ($investment_prod->interest_rate/100),2);
                // $output['INTEREST_AMOUNT'] = ROUND($total_end_interest/$investment_prod->terms,2);
            }


            switch ($investment_prod->id_interest_period){
                case 1: //Monthly
                $var = 1;
                break;
                case 2: //Quarterly
                $var = 3;
                break;
                default:
                $var = 12;
                break;
            }
            // $loop_count = $investment_prod->terms/$var;
            $loop_count = $investment_prod->terms;
            $end_interest = 0 ;
            $investment_table = array();
            $c_inv_amount = $amount;
            
            $output['SHOW_DATE'] = false;



            // dd($output['INVESTMENT_PROD']);
            for($i=1;$i<=$loop_count;$i++){

                $temp = array();

                $investment_date = $temp['date'] =Carbon::parse($investment_date)->addMonths($var)->format('Y-m-d');
                $temp['date'] = $investment_date;
                if($investment_prod->id_interest_type == 1){ // simple
                    // if($i==$loop_count){
                    //     $temp['interest_amount'] = $total_end_interest-$end_interest;
                    //     $temp['end_interest'] = $total_end_interest;
                    //     $temp['end_amount'] = $amount+$temp['end_interest'];    
                    // }else{
                    //     $end_interest += $output['INTEREST_AMOUNT'];
                    //     $temp['interest_amount'] = $output['INTEREST_AMOUNT'];
                    //     $temp['end_interest'] = $end_interest;
                    //     $temp['end_amount'] = $amount+$temp['end_interest'];                        
                    // }

                    $end_interest += $output['INTEREST_AMOUNT'];
                    $temp['interest_amount'] = $output['INTEREST_AMOUNT'];
                    $temp['end_interest'] = $end_interest;
                    $temp['end_amount'] = $amount+$temp['end_interest'];

                }else{ //compounded
                    $interest_amount =  ROUND($c_inv_amount * ($investment_prod->interest_rate/100),2);
                    $c_inv_amount = $c_inv_amount+$interest_amount;
                    $end_interest += $interest_amount;

                    $temp['interest_amount'] = ROUND($interest_amount,2);
                    $temp['end_interest'] = ROUND($end_interest,2);
                    $temp['end_amount'] = ROUND($c_inv_amount,2);
                }
                array_push($investment_table,$temp);
            }

            $output['INVESTMENT_TABLE'] = $investment_table;
        }else{

            $data['SHOW_MONTH_COL'] = false;
            $output['INVESTMENT_PROD']=$investment_prod = DB::table('investment as i')
            ->select(DB::raw("ip.product_name,i.id_investment_product,ip.min_amount,ip.max_amount,i.id_interest_type,i.id_interest_period,i.interest_rate,i.terms,iit.description as interest_type,iip.description as interest_period,iwp.description as withdrawable_part,i.id_withdrawable_part,i.withdraw_before_maturity,i.member_only,i.amount,concat(i.terms,' ',iip.unit) as duration_text,iip.unit,DATE_FORMAT(i.investment_date,'%m/%d/%Y') as investment_date,DATE_FORMAT(i.maturity_date,'%m/%d/%Y') as maturity_date,i.status,i.withdrawal_fee,i.monthly_fee,i.yearly_fee,i.one_time,i.date_closed"))
            ->leftJoin('investment_product as ip','ip.id_investment_product','i.id_investment_product')
            ->leftJoin('inv_interest_type as iit','iit.id_interest_type','i.id_interest_type')
            ->leftJoin('inv_interest_period as iip','iip.id_interest_period','i.id_interest_period')
            ->leftJoin('inv_withdrawable_part as iwp','iwp.id_withdrawable_part','i.id_withdrawable_part')
            ->where('i.id_investment',$id_investment)
            ->first();



            $output['SHOW_DATE'] = ($investment_prod->status >=3 && $investment_prod->status <=4)?false:true;
            $output['INVESTMENT_AMOUNT'] = $investment_prod->amount;

            $investment_date = MySession::current_date();

            // $output['FEES'] = DB::table('investment_fee as if')
            //                   ->select(DB::raw("ift.description as fee,if.amount"))
            //                   ->leftJoin('inv_fee_type as ift','ift.id_fee_type','if.id_fee_type')
            //                   ->where('if.id_investment',$id_investment)
            //                   ->where('if.amount','>',0)
            //                   ->get();
            if($investment_prod->id_interest_type == 1){
                // simple fixed interest
                $output['INTEREST_AMOUNT'] = ROUND($output['INVESTMENT_AMOUNT'] * ($investment_prod->interest_rate/100),2);
            }
            $output['INVESTMENT_TABLE'] = json_decode(json_encode(DB::table('investment_table')
                                          ->select(DB::raw("date,interest_amount,end_interest,end_amount"))
                                          ->where('id_investment',$id_investment)
                                          ->where('date','<=',($investment_prod->status==5)?$investment_prod->date_closed:MySession::current_date())
                                          ->get()),true);
            $output['FEES'] = DB::select("SELECT ift.id_fee_type,ift.description as fee_name,ipf.value,ipf.id_fee_type,ipf.id_fee_calculation,ipf.id_calculated_fee_base,
            @calculated:=ipf.amount as amount,
            CASE
            WHEN ipf.id_fee_calculation = 2 THEN ''
            ELSE concat(ipf.value,'% of ',cf.description)
            END as fee_description

            FROM investment_fee as ipf
            LEFT JOIN inv_fee_type as ift on ift.id_fee_type = ipf.id_fee_type
            LEFT JOIN calculated_fee_base as cf on cf.id_calculated_fee_base = ipf.id_calculated_fee_base
            WHERE ipf.id_investment = ?;",[$id_investment]);

        }


        // $output['FEES'] = self::fees($investment_prod);
        return $output;
    }

    public static function fees($investment_prod){
        $g = new Http\Controllers\InvestmentProductController();
        $fee_keys = $g->fee_types();
        $f_fees = array();
        foreach($fee_keys as $key=>$description){
            if($investment_prod->{$key} > 0){
                $f_fees[$description] = $investment_prod->{$key};
            }
        }    

        return $f_fees;    
    }
}
