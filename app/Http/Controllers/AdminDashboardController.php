<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\MySession;
use App\CredentialModel;

class AdminDashboardController extends Controller
{

    public function border_width(){
        return 2;
    }
    public function index($type,Request $request){


        $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),"/admin_dashboard/$type");
        if(!$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }

        $current_month = date("n", strtotime(MySession::current_date()));
        $current_year = date("Y", strtotime(MySession::current_date()));
        $data = array();
        $data['selected_year']=$year = $request->year ?? $current_year;
        $data['selected_month']=$month= $request->month ?? $current_month;


        $start_month = 1;

        $g = new GroupArrayController();



        if($year == $current_year && $month == $current_month){
            $end_date = MySession::current_date();
        }else{
            $end_date = date("Y-m-t", strtotime("$year-$month-01"));
        }

        $current_year_start = date("Y-01-01", strtotime("$year-$month-01"));
        $current_month_start = date("Y-m-01", strtotime("$year-$month-01"));


        $last_2_year_start = date("Y-01-01", strtotime(($year-1)."-$month-01"));
        $last_5_year_start = date("Y-01-01", strtotime(($year-4)."-$month-01"));


        $yearly_label = array();
        $yearly_separator = array();
        $yearly_separator_last_2 = array();

        for($i=($year-4);$i<=$year;$i++ ){
            $t = "SUM(CASE WHEN YEAR(date) <=$i THEN if(ca.normal=1,debit-credit,credit-debit)*if(id_chart_account_subtype=2,-1,1) ELSE 0 END) as '$i'";

            array_push($yearly_separator,$t);
            array_push($yearly_label,$i);

            if($i >= $year-1){
                array_push($yearly_separator_last_2,$t);
            }
        }

        //Year Monthly Label
        $monthly_label = array();
        $monthly_separator = array();
        $monthly_separator2 = array();

        for($i=1;$i<=$month;$i++){
            $month_lbl = date("M", strtotime($year."-$i-01"));
            $month_end = date("Y-m-t", strtotime($year."-$i-01"));
            $t = "SUM(CASE WHEN Month(date)=$i THEN if(ca.normal=1,debit-credit,credit-debit)*if(id_chart_account_subtype=2,-1,1) ELSE 0 END) as '$month_lbl'";
            $t2 = "SUM(CASE WHEN date<='$month_end' THEN if(ca.normal=1,debit-credit,credit-debit)*if(id_chart_account_subtype=2,-1,1) ELSE 0 END) as '$month_lbl'";
            array_push($monthly_separator,$t);
            array_push($monthly_separator2,$t2);
            array_push($monthly_label,$month_lbl);
        }

        // return $monthly_separator2;

        // return $monthly_separator;

        $data['FILTER_MONTH'] = $monthly_label[$month-1]." $year";
        $data['FILTER_YEAR_RANGE'] = $monthly_label[$start_month-1]." - ".$monthly_label[$month-1]." $year";

        // return $data['FILTER_YEAR_RANGE'];
        $data['FILTER_YEAR_5'] = ($year-4)." - $year";
        // return $data['FILTER_YEAR_RANGE'];

        $data['year_key'] = [$year,$year-1];
        $data['sidebar'] = "sidebar-collapse";

        $data['type'] = $type;
        if($type == "revenue_expenses"){
            $data['DASHBOARD_TITLE'] =  $data['head_title'] = "Net Surplus Dashboard";
            $data['DASHBOARD_TYPE'] = 1;
           // REVENUE AND EXPENSES FOR CURRENT MONTH
            $out = $this->base_query("REVENUE_EXPENSES_MONTH",$current_month_start,$end_date);
            $out = $g->array_group_by($out,['type']);
            $data['REVENUE_CURRENT_MONTH'] = $this->populate_dataset($out['Revenues'] ?? [],'line_item'); //description



            
            $out['Expenses'] = $out['Expenses'] ?? [];
            $data['EXPENSES_CURRENT_MONTH'] = $this->populate_dataset($out['Expenses'] ?? [],'line_item');

            //REVENUE AND EXPENSES FOR CURRENT YEAR
            $out = $this->base_query("REVENUE_EXPENSES_YEAR",$current_year_start,$end_date,$monthly_separator);
            $out = $g->array_group_by($out,['type']);


            //LAST MONTH OF THE MONTH SELECTED IS DECEMBER
            if($month == 1){
                $december_data = $this->base_query("REVENUE_EXPENSES_MONTH",($year-1)."-12-01",($year-1)."-12-31");
                $dec = $g->array_group_by($december_data,['type']);

                $december['REVENUE'] = $this->sum_key($dec['Revenues'] ?? [],'amt');
                $december['EXPENSES'] = $this->sum_key($dec['Expenses'] ?? [],'amt'); 
                $december['NET_SURPLUS'] =  $december['REVENUE']- $december['EXPENSES'];      
            }
            // 

            $revenue = $out['Revenues'] ?? [];
            $expenses = $out['Expenses'] ?? [];

            $data['REVENUE_CURRENT_YEAR'] = $this->populate_multiple_dataset($monthly_label,$revenue,false,'bar');
            $rev = $data['REVENUE_CURRENT_YEAR']['datasets'][0]['data'] ?? [];


            $data['REVENUE_CURRENT_MONTH_AMOUNT'] = $rev[$month-1] ?? 0;
            // return $data['REVENUE_CURRENT_MONTH_AMOUNT'];

            $rev_prev_month = ($month == 1)?$december['REVENUE']:($rev[$month-2] ?? 0);


            $data['REVENUE_PERCENTAGE_DIFF'] =  $this->perc_difference($rev_prev_month,$data['REVENUE_CURRENT_MONTH_AMOUNT']);



            $data['REVENUE_TOTAL_YEAR'] = array_sum($rev);

            $data['EXPENSES_CURRENT_YEAR'] = $this->populate_multiple_dataset($monthly_label,$expenses,true,'bar');
            $exp_sum = array();
            
            for($i=0;$i<$month;$i++){
                $temp_sum = 0;
                foreach($data['EXPENSES_CURRENT_YEAR']['datasets'] as $e){
                    $temp_sum += $e['data'][$i];
                }
                array_push($exp_sum,$temp_sum);
            }


            $exp = $exp_sum;    

            $data['EXPENSES_CURRENT_MONTH_AMOUNT'] = $exp[$month-1];

            $exp_prev_month = ($month == 1)?$december['EXPENSES']:$exp[$month-2];
            $data['EXPENSES_PERCENTAGE_DIFF'] =  $this->perc_difference($exp_prev_month,$data['EXPENSES_CURRENT_MONTH_AMOUNT']);
            $data['EXPENSES_TOTAL_YEAR'] = array_sum($exp_sum);

            //BUDGET
            $budget = array();
            $chart_budget = DB::select("SELECT cab.month,ifnull(SUM(cab.amount),0) as amount FROM chart_account as ca
                LEFT JOIN chart_account_budget as cab on cab.id_chart_account = ca.id_chart_account
                WHERE ca.id_chart_account_type =5 and cab.year = ? and cab.month <= ? AND ca.ac_active=1
                GROUP BY cab.month;",[$data['selected_year'],$data['selected_month']]);

            $cb = $g->array_group_by($chart_budget,['month']);

            for($i=1;$i<=$data['selected_month'];$i++){
                array_push($budget,(isset($cb[$i]))?$cb[$i][0]->amount:0);
            }

            //for testing purposes
            for($i=0;$i<$month;$i++){

            }

            $budget_exp = array();
            $budget_exp['labels'] = $monthly_label;
            $actual_color = $this->rand_color();
            $budget_color = $this->rand_color();
            $budget_exp['datasets']=array(
                [
                    'label'=>"Budgeted",
                    'backgroundColor'=>$budget_color,
                    'borderColor'=>$budget_color,
                    'data'=>$budget
                ],
                [
                    'label'=>"Actual",
                    'backgroundColor'=>$actual_color,
                    'borderColor'=>$actual_color,
                    'data'=>$exp

                ]
            );

            $data['EXPENSES_BUDGET'] = $budget_exp;



            //NET SURPLUS
            $exp_sum  = $this->sum_5($monthly_label,$expenses);

            // return $exp_sum;
            $rev_sum  = $this->sum_5($monthly_label,$revenue);
            $net_surplus_montly_array = array();

            $ns_temp = 0;
            for($i=0;$i<count($exp_sum);$i++){
                $ns_temp += (ROUND($rev_sum[$i]-$exp_sum[$i],2));
                array_push($net_surplus_montly_array,$ns_temp);
            }
            $data['NET_SURPLUS_CURRENT_YEAR'] =  $this->generate_line_chart_data($net_surplus_montly_array,$monthly_label);
            $ns = $data['NET_SURPLUS_CURRENT_YEAR']['datasets'][0]['data'];
            // $data['NET_SURPLUS_CURRENT_MONTH_AMOUNT'] = $ns[$month-1];
            $data['NET_SURPLUS_CURRENT_MONTH_AMOUNT'] = $data['REVENUE_CURRENT_MONTH_AMOUNT'] - $data['EXPENSES_CURRENT_MONTH_AMOUNT'];


            // $exp_prev_month = ($month == 1)?$december['NET_SURPLUS']:$ns[$month-2];
            $net_prev = $rev_prev_month-$exp_prev_month;

            // return $net_prev;

            $data['NET_SURPLUS_PERCENTAGE_DIFF'] =  $this->perc_difference($net_prev,$data['NET_SURPLUS_CURRENT_MONTH_AMOUNT']);

            // return $ns;
            $data['NET_SURPLUS_TOTAL_YEAR'] = end($ns);

            // return $data['NET_SURPLUS_PERCENTAGE_DIFF'];

            //Net Surplus for current month
            $ns_month = array();
            $current_expenses = $exp_sum[$month-1];
            $current_revenue = $rev_sum[$month-1];

            // $current_expenses = array_sum($exp_sum);
            // $current_revenue = array_sum($rev_sum);

            $current_net = ROUND($current_revenue-$current_expenses,2);

            $ns_month['labels'] = ['Expenses','Net Surplus'];
            $ns_month['datasets'][0] = array('data'=>[$current_expenses,$current_net],'backgroundColor'=>[$this->rand_color(),$this->rand_color()]);

            $data['NET_SURPLUS_CURRENT_MONTH'] = $ns_month;

            // return $ns_month;


            $data['TOP_PRODUCT'] =  $this->parse_top_products($month,$year);



            $data['NO_LOAN_TRANSACTION'] = $this->sum_key($data['TOP_PRODUCT'],'loan_count');
            $data['NO_LOAN_TRANSACTION_PREVIOUS_PERCENTAGE'] =$this->perc_difference($this->sum_key($data['TOP_PRODUCT'],'loan_count_previous'),$data['NO_LOAN_TRANSACTION']);

            $data['TOTAL_PRINCIPAL'] = $this->sum_key($data['TOP_PRODUCT'],'current_principal');
            $data['TOTAL_PRINCIPAL_PREV_PERCENTAGE'] =$this->perc_difference($this->sum_key($data['TOP_PRODUCT'],'previous_principal'),$data['TOTAL_PRINCIPAL']);

            // return $data['TOTAL_PRINCIPAL_PREV_PERCENTAGE'];


            $data['TOP_CUSTOMER'] = $this->top_customer($current_month_start,date("Y-m-t", strtotime($current_month_start)));

            $data['LOAN_SUMMARY'] = $this->LoanSummary($current_year_start,$end_date);
            // dd($data['LOAN_SUMMARY']);

            // return  $data['TOP_CUSTOMER'] ;


            // dd($data);

        }elseif($type == "financial"){


            $data['DASHBOARD_TITLE'] =$data['head_title'] =  "Financial Analysis Dashboard";
            $data['DASHBOARD_TYPE'] = 2;
            // ASSET LIABILITIES AND EQUITY LAST 2 YEARS
            $out = $this->base_query("ASSET_LIABILITIES_EQUITY_LAST_2",$last_2_year_start,$end_date,$yearly_separator_last_2);
            $out = $g->array_group_by($out,['type']);

            $liabilities = $out['Liabilities'] ?? [];
            $assets = $out['Assets'] ?? [];
            $equity = $out['Equity'] ?? [];

            $data['ASSETS_LAST_2'] = $out['Assets'] ?? [];


            $data['LIABILITIES_LAST_2'] =  $out['Liabilities'] ?? [];
            $data['EQUITIES_LAST_2'] =  $out['Equity'] ?? [];


            $asset_2 = $this->yearly_total($assets,$year);
            $data['ASSET_CURRENT_YEAR_AMOUNT'] = $asset_2[$year];
            $data['ASSET_PERCENTAGE_DIFF'] =  $this->perc_difference($asset_2[$year-1],$data['ASSET_CURRENT_YEAR_AMOUNT']);


            $liab_2 = $this->yearly_total($liabilities,$year);
            $data['LIABILITIES_CURRENT_YEAR_AMOUNT'] = $liab_2[$year];
            $data['LIABILITIES_PERCENTAGE_DIFF'] =  $this->perc_difference($liab_2[$year-1],$data['LIABILITIES_CURRENT_YEAR_AMOUNT']);


            $equity_2 = $this->yearly_total($equity,$year);
            $data['EQUITIES_CURRENT_YEAR_AMOUNT'] = $equity_2[$year];
            $data['EQUITIES_PERCENTAGE_DIFF'] =  $this->perc_difference($equity_2[$year-1],$data['EQUITIES_CURRENT_YEAR_AMOUNT']);

            // return $data['EQUITIES_CURRENT_MONTH_AMOUNT'];

            // ASSET LIABILITIES AND EQUITY LAST 5 YEARS
            $out = $this->base_query("ASSET_LIABILITIES_EQUITY_LAST_5",$last_5_year_start,$end_date,$yearly_separator);
            $out = $g->array_group_by($out,['type']);

            $assets = $out['Assets'] ?? [];
            $equity = $out['Equity'] ?? [];
            $liabilities = $out['Liabilities'] ?? [];

            $data['LIABILITIES_LAST_5'] = $this->populate_multiple_dataset($yearly_label,$liabilities,false,'line');

            if(count($data['LIABILITIES_LAST_5']['datasets']) > 0){
                $data['TOTAL_LIABILITIES'] = end($data['LIABILITIES_LAST_5']['datasets'][0]['data']);
            }else{
                $data['TOTAL_LIABILITIES'] = 0;
            }


            $data['ASSET_LAST_5'] = $this->populate_multiple_dataset($yearly_label,$assets,false,'line');
            if(count($data['ASSET_LAST_5']['datasets']) > 0){
                $data['TOTAL_ASSET'] = end($data['ASSET_LAST_5']['datasets'][0]['data']);
            }else{
                $data['TOTAL_ASSET'] = 0;
            }
            

            // return $equity;
            //for equities
            $data['EQUITY_LAST_5'] = $this->generate_line_chart_data($this->sum_5($yearly_label,$equity),$yearly_label);
            

            if(count($data['EQUITY_LAST_5']['datasets']) > 0){
               $data['TOTAL_EQUITY'] = end($data['EQUITY_LAST_5']['datasets'][0]['data']);
           }else{
            $data['TOTAL_EQUITY'] = 0;
        }


            //CBU LAST 5 YEARS
        $cbu = $g->array_group_by($equity,['CBU'])['CBU'] ?? [];
        $data['CBU_LAST_5'] = $this->populate_multiple_dataset($yearly_label,$cbu,false,'line');

        if(count($data['CBU_LAST_5']['datasets']) > 0){
            $data['TOTAL_CBU'] = end($data['CBU_LAST_5']['datasets'][0]['data']);
        }else{
            $data['TOTAL_CBU'] = 0;
        }

        $stat = $g->array_group_by($equity,['line_item'])['Statutory Fund'] ?? [];
        $data['TOTAL_STAT_FUND'] = 0;

        foreach($stat as $s){
            $data['TOTAL_STAT_FUND'] += $s->amt;
        }

            // return  $data['TOTAL_STAT_FUND'];

            // return $stat;

        $data['STAT_FUND_CURRENT'] =  $this->populate_dataset($stat,'description');

            //CASH FLOW
        $out = $this->base_query("CASH_FLOW",env('ADJ_DATE'),$end_date,$monthly_separator2);
        // dd($out);

        $total_current = $this->sum_key($out,'last_year')+$this->sum_key($out,'current_year');
        $total_last_year = $this->sum_key($out,'last_year');
        $data['CASH_FLOW_CURRENT'] =$total_current;
        // dd($total_current);




        $data['CASH_FLOW_CURRENT_IN'] = $this->sum_key($out,'debit');
        $data['CASH_FLOW_CURRENT_OUT'] = $this->sum_key($out,'credit');

        $data['CASH_FLOW_LAST_YEAR_PERCENTAGE'] = $this->perc_difference($total_last_year,($total_current)) ;
       
        $data['CASH_FLOW_CURRENT_YEAR'] = $this->populate_multiple_dataset($monthly_label,$out,true,'line');

            //get current month on cash flow
        $temp_cf = $data['CASH_FLOW_CURRENT_YEAR']['datasets'];

        $data['TOTAL_CASH'] = 0;



        $cf_current_month = array();
        $cf_current_month['labels'] = array();
        $cf_current_month['datasets'] = array();
        $cf_current_month['datasets'][0]['data'] = array();
        $cf_current_month['datasets'][0]['borderWidth'] =$this->border_width();
        $cf_current_month['datasets'][0]['backgroundColor'] = array();
            // for($i = 0;$i<count($temp_cf);$i++){
            //     array_push($cf_current_month['labels'],$temp_cf[$i]['label']);

            //     $month_data = $temp_cf[$i]['data'][$month-1];

            //     array_push($cf_current_month['datasets'][0]['data'],$month_data);
            //     array_push($cf_current_month['datasets'][0]['backgroundColor'],$temp_cf[$i]['backgroundColor']);
            // }

        foreach($out as $c=>$cf){
            array_push($cf_current_month['labels'],$cf->description);
            array_push($cf_current_month['datasets'][0]['backgroundColor'],$temp_cf[$c]['backgroundColor']);
            array_push($cf_current_month['datasets'][0]['data'],floatval($cf->current_month));

            $data['TOTAL_CASH'] += $temp_cf[$c]['data'][$month-1];


        }

     



        $data['CASH_FLOW_CURRENT_MONTH'] =$cf_current_month;

        $data['MEMBER_TOP_CBU'] = $this->top_member_cbu($end_date);


        // $data['LOAN_RECEIVABLES'] = $this->loan_receivable(date("Y-m-t", strtotime($current_month_start)));
        $data['LOAN_RECEIVABLES'] = $this->loan_receivable(date("Y-m-01", strtotime($current_month_start)));

        $loan_receivable_pie = array();
        $loan_receivable_pie['labels'] = ['Current','Overdue'];

        $loan_receivable_pie['datasets'][0]['data'] = array();
        $loan_receivable_pie['datasets'][0]['backgroundColor'] = [$this->rand_color(),$this->rand_color()];
        $loan_receivable_pie['datasets'][0]['borderWidth'] =$this->border_width();



        $temp_over = 0;
        $temp_current = 0;
        foreach($data['LOAN_RECEIVABLES'] as $lr){
            $temp_over+= $lr->overdue;
            $temp_current+= $lr->current;
        }
        $loan_receivable_pie['datasets'][0]['data'] = [$temp_current,$temp_over];

        $data['LOAN_RECEIVABLES_CURRENT_MONTH'] = $loan_receivable_pie;


            // $cbu = new CBUController();
            // $out = $cbu->parseCBUMonthly(1,$month,$year,true)['cbus'];
            // $data['CAPITAL_SHARE'] = $this->populate_multiple_dataset($monthly_label,$out,false,'bar');


        $out = $this->base_query("CAPITAL_SHARE",$current_year_start,$end_date,$monthly_separator2);
        $data['CAPITAL_SHARE'] = $this->populate_multiple_dataset($monthly_label,$out,false,'bar');

        $data['CAPITAL_SHARE_TOTAL'] = end($data['CAPITAL_SHARE']['datasets'][0]['data']);



            // return $data;

    }else{
        abort(404);
    }







    // dd($data);

    return view('admin_dashboard.dashboard',$data);



    return $data;


}

