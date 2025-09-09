<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\MySession;
use App\WebHelper;
use App\CredentialModel;
use PDF;
use App\Loan;

class RepaymentStatementController extends Controller
{
    public function index(Request $request){
        $data['credential']= CredentialModel::GetCredential(MySession::myPrivilegeId());
        if(!$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }   

        $data['statements'] = DB::table('repayment_statement as rs')
                              ->select(DB::raw("rs.id_repayment_statement,rs.id_baranggay_lgu,rs.id_repayment_statement,date,due_date,bl.name as baranggay_lgu,if(bl.type=1,'Barangay','LGU') as group_,DATE_FORMAT(due_date,'%m%Y') as month_year,DATE_FORMAT(rs.date,'%m/%d/%Y') as statement_date,DATE_FORMAT(due_date,'%M %Y') as month_due,DATE_FORMAT(rs.date_created,'%m/%d/%Y %h:%i %p') as date_created,SUM(rsd.loan_due) as total_due,SUM(rsd.amount_paid) as amount_paid,rs.status,if(rs.status=1,'Received',if(rs.status=2,'Deposited',if(rs.status=10,'Cancelled',''))) as status_description,if(rs.status=1,'primary',if(rs.status=2,'success',if(rs.status=10,'danger',''))) as status_class"))
                              ->leftJoin('baranggay_lgu as bl','bl.id_baranggay_lgu','rs.id_baranggay_lgu')
                              ->leftJoin('repayment_statement_details as rsd','rsd.id_repayment_statement','rs.id_repayment_statement')
                              ->groupBy('rs.id_repayment_statement')
                              ->orderBy('rs.id_repayment_statement','DESC')
                              ->get();

        return view('repayment-statement.index',$data);
    }
    public function create(Request $request){
        
       $data['branches']  = DB::table('baranggay_lgu')
        ->select(DB::raw("id_baranggay_lgu,name,if(type=1,'Barangay','LGU') as type"))
        ->orderByRaw("type,name")
        ->get();

        $data['selected_branch'] = $request->br ?? $data['branches'][0]->id_baranggay_lgu;

        $data['opcode'] = 0;

        $g = new GroupArrayController();
        $data['branches'] = $g->array_group_by($data['branches'],['type']);

        $data['date'] = $request->date ?? MySession::current_date();
        $data['date_due'] = WebHelper::ConvertDatePeriod($data['date']);

        // dd($data);
        $cur_statement = DB::table('repayment_statement')
                     ->select(DB::raw("DATE_FORMAT(due_date,'%m%Y') as month_year,id_repayment_statement"))
                     ->where('id_baranggay_lgu',$data['selected_branch'])
                     ->where('due_date',$data['date_due'])
                     ->where('status','<>',10)
                     ->first();

        $data['with_statement'] = false;

        if(isset($cur_statement)){
            $data['loans'] =  [];
            $data['with_statement'] =true;
            $data['statement_details'] = $cur_statement;
            $data['branch_details'] = DB::table('baranggay_lgu')->where('id_baranggay_lgu',$data['selected_branch'])->first();

            // dd($data);
        }else{
            $param = ['branch'=>$data['selected_branch'],'date1'=>$data['date_due'],'date2'=>$data['date_due']];
            $g = new GroupArrayController();
            $loans = $this->ActiveLoans($param);
            $data['loans'] = $g->array_group_by($loans,['id_member']);
           

        }

        // date("Y-m-$in",strtotime("$year-$month-$day"));



        return view('repayment-statement.form',$data);

        // $loans = $this->ActiveLoans($data['selected_branch'],$data['date_due'],0);
        // // dd($loans);
        
        // $data['loans'] = $g->array_group_by($loans,['id_member']);  
    }

