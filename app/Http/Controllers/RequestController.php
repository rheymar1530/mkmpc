<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\MySession as MySession;
use App\CredentialModel;
use DateTime;
use Session;
class RequestController extends Controller
{
	public function index(Request $request){
        if(isset($request->filter_data)){
            $filter_data = json_decode($request->filter_data,true);
            $data['request_list'] = $this->filter_data($filter_data);
        }else{
            $filter_data = array('status'=>0,'request_type'=>0,'opcode'=>1);
            $data['request_list'] = $this->filter_data( $filter_data);
        }

        $data['filter_data'] = json_encode($filter_data);

        // return $filter_data;
        $data['message_type'] =  Session::get('message');
        $data['credential']= CredentialModel::GetCredential(MySession::myPrivilegeId());
        $data['request_types'] = DB::table('request_type')->get();
        $data['current_date'] = MySession::current_date();
        if(!$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
      
		$data['title'] = "Request";
		$data['breadcrumbs'] = array(
			'Request' => '#'
		);
		
		return view('request.request_list',$data);
	}
    public function add_view(){
        $data['message_type'] =  Session::get('message');
        $data['credential']= CredentialModel::GetCredential(MySession::myPrivilegeId());
        if(!$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
    	$data['breadcrumbs'] = array(
			'Request' => '/admin/request/index',
			'Create Request'=> '#'
		);

    	$data['title'] = 'Create Request';
    	$data['opcode'] = 0;
    	$data['request_types'] = DB::table('request_type')->get();
    	$data['current_date'] = MySession::current_date();
    	return view('request.request_form',$data);
    }
    public function edit_view(Request $request){
        $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/admin/request/index');
        if(!$data['credential']->is_read){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
        
    	if(isset($request->id_request)){
            // return $request->return_url;
    		$data['details'] = DB::table('tbl_request')
				  ->select('tbl_request.*',DB::raw('concat(employee_information.first_name," ",employee_information.last_name) as requested_by,if(tbl_request.status=0,"Draft",if(tbl_request.status=1,"Confirmed","Cancelled")) as status_name'))
				  ->leftJoin('hrms.employee_information','employee_information.id','=','tbl_request.id_employee')
				  ->where('id_request',$request->id_request)
				  ->first();
			if($data['details'] == null){
				return back()->withInput();
			}
			$data['breadcrumbs'] = array(
				'Request' =>  isset($request->return_url)?$request->return_url:url()->previous(),
				'View Request'=> '#'
			);
    		$data['title'] = 'View Request ('.$request->id_request.')';
    		$data['opcode'] = 1;
    		$data['request_types'] = DB::table('request_type')->get();

    		$data['current_date'] = $data['details']->date;
    		$data['items'] = DB::table('tbl_request_details as r_item')
    						 ->select('r_item.id_request_details','r_item.id_item','r_item.quantity','item.name as item_name','r_item.id_uom',DB::raw("get_item_uoms(r_item.id_item) as uom_list"),'r_item.remarks',
                            DB::raw('ifnull(concat(quantity_approved," - ",uom_app.abbr),"-") as approved_details'))
    						 ->leftJoin('item','item.id','r_item.id_item')
                             ->leftjoin('uom as uom_app','uom_app.id_uom','r_item.id_uom_approved')
    						 ->where('id_request',$request->id_request)
    						 ->get();	
    		return view('request.request_form',$data);
    	}else{
    		return back()->withInput();
    	}
    }
    public function post_request(Request $request){
    	if($request->ajax()){
    		$request_item = array();
    		$opcode = $request->opcode;

    		if($opcode == 0){
	    		DB::table('tbl_request')
	    		->insert([
	    			'date' => $request->date,
	    			'requested_by' => $request->requested_by,
	    			'id_type' => $request->request_type,
	    			'remarks' => $request->remarks,
	    			'status' => 0,
	    			'reason' => $request->reason,
	    			'id_user' => MySession::myId(),
	    			'id_employee' => $request->requested_by
	    		]);
	    		$id_request = DB::table('tbl_request')->max('id_request');
    		}else{
    			$id_request = $request->id_request;
    			DB::table('tbl_request')
    			->where('id_request',$id_request)
    			->update([
    				'date' => $request->date,
	    			'requested_by' => $request->requested_by,
	    			'id_type' => $request->request_type,
	    			'remarks' => $request->remarks,	
	    			'reason' => $request->reason,
	    			'id_employee' => $request->requested_by
    			]);
    			DB::table('tbl_request_details')
    			->where('id_request',$id_request)
    			->delete();
    		}
    		for($i=0;$i<count($request->id_item);$i++){
    			$request_item[] = [
    				'id_request'=>$id_request,
    				'id_item' => $request->id_item[$i],
    				'quantity' => $request->quantity[$i],
    				'quantity_approved'=> 0,
    				'remarks' =>$request->item_remarks[$i],
    				'id_uom'=>$request->id_uom[$i]
    			];
    		}

    		DB::table('tbl_request_details')
    		->insert($request_item);

    		$data['message'] = "success";
    		$data['id_request'] = $id_request;
    		
    		return response($data);

    	}
    }
    public function post_status(Request $request){
    	if($request->ajax()){
            $request_details = DB::table('tbl_request')->where('id_request',$request->id_request)->first();
            DB::table('tbl_request')
            ->where('id_request',$request->id_request)
            ->update([
                'status' => $request->status,
                'date_status' => $request->date_confirm
            ]);
			if($request->status == 1){
				$update_object = array();
				for($i=0;$i<count($request->id_request_details);$i++){
					$update_object[$request->id_request_details[$i]] = [
						'id_uom_approved' => $request->uom_confirm[$i],
						'quantity_approved' => $request->quantity_approved[$i],
						'remarks' =>$request->remarks_confirm[$i]
					];
				}
				foreach($update_object as $key => $data){
					DB::table('tbl_request_details')
					->where('id_request',$request->id_request)
					->where('id_request_details',$key)
					->update($data);
				}
                //Insert to purchase request
                if($request_details->id_type == 1){
                    $this->insert_purchase_request($request->id_request);
                }
			}
			$data['message'] = "success";
			return $data;
    	}
    }

    public function insert_purchase_request($id_request){
        DB::table('tbl_purchase_request')
        ->insert([
            'id_request'=>$id_request,
            'status'=>0
        ]);
        $max_id = DB::table('tbl_purchase_request')->max('id_purchase_request');
        DB::select("INSERT INTO tbl_purchase_request_details (id_purchase_request,id_request_details,id_item,quantity,id_uom)
                    SELECT $max_id,id_request_details,id_item,quantity_approved,id_uom_approved
                    FROM tbl_request_details
                    WHERE id_request=$id_request;");
    }
    public function filter(Request $request){
        if($request->ajax()){
            $filter_data = $request->filter_data;
            $opcode = $request->opcode;

            // return $filter_data;
            $data['data'] = $this->filter_data($filter_data);

            return $data;
            return response($filter_data);
        }
    }
    public function filter_data($filter_data){
        $where="";
        switch ($filter_data['opcode']) {
            case '1':
                $where = " req.status =".$filter_data['status'];
                # code...
                break;
            case '2':
                $where = " req.date >='".$filter_data['start_date']."' AND req.date <= '".$filter_data['end_date']."'";
                break;
            case '3':
                $where = " req.date_status >='".$filter_data['start_date']."' AND req.date_status <= '".$filter_data['end_date']."'";
                break;
            
            default:
                # code...
                break;
        }
        $req_type = ($filter_data['request_type'] == 0)?"":"AND req.id_type=".$filter_data['request_type'];

        $sql = "SELECT id_request,date,concat(first_name,' ',last_name) as requested_by,
                req_t.description as request_type,req.remarks,
                if(req.status=0,'Draft',if(req.status=1,'Confirmed','Cancelled')) as status
                FROM tbl_request req
                LEFT JOIN hrms.employee_information as emp on emp.id = req.id_employee
                LEFT JOIN request_type req_t on req.id_type = req_t.id_type
                WHERE $where $req_type
                ";

        return DB::select($sql);
        // return $sql;
    }   
    public function employee_list(Request $request){
    	$data['items'] = DB::table('hrms.employee_information')
    					->select('id as tag_id',DB::raw('concat(first_name," ",last_name) as tag_value'))
    					->whereRaw("concat(first_name,' ',last_name) like '%$request->term%'")
    					->limit(20)
    					->get();
    	return $data;

    }
    public function search_item(Request $request){
    	$search = $request->search;
    	$product = DB::table('item')
        ->where('name','like','%'.$search.'%')
        ->get();
        $out = "";
        if(count($product) > 0){
        	foreach($product as $prod){
        		$out .= "<option data-value='$prod->id' value='$prod->name'>";
        	}
        }else{
        	$out .= '<option data-value="x" value="Item not found ('.$search.')">';
        }
        return $out;
    }
    public function get_uom(Request $request){
    	if($request->ajax()){
    		$id_item = $request->id_item;
			$uom = DB::select('SELECT uom.description,uom.id_uom,uom.abbr from item_uom
					   LEFT JOIN uom on uom.id_uom = item_uom.id_uom where id_item ='.$id_item);
			$out = "";
			foreach($uom as $row){
				$out.='<option value="'.$row->id_uom.'">'.$row->abbr.'</option>';
			}
			return $out;
    	}
    }
}
