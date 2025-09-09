<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MySession;
use DB;
use App\Loan;
use App\Member;
use App\CredentialModel;
use Dompdf\Dompdf;
use DateTime;
use PDF;
use App\WebHelper;
// use \NumberFormatter;

class LoanApplicationController extends Controller
{
    public function current_month(){
        date_default_timezone_set('Asia/Manila');
        $dt = new DateTime();
        return 2;

       
        return (int)$dt->format('m');
    }

    public function search_member(Request $request){
        // if($request->ajax()){
        $search = $request->term;   
        if(strlen($search) < 3){
            $data['accounts'] = array();
            return response($data);
        }
        $my_id = MySession::myId();
        $data['accounts'] = DB::table('member as m')
        ->select(DB::raw("concat(membership_id,' || ',FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)) as tag_value,id_member as tag_id"))
        ->where(function ($query) use ($search,$my_id){
            $query->where(DB::raw("concat(first_name,' ',last_name)"), 'like', "%$search%");
            // ->where('m.id_member','<>',$my_id);
        })
        ->where('m.status',1)
        ->get();

        return response($data);
        // }
    }

    public function validate_summary(Request $request){
        if($request->ajax()){
            // $transaction_date = $request->transaction_date;
            // $transaction_type = $request->transaction_type;

            // return $transaction_type;
            $count = DB::table('loan')
            ->where('loan_status',1)
            ->count();

            if($count == 0){
                $response['RESPONSE_CODE'] = "ERROR";
                $response['message'] = "No Active Loan Found";

                return response($response);
            }

            $response['RESPONSE_CODE'] = "SUCCESS";
            // $response['transaction_date'] = $transaction_date;
            // $response['transaction_type'] = $transaction_type;

            return response($response);
        }
    }

    public function generate_active_loan($type){
        $date = WebHelper::ConvertDatePeriod(MySession::current_date());
        $loan_list = DB::select("CALL ActiveLoanSummary(?,?)",[$date,$type]);
        $g = new GroupArrayController();


        $data['type'] = $type;

        $data['group_key']=$group_key = ($type==1)?'borrower_name':'ls';

        // return $loan_list;
        $data['loan_list'] = $g->array_group_by($loan_list,[$group_key]);

        $data['current_date'] = date("F d,Y", strtotime(MySession::current_date()));

        $html =  view('loan.active_loan',$data);
        
        // $dompdf = new Dompdf();
        // $dompdf->set_option("isRemoteEnabled",false);
        // $dompdf->set_option("isPhpEnabled", true);

        // $dompdf->loadHtml($html);
        // $dompdf->render();
        // $font = $dompdf->getFontMetrics()->get_font("helvetica","normal");
        // $dompdf->getCanvas()->page_text(24, 18, "Page {PAGE_NUM} of {PAGE_COUNT}", $font, 8, array(0,0,0));
        // $canvas = $dompdf->getCanvas();
        // $canvas->page_script('
        //   if ($PAGE_NUM > 1) {
        //     $font = $fontMetrics->getFont("helvetica","normal");
        //     $current_page = $PAGE_NUM-1;
        //     $total_pages = $PAGE_COUNT-1;
        //   }
        // ');
        // $pdf->text(480, 18, "Control No.: '.$data['details']->control_number.'", $font, 8, array(0,0,0));
        //     $pdf->text(480, 30, "Account No.: '.$data['details']->account_no.'", $font, 8, array(0,0,0));
        // $dompdf->stream("repayment_summary.pdf", array("Attachment" => false));  
        
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

        return $pdf->stream();
        exit;
        // return 
        return $data;
    }
    