    public function edit($id_repayment_statement,Request $request){
        $details=$data['details'] = DB::table('repayment_statement')
                           ->where('id_repayment_statement',$id_repayment_statement)
                           ->first();

        if($details->status != 0){
            $url = "/repayment-statement/view/$id_repayment_statement?".http_build_query($request->all());
            return redirect()->to($url);            
        }

        $data['branches']  = DB::table('baranggay_lgu')
        ->select(DB::raw("id_baranggay_lgu,name,if(type=1,'Barangay','LGU') as type"))
        ->orderByRaw("type,name")
        ->get();

        $data['selected_branch'] = $details->id_baranggay_lgu;

        $data['opcode'] = 1;

        $g = new GroupArrayController();
        $data['branches'] = $g->array_group_by($data['branches'],['type']);

        $data['date'] = $details->date;
        $data['date_due'] = WebHelper::ConvertDatePeriod($data['date']);
        $data['with_statement'] = false;


        $param = ['branch'=>$data['selected_branch'],'date1'=>$data['date_due'],'date2'=>$data['date_due']];
        $g = new GroupArrayController();
        $loans = $this->ActiveLoans($param);
        $data['loans'] = $g->array_group_by($loans,['id_member']);

        $data['selected_member'] = DB::table('repayment_statement_details')
                                   ->select('id_member')
                                   ->where('id_repayment_statement',$id_repayment_statement)
                                   ->groupBy('id_member')
                                   ->get()->pluck('id_member')->toArray();

        return view('repayment-statement.form',$data);

    }

    public function view($id_repayment_statement,Request $request){
        // $this->PushRepaymentBulk($id_repayment_statement);
        // return;
        // $url = '/test_rr?'.http_build_query($request->all());
        // return redirect()->to($url);

        $data['details'] = DB::table('repayment_statement as rs')
                          ->select(DB::raw("rs.id_repayment_statement,rs.id_baranggay_lgu,rs.id_repayment_statement,date,due_date,bl.name as baranggay_lgu,if(bl.type=1,'Barangay','LGU') as group_,DATE_FORMAT(due_date,'%m%Y') as month_year,DATE_FORMAT(rs.date,'%m/%d/%Y') as statement_date,DATE_FORMAT(due_date,'%M %Y') as month_due,id_paymode,id_bank,id_check_type,check_date,check_no,remarks,total_amount,date_received,or_number,bl.treasurer,rs.status,rs.status_remarks,DATE_FORMAT(rs.status_date,'%m/%d/%Y %h:%i %p') as status_date,rs.check_bank,rs.date_deposited,rs.check_amount,if(rs.status=1,'Received',if(rs.status=2,'Deposited',if(rs.status=10,'Cancelled',''))) as status_description,if(rs.status=1,'primary',if(rs.status=2,'success',if(rs.status=10,'danger',''))) as status_class"))
                          ->leftJoin('baranggay_lgu as bl','bl.id_baranggay_lgu','rs.id_baranggay_lgu')
                          ->where('rs.id_repayment_statement',$id_repayment_statement)
                          ->first();


        $loans = $this->StatementLoans($id_repayment_statement);

        $g = new GroupArrayController();
        $data['loans'] = $g->array_group_by($loans,['id_member']);
        $data['opcode'] = 1;
        $data['selected_branch'] = $data['details']->id_baranggay_lgu;
        $data['date'] = $data['details']->date;
        $data['date_due'] = $data['details']->due_date;
        $data['banks'] = DB::table('tbl_bank')
                         ->select(DB::raw("id_bank,bank_name"))
                         ->get();

        $data['allow_post'] = true;     

        if($data['details']->status > 1){
            $data['allow_post'] = false;
        }           
        return view('repayment-statement.view',$data);
    }

    public function ActiveLoans($param){
        $loans = DB::select("SELECT sd.*,
                            FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member,
                            getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms) as loan_name,loan.loan_token FROM (
                            SELECT loan.id_loan,loan.id_member,getTotalAmortization(loan.id_loan,loan.maturity_date) as balance,
                            @cur_due:=getTotalAmortization(loan.id_loan,:date1) as current_due,ROUND(@cur_due,2) as payment,
                            getLoanRebates(loan.id_loan,:date2) as rebates,0 as penalty
                            FROM loan
                            LEFT JOIN member as m on m.id_member = loan.id_member
                            WHERE loan.loan_status = 1 AND m.id_baranggay_lgu = :branch) as sd
                            LEFT JOIN member as m on m.id_member  = sd.id_member
                            LEFT JOIN loan on loan.id_loan = sd.id_loan
                            LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
                            WHERE current_due > 0
                            ORDER BY member",$param);
        return $loans;
    }

