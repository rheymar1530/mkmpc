<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Loan;
use DateTime;
use App\JVModel;
use App\CRVModel;
use App\WebHelper;
use App\CredentialModel;
use App\MySession;

class TermConditionController extends Controller
{
	public function index(Request $request){
        $data['credential']= CredentialModel::GetCredential(MySession::myPrivilegeId());
        if(!$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }   
		$data['terms_condition'] = DB::table('terms_condition')
								  ->select(DB::raw("id_terms_condition,description,DATE_FORMAT(date_created,'%m/%d/%Y') as date_created"))
								  ->orderBy('id_terms_condition','DESC')
								  ->get();
		return view('term_condition.index',$data);
		dd($data);
	}
	public function create(Request $request){
		$data['opcode'] = 0;
		return view('term_condition.form',$data);
	}
	public function view($id_terms_condition,Request $request){
		$data['details'] = DB::table('terms_condition')->where('id_terms_condition',$id_terms_condition)->first();
		$data['terms'] = DB::table('terms_conditions_details')->where('id_terms_condition',$id_terms_condition)->get();
		$data['opcode'] = 1;

		return view('term_condition.form',$data);
		dd($data);
	}
	public function post(Request $request){
		$opcode = $request->opcode ?? 0;
		$terms = $request->terms ?? [];
		$id_terms_condition = $request->id_terms_condition ?? 0;



		if(count($terms) == 0){
			$data['RESPONSE_CODE'] = "ERROR";
			$data['message'] = "Please select at least 1 term";
			return response($data);
		}

		if($opcode == 0){
			DB::table('terms_condition')
			->insert(['description'=>$request->description]);

			$id_terms_condition = DB::table('terms_condition')->MAX('id_terms_condition');
		}else{
			DB::table('terms_condition')
			->where('id_terms_condition',$id_terms_condition)
			->update(['description'=>$request->description]);

			DB::table('terms_conditions_details')->where('id_terms_condition',$id_terms_condition)->delete();
		}

		$termOBJ = array();
		foreach($terms as $t){
			$termOBJ[] = [
				'min_cbu' => $t['min_cbu'],
				'max_cbu' => $t['max_cbu'],
				'min_principal' => $t['min_principal'],
				'max_principal' => $t['max_principal'],
				'up_to_terms' => $t['up_to_terms'],
				'id_terms_condition'=>$id_terms_condition
			];
		}

		DB::table('terms_conditions_details')
		->insert($termOBJ);


		$data['RESPONSE_CODE'] = "SUCCESS";
		$data['message'] = "Term Condition Successfully Saved";
		$data['id_terms_condition'] = $id_terms_condition;

		return response($data);
	}
}