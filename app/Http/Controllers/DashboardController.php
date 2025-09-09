<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use DB;
use App\WebHelper;
use App\MySession;
use DateTime;

class DashboardController extends Controller
{
    public function dashboard(Request $request){

    	$data['message_type'] =  Session::get('message');
        // $data['sidebar'] = "sidebar-collapse";
        if(MySession::isAdmin()){
            $data['head_title'] = "Modules";
            
            if($request->dashboard==2){
                return view('dashboard_nc.dashboard',$data);
            }elseif($request->dashboard==3){
                return view('dashboard_nc2.dashboard',$data);
            }else{
                return view('dashboard.dashboard',$data);
            }
        }
        $data['head_title'] = "Dashboard";
        $loan_services = DB::select("select ls.id_loan_service,name,if(min_amount=max_amount,concat('₱',format(max_amount,2)),concat('₱',format(min_amount,2),' - ₱',format(max_amount,2))) as amount,
            if(ls.id_loan_payment_type=1,concat(t.terms,' mo',if(t.terms > 1,'s',''),'.'),getMonth(period)) as term_period,t.interest_rate,lpt.description as payment_type,lpt.id_loan_payment_type,t.terms_token,ls.avail_age as age,ls.cbu_amount,is_deduct_cbu,no_comakers,is_open_new_loaner,deduct_interest,is_multiple,if(ls.id_loan_service > 16 AND PERIOD_DIFF(EXTRACT(YEAR_MONTH FROM curdate()),EXTRACT(YEAR_MONTH FROM ls.date_created)),1,0) as new
            
            FROM loan_service as ls
            LEFT JOIN terms as t on t.id_loan_service = ls.id_loan_service
            LEFT JOIN loan_payment_type as lpt on lpt.id_loan_payment_type = ls.id_loan_payment_type
            WHERE status = 1
            ORDER BY id_loan_service DESC");


        $requirements = DB::table('loan_service_requirements as lsr')
                        ->select("lsr.*")
                        ->leftJoin('loan_service as ls','ls.id_loan_service','lsr.id_loan_service')
                        ->where('ls.status',1)
                        ->get();
        // return $requirements;
        $g = new GroupArrayController();
        $data['requirements'] =  $g->array_group_by($requirements,['id_loan_service']);

      
        $data['loan_services'] = $g->array_group_by($loan_services,['name']);
        $dt = WebHelper::ConvertDatePeriod(MySession::current_date());
        $id_member = MySession::myId();

        // $id_member = 24;

        $loans = DB::select("SELECT *,if(loan_status=0,0,(principal_amount-total_pr_paid)) as loan_balance,(principal_due+interest_due+fees_due) as current_due FROM (
            SELECT loan.loan_status,loan.id_loan,concat('','','',getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms)) as 'loan_service_name',loan.principal_amount,
            getLoanTotalPaymentType(loan.id_loan,1) as total_pr_paid,getPrincipalBalanceAsOf(loan.id_loan,'$dt') as principal_due,getInterestBalanceAsOf(loan.id_loan,'$dt') as interest_due,getFeesBalanceAsOf(loan.id_loan,'$dt') as fees_due,DATE_FORMAT(if(loan.loan_status=0,DATE(loan.date_created),loan.date_released),'%m/%d/%Y') as loan_date,loanStatus(loan.status) as loan_status_dec,loan.status as status_code,loan.interest_rate,loan.loan_token
            FROM loan
            LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
            LEFT JOIN loan_table as lt on lt.id_loan = loan.id_loan
            LEFT JOIN period on period.id_period = ls.id_term_period
            WHERE  id_member = ? and (loan.loan_status = 1)
            GROUP BY loan.id_loan
            ORDER by loan.date_released DESC) as loan;",[$id_member]);


        $data['st_year']=$start_year = date("Y-01-01",strtotime(MySession::current_date()));
        $data['end_year']=$end_year = date("Y-12-t",strtotime(MySession::current_date()));

       
        $data['pending_loans'] = DB::table('loan')
                                  ->select(DB::raw("loan.principal_amount,loan.id_loan,concat('','','',getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms)) as 'loan_service_name',loan.loan_status,loanStatus(loan.status) as loan_status_dec,loan.status as status_code,loan.loan_token,loan.interest_rate"))
                                  ->leftJoin("loan_service as ls","loan.id_loan_service","ls.id_loan_service")
                                  // ->where('loan.status','<=',2)
                                  ->where('loan.id_member',$id_member)
                                  ->whereRaw("DATE(loan.date_created) >='$start_year' AND DATE(loan.date_created) <= '$end_year'")
                                  ->orderByRaw("loan.status ASC,loan.date_created DESC")
                                  ->limit(5)
                                  ->get();
        $data['pending_loan_count'] = DB::table('loan')
                                      ->where('id_member',$id_member)
                                      ->whereRaw("DATE(loan.date_created) >='$start_year' AND DATE(loan.date_created) <= '$end_year'")
                                      ->count();

        $data['active_loan_count'] = 0;
        // $data['pending_loan_count'] = count($data['pending_loans']);

        $data['loan_dues_amount'] =0;
        $data['total_loan_balance'] =0;

        $data['loans'] = array();
        
        foreach($loans as $l){
            if($l->loan_status == 1){
                array_push($data['loans'],$l);
                $data['loan_dues_amount'] += $l->current_due;
                $data['total_loan_balance'] += $l->loan_balance;
                $data['active_loan_count']++;
            }            
        }

        date_default_timezone_set('Asia/Manila');
        $dt = new DateTime();
        $year =  $dt->format('Y');

        $data['last_year'] = $year-1;

        $data['CBU'] = $this->parseCBUMonthly(1,12,$year,$id_member);

        // return $data['CBU'];

        // return $data['CBU'];
        $data['total_cbu'] = 0;

        foreach($data['CBU'] as $cbu){
            $data['total_cbu'] += $cbu->Total;
        }

        $data['cbu_graph_label'] =array();
        $data['cbu_graph_value'] =array();
        $field_not_graph = ['Total'];
        $cc= 0; 
        // return $data['CBU'] ;

        foreach($data['CBU'][0] as $key=>$val){
            if(!in_array($key,$field_not_graph)){
                if($cc > 0){
       
                    array_push($data['cbu_graph_label'],$key);
                    array_push($data['cbu_graph_value'],$val);                   
                }else{
                    $data['beg_cbu_bal'] = $val;

                }
                $cc++;
            }
        }   

        $data['beginning_label'] = "Beginning".(($year <= 2022)?'':" (".($year-1).")");
        
        $data['payments'] = DB::select("SELECT id_repayment_transaction as reference,ifnull(or_no,'-') as or_no,if(transaction_type=1,'Cash','ATM Swipe') as transaction_type,DATE_FORMAT(transaction_date,'%m/%d/%Y') as transaction_date,total_payment,swiping_amount,`change` 
        FROM repayment_transaction 
        WHERE id_member = ? and status <> 10 and repayment_type=1 AND transaction_date >= '$start_year' AND transaction_date <= '$end_year'
        ORDER BY id_repayment_transaction DESC,transaction_date LIMIT 10;",[$id_member]);


        // return view('dashboard.carousel_test2',$data);
        return view('dashboard.dashboard_member',$data);
        
    }
    public function redirect_error(){
    	$data['message_type'] =  Session::get('message');
    	return view('dashboard.privilege_issue',$data);
    }
    public function parseCBUMonthly($start_month,$end_month,$year,$id_member){
        // $year = 2022;
        $data['selected_year'] = $year;
        $data['end_month'] = $end_month;
        $month_query= "";
        $month_query_ar = array();       

        if($year == env('BEGINNING_YEAR')){
            $start_month = env('BEGINNING_MONTH');
            // $period_start = '2022-07-10'; //2022-07-09 is the beginning of CBU
            $period_start = env('BEGINNING_YEAR')."-".env('BEGINNING_MONTH')."-".(intval(env('BEGINNING_DAY'))+1);
            // $period_start = env('BEGINNING_YEAR')."-".env('BEGINNING_MONTH')."-".(env('BEGINNING_DAY'))+1;
        }else{
            $period_start = date("Y-m-01", strtotime("$year-$start_month-01"));
        }
        
        array_push($month_query_ar,"SUM(CASE WHEN transaction_date < '$period_start' THEN amount ELSE 0 END) as '".($year-1)."'");


        for($i=$start_month;$i<=$end_month;$i++){
            if($year == env('BEGINNING_YEAR') & $i == env('BEGINNING_MONTH')){
                $dt_s = date("Y-m-10", strtotime("$year-$i-01"));
            }else{
                $dt_s = date("Y-m-01", strtotime("$year-$i-01"));
            }

            
            $dt_e = date("Y-m-t", strtotime($dt_s));
            $month_text = date("M", strtotime($dt_s));

            $q = "SUM(CASE WHEN transaction_date >= '$dt_s' AND transaction_date <= '$dt_e' THEN amount ELSE 0 END) as '$month_text'";

            array_push($month_query_ar,$q);

        }

        $dt_query_end = date("Y-m-t", strtotime("$year-$end_month-01"));
        $month_query = implode(",",$month_query_ar);


        $sql_query="SELECT
        $month_query
        ,SUM(amount)  as 'Total'   FROM (
            SELECT c.date_received as transaction_date,ifnull(amount,0) as amount,id_member 
            FROM cash_receipt_details as cd
            LEFT JOIN cash_receipt as c on c.id_cash_receipt = cd.id_cash_receipt
            LEFT JOIN tbl_payment_type as pt on pt.id_payment_type = cd.id_payment_type
            WHERE pt.is_cbu = 1 AND pt.type in (1) AND status <> 10  and c.date_received <= ? AND c.id_member = $id_member
            UNION ALL
            SELECT loan.date_released,ifnull(lc.calculated_charge,0) as amount,id_member from loan_charges as lc
            LEFT JOIN loan on loan.id_loan = lc.id_loan
            LEFT JOIN tbl_payment_type as pt on pt.reference = lc.id_loan_fees and pt.type = 2
            WHERE  pt.is_cbu = 1 AND (loan.status in (6) or loan.loan_status in (1)) and pt.type =2
            and  loan.date_released  <= ? AND loan.id_member = $id_member
            UNION ALL
            SELECT rt.transaction_date,ifnull(rf.amount,0),id_member FROM repayment_fees as rf
            LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rf.id_repayment_transaction
            LEFT JOIN tbl_payment_type as pt on pt.id_payment_type = rf.id_payment_type
            WHERE pt.is_cbu = 1 and pt.type =3 AND rt.status <> 10  AND  rt.transaction_date  <= ? AND rt.id_member = $id_member
            UNION ALL
            SELECT date,ifnull(amount,0),id_member FROM cbu_beginning where date  <= ? AND id_member = $id_member
            UNION ALL
            SELECT cd.date,(cdd.debit*-1) as amount,cd.id_member FROM cash_disbursement as cd
            LEFT JOIN cash_disbursement_details as cdd on cdd.id_cash_disbursement = cd.id_cash_disbursement
            LEFT JOIN chart_account as ca on ca.id_chart_account = cdd.id_chart_account
            WHERE ca.iscbu = 1 and cd.status <> 10 AND  cd.date  <= ? AND cd.id_member = $id_member
            /****AUTO FILLING*******/
            UNION ALL
            SELECT '$period_start' as date,0 as amount,id_member FROM member where id_member = $id_member
        ) as CBU
        LEFT JOIN member as m on m.id_member = CBU.id_member
        GROUP BY CBU.id_member;";


        $data['cbus'] = DB::select($sql_query,[$dt_query_end,$dt_query_end,$dt_query_end,$dt_query_end,$dt_query_end]);

        return $data['cbus'];
    }
}