    public function validation($due_date,$id_baranggay_lgu,$id_repayment_statement){
        //validation
        $data = DB::table('repayment_statement')
                 ->where('due_date',$due_date)
                 ->where('id_baranggay_lgu',$id_baranggay_lgu)
                 ->where('status','<>',10)
                 ->where('id_repayment_statement','<>',$id_repayment_statement)
                 ->first() ?? null;
        return $data;
    }

    public function StatementLoans($id_repayment_statement){
        $loans = DB::table('repayment_statement_details as rsd')
                                ->select(DB::raw("rsd.id_repayment_statement_details,rsd.id_loan,loan.loan_token,rsd.id_member,TRIM(FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)) as member,getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms) as loan_name,rsd.loan_due as current_due,rsd.penalty,ifnull(rsd.amount_paid,rsd.loan_due) as amount_paid,ifnull(rsd.amount_paid,0) as act_amount_paid,rt.id_cash_receipt_voucher"))
                                ->leftJoin('member as m','m.id_member','rsd.id_member')
                                ->leftJoin('loan','loan.id_loan','rsd.id_loan')
                                ->leftJoin('loan_service as ls','ls.id_loan_service','loan.id_loan_service')
                                ->leftJoin('repayment_transaction as rt','rt.id_repayment_transaction','rsd.id_repayment_transaction')
                                ->where('rsd.id_repayment_statement',$id_repayment_statement)
                                ->orderBy('member','ASC')
                                ->get();
        return $loans;        
    }
    public function post(Request $request){
        $date= $request->date ?? MySession::current_date();
        $id_baranggay_lgu = $request->id_barangay_lgu;
        $opcode = $request->opcode ?? 0;

        $loans = $request->loans ?? [];
        $id_repayment_statement = $request->id_repayment_statement ?? 0;
        $members = $request->members ?? [];





        if(count($members) == 0){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Please select at least 1 member";
            return response($data);
        }


        // dd($request->all());
        $due_date = WebHelper::ConvertDatePeriod($date);
        if($opcode == 0){
            $v = $this->validation($due_date,$id_baranggay_lgu,$id_repayment_statement);
            if($v){
                $data['RESPONSE_CODE'] = "ERROR";
                $data['message'] = "Selected Brgy/LGU has already Statement";

                return response($data);
            }            
        }

        $loanFormatted = array();
        foreach($loans as $loan){
            $loanFormatted[$loan['id_loan']] = $loan;
        }

    
        $param = ['branch'=>$id_baranggay_lgu,'date1'=>$due_date,'date2'=>$due_date];
        $ActiveLoans = $this->ActiveLoans($param);

        $g = new GroupArrayController();
        $ActiveLoans = $g->array_group_by($ActiveLoans,['id_loan']);
        $loanOBJ = array();

        foreach($ActiveLoans as $id_loan=>$l){
            if(in_array($l[0]->id_member,$members)){
                $loanOBJ[]=[
                    'id_repayment_statement'=>0,
                    'id_member'=>$l[0]->id_member,
                    'id_loan'=>$id_loan,
                    'loan_due'=>$l[0]->current_due,
                    'penalty'=>0
                    // 'penalty'=>($loanFormatted[$id_loan])?$loanFormatted[$id_loan]['penalty']:0
                ];                
            }

        }


        $RepaymentStatement = [
            'date' => $date,
            'due_date'=>$due_date,
            'id_baranggay_lgu'=>$id_baranggay_lgu,
            'user_id'=>MySession::myId(),
        ];

        DB::beginTransaction();

        try{

            if($opcode == 0){
                DB::table('repayment_statement')
                ->insert($RepaymentStatement);

                $id_repayment_statement = DB::table('repayment_statement')->max('id_repayment_statement');                
            }


            DB::table('repayment_statement_details')
            ->where('id_repayment_statement',$id_repayment_statement)
            ->delete();

            for($i=0;$i<count($loanOBJ);$i++){
                $loanOBJ[$i]['id_repayment_statement'] = $id_repayment_statement;
            }

            DB::table('repayment_statement_details')
            ->insert($loanOBJ);

            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            dd($e->getMessage());
        }

        $data['RESPONSE_CODE'] = "SUCCESS";
        $data['message'] = "Loan Payment Statement Successfully Posted";
        $data['ID_REPAYMENT_STATEMENT'] = $id_repayment_statement;

        return response($data);


        dd($loanOBJ);

        // dd($loans);
    }
    public function postAmount(Request $request){
        $loans = $request->loans;

        $id_repayment_statement = $request->ID_REPAYMENT_STATEMENT ?? 0;

        $paymode = $request->paymode;

        $field = array();


        $loan_payment_total = collect($loans)->sum('amount_paid');

        if($loan_payment_total == 0){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Please apply loan payment";

            return response($data);
        }
     

        if($loan_payment_total != floatval($paymode['check_amount'])){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Check Amount and Loan Payment Application not balance";
            return response($data);
        }


        $postOBJ = array(
            'id_check_type'=>null,
            'id_bank'=>null,
            'check_date'=>null,
            'check_no'=>null
        );

        //compilation of payment mode details
        if($paymode['paymode'] == 4){
            //Check
            $field = array(
                'check_type'=>['key'=>'id_check_type','required'=>true],
                'check_bank'=>['key'=>'check_bank','required'=>true],
                'check_date'=>['key'=>'check_date','required'=>true],
                'check_no'=>['key'=>'check_no','required'=>true],
                'check_amount'=>['key'=>'check_amount','required'=>true],

                // 'date_deposited'=>['key'=>'date_deposited','required'=>true],
                // 'bank'=>['key'=>'id_bank','required'=>true],


                
            );
        }

        $field['paymode']=['key'=>'id_paymode','required'=>true];
        $field['date_received']=['key'=>'date_received','required'=>true];
        $field['remarks']=['key'=>'remarks','required'=>false];
        $field['or_number'] = ['key'=>'or_number','required'=>true];

        $loans = array_map(function($item) {
            $item['amount_paid'] = (float)$item['amount_paid'];
            return $item;
        }, $loans);


    

        $invalid_field = array();


        foreach($field as $key=>$f){
            if($f['required'] && (!isset($paymode[$key]) || $paymode[$key] == "" )){
                array_push($invalid_field,$key);
            }
            $postOBJ[$f['key']] = $paymode[$key];
        }




       $postOBJ['total_amount'] = collect($loans)->sum('amount_paid');

        if(count($invalid_field) > 0){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Please fill required fields";
            $data['invalid_fields'] = $invalid_field;
            return response($data);
        }

        $invalidLoans = array();
        foreach($loans as $loan){
            if($loan['amount_paid'] > 0){
                $d = DB::table('repayment_statement_details')->select('id_loan',DB::raw('ifnull(id_repayment_transaction,0) as id_repayment_transaction'))
                ->where('id_repayment_statement_details',$loan['id_repayment_statement'])->first();
            

                
                $loanBalance = Loan::LoanOverallBalance([$d->id_loan],$d->id_repayment_transaction ?? 0)[0];

                if($loan['amount_paid'] > $loanBalance->loan_balance){
                    array_push($invalidLoans,$d->id_loan);
                }
                
            }
        }

        if(count($invalidLoans) > 0){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['loanError'] = $invalidLoans;
            $data['message'] = "Invalid Loan Payment Amount";

            return response($data);
        }
   

        // $maxPayment = 

        $postOBJ['status'] = 1;
        $postOBJ['status_date'] = DB::raw("now()");
        DB::table('repayment_statement')
        ->where('id_repayment_statement',$id_repayment_statement)
        ->update($postOBJ);

        foreach($loans as $l){
            DB::table('repayment_statement_details')
            ->where('id_repayment_statement',$id_repayment_statement)
            ->where('id_repayment_statement_details',$l['id_repayment_statement'])
            ->update(['amount_paid'=>$l['amount_paid']]);
        }

        $data['RESPONSE_CODE'] = "SUCCESS";
        $data['message'] = "Loan Payment Transaction Successfully Posted";
        $data['ID_REPAYMENT_STATEMENT'] = $id_repayment_statement;

        $this->PushRepaymentBulk($id_repayment_statement);
        return response($data);
        dd("SUCCESS");
    }