    public function index(Request $request){



        return $this->index_new($request);

        $data['credential']= CredentialModel::GetCredential(MySession::myPrivilegeId());
        $data['head_title'] = "Loans";
        // return $data;
        $data['current_date'] = MySession::current_date();
        $data['loan_service_lists'] = DB::table('loan_service')
        ->select("id_loan_service","name")
        ->where('status',1)
        ->orDerby('name')
        ->get();
        $data['isAdmin'] = MySession::isAdmin();

        $data['fill_date_start'] = $request->filter_start_date ?? date('Y-m-d', strtotime('-6 months'));
        $data['fill_date_end'] = $request->filter_end_date ?? MySession::current_date();

        $data['sel_filter_date_type'] = isset($request->filter_date_type)?$request->filter_date_type:1;
        $data['date_check'] = isset($request->date_check)?$request->date_check:1;

        if(!$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }

        $with_filter = false;
        $data['loan_lists'] = DB::table('loan as ap_loan')
        ->select(DB::raw("ap_loan.loan_token,ap_loan.id_loan,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member_name,getLoanServiceName(ap_loan.id_loan_payment_type,ls.name,ap_loan.terms) as loan_service_name,
            ap_loan.principal_amount as principal_amount,DATE_FORMAT(ap_loan.date_created,'%m/%d/%Y') as application_date,DATE_FORMAT(ap_loan.date_released,'%m/%d/%Y') as date_released,
            loanStatus(ap_loan.status) as loan_status,ap_loan.status as status_code,@q:=getLoanTotalPaymentType(ap_loan.id_loan,1) as paid_principal,(ap_loan.principal_amount-@q) as principal_balance,ap_loan.interest_rate"))
        ->leftJoin("member as m","m.id_member","ap_loan.id_member")
        ->leftJoin("loan_service as ls","ls.id_loan_service","ap_loan.id_loan_service")
        ->where(function($query) use ($data){
            if(!$data['isAdmin']){
                $query->where('ap_loan.id_member',MySession::myId());
            }
        })
        ->where(function($query) use ($request,$with_filter,$data){

            // if(isset($request->filter_status) && $request->filter_status != "ALL"){
            //     $query->where('ap_loan.status',$request->filter_status);
            //     $with_filter = true;
            // }

            if(isset($request->id_member)){
                $query->where('ap_loan.id_member',$request->id_member);
                $with_filter = true;
            }

            if($data['date_check'] == 1){
                if($data['sel_filter_date_type'] == 1){ // date created
                    $query->whereRaw("DATE(ap_loan.date_created) >= ?",[$data['fill_date_start']])
                    ->whereRaw("DATE(ap_loan.date_created) <= ?",[$data['fill_date_end']]);
                    $with_filter = true;
                }elseif($data['sel_filter_date_type'] == 2){
                    $query->whereRaw("DATE(ap_loan.date_released) >= ?",[$data['fill_date_start']])
                    ->whereRaw("DATE(ap_loan.date_released) <= ?",[$data['fill_date_end']]);   
                    $with_filter = true;                 
                }
            }

            if(isset($request->filter_loan_service) && $request->filter_loan_service > 0){
                $query->where("ap_loan.id_loan_service",$request->filter_loan_service);
                $with_filter = true;
            }

            if(!$with_filter){
                // if($request->filter_status != "ALL"){
                //     $query->where('ap_loan.status',0);
                // }
            }

        })
        ->orderBy('ap_loan.status','ASC')
        ->orDerby('ap_loan.id_loan','DESC')
        ->get();

        $data['viewing_route'] = ($data['isAdmin'])?"/loan/application/approval/":"/loan/application/view/";
        $data['selected_member'] =DB::table('member as m')
        ->select(DB::raw("concat(membership_id,' || ',FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)) as member_name,id_member"))
        ->where('id_member',$request->id_member)
        ->first();

            // return $data;
        // return $data['viewing_route'];
        return view('loan.index',$data);

        return $data;

    }

    public function index_new(Request $request){
        $data['credential']= CredentialModel::GetCredential(MySession::myPrivilegeId());
        $data['isAdmin'] = MySession::isAdmin();

        $data['fill_date_start'] = $request->filter_start_date ?? date('Y-m-d', strtotime('-6 months'));
        $data['fill_date_end'] = $request->filter_end_date ?? MySession::current_date();

        $data['sel_filter_date_type'] = isset($request->filter_date_type)?$request->filter_date_type:2;
        $data['date_check'] = isset($request->date_check)?$request->date_check:0;
        $data['loan_service_lists'] = DB::table('loan_service')
        ->select("id_loan_service","name")
        ->where('status',1)
        ->orDerby('name')
        ->get();

        if(!$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }

        /***********
         * Tabs
         * All - All (with date filter)
         * 0 - Submitted
         * 1 - Processing
         * 2 - Approved
         * 3 - Active
         * 4/5 - Cancelled Disapproved
         * 6 - Closed
         * ************/ 
        $default = 3;
        $data['head_title'] = "Loans";
        $data['viewing_route'] = (MySession::isAdmin())?"/loan/application/approval/":"/loan/application/view/";
        $data['current_tab'] = $request->status ?? $default;

        $data['current_tab'] = is_numeric($data['current_tab'])?intval($data['current_tab']):$data['current_tab'];

        $counts = DB::table('loan')
        ->select(DB::raw('if(status in (4,5),4,status) as status_in,COUNT(*) as count'))
        ->where(function($query) use ($data,$request){
            if(!MySession::isAdmin()){
                $query->where('loan.id_member',MySession::myId());
            }else{
                if(isset($request->id_member)){
                    $query->where('id_member',$request->id_member);
                    $with_filter = true;
                }
                if($data['date_check'] == 1){
                    if($data['sel_filter_date_type'] == 1){ // date created
                        $query->whereRaw("DATE(date_created) >= ?",[$data['fill_date_start']])
                        ->whereRaw("DATE(date_created) <= ?",[$data['fill_date_end']]);
                        $with_filter = true;
                    }elseif($data['sel_filter_date_type'] == 2){
                        $query->whereRaw("DATE(date_released) >= ?",[$data['fill_date_start']])
                        ->whereRaw("DATE(date_released) <= ?",[$data['fill_date_end']]);   
                        $with_filter = true;                 
                    }
                }
                if(isset($request->filter_loan_service) && $request->filter_loan_service > 0){
                    $query->where("id_loan_service",$request->filter_loan_service);
                    $with_filter = true;
                }
            }
        })
        ->groupBy('status_in')
        ->get();


        $g = new GroupArrayController();
        $data['loan_counts'] = $g->array_group_by($counts,['status_in']);

        $data['loan_lists'] = DB::table('loan as ap_loan')
        ->select(DB::raw("ap_loan.loan_token,ap_loan.id_loan,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member_name,getLoanServiceName(ap_loan.id_loan_payment_type,ls.name,ap_loan.terms) as loan_service_name,
            ap_loan.principal_amount as principal_amount,DATE_FORMAT(ap_loan.date_created,'%m/%d/%Y') as application_date,DATE_FORMAT(ap_loan.date_released,'%m/%d/%Y') as date_released,
            loanStatus(ap_loan.status) as loan_status,ap_loan.status as status_code,@q:=getLoanTotalPaymentType(ap_loan.id_loan,1) as paid_principal,(ap_loan.principal_amount-@q) as principal_balance,ap_loan.interest_rate"))
        ->leftJoin("member as m","m.id_member","ap_loan.id_member")
        ->leftJoin("loan_service as ls","ls.id_loan_service","ap_loan.id_loan_service")
        ->where(function($query) use ($data,$request){
            if(!MySession::isAdmin()){
                $query->where('ap_loan.id_member',MySession::myId());
            }else{
                if(isset($request->id_member)){
                    $query->where('ap_loan.id_member',$request->id_member);
                    $with_filter = true;
                }
                if($data['date_check'] == 1){


                    if($data['sel_filter_date_type'] == 1){ // date created
                        $query->whereRaw("DATE(ap_loan.date_created) >= ?",[$data['fill_date_start']])
                        ->whereRaw("DATE(ap_loan.date_created) <= ?",[$data['fill_date_end']]);
                        $with_filter = true;
                    }elseif($data['sel_filter_date_type'] == 2){
                        $query->whereRaw("DATE(ap_loan.date_released) >= ?",[$data['fill_date_start']])
                        ->whereRaw("DATE(ap_loan.date_released) <= ?",[$data['fill_date_end']]);   
                        $with_filter = true;                 
                    }
                }
                if(isset($request->filter_loan_service) && $request->filter_loan_service > 0){
                    $query->where("ap_loan.id_loan_service",$request->filter_loan_service);
                    $with_filter = true;
                }
            }

            if($data['current_tab'] !== "All"){
                $fil_status = $data['current_tab'] == 4 ? [4,5] : [$data['current_tab']];
                $query->whereIn('ap_loan.status',$fil_status);
            }
        })
        ->orderBy('ap_loan.id_loan','DESC')

        ->get();

        $data['selected_member'] =DB::table('member as m')
        ->select(DB::raw("concat(membership_id,' || ',FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)) as member_name,id_member"))
        ->where('id_member',$request->id_member)
        ->first();
        // dd($data);

        // dd($data);
        return view('loan.index_new',$data);
    }
    public function parseLoanFrameData($id_loan_service,$term_reference,$principal_amount,$id_member,$pay_active_loan,$other_deductions,$due_year){

     
        $data = array();
        $data['isAdmin'] = MySession::isAdmin();
        $data['PARAMETERS_VALID'] = true;



        $pay_active_loan = $pay_active_loan ?? [];

        $interest_multiplier = 1;



        // $id_loan_service = $request->id_loan_service;
        // $term_reference = $request->term_reference;
        // $principal_amount = $request->principal_amount;
        


        // GET LOAN SERVICE TYPE (INSTALLMENT OR ONE TIME PAYMENT)

        $service_validation = DB::table('loan_service')->select('id_loan_service','id_loan_payment_type','start_month_period','end_month_period','id_one_time_type')->where('id_loan_service',$id_loan_service)->first();



        // dd($id_loan_service);


        if(!isset($service_validation)){
            return;
        }
        $selected_loan_payment_type = $service_validation->id_loan_payment_type;

        if(($selected_loan_payment_type == 1 && !isset($term_reference)) || (!isset($principal_amount)) ||  ($data['isAdmin'] && !isset($id_member))){
            return;
        }

        if($selected_loan_payment_type == 2){
            $month_applied =MySession::current_month();
            // dd($month_applied,$service_validation);       
            // $month_applied = 5;
                // dd($month_applied);
                if($service_validation->id_one_time_type == 1){
                    if($service_validation->end_month_period >= $service_validation->start_month_period){
                        $month_applied = ($month_applied < $service_validation->start_month_period || $month_applied > $service_validation->end_month_period)?$service_validation->start_month_period:$month_applied;
                    }else{
                        $monthRange = array();
                        $monthLimit = $service_validation->start_month_period+$service_validation->end_month_period+1;

                        for($i=$service_validation->start_month_period;$i<=$monthLimit;$i++){
                            array_push($monthRange,($i > 12)?($i-12):$i);
                        }
                        
                        $month_applied = (in_array($month_applied,$monthRange))?$month_applied:$monthRange[0];
                    }
                    
            
                    
                    $t = DB::table('terms')
                    ->select("terms_token","period")
                    ->where('id_loan_service',$id_loan_service)
                    // ->where('period',DB::raw("DATE_FORMAT(curdate(),'%c')"))
                    ->where('period',$month_applied)
                    ->first();

                    $period = $t->period;
                    $term_reference = $t->terms_token;  
                             
                }else{
                   // dd($service_validation->end_month_period);

                    $one = Loan::OneTimeOpen(['id_loan_service'=>$id_loan_service,'due_year'=>$due_year]);

                    $interest_multiplier = $one['duration'];

                    $t = DB::table('terms')
                    ->select("terms_token","period")
                    ->where('id_loan_service',$id_loan_service)
                    ->first();                   

                    $period = $t->period;
                    $term_reference = $t->terms_token;  
               


                }
        }


        $service_details = DB::table('loan_service as ls')
        ->select(DB::raw("getLoanServiceName(ls.id_loan_payment_type,ls.name,terms.terms) as name"),"ls.id_loan_service","terms.terms","terms.period","ls.cbu_amount","ls.is_deduct_cbu",DB::raw("$principal_amount as principal_amount,terms.loan_protection_rate,ls.id_term_period,ls.id_interest_period,ls.id_interest_method,if(ls.id_loan_payment_type =1,concat(terms.terms,' ',p.description),getMonth(terms.period)) as terms_desc,terms.terms_token,ls.min_amount,ls.max_amount,ls.id_loan_payment_type,concat(getMonth(ls.end_month_period),' ',repayment_schedule,', ',YEAR(curdate())) as due_date,ls.deduct_interest,ls.is_multiple,ls.id_terms_condition,$interest_multiplier as month_duration,terms.interest_rate*$interest_multiplier as interest_rate,interest_rate as interest_show"))
        ->leftJoin('terms','ls.id_loan_service','terms.id_loan_service')
        ->where('terms_token',$term_reference)
        ->where('ls.id_loan_service',$id_loan_service)
        ->leftJoin('period as p','p.id_period','ls.id_term_period')
        ->first();



        if($service_details->id_terms_condition == 0){
            $min_principal_validation = $service_details->min_amount;
            $max_principal_validation = $service_details->max_amount;

        }else{
            $member_cbu = Member::getCBU($id_member);

            $tc = Loan::ParseTermConditionDetails($service_details->id_terms_condition,$member_cbu);

            if(!isset($tc)){
                $data['PARAMETERS_VALID'] = false;
                $data['ERROR_TEXT'] = "Selected Member can't avail the loan service due to minimum CBU requirement not met  [Current CBU: ".number_format($member_cbu,2)."]";

                $data['CODE'] = "ERROR";
                return $data;                  
            }

            $min_principal_validation = $tc->min_principal;
            $max_principal_validation = $tc->max_principal;   

            $up_to_terms = $tc->up_to_terms;
            // $up_to_terms = 10;
            if($service_details->terms > $up_to_terms){
                $data['PARAMETERS_VALID'] = false;
                $data['ERROR_TEXT'] = "Invalid loan term (up to $up_to_terms month(s) only)";

                $data['CODE'] = "ERROR";
                return $data;                
            }   
        }

        // return $pay_active_loan;

        $data['member_details'] = DB::table('member as m')->select(DB::raw("FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as name"))->where('id_member',$id_member)->first();

        if($principal_amount < $min_principal_validation || $principal_amount > $max_principal_validation){
            $data['PARAMETERS_VALID'] = false;
            $data['ERROR_TEXT'] = " Principal amount must not be less than ".number_format($min_principal_validation,2)." and not more than ".number_format($max_principal_validation,2);

            $data['CODE'] = "ERROR";
            return $data;

        }


        $data['CODE'] = "SUCCESS";
        $data['service_details'] = $service_details;

        $first_loan = Member::CheckFirstLoan($id_member,$id_loan_service);
        $charges = $this->parseCharges($service_details,$first_loan);

        // return $charges;     

        $loan_protection_rate = $service_details->loan_protection_rate;
        $cbu = Member::CheckCBU($data['service_details']->cbu_amount,$id_member);

        $previous_loan = Member::CheckPreviousLoan($id_loan_service,$id_member,$term_reference);


        // dd($previous_loan);
        $loan_parameter = [
            'id_member'=>$id_member,
            'cbu_deficient' => $cbu['difference'],
            'charges' => $charges,
            'principal_amount' =>$service_details->principal_amount,
            'interest_rate'=> $service_details->interest_rate,
            'terms' => $service_details->terms,
            'term_period' => $service_details->id_term_period,
            'interest_pediod'=>$service_details->id_interest_period,
            'interest_method' => $service_details->id_interest_method,
            'is_cbu_deduct' => $service_details->is_deduct_cbu,
            'loan_protection_rate' => $loan_protection_rate,
            'payment_type' => $service_details->id_loan_payment_type,
            'previous_loan' => $previous_loan,
            'deduct_interest'=>$service_details->deduct_interest,
            'id_loan_service'=>$id_loan_service,
            'interest_multiplier'=>$interest_multiplier,
            'other_deductions'=>$other_deductions ?? []
        ];





        if(count($pay_active_loan) > 0){
         
            $loan_parameter['loan_payment'] = Loan::parseExistingLoanBalance($id_loan_service,$term_reference,$id_member,MySession::current_date(),$pay_active_loan,2);


        }
        // return Loan::ComputeLoan($loan_parameter);

        $data['loan'] = Loan::ComputeLoan($loan_parameter);


        $c_date = WebHelper::ConvertDatePeriod2(MySession::current_date());
        if($service_details->is_multiple == 1){
            $data['active_multiple'] = DB::table('loan')
            ->select(DB::raw("loan.id_loan,loan.loan_token,getLoanBalanceAsOf(loan.id_loan,'$c_date') as balance"))
            ->where('id_member',$id_member)
            ->where('id_loan_service',$id_loan_service)
            ->where('loan.loan_status',1) 
            ->get();
        }


        $data['actives'] = 123444;
        $data['month_duration'] = $interest_multiplier;




        return $data;
    }
    public function table_loan_frame(Request $request){
        $id_loan_service = $request->id_loan_service;
        $term_reference = $request->term_reference;
        $principal_amount = $request->principal_amount;

        // return $request->active_loan_payment;
        $pay_active_loan = json_decode($request->active_loan_payment,true);

        $id_member = (MySession::isAdmin())?$request->id_member:MySession::myId();
        $other_deductions = json_decode($request->other_deductions,true);
       


        // return 123;
        // try{}
        $data  = $this->parseLoanFrameData($id_loan_service,$term_reference,$principal_amount,$id_member,$pay_active_loan,$other_deductions,$request->due_year);

        // return $data;
        if($data['CODE'] == "ERROR"){
            return view('loan.invalid_frame',$data);
        }
        return view('loan.loan_table_frame',$data);
    }
    public function ParseLoanServiceAvail(Request $request){
        $id_member = $request->id_member;

        $data['loan_services'] =DB::select("SELECT ls.id_loan_service,ls.name FROM member as m
        LEFT JOIN loan_service as ls on ls.id_membership_type = m.memb_type
        WHERE m.id_member = ? AND ls.status = 1 ORDER BY ls.name;",[$id_member]); 

        return $data;

        return response($data);

    }
    public function create(Request $request){
        //FOR LOAN TESTING
        // return Loan::ComputeLoan($loan_parameter);
        // return MySession::myId();

        // dd("WOW");



        $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/loan');

        if(!$data['credential']->is_create){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
        $data['head_title'] = "Loan Application";
        $data['WITH_LOAN_SERVICE'] = false;
        $data['INVALID_APPLICATION'] = true;
        $data['INVALID_APPLICATION_BLOCK'] = false;

        $data['isAdmin'] = MySession::isAdmin();

        // $data['loan_service_lists'] = DB::table('loan_service')
        // ->select("id_loan_service","name")
        // ->where('status',1)
        // ->orDerby('name')
        // ->get();



        $data['loan_service_lists'] = [];
        $id_member = ($data['isAdmin'])?$request->id_member:MySession::myId();


        if(!$data['isAdmin']){
            $r = new Request(['id_member'=>$id_member]);
            $data['loan_service_lists'] = $this->ParseLoanServiceAvail($r)['loan_services'];
        }

        
        $data['red_reference'] = $request->red_reference;
        $data['opcode'] = 0;
        if(isset($request->loan_reference)){

            if($data['isAdmin']){
                $r = new Request(['id_member'=>$id_member]);
                $data['loan_service_lists'] = $this->ParseLoanServiceAvail($r)['loan_services'];
            }



            // return 123;
            $data['TERMS_TOKEN'] = $request->terms_token;
            $id_loan_service = $request->loan_reference;



            $data['ERROR_MESSAGE'] = array();
            $data['ERROR_MESSAGE_BLOCK'] = array();
            $data['CHANGE_SERVICE'] = true;
            $data['INVALID_APPLICATION'] = false;

            $selected_service = DB::table('loan_service')->select('id_loan_service','name','id_terms_condition','id_one_time_type')->where('id_loan_service',$request->loan_reference)->first();


            $selected_member  = DB::table('member as m')
            ->select(DB::raw("concat(membership_id,' || ',FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)) as member_name,id_member,m.status"))
            ->where('id_member',$id_member)
            ->first();


            $data['selected_service'] = $selected_service;

            $data['selected_member'] = $selected_member;

            //check loan service base on member

            $data['ls_validation'] = DB::select("SELECT ls.id_loan_service,ls.name as loan_service FROM member as m
            LEFT JOIN loan_service as ls on ls.id_membership_type = m.memb_type
            WHERE m.id_member = ? AND ls.id_loan_service = ?;",[$id_member,$request->loan_reference]);

            if(count($data['ls_validation']) == 0){
                $data['INVALID_APPLICATION_BLOCK'] = true;
                $data['WITH_LOAN_SERVICE'] = false;
                array_push($data['ERROR_MESSAGE_BLOCK'],"Invalid Loan Service");

                return view('loan.loan_form',$data);               
            }




            if($selected_member->status == 0){
                $data['INVALID_APPLICATION_BLOCK'] = true;
                $data['WITH_LOAN_SERVICE'] = false;

                array_push($data['ERROR_MESSAGE_BLOCK'],"INACTIVE MEMBER ACCOUNT");
            // return $data;
                return view('loan.loan_form',$data);
            }
            $data['WITH_LOAN_SERVICE'] = true;
            $data['id_member'] = $id_member;

            $check_pending_loan = Member::CheckPendingLoanApplication($id_member,$id_loan_service);

            if($check_pending_loan['has_pending_application']){


                $data['WITH_LOAN_SERVICE'] = false;
                $data['INVALID_APPLICATION_BLOCK'] = true;
                $loan_token = $check_pending_loan['loan_token'];
                $id_loan = $check_pending_loan['id_loan'];
                $back_link = (request()->get('href') == '')?'/loan':request()->get('href');
                $redirect_link = "/loan/application/view/$loan_token?href=$back_link";

                array_push($data['ERROR_MESSAGE_BLOCK'],"You have a pending Loan Application on this Loan Service. <a href='$redirect_link' target='_blank'>Loan ID# $id_loan</a>");
                return view('loan.loan_form',$data);
            }

            // return Member::CheckAgeCondition($id_loan_service,$id_member);
            $data['FirstLoan'] = Member::CheckFirstLoan($id_member,$id_loan_service);
            // return $data['FirstLoan'];
            //LOAN VALIDATION
            $repayment_condition = Member::CheckRepaymentCondition($id_loan_service,$id_member);
            if(!$repayment_condition['isValid']){
                $data['INVALID_APPLICATION'] = true;
                array_push($data['ERROR_MESSAGE'],$repayment_condition["ERROR_MESSAGE"]);
            }
            $outsanding_overdue = Member::CheckOutstandingOverdue($id_member);
            if(!$outsanding_overdue['isValid']){
                $data['INVALID_APPLICATION'] = true;
                array_push($data['ERROR_MESSAGE'],$outsanding_overdue["ERROR_MESSAGE"]);
            }

            // return $id_member;
            $age_condition = Member::CheckAgeCondition($id_loan_service,$id_member);
            if(!$age_condition['isValid']){
                $data['INVALID_APPLICATION'] = true;
                array_push($data['ERROR_MESSAGE'],$age_condition["ERROR_MESSAGE"]);
            }
            //END LOAN VALIDATION

            ///IF APPLICATION IS VALID

            //Loan service details
            $data['loan_service'] = DB::table('loan_service')
            ->select(DB::raw("id_loan_service,if(min_amount=max_amount,concat('₱',FORMAT(min_amount,2)),concat('₱',FORMAT(min_amount,2),' - ₱',FORMAT(max_amount,2))) as amount_range,format(default_amount,2) as amount,id_loan_payment_type,min_amount,max_amount,loan_service.name,FORMAT(cbu_amount,2) as cbu_amount,im.description as interest_method,if(loan_service.is_deduct_cbu = 1,'(Deduct the deficient amount on loan)','') as cbu_deducted,cbu_amount as o_cbu_amount,no_comakers,id_terms_condition,id_one_time_type,with_maker_cbu,maker_min_cbu"))
            ->leftJoin('interest_method as im','im.id_interest_method','loan_service.id_interest_method')
            ->where('id_loan_service',$id_loan_service)
            ->first();


            $data['requirements'] = DB::table("loan_service_requirements")
            ->select("req_description")
            ->where('id_loan_service',$id_loan_service)
            ->orDerby('id_loan_service_requirements')
            ->get();

            //return $data['requirements'] ;
            //terms for loan service details

            //CBU CHECK
            $data['CBU'] = Member::CheckCBU($data['loan_service']->o_cbu_amount,$id_member);
            $data['ACCOUNT_CBU'] = Member::getCBU($id_member);
     

            if($data['loan_service']->id_terms_condition > 0){
                $tc = Loan::ParseTermConditionDetails($data['loan_service']->id_terms_condition,$data['ACCOUNT_CBU']);

                if(!isset($tc)){
                    $data['INVALID_APPLICATION_BLOCK'] = true;
                    $data['WITH_LOAN_SERVICE'] = false;

                    array_push($data['ERROR_MESSAGE_BLOCK'],"Selected Member can't avail the loan service due to minimum CBU requirement not met  [Current CBU: ".number_format($data['ACCOUNT_CBU'],2)."]");

                    return view('loan.loan_form',$data); 
                }

                $data['terms'] = DB::table('terms as t')
                ->select(DB::raw("terms_token,if(id_loan_payment_type=1,concat(t.terms,' ',p.description,' - ',interest_rate,'%'),'') as terms_sel"))
                ->LeftJoin('loan_service as ls','ls.id_loan_service','t.id_loan_service')
                ->LeftJoin('period as p','p.id_period','ls.id_term_period')
                ->where('t.id_loan_service',$id_loan_service)
                ->where('terms','<=',$tc->up_to_terms)
                ->orderBy('terms','ASC')
                ->get();  

                $data['loanable_range'] = $tc->amount_range;
                $data['default_amount'] = number_format($tc->min_principal,2);

                $data['min_amt'] = $tc->min_principal;
                $data['max_amt'] = $tc->max_principal;

            }else{
                $data['terms'] = DB::table('terms as t')
                ->select(DB::raw("terms_token,if(id_loan_payment_type=1,concat(t.terms,' ',p.description,' - ',interest_rate,'%'),'') as terms_sel"))
                ->LeftJoin('loan_service as ls','ls.id_loan_service','t.id_loan_service')
                ->LeftJoin('period as p','p.id_period','ls.id_term_period')
                ->where('t.id_loan_service',$id_loan_service)
                ->orderBy('terms','ASC')
                ->get();                

                $data['loanable_range'] = $data['loan_service']->amount_range;
                $data['default_amount'] = $data['loan_service']->amount;

                $data['min_amt'] = $data['loan_service']->min_amount;
                $data['max_amt'] = $data['loan_service']->max_amount;

                

                // dd($data);

            }
            $data['LoanApplicationYear'] = $data['SelectedDueYear'] = date("Y", strtotime(MySession::current_date()));


            if(!$data['CBU']['is_capital_buildup_valid']){
                $data['INVALID_APPLICATION'] = true;
                array_push($data['ERROR_MESSAGE'],"CBU Required Amount: ".$data['loan_service']->cbu_amount." || Current CBU: ".number_format($data['ACCOUNT_CBU'],2)." || CBU deficient: ".number_format($data['CBU']['difference'],2).".");
            }

            // dd($data);
            return view('loan.loan_form',$data);
        }else{
            return view('loan.loan_form',$data);
        }
    }
    public function view_loan_application($loan_token,Request $request){
        // $data['isAdmin'] = MySession::isAdmin();
        // return 123;
        // $data['isAdmin'] = MySession::isSuperAdmin();
        // return 123;
        //CHECK IF MEMBER OWNS THE LOAN TO VIEW IF NOT ADMIN

        
        $data['isAdmin'] = MySession::isAdmin();

        if(!$data['isAdmin']){
            $check_user_loan = DB::table('loan')
            ->where('loan_token',$loan_token)
            ->where(function($query){
                $query->where('created_by',MySession::mySystemUserId())
                ->orwhere('id_member',MySession::myId());
            })->count();
                                 // ->where('created_by',MySession::mySystemUserId())->count();
            if($check_user_loan == 0){
                return redirect('/redirect/error')->with('message', "privilege_access_invalid");
                return array("Message"=>"Invalid Loan Viewing");
            }
        }
        $validate_loan = DB::table('loan')->select('status','id_loan')->where('loan_token',$loan_token)->first();
        $loan_status = $validate_loan->status;

        // dd(123);
        $id_loan = $validate_loan->id_loan;
        // dd(123);
        if($loan_status == 0){

            $data = $this->view_application_submitted($loan_token,$request);
            $data['LoanApplicationYear'] = date("Y", strtotime(MySession::current_date()));
            $data['SelectedDueYear'] = $data['loan_service']->year_due;

            // dd($data);
            $data['head_title'] = "Loan Application #".$data['loan_service']->id_loan." (Submitted)";


            $data['loan_token'] = $loan_token;
        // return 123;
            $view = 'loan.loan_form';
            $data['loan_status'] = [4=>"Cancelled"];
            $data['INVALID_APPLICATION_BLOCK'] = false;
            $data['net_pays'] = DB::table('loan_net_pay')
            ->select(DB::raw("period_start,period_end,FORMAT(amount,2) as amount"))
            ->where('id_loan',$id_loan)
            ->where('amount','>',0)
            ->orderBy('id_loan_net_pay')
            ->get();


            $data['comakers'] = DB::table('loan_comakers as lc')
            ->select(DB::raw("concat(membership_id,' || ',FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)) as tag_value,lc.id_member as tag_id"))
            ->leftJoin('member as m','m.id_member','lc.id_member')
            ->where('id_loan',$id_loan)
            ->orDerby("id_loan_co_makers")
            ->get();



            $data['maker_cbu'] = array();



            if($data['loan_service']->with_maker_cbu == 1){
                foreach($data['comakers'] as $count=>$m){  
                    $mCBU = Member::getCBU($m->tag_id);
                    array_push($data['maker_cbu'],$mCBU);
                    // $data['maker_cbu'][$count] = $mCBU;

                }                
            }




            $data['other_lendings'] = DB::table('other_lenders')
            ->select(DB::raw("name,date_started,date_ended,FORMAT(amount,2) as amount"))
            ->where('id_loan',$id_loan)
            ->orDerby('id_other_lenders')
            ->get();

            $loan_offset = DB::table('loan_offset as lo')
            ->leftJoin('loan','loan.id_loan','lo.id_loan_to_pay')
            ->where('lo.id_loan',$id_loan)
            ->get();

            $loan_paid = array();

            $data['active_loan_paid_holder'] = array();

            foreach($loan_offset as $lo){
                $temp = array();
                $temp['loan_token'] = $lo->loan_token;
                $temp['amount'] = $lo->amount;
                $data['active_loan_paid_holder'][$lo->loan_token] = $lo->amount;

                array_push($loan_paid,$temp);
            }
            $data['loan_paid'] = $loan_paid;

            $other_deductions = DB::table('loan_manual_deduction')
                                ->select('id_loan_fees','amount','remarks')
                                ->where('id_loan',$id_loan)
                                ->get();
            $other_deductions = json_decode(json_encode($other_deductions),true);

            $data['other_deductions'] = $other_deductions;

        // $data['existing_loan'] =Loan::parseExistingLoanBalance($data['loan_service']->id_loan_service,$data['loan_service']->terms_token,$data['id_member'],MySession::current_date(),$loan_paid,1);

        // return $data['existing_loan'];

        }else{
            $data = Loan::LoanDetails($id_loan);



           
            $data['service_details'] = $data['service_details'];
            // $data['head_title'] = "Loan Application #".$data['loan_service']->id_loan." (Submitted)";
            $data['loan'] = $data;
            $data['for_approval'] = false;

            if($data['service_details']->lstatus > 0){
                $data['show_repayment'] = true;
            }

            $data['INVALID_APPLICATION'] = false;
            $data['ERROR_MESSAGE'] = array();
            $repayment_condition = Member::CheckRepaymentCondition($data['service_details']->id_loan_service,$data['service_details']->id_member);
            if(!$repayment_condition['isValid']){
                $data['INVALID_APPLICATION'] = true;
                array_push($data['ERROR_MESSAGE'],$repayment_condition["ERROR_MESSAGE"]);
            }
            $outsanding_overdue = Member::CheckOutstandingOverdue($data['service_details']->id_member);
            if(!$outsanding_overdue['isValid']){
                $data['INVALID_APPLICATION'] = true;
                array_push($data['ERROR_MESSAGE'],$outsanding_overdue["ERROR_MESSAGE"]);
            }
            $age_condition = Member::CheckAgeCondition($data['service_details']->id_loan_service,$data['service_details']->id_member);
            if(!$age_condition['isValid']){
                $data['INVALID_APPLICATION'] = true;
                array_push($data['ERROR_MESSAGE'],$age_condition["ERROR_MESSAGE"]);
            }

            //CBU CHECK
            $data['CBU'] = Member::CheckCBU($data['service_details']->cbu_amount,$data['service_details']->id_member);
            $data['ACCOUNT_CBU'] = Member::getCBU($data['service_details']->id_member);
            if(!$data['CBU']['is_capital_buildup_valid']){
                $data['INVALID_APPLICATION'] = true;
                array_push($data['ERROR_MESSAGE'],"CBU Required Amount: ".number_format($data['service_details']->cbu_amount,2)." || Current CBU: ".number_format($data['ACCOUNT_CBU'],2)." || CBU deficient: ".number_format($data['CBU']['difference'],2).".");
            }

            $data['net_pays'] = DB::table('loan_net_pay')
            ->select(DB::raw("DATE_FORMAT(period_start,'%m/%d/%Y') as period_start,DATE_FORMAT(period_end,'%m/%d/%Y') as period_end,FORMAT(amount,2) as amount"))
            ->where('id_loan',$id_loan)
            ->where('amount','>',0)
            ->orderBy('id_loan_net_pay')
            ->get();


            $data['comakers'] = DB::table('loan_comakers as lc')
            ->select(DB::raw("FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as name,m.id_member"))
            ->leftJoin('member as m','m.id_member','lc.id_member')
            ->where('id_loan',$id_loan)
            ->orDerby("id_loan_co_makers")
            ->get();

            $data['other_lendings'] = DB::table('other_lenders')
            ->select(DB::raw("name,DATE_FORMAT(date_started,'%m/%d/%Y') as date_started,DATE_FORMAT(date_ended,'%m/%d/%Y') as date_ended,FORMAT(amount,2) as amount"))
            ->where('id_loan',$id_loan)
            ->orDerby('id_other_lenders')
            ->get();
            $view = "loan.loan_form_display";
        }
        $data['isAdmin'] = MySession::isAdmin();
        // dd($data);


        return view($view,$data);;
    }
    public function view_application_submitted($loan_token,$request){
        $data['opcode'] = 1;
        $data['WITH_LOAN_SERVICE'] = true;
        $data['INVALID_APPLICATION'] = false;        
        $data['ERROR_MESSAGE'] = array();

        $data['CHANGE_SERVICE'] = true;
        $data['loan_service_lists'] = DB::table('loan_service')
        ->select("id_loan_service","name")
        ->where('status',1)
        ->orDerby('name')
        ->get();

        $data['isAdmin'] = MySession::isAdmin();

        $id_loan_service_req = $request->loan_reference ?? 0;
        $id_member_req = 0;
        if($data['isAdmin'] && isset($request->id_member)){
            $id_member_exist = DB::table('member')->where('id_member',$request->id_member)->count();
            if($id_member_exist > 0){
                $id_member_req = $request->id_member;
            }else{
                $id_member_req = 0;
            }
        }else{
            $id_member_req = 0;
        }


        if($id_loan_service_req > 0){
            $id_loan_service_check = DB::table('loan_service')->select("id_loan_service")->where('status',1)->where('id_loan_service',$id_loan_service_req)->first();
            if(!isset($id_loan_service_check)){
                $id_loan_service_req = 0;
            }
            //Check if previous loan service
            $check_loan_service_changed = DB::table('loan')->where('loan_token',$loan_token)->where('id_loan_service',$id_loan_service_req)->count();

            if($check_loan_service_changed == 0){
                $data['CHANGE_SERVICE'] = false;
            }
        }

        $data['loan_service'] = DB::table('loan as ap_loan')
        ->select(DB::raw("concat(m.membership_id,' || ',FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)) as member_name,m.id_member,m.id_member,ap_loan.id_loan,l_ser.id_loan_service,if(l_ser.min_amount=l_ser.max_amount,concat('₱',FORMAT(l_ser.min_amount,2)),concat('₱',FORMAT(l_ser.min_amount,2),' - ₱',FORMAT(l_ser.max_amount,2))) as amount_range,format(ap_loan.principal_amount,2) as amount,l_ser.id_loan_payment_type,l_ser.min_amount,l_ser.max_amount,l_ser.name,FORMAT(l_ser.cbu_amount,2) as cbu_amount,im.description as interest_method,if(l_ser.is_deduct_cbu = 1,'(Deduct the deficient amount on loan)','') as cbu_deducted,l_ser.cbu_amount as o_cbu_amount,l_ser.no_comakers,ap_loan.terms_token,ap_loan.principal_amount as o_amount,ap_loan.loan_remarks,l_ser.id_terms_condition,l_ser.with_maker_cbu,l_ser.maker_min_cbu,ap_loan.year_due"))
        ->leftJoin('loan_service as l_ser',function($query) use($id_loan_service_req){
            if($id_loan_service_req == 0){
                $query->on('l_ser.id_loan_service','ap_loan.id_loan_service');
            }else{
                $query->on('l_ser.id_loan_service','=',DB::raw("$id_loan_service_req"));
            }
        })
        ->leftJoin('member as m',function($query) use($id_member_req){
            if($id_member_req == 0){
                $query->on('m.id_member','ap_loan.id_member');
            }else{
                $query->on('m.id_member','=',DB::raw("$id_member_req"));
            }
        })        
        ->leftJoin('interest_method as im','im.id_interest_method','l_ser.id_interest_method')
        // ->leftJoin('member as m','m.id_member','ap_loan.id_member')
        ->where('ap_loan.loan_token',$loan_token)
        ->first();

        $id_member = $data['loan_service']->id_member;
        $id_loan_service = $data['loan_service']->id_loan_service;
        $term_reference = $data['loan_service']->terms_token;
        $principal_amount = $data['loan_service']->o_amount;
        $data['ACCOUNT_CBU'] = Member::getCBU($id_member);

        $data['default_amount'] = $data['loan_service']->amount;

        if($data['loan_service']->id_terms_condition == 0){
            $data['loanable_range'] = $data['loan_service']->amount_range;

            $data['terms'] = DB::table('terms as t')
            ->select(DB::raw("terms_token,if(id_loan_payment_type=1,concat(t.terms,' ',p.description,' - ',interest_rate,'%'),'') as terms_sel"))
            ->LeftJoin('loan_service as ls','ls.id_loan_service','t.id_loan_service')
            ->LeftJoin('period as p','p.id_period','ls.id_term_period')
            ->where('t.id_loan_service',$id_loan_service)
            ->orderBy('terms','ASC')
            ->get();

            $data['min_amt'] = $data['loan_service']->min_amount;
            $data['max_amt'] = $data['loan_service']->max_amount;
        }else{
            $tc = Loan::ParseTermConditionDetails($data['loan_service']->id_terms_condition,$data['ACCOUNT_CBU']);
      
            if(!isset($tc)){
                $data['PARAMETERS_VALID'] = false;
                $data['ERROR_TEXT'] = "Selected Member can't avail the loan service due to minimum CBU requirement not met  [Current CBU: ".number_format($data['ACCOUNT_CBU'],2)."]";

                $data['CODE'] = "ERROR";
                return $data;                  
            }
            $min_principal_validation = $tc->min_principal;
            $max_principal_validation = $tc->max_principal;  

            $data['loanable_range'] = $tc->amount_range; 
            $data['terms'] = DB::table('terms as t')
            ->select(DB::raw("terms_token,if(id_loan_payment_type=1,concat(t.terms,' ',p.description,' - ',interest_rate,'%'),'') as terms_sel"))
            ->LeftJoin('loan_service as ls','ls.id_loan_service','t.id_loan_service')
            ->LeftJoin('period as p','p.id_period','ls.id_term_period')
            ->where('t.id_loan_service',$id_loan_service)
            ->where('terms','<=',$tc->up_to_terms)
            ->orderBy('terms','ASC')
            ->get(); 
        }

        $data['selected_service'] = $data['loan_service'] ;
        $data['selected_member'] = $data['loan_service'];

        $data['min_amt'] = $tc->min_principal ?? $data['loan_service']->min_amount;
        $data['max_amt'] = $tc->max_principal ??$data['loan_service']->max_amount;
       
        $data['id_member'] = $id_member;
        // return $data['id_member'];
        //LOAN VALIDATION
        $repayment_condition = Member::CheckRepaymentCondition($id_loan_service,$id_member);
        if(!$repayment_condition['isValid']){
            $data['INVALID_APPLICATION'] = true;
            array_push($data['ERROR_MESSAGE'],$repayment_condition["ERROR_MESSAGE"]);
        }
        $outsanding_overdue = Member::CheckOutstandingOverdue($id_member);
        if(!$outsanding_overdue['isValid']){
            $data['INVALID_APPLICATION'] = true;
            array_push($data['ERROR_MESSAGE'],$outsanding_overdue["ERROR_MESSAGE"]);
        }
        $age_condition = Member::CheckAgeCondition($id_loan_service,$id_member);
        if(!$age_condition['isValid']){
            $data['INVALID_APPLICATION'] = true;
            array_push($data['ERROR_MESSAGE'],$age_condition["ERROR_MESSAGE"]);
        }
        //END LOAN VALIDATION

        //REQUIREMENTS
        if($data['CHANGE_SERVICE']){
            $data['requirements'] = DB::table("loan_service_requirements")
            ->select("req_description")
            ->where('id_loan_service',$id_loan_service)
            ->orDerby('id_loan_service_requirements')
            ->get();
        }



        //CBU CHECK
        $data['CBU'] = Member::CheckCBU($data['loan_service']->o_cbu_amount,$id_member);
        
        if(!$data['CBU']['is_capital_buildup_valid']){
            $data['INVALID_APPLICATION'] = true;
            array_push($data['ERROR_MESSAGE'],"CBU Required Amount: ".$data['loan_service']->cbu_amount." || Current CBU: ".number_format($data['ACCOUNT_CBU'],2)." || CBU deficient: ".number_format($data['CBU']['difference'],2).".");
        }




        return $data;        
    }

    public function get_loan_service_details(Request $request){
        if($request->ajax()){
            $id_loan_service = $request->id_loan_service;
            $data['loan_service'] = DB::table('loan_service')
            ->select(DB::raw("if(min_amount=max_amount,concat('₱',FORMAT(min_amount,2)),concat('₱',FORMAT(min_amount,2),' - ₱',FORMAT(max_amount,2))) as amount_range,format(default_amount,2) as default_amount,id_loan_payment_type"))
            ->where('id_loan_service',$id_loan_service)
            ->first();
            $data['terms'] = DB::table('terms as t')
            ->select(DB::raw("terms_token,if(id_loan_payment_type=1,concat(t.terms,' ',p.description,' - ',interest_rate,'%'),'') as terms_sel"))
            ->LeftJoin('loan_service as ls','ls.id_loan_service','t.id_loan_service')
            ->LeftJoin('period as p','p.id_period','ls.id_term_period')
            ->where('t.id_loan_service',$id_loan_service)
            ->orderBy('terms','ASC')
            ->get();
            return response($data);

            return response($id_loan_service);
        }
    }


    public function parseCharges($loan,$first_loan){
        $id_loan_service = $loan->id_loan_service;
        $principal_amount = $loan->principal_amount;
        $interest_rate = $loan->interest_rate;



        $type = [1]; //Default fee type to all

        if($first_loan){
            array_push($type,2); // add first loan on filter type
        }else{
            array_push($type,3); // add renewal loan on filter type
        }

        // $comp_charge_param = "if(c.id_fee_calculation=1,calculateChargeAmountPer(".$principal_amount.",".$interest_rate.",c.value,c.id_calculated_fee_base),c.value) as calculated_charge";
        // $charges = DB::table('charges_group as cg')
        // ->select(DB::raw("c.id_loan_fees,c.id_fee_calculation,c.value,concat(lf.name,if(c.id_fee_calculation=1,concat(' (',value,'%)'),'')) as charge_complete_details,lf.name as fee_name,c.id_calculated_fee_base,if(c.id_fee_calculation=1,'Percentage','Fixed') as fee_calculation,c.value,if(c.id_fee_calculation=1,concat(value,'% of ',cb.description),concat('₱',FORMAT(value,2))) as charge_description,if(c.is_deduct=1,'DEDUCTED','NOT_DEDUCTED') as is_deduct_text,c.is_deduct,c.application_fee_type,$comp_charge_param,if(c.non_deduct_option is null,'',if(c.non_deduct_option=1,'Fixed','Divided')) as non_deduct_option"))
        // ->leftJoin('charges as c','c.id_charges_group','cg.id_charges_group')
        // ->leftJoin('loan_service as lc','lc.id_charges_group','cg.id_charges_group')
        // ->leftJoin('loan_fees as lf','lf.id_loan_fees','c.id_loan_fees')
        // ->LeftJoin('calculated_fee_base as cb','cb.id_calculated_fee_base','c.id_calculated_fee_base')
        // ->where('id_loan_service',$id_loan_service)
        // ->whereIn('application_fee_type',$type)
        // ->get();

        $comp_charge_param = "if(c.id_fee_calculation=1,calculateChargeAmountPer(".$principal_amount.",".$interest_rate.",@val,c.id_calculated_fee_base),@val) as calculated_charge";
        $charge = DB::table('charges_group as cg')
        ->select(DB::raw("c.id_charges,c.id_loan_fees,c.id_fee_calculation,@val:= if(c.with_range=0,c.value,ChargeRange(c.id_charges,$principal_amount,c.value)) as value ,concat(lf.name,if(c.id_fee_calculation=1,concat(' (',@val,'%)'),'')) as charge_complete_details,lf.name as fee_name,c.id_calculated_fee_base,if(c.id_fee_calculation=1,'Percentage','Fixed') as fee_calculation,if(c.id_fee_calculation=1,concat(@val,'% of ',cb.description),concat('₱',FORMAT(@val,2))) as charge_description,if(c.is_deduct=1,'DEDUCTED','NOT_DEDUCTED') as is_deduct_text,c.is_deduct,c.application_fee_type,$comp_charge_param,if(c.non_deduct_option is null,'',if(c.non_deduct_option=1,'Fixed','Divided')) as non_deduct_option"))
        ->leftJoin('charges as c','c.id_charges_group','cg.id_charges_group')
        ->leftJoin('loan_service as lc','lc.id_charges_group','cg.id_charges_group')
        ->leftJoin('loan_fees as lf','lf.id_loan_fees','c.id_loan_fees')
        ->LeftJoin('calculated_fee_base as cb','cb.id_calculated_fee_base','c.id_calculated_fee_base')
        ->where('id_loan_service',$id_loan_service)
        ->whereIn('application_fee_type',$type);


        $charges = DB::table(DB::raw('(' . $charge->toSql() . ') AS charge'))
                ->select('*')
                ->mergeBindings($charge)
                ->where('charge.value', '>', 0)
                ->get();

        $charges_output = array();
        // $charges_output['COMPLETE_CHARGES'] = json_decode(json_encode($charges),TRUE);
        // $charges_output['complete_details'] = $charges;
        $g = new GroupArrayController();

        //SEPARATE DEDUCTED TO LOAN CHARGES
        $separated_charge = $g->array_group_by($charges,['is_deduct_text']);

        $charges_output['DEDUCTED'] = $separated_charge['DEDUCTED'] ?? [];
        $charges_output['NOT_DEDUCTED'] = $separated_charge['NOT_DEDUCTED'] ?? [];

        $charges_output['DEDUCTED_TOTAL'] = $this->sum_charges($charges_output['DEDUCTED']);
        $charges_output['NOT_DEDUCTED_TOTAL'] = $this->sum_charges($charges_output['NOT_DEDUCTED']);

        $non_deduct = $g->array_group_by($charges_output['NOT_DEDUCTED'],['non_deduct_option']);

        $charges_output['NOT_DEDUCTED_FIXED_TOTAL'] = $this->sum_charges($non_deduct['Fixed'] ?? []);
        $charges_output['NOT_DEDUCTED_DIVIDED_TOTAL'] = $this->sum_charges($non_deduct['Divided'] ?? []);

        return $charges_output;
    }
    public function parseChargesLoan($id_loan){
        $l = DB::table('loan')->where('id_loan',$id_loan)->first();
        $first_loan = Member::CheckFirstLoan($l->id_member,$l->id_loan_service);
            $c_type = [1]; //Default fee type to all

            if($first_loan){
                array_push($c_type,2); // add first loan on filter type
            }else{
                array_push($c_type,3); // add renewal loan on filter type
            }
            $comp_charge_param = "if(c.id_fee_calculation=1,calculateChargeAmountPer(ap_loan.principal_amount,ap_loan.interest_rate,@val,c.id_calculated_fee_base),@val) as calculated_charge";

            // $charges = DB::table('charges_group as cg')
            // ->select(DB::raw("c.id_loan_fees,c.id_fee_calculation,c.value,concat(lf.name,if(c.id_fee_calculation=1,concat(' (',c.value,'%)'),'')) as charge_complete_details,lf.name as fee_name,c.id_calculated_fee_base,if(c.id_fee_calculation=1,'Percentage','Fixed') as fee_calculation,c.value,if(c.id_fee_calculation=1,concat(c.value,'% of ',cb.description),concat('₱',FORMAT(c.value,2))) as charge_description,if(c.is_deduct=1,'DEDUCTED','NOT_DEDUCTED') as is_deduct_text,c.is_deduct,c.application_fee_type,$comp_charge_param,if(c.non_deduct_option is null,'',if(c.non_deduct_option=1,'Fixed','Divided')) as non_deduct_option"))
            // ->leftJoin('charges as c','c.id_charges_group','cg.id_charges_group')
            // ->leftJoin('loan_fees as lf','lf.id_loan_fees','c.id_loan_fees')
            // ->LeftJoin('calculated_fee_base as cb','cb.id_calculated_fee_base','c.id_calculated_fee_base')
            // ->leftJoin('loan as ap_loan','ap_loan.id_charges_group','cg.id_charges_group')
            // ->where('ap_loan.id_loan',$id_loan)
            // ->whereIn('c.application_fee_type',$c_type)
            // ->get();



            $charge = DB::table('charges_group as cg')
            ->select(DB::raw("c.id_loan_fees,c.id_fee_calculation,@val:= if(c.with_range=0,c.value,ChargeRange(c.id_charges,ap_loan.principal_amount,c.value)) as value,concat(lf.name,if(c.id_fee_calculation=1,concat(' (',@val,'%)'),'')) as charge_complete_details,lf.name as fee_name,c.id_calculated_fee_base,if(c.id_fee_calculation=1,'Percentage','Fixed') as fee_calculation,if(c.id_fee_calculation=1,concat(@val,'% of ',cb.description),concat('₱',FORMAT(@val,2))) as charge_description,if(c.is_deduct=1,'DEDUCTED','NOT_DEDUCTED') as is_deduct_text,c.is_deduct,c.application_fee_type,$comp_charge_param,if(c.non_deduct_option is null,'',if(c.non_deduct_option=1,'Fixed','Divided')) as non_deduct_option"))
            ->leftJoin('charges as c','c.id_charges_group','cg.id_charges_group')
            ->leftJoin('loan_fees as lf','lf.id_loan_fees','c.id_loan_fees')
            ->LeftJoin('calculated_fee_base as cb','cb.id_calculated_fee_base','c.id_calculated_fee_base')
            ->leftJoin('loan as ap_loan','ap_loan.id_charges_group','cg.id_charges_group')
            ->where('ap_loan.id_loan',$id_loan)
            ->whereIn('c.application_fee_type',$c_type);
            // ->get();

            $charges = DB::table(DB::raw('(' . $charge->toSql() . ') AS charge'))
            ->select('*')
            ->mergeBindings($charge)
            ->where('charge.value', '>', 0)
            ->get();

            $charges_output = array();

            $g = new GroupArrayController();

        //SEPARATE DEDUCTED TO LOAN CHARGES
            $separated_charge = $g->array_group_by($charges,['is_deduct_text']);

            $charges_output['DEDUCTED'] = $separated_charge['DEDUCTED'] ?? [];
            $charges_output['NOT_DEDUCTED'] = $separated_charge['NOT_DEDUCTED'] ?? [];

            $charges_output['DEDUCTED_TOTAL'] = $this->sum_charges($charges_output['DEDUCTED']);
            $charges_output['NOT_DEDUCTED_TOTAL'] = $this->sum_charges($charges_output['NOT_DEDUCTED']);

            $non_deduct = $g->array_group_by($charges_output['NOT_DEDUCTED'],['non_deduct_option']);

            $charges_output['NOT_DEDUCTED_FIXED_TOTAL'] = $this->sum_charges($non_deduct['Fixed'] ?? []);
            $charges_output['NOT_DEDUCTED_DIVIDED_TOTAL'] = $this->sum_charges($non_deduct['Divided'] ?? []);

            return $charges_output;

        }

        public function sum_charges($charges){
        //This will sum up array of charges
            $total = 0;
            foreach($charges as $c){
                $total += $c->calculated_charge;
            }
            return $total;
        }

        public function check_firsts_loan(){
            return false;
        }
        public function check_capital_buildup($required_amount,$id_member){
            $total_capital_buildup = Member::getCBU($id_member);
            $output['is_capital_buildup_valid'] = true;
            $output['difference'] = 0;
            $difference = $required_amount - $total_capital_buildup;
            if($difference > 0){
                $output['is_capital_buildup_valid'] = false;
                $output['difference'] = $difference;
            }

            return $output;
        }
        public function getCBU($id_member){


        }
        public function post(Request $request){
            if($request->ajax()){
                $data['RESPONSE_CODE'] = "success";
                $data['isAdmin'] = MySession::isAdmin();
                // dd($request->all());


            // return $data;
                $opcode = $request->opcode;


                $has_other_loan = $request->has_other_loan;
                $id_loan_service = $request->id_loan_service;
                $terms_token = $request->terms_token;
                $principal_amount = $request->principal_amount;
                $active_loan_payment = $request->active_loan_payment;
                $manual_payment = $request->manual_payment ?? [];




                $id_member  = ($data['isAdmin'])?$request->id_member:MySession::myId();

                $validation_temp = $this->parseLoanFrameData($id_loan_service,$terms_token,$principal_amount,$id_member,$active_loan_payment,$manual_payment,$request->year_due??MySession::current_year());

                // dd($validation_temp);

                $loan_offset = $validation_temp['loan']['PREV_LOAN_OFFSET'];


                if($validation_temp['loan']['TOTAL_LOAN_PROCEED'] <= 0){
                    $data['RESPONSE_CODE'] = "INVALID_AMOUNT";
                    $data['message'] = "Total Loan Proceed must not be less than or equal to 0";

                    return response($data);
                }

                $net_pay = $request->net_pay;
                $other_lendings = $request->other_lendings;

                $comakers = $request->comakers ?? [];
                $id_loan = $request->id_loan;

                $loan_remarks = $request->loan_remarks ?? '';



                $validation = $this->validate_posted_data($id_loan_service,$request);
                
                if($validation['IS_INVALID_INPUT']){
                    $validation['RESPONSE_CODE'] = "INVALID_PARAMETERS";
                    return response($validation);   
                }


            // return response($net_pay);
                if($opcode == 0){
                //Insert Parent Loan
                    DB::select("INSERT INTO loan (id_member,loan_token,id_loan_service,id_disbursement_type,id_interest_method,id_loan_payment_type,id_term_period,
                        id_interest_period,id_repayment_period,start_month_period,end_month_period,repayment_schedule,id_charges_group,interest_rate,terms,period,principal_amount,terms_token,loan_protection_rate,loan_remarks,created_by,is_deduct_cbu,deduct_interest)
                    SELECT ?,concat(?,DATE_FORMAT(now(),'%m%d%Y%H%i%s'),concat(LEFT(MD5(NOW()), 5))) as loan_token,ls.id_loan_service,id_disbursement_type,id_interest_method,id_loan_payment_type,id_term_period,
                    id_interest_period,id_repayment_period,start_month_period,end_month_period,repayment_schedule,id_charges_group,interest_rate,terms,period,? as principal_amount,terms_token,loan_protection_rate,?,?,ls.is_deduct_cbu,ls.deduct_interest
                    FROM loan_service as ls
                    JOIN terms on terms.id_loan_service = ls.id_loan_service
                    WHERE ls.id_loan_service = ? and terms_token = ?;",[$id_member,$id_member,$principal_amount,$loan_remarks,MySession::mySystemUserId(),$id_loan_service,$terms_token]);

                    $id_loan = DB::table('loan')->max('id_loan');
                }else{
                    $binded = array(
                        'loan_remarks'=>$loan_remarks
                    );

                    DB::select("UPDATE loan as ap_loan
                        JOIN loan_service as ls on ls.id_loan_service = $id_loan_service
                        JOIN terms on terms.id_loan_service = ls.id_loan_service
                        SET ap_loan.id_member=$id_member, ap_loan.id_loan_service = ls.id_loan_service, ap_loan.id_disbursement_type = ls.id_disbursement_type, 
                        ap_loan.id_interest_method = ls.id_interest_method, ap_loan.id_loan_payment_type = ls.id_loan_payment_type, 
                        ap_loan.id_term_period = ls.id_term_period, ap_loan.id_interest_period = ls.id_interest_period, 
                        ap_loan.id_repayment_period = ls.id_repayment_period, ap_loan.start_month_period = ls.start_month_period, 
                        ap_loan.end_month_period = ls.end_month_period, ap_loan.repayment_schedule = ls.repayment_schedule, 
                        ap_loan.id_charges_group = ls.id_charges_group, ap_loan.interest_rate = terms.interest_rate, 
                        ap_loan.terms = terms.terms, ap_loan.period = terms.period, 
                        ap_loan.principal_amount = $principal_amount, ap_loan.terms_token = terms.terms_token,ap_loan.loan_protection_rate = terms.loan_protection_rate,ap_loan.is_deduct_cbu = ls.is_deduct_cbu,ap_loan.loan_remarks = :loan_remarks,ap_loan.deduct_interest = ls.deduct_interest
                        WHERE id_loan = $id_loan and terms.terms_token ='$terms_token';",$binded);

                    DB::select("DELETE loan_net_pay,other_lenders,loan_comakers,loan_charges,loan_offset,loan_manual_deduction FROM loan
                        LEFT JOIN loan_net_pay on loan.id_loan = loan_net_pay.id_loan
                        LEFT JOIN other_lenders on loan.id_loan = other_lenders.id_loan
                        LEFT JOIN loan_comakers on loan.id_loan = loan_comakers.id_loan
                        LEFT JOIN loan_charges on loan.id_loan = loan_charges.id_loan
                        LEFT JOIN loan_offset on loan_offset.id_loan = loan.id_loan
                        LEFT JOIN loan_manual_deduction on loan_manual_deduction.id_loan = loan.id_loan

                        WHERE loan.id_loan = $id_loan");
                }


                //set loan due year
                DB::table('loan')
                ->where('id_loan',$id_loan)
                ->update(['year_due'=>$request->year_due]);

                for($i=0;$i<count($net_pay);$i++){
                    $net_pay[$i]['id_loan'] = $id_loan;
                }


                DB::table('loan_net_pay')
                ->insert($net_pay);

                $other_d = array();
                for($i=0;$i<count($manual_payment);$i++){
                    $other_d[]=[
                        'id_loan'=>$id_loan,
                        'id_loan_fees'=>$manual_payment[$i]['id_loan_fees'],
                        'amount'=>$manual_payment[$i]['amount'],
                        'remarks'=>$manual_payment[$i]['remarks'],
                    ];
                }

                DB::table('loan_manual_deduction')->insert($other_d);


                if($has_other_loan){

                    for($i=0;$i<count($other_lendings);$i++){
                        $other_lendings[$i]['id_loan'] = $id_loan;
                    }
                    DB::table('other_lenders')
                    ->insert($other_lendings);

                }

                for($i=0;$i<count($comakers ?? []);$i++){
                    $comakers[$i]['id_loan'] = $id_loan;
                }

                DB::table('loan_comakers')
                ->insert($comakers);


            //GET TOKEN AND NEW INTEREST RATE
                $new_loan = DB::table('loan')
                ->Select("interest_rate","loan_token","id_charges_group","id_loan_service")
                ->where('id_loan',$id_loan)
                ->first();

                $interest = $new_loan->interest_rate;
                $id_charges_group = $new_loan->id_charges_group;


            //Push Charges
                $first_loan = Member::CheckFirstLoan($id_member,$id_loan_service);
            $c_type = [1]; //Default fee type to all

            if($first_loan){
                array_push($c_type,2); // add first loan on filter type
            }else{
                array_push($c_type,3); // add renewal loan on filter type
            }
            // DB::select("INSERT INTO loan_charges (id_loan,id_loan_fees,id_fee_calculation,value,id_calculated_fee_base,is_deduct,application_fee_type,calculated_charge)
            //     SELECT $id_loan,id_loan_fees,id_fee_calculation,value,id_calculated_fee_base,is_deduct,application_fee_type,if(c.id_fee_calculation=1,calculateChargeAmountPer($principal_amount,$interest,c.value,c.id_calculated_fee_base),c.value) as calculated_charge
            //     FROM charges as c
            //     WHERE id_charges_group =$id_charges_group and application_fee_type in (".implode(",",$c_type).")");

            

            DB::select("INSERT INTO loan_charges (id_loan,id_loan_fees,id_fee_calculation,value,id_calculated_fee_base,is_deduct,application_fee_type,calculated_charge)
                SELECT * FROM (
                SELECT $id_loan,id_loan_fees,id_fee_calculation,@val:= if(c.with_range=0,c.value,ChargeRange(c.id_charges,$principal_amount,c.value)) as value,id_calculated_fee_base,is_deduct,application_fee_type,if(c.id_fee_calculation=1,calculateChargeAmountPer($principal_amount,$interest,@val,c.id_calculated_fee_base),@val) as calculated_charge
                FROM charges as c
                WHERE id_charges_group =$id_charges_group and application_fee_type in (".implode(",",$c_type).")) as charges
                WHERE charges.value > 0");

            //Insert Loan Approvers
            $this->insert_loan_approver($id_loan,$id_loan_service);

            if(count($loan_offset) > 0){
                $l_off = array();
                foreach($loan_offset as $l){
                    $temp = array();
                    $temp['id_loan'] = $id_loan;
                    $temp['id_loan_to_pay'] = $l->id_loan;
                    $temp['amount'] = $l->payment;
                    $temp['rebates'] = $l->rebates;

                    array_push($l_off,$temp);
                }

                DB::table('loan_offset')
                ->insert($l_off);
                    // return $l_off;
            }
            $data['LOAN_TOKEN'] = $new_loan->loan_token;
            $data['LOAN_ID'] = $id_loan;


            //UPDATE LOAN AMOUNTS

            if($opcode == 0){
                $loan_details = DB::table('loan')->select('loan.status','loan.id_loan','loan.id_loan_service','loan.id_loan_payment_type','loan.terms','loan.id_member','loan.terms_token','m.email')
                ->leftJoin('member as m','m.id_member','loan.id_member')
                ->where('loan.id_loan',$id_loan)
                ->first();

                $lc = new LoanApprovalController();
                $lc->EmailPusher($loan_details,0);
            }
            // DB::select("UPDATE loan
            // LEFT JOIN member on member.id_member = loan.id_member
            // SET loan.id_membership_type=member.memb_type , loan.id_baranggay_lgu = member.id_baranggay_lgu
            // WHERE id_loan = ?",[$id_loan]);
            DB::select("UPDATE loan
                        LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
                        LEFT JOIN member on member.id_member = loan.id_member AND ls.id_membership_type = member.memb_type
                        SET loan.id_membership_type=ls.id_membership_type , loan.id_baranggay_lgu = if(ls.id_membership_type >= 2,member.id_baranggay_lgu,null)
                        WHERE id_loan = ?",[$id_loan]);


            return response($data);

            // return $net_pay;

            return "success";
        }
    }
    public function insert_loan_approver($id_loan,$id_loan_service){
        DB::table('loan_approvers')->where('id_loan',$id_loan)->delete();
        // DB::select("INSERT INTO loan_approvers (id_loan,id_cms_privileges)
        //     SELECT $id_loan,id_cms_privileges FROM loan_service_approvers
        //     WHERE id_loan_service = $id_loan_service;");
    }

    public function validate_posted_data($id_loan_service,$request){
        $loan_service = DB::table('loan_service')->select("no_comakers")->where('id_loan_service',$id_loan_service)->first();

        $no_comaker = $loan_service->no_comakers;
        $error_messages = [];
        $is_invalid_input = false;

        // NET PAY VALIDATION
        $net_pay_required_fields = ["period_start","period_end"];
        $net_pay_required_amounts = ["amount"];
        $net_pay = $request->net_pay;

        $net_pay_invalid = array();

            // for($i=0;$i<count($net_pay);$i++){
            //     $temp = array();
            //     foreach($net_pay[$i] as $key=>$val){
            //         if(in_array($key,$net_pay_required_fields)){
            //             if($val == ""){
            //                 array_push($temp,$key);
            //             }
            //         }
            //         if(in_array($key,$net_pay_required_amounts)){
            //             if($val <= 0){
            //                 array_push($temp,$key);
            //             }
            //         }
            //     }
            //     if(count($temp) > 0){
            //         $net_pay_invalid[$i] = $temp;
            //         $is_invalid_input = true;
            //     }
            // }
        if(count($net_pay_invalid) > 0){
            array_push($error_messages,"Missing Mandatory Fields on Net Pay");
        }


        // NET PAY VALIDATION
        $has_other_loan = $request->has_other_loan;
        $other_lenders_fields = ["name","date_started","date_ended"];
        $other_lenders_amounts = ["amount"];
        $other_lendings = $request->other_lendings;
        $other_lenders_invalid = array();

        if($has_other_loan == 1){

            if(isset($other_lendings)){

                // return "SET";
                for($i=0;$i<count($other_lendings);$i++){
                    $temp = array();
                    foreach($other_lendings[$i] as $key=>$val){
                        if(in_array($key,$other_lenders_fields)){
                            if($val == ""){
                                array_push($temp,$key);
                            }
                        }
                        if(in_array($key,$other_lenders_amounts)){
                            if($val <= 0){
                                array_push($temp,$key);
                            }
                        }
                    }
                    if(count($temp) > 0){
                        $other_lenders_invalid[$i] = $temp;
                        $is_invalid_input = true;
                    }
                }

                if(count($other_lenders_invalid) > 0){
                    array_push($error_messages,"Missing Mandatory Fields on Other Lending");
                }
            }else{
                // return "NA";
                array_push($error_messages,"Please enter atleast 1 other lendings");
            }
        }

        // return $other_lenders_invalid;

        //Check Comakers
        $comakers_in = $request->comakers;

        if(!isset($comakers_in)){ // if comakers is equal to 0 (no comaker selected)
            if($no_comaker > 0){
                array_push($error_messages,"NO COMAKER(S) SELECTED");
                $is_invalid_input = true;                
            }

        }else{
            $temp_comakers = array();
            foreach($comakers_in as $count=>$c){
                if(isset($c['id_member'])){
                    array_push($temp_comakers,$c['id_member']);
                }
            }
            $count_comakers = count(array_unique($temp_comakers));          
            if($count_comakers != $loan_service->no_comakers){
                $is_invalid_input = true;
                array_push($error_messages,"INVALID COMAKER(S)");
            }
        }



        $response['ERROR_MESSAGES'] = $error_messages;
        $response['IS_INVALID_INPUT'] = $is_invalid_input;
        $response['NET_PAY_INVALID'] = $net_pay_invalid;
        $response['OTHER_LENDER_INVALID'] = $other_lenders_invalid;

        return $response;
    }

    public function search_comaker(Request $request){
        if($request->ajax()){
            $search = $request->term;  
            $admin = MySession::isAdmin(); 
            $cm = $request->cm;

            // $avail_comak 
            if(strlen($search) < 3){
                $data['accounts'] = array();
                return response($data);
            }
            $data['accounts'] = DB::table('member as m')
            ->select(DB::raw("concat(membership_id,' || ',FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)) as tag_value,id_member as tag_id"))
            ->where(function ($query) use ($search){
                $query->where(DB::raw("concat(first_name,' ',last_name)"), 'like', "%$search%");
            })
            ->where(function($query) use ($admin,$cm){
                if(!$admin){
                    $query->where('id_member','<>',MySession::myId());
                }else{
                    $query->where('id_member','<>',$cm);
                }
            })
            ->where('m.status',1)
            ->get();
            return response($data);
        }
    }
    public function cancel_loan_application(Request $request){
        if($request->ajax()){
            $id_loan = $request->id_loan;
            $cancellation_reason = $request->cancellation_reason." [Admin]";

            $response['RESPONSE_CODE'] = "SUCCESS";
            $d['e'] = $loan_details = DB::table('loan')->select('id_loan','loan_token','status','id_cash_disbursement','previous_loan_id')->where('id_loan',$id_loan)->first();

            if($loan_details->status == 3){
                $payment_count = DB::table('repayment_loans')
                                 ->where('id_loan',$id_loan)
                                 ->where('status','<>',10)
                                 ->count();
                if($payment_count > 0){
                    $data['RESPONSE_CODE'] = "ERROR";
                    $data['message'] = "Invalid Request. Loan has already repayment";
                    return response($data);
                }

                // cancel loan
                DB::table('loan')
                ->where('id_loan',$id_loan)
                ->update([
                    'status'=>4,
                    'loan_status'=>0,
                    'cancellation_reason'=>$cancellation_reason
                ]);

                //cancel CDV
                DB::table('cash_disbursement')     
                ->where('id_cash_disbursement',$loan_details->id_cash_disbursement)
                ->update([
                    'status'=>10,
                    'cancellation_reason'=>$cancellation_reason,
                    'date_cancelled'=>DB::raw("now()"),
                    'description'=>DB::raw("concat(description,' [CANCELLED]')")
                ]);           

                // //set previous loan as active (if exists)
                // DB::table('loan')
                // ->where('id_loan',$loan_details->previous_loan_id)
                // ->update([
                //     'status'=>3,
                //     'loan_status'=>1
                // ]);

                //return the paid previous and offset loan to active
                DB::select("UPDATE  (
                SELECT distinct id_loan FROM repayment_transaction as rt
                LEFT JOIN repayment_loans as rl on rl.id_repayment_transaction = rt.id_repayment_transaction
                where pay_on_id_loan = ?) as rl
                LEFT JOIN loan on loan.id_loan = rl.id_loan
                SET loan.status=3,loan.loan_status =1;",[$id_loan]);

                //cancel repayment_Transaction of offsetted
                DB::table('repayment_transaction')
                ->where('pay_on_id_loan',$id_loan)
                ->update([
                    'status'=>10,
                    'cancel_reason'=>$cancellation_reason,
                    'date_cancelled'=>DB::raw("now()")
                ]);

                //update loan table is paid identifier
                DB::select("
                    UPDATE repayment_transaction as rt
                    LEFT JOIN repayment_loans as rl on rl.id_repayment_transaction = rt.id_repayment_transaction
                    LEFT JOIN loan_table as lt on lt.id_loan = rl.id_loan and rl.term_code = lt.term_code
                    SET lt.is_paid = CASE
                    WHEN ifnull(getLoanTotalTermPayment(rl.id_loan,rl.term_code),0) = 0 THEN 0
                    WHEN (total_due-ifnull(getLoanTotalTermPayment(rl.id_loan,rl.term_code),0)) =0 THEN 1
                    ELSE 2 END
                    where pay_on_id_loan = ?;
                ",[$id_loan]);
                $response['LOAN_TOKEN'] = $loan_details->loan_token;


                return response($response);
            }else{
                $response['LOAN_TOKEN'] = $loan_details->loan_token;
                //UPDATE OTHER LOAN DETAILS
                // DB::table('loan')
                // ->where('id_loan',$id_loan)
                // ->where('status',0)
                // ->update([
                //     'status'=>4,
                //     'cancellation_reason'=>$cancellation_reason
                // ]);

                $LoanApproval = new LoanApprovalController();
                $LoanApproval->postLoanApproved($loan_details->loan_token,4,$cancellation_reason);                
            }

            //END
            return $response;
        }
    }


    public function parseActiveLoans(Request $request){
        // if($request->ajax()){

            return Loan::parseExistingLoanBalance($request->id_loan_service,$request->terms_token,$request->id_member,MySession::current_date(),[],1);
        // }
    }
}


// LOAN SERVICE DISPLAY
//Loan Name
// Capital buildup
// Charges and Fees
//     -   fee name
//     -   fee description (amount or percentage of ......)
//     -   is deducted to loan
// Interest Method
// Requirements  




// id_loan
// count
// term_code
// repayment_amount 
// interest_amount 
// fees 
// total_due


// is_deduct_cbu