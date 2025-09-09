<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class AdminDashboard2Controller extends Controller
{
    public function index(){
        $data['CURRENT_DATE_RANGE'] = "October 1 - 23, 2022";
        $data['CURRENT_YEAR_LABEL'] = "January - December 2022";


        $data['MonthRevenue'] = $this->MonthRevenue(10,2022);

        $data['YearRevenue'] = $this->YearRevenue(2022);

        $data['LoanMonthly'] = $this->LoanMonthly(10,2022);

        $data['YearLoan'] = $this->LoanYear(2022);

        // return $data['LoanYear'];

        // return $data;






        return view('admin_dashboard.dashboard2',$data);
    }
    public function MonthRevenue($month,$year){

        $prev_m = $month-1;
        $prev_start_date = date("Y-m-01", strtotime("$year-$prev_m-01"));
        $cur_start_date = date("Y-m-01", strtotime("$year-$month-01"));
        $cur_end_date = date("Y-m-t", strtotime("$year-$month-01"));

        $sql = "SELECT if(MONTH(revenue.date)=$month,'CURRENT','BEG') as Month,ca.description,SUM(credit-debit) as amount FROM (
            SELECT cd.date,cd.id_cash_disbursement as ref,cdd.id_chart_account,cdd.description,cdd.debit,cdd.credit
            FROM cash_disbursement as cd
            LEFT JOIN cash_disbursement_details as cdd on cdd.id_cash_disbursement = cd.id_cash_disbursement
            LEFT JOIN chart_account as ca on ca.id_chart_account = cdd.id_chart_account
            WHERE date >= '$prev_start_date' and date <= '$cur_end_date' and status <> 10 AND ca.id_chart_account_type = 4
            UNION ALL
            SELECT jv.date,jv.id_journal_voucher,jvd.id_chart_account,jvd.description,jvd.debit,jvd.credit
            FROM journal_voucher as jv
            LEFT JOIN journal_voucher_details as jvd on jvd.id_journal_voucher = jv.id_journal_voucher
            LEFT JOIN chart_account as ca on ca.id_chart_account = jvd.id_chart_account
            WHERE date >= '$prev_start_date' and date <= '$cur_end_date' AND ca.id_chart_account_type = 4 and status <> 10
            UNION ALL
            SELECT crv.date,crv.id_cash_receipt_voucher,crvd.id_chart_account,crvd.description,crvd.debit,crvd.credit
            FROM cash_receipt_voucher as crv
            LEFT JOIN cash_receipt_voucher_details as crvd on crvd.id_cash_receipt_voucher = crv.id_cash_receipt_voucher
            LEFT JOIN chart_account as ca on ca.id_chart_account = crvd.id_chart_account
            WHERE date >= '$prev_start_date' and date <= '$cur_end_date' and status <> 10 AND ca.id_chart_account_type = 4) as revenue
        LEFT JOIN chart_account as ca on ca.id_chart_account = revenue.id_chart_account
        GROUP BY Month,if(date < '$cur_start_date ',0,revenue.id_chart_account)
        ORDER BY revenue.date;";


        $revenue  = DB::select($sql);

        $g = new GroupArrayController();

        $revenue = $g->array_group_by($revenue,['Month']);

        $current_revenue =  $revenue['CURRENT'] ?? [];

        $output = array();
        $output['chart']['labels'] = array();
        $output['total_revenue'] = 0;
        $temp_data = array();
        $temp_background = array();

        $output['revenue_list'] = array();

        foreach($current_revenue as $cr){
            array_push($output['chart']['labels'],$cr->description);

            array_push($temp_data,$cr->amount);
            array_push($temp_background,$this->rand_color());

            $temp = array();
            $output['revenue_list'][$cr->description] = $cr->amount;
            $output['total_revenue'] += $cr->amount;

        }

        $output['chart']['datasets'] = array();
        $output['chart']['datasets'][0]['data'] = $temp_data;
        $output['chart']['datasets'][0]['backgroundColor'] = $temp_background;

        $output['prev_revenue'] = $revenue['BEG'][0]->amount;

        return $output;

        return $revenue;

    }

    public function rand_color() {
        return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
    }
    public function YearRevenue($year){
        $where = "";
        if($year == 2022){ // Set 2022 beginning to Jule
            $start_date = "2022-07-01";
            // $where = " WHERE id_month > 6";
        }else{
            $start_date = "$year-01-01";
        }

        $end_date = "$year-12-31";

        $sql = "
        SELECT DATE_FORMAT(revenue.date,'%b') as Month,SUM(credit-debit) as amount FROM (
            SELECT cd.date,cdd.id_chart_account,cdd.debit,cdd.credit
            FROM cash_disbursement as cd
            LEFT JOIN cash_disbursement_details as cdd on cdd.id_cash_disbursement = cd.id_cash_disbursement
            LEFT JOIN chart_account as ca on ca.id_chart_account = cdd.id_chart_account
            WHERE date >= '$start_date' and date <= '$end_date' and status <> 10 AND ca.id_chart_account_type = 4
            UNION ALL
            SELECT jv.date,jvd.id_chart_account,jvd.debit,jvd.credit
            FROM journal_voucher as jv
            LEFT JOIN journal_voucher_details as jvd on jvd.id_journal_voucher = jv.id_journal_voucher
            LEFT JOIN chart_account as ca on ca.id_chart_account = jvd.id_chart_account
            WHERE date >= '$start_date' and date <= '$end_date' AND ca.id_chart_account_type = 4 and status <> 10
            UNION ALL
            SELECT crv.date,crvd.id_chart_account,crvd.debit,crvd.credit
            FROM cash_receipt_voucher as crv
            LEFT JOIN cash_receipt_voucher_details as crvd on crvd.id_cash_receipt_voucher = crv.id_cash_receipt_voucher
            LEFT JOIN chart_account as ca on ca.id_chart_account = crvd.id_chart_account
            WHERE date >= '$start_date' and date <= '$end_date' and status <> 10 AND ca.id_chart_account_type = 4
            UNION ALL
            SELECT concat('$year-',id_month,'-01'),0,0,0
            FROM month_desc
            $where


            ) as revenue
        LEFT JOIN chart_account as ca on ca.id_chart_account = revenue.id_chart_account
        GROUP BY Month
        ORDER BY MONTH(revenue.date)";


        $output = array();
        $output['chart']['label'] = array();
        $output['chart']['data'] = array();
        $revenue = DB::select($sql);
        foreach($revenue as $rev){
            array_push($output['chart']['label'],$rev->Month);
            array_push($output['chart']['data'],$rev->amount);
        }

        return $output;
    }
    public function LoanMonthly($month,$year){
        $prev_m = $month-1;
        $prev_start_date = date("Y-m-01", strtotime("$year-$prev_m-01"));
        $cur_start_date = date("Y-m-01", strtotime("$year-$month-01"));
        $cur_end_date = date("Y-m-t", strtotime("$year-$month-01"));

        $sql = "
        SELECT if(date_released < '$cur_start_date','PREV','CURRENT') as type,
        if(date_released < '$cur_start_date','PREV',ls.name) as ls_name,SUM(principal_amount) as total_principal,count(*) as count
            FROM loan
        LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
        WHERE date_released >= '$prev_start_date' and date_released <= '$cur_end_date'
        GROUP BY type,ls_name
        ORDER BY ls_name";


        $loan  = DB::select($sql);

        $g = new GroupArrayController();

        $loan = $g->array_group_by($loan,['type']);

        $current_loan =  $loan['CURRENT'] ?? [];

        $output = array();
        $output['chart']['labels'] = array();
        $output['total_loan'] = 0;
        $temp_data = array();
        $temp_background = array();

        $output['loan_list'] = array();

        foreach($current_loan as $cr){
            array_push($output['chart']['labels'],$cr->ls_name);

            array_push($temp_data,$cr->total_principal);
            array_push($temp_background,$this->rand_color());

            $temp = array();
            $output['loan_list'][$cr->ls_name] = $cr->total_principal;
            $output['total_loan'] += $cr->total_principal;

        }

        $output['chart']['datasets'] = array();
        $output['chart']['datasets'][0]['data'] = $temp_data;
        $output['chart']['datasets'][0]['backgroundColor'] = $temp_background;

        $output['prev_loan'] = $loan['PREV'][0]->total_principal;

        return $output;
        return DB::select($sql);


    }

    public function LoanYear($year){
        $where = "";
        if($year == 2022){ // Set 2022 beginning to Jule
            // $start_date = "2022-07-01";
            // $where = " WHERE id_month > 6";
            $start_date = "$year-01-01";
        }else{
            $start_date = "$year-01-01";
        }

        $end_date = "$year-12-31";
        $sql="
        SELECT * FROM (
            SELECT MONTH(loan.date_released) as m,DATE_FORMAT(loan.date_released,'%b') as month,principal_amount as total_principal
            FROM loan
            WHERE date_released >= '$start_date' and date_released <= '$end_date'
            GROUP BY MONTH(date_released)
            UNION ALL
            SELECT id_month,DATE_FORMAT(concat('$year-',id_month,'-01'),'%b') as dt,0
            FROM month_desc
         
            ) as loan
        group by m
        ORDER BY m";

        $loan =DB::select($sql);
        $output = array();
        $output['chart']['label'] = array();
        $output['chart']['data'] = array();
        $revenue = DB::select($sql);
        foreach($loan as $rev){
            array_push($output['chart']['label'],$rev->month);
            array_push($output['chart']['data'],$rev->total_principal);
        }

        return $output;
        
    }

    // public function 
}
