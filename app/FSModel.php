<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use DB;
use App\MySession;
use App\Http\Controllers\GroupArrayController;
class FSModel extends Model
{
    public static function NetSurplusScript($date){
        $sql="

        /*********NET SURPLUS*************/
        UNION ALL
        SELECT 
        ca.id_chart_account_subtype,ca.account_code,ca.normal,concat(ca.description)  as Account,cal.description as line,cat.description as type,
        LAST_DAY(concat(g.year,'-',g.month,'-01')) as date,ca.id_chart_account,SUM(if(g.normal=1,debit-credit,credit-debit)*if(g.id_chart_account_subtype=2,-1,1) * if(g.id_chart_account_type=4,1,-1)) as credit,0 as debit,YEAR(g.date) as year,MONTH(g.date) as month,'NET ' as reference
        FROM (
        SELECT ca.normal,ca.id_chart_account_subtype,ca.id_chart_account_type,jv.date,credit,debit,YEAR(jv.date) as year,MONTH(jv.date) as month,ca.id_chart_account
        FROM journal_voucher as jv
        LEFT JOIN journal_voucher_details as jvd on jvd.id_journal_voucher = jv.id_journal_voucher
        LEFT JOIN chart_account as ca on ca.id_chart_account = jvd.id_chart_account
        LEFT JOIN chart_account_type as cat on cat.id_chart_account_type = ca.id_chart_account_type         
        WHERE jv.status <> 10 and cat.report_type = 2 AND jv.date <= '$date'
        UNION ALL
        /*************CV***************/
        SELECT ca.normal,ca.id_chart_account_subtype,ca.id_chart_account_type,cv.date,credit,debit,YEAR(cv.date) as year,MONTH(cv.date) as month,ca.id_chart_account
        FROM cash_disbursement as cv
        LEFT JOIN cash_disbursement_details as cvd on cvd.id_cash_disbursement = cv.id_cash_disbursement
        LEFT JOIN chart_account as ca on ca.id_chart_account = cvd.id_chart_account
        LEFT JOIN chart_account_type as cat on cat.id_chart_account_type = ca.id_chart_account_type         
        WHERE cv.status <> 10 and cat.report_type = 2 AND cv.date <= '$date'
        UNION ALL
        /*************CRV***************/
        SELECT ca.normal,ca.id_chart_account_subtype,ca.id_chart_account_type,crv.date,credit,debit,YEAR(crv.date) as year,MONTH(crv.date) as month,ca.id_chart_account
        FROM cash_receipt_voucher as crv
        LEFT JOIN cash_receipt_voucher_details as crvd on crvd.id_cash_receipt_voucher = crv.id_cash_receipt_voucher
        LEFT JOIN chart_account as ca on ca.id_chart_account = crvd.id_chart_account
        LEFT JOIN chart_account_type as cat on cat.id_chart_account_type = ca.id_chart_account_type         
        WHERE crv.status <> 10 and cat.report_type = 2 AND crv.date <= '$date'
       
        /***************DEPRECIATION***************/
        /*******************
         UNION ALL
        select 
        ca.normal,ca.id_chart_account_subtype,ca.id_chart_account_type,LAST_DAY(concat(adm.year,'-',adm.month,'-','01')) as date,0 as credit,depreciation_amount as debit,YEAR(LAST_DAY(concat(adm.year,'-',adm.month,'-','01'))) as year,MONTH(LAST_DAY(concat(adm.year,'-',adm.month,'-','01'))) as month,ca.id_chart_account
        FROM asset_item as ai
        LEFT JOIN asset as a on a.id_asset= ai.id_asset
        LEFT JOIN asset_depreciation_month as adm on adm.id_asset = ai.id_asset
        LEFT JOIN chart_account as ca on ca.id_chart_account = if(ai.id_chart_account=9,65,63)
        LEFT JOIN chart_account_line_item as cal on cal.id_chart_account_line_item = ca.id_chart_account_line_item   
        LEFT JOIN chart_account_type as cat on cat.id_chart_account_type = ca.id_chart_account_type
        WHERE a.status <> 10 and ai.disposed  = 0 and fully_disposal_date is null
        and LAST_DAY(concat(adm.year,'-',adm.month,'-','01')) <= '$date' and cat.report_type = 2
        *************************/
        UNION ALL
        /*************BEGINNING***************/
        SELECT ca.normal,ca.id_chart_account_subtype,ca.id_chart_account_type,date,credit,debit,YEAR(date) as year,MONTH(date) as month,ca.id_chart_account
        FROM chart_beginning 
        LEFT JOIN chart_account as ca on ca.id_chart_account = chart_beginning.id_chart_account
        LEFT JOIN chart_account_type as cat on cat.id_chart_account_type = ca.id_chart_account_type         
        WHERE cat.report_type = 2 AND date <= '$date' and chart_beginning.status <> 10) as g
        LEFT JOIN chart_account as ca on ca.id_chart_account = 34
        LEFT JOIN chart_account_line_item as cal on cal.id_chart_account_line_item = ca.id_chart_account_line_item
        LEFT JOIN chart_account_type as cat on cat.id_chart_account_type = ca.id_chart_account_type
        GROUP BY g.year,g.month";

        return $sql;
    }