public function yearly_total($data,$year){
    $prev = $year-1;
    $out[$year] = 0;
    $out[$prev] = 0;

    foreach($data as $item){
        $out[$year] += $item->{$year};
        $out[$prev] += $item->{$prev};
    }

    return $out;

}

public function generate_line_chart_data($data,$label){
    $temp_out = array();
    $temp_out['labels'] = $label;
    $temp_out['datasets'] = array();
    $color = $this->rand_color();
    array_push($temp_out['datasets'],array('data'=>$data,'fill'=>false,'tension'=>0,'borderColor'=>$color,'backgroundColor'=>$color));
    return $temp_out;
}

public function populate_multiple_dataset($monthly_label,$temp_var,$with_label,$chart_type){

    $output = array();
    $output['labels'] = $monthly_label;
    $output['datasets'] = array();
    $temp_final = array();
    foreach($temp_var as $t){
        $temp_array = array();
        if($with_label){
            $temp_array['label'] = $t->line_item ?? $t->description;
        }
        $color = $this->rand_color();
        $temp_array['backgroundColor'] =$color;
        $temp_array['borderColor'] =$color;
        $temp_array['data'] = array();

        foreach($monthly_label as $ml){
            array_push($temp_array['data'],floatval($t->{$ml} ?? 0));
                // array_push($temp_array['backgroundColor'],$this->rand_color());
        }
        if($chart_type == 'line'){
            $temp_array['fill'] =false;
            $temp_array['tension'] = 0;
        }
        array_push($temp_final,$temp_array);
    }

    $output['datasets'] = $temp_final;


    return $output;
}