    public function PrintStatement($type,$id_repayment_statement){
        $data['details'] = DB::table('repayment_statement as rs')
        ->select(DB::raw("rs.id_repayment_statement,rs.id_baranggay_lgu,rs.id_repayment_statement,date,due_date,bl.name as baranggay_lgu,if(bl.type=1,'Barangay','LGU') as group_,DATE_FORMAT(due_date,'%m%Y') as month_year,DATE_FORMAT(rs.date,'%M %d, %Y') as statement_date,DATE_FORMAT(due_date,'%M, %Y') as month_due,id_paymode,id_bank,id_check_type,check_date,check_no,remarks,total_amount,date_received,or_number,bl.treasurer,if(bl.type=1,'Brgy.','LGU') as group_shortcut,bl.type as type_code"))
        ->leftJoin('baranggay_lgu as bl','bl.id_baranggay_lgu','rs.id_baranggay_lgu')
        ->where('rs.id_repayment_statement',$id_repayment_statement)
        ->first();        

        $loans = $this->StatementLoans($id_repayment_statement);
        $g = new GroupArrayController();
        $data['loans'] = $g->array_group_by($loans,['id_member']);

        $view = ($type=="generated")?'print':'print_payment';


        $data['file_name'] = "TEST";

        $html =  view('repayment-statement.'.$view,$data);

        // return $html;
        $pdf = PDF::loadHtml($html);
        $pdf->setOption("encoding","UTF-8");
        $pdf->setOption('margin-bottom', '0.33in');
        $pdf->setOption('margin-top', '0.33in');
        $pdf->setOption('margin-right', '0.33in');
        $pdf->setOption('margin-left', '0.42in');
        $pdf->setOption('header-left', 'Page [page] of [toPage]');
        $pdf->setOption('header-right', 'No.: '.$data['details']->month_year.'-'.$data['details']->id_repayment_statement);
    
        $pdf->setOption('header-font-size', 8);
        $pdf->setOption('header-font-name', 'Calibri');

        return $pdf->stream("Manager Certification {$data['file_name']}.pdf",array('Attachment'=>1));
    }
    public function postStatus(Request $request){
        $id_repayment_statement = $request->id_repayment_statement ?? 0;
        $status = $request->status ?? 10;
        $reason = $request->reason;

        $details = DB::table('repayment_statement')
                   ->where('id_repayment_statement',$id_repayment_statement)
                   ->first();

      

        if($details->status == 10){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Invalid Request";

            return response($data);
        }

        if($details->status == 2){
            $data['RESPONSE_CODE'] = "ERROR";
            $data['message'] = "Loan Payment is already remitted";

            return response($data);
        }


        DB::table('repayment_statement')
        ->where('id_repayment_statement',$id_repayment_statement)
        ->update(['status'=>10,
                  'status_date'=>DB::raw("now()"),
                  'id_user_status'=>MySession::myId(),
                  'status_remarks'=>$reason]);

        $data['RESPONSE_CODE'] = "SUCCESS";
        $data['message'] = "Status Successfully Updated";





        if($details->status == 1){
            $RepaymentBulk = new BulkRepaymentController();

            $requestObj = [
                'id_repayment'=>$details->id_repayment,
                'cancel_reason'=>$reason
            ];

            $sync_out = $RepaymentBulk->updateStatus(new Request($requestObj));            
        }




        return response($data);
    }

