<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\CredentialModel;
use App\MySession as MySession;
class AssetMaintenanceController extends Controller
{
    public function index(){
        $data['credential']= CredentialModel::GetCredential(MySession::myPrivilegeId());
        $data['allow_post'] = ($data['credential']->is_create || $data['credential']->is_edit)?1:0;
        $data['head_title'] = "Asset Maintenance";
        // return $data['credential'];
        $data['asset_accounts'] = DB::table('chart_account as ca')
                                 ->select('ca.id_chart_account','ca.account_code','ca.description','am.life_span','am.salvage_percentage','am.depreciation_method')
                                 ->leftJoin('asset_maintenance as am','am.id_chart_account','ca.id_chart_account')
                                 ->where('ca.id_chart_account_type',1)
                                 ->get();
        return view('asset.asset_maintenance',$data);

        return $data;

    }
    public function post(Request $request){
        if($request->ajax()){
            $changes = $request->changes;
            $data['RESPONSE_CODE'] = "SUCCESS";

            DB::table('asset_maintenance')
            ->upsert($changes,['id_chart_account'],['life_span','salvage_percentage','depreciation_method']);


            // $data['RESPONSE_CODE'] = "ERROR";
            // $data['message'] = "THIS IS THE ERROR";

            return response($data);
        }
    }
}
