<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use DateTime;
use App\CredentialModel;
use App\MySession as MySession;
use Dompdf\Dompdf;
use PDF;
use App\JVModel;
class AssetController extends Controller
{
    public function current_dt(){
        date_default_timezone_set('Asia/Manila');
        $dt = new DateTime();
        $out['month'] = $dt->format('m');
        $out['year'] = $dt->format('Y');
        return $out;
        return $dt->format('Y-m-d');
    }
    public function asset_purchase(){
        $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/asset');
        if(!$data['credential']->is_create){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
        $data['head_title'] = "Create Asset From Purchase";
        $data['type'] = 1;
        $data['module_title'] = "Asset Purchase";
        $data['opcode'] = 0;
        $data['current_date'] = MySession::current_date();
        $data['index_route'] = "asset_purchase";
        $data['allow_post'] = 1;
        $current = $this->current_dt();
        $data['val_year'] = $current['year'];
        $data['val_month'] = $current['month'];

        return view('asset.asset_form',$data);
    }
    public function asset_index(){

        $type ="asset_adjustment";
        if($type == "asset_purchase"){
            $data['type'] = 1;
            $data['module_title'] = "Asset Purchase";
        }elseif($type == "asset_adjustment"){
            $data['type'] = 2;
            $data['module_title'] = "Asset Adjustments";
        }else{
            return "ERROR";
        }
    
        $data['module_title'] = $data['head_title'] = "Asset";

        $data['route'] = $type;
        $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/asset');
        if(!$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }

        $data['assets'] = DB::table('asset as a')
        ->select(DB::raw("a.id_asset,if(a.type=1,'Asset Purchase','Asset Adjustment') as type,DATE_FORMAT(a.purchase_date,'%m/%d/%Y') as purchase_date,tb.branch_name,DATE_FORMAT(a.valuation_date,'%M %Y') as valuation_date,if(a.status=10,'Cancelled','') as status,id_cash_disbursement,id_journal_voucher,DATE_FORMAT(a.date_created,'%m/%d/%Y') as date_created"))
        ->leftJoin("tbl_branch as tb","tb.id_branch","a.id_branch")
        ->orDerby('a.id_asset','DESC')
                          // ->where('a.type',$data['type'])
        ->get();
        return view("asset.index",$data);
        return $data;
    }


    public function adjustment(){
        $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/asset');
        if(!$data['credential']->is_create){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
        $data['type'] = 2;
        $data['opcode'] = 0;
        $data['module_title'] = "Asset Adjustment";
        $data['allow_post'] = 1;
        $data['head_title'] = "Create Asset Adjustment";
        $data['index_route'] = 'asset_adjustment';


        $current = $this->current_dt();
        $data['val_year'] = $current['year'];
        $data['val_month'] = $current['month'];
        $data['charts'] = DB::table('chart_account')
        ->select('id_chart_account','account_code','description')
        ->where('id_chart_account_type',1)
        ->get();  


        $data['branches'] = DB::table('tbl_branch')->get();
        $data['current_date'] = MySession::current_date();
        return view('asset.asset_form',$data);
    }

    public function view_asset($type,$id_asset){

        // return $this->GenerateDepreciationTable(30);
        // return $this->GenerateDepreciationTable(29);
        // return $type;

        $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/asset');
        if(!$data['credential']->is_view && !$data['credential']->is_edit){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
        $g = new GroupArrayController();
        $data['asset'] = DB::table('asset as a')
        ->select(DB::raw("a.id_asset,a.purchase_date,DATE_FORMAT(a.valuation_date,'%M %Y') as valuation_date,a.id_branch,tb.branch_name,a.id_cash_disbursement,a.type,a.id_journal_voucher,a.status,a.cancellation_reason,a.val_month,a.val_year"))
        ->leftJoin('tbl_branch as tb','tb.id_branch','a.id_branch')
        ->where('a.id_asset',$id_asset)
        ->first();
                         // return $data;

        $data['allow_post'] = ($data['asset']->status == 10)?0:1;




        $data['disposed_count'] = $this->check_asset_disposed($id_asset);
        if($data['disposed_count'] > 0){
           $data['allow_post'] =0;
       }

       $beg_asset = [2,3];

       if(in_array($id_asset,$beg_asset)){
            // $data['allow_post'] = 0;
       }

       $data['head_title'] ="Asset #$id_asset";


       $data['type'] = $data['asset']->type;

        // $data['module_title'] = ($data['type'] == 1)?"Asset Purchase":"Asset Adjustments";

       $data['module_title'] = "Asset";
        // $data['index_route'] = ($data['type'] == 1)?"asset_purchase":"asset_adjustment";
       $data['index_route'] = "asset_adjustment";
       $data['opcode'] = 1;
       $data['val_year'] = $data['asset']->val_year;
       $data['val_month'] = $data['asset']->val_month;
       if($type == "view"){
        $data['view'] = 1;  
        
        $asset_item =DB::select("SELECT ai.id_asset_item,concat(ca.account_code,' - ',ca.description) as account,ai.asset_code,ai.description,ai.serial_no,ai.quantity,
            ROUND((ai.total_cost/quantity),2) as cost ,ROUND((ai.salvage_value/quantity),2) as salvage_value,ai.life,if(ai.depreciation_method=1,'Straight Line',if(ai.depreciation_method=2,'Sum of Years','Double Declining Balance')) as depreciatiton_method
            FROM asset_item as ai
            LEFT JOIN chart_account as ca on ca.id_chart_account = ai.id_chart_account
            WHERE ai.id_asset=?;",[$id_asset]);



        $data['asset_items'] = $g->array_group_by($asset_item,['account']);
        $depreciation_table = $this->parseDepreciationTable($id_asset);



        $data['depreciation_table'] = $depreciation_table['assets'];

            // return $data['depreciation_table']['108030412202240']['2022'];
        $data['depreciation_header'] = $depreciation_table['year_header'];



        return view('asset.view_asset',$data);

        return $data;
    }

    $data['branches'] = DB::table('tbl_branch')->get();
    $data['current_date'] = MySession::current_date();
    $data['charts'] = DB::table('chart_account')
    ->select('id_chart_account','account_code','description')
    ->where('id_chart_account_type',1)
    ->get();  

    $asset_items = DB::table('asset_item as asi')
    ->select('ca.id_chart_account','asi.description','asi.serial_no','asi.quantity','asi.total_cost','asi.life','asi.salvage_value','cdd.debit as amount',DB::raw("asi.asset_code,concat(ca.account_code,' - ',ca.description) as acc_description,am.salvage_percentage,am.life_span"))
    ->leftJoin('chart_account as ca','ca.id_chart_account','asi.id_chart_account')
    ->leftJoin('asset as a','a.id_asset','asi.id_asset')
    ->leftJoin('asset_maintenance as am','am.id_chart_account','ca.id_chart_account')
    ->leftJoin('cash_disbursement_details as cdd',function($join){
        $join->on('cdd.id_cash_disbursement','a.id_cash_disbursement')
        ->on('asi.id_chart_account','cdd.id_chart_account');
    })
    ->where('asi.id_asset',$id_asset)
    ->orDerby('asi.id_asset_item')
    ->get();

    $grouped_item = $g->array_group_by($asset_items,['id_chart_account']);
    $asset_maintenance = array();
    $dd = array();
    $data['id_charts'] = array();
    foreach($grouped_item as $id_chart_account=>$asset){
        $temp = array();
        $total = 0;
        $temp['items'] = array();

        foreach($asset as $items){
            array_push($temp['items'],$items);
                // $temp['items'] = $items;
            $total += $items->total_cost;
        }
        $temp['total_amount'] =($data['type'] == 1)?$asset[0]->amount:$total;
        $temp['account_description'] = $asset[0]->acc_description;

        $dd[$id_chart_account]=$temp;
        array_push($data['id_charts'],$id_chart_account);

           //asset maintanance
        $temp_asset = array();
        $temp_asset['id_chart_account'] = $id_chart_account;
        $temp_asset['life_span'] = $asset[0]->life_span;
        $temp_asset['salvage_percentage'] = $asset[0]->salvage_percentage;
        $temp_asset['amount'] = $asset[0]->amount;

        $asset_maintenance[$id_chart_account] = $temp_asset;
    }

    $data['asset_maintenance'] = $asset_maintenance;

    $data['asset_items'] = $dd;

    return view('asset.asset_form',$data);

}
public function get_cdv(Request $request){
    if($request->ajax()){
        $id_cdv = $request->id_cdv;
        $data['cdv_details'] = DB::table('cash_disbursement')
        ->select(DB::raw("id_cash_disbursement,branch_name,date"))
        ->leftJoin('tbl_branch as tb','tb.id_branch','cash_disbursement.id_branch')
        ->where('id_cash_disbursement',$id_cdv)
        ->first();


        $data['entry'] = DB::table('cash_disbursement_details as cdd')
        ->select(DB::raw("ca.id_chart_account,concat(ca.account_code,' - ',ca.description) as ca_account,cdd.debit as amount,am.life_span,am.salvage_percentage"))
        ->leftJoin('chart_account as ca','ca.id_chart_account','cdd.id_chart_account')
        ->leftJoin('asset_maintenance as am','am.id_chart_account','cdd.id_chart_account')
        ->where('cdd.id_cash_disbursement',$id_cdv)
        ->where('ca.id_chart_account_type',1)
        ->where('debit','>',0)
        ->orDerby('cdd.id_cash_disbursement_details')
        ->get();
        return response($data);
        return response($id_cdv);
    }
}
public function parseChartDetails(Request $request){
    if($request->ajax()){
        $id_chart_account = $request->id_chart_account;
        $data['entry'] = DB::table('chart_account as ca')
        ->select(DB::raw("ca.id_chart_account,concat(ca.account_code,' - ',ca.description) as ca_account,am.life_span,am.salvage_percentage"))
        ->leftJoin('asset_maintenance as am','am.id_chart_account','ca.id_chart_account')
        ->where('ca.id_chart_account_type',1)
        ->where('ca.id_chart_account',$id_chart_account)
        ->first();
        return response($data);
    }
}
public function parseCDVList(Request $request){
    $data['cdv'] = DB::table('cash_disbursement as cd')
    ->select('cd.id_cash_disbursement',DB::raw("DATE_FORMAT(cd.date,'%m/%d/%Y') as date"),'cd.payee','cd.description')
    ->leftJoin('asset as a',function($join){
        $join->on('a.id_cash_disbursement','cd.id_cash_disbursement');
        $join->on('a.status','<>',DB::raw("10"));

    })
                      // ->leftJoin('asset as a','a.id_cash_disbursement','cd.id_cash_disbursement')
    ->where('cd.type',3)
    ->whereNull('a.id_cash_disbursement')

    ->groupBy('cd.id_cash_disbursement')
    ->get();
    return $data;
}
public function check_asset_disposed($id_asset){
    $count = DB::table(DB::raw("asset_disposal_item as adi"))
    ->leftJoin('asset_disposal as ad','adi.id_asset_disposal','ad.id_asset_disposal')
    ->leftJoin('asset_item as ai','ai.asset_code','adi.asset_code')
    ->where('ad.status','<>',10)
    ->where('ai.id_asset',$id_asset)
    ->count();

    return $count;
}
public function post(Request $request){

        // return 12345;
    $data['RESPONSE_CODE'] = "SUCCESS";
    $asset_to_remove = $request->asset_to_remove ?? [];
    $opcode = $request->opcode;
    $id_asset = $request->id_asset;
    $parent_asset = $request->parent;
    $items = $request->items;
    $id_accounts = array();

    $parent_asset['valuation_date'] = $parent_asset['val_year']."-".$parent_asset['val_month']."-01";

    $item_asset = array();

    $no_item = array();


    if($opcode == 1){
            // CHECK IF THERE IS RECORD ON ASSET DISPOSAL


        if($this->check_asset_disposed($id_asset) > 0){
            $response['RESPONSE_CODE'] = "ERROR";
            $response['message'] = "Asset has already record on asset disposal";

            return response($response);
        }



    }




    $invalid_inputs = array();
    foreach($items as $key=>$item){
        $asset_key = str_replace("acc_","",$key);
        $item_asset[$asset_key] = $item;
        if($item == "NO_ITEMS"){
            array_push($no_item,$asset_key);
        }else{
            array_push($id_accounts,str_replace("acc_","",$key));
            $validator = $this->validate_item($item);
            if(count($validator) > 0){

                $invalid_inputs[$asset_key] = $validator;
            }
        }

    }

    if(count($invalid_inputs) > 0){
        $data['RESPONSE_CODE'] = "ERROR";
        $data['message'] = "Missing Mandatory Fields";
        $data['invalid_inputs'] = $invalid_inputs;

        return response($data);
    }



    if(count($no_item) > 0){
        $data['RESPONSE_CODE'] = "ERROR";
        $data['message'] = "No Selected Item";
        $data['asset_id'] = $no_item;

        return response($data);
        return $no_item;
    }

    if($opcode == 1){
        $previous_asset = DB::table('asset')->where('id_asset',$id_asset)->first();
    }


        if($parent_asset['type'] == 1){ //CDV
            $cdv_info = $this->parseCDVDetails($parent_asset['id_cash_disbursement']);
            $parent_asset['id_branch'] = $cdv_info['details']->id_branch;
            $parent_asset['purchase_date'] = $cdv_info['details']->date;

            //entry validation
            $entry = $cdv_info['entries'];

            //VALIDATE ITEMS
            foreach($item_asset as $id_chart_account=>$items){
                $temp_total = 0;
                foreach($items as $item){
                  $temp_total += $item['total_cost'];
              }
              $entry_amount =  $entry[$id_chart_account][0]->amount;
              if($temp_total != $entry_amount){
                $data['RESPONSE_CODE'] = "ERROR";
                $data['message'] = "Amount not balance. Please check your asset item amount";
                return response($data);
            }
        }
    }else{
        $entry = $this->parseAccounts($id_accounts)['entries'];
    }

        //POPULATE ASSET ITEM FOR SQL OBJECT
    $item_sql_object = array();
    $total_amount_asset = 0;
    foreach($item_asset as $id_chart_account=>$asset){
        for($j=0;$j<count($asset);$j++){
            $asset[$j]['id_chart_account'] =$id_chart_account;
            $asset[$j]['account_code'] = $entry[$id_chart_account][0]->account_code;
            $asset[$j]['unit_cost'] = ROUND($asset[$j]['total_cost']/$asset[$j]['quantity'],2);
            $asset[$j]['depreciation_method'] = $entry[$id_chart_account][0]->depreciation_method   ;
            $total_amount_asset += $asset[$j]['total_cost'];

            array_push($item_sql_object,$asset[$j]);
        }
    }


    $parent_asset['total_cost'] = $total_amount_asset;

    if($opcode == 0){
        DB::table('asset')
        ->insert($parent_asset);

        $id_asset = DB::table('asset')->max('id_asset');           
    }else{
        DB::table('asset')
        ->where('id_asset',$id_asset)
        ->update($parent_asset);
    }



    $purchase_date = $parent_asset['purchase_date'];
        //PUSH ASSET ITEMS
    for($i=0;$i<count($item_sql_object);$i++){
        $item_sql_object[$i]['id_asset'] = $id_asset;
        // $date = "2022-05-01";
        // $newDate = date('Y-m-t', strtotime($date. ' + 59 months'));
        $item_sql_object[$i]['start_life'] = $parent_asset['valuation_date'];
        $minus_month = (($item_sql_object[$i]['life'])*12)-1;

            // return $minus_month;
        $item_sql_object[$i]['end_life'] = date('Y-m-t', strtotime($parent_asset['valuation_date']. " + $minus_month months"));


        ;

            // return  $item_sql_object[$i]['id_asset'];
        if($opcode == 0 || $item_sql_object[$i]['asset_code'] == null){
            DB::table('asset_item')
            ->insert($item_sql_object[$i]);

            DB::table('asset_item')
            ->where('id_asset_item',DB::raw("LAST_INSERT_ID()"))
            ->update(['asset_code'=>DB::raw("concat(account_code,DATE_FORMAT('$purchase_date','%m%d%Y'),LAST_INSERT_ID())")]);               
        }else{

                // return $item_sql_object[$i];
            DB::table('asset_item')
            ->where('asset_code',$item_sql_object[$i]['asset_code'])
            ->where('id_asset',$id_asset)
            ->update($item_sql_object[$i]);
        }
    }

        //Delete asset
    if($opcode == 1 && count($asset_to_remove) > 0){
        DB::table('asset_item')
        ->where('id_asset',$id_asset)
        ->whereIn('asset_code',$asset_to_remove)
        ->delete();
    }

        // $beg_id_asset = [2,3];
    $beg_id_asset = [];
    if(!in_array($id_asset,$beg_id_asset)){
            if($parent_asset['type'] == 2){ // CREATE JV
                if($opcode == 0){
                    $id_journal_voucher = $this->InsertAdjustmentJV($id_asset,0,$opcode);
                    DB::table('asset')
                    ->where('id_asset',$id_asset)
                    ->update(['id_journal_voucher'=>$id_journal_voucher]);                
                }else{
                    //UPDATE JV
                    $this->InsertAdjustmentJV($id_asset,$previous_asset->id_journal_voucher,1);
                }
            }
        }

        DB::table('asset_depreciation')
        ->where('id_asset',$id_asset)
        ->delete();


        DB::table('asset_depreciation_month')
        ->where('id_asset',$id_asset)
        ->delete();

        $this->GenerateDepreciationTable($id_asset);

        //update last date of asset
        DB::select("UPDATE asset_depreciation_month
            SET depreciation_date =  LAST_DAY(concat(year,'-',month,'-01'))
            WHERE id_asset =?;",[$id_asset]);


        $data['id_asset'] = $id_asset;

        return response($data);

        return "SUCCESS";


        return $parent_asset;
        // return $parent_asset;
        return response($request);
    }

    public function parseCDVDetails($id_cash_disbursement){
        $output = array();
        $output['details']  = DB::table('cash_disbursement')
        ->select('id_cash_disbursement','date','id_branch','total')
        ->where('id_cash_disbursement',$id_cash_disbursement)
        ->where('type',3)
        ->first();
        $entries = DB::table('cash_disbursement_details as cdd')
        ->select('cdd.id_chart_account','cdd.account_code','cdd.debit as amount','am.depreciation_method')
        ->leftjoin('asset_maintenance as am','am.id_chart_account','cdd.id_chart_account')
        ->where('cdd.id_cash_disbursement',$output['details']->id_cash_disbursement)
        ->where('debit','>',0)
        ->get();
        $g = new GroupArrayController();

        $output['entries'] = $g->array_group_by($entries,['id_chart_account']);
        return $output;
    }

    public function parseAccounts($id_accounts){
        $entries = DB::table('chart_account as ca')
        ->select('ca.id_chart_account','ca.account_code','am.depreciation_method')
        ->leftjoin('asset_maintenance as am','am.id_chart_account','ca.id_chart_account')
        ->whereIn('ca.id_chart_account',$id_accounts)
        ->get();
        $g = new GroupArrayController();

        $output['entries'] = $g->array_group_by($entries,['id_chart_account']);
        return $output;
    }

    public function InsertAdjustmentJV($id_asset,$id_journal_voucher,$opcode){

        if($opcode == 0){
            DB::select("INSERT INTO journal_voucher (date,type,description,id_member,payee,reference,status,total_amount,id_branch,address,payee_type,id_cdv)
                SELECT purchase_date as date,5 as type,concat('ASSET ADJUSTMENT REF# ',asset.id_asset),0 as id_member,'ASSET' as payee,
                asset.id_asset as reference,0 as status,total_cost as total_amount,asset.id_branch as branch,'' as address, 4 as payee_type,asset.id_cash_disbursement
                FROM asset where id_asset = ?;",[$id_asset]);
            $id_journal_voucher = DB::table('journal_voucher')->max('id_journal_voucher');
        }else{
            DB::select("UPDATE asset as a
                LEFT JOIN journal_voucher as jv on jv.id_journal_voucher = a.id_journal_voucher
                SET date=purchase_date,total_amount=total_cost,jv.id_branch = a.id_branch
                where jv.id_journal_voucher = ?;",[$id_journal_voucher]);

            DB::table("journal_voucher_details")
            ->where('id_journal_voucher',$id_journal_voucher)
            ->delete();
        }

        // INSERT JV CHILD
        DB::select("INSERT INTO journal_voucher_details (id_journal_voucher,id_chart_account,account_code,description,debit,credit,details,reference)
            SELECT ? as id_journal_voucher,ca.id_chart_account ,ca.account_code,ca.description,SUM(total_cost) as debit,0 as credit, concat(asi.asset_code,' - ',asi.description) as details,asi.id_asset_item
            FROM asset_item as asi 
            LEFT JOIN chart_account as ca on ca.id_chart_account = asi.id_chart_account
            where asi.id_asset = ?
            GROUP BY ca.id_chart_account;",[$id_journal_voucher,$id_asset]);


        JVModel::setIsCash($id_journal_voucher);

        return $id_journal_voucher;

    }
    public function validate_item($item){
        $number_inputs = ['quantity','total_cost','life'];
        // ,'salvage_value'
        $text_inputs = ['description','serial_no'];

        $invalid = array();

        foreach($item as $c=>$row){
            $temp = array();

            foreach($text_inputs as $txt){
                if($row[$txt] == "" || !isset($row[$txt])){
                    array_push($temp,$txt);
                }
            }
            foreach($number_inputs as $num){
                if($row[$num] <= 0 || !isset($row[$num])){
                    array_push($temp,$num);
                }
            }
            if(count($temp) > 0){
                $invalid[$c] = $temp;
            }
            
        }
        return $invalid;
    }

    public function parseDepreciationTable($id_asset){
        $asset_depreciation = DB::table('asset_depreciation')
        ->where('id_asset',$id_asset)
        ->orDerby('id_Asset_depreciation')
        ->get();

        $g = new GroupArrayController();
        $output = array();
        $output['assets'] = $g->array_group_by($asset_depreciation,['asset_code','year']);


        $in_count = 0;
        $current_highest = '';
        foreach($output['assets'] as $asset_code=>$as){
            $c = count($as);
            if($c > $in_count){
                $current_highest = $asset_code;
                $in_count = $c;
            }
        }

        $years = array();

        foreach($output['assets'][$current_highest] as $year=>$val){
            array_push($years,$year);
        }

        $output['year_header'] = $years;

        return $output;

        return $current_highest;
    }
    public function test_dep($id_asset){
        $result = $this->GenerateDepreciationTable($id_asset);
        // return $result;

        $rows = "";
        foreach($result as $c=>$res){
            $rows .="<tr>";
            $rows .= "<td>".($c+1)."</td>";
            $rows .= "<td>".$res['year']."</td>";
            $rows .= "<td>".$res['start_book_value']."</td>";
            $rows .= "<td>".$res['depreciation_amount']."</td>";
            $rows .= "<td>".$res['end_book_value']."</td>";
            $rows .="</tr>";
        }


        echo "<table>$rows</table>";
    }
    public function GenerateDepreciationTable($id_asset){
        $asset = DB::table('asset_item as ai')
        ->select(DB::raw("ai.id_asset_item,ai.id_asset,asset_code,quantity,ai.total_cost,unit_cost as cost,life,(salvage_value/quantity) as salvage_value,depreciation_method,YEAR(valuation_date) as val_year,MONTH(valuation_date) as val_month"))
        ->leftJoin('asset as a','a.id_asset','ai.id_asset')
        ->where('ai.id_asset',$id_asset)
        ->get();




        $depreciation_array = array();
        $monthly_depreciation = array();

        // return $asset;



        foreach($asset as $as){
            // return $this->SumOfYears($as);

            // return $this->SumOfYears($as);
            if($as->depreciation_method == 1){
               $result = $this->StraightLine($as);

                 // return $result;
                 // return 123;
           }elseif($as->depreciation_method == 2){
               $result = $this->SumOfYears($as);
           }



            // return $as->depreciation_method;
           
            // $result = $this->SumOfYears($as);

           $depreciation_array = array_merge($depreciation_array,$result);
           $monthly_depreciation = array_merge($monthly_depreciation,$this->parseMonthlyDepreciation($result,$as));
       }

        // return $monthly_depreciation;



       DB::table('asset_depreciation')
       ->insert($depreciation_array);

       DB::table('asset_depreciation_month')
       ->insert($monthly_depreciation);

       return "success";
       return $depreciation_array[0];
   }

   public function StraightLine($asset){
        // return json_encode($asset);
    $cost = $asset->cost;
    $salvage_value = $asset->salvage_value;
    $output = array();

    $depreciation_per_year = ($cost-$salvage_value)/$asset->life;

    $percentage_dep = ROUND(($depreciation_per_year/($cost-$salvage_value))*100,2);     
    $year = $asset->val_year;


        // return $asset->life;

    $end_year = ($year+$asset->life)-(($asset->val_month >1)?0:1);

    for($i=$year;$i<=$end_year;$i++){
        $temp = array();

        $per = ($i==$year)?((12-$asset->val_month+1)/12):1;

        if($i != $end_year){
            $depreciation_value = $depreciation_per_year*$per;
        }else{
            $depreciation_value = $cost-$salvage_value;
        }

            // return $depreciation_value;
        $temp['start_book_value'] = $cost;

        $cost = ROUND($cost-$depreciation_value,2);

        $temp['id_asset_item'] = $asset->id_asset_item;
        $temp['year'] = $i;
            // $temp['date'] = 
        $temp['asset_code'] = $asset->asset_code;

            // $temp['depreciation_percentage'] = $percentage_dep;
        $temp['depreciation_amount'] = ROUND($depreciation_value,2);
        $temp['end_book_value'] = ($cost <= 0) ? $asset->salvage_value: $cost;
        $temp['id_asset'] = $asset->id_asset;

        array_push($output,$temp);
    }
        // $output[$i] = $asset->salvage_value;
    return $output;
}
public function SumOfYears($asset){
    $cost = $asset->cost;
    $salvage_value = $asset->salvage_value;
    $output = array();


    $sum_of_years = 0;
    for($i=1;$i<=$asset->life;$i++){
        $sum_of_years += $i;
    }
    $year = $asset->val_year;

    $base = $cost-$salvage_value;

    $loop_end = ($asset->val_month == 1)?1:0;


    $val_year = $asset->val_year;
    for($i=$asset->life;$i>=$loop_end;$i--){
        $temp = array();
            // $per = ($i==0)?(($asset->val_month)/12):1;

        $per = ($i==$asset->life)?((12-$asset->val_month+1)/12):1;

        $percentage_dep = ($i/$sum_of_years);


        if($i != 0){
            $depreciation_value = ROUND($base*$percentage_dep,2)*$per;
        }else{

            $depreciation_value = $cost-$salvage_value;
        }          

        $temp['start_book_value'] = $cost;
        $cost = ROUND($cost-$depreciation_value,2);
        $temp['id_asset_item'] = $asset->id_asset_item;
        $temp['year'] = $val_year;
        $temp['asset_code'] = $asset->asset_code;
        $temp['depreciation_percentage'] = ROUND($percentage_dep*100,2);
        $temp['depreciation_amount'] = $depreciation_value;
        $temp['end_book_value'] = ($cost <= 0) ? $asset->salvage_value: $cost;
        $temp['id_asset'] = $asset->id_asset;

        $val_year++;
        array_push($output,$temp);

    }
    return $output;
}
public function print_sticker($id_asset){

    $details = DB::table('asset')
    ->where('id_asset',$id_asset)
    ->first();

    if(!isset($details)){
        return "INVALID ASSET ID";
    }


    if($details->status == 10){
        return "CANCELLED ASSET";
    }


    $assets = DB::table('asset_item')
    ->select("account_code","asset_code",'serial_no','description','quantity',DB::raw("0 as counter"))
    ->where('id_asset',$id_asset)
    ->get();
    $g = new GroupArrayController();
    $assets = $g->array_group_by($assets,['account_code']);

    $asset_out = array();

    foreach($assets as $code=>$asset){
        $asset = json_decode(json_encode($asset),true);

        $asset_counter = 1;
        foreach($asset as $as){
            $temp = $as;

            for($i=0;$i<$as['quantity'];$i++){
                $temp['counter'] =  $i+1;
                array_push($asset_out,$temp);                
            }                
        }
    }

    $data['assets'] = array_chunk($asset_out, 3);

        // return $data;

    $data['fill_td'] = 3-count($data['assets'][count($data['assets'])-1]);


        // return $data;
        // return count($data['assets'][11]);

    $html = view('asset.sticker_print',$data);


    $pdf = PDF::loadHtml($html);
    $pdf->setOption("encoding","UTF-8");
    $pdf->setOption('margin-bottom', '3mm');
    $pdf->setOption('margin-top', '3mm');
    $pdf->setOption('margin-right', '3mm');
    $pdf->setOption('margin-left', '3mm');

    return $pdf->stream();

        // $pdf->setOption('header-left', 'Page [page] of [toPage]');
        // $pdf->setOption('header-right', "Control No.: ".$data['details']->control_number."  Account No. : ".$data['details']->account_no);
        // $pdf->setOption('header-font-size', 8);
        // $pdf->setOption('header-font-name', 'Calibri');



        // return $html;
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->render();
    $font = $dompdf->getFontMetrics()->get_font("serif");

        // $dompdf->getCanvas()->page_text(500, 50, "CDV No.", $font, 12, array(0,0,0));
        // $dompdf->getCanvas()->page_text(24, 18, "Page {PAGE_NUM} of {PAGE_COUNT}", $font, 8, array(0,0,0));
    $canvas = $dompdf->getCanvas();
        // $dompdf->set_paper("A4", 'landscape');
        // $dompdf->getCanvas()->page_text(530, 5, "Page: {PAGE_NUM} of {PAGE_COUNT}", $font, 9, array(0,0,0));      
    $dompdf->stream("Cash Disbursement Voucher No 123.pdf", array("Attachment" => false));   
    return $data;
    return view('asset.sticker_print');
}

public function post_cancel(Request $request){
    if($request->ajax()){
        $id_asset = $request->id_asset;
        $cancellation_reason = $request->cancellation_reason;



            // VALIDATION
        $asset_details = DB::table('asset')
        ->select('id_asset','status','id_journal_voucher')
        ->where('id_asset',$id_asset)
        ->first();
        if($this->check_asset_disposed($id_asset) > 0){
            $response['RESPONSE_CODE'] = "ERROR";
            $response['message'] = "Asset has already record on asset disposal";

            return response($response);
        }


        if(!isset($asset_details)){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "INVALID REQUEST";

            return response($data);
        }

        if($asset_details->status == 10){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Asset is alredy cancelled";

            return response($data);                
        }

        DB::table('asset')
        ->where('id_asset',$id_asset)
        ->update([

            'status' => 10,
            'cancellation_reason' => $cancellation_reason,
            'date_cancelled' => DB::raw("now()")
        ]);

        DB::table('journal_voucher')
        ->where('id_journal_voucher',$asset_details->id_journal_voucher)
        ->update(['status'=>10,'description'=>DB::raw("concat(description,' [CANCELLED]')"),'date_cancelled'=>DB::raw("now()"),'cancellation_reason'=>$cancellation_reason]);

        $data['RESPONSE_CODE'] = "SUCCESS";
            // $data['']
        return response($data);


        return response($request);

        return response($id_asset);
    }
}

public function parseMonthlyDepreciation($dep,$as){

    $monthly_dep = array();
    $accumulated_depreciation = 0;
    $end_value= 0;
    $start_book_value=$as->cost;
    $count = count($dep)-1;

        // return $dep[10];
    foreach($dep as $c=> $d){

            // return $c;
        $temp_val = array();
        if($as->val_year == $d['year']){
            $start = $as->val_month;
            $div_by = 12-$start+1;
            $end = 12;
        }elseif($c == $count){
            $start = 1;
            $div_by = $as->val_month-1;
            $end =$as->val_month-1;
        }else{
            $start = 1;
            $div_by = 12;
            $end=12;
        }

        $div_by  = $div_by==0?1:$div_by;
        $end = $end==0?1:$end;

        $dep_amt = $d['depreciation_amount'];
        $dep_month = ROUND($dep_amt/$div_by,2);


        $total = 0;
        for($i=$start;$i<=$end;$i++){
            $temp = array();
            $temp['month'] = $i;
            $temp['year'] = $d['year'];
            $temp['id_asset'] = $as->id_asset;
            $temp['id_asset_item'] = $as->id_asset_item;
            $temp['asset_code'] = $as->asset_code;
            $temp['start_book_value'] = ROUND($start_book_value,2);
            $temp['depreciation_amount'] =($i==$end)?(ROUND($dep_amt-$total,2)):ROUND($dep_month,2);

            $start_book_value-=$temp['depreciation_amount'];
            $temp['end_book_value'] = ROUND($start_book_value,2);

            $accumulated_depreciation+=ROUND($temp['depreciation_amount'],2);

            $temp['accumulated_depreciation'] = ROUND($accumulated_depreciation,2);

            array_push($monthly_dep,$temp);

            $total+=$dep_month;

        }


    }
    return $monthly_dep;

}
public function viewMonthlyDep(Request $request){
    if($request->ajax()){


        $data['details'] = DB::table('asset_item')
        ->select(DB::raw("concat(asset_code,' - ',description,' (Year ".$request->year.")') as asset_dec"))
        ->where('id_asset_item',$request->id_asset_item)
        ->first();
        $data['dep'] = DB::table('asset_depreciation_month')
        ->select(DB::raw("DATE_FORMAT(concat(year,'-',month,-01),'%M %Y') as month,start_book_value,depreciation_amount,accumulated_depreciation,end_book_value"))
        ->where('id_asset_item',$request->id_asset_item)
        ->where('year',$request->year)
        ->get();

                           // ->where('')
        return response($data);
    }
}
}
//VALIDATION

// THE TOTAL COST PER ACCOUNT MUST BE EQUAL TO CDV AMOUNT (PER ACCOUNT) - IMPLEMENTED
// THE ASSET PER ACCOUNT MUST BE GREATER THAN 0
// ASSET DETAILS REQUIRED FIELDS
// DESCRIPTION => TEXT
// SERIAL NUMBER => TEXT
// QUANTITY => NUMBER
// TOTAL COST => NUMBER
// LIFE => NUMBER
// SALVAGE VALUE => NUMBER







// sample output of invalid inputs



// 8:{
//     0 : ['description']
//     2 : ['amount','serial_no']
// }



