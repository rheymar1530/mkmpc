<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use DateTime;
use App\CredentialModel;
use MySession;
use Excel;
use App\Exports\PayrollSummaryExport;
use Dompdf\Dompdf;
use PDF;
use App\JVModel;
use App\CDVModel;
class PayrollController extends Controller
{
    public function index(Request $request){
        // return Excel::download(new PayrollSummaryExport(6), 'invoices.xlsx');
        $data['credential']= CredentialModel::GetCredential(MySession::myPrivilegeId());

        if(!$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
        $data['head_title'] = "Payroll";
        $data['payroll']= DB::table('payroll')
                         ->select(DB::raw("id_payroll,DATE_FORMAT(payroll.date_released,'%m/%d/%Y') as date_released,pm.description,ifnull(concat('Bank - ',bank_name),'Cash') as salary_mode,DATE_FORMAT(period_start,'%m/%d/%Y') as period_start,DATE_FORMAT(period_end,'%m/%d/%Y') as period_end,if(payroll.id_payroll_mode=3,no_days,'-') as no_days,DATE_FORMAT(payroll.date_Created,'%M %d, %Y %r') as date_created,payroll.status"))
                         ->leftJoin('payroll_mode as pm','pm.id_payroll_mode','payroll.id_payroll_mode')
                         ->leftJoin('tbl_bank as tb','tb.id_bank','payroll.id_bank')
                         ->orDerby('payroll.id_payroll','DESC')
                         ->get();

        return view('payroll.index',$data);
        return $data;
    }
    public function create(){
        date_default_timezone_set('Asia/Manila');
        $dt = new DateTime();
        $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/payroll');
        if(!$data['credential']->is_create){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
        $data['head_title'] = "Create Payroll";
        $data['opcode'] = 0;
        $data['allow_post'] = 1;
        $data['temp_period_start'] = $dt->format('Y-m-01');
        $data['temp_period_end'] = $dt->format('Y-m-t');
        $data['banks'] = DB::table('tbl_bank')->get();

        $data['current_date']= MySession::current_date();
        // return $data['banks'];
        $data['payroll_mode'] = DB::table('payroll_mode')->get();

        return view('payroll.payroll_form',$data);
    }

    public function post(Request $request){
        $payroll_details =  $request->payroll_details;
        $opcode = $request->opcode;
        $id_payroll = $request->id_payroll;
        $payroll_object = $request->payroll_parent;

        //CHECK IF EMPLOYEE HAS A PAYROLL RECORD WITHIN PERIOD
        $id_employees = array();
        foreach($payroll_details as $id_employee=>$details){
            array_push($id_employees,$id_employee);
        }

        $id_employee_error = array();

        $id_employee_error = DB::table('payroll as p')
                             ->select('id_employee')
                             ->leftJoin('payroll_employee as pe','pe.id_payroll','p.id_payroll')
                             ->where('p.id_payroll','<>',$id_payroll)
                             ->where('period_start','>=',$payroll_object['period_start'])
                             ->where('period_end','<=',$payroll_object['period_end'])
                             ->whereIn('pe.id_employee',$id_employees)
                             ->where('p.status',"<>",10)
                             ->groupBy('pe.id_employee')
                             ->get()->pluck('id_employee');
        
        if(count($id_employee_error) > 0){
            // $data['RESPONSE_CODE'] = "INVALID_EMPLOYEE";
            // $data['employee_error'] = $id_employee_error;

            // return response($data);
        }


        $data['RESPONSE_CODE'] = "SUCCESS";
        if(!isset($payroll_details)){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "No Employee Selected";

            return response($data);

        }
       
        $id_payroll_mode = $payroll_object['id_payroll_mode'];
        $no_days = $payroll_object['no_days'];

        $payroll_employee_insert = array();
        $payroll_employee_allowances = array();
        $payroll_cash_advance = array();
        $deduction_object =["employee_deductions","deducted_benefits"];
        $additional_object = ["additional_compensation"];

        $emp_with_ca = array();

        foreach($payroll_details as $id_employee=>$details){
            $payroll_employee = array();
            $employee_details = $this->get_employee_details($id_employee);

            if($id_payroll_mode == 3){ // Daily
                $basic_pay = $employee_details['details']->daily_rate * $no_days;
            }else{ //Monthly or Semi
                $divide = ($id_payroll_mode == 1)?1:2;
                $basic_pay = $employee_details['details']->monthly_rate/$divide;
            }
            $payroll_employee['id_employee'] = $employee_details['details']->id_employee;
            $payroll_employee['basic_pay'] = $basic_pay;
            // $payroll_employee['ca'] = $details['cash_advance']['ca_amount'];

            $total_deductions = 0;
            $total_additional_income = 0;
            
            //additional income
            foreach($additional_object as $obj){
                foreach($details[$obj] as $key=>$value){
                    $payroll_employee[$key] = $value;
                    $total_additional_income += $value;
                }
            }

            //deductions
            foreach($deduction_object as $obj){
                foreach($details[$obj] as $key=>$value){
                    $payroll_employee[$key] = $value;
                    $total_deductions += $value;
                }
            }

            //allowance
            $allowances = $this->populate_allowance_object(($details['employee_allowance'] ?? []),$payroll_employee['id_employee'] );

            $allowance_insert_obj = $allowances['allowance_object'];

            foreach($allowance_insert_obj as $aib){
                array_push($payroll_employee_allowances,$aib);
            }

            // array_push($payroll_employee_allowances,$allowance_insert_obj);
            $payroll_employee['total_allowance'] = $allowances['total_allowance'];

            //populate_cash_advances
            $ca = $details['cash_advances'] ?? [];


            $payroll_employee['ca'] = 0;

            if(count($ca) > 0){
                array_push($emp_with_ca,$employee_details['details']->id_employee);
            }

            // return $ca;

            foreach($ca as $c){
                $temp = array();

                $exp_reference = explode('-',$c['id_ref']);
                $type = $exp_reference[0];
                $id_reference = $exp_reference[1];

                if($type == "cdv"){
                    $temp['id_journal_voucher'] = 0;
                    $temp['id_cash_disbursement'] = $id_reference;
                    $balance = DB::select("SELECT getCashAdvanceBalance(?,?,?) as balance",[$id_reference,$employee_details['details']->id_employee,$id_payroll])[0]->balance;
                }else{
                    $temp['id_journal_voucher'] = $id_reference;
                    $temp['id_cash_disbursement'] = 0;   
                    $balance = DB::select("SELECT getCashAdvanceBalanceJV(?,?,?) as balance",[$id_reference,$employee_details['details']->id_employee,$id_payroll])[0]->balance;                 
                }
                

                
                $temp['amount'] = ($c['deducted_amt'] > $balance)?$balance:$c['deducted_amt'];
                $temp['id_employee'] = $payroll_employee['id_employee'];
                $payroll_employee['ca'] += $temp['amount'];

                array_push($payroll_cash_advance,$temp);
            }

        
          
            if($opcode ==1){
                $payroll_employee['id_cash_disbursement'] = DB::table('payroll_employee')->where('id_payroll',$id_payroll)->where('id_employee',$employee_details['details']->id_employee)->max('id_cash_disbursement') ?? 0;

                
            }
            $payroll_employee['gross_income'] = $basic_pay+$total_additional_income+$payroll_employee['total_allowance'];
            $payroll_employee['net_income'] = $payroll_employee['gross_income'] - $total_deductions-$payroll_employee['ca'];
            $payroll_employee['remarks'] = $details['remarks'];

            if($payroll_employee['net_income'] <= 0){

                // return $payroll_employee;
                $data['RESPONSE_CODE'] = "ERROR";
                $data['message'] = "Net Income must be greater than 0. Please check your employee payroll";

                return $data;
            }

            array_push($payroll_employee_insert,$payroll_employee);
            // return $payroll_employee_insert;
        }


        if($opcode == 0){
            DB::table('payroll')
            ->insert($payroll_object);

            $id_payroll = DB::table('payroll')->max('id_payroll');           
        }else{
            $status = DB::table('payroll')->select('status')->where('id_payroll',$id_payroll)->first();

            if(!isset($status) || $status->status == 10){
                $data['RESPONSE_CODE'] = "ERROR";
                $data['message'] = "Invalid Request";  

                return response($data);
            }

            DB::table('payroll')
            ->where('id_payroll',$id_payroll)
            ->update($payroll_object);

            DB::table('payroll_employee')->where('id_payroll',$id_payroll)->delete();           
            DB::table('payroll_employee_allowances')->where('id_payroll',$id_payroll)->delete();
            DB::table('payroll_ca')->where('id_payroll',$id_payroll)->delete();
        }


        // INSERT PAYROLL EMPLOYEE
        foreach($payroll_employee_insert as $pe){
           $pe['id_payroll']  = $id_payroll;
           DB::table('payroll_employee')
           ->insert($pe);
        }

        // INSERT PAYROLL ALLOWANCES
        foreach($payroll_employee_allowances as $pea){
            $pea['id_payroll'] = $id_payroll;
            DB::table('payroll_employee_allowances')
            ->insert($pea);
        }
        //INSERT PAYROLL CASH ADVANCES
        foreach($payroll_cash_advance as $pca){
            $pca['id_payroll'] = $id_payroll;
            DB::table('payroll_ca')
            ->insert($pca);
        }   

        //GENERATE CDV

        foreach($emp_with_ca as $emp_ca){
             // $this->generate_cdv_entry($id_payroll,$emp_ca);
        }    

        $data['id_payroll'] = $id_payroll;

        // JVModel::PayrollJV($id_payroll);
        CDVModel::Payroll($id_payroll);


        return response($data);

        return "success";

        return $payroll_employee_insert;

        return $id_payroll;
        return $payroll_employee_insert;
    }

    public function get_employee_details($id_employee){
        $data['details'] = DB::table('employee as e')
                          ->select(DB::raw("FormatName(e.first_name,e.middle_name,e.last_name,e.suffix) as name,e.id_employee,e.daily_rate,e.monthly_rate,e.sss_amount,e.philhealth_amount,e.hdmf_amount,e.withholding_tax,e.insurance, getEmployeeCashAdvance(e.id_employee,0) as ca_balance"))
                          ->where('id_employee',$id_employee)
                          ->first();
        return $data;
    }
    public function populate_allowance_object($allowance,$id_employee){


        $allowance_obj = array();
        $output['total_allowance'] = 0;
        foreach($allowance as $id_allowance_name=>$amount){
            $output['total_allowance'] += $amount;
            $temp = array();
            $temp['id_allowance_name'] = $id_allowance_name;
            $temp['amount'] = $amount;
            $temp['id_employee'] = $id_employee;
            array_push($allowance_obj,$temp);
        }
        $output['allowance_object'] = $allowance_obj;

        return $output;
    }
    public function populate_ca_object($ca,$id_employee){
        
    }

    public function view($id_payroll){

        $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/payroll');
        if(!$data['credential']->is_view && !$data['credential']->is_edit){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
        // return $this->generate_cdv_entry($id_payroll,6);

        $data['current_date'] = MySession::current_date();  
        $data['opcode'] = 1;
        $data['banks'] = DB::table('tbl_bank')->get();
        $data['payroll_mode'] = DB::table('payroll_mode')->get();
        $additional_compensation = ["ot","night_shift_dif","holiday","paid_leaves","salary_adjustment","13th_month","others"];
        $employee_deductions = ["absences","late","sss_loan","hdmf_loan"];
        $deducted_benefits = ["sss","philhealth","hdmf","wt","insurance"];
        $data['head_title'] = "Payroll #$id_payroll";
        $data['payroll'] = DB::table('payroll')
                           ->where('id_payroll',$id_payroll)
                           ->first();

        $data['disbursed_by'] = DB::table('employee as e')
                                 ->select(DB::raw("concat(id_employee,' || ',FormatName(e.first_name,e.middle_name,e.last_name,e.suffix)) as tag_value,id_employee as tag_id"))
                                 ->where("id_employee",$data['payroll']->disbursed_by)
                                 ->first();

        $data['allow_post'] = 1;

        if($data['payroll']->status == 10){
            $data['allow_post'] = 0;
        }
       
        $payroll_employee_obj = array();

        $payroll_employee = DB::table('payroll_employee')
                            ->where('id_payroll',$id_payroll)
                            ->orderBy('id_payroll_employee','ASC')
                            ->get();

        //POPULATE DATA OBJECT
        foreach($payroll_employee as $p){
            $payroll_emp_details = array();
            $payroll_emp_details['remarks'] = $p->remarks;
            $payroll_emp_details['additional_compensation'] = array();

            //Employee details
            $payroll_emp_details['employee_details'] = $this->get_employee_details($p->id_employee)['details'];


            //Additional Compensation 
            $total_add_com = 0;
            foreach($additional_compensation as $ac){
                $payroll_emp_details['additional_compensation'][$ac] = floatval($p->{$ac});
                $total_add_com += floatval($p->{$ac});
            }

            //Employee Deduction
            $total_emp_ded = 0;
            foreach($employee_deductions as $ac){
                $payroll_emp_details['employee_deductions'][$ac] = floatval($p->{$ac});
                $total_emp_ded += floatval($p->{$ac});
            }

            //Deducted benefits
            $total_ded_ben = 0;
            foreach($deducted_benefits as $ac){
                $payroll_emp_details['deducted_benefits'][$ac] = floatval($p->{$ac});
                $total_ded_ben += floatval($p->{$ac});
            } 

            //Cash Advance
            $payroll_emp_details['cash_advance']['balance'] = 0;
            $payroll_emp_details['cash_advance']['ca_amount'] =  floatval($p->ca);

           //  $cc = DB::table('payroll_ca as pc')
           //                                          ->select(DB::raw("cd.id_cash_disbursement,DATE_FORMAT(cd.date,'%m/%d/%Y') as date,cd.description,(debit-getCashAdvanceLessTotal(cd.id_cash_disbursement,$id_payroll)) as balance,pc.amount as deducted_amt"))
           //                                          ->leftJoin('cash_disbursement as cd','cd.id_cash_disbursement','pc.id_cash_disbursement')
           //                                          ->leftJoin('cash_disbursement_details as cdd','cdd.id_cash_disbursement','cd.id_cash_disbursement')
           //                                          ->leftJoin('chart_account as ca','ca.id_chart_account','cdd.id_chart_account')
           //                                          ->where('id_payroll',$id_payroll)
           //                                          ->where('pc.id_employee',$p->id_employee)
           //                                          ->where('ca.is_cash_advance',1);
           //                                          // ->get()
           // $payroll_emp_details['cash_advances'] =  DB::table( DB::raw("({$cc->toSql()}) as sub") )
           //                                          ->mergeBindings($cc)
           //                                          ->where('sub.balance','>',0)
           //                                          ->get();

            $payroll_emp_details['cash_advances'] = DB::select("SELECT *,concat(type,'-',ref) as id_ref,concat(upper(type),'#',ref) as ref_text FROM (
                                                    SELECT 'cdv' as type,cd.id_cash_disbursement as ref,DATE_FORMAT(cd.date,'%m/%d/%Y') as date,cd.description,(debit-getCashAdvanceLessTotal(cd.id_cash_disbursement,pc.id_payroll)) as balance,pc.amount as deducted_amt
                                                    FROM payroll_ca as pc
                                                    LEFT JOIN cash_disbursement as cd on cd.id_cash_disbursement = pc.id_cash_disbursement
                                                    LEFT JOIN cash_disbursement_details as cdd on cdd.id_cash_disbursement = cd.id_cash_disbursement
                                                    LEFT JOIN chart_account as ca on ca.id_chart_account = cdd.id_chart_account
                                                    WHERE pc.id_payroll = ? AND pc.id_employee=? AND ca.is_cash_advance = 1
                                                    UNION ALL
                                                    SELECT 'jv' as type,jv.id_journal_voucher as ref,DATE_FORMAT(jv.date,'%m/%d/%Y') as date,jv.description,(debit-getCashAdvanceLessTotalJV(jv.id_journal_voucher,pc.id_payroll)) as balance,pc.amount as deducted_amt
                                                    FROM payroll_ca as pc
                                                    LEFT JOIN journal_voucher as jv on jv.id_journal_voucher = pc.id_journal_voucher
                                                    LEFT JOIN journal_voucher_details as jvd on jvd.id_journal_voucher = jv.id_journal_voucher
                                                    LEFT JOIN chart_account as ca on ca.id_chart_account = jvd.id_chart_account
                                                    WHERE pc.id_payroll = ? AND pc.id_employee=? AND ca.is_cash_advance = 1) as sub
                                                    WHERE sub.balance > 0;",[$id_payroll,$p->id_employee,$id_payroll,$p->id_employee]);


            // return $payroll_emp_details['cash_advances'] ;

                                                   
            // $payroll_emp_details['cash_advances'] =                                        
            //Allowances
            $total_allowance= floatval($p->total_allowance);
            $allowance_out = $this->parseAllowances($id_payroll,$p->id_employee);
            $payroll_emp_details['allowances'] = $allowance_out['allowances'];
            $payroll_emp_details['employee_allowance'] = $allowance_out['employee_allowance'];

            //Totals
            $totals['total_additional_compensation'] = $total_add_com;
            $totals['total_employee_deduction'] = $total_emp_ded;
            $totals['total_deducted_benefits'] = $total_ded_ben;
            $totals['total_deducted_cash_advance'] = floatval($p->ca);
            $totals['total_employee_allowance'] = $total_allowance;
            $payroll_emp_details['totals'] = $totals;

            //BASIC PAY
            $payroll_emp_details['basic_pay'] = floatval($p->basic_pay);

            // Income
            $payroll_emp_details['income']['gross'] = $p->basic_pay+$total_add_com+$total_allowance;
            $payroll_emp_details['income']['net'] = $payroll_emp_details['income']['gross']-$total_emp_ded-$total_ded_ben-floatval($p->ca);

            // return $allowance_out;

            $payroll_employee_obj[$p->id_employee] = $payroll_emp_details;

            // array_push($payroll_employee_obj,$payroll_emp_details);
        }
        // return $payroll_employee_obj;
        $data['payroll_employee'] = $payroll_employee_obj;

        return view('payroll.payroll_form',$data);
        return $payroll_employee_obj;
    }

    public function parseAllowances($id_payroll,$id_employee){
        $allowances = DB::table('payroll_employee_allowances as pea')
                      ->select('an.description','pea.id_allowance_name','pea.amount','ea.type')
                      ->leftJoin('allowance_name as an','an.id_allowance_name','pea.id_allowance_name')
                      ->leftJoin('employee_allowance as ea',function($join) use ($id_employee){
                        $join->on('ea.id_allowance_name','pea.id_allowance_name')
                        ->on('ea.id_employee','pea.id_employee');
                      })
                      // ->leftJoin('employee_allowance as ea','ea.id_allowance_name','pea.id_allowance_name')
                      ->where('pea.id_employee',$id_employee)
                      ->where('pea.id_payroll',$id_payroll)
                      ->get();

        $output['allowances'] = $allowances;
        $output['employee_allowance'] = array();

        foreach($allowances as $al){
          
            $output['employee_allowance'][$al->id_allowance_name] = floatval($al->amount);
           
        }
        return $output;
    }                                                                                                                                                                                                                                                                                                                                                                                                 
    public function master_list(Request $request){
        if($request->ajax()){
            $data['employee_list'] = DB::table('employee as e')
                                     ->select(DB::raw("et.id_employee_type,id_employee,FormatName(e.first_name,e.middle_name,e.last_name,e.suffix) as name,et.description"))
                                     ->leftJoin('employee_type as et','et.id_employee_type','e.id_employee_type')
                                     ->orDerby('name')
                                     ->get();
            return response($data);
        }
    }
    public function PayrollSummaryExcel($id_payroll){
        // $data['payroll']= $this->payroll_data($id_payroll);
        // return view('payroll.payroll_summary',$data);

        $validator = $this->check_payroll_status($id_payroll);
        if($validator == "INVALID"){
            return array(
                'message' => "INVALID_REQUEST"
            );
        }
        $p = $this->payroll_data($id_payroll);
        $file_name = "PAYROLL SUMMARY ".$p[0]->payroll_type." ".str_replace("/","_",$p[0]->period);

       
        return Excel::download(new PayrollSummaryExport($p), "$file_name.xlsx");

        $data['payroll'] = DB::select("select pe.id_employee,FormatName(e.first_name,e.middle_name,e.last_name,e.suffix) as name,tb.branch_name,d.name as department,es.description as emp_status,p.description as position,
            pe.basic_pay,0 as cola,ot,total_allowance,13th_month as thir_month,0 as incentives,paid_leaves,holiday,others,salary_adjustment,gross_income,
            sss,hdmf,philhealth,wt,pe.insurance,ca,absences,late,sss_loan,hdmf_loan,
            (sss+hdmf+philhealth+wt+pe.insurance+ca+absences+late+sss_loan+hdmf_loan) as total_deduction,net_income
            FROM payroll_employee as pe 
            LEFT JOIN employee as e on e.id_employee = pe.id_employee
            LEFT JOIN tbl_branch as tb on tb.id_branch = e.id_branch
            LEFT JOIN department as d on d.id_department = e.id_department
            LEFT JOIN position as p on p.id_position = e.id_position
            LEFT JOIN employee_status as es on es.id_employee_status = e.id_employee_status
            where id_payroll=?",[$id_payroll]);

            return view('payroll.payroll_summary',$data);
    }
    public function PrintPayrollSummary($id_payroll){

        $validator = $this->check_payroll_status($id_payroll);
        if($validator == "INVALID"){
            return array(
                'message' => "INVALID_REQUEST"
            );
        }

        $data['payroll'] = $this->payroll_data($id_payroll);
        $file_name = $data['file_name'] = "PAYROLL SUMMARY ".$data['payroll'][0]->payroll_type." ".str_replace("/","_",$data['payroll'][0]->period);



        $html =  view('payroll.payroll_summary_pdf',$data);

        $pdf = PDF::loadHtml($html);
        $pdf->setOption("encoding","UTF-8");
        $pdf->setOption('margin-bottom', '5mm');
        $pdf->setOption('margin-top', '7mm');
        $pdf->setOption('margin-right', '5mm');
        $pdf->setOption('margin-left', '5mm');
        
        $pdf->setOption('header-left', 'Page [page] of [toPage]');

        $pdf->setOption('header-font-size', 8);
        $pdf->setOption('header-font-name', 'Calibri');
        $pdf->setOrientation('landscape');

        return $pdf->stream("$file_name.pdf");
        
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
            
          }
        ');
        $dompdf->stream("repayment_summary.pdf", array("Attachment" => false));  
        exit;
        return view('payroll.payroll_summary_pdf',$data);
        return $data;
    }
    public function print_payroll_payslip($id_payroll,Request $request){

        $validator = $this->check_payroll_status($id_payroll);
        if($validator == "INVALID"){
            return array(
                'message' => "INVALID_REQUEST"
            );
        }

        $data['payroll'] = $this->payroll_data($id_payroll);

 
        $html =  view('payroll.print_payslip',$data);
        if($request->debug == 1){
            return $html;
        }
        // return $html;   

        $pdf = PDF::loadHtml($html);
        $pdf->setOption("encoding","UTF-8");
        $pdf->setOption('margin-bottom', '5mm');
        $pdf->setOption('margin-top', '7mm');
        $pdf->setOption('margin-right', '5mm');
        $pdf->setOption('margin-left', '5mm');
        

        $pdf->setOption('header-font-size', 8);
        $pdf->setOption('header-font-name', 'Calibri');

        return $pdf->stream();

        return $data;
    }

    public function check_payroll_status($id_payroll){
            $result = DB::table('payroll')->select('status')->where('id_payroll',$id_payroll)->first();

            $response = ((!isset($result)) || $result->status >= 10 )?"INVALID":"ALLOWED";

            return $response;
    }

    public function payroll_data($id_payroll){
        return DB::select("select payroll.id_payroll,concat('PAYSLIP - ',UPPER(pm.description),' PAYROLL') as payslip_description,UPPER(pm.description) as payroll_type,concat(DATE_FORMAT(payroll.period_start,'%m/%d/%Y'),' - ',DATE_FORMAT(payroll.period_end,'%m/%d/%Y'),if(payroll.id_payroll_mode=3,concat(' (',payroll.no_days,' Days)'),'')) as period,pe.id_employee,UPPER(FormatName(e.first_name,e.middle_name,e.last_name,e.suffix)) as name,tb.branch_name,d.name as department,UPPER(es.description) as emp_status,UPPER(p.description) as position,
        pe.basic_pay,0 as cola,ot,night_shift_dif,total_allowance,13th_month as thir_month,0 as incentives,paid_leaves,holiday,others,salary_adjustment,gross_income,
        sss,hdmf,philhealth,wt,pe.insurance,ca,absences,late,sss_loan,hdmf_loan,
        (13th_month+paid_leaves+holiday+others+salary_adjustment+night_shift_dif) as total_adjustments,
        (sss+hdmf+philhealth+wt+pe.insurance+ca+absences+late+sss_loan+hdmf_loan) as total_deduction,net_income,pe.remarks
        FROM payroll_employee as pe 
        LEFT JOIN employee as e on e.id_employee = pe.id_employee
        LEFT JOIN tbl_branch as tb on tb.id_branch = e.id_branch
        LEFT JOIN department as d on d.id_department = e.id_department
        LEFT JOIN position as p on p.id_position = e.id_position
        LEFT JOIN employee_status as es on es.id_employee_status = e.id_employee_status
        LEFT JOIN payroll on payroll.id_payroll = pe.id_payroll
        LEFT JOIN payroll_mode as pm on pm.id_payroll_mode = payroll.id_payroll_mode
        where pe.id_payroll=? ",[$id_payroll]);
    }
    public function post_cancel(Request $request){
        if($request->ajax()){
            $id_payroll = $request->id_payroll;
            $cancellation_reason = $request->cancellation_reason ?? '';

            $data['RESPONSE_CODE'] = "SUCCESS";

            // validation
            $validation = DB::table('payroll')->select('status','id_cash_disbursement')->where('id_payroll',$id_payroll)->first();

           if($validation->status == 10 || !isset($validation)){
                $data['RESPONSE_CODE'] = "ERROR";
                $data['message'] = "Invalid Request";

                return response($data);
           }

           DB::table('payroll')
           ->where('id_payroll',$id_payroll)
           ->update(['status'=>10,'cancellation_reason'=>$cancellation_reason]);

           // DB::table('cash_disbursement')
           // ->Where('type',5)
           // ->where('reference',$id_payroll)
           // ->update(['status'=>10,'description'=>DB::raw("concat(description,' [CANCELLED]')")]);

            DB::table('cash_disbursement')
            ->where('id_cash_disbursement',$validation->id_cash_disbursement)
            ->update(['status'=>10,'description'=>DB::raw("concat(description,' [CANCELLED]')"),'cancellation_reason'=>$cancellation_reason,'date_cancelled'=>DB::raw("now()")]);



            return response($data);
        }
    }

    public function generate_cdv_entry($id_payroll,$id_employee){


        //CHECK IF THE PAYROLL HAS CDV
        $max_id = DB::table('cash_disbursement')
                 ->where('type',5)
                 ->where('id_employee',$id_employee)
                 ->where('reference',$id_payroll)
                 ->max('id_cash_disbursement');

        if(!isset($max_id)){ //INSERT
         //PARENT
            DB::select("INSERT INTO cash_disbursement (date,type,description,id_employee,payee,reference,status,total,id_branch,address,paymode,paymode_account,payee_type)                    
            SELECT period_end,5 as type,CONCAT('Payment for CDV# ',group_concat(pc.id_cash_disbursement)) as description,pc.id_employee,FormatName(e.first_name,e.middle_name,e.last_name,e.suffix) as payee,p.id_payroll as reference,0 as status,
            SUM(pc.amount) as amount,e.id_branch,e.address,1 as paymode,1 as paymode_account,3 as payee_type
            FROM payroll as p 
            LEFT JOIN payroll_ca as pc on pc.id_payroll = p.id_payroll
            LEFT JOIN employee as e on pc.id_employee = e.id_employee
            where p.id_payroll =? and pc.id_employee= ?;",[$id_payroll,$id_employee]);

            $id_cash_disbursement = DB::table('cash_disbursement')->where('type',5)->max('id_cash_disbursement');           
        }else{
            $id_cash_disbursement = $max_id;

            // return DB::select("SELECT cd.total, pe.ca FROM payroll_employee as pe 
            //                     LEFTs JOIN cash_disbursement as cd on cd.id_cash_disbursement = pe.id_cash_disbursement
            //                     where pe.id_payroll = ? and pe.id_employee = ?",[$id_payroll,$id_employee]);
            DB::select("UPDATE payroll_employee as pe 
                        LEFT JOIN cash_disbursement as cd on cd.id_cash_disbursement = pe.id_cash_disbursement
                        SET cd.total = pe.ca
                        where pe.id_payroll = ? AND pe.id_employee = ?;",[$id_payroll,$id_employee]);

            DB::table('cash_disbursement_details')
            ->where('id_cash_disbursement',$id_cash_disbursement)
            ->delete();
            // DB::table('')
        }    



        //CHILD

        DB::select("INSERT INTO cash_disbursement_details (id_cash_disbursement,id_chart_account,account_code,description,debit,credit,remarks,reference,id_account_code_maintenance)
            SELECT ? as id_cash_disbursement,ac.id_chart_account,ca.account_code,ca.description,SUM(pca.amount) as debit,0 as credit,'' as remarks,0,ac.id_account_code_maintenance FROM payroll_ca as pca
            LEFT JOIN account_code_maintenance as ac on ac.id_account_code_maintenance =12
            LEFT JOIN chart_account as ca on ca.id_chart_account = ac.id_chart_account
            where pca.id_payroll = ? and pca.id_employee = ?
            UNION ALL
            SELECT ? as id_cash_disbursement,ac.id_chart_account,ca.account_code,ca.description,0 as debit,pca.amount as credit,concat('PAYMENT FOR CDV# ',id_cash_disbursement) as remarks,id_cash_disbursement,ac.id_account_code_maintenance FROM payroll_ca as pca
            LEFT JOIN account_code_maintenance as ac on ac.id_account_code_maintenance =13
            LEFT JOIN chart_account as ca on ca.id_chart_account = ac.id_chart_account
            where pca.id_payroll = ? and pca.id_employee = ?;",[$id_cash_disbursement,$id_payroll,$id_employee,$id_cash_disbursement,$id_payroll,$id_employee]);


        DB::table('payroll_employee')
        ->where('id_payroll',$id_payroll)
        ->where('id_employee',$id_employee)
        ->update(['id_cash_disbursement'=>$id_cash_disbursement]);

        // return $id_cash_disbursement;


    }

    public function check_status(Request $request){
        if($request->ajax()){
            $id_payroll = $request->id_payroll;


            $result = $this->check_payroll_status($id_payroll);

            $data['RESPONSE_CODE'] = $result;

            return response($data);

            return response($id_payroll);
        }
    }
}



// select pe.id_employee,concat(first_name,' ',last_name,' ',suffix) as name,tb.branch_name,d.name as department,p.description as position,
// pe.basic_pay,0 as cola,ot,total_allowance,13th_month,0 as incentives,paid_leaves,holiday,others,salary_adjustment,gross_income,
// sss,hdmf,philhealth,wt,pe.insurance,ca,absences,late,sss_loan,hdmf_loan,
// (sss+hdmf+philhealth+wt+pe.insurance+ca+absences+late+sss_loan+hdmf_loan) as total_deduction,net_income
// FROM payroll_employee as pe 
// LEFT JOIN employee as e on e.id_employee = pe.id_employee
// LEFT JOIN tbl_branch as tb on tb.id_branch = e.id_branch
// LEFT JOIN department as d on d.id_department = e.id_department
// LEFT JOIN position as p on p.id_position = e.id_position
// where id_payroll=7




// INSERT INTO cash_disbursement (date,type,description,id_employee,payee,reference,status,total,id_branch,address,paymode,paymode_account,payee_type)                    
// SELECT period_end,5 as type,CONCAT('Payment for CDV# ',group_concat(id_cash_disbursement)) as description,pc.id_employee,concat(e.first_name,' ',e.last_name,' ',e.suffix) as payee,p.id_payroll as reference,0 as status,
// SUM(pc.amount) as amount,e.id_branch,e.address,1 as paymode,1 as paymode_account,3 as payee_type
// FROM payroll as p 
// LEFT JOIN payroll_ca as pc on pc.id_payroll = p.id_payroll
// LEFT JOIN employee as e on pc.id_employee = e.id_employee
// where p.id_payroll =9 and pc.id_employee= 6;




// PAYROLL
// SELECT SUM(basic_pay+ot+night_shift_dif+holiday+paid_leaves+salary_adjustment+13th_month+others),'Salaries & wages'
// FROM payroll_employee as pe 
// where pe.id_payroll = 3
// UNION ALL
// SELECT SUM(total_allowance) ,'Officers & Staff Benefits'
// FROM payroll_employee as pe 
// where pe.id_payroll = 3
// UNION ALL
// SELECT SUM(sss+philhealth+hdmf+insurance+wt+sss_loan+hdmf_loan),'Employees Benefits Payable'
// FROM payroll_employee as pe 
// where pe.id_payroll = 3
// UNION ALL
// SELECT SUM(ca),'Advances to Officers and Employees'
// FROM payroll_employee as pe 
// where pe.id_payroll = 3
// UNION ALL
// SELECT SUM(absences+late), 'Salaries & Wages'
// FROM payroll_employee as pe 
// where pe.id_payroll = 3;