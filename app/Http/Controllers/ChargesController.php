<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\CredentialModel;
use App\MySession as MySession;
class ChargesController extends Controller
{
    public function index(){
        $data['credential']= CredentialModel::GetCredential(MySession::myPrivilegeId());
        if(!$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
        $data['charges_list'] = DB::table('charges_group')
        ->select(DB::raw("id_charges_group,name,description,if(active=0,'Inactive','Active') as active,DATE_FORMAT(date_created,'%M %d, %Y %r') as date_created"))
        ->orDerby('id_charges_group','DESC')
        ->get();

        $data['head_title'] = "Loan Fees & Charges";
        return view('charges.index',$data);

        return $data;
    }
    public function create(){
        $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/charges');
        if(!$data['credential']->is_create){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
        $data['opcode'] = 0;
        $data['loan_fees'] = DB::table('loan_fees')->where('visible',1)->get();
        $data['fee_calculations'] = DB::table('fee_calculation')->get();
        $data['calculated_fee_base'] = DB::table('calculated_fee_base')->get();
        $data['head_title'] = "Add Loan Fees & Charges";
        return view('charges.charges_form',$data);
    }
    public function post(Request $request){
        if($request->ajax()){
            $parent_field = $request->parent_charges;
            $charges = $request->charges;
            $opcode = $request->opcode;
            $submit_via = $request->submit_via;
            $id_charges_group = $request->id_charges_group;
            $deleted = $request->deleted ?? [];
            $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/charges');



            if($opcode == 0 || $submit_via == 2){
                if(!$data['credential']->is_create){
                    $data['RESPONSE_CODE'] = "CREDENTIAL_ERROR";
                    $data['message'] = "You dont have a privilege to save this";
                    return response($data);
                }                
                // return $charges;
                DB::table('charges_group')
                ->insert($parent_field);

                $id_charges_group = DB::table('charges_group')->max('id_charges_group');            
            }else{
                if(!$data['credential']->is_edit){
                    $data['RESPONSE_CODE'] = "CREDENTIAL_ERROR";
                    $data['message'] = "You dont have a privilege to save this";
                    return response($data);
                }
                DB::table('charges_group')
                ->where('id_charges_group',$id_charges_group)
                ->update($parent_field);     

            //     DB::table('charges')
            //     ->where('id_charges_group',$id_charges_group)
            //     ->delete();   
            }

            $charges_object = array();

            foreach($charges as $charge){
                $temp = array();
                foreach($charge as $key=>$val){
                    $temp[$key] = $val;
                }
                $temp['id_charges_group'] = $id_charges_group;

                $id_charges = $temp['id_charges'] ?? 0;

                $range = $temp['ranges'] ?? 0;

                if($temp['with_range'] == 1){
                    $temp['value'] = 0;
                }

                unset($temp['id_charges']);
                unset($temp['ranges']);

                if($id_charges == 0 || $submit_via == 2){
                    //insert
                    DB::table('charges')
                    ->insert($temp);

                    $id_charges = DB::table('charges')->max('id_charges');
                }else{
                    DB::table('charges')
                    ->where('id_charges',$id_charges)
                    ->where('id_charges_group',$id_charges_group)
                    ->update($temp);

                    DB::table('charges_range')
                    ->where('id_charges',$id_charges)
                    ->delete();
                }

                if($temp['with_range'] == 1){
                    for($x=0;$x<count($range);$x++){
                        $range[$x]['id_charges'] = $id_charges;
                    }

                    DB::table('charges_range')
                    ->insert($range);
                }

            }

            DB::table('charges')
            ->whereIn('id_charges',$deleted)
            ->where('id_charges_group',$id_charges_group)
            ->delete();



            $data['RESPONSE_CODE'] = "success";
            $data['id_charges_group'] = $id_charges_group;

            return $data;
        }
    }
    public function view($id_charges_group){
        $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/charges');

        if(!$data['credential']->is_view && !$data['credential']->is_edit){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }


        $data['opcode'] = 1;
        $data['loan_fees'] = DB::table('loan_fees')->where('visible',1)->get();
        $data['fee_calculations'] = DB::table('fee_calculation')->get();
        $data['calculated_fee_base'] = DB::table('calculated_fee_base')->get();

        $data['details'] = DB::table('charges_group')->where('id_charges_group',$id_charges_group)->first();
        $data['charges'] = DB::table('charges')->select('*',DB::raw("if(id_fee_calculation=1,if(value-TRUNCATE(value,0) >0,value,FLOOR(value)),FORMAT(value,2)) as value"))->where('id_charges_group',$id_charges_group)->orderBy('id_charges')->get();

 
        $range = DB::select("SELECT cr.id_charges,minimum ,maximum,cr.value  FROM charges_range as cr
        LEFT JOIN charges as c on c.id_charges = cr.id_charges
        WHERE c.id_charges_group = ?;",[$id_charges_group]);

        $g = new GroupArrayController();
        $data['range'] = $g->array_group_by($range,['id_charges']);

        $data['head_title'] = "Loan Fees & Charges - ".$data['details']->name;
        return view('charges.charges_form',$data);
    }
    public function seach_charges(Request $request){
        if($request->ajax()){
            $search = $request->term;   
            if(strlen($search) < 3){
                $data['accounts'] = array();
                return response($data);
            }
            $data['accounts'] = DB::table('charges_group')
            ->select(DB::raw("name as tag_value,id_charges_group as tag_id"))
            ->where(function ($query) use ($search){
                $query->where(DB::raw("name"), 'like', "%$search%");
            })
            ->where('active',1)
            ->get();
            return response($data);
        }
    }
}