    public static function parseData($gen_type,Request $request){

       
        $data['export_type'] = $request->export_type ?? 1;
        $data['financial_report_type'] = $request->financial_report_type ?? 1; // 1 balance sheet; 2 income statement
        $data['type'] = $type = $request->type ?? 1;
        $data['comparative_type'] = $request->comparative_type ?? 1;

        $data['current_month'] = date("n", strtotime(MySession::current_date()));
        $data['current_year'] = date("Y", strtotime(MySession::current_date()));
        $data['last_month_year'] = false;
        $data['multiple_date'] = false;
        $data['date'] = MySession::current_date();

        $data['mod_title'] = ($data['financial_report_type'] == 1)?"Statement of Financial Condition":"Statement of Financial Operation";




        $cust_filter = array();

        $header_keys = array();
        $comp_header = array();

        if($data['comparative_type'] == 1){
            $data['last_month_year'] = true;
        }
        $dt_end_title = '';
        if($type == 1){ // Monthly
            $data['end_month'] = $end =  $request->month_end ?? $data['current_month'];
            $data['year'] = $year = $request->year ?? $data['current_year'];
            $dt_end_title = date("F t, Y", strtotime("$year-$end-01"));

            // $data['title_period'] = "For the period ended ".
        }elseif($type == 2){ // Yearly
            $end = 12;

            $data['end_year'] =$year= $request->year_end ?? $data['current_year'];
            $dt_end_title = "December 31, $year";
            // $data['title_period'] = "For the period ended Year ".$year;
        }

        if($data['financial_report_type'] == 1){ // balance sheet
            $date_fil_start = '';
            $date_fil_end = date("Y-m-t", strtotime("$year-$end-01"));
            $data['title_period'] = "As of $dt_end_title";

        }else{
            $date_fil_start = date("Y-01-01", strtotime("$year-$end-01"));
            $date_fil_end = date("Y-m-t", strtotime("$year-$end-01"));
            $data['title_period'] = "For the year ended $dt_end_title";
        }

        //Construct Column
        if($data['last_month_year']){
            $last_month_date = date('Y-m-t', strtotime('-31 days', strtotime($date_fil_end)));


            $last_year_date = date('Y-m-d', strtotime('-1 year', strtotime($date_fil_end)));
            $start_last_year =date("Y-01-01", strtotime($last_year_date));

            //ALIAS
            if($type == 1){
                $header_keys['A'] = $current_alias =  date("F", strtotime($date_fil_end));
                $header_keys['B'] =$last_month_alias =  date("F", strtotime($last_month_date));                
                $header_keys['C'] =  date("F", strtotime($last_year_date));    


                $comp_header = [date("Y", strtotime($date_fil_end)),date("Y", strtotime($last_month_date)),date("Y", strtotime($last_year_date))];


            }else{
                $header_keys['A'] =$current_alias =  date("Y", strtotime($date_fil_end));
                $header_keys['C'] = $last_year_alias =  date("Y", strtotime($last_year_date)); 

                $comp_header = [$current_alias,$last_year_alias];
            }


            $col = "SUM(CASE WHEN ".($data['financial_report_type']==2?"fs.date >='".$date_fil_start."' AND ":"")."fs.date <= '$date_fil_end' THEN if(fs.normal=1,(debit-credit),(credit-debit)) ELSE 0 END)*if(fs.id_chart_account_subtype=2,-1,1) as 'A',";

            if($type == 1){
                $col .="SUM(CASE WHEN ".($data['financial_report_type']==2?"fs.date >='".$date_fil_start."' AND ":"")." fs.date <= '$last_month_date' THEN if(fs.normal=1,(debit-credit),(credit-debit)) ELSE 0 END)*if(fs.id_chart_account_subtype=2,-1,1) as 'B',";   
            }

            
            $col .="SUM(CASE WHEN ".($data['financial_report_type']==2?"fs.date >='".$start_last_year."' AND ":"")." fs.date <= '$last_year_date' THEN if(fs.normal=1,(debit-credit),(credit-debit)) ELSE 0 END)*if(fs.id_chart_account_subtype=2,-1,1) as 'C'";

           
            if($data['financial_report_type'] == 2){
                $cust_filter['jv'] = "OR (jv.date >='$start_last_year' AND jv.date <= '$last_year_date')";
                $cust_filter['cv'] = "OR (cv.date >='$start_last_year' AND cv.date <= '$last_year_date')";
                $cust_filter['crv'] = "OR (crv.date >='$start_last_year' AND crv.date <= '$last_year_date')";
                $cust_filter['chart_beginning'] = "OR (chart_beginning.date >='$start_last_year' AND chart_beginning.date <= '$last_year_date')";
                $cust_filter['asset_depreciation'] = "OR (LAST_DAY(concat(adm.year,'-',adm.month,'-','01')) >= '$start_last_year' AND LAST_DAY(concat(adm.year,'-',adm.month,'-','01')) <= '$last_year_date')";

       
            }

            if($type == 1){ // Monthly
                // $data['comparative_description'] = "(With comparative figures for the period ended ".date('F t, Y', strtotime('-31 days', strtotime($date_fil_end)))." and ".date('F t, Y', strtotime('-1 year', strtotime($date_fil_end))).")";
                  $data['comparative_description'] = "(With comparative figures for the year ended ".date('F t, Y', strtotime('-31 days', strtotime($date_fil_end)))." and ".date('F t, Y', strtotime('-1 year', strtotime($date_fil_end))).")";
            }else{
                $data['comparative_description'] = "(With comparative figures for the year ended December 31, ".($year-1).")";
            }

        }else{

            $col = "SUM(if(normal=1,(debit-credit),(credit-debit)))*if(fs.id_chart_account_subtype=2,-1,1) as Amount";

            if($data['type'] == 1){ // monthly

            }
        }

        if(!isset($request->type)){

            $data['financial_statement'] = [];
            $data['financial_statement']['data'] = [];
            $data['show_no_record'] = false; 


        }else{

            $data['financial_statement']= self::getData($col,$date_fil_start,$date_fil_end,$data['financial_report_type'],$cust_filter);
        }
           $data['allocations'] = DB::table('chart_account')
                              ->select(DB::raw("id_chart_account,concat(description) as description,percentage"))
                              ->whereIn('id_chart_account',[20,19,30,31,32,33,87])
                              ->get();
        $data['header_keys'] = $header_keys;
        $data['comp_header'] = $comp_header;

        $data['file_text'] = ($data['type'] == 1)?date("F Y", strtotime($data['year']."-".$data['end_month']."-01")):$data['end_year'];
        return $data;
    }

