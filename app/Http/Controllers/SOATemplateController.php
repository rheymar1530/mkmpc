<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\DBModel;
use App\MySession as MySession;
use App\CredentialModel;
class SOATemplateController extends Controller
{
	private $default_template =69;
	public function sql_connection(){
		return DBModel::server_tat();
	}
	public function index(){
		$data['credential']= CredentialModel::GetCredential(MySession::myPrivilegeId());
		if(!$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
		$data['template_list'] = DB::connection($this->sql_connection())
								->table('lse_new_reports.tbl_report')
								->select('id_report','name','description','remarks',DB::raw("if(orientation=0,'Portrait','Landscape') as orientation"),'created_at','enc_id')
								->get();
		$data['default_template'] = $this->default_template;
		return view('soa_template.index',$data);
		return $data;
	}
    public function create_view(){
    	$data['credential']= CredentialModel::GetCredential(MySession::myPrivilegeId());
    	if(!$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
    	// echo "<script>window.close();</script>";
    	$data['fields'] = DB::connection($this->sql_connection())
    					 ->table('lse_new_reports.tbl_field')
    					 ->select('id_field','alias',DB::raw("0 as selected,0 as groupings"),'is_summarize')
    					 ->whereIn('id_table',[1,7,5,8])
    					 ->orderby('alias')
    					 ->get();

    	$data['op'] = 0;
    	
    	return view('soa_template.soa_template_form',$data);
    }
    public function edit_view(Request $request){
    	$data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/admin/soa_template/index');

    	if(!$data['credential']->is_read && !$data['credential']->is_edit){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }


    	$id_report = $request->id_report;

    	$data['op'] = 1;
		$data['details'] = DB::connection($this->sql_connection())
				 ->table('lse_new_reports.tbl_report')
				 ->where('id_report',$id_report)
				 ->first();
		$data['title'] = $data['details']->name;
		if($data['details'] == null){ // if invalid request

		}
    	$data['fields'] = DB::connection($this->sql_connection())
						->Select("SELECT field.id_field,field.alias,if(t_field.id_field is null,0,1) as selected,if(groupings.id_field is null,0,1) as groupings,custom_col,t_field.order as sel_order,groupings.order as group_order,field.is_summarize FROM lse_new_reports.tbl_field as field
						LEFT JOIN lse_new_reports.tbl_report_fields as t_field on t_field.id_field = field.id_field AND id_report =$id_report
						LEFT JOIN lse_new_reports.tbl_groupings as groupings on groupings.id_field = field.id_field AND groupings.id_report =$id_report
						WHERE field.id_table in (1,7,5,8)
						ORDER BY alias;");

		$data['selected'] = DB::connection($this->sql_connection())
							->select("SELECT t_field.id_field,alias,custom_col,t_field.order,t_field.is_sum
									FROM lse_new_reports.tbl_report_fields as t_field
									LEFT JOIN lse_new_reports.tbl_field as field on field.id_field = t_field.id_field
									WHERE id_report = $id_report
									ORDER by id_report_fields;");

		$data['groupings'] = DB::connection($this->sql_connection())
							 ->select("SELECT field.id_field,alias,groupings.order
										FROM lse_new_reports.tbl_groupings as groupings
										LEFT JOIN lse_new_reports.tbl_field as field on field.id_field = groupings.id_field
										WHERE id_report = $id_report
										ORDER by id_groupings");
		// return $data['selected'];
    	// $field = DB::connection($this->sql_connection())
					// 		->Select("SELECT field.id_field,field.alias,if(t_field.id_field is not null,1,if(groupings.id_field is not null,2,0)) as type ,if(t_field.id_field is null,0,1) as selected,if(groupings.id_field is null,0,1) as groupings,custom_col,t_field.order as sel_order,groupings.order as group_order FROM lse_new_reports.tbl_field as field
					// 		LEFT JOIN lse_new_reports.tbl_report_fields as t_field on t_field.id_field = field.id_field AND id_report =$id_report
					// 		LEFT JOIN lse_new_reports.tbl_groupings as groupings on groupings.id_field = field.id_field AND groupings.id_report =$id_report
					// 		ORDER BY alias;");
		// $g = new GroupArrayController();
		// $field_group = $g->array_group_by($field,['type']);
    	return view('soa_template.soa_template_form',$data);
    	// return $data;
    }
    public function post(Request $request){
    	if($request->ajax()){
    		$select_fields = $request->select_fields;
    		$group_fields = $request->group_fields;
    		$insert_group = array();
    		$opcode = $request->opcode;
    		$id_report = $request->id_report;
    		$orientation = $request->orientation;

    		if($opcode == 0){
	    		DB::connection($this->sql_connection())
				->table('lse_new_reports.tbl_report')
				->insert([
					'name' => $request->rpt_name,
					'description' => $request->rpt_desc,
					'remarks' => $request->rpt_remarks,
					'orientation'=>$orientation,
					'id_user' => MySession::myId()
				]);
	    		$max_id =   DB::connection($this->sql_connection())
	    					->table('lse_new_reports.tbl_report')
	    					->max('id_report');
	    		$id_report = $max_id;
    		}else{
    			DB::connection($this->sql_connection())
				->table('lse_new_reports.tbl_report')
				->where('id_report',$id_report)
				->update([
					'name' => $request->rpt_name,
					'description' => $request->rpt_desc,
					'remarks' => $request->rpt_remarks,
					'orientation'=>$orientation
				]);
				//Delete tbl_report_fields
				DB::connection($this->sql_connection())
				->table('lse_new_reports.tbl_report_fields')
				->where('id_report',$id_report)
				->delete();
				//Delete tbl_groupings
				DB::connection($this->sql_connection())
				->table('lse_new_reports.tbl_groupings')
				->where('id_report',$id_report)
				->delete();
    		}
    		foreach($select_fields as $select){
    			$insert_select[] = [
    				'id_report' => $id_report,
    				'id_field' => $select['id_field'],
    				'is_sum' => $select['summarize'],
    				'order' => $select['order'],
    				'custom_col' => $select['custom_col']
    			];
    		}

			DB::connection($this->sql_connection())
			->table('lse_new_reports.tbl_report_fields')
			->insert($insert_select);

			if($group_fields  != ""){
				foreach($group_fields as $group){
	    			$insert_group[] = [
	    				'id_report' => $id_report,
	    				'id_field' => $group['id_field'],
	    				'order'=> $group['order']
	    			];
    			}
    			DB::connection($this->sql_connection())
				->table('lse_new_reports.tbl_groupings')
				->insert($insert_group);
			}

			$data['message'] = "success";
			$data['id_report'] = $id_report;

			return response($data);
    	}
    }
    public function assign_template($id){
    	$data['report_details'] = DB::connection($this->sql_connection())
    							 ->table('lse_new_reports.tbl_report')
    							 ->where('enc_id',$id)
    							 ->first();
    	$data['default_template'] = $this->default_template;



		$data['selected'] = DB::connection($this->sql_connection())
					->select("SELECT t_field.id_field,alias,custom_col,t_field.order,t_field.is_sum
							FROM lse_new_reports.tbl_report_fields as t_field
							LEFT JOIN lse_new_reports.tbl_field as field on field.id_field = t_field.id_field
							LEFT JOIN lse_new_reports.tbl_report t on t.id_report = t_field.id_report
							WHERE enc_id = '$id'
							ORDER by id_report_fields;");

		$data['groupings'] = DB::connection($this->sql_connection())
							 ->select("SELECT field.id_field,alias,groupings.order
										FROM lse_new_reports.tbl_groupings as groupings
										LEFT JOIN lse_new_reports.tbl_field as field on field.id_field = groupings.id_field
										LEFT JOIN lse_new_reports.tbl_report t on t.id_report = groupings.id_report
										WHERE enc_id = '$id'
										ORDER by id_groupings");
		$data['client_list'] = DB::connection($this->sql_connection())
							   ->table('lse.tbl_client_profile')
							   ->select('id_client_profile','account_no','name')

							   ->where('id_soa_template',$data['report_details']->id_report)
							   ->Orwhere(function($query) use ($data){
							   		if($data['report_details']->id_report == $data['default_template']){
							   			$query->where('id_soa_template',0);
							   		}
							   })
							   ->get();


		// return $data['client_list'];
	
    	return view('soa_template.assign_client',$data);
    }
    public function post_validate(Request $request){
    	if($request->ajax()){
    		$id_client_profile = $request->account;
    		$opcode =  $request->opcode;
    		$id_report = $request->id_report;
    		$check_report = [0,$this->default_template];
    
    		$validated_report = DB::connection($this->sql_connection())
							->table('lse.tbl_client_profile')
							->select('id_soa_template',DB::raw("concat(account_no,' - ',tbl_client_profile.name) as account_name,rep.name as report_name"))
							->leftJoin('lse_new_reports.tbl_report as rep','rep.id_report','tbl_client_profile.id_soa_template')
							->where('tbl_client_profile.id_client_profile',$id_client_profile)
							->first();
			$data['id_report'] = $validated_report->id_soa_template;
			$data['account_name'] = $validated_report->account_name;
			
			if($opcode == 1){//POST UPDATE
				$data['STATUS_CODE'] = "POST_UPDATE";
				$this->post_update($id_client_profile,$id_report);
				return response($data);
			}elseif($opcode == 3){ //POST DELETE
				$data['STATUS_CODE'] = "REMOVE_CLIENT_REPORT";
				$this->post_update($id_client_profile,0);
				// DB::connection($this->sql_connection())

				return response($data);
			}
			if(in_array($validated_report->id_soa_template,$check_report)){
				$data['STATUS_CODE'] = "POST_DIRECTLY";
				$this->post_update($id_client_profile,$id_report);
			}else{
				if($validated_report->id_soa_template == $id_report){
					$data['STATUS_CODE'] = "NO_CHANGES";
				}else{
					$data['STATUS_CODE'] = "OTHER_REPORTS";
					$data['REPORT_NAME'] = $validated_report->report_name;
				}
			}

			return $data;
    	}
    }
    public function post_update($id_client_profile,$id_report){
    	// return 1;
		DB::connection($this->sql_connection())
		->table('lse.tbl_client_profile')
		->where('id_client_profile',$id_client_profile)
		->update(['id_soa_template'=>$id_report]);
    }
}
