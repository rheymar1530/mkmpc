<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use DB;
use App\CredentialModel;
use App\MySession as MySession;
class SelectMaintenanceController extends Controller
{
    public function index($type){
        $data = array();
        $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),"/maintenance/$type");
        if(!$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
        $tbl_details = $this->parse_main_config($type);

        
        $data['type'] = $type;
        $data['head_title'] = "Maintenance - ".$tbl_details['mod_title'];
        foreach($tbl_details as $key=>$val){
            $data[$key] = $val;
        }
        $data['datasets'] = $this->get_data($data);
        // return $data;
        // return $data['datasets'];

        return view('select_maintenance.index',$data);
    }
    public function get_data($table_details){
        $fields = array();
        array_push($fields,$table_details['primary_key']);
        foreach($table_details['table_fields'] as $tbl){
            if(isset($tbl['display_field'])){
                $field = $tbl['display_field'];
            }else{
                $field = $table_details['table_name'].".".$tbl['field'];
            }
            
            array_push($fields,$field);

        }

        $data = DB::table($table_details['table_name'])
        ->Select($fields);

        foreach($table_details['table_fields'] as $tbl){
            if(isset($tbl['join'])){
                $data->LeftJoin($tbl['join'],$tbl['join'].".".$tbl['join_field'],'=',$table_details['table_name'].".".$tbl['field']);
            }
        }
        if($table_details['table_name'] == "tbl_payment_type"){
            $data->where('type',1);
        }elseif($table_details['table_name'] == "loan_fees"){
            $data->where('visible',1);
        }elseif($table_details['type'] == "brgy"){
            $data->where('type',1);
        }elseif($table_details['type'] == "lgu"){
            $data->where('type',2);
        }

        if($table_details['type'] == "cash-flow"){
            return $data->orDerby('order','ASC')->get();
        }
        return $data->orDerby($table_details['primary_key'],'DESC')->get();
        // ->get();
    }
    public function post($type,Request $request){
        $post_object = $request->post_object;

        $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),"/maintenance/$type");
        $tbl_details = $this->parse_main_config($type);






        if($request->opcode == 0){ // insert
            for($i=0;$i<count($post_object);$i++){
                if($tbl_details['type'] == 'brgy'){
                    $post_object[$i]['type'] = 1;
                }elseif($tbl_details['type'] == 'lgu'){
                    $post_object[$i]['type'] = 2;
                }          
            }
            if(!$data['credential']->is_create){
                $data['RESPONSE_CODE'] = "CREDENTIAL_ERROR";
                $data['message'] = "You dont have a privilege to save this";
                return response($data);
            }
            DB::table($tbl_details['table_name'])
            ->insert($post_object);

            // if($tbl_details['table_name'] == "loan_fees"){
            //     $new_id = DB::table($tbl_details['table_name'])->max('id_loan_fees');
            //     DB::table('tbl_payment_type')
            //     ->insert([
            //         'description' =>$post_object['name'],
            //         'type' => 2,
            //         'reference' =>$new_id
            //     ]);

            // }
        }else{ // update
            if(!$data['credential']->is_edit){
                $data['RESPONSE_CODE'] = "CREDENTIAL_ERROR";
                $data['message'] = "You dont have a privilege to save this";
                return response($data);
            }
                if($tbl_details['type'] == 'brgy'){
                    $post_object[0]['type'] = 1;
                }elseif($tbl_details['type'] == 'lgu'){
                    $post_object[0]['type'] = 2;
                }  
            DB::table($tbl_details['table_name'])
            ->where($tbl_details['primary_key'],$request->id_reference)
            ->update($post_object[0]);

            // if($tbl_details['table_name'] == "loan_fees"){
            //     $new_id = DB::table($tbl_details['table_name'])->max('id_loan_fees');
            //     DB::table('tbl_payment_type')
            //     ->where('reference',$request->id_reference)
            //     ->where('type',1)
            //     ->update([
            //         'description' =>$post_object[0]['name']
            //     ]);

            // }
        }


        $data['RESPONSE_CODE'] = "SUCCESS";

        return response($data);
    }
    public function view($type,Request $request){
        if($request->ajax()){
            $tbl_details = $this->parse_main_config($type);
            $id_reference = $request->id_reference;

            $table_name = $tbl_details['table_name'];
            $primary_key = $tbl_details['primary_key'];

            $fields = array();
            
            foreach($tbl_details['table_fields'] as $tbl){
                array_push($fields,$tbl['field']);
            }

            $data['id_reference'] = $id_reference;
            $data['details'] = DB::table($table_name)
            ->Select($fields)
            ->where($primary_key,$id_reference)

            ->first();

            return $data;
            return response($id_reference);
        }
    }
    public function delete($type,Request $request){
        if($request->ajax()){
           $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),"/maintenance/$type");
            if(!$data['credential']->is_delete){
                $data['RESPONSE_CODE'] = "CREDENTIAL_ERROR";
                $data['message'] = "You dont have a privilege to save this";
                return response($data);
            }
           $tbl_details = $this->parse_main_config($type);
           $id_references = $request->id_references;

           $table_name = $tbl_details['table_name'];
           $primary_key = $tbl_details['primary_key'];

           DB::table($table_name)
           ->whereIn($primary_key,$id_references)
           ->delete();

           $data['RESPONSE_CODE'] = "SUCCESS";
           return response($data);
       }
   }
   public function parse_main_config($type){
    $data['type'] = $type;
    if($type== "branch"){
        $data['mod_title'] = "Branch";
        $data['table_name'] = "tbl_branch";
        $data['primary_key'] = "id_branch";
        $data['primary_key_label'] = "Branch ID";
        $data['table_fields'] =[
            ["field"=>"branch_name","label"=>"Branch Name","input"=>"input","type"=>"text"]
        ];           
    }elseif($type == "chart_account_category"){
        $data['mod_title'] = "Chart of Account Category";
        $data['table_name'] = "chart_account_category";
        $data['primary_key'] = "id_chart_account_category";
        $data['primary_key_label'] = "Chart Account Category ID";
        $data['table_fields'] =[
            ["field"=>"name","label"=>"Category Name","input"=>"input","type"=>"text"]
        ];                
    }elseif($type == "chart_account_line_item"){
        $data['mod_title'] = "Chart of Account Line Item";
        $data['table_name'] = "chart_account_line_item";
        $data['primary_key'] = "id_chart_account_line_item";
        $data['primary_key_label'] = "Chart Account Line Item ID";
        $data['table_fields'] =[
            ["field"=>"description","label"=>"Description","input"=>"input","type"=>"text"]
        ];             
    }elseif($type == "chart_account_type"){
        $data['mod_title'] = "Chart of Account Type";
        $data['table_name'] = "chart_account_type";
        $data['primary_key'] = "id_chart_account_type";
        $data['primary_key_label'] = "Chart of Account Type ID";
        $data['table_fields'] =[
            ["field"=>"description","label"=>"Description","input"=>"input","type"=>"text"],
                // ["field"=>"id_sort","label"=>"Sort","input"=>"input","type"=>"text"]
        ];              
    }elseif($type == "payment_type"){
        $options = DB::table('chart_account')->select(DB::raw("id_chart_account as value, description as description"))->get();
        $data['mod_title'] = "Payment Type";
        $data['table_name'] = "tbl_payment_type";
        $data['primary_key'] = "id_payment_type";
        $data['primary_key_label'] = "Payment Type ID";
        $data['table_fields'] =[
            ["field"=>"description","label"=>"Description","input"=>"input","type"=>"text"],
            ["field"=>"id_chart_account","label"=>"Chart Code","input"=>"select",'select_options'=>$options,'join'=>'chart_account','join_field'=>'id_chart_account',"display_field"=>"chart_account.description as chart_desc"],  
        ];              
    }elseif($type == "bank"){
        $options = DB::table('chart_account')->select(DB::raw("id_chart_account as value, concat(account_code,' - ',description) as description"))->get();
        $data['mod_title'] = "Banks";
        $data['table_name'] = "tbl_bank";
        $data['primary_key'] = "id_bank";
        $data['primary_key_label'] = "Bank ID";
        $data['table_fields'] =[
            ["field"=>"bank_name","label"=>"Bank Name","input"=>"input","type"=>"text"],
            ["field"=>"id_chart_account","label"=>"Chart Code","input"=>"select",'select_options'=>$options,'join'=>'chart_account','join_field'=>'id_chart_account',"display_field"=>"chart_account.description as chart_desc"]
                // ["field"=>"id_sort","label"=>"Sort","input"=>"input","type"=>"text"]
        ];            
    }elseif($type == "loan_fees"){
        $options = DB::table('chart_account')->select(DB::raw("id_chart_account as value, concat(account_code,' - ',description) as description"))->get();
        $data['mod_title'] = "Loan Fees";
        $data['table_name'] = "loan_fees";
        $data['primary_key'] = "id_loan_fees";
        $data['primary_key_label'] = "Loan Fee ID";
        $data['table_fields'] =[
            ["field"=>"name","label"=>"Loan Fee","input"=>"input","type"=>"text"],
            ["field"=>"id_chart_account","label"=>"Chart Code","input"=>"select",'select_options'=>$options,'join'=>'chart_account','join_field'=>'id_chart_account',"display_field"=>"chart_account.description as chart_desc"]
                // ["field"=>"id_sort","label"=>"Sort","input"=>"input","type"=>"text"]
        ];            
    }elseif($type == "department"){
        $data['mod_title'] = "Department";
        $data['table_name'] = "department";
        $data['primary_key'] = "id_department";
        $data['primary_key_label'] = "Department ID";
        $data['table_fields'] =[
            ["field"=>"name","label"=>"Department Name","input"=>"input","type"=>"text"]
        ];
    }elseif($type == "position"){
        $data['mod_title'] = "Position";
        $data['table_name'] = "position";
        $data['primary_key'] = "id_position";
        $data['primary_key_label'] = "Position ID";
        $data['table_fields'] =[
            ["field"=>"description","label"=>"Position Name","input"=>"input","type"=>"text"]
        ];
    }elseif($type == "allowance"){
        $options = DB::table('chart_account')->select(DB::raw("id_chart_account as value, concat(account_code,' - ',description) as description"))->get();
        $data['mod_title'] = "Allowance Type";
        $data['table_name'] = "allowance_name";
        $data['primary_key'] = "id_allowance_name";
        $data['primary_key_label'] = "Allowance ID";
        $data['table_fields'] =[
            ["field"=>"description","label"=>"Allowance Name","input"=>"input","type"=>"text"],
            ["field"=>"id_chart_account","label"=>"Chart Code","input"=>"select",'select_options'=>$options,'join'=>'chart_account','join_field'=>'id_chart_account',"display_field"=>"chart_account.description as chart_desc"]
        ];       
    }elseif($type == "employment_status"){
        $data['mod_title'] = "Employment Status";
        $data['table_name'] = "employee_status";
        $data['primary_key'] = "id_employee_status";
        $data['primary_key_label'] = "Employment Status ID";
        $data['table_fields'] =[
            ["field"=>"description","label"=>"Employment Status","input"=>"input","type"=>"text"]
        ];        
    }elseif($type == "brgy"){
        $data['mod_title'] = "Barangay";
        $data['table_name'] = "baranggay_lgu";
        $data['primary_key'] = "id_baranggay_lgu";
        $data['primary_key_label'] = "Barangay ID";
        $data['table_fields'] =[
            ["field"=>"name","label"=>"Barangay","input"=>"input","type"=>"text"],
            ["field"=>"treasurer","label"=>"Treasurer","input"=>"input","type"=>"text"],
            ["field"=>"chairman","label"=>"Chairman","input"=>"input","type"=>"text"]
        ];        
    }elseif($type == "lgu"){
        $data['mod_title'] = "LGU";
        $data['table_name'] = "baranggay_lgu";
        $data['primary_key'] = "id_baranggay_lgu";
        $data['primary_key_label'] = "LGU ID";
        $data['table_fields'] =[
            ["field"=>"name","label"=>"LGU","input"=>"input","type"=>"text"],
            ["field"=>"treasurer","label"=>"Admin Officer","input"=>"input","type"=>"text"],
            ["field"=>"chairman","label"=>"Municipal Accountant","input"=>"input","type"=>"text"]
        ];        
    }elseif($type == "cash-flow"){
        $options = DB::table('cash_flow_type')->select(DB::raw("id_cash_flow_type as value, description"))->get();
        $data['mod_title'] = "Cash Flow";
        $data['table_name'] = "cash_flow";
        $data['primary_key'] = "id_cash_flow";
        $data['primary_key_label'] = "ID";
        $data['hide_id_table'] = 
        $data['table_fields'] =[
            ["field"=>"type","label"=>"Type","input"=>"select",'select_options'=>$options,'join'=>'cash_flow_type','join_field'=>'id_cash_flow_type',"display_field"=>"cash_flow_type.description as chart_dessc"],
            ["field"=>"sub_type","label"=>"Sub-type","input"=>"input","type"=>"text","required"=>false],
            ["field"=>"description","label"=>"Description","input"=>"input","type"=>"text","required"=>true],
            ["field"=>"order","label"=>"Ordering","input"=>"input","type"=>"number","required"=>true],
           
        ];        
    }
    return $data;
}
}