public function populate_dataset($temp_var,$data_label_key){
        // return $data_label_key;

    $output['labels'] = array();
    $output['datasets'] = array();
        // $output['datasets']['data'] = array();
        // $output['datasets']['backgroundColor'] = array();
    $tt = array();
    $tt['data'] = array();
    $tt['backgroundColor'] = array();
    $tt['borderWidth'] =$this->border_width();
            // $tt['borderColor'] = array();


    foreach($temp_var as $t){

        $c = $this->rand_color();
        array_push($tt['data'],floatval($t->amt));
        array_push($tt['backgroundColor'],$c);
            // array_push($tt['borderColor'],str_replace("B3","BF",$c));

            // $output['datasets']['label'] = "X";
        array_push($output['labels'],$t->{$data_label_key});
    }
    array_push($output['datasets'],$tt);

    return $output;
}

public function perc_difference($previous,$current){

  
    $diff = $current - $previous;
    if($previous != 0){
        $percentage = ROUND(($diff/$previous)*100,2);
    }else{
        return 0;
    }
    return $percentage;
}

public function sum_5($yearly_label,$items){
    $output = array();

    for($k=0;$k<count($yearly_label);$k++){
        $temp_sum = 0;
        foreach($items as $item){
            $temp_sum += $item->{$yearly_label[$k]};

            $temp_sum = ROUND($temp_sum,2);
        }
        array_push($output,$temp_sum);
    }
    return $output;
}
public function sum_key($data,$key){
    $out_sum = 0;
    foreach($data as $d){
        $out_sum += $d->{$key};
    }
    return $out_sum;
}