    public function PushRepaymentBulk($id_repayment_statement){
        $loans = DB::table('repayment_statement_details as rsd')
                 ->select(DB::raw("loan.loan_token,loan.id_member,ifnull(amount_paid,0) as loan_payment,ifnull(rsd.id_repayment_transaction,0) as id_repayment_transaction"))
                 ->leftJoin('loan','loan.id_loan','rsd.id_loan')
                 ->where('id_repayment_statement',$id_repayment_statement)
                 ->get();
    
        $g = new GroupArrayController();
        $loans = $g->array_group_by($loans,['id_member']);

        $loanOBJ = array();

        foreach($loans as $id_member=>$loan){
            $temp = [];
            $temp['id_member']=$id_member;
            $temp['loan_payment'] = array();
            foreach($loan as $l){
                $temp['loan_payment'][$l->loan_token]=[
                    'loan_payment'=>$l->loan_payment,
                    'penalty'=>0
                ];
            }
            $temp['cbu']=0;
            $temp['id_repayment_transaction']=$loan[0]->id_repayment_transaction ;
            
            array_push($loanOBJ,$temp);
        }
      
        
        $details = DB::table("repayment_statement")
                    ->where('id_repayment_statement',$id_repayment_statement)
                    ->first();

        $paymode = [
            'transaction_date'=>$details->date_deposited,
            'paymode'=>$details->id_paymode,
            'or_number'=>$details->or_number,
            'check_type'=>$details->id_check_type,
            'bank'=>$details->id_bank,
            'check_date'=>$details->check_date,
            'check_no'=>$details->check_no,
            'remarks'=>$details->remarks,
            'check_bank'=>$details->check_bank,
            'check_amount'=>$details->check_amount,
            'date_received'=>$details->date_received


        ];

        $id_repayment=$details->id_repayment ?? 0;
        $opcode = ($id_repayment > 0)?1:0;


        $br=$details->id_baranggay_lgu;

        $requestObj = [
            'loans'=>$loanOBJ,
            'opcode'=>$opcode,
            'paymode'=>$paymode,
            'id_repayment'=>$id_repayment,
            'br'=>$br,
            'date_deposited'=>$details->date_deposited,
            'date'=>$details->date
        ];


        // $RepaymentBulk = new BulkRepaymentController();
        $RepaymentBulk = new RepaymentBulkController();

        $sync_out = $RepaymentBulk->post(new Request($requestObj));

        // dd($sync_out);

        DB::table('repayment_statement')
        ->where('id_repayment_statement',$id_repayment_statement)
        ->update(['id_repayment'=>$sync_out['ID_REPAYMENT']]);

        DB::table('repayment')
        ->where('id_repayment',$sync_out['ID_REPAYMENT'])
        ->update(['id_repayment_statement'=>$id_repayment_statement]);


        DB::select("UPDATE (
        SELECT r.id_repayment,rt.id_repayment_transaction,rt.id_member FROM repayment as r
        LEFT JOIN repayment_transaction as rt on rt.id_repayment = r.id_repayment
        WHERE r.id_repayment = ?
        GROUP BY id_member) as l
        LEFT JOIN repayment_statement as rs on rs.id_repayment = l.id_repayment
        LEFT JOIN repayment_statement_details as rsd on rsd.id_repayment_statement = rs.id_repayment_statement AND l.id_member = rsd.id_member
        SET rsd.id_repayment_transaction = l.id_repayment_transaction;",[$sync_out['ID_REPAYMENT']]);



        return 123;
        dd($sync_out);
        
        dd($requestObj);

        dd($loanOBJ);
        dd($id_repayment_statement);
    }
}
// array:5 [
//   "loans" => array:2 [
//     0 => array:4 [
//       "id_member" => "8"
//       "loan_payment" => array:1 [
//         "10042024225302f6b64706" => array:2 [
//           "loan_payment" => "300"
//           "penalty" => "0"
//         ]
//       ]
//       "cbu" => "0"
//       "id_repayment_transaction" => "0"
//     ]
//     1 => array:4 [
//       "id_member" => "630"
//       "loan_payment" => array:1 [
//         "10012024105015952f5672" => array:2 [
//           "loan_payment" => "5900"
//           "penalty" => "0"
//         ]
//       ]
//       "cbu" => "0"
//       "id_repayment_transaction" => "0"
//     ]
//   ]
//   "opcode" => "0"
//   "paymode" => array:8 [
//     "transaction_date" => "2024-10-10"
//     "paymode" => "4"
//     "or_number" => null
//     "check_type" => "1"
//     "bank" => "1"
//     "check_date" => "2024-10-10"
//     "check_no" => null
//     "remarks" => null
//   ]
//   "id_repayment" => "0"
//   "br" => "8"
// ]