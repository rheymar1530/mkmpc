<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Loan;
use App\Member;
use App\MySession;
use DB;
use App\CredentialModel;
class EmployeeController extends Controller
{
    //KEYS EMPLYOEE
    // 'first_name','middle_name','last_name','suffix','address','birthday','tin_no','sss_gsis_no','philhealth_no','hdmf_no','date_hired','id_branch','id_department','id_position','id_employee_status','monthly_rate','daily_rate','sss_amount','philhealth_amount','hdmf_amount','withholding_tax','insurance'
    // KEYS ALLOWANCE
    // 'id_allowance_name','amount','type'

    // 'sss_gsis_no','philhealth_no','hdmf_no','email'
    // ,'tin_no','date_hired'
    private $required_key_text = ['first_name','middle_name','last_name','address','birthday','id_branch','id_department','id_position','id_employee_status'];

    private $required_key_amount = [];
    // 'sss_amount','philhealth_amount','hdmf_amount'
    // ,'withholding_tax','insurance'


    private $required_key_allowance_text = ['id_allowance_name'];
    private $required_key_allowance_amount = ['amount'];


    public function index(Request $request){
        $data['credential']= CredentialModel::GetCredential(MySession::myPrivilegeId());
        if(!$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }

        $data['head_title'] = "Employees";
        $data['employee_list'] = DB::table('employee as e')
                                 ->select(DB::raw("e.id_employee,FormatName(e.first_name,e.middle_name,e.last_name,e.suffix) as name,b.branch_name,d.name as 'department',p.description as 'position',es.description as employee_status,
                                if(e.status=1,'Active','Inactive') as status,et.description as e_type"))
                                 ->leftJoin('tbl_branch as b','b.id_branch','e.id_branch')
                                 ->leftJoin('department as d','d.id_department','e.id_department')
                                 ->leftJoin('position as p','p.id_position','e.id_position')
                                 ->leftJoin('employee_status as es','es.id_employee_status','e.id_employee_status')
                                 ->leftJoin('employee_type as et','et.id_employee_type','e.id_employee_type')
                                 ->get();


        return view('employee.index',$data);
        return $data;
    }

    public function create(){
        $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/employee');
        if(!$data['credential']->is_create){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
        $data['head_title'] = "Add Employee";
        $data['opcode'] = 0;
        $data['branches'] = DB::table('tbl_branch')
                            ->select('id_branch','branch_name')
                            ->get();
        $data['employee_type'] = DB::table('employee_type')->get();
        $data['department'] = DB::table('department')
                            ->select('id_department','name')
                            ->get();
        $data['position'] = DB::table('position')
                            ->select('id_position','description')
                            ->get();
        $data['employee_status'] = DB::table('employee_status')
                            ->select('id_employee_status','description')
                            ->get();
        $data['employee_compensation'] = DB::table('employee_compensation')
                            ->select('id_employee_compensation','description')
                            ->orDerby('id_employee_compensation','DESC')
                            ->get();
        $data['allowance_name'] = DB::table('allowance_name')
                                 ->get();

        $data['banks'] = DB::table('tbl_bank')
                         ->get();
        return view('employee.form',$data);      
        return $data;
    }
    public function search_employee(Request $request){
        // if($request->ajax()){
            $search = $request->term;   
            if(strlen($search) < 3){
                $data['accounts'] = array();
                return response($data);
            }
            $data['accounts'] = DB::table('employee as e')
            ->select(DB::raw("concat(id_employee,' || ',FormatName(e.first_name,e.middle_name,e.last_name,e.suffix),' [',et.description,']') as tag_value,id_employee as tag_id"))
            ->leftJoin('employee_type as et','et.id_employee_type','e.id_employee_type')
            ->where(function ($query) use ($search){
                $query->where(DB::raw("concat(first_name,' ',last_name)"), 'like', "%$search%");
            })
            ->get();
            return response($data);
        // }
    }
    public function view($id_employee){
        $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/employee');
        if(!$data['credential']->is_view && !$data['credential']->is_edit){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
        $data['opcode'] = 1;
        $data['branches'] = DB::table('tbl_branch')
                            ->select('id_branch','branch_name')
                            ->get();
        $data['department'] = DB::table('department')
                            ->select('id_department','name')
                            ->get();
        $data['position'] = DB::table('position')
                            ->select('id_position','description')
                            ->get();
        $data['employee_status'] = DB::table('employee_status')
                            ->select('id_employee_status','description')
                            ->get();

        $data['allowance_name'] = DB::table('allowance_name')
                                 ->get();

        $data['employee_type'] = DB::table('employee_type')->get();

        $data['employee_compensation'] = DB::table('employee_compensation')
                            ->select('id_employee_compensation','description')
                            ->orDerby('id_employee_compensation','DESC')
                            ->get();
        $data['employee_details'] = DB::table('employee')
                                    ->select(DB::raw("employee.*,if(id_employee_compensation=1,daily_rate,monthly_rate) as rate,FormatName(first_name,middle_name,last_name,suffix) as full_name"))
                                    ->where('id_employee',$id_employee)
                                    ->first();  
        $data['head_title'] = "Employee - ".$data['employee_details']->full_name;
        // return $data;                            

        $data['allowances'] = DB::table('employee_allowance')
                              ->where('id_employee',$id_employee)
                              ->orderBy('id_employee_allowance')
                              ->get();
        $data['banks'] = DB::table('tbl_bank')->get();
                 
        if($data['employee_details']->id_member > 0){
            $data['selected_member'] =DB::table('member as m')
                                ->select(DB::raw("concat(membership_id,' || ',FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)) as member_name,id_member"))
                                ->where('id_member',$data['employee_details']->id_member)
                                ->first();           
        }

        return view('employee.form',$data);      
        return $id_employee;
    }
    public function post(Request $request){
        if($request->ajax()){

            $employee_info = $request->employee_data;
            $allowance = $request->allowance ?? [];
            $id_employee  = $request->id_employee;
            $opcode = $request->opcode;



            if($employee_info['id_employee_compensation'] == 1){
                $employee_info['daily_rate'] = $employee_info['rate'];
                $employee_info['monthly_rate'] = 0;
            }else{
                $employee_info['daily_rate'] = 0;
                $employee_info['monthly_rate'] = $employee_info['rate'];
            }

            unset($employee_info['rate']);

            // return response($employee_info);


            $employee_info['suffix'] = $employee_info['suffix'] ?? '';

            $employee_info['id_member'] = $employee_info['id_member'] ?? 0;





            $validator = $this->validate_input($employee_info,$allowance);
            // return $validator;
            if(!$validator['valid']){
                $data['RESPONSE_CODE'] = "INVALID_INPUT";
                $data['message'] = "Missing Mandatory Fields";
                $data['invalid_details'] = $validator;

                return response($data);
            }


            // $test = array();
            // foreach($employee_info as $key=>$val){
            //     array_push($test,$key);
            // }

            // return "'".implode("','",$test)."'";

  
            if($opcode == 0){ //insert
                DB::table('employee')
                ->insert($employee_info);
                $id_employee = DB::table('employee')->max('id_employee');
            }else{ // update
                DB::table('employee')
                ->where('id_employee',$id_employee)
                ->update($employee_info);

                DB::table('employee_allowance')
                ->where('id_employee',$id_employee)
                ->delete();
            }

            for($i=0;$i<count($allowance);$i++){
                $allowance[$i]['id_employee'] = $id_employee;
                $allowance[$i]['type'] = $employee_info['id_employee_compensation'];
            }

            if(count($allowance) > 0){
                DB::table('employee_allowance')
                ->insert($allowance);                
            }

            if($employee_info['id_member'] > 0){
                DB::table('member')
                ->where('id_member',$employee_info['id_member'])
                ->update(['id_employee'=>$id_employee]);
            }

            $data['RESPONSE_CODE'] = "SUCCESS";
            $data['id_employee'] = $id_employee;

            return response($data);
            return response($request);
        }
    }
    public function sync_member(Request $request){
        if($request->ajax()){
            $id_member = $request->id_member;

            $data['member_details'] = DB::table('member')
                                      ->select(DB::raw("id_member,first_name,middle_name,last_name,suffix,date_of_birth as birthday,tin as tin_no,address,id_branch,id_employee"))
                                      ->where('id_member',$id_member)
                                      ->first();
            return response($data);

            return response($id_member);
        }
    }

    public function validate_input($employee_info,$allowance){
        $required_fields_text = $this->required_key_text;
        $required_fields_amount = $this->required_key_amount;
        $valid = true;

        $invalid_fields = array();
        foreach($required_fields_text as $key){
            if(!isset($employee_info[$key]) || $employee_info[$key] == ""){
                array_push($invalid_fields,$key);
                $valid = false;
            }
        }

       foreach($required_fields_amount as $key){
            if(!isset($employee_info[$key]) || $employee_info[$key] <= 0){
                array_push($invalid_fields,$key);
                $valid = false;
            }
        }


        //Allowance
        $required_key_allowance_text = $this->required_key_allowance_text;
        $required_key_allowance_amount = $this->required_key_allowance_amount;


        $invalid_allowance = array();

        if(isset($allowance)){
            for($i=0;$i<count($allowance);$i++){
                $temp = array();
                foreach($required_key_allowance_text as $key){
                    if(!isset($allowance[$i][$key]) || $allowance[$i][$key] == ""){
                        array_push($temp,$key);
                        $valid  = false;
                    } 
                }
                foreach($required_key_allowance_amount as $key){
                    if(!isset($allowance[$i][$key]) || $allowance[$i][$key] <= 0){
                        array_push($temp,$key);
                        $valid  = false;
                    } 
                }
                if(count($temp) > 0){
                    $invalid_allowance[$i] = $temp;
                }
            }
        }
        // return $invalid_allowance;


        $output['valid'] = $valid;
        $output['invalid_fields'] = $invalid_fields;
        $output['invalid_allowance'] = $invalid_allowance;

        return $output;

    }
    public function get_employee_details(Request $request){
        if($request->ajax()){
            $id_employee = $request->id_employee;
            $id_payroll = $request->id_payroll;
            $data['details'] = DB::table('employee as e')
                              ->select(DB::raw("FormatName(e.first_name,e.middle_name,e.last_name,e.suffix) as name,e.id_employee,e.daily_rate,e.monthly_rate,e.sss_amount,e.philhealth_amount,e.hdmf_amount,e.withholding_tax,e.insurance, getEmployeeCashAdvance(e.id_employee,0) as ca_balance"))
                              ->where('id_employee',$id_employee)
                              ->first();
            $data['allowances'] = DB::table('employee_allowance as ea')
                                 ->select('ea.id_allowance_name','an.description','ea.amount','ea.type')
                                 ->leftJoin('allowance_name as an','an.id_allowance_name','ea.id_allowance_name')
                                 ->where('ea.id_employee',$id_employee)
                                 ->get();

            // $data['cash_advances'] = DB::select("
            //     SELECT * FROM (
            //     select cd.id_cash_disbursement,DATE_FORMAT(cd.date,'%m/%d/%Y') as date,cd.description,(debit-getCashAdvanceLessTotal(cd.id_cash_disbursement,$id_payroll)) as balance from cash_disbursement as cd
            // LEFT JOIN cash_disbursement_details as cdd on cdd.id_cash_disbursement = cd.id_cash_disbursement
            // LEFT JOIN chart_account as ca on ca.id_chart_account = cdd.id_chart_account
            // WHERE id_employee = ? and ca.is_cash_advance = 1 and cdd.id_cash_disbursement <> 0) as kk
            //     WHERE balance > 0;",[$id_employee]);
                                 
            $data['cash_advances'] = DB::select("
                SELECT *,concat(type,'-',ref) as id_ref,concat(upper(type),'#',ref) as ref_text FROM (
                select 'cdv' as type,cd.id_cash_disbursement as ref,DATE_FORMAT(cd.date,'%m/%d/%Y') as date,cd.description,(debit-getCashAdvanceLessTotal(cd.id_cash_disbursement,$id_payroll)) as balance from cash_disbursement as cd
                LEFT JOIN cash_disbursement_details as cdd on cdd.id_cash_disbursement = cd.id_cash_disbursement
                LEFT JOIN chart_account as ca on ca.id_chart_account = cdd.id_chart_account
                WHERE id_employee = ? and ca.is_cash_advance = 1 and cdd.id_cash_disbursement <> 0 and cd.status <> 10
                UNION ALL
                select 'jv' as type,jv.id_journal_voucher as ref,DATE_FORMAT(jv.date,'%m/%d/%Y') as date,jv.description,(debit-getCashAdvanceLessTotalJV(jv.id_journal_voucher,$id_payroll)) as balance from journal_voucher as jv
                LEFT JOIN journal_voucher_details as jvd on jvd.id_journal_voucher = jv.id_journal_voucher
                LEFT JOIN chart_account as ca on ca.id_chart_account = jvd.id_chart_account
                WHERE id_employee = ? and ca.is_cash_advance = 1 and jvd.id_journal_voucher <> 0 and jv.status <> 10
                ) as kk
                WHERE balance > 0;",[$id_employee,$id_employee]);


            // $data['ca_balance'] = DB::select("select ifnull(SUM(debit-credit),0) as ca_balance from cash_disbursement as cd
            //                         LEFT JOIN cash_disbursement_details as cdd on cdd.id_cash_disbursement = cd.id_cash_disbursement
            //                         LEFT JOIN chart_account as ca on ca.id_chart_account = cdd.id_chart_account
            //                         WHERE id_employee = ? and ca.is_cash_advance = 1;",[$id_employee])[0]->ca_balance;
                                                                     
            return response($data);
        }
    }



}