public function base_query($query_type,$start_date,$end_date,$sep = null){
    $where_type = "";
    $where_beg = "";
    $und = false;

    if($query_type == "ASSET_LIABILITIES_EQUITY_LAST_2" || $query_type == "ASSET_LIABILITIES_EQUITY_LAST_5"){
        $where = "status <> 10 AND date >='$start_date' AND date <= '$end_date' AND cat.report_type in (1)";
        $where_type = " cat.report_type in (1)";
        $where_beg = "status <> 10 AND cat.report_type in (1) AND date >='$start_date' AND date <= '$end_date'";
        $und = true;
    }elseif($query_type == "REVENUE_EXPENSES_MONTH" || $query_type == "REVENUE_EXPENSES_YEAR"){
        $where = "status <> 10 AND date >='$start_date' AND date <= '$end_date' AND cat.report_type = 2";
        $where_type = " cat.report_type=2";
        $where_beg = "status <> 10 AND cat.report_type=2 AND cb.date >= '$start_date' AND cb.date <= '$end_date'";
    }elseif($query_type == "CASH_FLOW"){
        $where = "status <> 10 AND date >='$start_date' AND date <= '$end_date' AND ca.id_chart_account_category in (1,2)";
        $where_type = " ca.id_chart_account_category in (1,2)";
        $where_beg = "status <> 10 AND ca.id_chart_account_category in (1,2)";
    }elseif($query_type == "CAPITAL_SHARE"){
        $where = "status <> 10 AND date >='$start_date' AND date <= '$end_date' AND ca.iscbu=1";
        $where_type = " ca.iscbu=1";
        $where_beg = "status <> 10 AND ca.iscbu=1";            
    }

    $sql="
    SELECT cd.date,cat.description as type,cd.id_cash_disbursement,ca.id_chart_account,cdd.description,cdd.debit,cdd.credit
    FROM cash_disbursement as cd
    LEFT JOIN cash_disbursement_details as cdd on cdd.id_cash_disbursement = cd.id_cash_disbursement
    LEFT JOIN chart_account as ca on ca.id_chart_account = cdd.id_chart_account
    LEFT JOIN chart_account_type as cat on cat.id_chart_account_type = ca.id_chart_account_type
    WHERE $where
    UNION ALL
    SELECT jv.date,cat.description as type,jv.id_journal_voucher,ca.id_chart_account,jvd.description,jvd.debit,jvd.credit
    FROM journal_voucher as jv
    LEFT JOIN journal_voucher_details as jvd on jvd.id_journal_voucher = jv.id_journal_voucher
    LEFT JOIN chart_account as ca on ca.id_chart_account =jvd.id_chart_account
    LEFT JOIN chart_account_type as cat on cat.id_chart_account_type = ca.id_chart_account_type

    WHERE $where
    UNION ALL
    SELECT crv.date,cat.description as type,crv.id_cash_receipt_voucher,ca.id_chart_account,crvd.description,crvd.debit,crvd.credit
    FROM cash_receipt_voucher as crv
    LEFT JOIN cash_receipt_voucher_details as crvd on crvd.id_cash_receipt_voucher = crv.id_cash_receipt_voucher
    LEFT JOIN chart_account as ca on ca.id_chart_account = crvd.id_chart_account
    LEFT JOIN chart_account_type as cat on cat.id_chart_account_type = ca.id_chart_account_type
    WHERE $where
    UNION ALL
    SELECT 
    cb.date,cat.description as type,0 as ref,ca.id_chart_account,ca.description,debit,credit
    FROM chart_beginning as cb
    LEFT JOIN chart_account as ca on ca.id_chart_account = cb.id_chart_account
    LEFT JOIN chart_account_type as cat on cat.id_chart_account_type = ca.id_chart_account_type
    LEFT JOIN chart_account_line_item as cal on cal.id_chart_account_line_item = ca.id_chart_account_line_item
    WHERE  $where_beg";




    if($und){
        $sql .="UNION ALL 
        SELECT g.date as date,cat.description as type,0,ca.id_chart_account,ca.description,0 as debit,
        SUM(if(g.normal=1,debit-credit,credit-debit)*if(g.id_chart_account_subtype=2,-1,1) * if(g.id_chart_account_type=4,1,-1)) as credit
        FROM (
        SELECT ca.normal,ca.id_chart_account_subtype,ca.id_chart_account_type,jv.date,credit,debit,YEAR(jv.date) as year,MONTH(jv.date) as month,ca.id_chart_account
        FROM journal_voucher as jv
        LEFT JOIN journal_voucher_details as jvd on jvd.id_journal_voucher = jv.id_journal_voucher
        LEFT JOIN chart_account as ca on ca.id_chart_account = jvd.id_chart_account
        LEFT JOIN chart_account_type as cat on cat.id_chart_account_type = ca.id_chart_account_type         
        WHERE jv.status <> 10 and cat.report_type = 2 AND jv.date <= '$end_date'
        UNION ALL
        /*************CV***************/
        SELECT ca.normal,ca.id_chart_account_subtype,ca.id_chart_account_type,cv.date,credit,debit,YEAR(cv.date) as year,MONTH(cv.date) as month,ca.id_chart_account
        FROM cash_disbursement as cv
        LEFT JOIN cash_disbursement_details as cvd on cvd.id_cash_disbursement = cv.id_cash_disbursement
        LEFT JOIN chart_account as ca on ca.id_chart_account = cvd.id_chart_account
        LEFT JOIN chart_account_type as cat on cat.id_chart_account_type = ca.id_chart_account_type         
        WHERE cv.status <> 10 and cat.report_type = 2 AND cv.date <= '$end_date'
        UNION ALL
        /*************CRV***************/
        SELECT ca.normal,ca.id_chart_account_subtype,ca.id_chart_account_type,crv.date,credit,debit,YEAR(crv.date) as year,MONTH(crv.date) as month,ca.id_chart_account
        FROM cash_receipt_voucher as crv
        LEFT JOIN cash_receipt_voucher_details as crvd on crvd.id_cash_receipt_voucher = crv.id_cash_receipt_voucher
        LEFT JOIN chart_account as ca on ca.id_chart_account = crvd.id_chart_account
        LEFT JOIN chart_account_type as cat on cat.id_chart_account_type = ca.id_chart_account_type         
        WHERE crv.status <> 10 and cat.report_type = 2 AND crv.date <= '$end_date'

        UNION ALL
        /*************BEGINNING***************/
        SELECT ca.normal,ca.id_chart_account_subtype,ca.id_chart_account_type,date,credit,debit,YEAR(date) as year,MONTH(date) as month,ca.id_chart_account
        FROM chart_beginning 
        LEFT JOIN chart_account as ca on ca.id_chart_account = chart_beginning.id_chart_account
        LEFT JOIN chart_account_type as cat on cat.id_chart_account_type = ca.id_chart_account_type         
        WHERE cat.report_type = 2 AND date <= '$end_date' and chart_beginning.status <> 10

        ) 
        as g
        LEFT JOIN chart_account as ca on ca.id_chart_account = 34
        LEFT JOIN chart_account_type as cat on cat.id_chart_account_type = ca.id_chart_account_type
        LEFT JOIN cash_flow as cf on cf.id_cash_flow = ca.id_cash_flow
        GROUP BY g.year,g.month";
    }


        // UNION ALL
        // select curdate(),cat.description as type,0 as ref,id_chart_account,ca.description,0 as debit,0 as credit
        // FROM chart_account as ca
        // LEFT JOIN chart_account_type as cat on cat.id_chart_account_type = ca.id_chart_account_type
        // WHERE $where_type

    if($query_type == "ASSET_LIABILITIES_EQUITY_LAST_2"){
        $st = date("Y-01-01", strtotime($end_date));
        $sep = implode(" , ",$sep);
        $sql_query = "
        SELECT ca.description,k.type,
        $sep FROM (
            $sql
            ) as k
        LEFT JOIN chart_account as ca on ca.id_chart_account = k.id_chart_account
        GROUP BY ca.id_chart_account;";
    }elseif($query_type == "ASSET_LIABILITIES_EQUITY_LAST_5"){
        $sep = implode(" , ",$sep);
            // return $sep;
        $sql_query="
        SELECT if(iscbu=1,'CBU','NOT') as CBU,k.type,cal.description as line_item,ca.description,
        $sep,
        SUM(if(ca.normal=1,debit-credit,credit-debit)*if(id_chart_account_subtype=2,-1,1)) as amt FROM (
            $sql
            ) as k
        LEFT JOIN chart_account as ca on ca.id_chart_account = k.id_chart_account
        LEFT JOIN chart_account_line_item as cal on cal.id_chart_account_line_item = ca.id_chart_account_line_item
        GROUP BY k.type,if(ca.id_chart_account_type=3,if(ca.id_chart_account_line_item=6,ca.id_chart_account,ca.id_chart_account_line_item),0),ca.iscbu; ";    
    }elseif($query_type == "REVENUE_EXPENSES_MONTH" || $query_type == "REVENUE_EXPENSES_YEAR"){

        // $groupings = ($query_type == "REVENUE_EXPENSES_MONTH")?" if(ca.id_chart_account_type=4,ca.id_chart_account,cal.description)":"if(ca.id_chart_account_type=4,ca.id_chart_account_type,cal.description)";
        $groupings = ($query_type == "REVENUE_EXPENSES_MONTH")?" if(ca.id_chart_account_type=4,cal.description,cal.description)":"if(ca.id_chart_account_type=4,ca.id_chart_account_type,cal.description)";
            // SUM(if(ca.normal=1,debit-credit,credit-debit)*if(id_chart_account_subtype=2,-1,1)) as amt

        $sep = (isset($sep))?implode(" , ",$sep):"SUM(if(ca.normal=1,debit-credit,credit-debit)*if(id_chart_account_subtype=2,-1,1)) as amt";
        $sql_query = "
        SELECT k.type,k.description,$sep
        ,
        cal.description as line_item
        FROM (
            $sql
            ) as k
        LEFT JOIN chart_account as ca on ca.id_chart_account = k.id_chart_account
        LEFT JOIN chart_account_line_item as cal on cal.id_chart_account_line_item = ca.id_chart_account_line_item
        GROUP BY $groupings;
        ";
    }elseif($query_type == "CASH_FLOW"){
        $sep = implode(" , ",$sep);



        $selected_month = date("n", strtotime($end_date));
        $selected_year = date("Y", strtotime($end_date));
        $last_year = $selected_year - 1;

        $m = date("Y-m-t", strtotime($end_date));


             // SUM(CASE WHEN Month(date)=$selected_month THEN debit-credit ELSE 0 END) as current_month,
            // CASE WHEN Month(date)=$m 
            // SUM(CASE WHEN date <= '$m' THEN credit ELSE 0 END) as credit,SUM(CASE WHEN date <= '$m' THEN debit ELSE 0 END) as debit
        $sql_query="
        SELECT ca.description,
        $sep,SUM(CASE WHEN YEAR(date) = $selected_year THEN credit ELSE 0 END) as credit,SUM(CASE WHEN YEAR(date) = $selected_year THEN debit ELSE 0 END) as debit,
        SUM(CASE WHEN date<='$m' THEN debit-credit ELSE 0 END) as current_month,
        SUM(CASE WHEN YEAR(date)=$last_year THEN debit-credit ELSE 0 END) as last_year,
        SUM(CASE WHEN YEAR(date)=$selected_year THEN debit-credit ELSE 0 END) as current_year
        FROM (
            $sql
            ) as k
        LEFT JOIN chart_account as ca on ca.id_chart_account = k.id_chart_account

        GROUP BY ca.id_chart_account; ";  
        
        // dd($sql_query);
    }elseif($query_type == "CAPITAL_SHARE"){
        $sep = implode(" , ",$sep);
        $sql_query = "
        SELECT k.type,k.description,$sep
        ,
        cal.description as line_item
        FROM (
            $sql
            ) as k
        LEFT JOIN chart_account as ca on ca.id_chart_account = k.id_chart_account
        LEFT JOIN chart_account_line_item as cal on cal.id_chart_account_line_item = ca.id_chart_account_line_item;
        ";
            // return $sep;
    }


    return DB::select($sql_query);

    return $sql;
}
public function rand_color() {

    $shades = $this->color_shade();

    return $shades[rand(0,count($shades)-1)]."80";
        // return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
}


