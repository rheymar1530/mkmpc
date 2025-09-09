<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\MySession as MySession;
use App\CredentialModel;
use App\DBModel;
use DB;
use Dompdf\Dompdf;
use DNS2D;
use DNS1D;
use PHPExcel; 
use PHPExcel_IOFactory;
use App\Mail\SendSOAMail;
use App;
use Response;
use PDF;
use Storage;
use File;


class SOAController extends Controller
{
	private $default_template =69;
	public function sql_connection(){
		return DBModel::lse_db();
	}
	public function current_date(){
		return MySession::current_date();
	}
	public function close_tab(){
		return view('blank');
		echo "<script>window.open('', '_self', '').close();</script>";
	}
	public function test_mail(){
		return new SendSOAMail(45);
		Mail::send(new SendSOAMail(45));
		return 123;
	}
	public function check_account_branch($credential,$id_client_profile){
		if($credential->is_read == 1){
			return true;
		}

		$count = DB::connection($this->sql_connection())
				 ->table('tbl_client_profile')
				 ->where('id_branch',MySession::myBranchID())
				 ->where('id_client_profile',$id_client_profile)
				 ->count();
		return ($count > 0)?true:false;
	}
	public function update_soa_status($access_token){
		sleep(5);
		echo "YES";
		echo "WOW";
		return;
		// return redirect()->away('https://libcap.com.ph/');
		$data = DB::connection($this->sql_connection())
				->table('tbl_statement_control')
				->where('access_token',$access_token)
				->first();
		if($data == null){
			$response['message'] = "Invalid Control";
			return $response;
		}else{
			$response['message'] = "Record Found";
			DB::connection($this->sql_connection())
			->table("tbl_statement_control")
			->where('control_number',$data->control_number)
			->update(['status'=>1]);
		}
	}
	public function soa_index(Request $request){
		
		$data['credential']= CredentialModel::GetCredential(MySession::myPrivilegeId());
		if(!$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }

        $data['current_date'] = MySession::current_date();

        $data['years'] = array();
        $data['months'] = ["January","February","March","April","May","June","July","August","September","October","November","December"];

        /*****VALIDATE LINK QUERY STRING*******/
        $data['sel_all'] = (isset($request->sel_all))?$request->sel_all:0;
        $data['account_no'] = (isset($request->account_no))?$request->account_no:0;
        $data['sel_billing'] = (isset($request->sel_billing))?$request->sel_billing:2;
        $data['billing_start'] = (isset($request->billing_start))?$request->billing_start:$this->current_date();
        $data['billing_end'] = (isset($request->billing_end))?$request->billing_end:$this->current_date();
        $data['sel_bill_month'] = (isset($request->sel_bill_month))?$request->sel_bill_month:strval(date("n", strtotime($data['current_date'])));


        $data['sel_bill_year'] = (isset($request->sel_bill_year))?$request->sel_bill_year:date("Y", strtotime($data['current_date']));

     	
        $data['selected_account'] = DB::connection($this->sql_connection())
        							->table('tbl_client_profile')
        							->select(DB::raw("concat(account_no,' || ',name) as tag_value,id_client_profile as tag_id"))
        							->where('id_client_profile',$data['account_no'])
        							->first();
        $data['dt'] = $data['sel_bill_year']."-".$data['sel_bill_month']."-01";
        // return ;

        // return $data['fil_start'];

        for($i=2021;$i<=2050;$i++){
        	$data['years'][$i] = $i;
        }

		$data['soa_list'] = DB::connection($this->sql_connection())
							->table('tbl_statement_control as ts')
							->select(DB::raw("access_token,DATE_FORMAT(statement_start_date,'%M') as group_month,if(ts.control_number > 1000000,ts.control_number,LPAD(ts.control_number, 7, 0)) as control_number,concat(DATE_FORMAT(statement_start_date,'%m/%d/%Y'),' - ',DATE_FORMAT(statement_end_date,'%m/%d/%y')) as billing_period,tp.name as account_name,DATE_FORMAT(statement_date,'%m/%d/%Y') as statement_date,ts.account_no,DATE_FORMAT(DATE_ADD(statement_date, INTERVAL 30 DAY),'%m/%d/%Y') as due_date,lse.DecodeSoaStatus(ts.status) as status,ts.status as status_code,ifnull(GETCostCenters(control_number),'-') as cost_centers,if(attachment_status=0,'None',if(attachment_status=1,'Processing','Generated')) as attachment"))
							->leftJoin('tbl_client_profile as tp','tp.account_no','ts.account_no')
							->where(function($query) use ($data){
								if(!$data['credential']->is_read){
									$query->where('tp.id_branch',MySession::myBranchID());
								}
							})
							->where(function($query) use ($data){
								if($data['sel_all'] == 0){
									$query->where('tp.id_client_profile', '=', $data['account_no']);
								}
				            })
				            ->where(function($query) use ($data){
								if($data['sel_billing'] == 1){
									$query->where([
													['statement_start_date','>=',$data['billing_start']],
													['statement_start_date','<=',$data['billing_end']]
												 ])
									->orWhere([
												['statement_end_date','>=',$data['billing_start']],
												['statement_end_date','<=',$data['billing_end']]
											]);									
								}else{
									$query->where([
													['statement_start_date','>=',date("Y-m-01", strtotime($data['dt']))],
													['statement_start_date','<=',date("Y-m-t", strtotime($data['dt']))]
												 ])
									->orWhere([
												['statement_end_date','>=',date("Y-m-01", strtotime($data['dt']))],
												['statement_end_date','<=',date("Y-m-t", strtotime($data['dt']))]
											]);	
								}
				            })
							->get();
		return view('soa.soa_index',$data);
	}
    public function generate_soa(Request $request){
    	$data['credential']= CredentialModel::GetCredential(MySession::myPrivilegeId());

		if(!$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
        if(isset($request->id_account)){
        	$valid_account = $this->check_account_branch($data['credential'],$request->id_account);
        	if($valid_account){
        		$data['default_account'] = DB::connection($this->sql_connection())
        								   ->table('tbl_client_profile')
        								   ->select('id_client_profile','account_no','name')
        								   ->where('id_client_profile',$request->id_account)
        								   ->first();
        	}
        }
        // return $data;
       	
    	$g = new GroupArrayController();
    	$data['current_date'] = MySession::current_date();

    	return view('soa.generate_soa',$data);
    }
    public function search_account(Request $request){

    	$search = $request->term;	
    	$data['accounts'] = DB::connection($this->sql_connection())
    					    ->table('lse.tbl_client_profile')
    					    ->select(DB::raw("concat(account_no,' || ',name) as tag_value,id_client_profile as tag_id"))
				    		->where(function ($query) use ($search){
				                $query->where('name', 'like', "%$search%")
				                      ->orWhere('account_no', 'like', "%$search%");
				            })
				            // ->where('id_branch',MySession::myBranchID())
    					   // ->WhereRaw("name like '%$search%'")
    					   // ->orWhereRaw("account_no like '%$search%'")
    					   ->get();
    	return response($data);
    }
    public function parse_cost_center(Request  $request){
    	$id_client_profile = $request->id_client_profile;
    	$data['cost_centers'] = DB::connection($this->sql_connection())
    							->table('lse.tbl_client_cost_center')
    							->select('id_tbl_client_cost_center',DB::raw('concat(department," - ",reference_number) as description'))
    							->where('id_client_profile',$id_client_profile)
    							->get();

    	return response($data);
    }
    public function get_data_preview(Request $request){
    	if($request->ajax()){
    		$g = new GroupArrayController();
    		$start_date= $request->date_from;
    		$end_date = $request->date_to;
    		$account_no = $request->account_no;
    		$cost_center = $request->cost_center;
    		$add_where = "";

    		if(in_array(0, $cost_center)){
    			$add_where = " OR hawb_info.cost_center is null";
    		}
    		$imp_cost = implode(',', $cost_center);
			$sql = "SELECT ifnull(concat(department,' - ',reference_number),'') as cost_center,DATE_FORMAT(tc.transaction_date,'%m/%d/%Y') as transaction_date,tc.hawb_no
					,tc.destination,tc.description,tc.date_time_received,tc.received_by,tc.debit as amount
					FROM tbl_client_account tc
					LEFT JOIN tbl_client_cost_center cc on cc.id_tbl_client_cost_center = tc.cost_center
					WHERE (cost_center in ($imp_cost) $add_where) 
					AND transaction_date >= '$start_date' and transaction_date <= '$end_date' and tc.id_client_profile = $account_no
					ORDER BY cost_Center,transaction_date;";

			$raw_data = DB::connection($this->sql_connection())
									->select($sql);
			$data['preview_data'] = $g->array_group_by($raw_data,['cost_center']);
			// return $sql;

			return $data;
    		return response($cost_center);
    	}
    }
    public function preview_controller(Request $request){
    	$add_where  = "";

    	$data['client_details'] = DB::connection($this->sql_connection())
    								->table('tbl_client_profile')
    								->select('name','account_no','b_address as address',DB::raw("if(id_soa_template=0,$this->default_template,id_soa_template) as id_soa_template"))
    								->where('id_client_profile',$request->account_no)
    								->first();
    	
		// return $data;
    	$query =  $request->query();
    	$query['id_template'] = $data['client_details']->id_soa_template;
		$data['form_request'] = json_encode($query);

		// return $data;
    	// return $query;

    	$data['cost_center'] = DB::connection($this->sql_connection())
    							->table('tbl_client_cost_center')
    							->select(DB::raw("group_concat(concat(department,'-',reference_number)) as cost_center"))
    							->whereIn('id_tbl_client_cost_center',$request->cost_center)
    							->first();
    	$data['billing_period'] = date_format(date_create($request->date_from),'m/d/Y') ." - ".date_format(date_create($request->date_to),'m/d/Y');

		if(in_array(0, $request->cost_center)){
			$add_where = " OR hawb_info.cost_center is null";
		}
		
		$imp = implode(',', $request->cost_center);
		$where = "WHERE tbl_client_account.control_number =0 AND (hawb_info.cost_center in ($imp) $add_where ) AND hawb_info.shipment_date >= '$request->date_from' and hawb_info.shipment_date <= '$request->date_to' and tbl_client_account.id_client_profile = $request->account_no";
		
		$id_template = ($data['client_details']->id_soa_template == 0)?$this->default_template:$data['client_details']->id_soa_template;
		$dd = $this->parseReportOutputs($where,$id_template);

		foreach($dd as $key=>$rep){
			$data[$key] = $rep;
		}
		$data['previous_amt_due'] = $this->get_prev_amt_due($request->date_from,$data['client_details']->account_no);
		$data['payments'] = $this->get_less_payments($request->date_from,$request->date_to,$data['client_details']->account_no);

		$data['adjustments'] = 0;
		$data['grand_sum']['total'] = (isset($data['grand_sum']['total']) ? $data['grand_sum']['total']:0);

		$data['amount_due'] = $data['grand_sum']['total']-$this->parseSum($data['payments'],'amount')+$data['previous_amt_due']+$data['adjustments'];

    	return view('soa.preview_frame',$data);
    }
    public function post_soa(Request $request){
    	if($request->ajax()){
    		$form = $request->form_data;



    		$account_no = $form['account_no'];

    		// return $account_no;
    		$start_date = $form['date_from'];
    		$end_date = $form['date_to'];

    		// return response();
    		//Create SOA Control
    		DB::connection($this->sql_connection())
    		->table('tbl_statement_control')
    		->insert([
    			'id_template' => $form['id_template'],
    			'account_no' => $request->actual_account_no,
    			'statement_start_date' => $start_date,
    			'statement_end_date'=> $end_date,
    			'ispaid' => 0,
    			'access_token' =>DB::raw('concat(LEFT(MD5(NOW()), 32),LEFT(MD5(NOW()),13 ))'),
    			'statement_date' => MySession::current_date(),
    			'created_by' =>MySession::myId()
    		]);
    		$add_where="";
    		$control_number = DB::connection($this->sql_connection())
    		->table('tbl_statement_control')
    		->max('control_number');
    		if(in_array(0, $form['cost_center'])){
				$add_where = " OR tc.cost_center is null";
			}
    		$imp = implode(',', $form['cost_center']);

    		DB::connection($this->sql_connection())
    		->select("UPDATE tbl_client_account tc set tc.control_number = $control_number
    				 WHERE transaction_date >= '$start_date' AND transaction_date <= '$end_date'
    				 AND (cost_center in ($imp) $add_where) and control_number = 0 AND account_no=$request->actual_account_no");
    		$data['message'] = "success";
    		$data['control_number'] = $control_number;

    		return response($data);
    		return response($form_data);
    	}
    }
    public function post_status(Request $request){
    	if($request->ajax()){
    		$status = $request->status;
    		$reason = $request->reason;
    		$control_number = $request->control_number;
    		$data['message'] = "success";
    		$data['control_number'] = $control_number;
    		// return response($data);
    		//Insert to cancelled_soa
    		if($status == 10){
    			DB::connection($this->sql_connection())
    			->table('tbl_statement_control')
    			->where('control_number',$control_number)
    			->update([
    				'status' => $status,
    				'reason' => $reason,
    				'updated_by' => MySession::myId(),
    				'date_updated' => DB::raw('now()')
    			]);

    			DB::connection($this->sql_connection())
    			->select("INSERT INTO tbl_soa_tn (control_number,hawb_no,total)
						  SELECT control_number,hawb_no,debit FROM tbl_client_account where 
						  control_number = $control_number;");

	    		DB::connection($this->sql_connection())
	    		->table('tbl_client_account')
	    		->where('control_number',$control_number)
	    		->update(['control_number'=>0]);
    		}elseif($status == 1){
    			DB::connection($this->sql_connection())
    			->table('tbl_statement_control')
    			->where('control_number',$control_number)
    			->update([
    				'status' => $status,
    				'updated_by' => MySession::myId(),
    				'date_updated' => DB::raw('now()')
    			]);
				Mail::send(new SendSOAMail($control_number));
    		}

    		return response($data);
    		return response(
    			array(
    				'status' => $status,
    				'reason' => $reason,
    				'control_number' => $control_number
    			)
    		); 
    	}
    }
    public function view_soa(Request $request){
    	
    	$data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/admin/soa/index');
        if(!$data['credential']->is_read){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
    	$g = new GroupArrayController();
    	$control_number = $request->control_number;
    	$data['details'] = DB::connection($this->sql_connection())
    						->table('tbl_statement_control as ts')
    						->select(DB::raw("ts.account_no,tp.name,tp.b_address as address,DATE_FORMAT(statement_date,'%m/%d/%Y') as statement_date,if(ts.control_number > 1000000,ts.control_number,LPAD(ts.control_number, 7, 0)) as control_number,CONCAT(DATE_FORMAT(statement_start_date,'%m/%d/%Y'),' - ',DATE_FORMAT(statement_end_date,'%m/%d/%Y')) as billing_period,tp.tin,DATE_FORMAT(DATE_ADD(statement_date, INTERVAL 30 DAY),'%m/%d/%Y') as due_date,ts.statement_start_date as start_date,ts.statement_end_date as end_date,ts.status,DecodeSoaStatus(ts.status) as status_text,id_template,ts.reason,ts.date_updated,ts.date_viewed,ts.access_token,attachment_status"))
    						->leftjoin('tbl_client_profile as tp','tp.account_no','ts.account_no')

    						->where('control_number',$control_number)
    						->first();
    	$where = "WHERE tbl_client_account.control_number = $control_number";
  		if($data['details']->status == 10){
  			$where = "WHERE hawb_info.hawb_no in (SELECT hawb_no FROM tbl_soa_tn WHERe control_number = $control_number)";
  		}

		$dd = $this->parseReportOutputs($where,$data['details']->id_template);

		foreach($dd as $key=>$rep){
			$data[$key] = $rep;
		}
		$data['grand_sum']['total'] = (isset($data['grand_sum']['total']) ? $data['grand_sum']['total']:0);
		// return $data;
    	$data['payments'] = $this->get_less_payments($data['details']->start_date,$data['details']->end_date,$data['details']->account_no);
    	$data['previous_amt_due'] = $this->get_prev_amt_due($data['details']->start_date,$data['details']->account_no);
    	$data['adjustments'] =0;
	    // $data['amount_due'] = $this->parseSum($raw,'total')-$this->parseSum($data['payments'],'amount')+$data['previous_amt_due']+$data['adjustments'];
	    $data['amount_due'] = $data['grand_sum']['total']-$this->parseSum($data['payments'],'amount')+$data['previous_amt_due']+$data['adjustments'];	
    	return view('soa.soa_view',$data);
    }
    public function parseSum($obj,$key){
    	 $sum = 0;
    	foreach($obj as $item){
  			$sum += $item->{$key};
    	}
    	return $sum;
    }
    public function export_soa(Request $request){
    	// ini_set("memory_limit", "-1");
		// return "test"
    	$control_number = $request->control_number;
    	$g = new GroupArrayController();
    	$data['details'] = DB::connection($this->sql_connection())
					->table('tbl_statement_control as ts')
					->select(DB::raw("ts.account_no,tp.name,tp.b_address as address,DATE_FORMAT(statement_date,'%m/%d/%Y') as statement_date,if(ts.control_number > 1000000,ts.control_number,LPAD(ts.control_number, 7, 0)) as control_number,CONCAT(DATE_FORMAT(statement_start_date,'%m/%d/%Y'),' - ',DATE_FORMAT(statement_end_date,'%m/%d/%Y')) as billing_period,tp.tin,DATE_FORMAT(DATE_ADD(statement_date, INTERVAL 30 DAY),'%m/%d/%Y') as due_date,tbl_branch.address as 'branch_address',tbl_branch.phone as contact"),'statement_start_date as st','statement_end_date as ed','ts.id_template','tr.orientation')
					->leftjoin('tbl_client_profile as tp','tp.account_no','ts.account_no')
					->leftJoin('tbl_branch','tbl_branch.id_branch','tp.id_branch')
					->leftJoin('lse_new_reports.tbl_report as tr','tr.id_report','ts.id_template')
					->where('control_number',$control_number)
					->first();

					// return $data;

		$start_date = $data['details']->st;
		$end_date = $data['details']->ed;
		$account_no = $data['details']->account_no;

		$data['payments'] = $this->get_less_payments($start_date,$end_date,$account_no);

		$dd = $this->parseReportOutputs("WHERE tbl_client_account.control_number = $control_number",$data['details']->id_template);
		foreach($dd as $key=>$rep){
			$data[$key] = $rep;
		}
		$data['grand_sum']['total'] = (isset($data['grand_sum']['total']) ? $data['grand_sum']['total']:0);
		$data['previous_amt_due'] = $this->get_prev_amt_due($start_date,$account_no);
		$data['adjustments'] =0;

	    $data['amount_due'] = $data['grand_sum']['total']-$this->parseSum($data['payments'],'amount')+$data['previous_amt_due']+$data['adjustments'];

		$html = view('soa.soa_export_snappy',$data);

		$pdf = PDF::loadHtml($html);
		$pdf->setOption("encoding","UTF-8");
		$pdf->setOption('margin-bottom', '7mm');
		$pdf->setOption('margin-top', '7mm');
		$pdf->setOption('margin-right', '9mm');
		$pdf->setOption('margin-left', '9mm');
		
		$pdf->setOption('header-left', 'Page [page] of [toPage]');
		$pdf->setOption('header-right', "Control No.: ".$data['details']->control_number."  Account No. : ".$data['details']->account_no);
		$pdf->setOption('header-font-size', 8);
		$pdf->setOption('header-font-name', 'Calibri');
		// $pdf->render();

		//IF ORIENTATION IS LANDSCAPE
		if($data['details']->orientation == 1){
			$pdf->setOrientation('landscape');
		}
		// $pdf->setOrientation('landscape');
		// $font = $pdf->getFontMetrics()->get_font("helvetica","normal");
		return $pdf->stream();

	    $dompdf = new Dompdf();
        $dompdf->set_option("isRemoteEnabled",false);
		$dompdf->set_option("isPhpEnabled", true);

		$dompdf->loadHtml($html);
		$dompdf->render();
		$font = $dompdf->getFontMetrics()->get_font("helvetica","normal");
		$dompdf->getCanvas()->page_text(24, 18, "Page {PAGE_NUM} of {PAGE_COUNT}", $font, 8, array(0,0,0));
		$canvas = $dompdf->getCanvas();
		$canvas->page_script('
		  if ($PAGE_NUM > 1) {
		    $font = $fontMetrics->getFont("helvetica","normal");
		    $current_page = $PAGE_NUM-1;
		    $total_pages = $PAGE_COUNT-1;
		    $pdf->text(480, 18, "Control No.: '.$data['details']->control_number.'", $font, 8, array(0,0,0));
		    $pdf->text(480, 30, "Account No.: '.$data['details']->account_no.'", $font, 8, array(0,0,0));
		  }
		');
		$dompdf->stream("test.pdf", array("Attachment" => false));  
		exit;
    }
    public function get_less_payments($start_date,$end_date,$account_no){
    	return [];
		$data = DB::connection('main_lse')
						->select("Select DATE_FORMAT(tbl_individual_collection.transaction_date,'%m/%d/%Y') as transaction_date,tbl_collection.or_number,sum(tbl_individual_collection.amount) as amount from tbl_individual_collection left join tbl_collection on tbl_individual_collection.id_or = tbl_collection.id_or  where tbl_individual_collection.account_no =$account_no and tbl_individual_collection.transaction_date >= '$start_date' and tbl_individual_collection.transaction_date <= '$end_date' and tbl_individual_collection.reference = '' group by tbl_individual_collection.id_or;");
		return $data;
	}
	public function get_prev_amt_due($beg_date,$account_no){
		return 0;
		$sales = DB::connection('main_lse')
				 ->select("select sum(debit) as debit  from tbl_client_account where tbl_client_account.transaction_date < '$beg_date' and tbl_client_account.account_no =$account_no;")[0]->debit;
		$collection = DB::connection('main_lse')
					  ->select("select SUM(tbl_individual_collection.amount) as collection  from tbl_individual_collection where  tbl_individual_collection.amount > 0 and tbl_individual_collection.account_no =$account_no and tbl_individual_collection.transaction_date < '$beg_date';")[0]->collection;
		return $sales-$collection;
	}
	public function parseReportOutputs($where,$id_report){
		$parseReport = new ParseReportController();
		$g = new GroupArrayController();

		$rpt = $parseReport->parseReport($id_report,[],0,$where);

		ini_set("memory_limit", "-1");
		if($rpt == "NO") return "OOPS";
		$data['headers'] = $rpt['headers'];
		$data['fields'] = $rpt['select_fields'];
		$data['data_types'] = $rpt['data_types'];
		$data['sum_fields'] = $rpt['sum_fields'];

		$raw_data = DB::connection($this->sql_connection())
					->select($rpt['sql_statement']. "");

		$data['group_count'] = count($rpt['group_keys']);

		if(count($rpt['group_keys']) > 0){
			$data['data_list'] = $g->array_group_by($raw_data,$rpt['group_keys']);
		}else{
			$data['data_list'] = $raw_data;
		}
		if(count($data['sum_fields']) > 0 && count($raw_data) > 0){
	        foreach($data['sum_fields'] as $s){
	            $grand_sum[$s] = 0;
	        }
	        $sum_details = $this->parseTotals($data['data_list'],$data['sum_fields'],$grand_sum,$data['group_count']);

	        $data['group_total'] = $sum_details['group_sum'];
	        $data['grand_sum'] = $sum_details['grand_sum'];
		}

		return $data;
	}
    public function parseTotals($results,$sum_field,$grand_sum,$group_count){
    	if($group_count == 0){
	        foreach($results as $row){
	        	$sum = 0;
	            foreach($sum_field as $key_sum){
	                $grand_sum[$key_sum]+= $row->{$key_sum};
	            }
	        }
    	}elseif($group_count == 1){
	        foreach($results as $key_first=>$data_content){
	            foreach($sum_field as $s){
	                $sum[$key_first][$s] = 0;
	            }
	            foreach($data_content as $row){
	                foreach($sum_field as $key_sum){
	                    $sum[$key_first][$key_sum] += $row->{$key_sum};
	                    $grand_sum[$key_sum]+= $row->{$key_sum};
	                }
	            }
	        }
    	}else{
	        foreach($results as $key_first=>$group_2){
	            foreach($sum_field as $s){
	                $sum[$key_first][$s] = 0;
	            }
	            foreach($group_2 as $key_second=>$data_content){
	                foreach($sum_field as $s){
	                    $sum[$key_first][$key_second][$s] = 0;
	                }
	                foreach($data_content as $row){
	                    foreach($sum_field as $key_sum){
	                        $sum[$key_first][$key_second][$key_sum] += $row->{$key_sum};
	                        $sum[$key_first][$key_sum]+= $row->{$key_sum};
	                        $grand_sum[$key_sum]+= $row->{$key_sum};
	                    }    
	                }
	            }
	        }    		
    	}
        return array(
            'group_sum' => $sum,
            'grand_sum' => $grand_sum
        );
    }

    function validate_date($date){
        if (DateTime::createFromFormat('Y-m-d', $date) !== false) {
            return $date;
        }else{
            return $this->current_date();
        }
    }
	public function soa_attachment($access_token,Request $request){
    	// $count = 121;
    	// $first_col = floor($count/2) + (($count%2 == 0)?0:1);
    	// $second_col = $count - $first_col; 
    	// return $first_col;
		$data['load'] = (isset($request->load))?1:0;
		$data['details'] = DB::connection($this->sql_connection())
		->table('tbl_statement_control as ts')
		->select(DB::raw("ts.account_no,tp.name,tp.b_address as address,DATE_FORMAT(statement_date,'%m/%d/%Y') as statement_date,if(ts.control_number > 1000000,ts.control_number,LPAD(ts.control_number, 7, 0)) as control_number,CONCAT(DATE_FORMAT(statement_start_date,'%m/%d/%Y'),' - ',DATE_FORMAT(statement_end_date,'%m/%d/%Y')) as billing_period,tp.tin,DATE_FORMAT(DATE_ADD(statement_date, INTERVAL 30 DAY),'%m/%d/%Y') as due_date,tbl_branch.address as 'branch_address',tbl_branch.phone as contact"),'statement_start_date as st','statement_end_date as ed','ts.id_template','ts.control_number as raw_control_number','tp.id_client_profile')
		->leftjoin('tbl_client_profile as tp','tp.account_no','ts.account_no')
		->leftJoin('tbl_branch','tbl_branch.id_branch','tp.id_branch')
		->where('access_token',$access_token)
		->first();

		// return $data;

		$attachment_options = DB::connection($this->sql_connection())->table('lse.account_soa_attachment')->where('id_client_profile',$data['details']->id_client_profile)->first();

    	$data['id_receiver'] = (isset($attachment_options->id_receiver))?$attachment_options->id_receiver:1;
    	$data['pod'] = (isset($attachment_options->pod))?$attachment_options->pod:1;
    	$data['signature'] = (isset($attachment_options->signature))?$attachment_options->signature:1;

    	$data['type_count'] = (($data['id_receiver'] == 0)?0:1) +(($data['pod'] == 0)?0:1) + (($data['signature'] == 0)?0:1);
    	$data['first_col'] = "";

    	if($data['id_receiver'] > 0){
    		$data['first_col'] = 'id_receiver';
    	}elseif($data['pod'] > 0){
    		$data['first_col'] = 'pod';
    	}else{
    		$data['first_col'] = 'signature';
    	}

    	$condition_array = array();
    	$data['image_keys'] = array();
    	if($data['id_receiver'] > 0){
    		array_push($condition_array,'id_image is not null ');
    		array_push($data['image_keys'],'id_image');
    	}
    	if($data['pod'] > 0){
    		array_push($condition_array,'image is not null ');
    		array_push($data['image_keys'],'image');
    	}
    	if($data['signature'] > 0){
    		array_push($condition_array,'signature is not null ');
    		array_push($data['image_keys'],'signature');
    	}

    	$c = "if(".implode(' OR ',$condition_array).",1,0) as w_attachment";

    	$control_number = DB::table('lse.tbl_statement_control')->select('control_number')->where('access_token',$access_token)->first();

		// $data['attachments'] = DB::connection($this->sql_connection())
		// ->select("SELECT UPPER(if(libcap.DecodeDMStatus(status)='Received',concat('Delivered and Received by ',CONVERT(received_by USING utf8)),libcap.DecodeDMStatus(status))) as status,$c,hawb_no,DATE_FORMAT(updated_dt,'%m/%d/%Y') as date_received,ifnull(concat('/uploads/POD_id_image/',id_image),'') as id_image,ifnull(concat('/uploads/POD/',image),'') as image,
		// ifnull(concat('/uploads/POD_signature/',signature),'') as signature FROM libcap.delivery_manifest_awb as dm_awb WHERE hawb_no in (
		// SELECT hawb_no 
		// FROM lse.tbl_client_account tc
		// LEFT JOIN lse.tbl_statement_control ts on ts.control_number = tc.control_number
		// where (ts.access_token = '$access_token'))
		// AND status >= 1
		// GROUP BY hawb_no
		// ORDER BY hawb_no");

		// INCLUDE NO POD
    	// return "SELECT * FROM (
					// SELECT tc.hawb_no,$c,
					// if(dm_awb.status is not null,UPPER(if(libcap.DecodeDMStatus(dm_awb.status)='Received',concat('Delivered and Received by ',CONVERT(dm_awb.received_by USING utf8)),libcap.DecodeDMStatus(dm_awb.status))),'NO POD STATUS') as status,
					// if(dm_awb.status in (2,3),libcap.get_attempt_pod(tc.hawb_no),0) as att,
					// DATE_FORMAT(updated_dt,'%m/%d/%Y') as date_received,
					// ifnull(concat('/uploads/POD_id_image/',id_image),'') as id_image,
					// ifnull(concat('/uploads/POD/',image),'') as image,
					// ifnull(concat('/uploads/POD_signature/',signature),'') as signature
					// FROM lse.tbl_client_account tc
					// LEFT JOIN lse.tbl_statement_control ts on ts.control_number = tc.control_number
					// LEFT JOIN libcap.delivery_manifest_awb as dm_awb on dm_awb.hawb_no = tc.hawb_no
					// LEFT JOIN lse.hawb_info as h on h.hawb_no = tc.hawb_no
					// where (ts.access_token = '$access_token')) as jj
					// WHERE jj.att = 0
					// GROUP BY hawb_no";
		$data['attachments'] = DB::connection($this->sql_connection())
		->select("SELECT * FROM (
					SELECT tc.hawb_no,$c,
					if(dm_awb.status is not null,UPPER(if(libcap.DecodeDMStatus(dm_awb.status)='Received',concat('Delivered and Received by ',CONVERT(dm_awb.received_by USING utf8)),libcap.DecodeDMStatus(dm_awb.status))),'NO POD STATUS') as status,
					if(dm_awb.status in (2,3),libcap.get_attempt_pod(tc.hawb_no),0) as att,
					DATE_FORMAT(updated_dt,'%m/%d/%Y') as date_received,
					ifnull(concat('/uploads/POD_id_image/',id_image),'') as id_image,
					ifnull(concat('/uploads/POD/',image),'') as image,
					ifnull(concat('/uploads/POD_signature/',signature),'') as signature
					FROM lse.tbl_client_account tc
					LEFT JOIN lse.tbl_statement_control ts on ts.control_number = tc.control_number
					LEFT JOIN libcap.delivery_manifest_awb as dm_awb on dm_awb.hawb_no = tc.hawb_no
					LEFT JOIN lse.hawb_info as h on h.hawb_no = tc.hawb_no
					where (ts.access_token = '$access_token')) as jj
					WHERE jj.att = 0
					GROUP BY hawb_no");

		$data['attachments'] = $this->populate_attachment_data($data['attachments'],$data['type_count'],$data['image_keys']);

		$html = view('soa.soa_attachment_snappy',$data);

		return $html;

		$pdf = PDF::loadHtml($html);
		$pdf->setOption("encoding","UTF-8");
		$pdf->setOption('margin-bottom', '7mm');
		$pdf->setOption('margin-top', '7mm');
		$pdf->setOption('margin-right', '9mm');
		$pdf->setOption('margin-left', '9mm');
		
		$pdf->setOption('header-left', 'Page [page] of [toPage]');
		$pdf->setOption('header-right', "Control No.: ".$data['details']->control_number."  Account No. : ".$data['details']->account_no);
		$pdf->setOption('header-font-size', 8);
		$pdf->setOption('header-font-name', 'Calibri');

		// $pdf->render();
		// $pdf->setOrientation('landscape');
		// $font = $pdf->getFontMetrics()->get_font("helvetica","normal");
		return $pdf->stream();

		// return $html;
	    $dompdf = new Dompdf();
        $dompdf->set_option("isRemoteEnabled",true);
		$dompdf->set_option("isPhpEnabled", true);

		$dompdf->loadHtml($html);
		$dompdf->render();
		$font = $dompdf->getFontMetrics()->get_font("helvetica","normal");
		$dompdf->getCanvas()->page_text(24, 18, "Page {PAGE_NUM} of {PAGE_COUNT}", $font, 8, array(0,0,0));
		$canvas = $dompdf->getCanvas();
		$canvas->page_script('
		  if ($PAGE_NUM > 1) {
		    $font = $fontMetrics->getFont("helvetica","normal");
		    $current_page = $PAGE_NUM-1;
		    $total_pages = $PAGE_COUNT-1;
		    $pdf->text(480, 18, "Control No.: '.$data['details']->control_number.'", $font, 8, array(0,0,0));
		    $pdf->text(480, 30, "Account No.: '.$data['details']->account_no.'", $font, 8, array(0,0,0));
		  }');
	
		$dompdf->stream("SOA CTRL#".$control_number->control_number."_attachment.pdf", array("Attachment" => false));  
		exit;
    	return $data;
    }

	public function populate_attachment_data($attachments,$count,$image_keys){
    	$data=array();
    	$data_= array();

    	foreach($attachments as $att){
    		$data[$att->hawb_no]['w_attachment'] = $att->w_attachment;
    		$data[$att->hawb_no]['remarks'] = "TN NO. ".$att->hawb_no." ".$att->status ." - ".$att->date_received;
    		$temp_attach = array();
    		if($att->w_attachment == 1){
    			foreach($image_keys as $key){
    				$temp_attach[$key] = $att->{$key};
    			}
    		}
    		$data[$att->hawb_no]['attachments'] = $temp_attach;
    		// $data_[$att->w_attachment]= $data
    	}
    	// return array_chunk($data, 3,true);
    	return $data;
    	// $image_keys = ['']
    }
	public function soa_attachment_preview(Request $request){
    	$add_where ='';
		if(in_array(0, $request->cost_center)){
			$add_where = " OR hawb_info.cost_center is null";
		}
    	$data['details'] = DB::connection($this->sql_connection())
				  ->table('lse.tbl_client_profile')
				  ->select(DB::raw("account_no,name,b_address as address,tin,CONCAT(DATE_FORMAT('$request->date_from','%m/%d/%Y'),' - ',DATE_FORMAT('$request->date_to','%m/%d/%Y')) as billing_period"))
				  ->where('id_client_profile',$request->account_no)
				  ->first();
		$imp = implode(',', $request->cost_center);

    	// $count = 121;
    	// $first_col = floor($count/2) + (($count%2 == 0)?0:1);
    	// $second_col = $count - $first_col; 

    	// return $first_col;
    	$attachment_options = DB::connection($this->sql_connection())->table('lse.account_soa_attachment')->where('id_client_profile',$request->account_no)->first();


    	$data['id_receiver'] = (isset($attachment_options->id_receiver))?$attachment_options->id_receiver:1;
    	$data['pod'] = (isset($attachment_options->pod))?$attachment_options->pod:1;
    	$data['signature'] = (isset($attachment_options->signature))?$attachment_options->signature:1;
    	$data['type_count'] = (($data['id_receiver'] == 0)?0:1) +(($data['pod'] == 0)?0:1) + (($data['signature'] == 0)?0:1);

    	$data['first_col'] = "";
    	if($data['id_receiver'] > 0){
    		$data['first_col'] = 'id_receiver';
    	}elseif($data['pod'] > 0){
    		$data['first_col'] = 'pod';
    	}else{
    		$data['first_col'] = 'signature';
    	}
    	$condition_array = array();
    	$data['image_keys'] = array();
    	if($data['id_receiver'] > 0){
    		array_push($condition_array,'id_image is not null ');
    		array_push($data['image_keys'],'id_image');
    	}
    	if($data['pod'] > 0){
    		array_push($condition_array,'image is not null ');
    		array_push($data['image_keys'],'image');
    	}
    	if($data['signature'] > 0){
    		array_push($condition_array,'signature is not null ');
    		array_push($data['image_keys'],'signature');
    	}

    	$c = "if(".implode(' OR ',$condition_array).",1,0) as w_attachment";




		// $data['attachments'] = DB::connection($this->sql_connection())
		// ->select("SELECT UPPER(if(libcap.DecodeDMStatus(status)='Received',concat('Delivered and Received by ',CONVERT(received_by USING utf8)),libcap.DecodeDMStatus(status))) as status,$c,hawb_no,DATE_FORMAT(updated_dt,'%m/%d/%Y') as date_received,ifnull(concat('/uploads/POD_id_image/',id_image),'') as id_image,ifnull(concat('/uploads/POD/',image),'') as image,
		// ifnull(concat('/uploads/POD_signature/',signature),'') as signature FROM libcap.delivery_manifest_awb as dm_awb WHERE hawb_no in (
		// 		SELECT tbl_client_account.hawb_no 
		// 		FROM lse.tbl_client_account
		// 		LEFT JOIN lse.hawb_info on hawb_info.hawb_no = tbl_client_account.hawb_no
		// 		WHERE tbl_client_account.control_number =0 AND (hawb_info.cost_center in ($imp) $add_where ) AND hawb_info.shipment_date >= '$request->date_from' and hawb_info.shipment_date <= '$request->date_to' and tbl_client_account.id_client_profile = $request->account_no

		// )
		// AND status >= 1
		// GROUP BY hawb_no
		// ORDER BY hawb_no");


    	$data['attachments'] = DB::connection($this->sql_connection())
    	->select("SELECT * FROM (
					SELECT tbl_client_account.hawb_no ,$c,
					if(status is not null,UPPER(if(libcap.DecodeDMStatus(status)='Received',concat('Delivered and Received by ',CONVERT(dm_awb.received_by USING utf8)),libcap.DecodeDMStatus(status))),'NO POD STATUS') as status,
					if(status in (2,3),libcap.get_attempt_pod(tbl_client_account.hawb_no),0) as att,
					DATE_FORMAT(updated_dt,'%m/%d/%Y') as date_received,
					ifnull(concat('/uploads/POD_id_image/',id_image),'') as id_image,
					ifnull(concat('/uploads/POD/',image),'') as image,
					ifnull(concat('/uploads/POD_signature/',signature),'') as signature
					FROM lse.tbl_client_account
					LEFT JOIN lse.hawb_info on hawb_info.hawb_no = tbl_client_account.hawb_no
					LEFT JOIN libcap.delivery_manifest_awb as dm_awb on dm_awb.hawb_no = hawb_info.hawb_no
					where tbl_client_account.control_number =0 AND (hawb_info.cost_center in ($imp) $add_where ) AND hawb_info.shipment_date >= '$request->date_from' and hawb_info.shipment_date <= '$request->date_to' and tbl_client_account.id_client_profile = $request->account_no) as jj
					WHERE jj.att =0
					GROUP BY hawb_no");


    	// return 

    	// return $data;


    	// 		->select("SELECT * FROM (
					// SELECT tc.hawb_no,$c,
					// if(dm_awb.status is not null,UPPER(if(libcap.DecodeDMStatus(dm_awb.status)='Received',concat('Delivered and Received by ',CONVERT(dm_awb.received_by USING utf8)),libcap.DecodeDMStatus(dm_awb.status))),'NO POD STATUS') as status,
					// if(dm_awb.status in (2,3),libcap.get_attempt_pod(tc.hawb_no),0) as att,
					// DATE_FORMAT(updated_dt,'%m/%d/%Y') as date_received,
					// ifnull(concat('/uploads/POD_id_image/',id_image),'') as id_image,
					// ifnull(concat('/uploads/POD/',image),'') as image,
					// ifnull(concat('/uploads/POD_signature/',signature),'') as signature
					// FROM lse.tbl_client_account tc
					// LEFT JOIN lse.tbl_statement_control ts on ts.control_number = tc.control_number
					// LEFT JOIN libcap.delivery_manifest_awb as dm_awb on dm_awb.hawb_no = tc.hawb_no
					// where (ts.access_token = '$access_token')) as jj
					// WHERE jj.att = 0");

		$data['attachments'] = $this->populate_attachment_data($data['attachments'],$data['type_count'],$data['image_keys']);
		$html = view('soa.soa_attachment_mod_preview',$data);
		return $html;
	    $dompdf = new Dompdf();
        $dompdf->set_option("isRemoteEnabled",true);
		$dompdf->set_option("isPhpEnabled", true);

		$dompdf->loadHtml($html);
		$dompdf->render();
		$font = $dompdf->getFontMetrics()->get_font("helvetica","normal");
		$dompdf->getCanvas()->page_text(24, 18, "Page {PAGE_NUM} of {PAGE_COUNT}", $font, 8, array(0,0,0));


		$dompdf->stream($data['details']->name."_attachment_preview.pdf", array("Attachment" => false));  
		exit;
    	return $data;
    }
	public function view_attachment($file){
        $file_path = $file;
      
        $path = Storage::disk('soa_attachment')->path($file_path.".pdf");

        // return $path;
        if (!File::exists($path)) {
            abort(404);
        }
        $file = File::get($path);


        $type = File::mimeType($path);
        $response = Response::make($file, 200);

        $response->header("Content-Type", $type);
        return $response;
    }
    public function generate_attachment(Request $request){
    	if($request->ajax()){
			// ini_set("memory_limit", "-1");
			// ini_set("max_execution_time ", 300);
			// ini_set("max_input_time ", 300);
			
			
    		$token = $request->token;
    		// $count = DB::table('lse.tbl_statement_control')
    		// 		 ->where('access_token',$token)
    		// 		 ->count();
    		$details = DB::table('lse.tbl_statement_control as ts')
    				 ->select(DB::raw("ts.*,if(ts.control_number > 1000000,ts.control_number,LPAD(ts.control_number, 7, 0)) as control_number"))
    				 ->where('access_token',$token)
    				 ->first();
					 
    		// if($count == 0)return response(array(
    		// 	'RESPONSE_CODE' => "INVALID"
    		// ));
    		if($details == null)return response(array(
    			'RESPONSE_CODE' => "INVALID"
    		));
    		$ctrl_no = $request->ctrl_no;
    		$file_name = "CTRL_".$ctrl_no."_".$token;
    		// $host= request()->getHost().":9091";
			$host= request()->getHost()."";
    		$laravel_path = str_replace("\public","",public_path());

			$header_pdf = "Control No.: ".$details->control_number." Account No. :".$details->account_no;
			$content= '
				cd C:/Program Files/wkhtmltopdf/bin
				wkhtmltopdf --encoding UTF-8 -B 7mm -T 7mm -L 9mm -R 9mm --header-font-size 8 --header-font-name calibri --header-left "Page [page] of [topage]" --header-right "'.$header_pdf.'" "'.$host.'/admin/soa/soa_attachment/'.$token.'" C:/wamp64/www/LSE_NEW_BACKEND/storage/app/public/soa_attachment/'.$file_name.'.pdf
				cd '.$laravel_path.'
				C:\wamp64\bin\php\php7.4.9\php.exe artisan soa:update_generated --token='.$token.'
				exit;';
				Storage::disk('soa_bat')->put("$file_name.bat", $content);
				DB::table('lse.tbl_statement_control')
				->where('access_token',$token)
				->update(['attachment_status' => 1]);


			exec("C:/wamp64/www/LSE_NEW_BACKEND/storage/app/public/soa_bat/$file_name.bat");
			return response(array(
    			'RESPONSE_CODE' => "SUCCESS"
    		));
    		return response($token);
    	}
    }
	public function check_attachment(Request $request){
    	if($request->ajax()){
    		$token = $request->token;

    		$details = DB::table('lse.tbl_statement_control as ts')
    				 ->select('status','attachment_status',DB::raw('if(ts.control_number > 1000000,ts.control_number,LPAD(ts.control_number, 7, 0)) as control_number'))
    				 ->where('access_token',$token)
    				 ->first();


    		if($details->status == 0){
    			$data['RESPONSE_CODE'] = "VALID";
    			$data['COMMAND'] = "PREVIEW";
    			$data['TOKEN'] = $token;
    
    		}elseif($details->status == 10){
    			$data['RESPONSE_CODE'] = "INVALID";
    			$data['ERROR_MESSAGE'] = "Cancelled SOA";
    			$data['TYPE'] = "warning";
    		}else{
    			if($details->attachment_status == 2){
    				$data['RESPONSE_CODE'] = "VALID";
    				$data['COMMAND'] = "VIEW_GENERATED";
    				$data['TOKEN'] = "CTRL_".$details->control_number."_".$token;
    			}elseif($details->attachment_status == 1){
    				$data['RESPONSE_CODE'] = "INVALID";
    				$data['ERROR_MESSAGE'] = "SOA Attachment Processing";
    				$data['TYPE'] = "info";
    			}else{
    				$data['RESPONSE_CODE'] = "INVALID";
    				$data['ERROR_MESSAGE'] = "SOA No Attachment";
    				$data['TYPE'] = "warning";
    			}
    		}

    		return  $data;

    		return response($token);
    	}
    }
}	
