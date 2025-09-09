<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\CredentialModel;
use App\MySession as MySession;
class ChartofAccountController extends Controller
{
    public function index(){
        $data['credential']= CredentialModel::GetCredential(MySession::myPrivilegeId());
        if(!$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
        $data['head_title'] = "Chart of Accounts";
        $data['charts'] = DB::table('chart_account as ca')
        ->select(DB::raw("ca.id_chart_account,ca.account_code,ca.description,cag.name as cat_desc,cali.description as line_desc,can.description as cred_deb_description,cas.description as sub_type,cat.description as type_description,if(ca.ac_active=0,'Inactive','') as status"))
        ->leftJoin("chart_account_category as cag","cag.id_chart_account_category","ca.id_chart_account_category")
        ->LeftJoin("chart_account_line_item as cali","cali.id_chart_account_line_item","ca.id_chart_account_line_item")
        ->LeftJoin("chart_account_normal as can","can.id_chart_account_normal","ca.normal")
        ->LeftJoin("chart_account_subtype as cas","cas.id_chart_account_subtype","ca.id_chart_account_subtype")
        ->LeftJoin("chart_account_type as cat","cat.id_chart_account_type","ca.id_chart_account_type")
        ->orDerby('ca.id_chart_account','DESC')
        ->get();

        // dd($data['charts'])
        $data['category'] = DB::table('chart_account_category')
        ->select("id_chart_account_category","name")
        ->orDerby("name")
        ->get();
        $data['line_item'] = DB::table('chart_account_line_item')
        ->select("id_chart_account_line_item","description")
        ->orDerby("description")
        ->get();
        $data['normal'] = DB::table('chart_account_normal')
        ->select("id_chart_account_normal","description")
        ->orDerby("description")
        ->get();

        $data['sub_type'] = DB::table('chart_account_subtype')
        ->select("id_chart_account_subtype","description")
        ->orDerby("description")
        ->get();
        $data['type'] = DB::table('chart_account_type')
        ->select("id_chart_account_type","description")
        ->orDerby("description")
        ->get();

        $data['charts_sel'] = DB::table('chart_account')
                          ->select('id_chart_account',DB::raw("concat(account_code,' - ',description) as account"))
                          ->where('id_chart_account_line_item',9)
                          ->orderBy('account_code','ASC')
                          ->orderBy('description','ASC')
                          ->get();

        $data['ac_charts_sel'] = DB::table('chart_account')
                          ->select('id_chart_account',DB::raw("concat(account_code,' - ',description) as account"))
                          ->where('id_chart_account_line_item',2)
                          ->orderBy('account_code','ASC')
                          ->orderBy('description','ASC')
                          ->get();

        $data['cfs'] = DB::select("SELECT cf.id_cash_flow,
        if(cf.type=1,'Operating Activities',if(cf.type=2,'Investing Activities','Financing Activities')) as type,if(sub_type is not null AND sub_type <> '',concat(' > ',sub_type),'') as sub_type,description
        FROM cash_flow as cf;");
        return view('accounting.chart_of_account',$data);
    }
    public function post(Request $request){
        if($request->ajax()){
            $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/charts_of_account');
            // inputs = ['txt_code','txt_description','sel_cat','sel_line_item','sel_normal','sel_type','sel_sub_type'];
            $data['RESPONSE_CODE'] = "success";
            $data_obj = array();
            $opcode = $request->opcode;
            $id_chart_account = $request->id_chart_account;
            //validate account code
            $account_codes = DB::table('chart_account')
            ->select('account_code')
            ->whereIn('account_code',$request->txt_code)
            ->where(function($query) use($opcode,$id_chart_account){
                if($opcode == 1){
                    $query->where('id_chart_account','<>',$id_chart_account);
                } 
            })
            ->get();
            $data['array_codes']    = array_column(json_decode($account_codes,true), 'account_code');

            if(count($data['array_codes']) > 0){
                $data['RESPONSE_CODE'] = "DUPLICATE_CODE";
                return response($data);
            }

            for($i=0;$i<count($request->txt_code);$i++){
                $data_obj[]=[
                    'description' => $request->txt_description[$i],
                    'account_code' => $request->txt_code[$i],
                    'normal' => $request->sel_normal[$i] ?? 0,
                    'id_chart_account_category' => $request->sel_cat[$i],
                    'id_chart_account_type' => $request->sel_type[$i],
                    'id_chart_account_subtype' => $request->sel_sub_type[$i],
                    'id_chart_account_line_item' => $request->sel_line_item[$i],
                    'depreciation_account'=>$request->sel_dep_acc[$i],
                    'ac_depreciation_account'=>$request->sel_ac_dep_account[$i],
                    'ac_active'=>$request->sel_status[$i],
                    'id_cash_flow'=>$request->sel_cash_flow[$i]
                ];
            }

            if($request->opcode == 0){ // Add
                if(!$data['credential']->is_create){
                    $data['RESPONSE_CODE'] = "CREDENTIAL_ERROR";
                    $data['message'] = "You dont have a privilege to save this";
                    return response($data);
                }
                DB::table('chart_account')
                ->insert($data_obj);             
            }else{ // Edit
                if(!$data['credential']->is_edit){
                    $data['RESPONSE_CODE'] = "CREDENTIAL_ERROR";
                    $data['message'] = "You dont have a privilege to save this";
                    return response($data);
                }
                DB::table('chart_account')
                ->where('id_chart_account',$request->id_chart_account)
                ->update($data_obj[0]);  
            }

            return response($data);
        }
    }
    public function view_chart(Request $request){
        if($request->ajax()){
            $id_chart_account = $request->id_chart_account;
            $data['details'] = DB::table('chart_account')->where('id_chart_account',$id_chart_account)->first();
            return response($data);
        }
    }
}