public function top_member_cbu($date,$limit=10){
    $cbu = DB::select("SELECT m.id_member,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member,SUM(amount) as amount   FROM (
        SELECT ifnull(amount,0) as amount,id_member 
        FROM cash_receipt_details as cd
        LEFT JOIN cash_receipt as c on c.id_cash_receipt = cd.id_cash_receipt
        LEFT JOIN tbl_payment_type as pt on pt.id_payment_type = cd.id_payment_type
        WHERE pt.is_cbu = 1 AND pt.type in (1) AND status <> 10  and c.date_received <= ?
        UNION ALL
        SELECT ifnull(lc.calculated_charge,0) as amount,id_member from loan_charges as lc
        LEFT JOIN loan on loan.id_loan = lc.id_loan
        LEFT JOIN tbl_payment_type as pt on pt.reference = lc.id_loan_fees and pt.type = 2
        WHERE  pt.is_cbu = 1 AND (loan.status in (6) or loan.loan_status in (1)) and pt.type =2 AND loan.status not in (4,5)
        and loan.date_released <= ?
        UNION ALL
        SELECT ifnull(rf.amount,0),id_member FROM repayment_fees as rf
        LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rf.id_repayment_transaction
        LEFT JOIN tbl_payment_type as pt on pt.id_payment_type = rf.id_payment_type
        WHERE pt.is_cbu = 1 and pt.type =3 AND rt.status <> 10 AND rt.transaction_date <= ?
        UNION ALL
        SELECT ifnull(amount,0),id_member FROM cbu_beginning where date <= ?
        UNION ALL
        SELECT (cdd.debit*-1) as amount,cd.id_member FROM cash_disbursement as cd
        LEFT JOIN cash_disbursement_details as cdd on cdd.id_cash_disbursement = cd.id_cash_disbursement
        LEFT JOIN chart_account as ca on ca.id_chart_account = cdd.id_chart_account
        WHERE ca.iscbu = 1 and cd.status <> 10 AND cd.date <= ?
        UNION ALL
        SELECT (jvd.credit-jvd.debit) as amount,jv.id_member FROM journal_voucher as jv
        LEFT JOIN journal_voucher_details as jvd on jvd.id_journal_voucher = jv.id_journal_voucher
        LEFT JOIN chart_account as ca on ca.id_chart_account = jvd.id_chart_account
        WHERE ca.iscbu = 1 and jv.status <> 10 AND jv.date <= ? AND jv.type=1
        ) as CBU
    LEFT JOIN member as m on m.id_member = CBU.id_member
    WHERE m.status = 1
    GROUP BY CBU.id_member
    ORDER BY amount DESC",[$date,$date,$date,$date,$date,$date]);

        // return $data;

    $g = new GroupArrayController();
    $d = $g->array_group_by($cbu,['amount']);

    $chunked = array_chunk($d,$limit)[0] ?? [];
    return $chunked;

    return $d;


    return  $data;
}

