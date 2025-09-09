<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\MySession;
use PDF;
use App\FSModel;
use App\CredentialModel;

use Excel;
use App\Exports\FSExport;

class FinancialStatementController extends Controller
{
    public function index($types,Request $request){


        if($types == "index"){
            $gen_type = 1;
        }elseif($types == "comparative"){
            $gen_type = 2;
            $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),"/financial_statement/$types");
            if(!$data['credential']->is_view){
                return redirect('/redirect/error')->with('message', "privilege_access_invalid");
            }
        }

        if($request->financial_report_type  <= 2){
            $data = FSModel::parseData($gen_type,$request);

            $data['act_fs'] = true;
        }else{
            $c = new Request([
                'year'=>$request->year_end
            ]);
            
            if($request->financial_report_type == 3){
                $cash_flow = new CashFlowController();
       
                $data = $cash_flow->parseData($c);



                $data['financial_report_type'] = $request->financial_report_type ?? 1;    
                $data['view'] = "cash_flow.table"  ;          

       
            }else{
                $changes_equity = new ChangesEquityController();

                $data = $changes_equity->parseData($c);
                $data['financial_report_type'] = $request->financial_report_type ?? 1;  


                $data['view'] = "changes_equity.table"  ;                     
            }

            $data['act_fs'] = false;
        }

        

        $data['head_title'] = $data['mod_title'];
        $data['gen_type'] = $gen_type;
        // dd($data);
       

        return view('financial_statement.index',$data);
    }

    public function request_test(Request $request){
        return $request->TEST;
    }

    public function parseData($gen_type,Request $request){

        $data['financial_report_type'] = $request->financial_report_type ?? 1; // 1 balance sheet; 2 income statement
        $data['type'] = $type = $request->type ?? 1;

        $data['multiple_date'] = false;
        $data['last_month_year'] = false;

        $data['comparative_type'] = $request->comparative_type ?? 0;

        if($gen_type == 2){
            $data['multiple_date'] = true;
            if($data['comparative_type'] == 1){
                $data['last_month_year'] = true;
            }else{
                $data['multiple_date'] = true;
                $date_request = array(
                    array('month_start'=>6,'month_end'=>6,'year'=>2022),
                    array('month_start'=>8,'month_end'=>8,'year'=>2022),
                );
            }
        }
         
        $data['current_month'] = date("n", strtotime(MySession::current_date()));
        $data['current_year'] = date("Y", strtotime(MySession::current_date()));
        $data['date'] = 12333;

        // 1 = monthly; 2 - yearly
        if($type == 1){
            $data['start_month'] = $start = ($request->month_start ?? $data['current_month']);
            $data['end_month'] = $end =  $request->month_end ?? $data['current_month'];
            
            if($gen_type == 2){ // if comparative
                $data['start_month'] = $start =$end;
            }
            $data['year'] = $year = $request->year ?? $data['current_year'];
        }else{
            $data['start_year'] =$start= $request->year_start ?? $data['current_year'];
            $data['end_year'] =$end= $request->year_end ?? $data['current_year'];
        }

        if(!$data['multiple_date'] || $data['last_month_year'] ){
            $dates_out = $this->decodeDates($data,$type,$start,$end,$year??'');

            // return $dates_out;

            if($data['last_month_year']){
                $dates_out = $this->decodeDates($data,$type,$start,$end,$year??'');
            
                // $fil_start = $dates_out['fil_start_date'];
                // $fil_end = $dates_out['fil_end_date'];
                $fil_start = array();
                $fil_end = array();

                $last_month_date = date('Y-m-t', strtotime('-31 days', strtotime($dates_out['fil_end_date'])));
                $current_date = $dates_out['fil_end_date'];
                $last_year_date = date('Y-m-d', strtotime('-1 year', strtotime($dates_out['fil_end_date'])));

                $last_month_alias =  date("F Y", strtotime($last_month_date));
                $current_alias =  date("F Y", strtotime($current_date));
                $last_year_alias =  date("F Y", strtotime($last_year_date));

                if($data['financial_report_type'] == 1){ // Balance Sheet
                    $fil_start = [date("Y-m-01", strtotime($current_date))];
                    $fil_end = [$current_date]; 

                    $dep_filter = "(adm.month = MONTH('$current_date') AND adm.year = YEAR('$current_date')) OR (adm.month = MONTH('$last_month_date') AND adm.year = YEAR('$last_month_date')) OR (adm.month = MONTH('$last_year_date') AND adm.year = YEAR('$last_year_date'))";


                }else{
                    $fil_start = [date("Y-m-01", strtotime($current_date)),date("Y-m-01", strtotime($last_month_date)),date("Y-m-01", strtotime($last_year_date))];
                    $fil_end = [$current_date,$last_month_date,$last_year_date];                   
                }


                
            }else{
                foreach($dates_out as $key=>$val){
                    $data[$key] = $val;
                }
                $fil_start = $data['fil_start_date'];
                $fil_end = $data['fil_end_date'];  
                // return $fil_start;

            }
        }else{
            $fil_start = array();
            $fil_end = array();
            $col_construct =[];

            foreach($date_request as $dt){

                $dates_out = $this->decodeDates($data,$type,$dt['month_start'],$dt['month_end'],$dt['year']);
            }


        }
        // return $data;
        $data['allocations'] = DB::table('chart_account')
                              ->select(DB::raw("id_chart_account,concat(account_code,' | ',description) as description,percentage"))
                              ->whereIn('id_chart_account',[20,19,30,31,32,33])
                              ->get();
  
        if(!isset($request->type)){

            $data['financial_statement'] = [];
            $data['financial_statement']['data'] = [];
            $data['show_no_record'] = false; 


        }else{
       
            if($data['multiple_date']){
                if($data['last_month_year']){
                    $col = "SUM(if(normal=1,(debit-credit),(credit-debit)))*if(id_chart_account_subtype is null OR id_chart_account_subtype = 1,1,-1) as Amount";

                    $col = "SUM(CASE WHEN ".($data['financial_report_type']==2?"fs.date >='".date("Y-m-01", strtotime($current_date))."' AND ":"")."fs.date <= '$current_date' THEN if(fs.normal=1,(debit-credit),(credit-debit)) ELSE 0 END)*if(id_chart_account_subtype is null OR id_chart_account_subtype = 1,1,-1) as '$current_alias',";

                    $col .="SUM(CASE WHEN ".($data['financial_report_type']==2?"fs.date >='".date("Y-m-01", strtotime($last_month_date))."' AND ":"")." fs.date <= '$last_month_date' THEN if(fs.normal=1,(debit-credit),(credit-debit)) ELSE 0 END)*if(id_chart_account_subtype is null OR id_chart_account_subtype = 1,1,-1) as '$last_month_alias',";
                    $col .="SUM(CASE WHEN ".($data['financial_report_type']==2?"fs.date >='".date("Y-m-01", strtotime($last_year_date))."' AND ":"")." fs.date <= '$last_year_date' THEN if(fs.normal=1,(debit-credit),(credit-debit)) ELSE 0 END)*if(id_chart_account_subtype is null OR id_chart_account_subtype = 1,1,-1) as '$last_year_alias'";



                }else{
                    $col = $this->column_constructor($type,$start,$end,$data['financial_report_type']);
                }
                
            }else{


                $col = "SUM(if(normal=1,(debit-credit),(credit-debit)))*if(id_chart_account_subtype is null OR id_chart_account_subtype = 1,1,-1) as Amount";
            }
 
            $data['financial_statement']= $this->getData($col,$fil_start,$fil_end,$data['financial_report_type'],$data['multiple_date']);

            return $data['financial_statement'];    
            $data['show_no_record'] = true; 
        }       
        return $data; 
    }

    public function decodeDates($data,$date_fil_type,$start,$end,$year){
        if($date_fil_type == 1){
            $output['fil_start_date'] = date("Y-m-01", strtotime("$year-$start-01"));
            $output['fil_end_date'] = date("Y-m-t", strtotime("$year-$end-01"));

            if($start == $end){
                 $output['date'] = date("F Y", strtotime("$year-$start-01"));
            }else{
                  $output['date'] = date("F", strtotime("$year-$start-01"))." to ".$data['date'] = date("F Y", strtotime("$year-$end-01"));
            }
        }else{
            $output['fil_start_date'] = date("Y-m-01", strtotime("$start-01-01"));
            $output['fil_end_date'] = date("Y-m-t", strtotime("$end-12-31"));  

            if($start == $end){
                 $output['date'] = $start;
            }else{
                  $output['date'] = $start." to ".$end;
            }
        }
        return $output;        
    }

    public function column_constructor($type,$start,$end,$label,$financial_report_type){
        $month_list = [
            1=>"January",
            2=>"February",
            3=>"March",
            4=>"April",
            5=>"May",
            6=>"June",
            7=>"July",
            8=>"August",
            9=>"September",
            10=>"October",
            11=>"November",
            12=>"December"
        ];

        $imp_month = array();
        for($i=$start;$i<=$end;$i++){
            if($type == 1){
                $temp =  "SUM(CASE WHEN fs.MONTH = $i THEN if(fs.normal=1,(debit-credit),(credit-debit)) ELSE 0 END)*if(id_chart_account_subtype is null OR id_chart_account_subtype = 1,1,-1) as '".$month_list[$i]."'";
            }else{
                $temp =  "SUM(CASE WHEN fs.YEAR = $i THEN if(fs.normal=1,(debit-credit),(credit-debit)) ELSE 0 END)**if(id_chart_account_subtype is null OR id_chart_account_subtype = 1,1,-1) as '$i'";
            }
            array_push($imp_month,$temp);
        }

        return implode(",",$imp_month);   
    }

    public function getData($col,$start_date,$end_date,$financial_report_type,$multiple_date,$dep_filter = null){
       $date_filter = $this->date_filter_const($financial_report_type,$start_date,$end_date,$multiple_date);
       // return $date_filter;


       $dep_chart = ($financial_report_type ==1)?"if(ai.id_chart_account=9,10,11)":"if(ai.id_chart_account=9,65,63)";
       $fin = DB::select("SELECT Account,type,
        $col
        ,line
        FROM (
        /*************JV***************/
        SELECT cas.id_chart_account_subtype,ca.account_code,ca.normal,concat(ca.account_code,' | ',ca.description)  as Account,cal.description as line,cat.description as type,jv.date,jvd.id_chart_account,credit,debit,YEAR(jv.date) as year,MONTH(jv.date) as month,concat('JV# ',jv.id_journal_voucher) as reference
        FROM journal_voucher as jv
        LEFT JOIN journal_voucher_details as jvd on jvd.id_journal_voucher = jv.id_journal_voucher
        LEFT JOIN chart_account as ca on ca.id_chart_account = jvd.id_chart_account
        LEFT JOIN chart_account_line_item as cal on cal.id_chart_account_line_item = ca.id_chart_account_line_item   
        LEFT JOIN chart_account_type as cat on cat.id_chart_account_type = ca.id_chart_account_type         
        WHERE jv.status <> 10 and cat.report_type = ? AND (".$date_filter['jv'].")
        UNION ALL
        /*************CV***************/
        SELECT ca.id_chart_account_subtype,ca.account_code,ca.normal,concat(ca.account_code,' | ',ca.description)  as Account,cal.description as line,cat.description as type,cv.date,cvd.id_chart_account,credit,debit,YEAR(cv.date) as year,MONTH(cv.date) as month,concat('CDV# ',cv.id_cash_disbursement) as reference
        FROM cash_disbursement as cv
        LEFT JOIN cash_disbursement_details as cvd on cvd.id_cash_disbursement = cv.id_cash_disbursement
        LEFT JOIN chart_account as ca on ca.id_chart_account = cvd.id_chart_account
        LEFT JOIN chart_account_line_item as cal on cal.id_chart_account_line_item = ca.id_chart_account_line_item
        LEFT JOIN chart_account_type as cat on cat.id_chart_account_type = ca.id_chart_account_type         
        WHERE cv.status <> 10 and cat.report_type = ? AND (".$date_filter['cv'].")
        UNION ALL
        /*************CRV***************/
        SELECT ca.id_chart_account_subtype,ca.account_code,ca.normal,concat(ca.account_code,' | ',ca.description)  as Account,cal.description as line,cat.description as type,crv.date,crvd.id_chart_account,credit,debit,YEAR(crv.date) as year,MONTH(crv.date) as month,concat('CRV# ',crv.id_cash_receipt_voucher) as reference
        FROM cash_receipt_voucher as crv
        LEFT JOIN cash_receipt_voucher_details as crvd on crvd.id_cash_receipt_voucher = crv.id_cash_receipt_voucher
        LEFT JOIN chart_account as ca on ca.id_chart_account = crvd.id_chart_account
        LEFT JOIN chart_account_line_item as cal on cal.id_chart_account_line_item = ca.id_chart_account_line_item
        LEFT JOIN chart_account_type as cat on cat.id_chart_account_type = ca.id_chart_account_type         
        WHERE crv.status <> 10 and cat.report_type = ? AND (".$date_filter['crv'].")
        UNION ALL
        /*************BEGINNING***************/
        SELECT ca.id_chart_account_subtype,ca.account_code,ca.normal,concat(ca.account_code,' | ',ca.description)  as Account,cal.description as line,cat.description as type,date,chart_beginning.id_chart_account,credit,debit,YEAR(date) as year,MONTH(date) as month,concat('BEG # ',chart_beginning.id_chart_beginning) as reference
        FROM chart_beginning 
        LEFT JOIN chart_account as ca on ca.id_chart_account = chart_beginning.id_chart_account
        LEFT JOIN chart_account_line_item as cal on cal.id_chart_account_line_item = ca.id_chart_account_line_item
        LEFT JOIN chart_account_type as cat on cat.id_chart_account_type = ca.id_chart_account_type         
        WHERE cat.report_type = ? AND (".$date_filter['chart_beginning'].")
        UNION ALL
        /*************DEPRECIATION*******************/
        select 
        ca.id_chart_account_subtype,ca.account_code,ca.normal,concat(ca.account_code,' | ',ca.description)  as Account,cal.description as line,cat.description as type,
        LAST_DAY(concat(adm.year,'-',adm.month,'-','01')) as date,
        ca.id_chart_account,depreciation_amount as credit,0 as debit,adm.year,adm.month,concat('ASSET ',ai.asset_code) as reference FROM asset_item as ai
        LEFT JOIN asset as a on a.id_asset= ai.id_asset
        LEFT JOIN asset_depreciation_month as adm on adm.id_asset = ai.id_asset
        LEFT JOIN chart_account as ca on ca.id_chart_account = if(ai.id_chart_account=9,10,11)
        LEFT JOIN chart_account_line_item as cal on cal.id_chart_account_line_item = ca.id_chart_account_line_item   
        LEFT JOIN chart_account_type as cat on cat.id_chart_account_type = ca.id_chart_account_type
        WHERE a.status <> 10 and ai.disposed  = 0 and fully_disposal_date is null and cat.report_type =1

        and (".($dep_filter ?? $date_filter['asset_depreciation']).") and cat.report_type = ?
        UNION ALL
        /*************FILL***************/
        SELECT ca.id_chart_account_subtype,ca.account_code,ca.normal,concat(ca.account_code,' | ',ca.description)  as Account,cal.description as line,cat.description as type,curdate(),ca.id_chart_account,0,0,YEAR(curdate()) as year,MONTH(curdate()) as month,'fill' as reference
        FROM chart_account as ca
        LEFT JOIN chart_account_line_item as cal on cal.id_chart_account_line_item = ca.id_chart_account_line_item
        LEFT JOIN chart_account_type as cat on cat.id_chart_account_type = ca.id_chart_account_type  
        WHERE ca.ac_active=1 AND cat.report_type = ?

        ) as fs
        GROUP BY fs.id_chart_account
        ORDER BY fs.account_code;",[$financial_report_type,$financial_report_type,$financial_report_type,$financial_report_type,$financial_report_type,$financial_report_type]);

       $g = new GroupArrayController();

       $out['headers'] = array();
       $field_exclude = ['line','type','type','id_chart_account','category'];

       if(count($fin) > 0){
            foreach($fin[0] as $head=>$items){
                if(!(in_array($head,$field_exclude))){
                    array_push($out['headers'],$head);
                }
           }       
       }

       $out['data'] = $g->array_group_by($fin,['type','line']);

       return $out;

    }

    public function date_filter_const($financial_report_type,$start_date,$end_date,$multiple_date){

        // return $start_date;
        if($financial_report_type == 1){ //BALANCE SHEET (AS OF)
            if(!$multiple_date){
                $out['jv'] = "jv.date <= '$end_date'";
                $out['cv'] = "cv.date <= '$end_date'";
                $out['crv'] = "crv.date <= '$end_date'";
                $out['chart_beginning'] = "chart_beginning.date <= '$end_date'";
                // $out['asset_depreciation'] = "adm.month <= MONTH('$end_date') OR adm.year <= YEAR('$end_date')";

                $out['asset_depreciation'] = "LAST_DAY(concat(adm.year,'-',adm.month,'-','01')) <= '$end_date'";

                
            }else{
                $out['jv'] = '';
                $out['cv'] = '';
                $out['crv'] = '';
                $out['chart_beginning'] = '';
                for($i=0;$i<count($start_date);$i++){
                    $out['jv'] .= "(jv.date <= '$end_date[$i]')". (($i != count($start_date)-1)?" OR ":"");
                    $out['cv'] .= "(cv.date <= '$end_date[$i]')". (($i != count($start_date)-1)?" OR ":"");
                    $out['crv'] .= "(crv.date <= '$end_date[$i]')". (($i != count($start_date)-1)?" OR ":"");
                    $out['chart_beginning'] .= "(chart_beginning.date <= '$end_date[$i]')". (($i != count($start_date)-1)?" OR ":"");


                    // $out['asset_depreciation'] = "adm.month <= MONTH('$end_date[$i]') OR adm.year <= YEAR('$end_date[$i]')". (($i != count($start_date)-1)?" OR ":"");
                    $out['asset_depreciation'] = "LAST_DAY(concat(adm.year,'-',adm.month,'-','01')) <= '$end_date[$i]'". (($i != count($start_date)-1)?" OR ":"");
                }             
            }
        }else{ //INCOME STATEMENT
            if(!$multiple_date){
                $out['jv'] = "jv.date >= '$start_date' AND jv.date <= '$end_date'";
                $out['cv'] = "cv.date >= '$start_date' AND cv.date <= '$end_date'";
                $out['crv'] = "crv.date >= '$start_date' AND crv.date <= '$end_date'";
                $out['chart_beginning'] = "chart_beginning.date >= '$start_date' AND chart_beginning.date <= '$end_date'";

                // RETURN $end_date;

                $out['asset_depreciation'] = "LAST_DAY(concat(adm.year,'-',adm.month,'-','01')) <= '$end_date'";

            }else{
                $out['jv'] = '';
                $out['cv'] = '';
                $out['crv'] = '';
                $out['chart_beginning'] = '';
                for($i=0;$i<count($start_date);$i++){
                    $out['jv'] .= "(jv.date >= '$start_date[$i]' AND jv.date <= '$end_date[$i]')". (($i != count($start_date)-1)?" OR ":"");
                    $out['cv'] .= "(cv.date >= '$start_date[$i]' AND cv.date <= '$end_date[$i]')". (($i != count($start_date)-1)?" OR ":"");
                    $out['crv'] .= "(crv.date >= '$start_date[$i]' AND crv.date <= '$end_date[$i]')". (($i != count($start_date)-1)?" OR ":"");
                    $out['chart_beginning'] .= "(chart_beginning.date >= '$start_date[$i]' AND chart_beginning.date <= '$end_date[$i]')". (($i != count($start_date)-1)?" OR ":"");
                }
                return $out;
            }           
        }

        return $out;
    }
    public function export($types,Request $request){
        if($types == "index"){
            $gen_type = 1;
        }elseif($types == "comparative"){
            $gen_type = 2;
        }
        $data = FSModel::parseData($gen_type,$request);
        // $data = $this->parseData($gen_type,$request);

        $data['gen_type'] = $gen_type;




        $html =  view('financial_statement.export',$data);
        $data['file_name'] = $data['mod_title']." - ".$data['file_text'];

        if($data['export_type'] == 2){
            return Excel::download(new FSExport($data,1), $data['file_name'].".xlsx");
        }
        
        // $html = preg_replace('/>\s+</', "><", $html);

        // return $html;
        $pdf = PDF::loadHtml($html);
        $pdf->setOption("encoding","UTF-8");
        // $pdf->setOption('margin-bottom', '0.7480in');
        // $pdf->setOption('margin-top', '0.7480in');
        // $pdf->setOption('margin-bottom', '0.1in');
        // $pdf->setOption('margin-top', '0.25in');
        $pdf->setOption('margin-bottom', '0.35in');
        $pdf->setOption('margin-top', '0.35in');
        $pdf->setOption('margin-right', '0.33in');
        $pdf->setOption('margin-left', '0.33in');
        $pdf->setOption('header-left', 'Page [page] of [toPage]');
    
        $pdf->setOption('header-font-size', 8);
        $pdf->setOption('header-font-name', 'Calibri');

        $pdf->setOption('page-width','215.9mm');
        $pdf->setOption('page-height','330.2mm');

        // $pdf->setPaper('Legal');

        if(count($data['financial_statement']['headers'])-2 > 8){
            $pdf->setOrientation('landscape');
        }
        

        return $pdf->stream();




        return $data;
    }
}
