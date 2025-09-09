<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Loan;
use App\Member;
use App\MySession;
use DB;
use App\CredentialModel;
class SupplierController extends Controller
{

    // 'address','contact_no','email','id_supplier_type','name','supplier_code','tin_no'


    private $required_key_text = ['address','contact_no','email','id_supplier_type','name','supplier_code','tin_no'];
    public function search_supplier(Request $request){
        if($request->ajax()){
            $search = $request->term;   
            if(strlen($search) < 3){
                $data['accounts'] = array();
                return response($data);
            }
            $data['accounts'] = DB::table('supplier')
            ->select(DB::raw("name as tag_value,id_supplier as tag_id"))
            ->where(function ($query) use ($search){
                $query->where(DB::raw("supplier.name"), 'like', "%$search%");
            })
            ->get();
            return response($data);
        }
    }

    public function index(Request $request){
        $data['credential']= CredentialModel::GetCredential(MySession::myPrivilegeId());
        if(!$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
        $data['supplier_list'] = DB::table('supplier as s')
                                 ->select(DB::raw("s.id_supplier,s.name,s.address,s.contact_no,s.email,s.supplier_code,st.description"))
                                 ->leftJoin('supplier_type as st','st.id_supplier_type','s.id_supplier_type')
                                 ->get();
        return view('supplier.index',$data);
    }
    public function create(){
        $data['type'] = DB::table('supplier_type')->get();
        $data['opcode'] = 0;

        return view('supplier.supplier_form',$data);

    }

    public function view($id_supplier){
        $data['opcode'] = 1;
        $data['type'] = DB::table('supplier_type')->get();
        $data['supplier_details'] = DB::table('supplier')
                                    ->where('id_supplier',$id_supplier)
                                    ->first();  



        return view('supplier.supplier_form',$data);      
        return $data;
    }
    public function post(Request $request){
        if($request->ajax()){

            $supplier_info = $request->supplier_data;
   
            $id_supplier  = $request->id_supplier;
            $opcode = $request->opcode;




      
            $validator = $this->validate_input($supplier_info);
            // return $validator;
            if(!$validator['valid']){
                $data['RESPONSE_CODE'] = "INVALID_INPUT";
                $data['message'] = "Missing Mandatory Fields";
                $data['invalid_details'] = $validator;

                return response($data);
            }


            // $test = array();
            // foreach($supplier_info as $key=>$val){
            //     array_push($test,$key);
            // }

            // return "'".implode("','",$test)."'";


            if($opcode == 0){ //insert
                DB::table('supplier')
                ->insert($supplier_info);
                $id_supplier = DB::table('supplier')->max('id_supplier');
            }else{ // update
                DB::table('supplier')
                ->where('id_supplier',$id_supplier)
                ->update($supplier_info);
            }


            $data['RESPONSE_CODE'] = "SUCCESS";
            $data['id_supplier'] = $id_supplier;

            return response($data);
            return response($request);
        }
    }
    public function validate_input($supplier_info){
        $required_fields_text = $this->required_key_text;

        $valid = true;

        $invalid_fields = array();
        foreach($required_fields_text as $key){
            if(!isset($supplier_info[$key]) || $supplier_info[$key] == ""){
                array_push($invalid_fields,$key);
                $valid = false;
            }
        }

        $output['valid'] = $valid;
        $output['invalid_fields'] = $invalid_fields;


        return $output;

    }

}