public function parse_top_products($month,$year){

    $current = [
        'start' => date("Y-m-01", strtotime("$year-$month-01")),
        'end' => date("Y-m-t", strtotime("$year-$month-01"))
    ];

    $pm = ($month == 1) ? 12 : $month-1;
    $py = ($month == 1)? $year-1:$year;
    $previous = [
        'start' => date("Y-m-01", strtotime("$py-$pm-01")),
        'end' => date("Y-m-t", strtotime("$py-$pm-01"))
    ];


    $sql ="SELECT ls.id_loan_service,
    ls.name as 'loan_service',
    SUM(CASE WHEN l.date_released >= '".$previous['start']."' AND l.date_released <= '".$previous['end']."' THEN principal_amount ELSE 0 END) as previous_principal,
        SUM(CASE WHEN l.date_released >= '".$current['start']."' AND l.date_released <= '".$current['end']."' THEN principal_amount ELSE 0 END) as current_principal
        ,SUM(principal_amount) as 'total_principal',
        SUM(CASE WHEN previous_loan_id > 0 AND l.date_released >= '".$current['start']."' THEN 1 ELSE 0 END) as renew
        ,SUM(CASE WHEN previous_loan_id = 0 AND l.date_released >= '".$current['start']."' THEN 1 ELSE 0 END) as new,
        SUM(CASE WHEN l.date_released >= '".$current['start']."' THEN 1 ELSE 0 END) as 'loan_count',
        SUM(CASE WHEN l.date_released < '".$current['start']."' THEN 1 ELSE 0 END) as 'loan_count_previous',
        getLoanServiceInterest(ls.id_loan_service,'".$previous['start']."','".$previous['end']."') as 'previous_interest',
        getLoanServiceInterest(ls.id_loan_service,'".$current['start']."','".$current['end']."') as 'current_interest'
        FROM loan as l
        LEFT JOIN loan_service as ls on ls.id_loan_service = l .id_loan_service
        WHERE date_released >= '".$previous['start']."' and date_released <= '".$current['end']."' AND l.status not in (4,5)
        GROUP BY l.id_loan_service
        ORDER BY current_principal DESC;";

        // dd($sql);

        $output = DB::select($sql);

        return $output;
    }

    public function loan_receivable($date){
 
        // $sql="SELECT ls.name ,
        // SUM(CASE WHEN loan.maturity_date < '$date' THEN bal ELSE 0 END) as overdue,
        // SUM(CASE WHEN loan.maturity_date >= '$date' THEN bal ELSE 0 END) as current,
        // SUM(bal) as total
        // FROM (
        //     SELECT id_loan_service,id_loan,maturity_date,getPrincipalBalanceAsOf(id_loan,'$date') as bal
        //     FROM loan
        //     WHERE loan.loan_status = 1 and loan.date_released <= '$date') as loan
        // LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
        // GROUP BY loan.id_loan_service
        // HAVING SUM(bal) > 0;";
        $sql="SELECT ls.name,
        SUM(CASE WHEN j.due_date >= ? THEN j.paid ELSE 0 END) as current,
        SUM(CASE WHEN j.due_date < ? THEN j.paid ELSE 0 END) as overdue,
        SUM(j.paid) as total FROM (
        SELECT loan.id_loan_service,loan.id_loan,lt.term_code,lt.due_date,lt.repayment_amount-ifnull(SUM(rl.paid_principal),0) as paid FROM loan
        LEFT JOIN loan_table as lt on lt.id_loan = loan.id_loan
        LEFT JOIN repayment_loans as rl on rl.id_loan = lt.id_loan AND rl.term_code = lt.term_code AND rl.status <> 10
        where loan.loan_status = 1
        GROUP BY lt.id_loan,lt.term_code) as j
        LEFT JOIN loan_service as ls on ls.id_loan_service = j.id_loan_service
        GROUP BY j.id_loan_service;";
        return DB::select($sql,[$date,$date]);
    }

    public function top_customer($start_date,$end_date,$limit=10){
        $cust = DB::select("SELECT FormatName(first_name,middle_name,last_name,suffix) as member,SUM(principal_amount) as amount FROM loan
            LEFT JOIN member as m on m.id_member = loan.id_member
            WHERE date_released >= '$start_date' and date_released <= '$end_date' and loan.status not in (4,5)
            GROUP BY m.id_member
            ORDER BY SUM(principal_amount) DESC
            ");
        $g = new GroupArrayController();
        $d = $g->array_group_by($cust,['amount']);

        $chunked = array_chunk($d,$limit)[0] ?? [];
        return $chunked;

        $output = array();

        foreach($chunked as $ch){
            foreach($ch as $c){
                array_push($output,$c);
            }
        }

        return $output;
    }

    public function color_shade(){
        return ['#0000ff','#add8e6','#b0c4de','#87cefa','#87ceeb','#6495ed','#4169e1','#1e90ff','#00bfff','#4682b4','#0000cd','#00008b','#191970','#000080','#e7feff','#bcd4e6','#abcdef','#ace5ee','#b0e0e6','#a1caf1','#a4dded','#add8e6','#b0c4de','#87cefa','#93ccea','#9bc4e2','#aec6cf','#89cff0','#8ab9f1','#77b5fe','#87ceeb','#99badd','#a2a2d0','#a2add0','#73c2fb','#80daeb','#7cb9e8','#8cbed6','#92a1cf','#6495ed','#6ca0dc','#4f86f7','#71a6d2','#779ecb','#5b92e5','#73a9c2','#4166f5','#6699cc','#6d9bc3','#72a0c1','#45b1e8','#4169e1','#778ba5','#1e90ff','#1f75fe','#318ce7','#4997d0','#5d89ba','#6082b6','#446ccf','#1c1cf0','#0fc0fc','#273be2','#417dc1','#5d8aa8','#0070ff','#007fff','#00bfff','#00ccff','#0247fe','#3f00ff','#4682b4','#545aa7','#1dacd6','#5072a7','#21abcd','#1974d2','#00aae4','#214fc6','#2a52be','#324ab2','#536895','#1c39bb','#436b95','#0892d0','#0073cf','#1560bd','#0000cd','#333399','#0d98ba','#0f52ba','#483d8b','#26619c','#0072bb','#0077be','#007fbf','#0087bd','#0070b8','#007bb8','#0095b6','#1034a6','#006db0','#0093af','#0047ab','#007ba7','#0014a8','#002fa7','#0033aa','#0038a8','#0067a5','#007aa5','#0f4d92','#00009c','#23297a','#003399','#004f98','#120a8f','#035096','#006994','#002395','#126180','#041690','#00308f','#00008b','#000f89','#191970','#002387','#08457e','#000080','#062a78','#1d2951','#00416a','#002366','#003366','#000060','#002e63','#1c2841','#003153','#002147','#000036','#000039'];
//         $shades = array(
//     "#0000FF", // Deep blue
//     "#0033FF", // Medium blue
//     "#0066FF", // Light blue
//     "#0099FF", // Sky blue
//     "#00CCFF", // Pale blue
//     "#00FFFF", // Cyan
//     "#3300FF", // Royal blue
//     "#3333FF", // Cornflower blue
//     "#3366FF", // Steel blue
//     "#3399FF", // Baby blue
//     "#33CCFF", // Powder blue
//     "#33FFFF", // Light sky blue
//     "#6600FF", // Blue violet
//     "#6633FF", // Periwinkle
//     "#6666FF", // Lavender
//     "#6699FF", // Ghost white
//     "#66CCFF", // Mint cream
//     "#66FFFF", // Azure
//     "#9900FF", // Violet
//     "#9933FF", // Electric blue
//     "#9966FF", // Dark turquoise
//     "#9999FF", // Cadet blue
//     "#99CCFF", // Alice blue
//     "#99FFFF", // Ice blue
// );

//         return $shades;
    }

    public function parse_top(Request $request){
        if($request->ajax()){
            $type = $request->type;

            $month = $request->month;
            $year = $request->year;
            $current_month_start = date("Y-m-01", strtotime("$year-$month-01"));
            $limit = $request->limit;

            // return response($current_month_start);
            if($type == 1){
                $data['OUTPUT'] =   $this->top_customer($current_month_start,date("Y-m-t", strtotime($current_month_start)),$limit);
                $data['BODY'] = 'top_customer';
            }else{
                $data['OUTPUT'] =  $this->top_member_cbu(date("Y-m-t", strtotime($current_month_start)),$limit);
                $data['BODY'] = 'top_cbu';
            }
            
            return response($data);
        }
    }
    public function LoanSummary($start_date,$end_date){
        // $end_date
        return DB::select("select ifnull(SUM(principal_amount),0) as total_principal,COUNT(*) as total_transaction 
            FROM loan 
            where loan.date_released >= ? and loan.date_released <= ? AND loan.status not in (4,5)",[$start_date,$end_date])[0];
    }
}
