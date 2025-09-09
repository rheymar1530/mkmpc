<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\MySession;
use App\CredentialModel;
use App\JVModel;
class AssetDisposalController extends Controller
{
    public function index(){
        $data['credential']= CredentialModel::GetCredential(MySession::myPrivilegeId());
        if(!$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
        $data['head_title'] = "Asset Disposal";
        $data['disposal_list'] = DB::table('asset_disposal as ad')
                                ->select(DB::raw("ad.id_asset_disposal,DATE_FORMAT(ad.date,'%m/%d/%Y') as date,ad.total_amount as disposed_amount,crv.or_no,loss_gain_amount,if(loss_gain_amount < 0,'Loss',if(loss_gain_amount > 0,'Gain','')) as loss_gain_status,if(ad.status=10,'Cancelled','') as status"))
                                ->leftJoin('cash_receipt_voucher as crv','crv.id_cash_receipt_voucher','ad.id_cash_receipt_voucher')
                                ->orDerby('ad.id_asset_disposal','DESC')
                                ->get();
        return view('asset_disposal.index',$data);

        return $data;

    }
    public function create(){
        $data['opcode']  =0;
        $data['allowed_post'] = true;
        $data['head_title'] = "Create Asset Disposal";
        $data['current_date'] = MySession::current_date();
        return view('asset_disposal.form',$data);
    }

    public function search_asset(Request $request){

        $input = "%".$request->search."%";
        $data['assets'] = DB::select("SELECT asset_code,concat(asset_code,' - ',description) as description 
                            FROM asset_item as ai
                            WHERE (asset_code like ? OR description like ? )  ",[$input,$input]);
        return $data;
    }

    public function view($id_asset_disposal){
        $data['current_date'] = MySession::current_date();
        $data['head_title'] = "Asset Disposal #$id_asset_disposal";
        $data['opcode'] = 1;
        $data['details'] = DB::table('asset_disposal as ad')
                           ->select(DB::raw("ad.id_asset_disposal,ad.date,ad.total_amount,ad.id_cash_receipt_voucher,ad.status,ad.id_journal_voucher"))
                           ->where('id_asset_disposal',$id_asset_disposal)
                           ->first();


        $data['allowed_post'] = ($data['details']->status ==10)?false:true;

        // return $data;
        // return $data;

        if($data['details']->id_cash_receipt_voucher > 0){
            $data['crv_details'] = DB::table('cash_receipt_voucher_details as crdv')
                                ->select(DB::raw("SUM(credit) as amount,cr.id_cash_receipt_voucher as id_cr,concat(cr.or_no,' - ',cr.description,' (',CAST(FORMAT(SUM(credit),2) AS CHAR CHARACTER SET utf8),')') as description"))
                                ->leftJoin('cash_receipt_voucher as cr','cr.id_cash_receipt_voucher','crdv.id_cash_receipt_voucher')
                                ->where('cr.id_cash_receipt_voucher', $data['details']->id_cash_receipt_voucher)
                                ->where('crdv.id_chart_account',40)
                                ->first();
        }

        // return json_encode($data['crv_details']);
        $data['assets'] =DB::table('asset_disposal_item as adi')
                        ->select(DB::raw("adi.asset_code,concat(adi.asset_code,' - ',ai.description) as description,getAssetQuantity(adi.asset_code,$id_asset_disposal) as quantity_remaining,FORMAT(adi.purchase_cost,2) as purchase_cost,FORMAT(adi.accumulated_depreciation,2) as accumulated_depreciation,FORMAT(adi.current_value,2) as current_value,adi.quantity"))
                        ->leftJoin('asset_item as ai','ai.asset_code','adi.asset_code')
                        ->where('adi.id_asset_disposal',$id_asset_disposal)
                        ->get();

        return view('asset_disposal.form',$data);
        return $data;
    }

    public function search_or(Request $request){
        if($request->ajax()){
            $input = "%".$request->search."%";
            $id_asset_disposal = $request->id_asset_disposal;

            $data['crv'] = DB::select("SELECT crv.id_cash_receipt_voucher as id_crv,concat(crv.or_no,' - ',crv.description,' (',CAST(FORMAT(SUM(credit),2) AS CHAR CHARACTER SET utf8),')') as description
                                        FROM cash_receipt_voucher as crv
                                        LEFT JOIN asset_disposal as ad on ad.id_cash_receipt_voucher = crv.id_cash_receipt_voucher and ad.status <> 10 and ad.id_asset_disposal <> ?
                                        LEFT JOIN cash_receipt_voucher_details as crdv on crdv.id_cash_receipt_voucher =crv.id_cash_receipt_voucher
                                        WHERE ad.id_asset_disposal is null and crdv.id_chart_account = 40 and crv.or_no like ?
                                        GROUP BY crv.id_cash_receipt_voucher",[$id_asset_disposal,$input]);

            return response($data);
        }
    }

    public function parse_asset_details(Request $request){
        $asset_code = $request->asset_code;
        $date = $request->date;

        //Depreciation
        $data['details']= $this->getDepreciation($asset_code,$date,0);

        return response($data);

        //GET ASSET DETAILS

    }
    public function getDepreciation($asset_code,$date,$id_asset_disposal){
        $output = DB::select("CALL GetMonthlyDepreciation(?,?,?)",[$asset_code,$date,$id_asset_disposal])[0];

        return $output;
    }
    public function post(Request $request){
        $assetData = $request->assetData;
        $date = $request->date;
        $id_crv = $request->id_crv;

        $opcode = $request->opcode ; //0-add;1-edit
        $id_asset_disposal = $request->id_asset_disposal;


        $asset_disposal_parent = array();

        $asset_disposal_parent['date'] = $date;

        

        if($request->cash_proceed == 1){
            if(!isset($id_crv)){
                $data['RESPONSE_CODE'] = "ERROR";
                $data['message'] = "Please select valid OR";

                return response($data);
            }
        }

        //validate crv
        $c = DB::table('asset_disposal')
            ->where('id_cash_receipt_voucher',$id_crv)
            ->where('asset_disposal.id_asset_disposal','<>',$id_asset_disposal)
            ->where('asset_disposal.status','<>',10)
            ->count();
        if($c > 0){
            $response['RESPONSE_CODE'] ="ERROR";
            $response['message'] = "Invalid Request";

            return response($response);
        }

        $total_amount = 0;
        $push_object_asset = array();
        foreach($assetData as $asset){
            $temp = array();
            $da['dd'] =$details = $this->getDepreciation($asset['asset_code'],$date,$id_asset_disposal);

            if($details->remaining_quantity > 0 && $details->remaining_quantity >= $asset['quantity_disposed']){
                $temp['asset_code'] = $asset['asset_code'];
                $temp['quantity_before'] = $details->remaining_quantity;
                $temp['purchase_cost'] = $details->purchase_cost;
                $temp['accumulated_depreciation'] = $details->accumulated_depreciation;
                $temp['current_value'] = $details->current_value;
                $temp['quantity'] = $asset['quantity_disposed'];

                $total_amount += $details->current_value*$asset['quantity_disposed'];

                array_push($push_object_asset,$temp);
                
            }else{

                $data['RESPONSE_CODE'] = "ERROR";
                $data['message'] = "Invalid Quantity";
                return response($data);
            }
        }
        $asset_disposal_parent['total_amount'] = $total_amount;

        $crv_amount = 0;
        if(isset($id_crv)){
            $crv_amount =     DB::table('cash_receipt_voucher_details as crdv')
                                  ->select(DB::raw("SUM(credit) as amount"))
                                  ->leftJoin('cash_receipt_voucher as cr','cr.id_cash_receipt_voucher','crdv.id_cash_receipt_voucher')
                                  ->where('cr.id_cash_receipt_voucher',$id_crv)
                                  ->where('crdv.id_chart_account',40)
                                  ->first()->amount;
            $asset_disposal_parent['id_cash_receipt_voucher'] = $id_crv;
            $asset_disposal_parent['amount_received'] = $crv_amount;
            $asset_disposal_parent['loss_gain_amount'] = $crv_amount - $total_amount;
        }else{
            $asset_disposal_parent['id_cash_receipt_voucher'] = 0;
            $asset_disposal_parent['amount_received'] = 0;
            
        }

        $asset_disposal_parent['loss_gain_amount'] = $asset_disposal_parent['amount_received']-$asset_disposal_parent['total_amount'];
        if($opcode == 0){
            $asset_disposal_parent['id_user'] = MySession::mySystemUserId();
            DB::table('asset_disposal')
            ->insert($asset_disposal_parent);


            $id_asset_disposal = DB::table('asset_disposal')->where('id_user',MySession::mySystemUserId())->max('id_asset_disposal');
        }else{

            DB::table('asset_disposal')
            ->where('id_asset_disposal',$id_asset_disposal)
            ->update($asset_disposal_parent);

            DB::table('asset_disposal_item')->where('id_asset_disposal',$id_asset_disposal)->delete();

        }

        
        for($i=0;$i<count($push_object_asset);$i++){
            $push_object_asset[$i]['id_asset_disposal'] = $id_asset_disposal;
        }

        DB::table('asset_disposal_item')
        ->insert($push_object_asset);


        $id_journal_voucher = JVModel::AssetDisposalJV($id_asset_disposal);

        $data['RESPONSE_CODE'] = "SUCCESS";
        $data['id_asset_disposal'] = $id_asset_disposal;
        $data['id_journal_voucher'] =$id_journal_voucher;

        $this->update_asset_disposed($id_asset_disposal,$asset_disposal_parent['date']);

        return response($data);

        return "success";
                             

        return $push_object_asset;
        return $assetData;
    }

    public function parseCRVDetails(Request $request){
        if($request->ajax()){
            $id_crv = $request->id_crv;


            $response['result'] = DB::table('cash_receipt_voucher_details as crdv')
                                  ->select(DB::raw("SUM(credit) as amount"))
                                  ->leftJoin('cash_receipt_voucher as cr','cr.id_cash_receipt_voucher','crdv.id_cash_receipt_voucher')
                                  ->where('cr.id_cash_receipt_voucher',$id_crv)
                                  ->where('crdv.id_chart_account',40)
                                  ->first();

            return response($response);
        }
    }
    public function post_cancel(Request $request){
        if($request->ajax()){
            $id_asset_disposal = $request->id_asset_disposal;
            $cancellation_reason = $request->cancellation_reason;

            // VALIDATION
            $asset_details = DB::table('asset_disposal')
                           ->select('id_asset_disposal','status','id_journal_voucher')
                           ->where('id_asset_disposal',$id_asset_disposal)
                           ->first();




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

            DB::table('asset_disposal')
            ->where('id_asset_disposal',$id_asset_disposal)
            ->update([
             
                'status' => 10,
                'cancellation_reason' => $cancellation_reason,
                'date_cancelled' => DB::raw("now()")
            ]);

            DB::table('journal_voucher')
            ->where('id_journal_voucher',$asset_details->id_journal_voucher)
            ->update(['status'=>10,'description'=>DB::raw("concat(description,' [CANCELLED]')"),'date_cancelled'=>DB::raw("now()"),'cancellation_reason'=>$cancellation_reason]);

            $data['RESPONSE_CODE'] = "SUCCESS";

            $this->update_asset_disposed($id_asset_disposal,null);
            // $data['']
            return response($data);


            return response($request);

            return response($id_asset_disposal);
        }
    }
    public function refresh_table(Request $request){
        if($request->ajax()){

            $assets = $request->assets;
            $date = $request->date;
            $id_asset_disposal = $request->id_asset_disposal;

            $asset_dep_details = array();
            for($i=0;$i<count($assets);$i++){
                if(isset($assets[$i])){
                    $asset_dep_details[$assets[$i]] = $this->getDepreciation($assets[$i],$date,$id_asset_disposal);
                }
            }


            $output['asset_depreciation'] = $asset_dep_details;

            $output['count'] = count($asset_dep_details);



            return response($output);
        }
    }

    public function update_asset_disposed($id_asset_disposal,$date){
        DB::select("UPDATE asset_disposal_item  as adi
                    LEFT JOIN asset_item as ai on ai.asset_code = adi.asset_code
                    SET ai.disposed = if(getAssetQuantity(adi.asset_code,0)>0,0,1)
                    WHERE adi.id_asset_disposal=?;",[$id_asset_disposal]);

        DB::select("UPDATE asset_disposal_item  as adi
                    LEFT JOIN asset_item as ai on ai.asset_code = adi.asset_code
                    SET fully_disposal_date = if(ai.disposed=1,?,null)
                    WHERE adi.id_asset_disposal=?;",[$date,$id_asset_disposal]);
    }
}




// SET @input = '2023-12-01';
// SELECT ai.asset_code,ifnull(ad.year,YEAR(@input)) as year,ifnull(ad.end_book_value,ai.salvage_value) as year_dep,(ad.depreciation_amount) as depreciation_amount,ifnull((12-MONTH(@input)),1) month_year_used
// FROM asset_item as ai
// LEFT JOIN asset_depreciation as ad on ai.id_asset_item = ad.id_asset_item and ad.year = YEAR(@input)
// where ai.asset_code = 108020503202249;





// SET @input = '2022-12-01';
// SELECT unit_cost as purchase_cost,
// @q:=
// ROUND(
// CASE 
//     WHEN (depreciation_amount is null OR  YEAR(@input) = year_end AND MONTH(@input) >= month_end) THEN unit_cost - salvage_value
//     WHEN YEAR(@input)=year_start THEN (depreciation_amount/first_year_remaining)*(MONTH(@input)-month_start+1)
//     ELSE  unit_cost-start_book_value + (depreciation_amount/12)*month_year_used
//     END,2) as accumulated_depreciation,ROUND(unit_cost-@q,2) as current_value
// FROM (
// SELECT ai.asset_code,unit_cost,ifnull(ad.year,YEAR(@input)) as year,ROUND(salvage_value/quantity,2) as salvage_value,start_book_value,end_book_value,MONTH(start_life) as month_start,YEAR(start_life) as year_start,
// MONTH(end_life) as month_end,YEAR(end_life) as year_end,
// (ad.depreciation_amount) as depreciation_amount,(12-MONTH(start_life)+1) as first_year_remaining,ifnull((MONTH(@input)),1) month_year_used
// FROM asset_item as ai
// LEFT JOIN asset_depreciation as ad on ai.id_asset_item = ad.id_asset_item and ad.year = YEAR(@input)
// where ai.asset_code = 108020503202249) as t;




//JV CHILD ENTRY
// INSERT INTO journal_voucher (id_journal_voucher,id_chart_account,account_code,description,debit,credit,details,reference)
// select 1 as id_journal_voucher,ca.id_chart_account,ca.account_code,ca.description,amount_received as debit,0 credit,'' as details,id_asset_disposal as reference
// FROM asset_disposal 
// LEFT JOIN chart_account as ca on ca.id_chart_account =40
// where id_asset_disposal =6
// UNION ALL
// select 1 as id_journal_voucher,ca.id_chart_account,ca.account_code,ca.description,abs(loss_gain_amount) as debit,0 credit,'' as details,id_asset_disposal as reference
// FROM asset_disposal 
// LEFT JOIN chart_account as ca on ca.id_chart_account =75
// where id_asset_disposal =6 and loss_gain_amount < 0
// UNION ALL
// SELECT 1 as id_journal_voucher,ca.id_chart_account,ca.account_code,ca.description,SUM(accumulated_depreciation*quantity) as debit,0 credit,'' as details,id_asset_disposal as reference
// FROM asset_disposal_item as adi
// LEFT JOIN chart_account as ca on ca.id_chart_account =10
// WHERE adi.id_asset_disposal = 6
// UNION ALL
// SELECT 1 as id_journal_voucher,ca.id_chart_account,ca.account_code,ca.description,0 as debit,(ai.unit_cost*adi.quantity) credit,'' as details,id_asset_disposal as reference
// FROM asset_disposal_item as adi
// LEFT JOIN asset_item as ai on ai.asset_code = adi.asset_code
// LEFT JOIN chart_account as ca on ca.id_chart_account =ai.id_chart_account
// WHERE adi.id_asset_disposal =6
// UNION ALL
// select 1 as id_journal_voucher,ca.id_chart_account,ca.account_code,ca.description,0 as debit,abs(loss_gain_amount) as credit,'' as details,id_asset_disposal as reference
// FROM asset_disposal 
// LEFT JOIN chart_account as ca on ca.id_chart_account =41
// where id_asset_disposal =6 and loss_gain_amount > 0;