    public static function getData($col,$start_date,$end_date,$financial_report_type,$cust_filter=array()){
        $date_filter = self::date_filter_const($financial_report_type,$start_date,$end_date);
        $dep_chart = ($financial_report_type ==1)?"if(ai.id_chart_account=9,10,11)":"if(ai.id_chart_account=9,65,63)";

       $fin = DB::select("SELECT fs.id_chart_account,cat.name as category,Account,type,
        $col
        ,line
        FROM (
        /*************JV***************/
        SELECT ca.id_chart_account_subtype,ca.account_code,ca.normal,concat(ca.description)  as Account,cal.description as line,cat.description as type,jv.date,jvd.id_chart_account,credit,debit,YEAR(jv.date) as year,MONTH(jv.date) as month,concat('JV# ',jv.id_journal_voucher) as reference
        FROM journal_voucher as jv
        LEFT JOIN journal_voucher_details as jvd on jvd.id_journal_voucher = jv.id_journal_voucher
        LEFT JOIN chart_account as ca on ca.id_chart_account = jvd.id_chart_account
        LEFT JOIN chart_account_line_item as cal on cal.id_chart_account_line_item = ca.id_chart_account_line_item   
        LEFT JOIN chart_account_type as cat on cat.id_chart_account_type = ca.id_chart_account_type         
        WHERE jv.status <> 10 and cat.report_type = ? AND ((".$date_filter['jv'].")".($cust_filter['jv'] ?? '').")
        UNION ALL
        /*************CV***************/
        SELECT ca.id_chart_account_subtype,ca.account_code,ca.normal,concat(ca.description)  as Account,cal.description as line,cat.description as type,cv.date,cvd.id_chart_account,credit,debit,YEAR(cv.date) as year,MONTH(cv.date) as month,concat('CDV# ',cv.id_cash_disbursement) as reference
        FROM cash_disbursement as cv
        LEFT JOIN cash_disbursement_details as cvd on cvd.id_cash_disbursement = cv.id_cash_disbursement
        LEFT JOIN chart_account as ca on ca.id_chart_account = cvd.id_chart_account
        LEFT JOIN chart_account_line_item as cal on cal.id_chart_account_line_item = ca.id_chart_account_line_item
        LEFT JOIN chart_account_type as cat on cat.id_chart_account_type = ca.id_chart_account_type       
        WHERE cv.status <> 10 and cat.report_type = ? AND ((".$date_filter['cv'].") ".($cust_filter['cv'] ?? '')." )
        UNION ALL
        /*************CRV***************/
        SELECT ca.id_chart_account_subtype,ca.account_code,ca.normal,concat(ca.description)  as Account,cal.description as line,cat.description as type,crv.date,crvd.id_chart_account,credit,debit,YEAR(crv.date) as year,MONTH(crv.date) as month,concat('CRV# ',crv.id_cash_receipt_voucher) as reference
        FROM cash_receipt_voucher as crv
        LEFT JOIN cash_receipt_voucher_details as crvd on crvd.id_cash_receipt_voucher = crv.id_cash_receipt_voucher
        LEFT JOIN chart_account as ca on ca.id_chart_account = crvd.id_chart_account
        LEFT JOIN chart_account_line_item as cal on cal.id_chart_account_line_item = ca.id_chart_account_line_item
        LEFT JOIN chart_account_type as cat on cat.id_chart_account_type = ca.id_chart_account_type         
        WHERE crv.status <> 10 and cat.report_type = ? AND ((".$date_filter['crv'].") ".($cust_filter['crv'] ?? '').")
        UNION ALL
        /*************BEGINNING***************/
        SELECT ca.id_chart_account_subtype,ca.account_code,ca.normal,concat(ca.description)  as Account,cal.description as line,cat.description as type,date,chart_beginning.id_chart_account,credit,debit,YEAR(date) as year,MONTH(date) as month,concat('BEG # ',chart_beginning.id_chart_beginning) as reference
        FROM chart_beginning 
        LEFT JOIN chart_account as ca on ca.id_chart_account = chart_beginning.id_chart_account
        LEFT JOIN chart_account_line_item as cal on cal.id_chart_account_line_item = ca.id_chart_account_line_item
        LEFT JOIN chart_account_type as cat on cat.id_chart_account_type = ca.id_chart_account_type         
        WHERE chart_beginning.status <> 10 AND cat.report_type = ? AND ((".$date_filter['chart_beginning'].") ".($cust_filter['chart_beginning'] ?? '').")
        
        /*************DEPRECIATION*******************/
        /*******************
        UNION ALL
        select 
        ca.id_chart_account_subtype,ca.account_code,ca.normal,concat(ca.description)  as Account,cal.description as line,cat.description as type,
        LAST_DAY(concat(adm.year,'-',adm.month,'-','01')) as date,
        ca.id_chart_account,if(cat.report_type=1,depreciation_amount,0) as credit,if(cat.report_type=2,depreciation_amount,0) as debit,adm.year,adm.month,concat('ASSET ',ai.asset_code) as reference FROM asset_item as ai
        LEFT JOIN asset as a on a.id_asset= ai.id_asset
        LEFT JOIN asset_depreciation_month as adm on adm.id_asset = ai.id_asset
        LEFT JOIN chart_account as ca on ca.id_chart_account = $dep_chart
        LEFT JOIN chart_account_line_item as cal on cal.id_chart_account_line_item = ca.id_chart_account_line_item   
        LEFT JOIN chart_account_type as cat on cat.id_chart_account_type = ca.id_chart_account_type
        WHERE a.status <> 10 and ai.disposed  = 0 and fully_disposal_date is null
        and ((".($dep_filter ?? $date_filter['asset_depreciation']).") ".($cust_filter['asset_depreciation'] ?? '').") and cat.report_type = ?
        ****************************************/
        UNION ALL
        /*************FILL***************/
        SELECT ca.id_chart_account_subtype,ca.account_code,ca.normal,concat(ca.description)  as Account,cal.description as line,cat.description as type,curdate(),ca.id_chart_account,0,0,YEAR(curdate()) as year,MONTH(curdate()) as month,'fill' as reference
        FROM chart_account as ca
        LEFT JOIN chart_account_line_item as cal on cal.id_chart_account_line_item = ca.id_chart_account_line_item
        LEFT JOIN chart_account_type as cat on cat.id_chart_account_type = ca.id_chart_account_type  
        WHERE ca.ac_active=1 AND cat.report_type = ?".(($financial_report_type == 1)?(self::NetSurplusScript($end_date)):'')."
            
        ) as fs
        LEFT JOIN chart_account as ca on ca.id_chart_account = fs.id_chart_account
        LEFT JOIN chart_account_category as cat on cat.id_chart_account_category = ca.id_chart_account_category
        GROUP BY fs.id_chart_account
        ORDER BY fs.account_code;",[$financial_report_type,$financial_report_type,$financial_report_type,$financial_report_type,$financial_report_type,$financial_report_type]);

// LEFT JOIN chart_account as ca on ca.id_chart_account = fs.id_chart_account
// LEFT JOIN chart_account_category as cat on cat.id_chart_account_category = fs.i
   

       $g = new GroupArrayController();

       $out['headers'] = array();
       $field_exclude = ['id_chart_account','category','Account','line','type'];

       if(count($fin) > 0){
            foreach($fin[0] as $head=>$items){
                if(!(in_array($head,$field_exclude))){
                    array_push($out['headers'],$head);
                }
           }       
       }

       $fsData = $g->array_group_by($fin,['type','line']);

       if(isset($fsData['Assets'])){
           $NonCurrent = $fsData['Assets']['Non-current Assets'];
           $fsData['Assets']['Non-current Assets'] = $g->array_group_by($NonCurrent,['category']);        
       }


       // unset($fsData['Assets']['Non-current Assets']);


       $out['data'] = $fsData;
       // dd($out);

       // dd(1234);

       return $out;
        return $fin;

    }
    public static function date_filter_const($financial_report_type,$start_date,$end_date){
        if($financial_report_type == 1){ //Balance sheet
            $out['jv'] = "jv.date <= '$end_date'";
            $out['cv'] = "cv.date <= '$end_date'";
            $out['crv'] = "crv.date <= '$end_date'";
            $out['chart_beginning'] = "chart_beginning.date <= '$end_date'";
            $out['asset_depreciation'] = "LAST_DAY(concat(adm.year,'-',adm.month,'-','01')) <= '$end_date'";
        }else{
            $out['jv'] = "jv.date >='$start_date' AND jv.date <= '$end_date'";
            $out['cv'] = "cv.date >='$start_date' AND cv.date <= '$end_date'";
            $out['crv'] = "crv.date >='$start_date' AND crv.date <= '$end_date'";
            $out['chart_beginning'] = "chart_beginning.date >='$start_date' AND chart_beginning.date <= '$end_date'";
            $out['asset_depreciation'] = "LAST_DAY(concat(adm.year,'-',adm.month,'-','01')) >= '$start_date' AND LAST_DAY(concat(adm.year,'-',adm.month,'-','01')) <= '$end_date'";
        }

        return $out;
    }
}
