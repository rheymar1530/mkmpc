<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Loan;
use App\Member;
use App\MySession;
use App\CredentialModel;
use Dompdf\Dompdf;
use App\JVModel;
use PDF;


class JournalVoucherController extends Controller
{
    public function index(Request $request){
        $data['credential']= CredentialModel::GetCredential(MySession::myPrivilegeId());
        if(!$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
       
        $start = date('Y-m-d', strtotime('-30 days'));
        $end = MySession::current_date();

        $data['date_from'] = $request->date_from ?? $start;
        $data['date_to'] = $request->date_to ?? $end;
        $data['fil_type'] = $request->fil_type ?? 1;
        $data['jv_search'] = $request->jv_search;
        $data['head_title'] = "Journal Voucher";

        $data['jv_list'] =  DB::table('journal_voucher as jv')
                            ->select(DB::raw("jv.id_journal_voucher,DATE_FORMAT(jv.date,'%m/%d/%Y') as date,
                                    CASE 
                                        WHEN jv.payee_type = 1 THEN 'Supplier'
                                        WHEN jv.payee_type = 2 THEN 'Member'
                                        WHEN jv.payee_type = 3 THEN 'Employee'
                                        WHEN jv.payee_type = 4 THEN 'Others'
                                        ELSE '' END as payee_type,
                                    jv.payee,jv.reference,
                                    if(jv.type=1,if(jv.jv_type=1,'Normal',if(jv.jv_type=2,'Reversal','Adjustment')),jt.description) as jv_type,jv.description,jv.total_amount,jv.status"))
                            ->leftJoin('jv_type as jt','jt.id_jv_type','jv.type')
                            ->where(function($query) use($data){
                                if($data['fil_type'] == 1){
                                    $query->where('jv.date','>=',$data['date_from'])
                                          ->where('jv.date','<=',$data['date_to']);
                                }elseif($data['fil_type'] == 2){
                                    $query->where('jv.id_journal_voucher','=',$data['jv_search']);
                                }
                            })
                            ->orDerby('id_journal_voucher','DESC')
                            ->get();

        // return $data['jv_list'];
        return view('journal_voucher.index',$data);
        return $data;
    }
    public function create(){
        $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/journal_voucher');
        if(!$data['credential']->is_create){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
        $data['head_title'] = "Create Journal Voucher";
        $data['current_date'] = MySession::current_date();
        $data['allow_post'] = true;
        $data['opcode'] = 0;
        $data['charts'] = DB::table('chart_account')
                          ->select('id_chart_account','account_code','description')
                          ->get();
        $data['branches'] = DB::table('tbl_branch')->get();

        // return $data['branches'];
        return view('journal_voucher.jv_form',$data);
    }

    public function post(Request $request){
        $j_parent = $request->jv_parent;

        // return $j_parent;
        $opcode = $request->opcode;
        

        $jv_parent = array();
        $key_reference ='';
        switch($j_parent['payee_type']){
            case '1':
                $key_reference = "id_supplier";
                break;
            case '2':
                $key_reference = "id_member";
                break;
            case '3':
                $key_reference = "id_employee";
                break;
            default:
        }
        $jv_parent = [
            'date'   => $j_parent['date'],
            'payee_type' => $j_parent['payee_type'],
            'description' => $j_parent['description'],
            'type' => 1,
            'id_user' =>  MySession::mySystemUserId(),
            'jv_type' => $j_parent['jv_type'],
            'address' =>$j_parent['address'],
            'reference' => $j_parent['reference'],
            'id_branch'=>$j_parent['id_branch'],
            // 'id_journal_voucher_reference' => $j_parent['id_journal_voucher_reference'] ?? 0
        ];

        if($opcode == 1){
            //If edit revert the following fields
            $jv_parent['id_journal_voucher_reference'] = 0;
            $jv_parent['id_member'] = 0;
            $jv_parent['id_employee'] = 0;
            $jv_parent['id_supplier'] = 0;
            $jv_parent['id_cdv'] = 0;
            $jv_parent['id_crv'] = 0;


        }

        if(isset($j_parent['id_adj_reference'])){
            $exploded  = explode("-",$j_parent['id_adj_reference']);
            $type = $exploded[0];
            $id_adj = $exploded[1];

            if($type == "cdv"){
                $jv_parent['id_cdv'] = $id_adj;
            }elseif($type == "jv"){
                $jv_parent['id_journal_voucher_reference'] = $id_adj;
                $jv_to_adjust = DB::table('journal_voucher')
                                ->select("id_cdv","id_crv")
                                ->where('id_journal_voucher',$id_adj)
                                ->first();
                if(isset($jv_to_adjust)){
                    if($jv_to_adjust->id_cdv > 0){
                        $jv_parent['id_cdv'] = $jv_to_adjust->id_cdv;
                    }elseif($jv_to_adjust->id_crv > 0){
                        $jv_parent['id_crv'] = $jv_to_adjust->id_crv;
                    }
                }
            }elseif($type == "crv"){
                $jv_parent['id_crv'] = $id_adj;
            }
        }

        // return $jv_parent;
        // return $jv_parent;
        if($key_reference != ""){
            $jv_parent[$key_reference] = $j_parent['payee_reference'];
            $jv_payee_det = $this->parsePayeeDetails($j_parent['payee_type'],$j_parent['payee_reference']);
            $jv_parent['payee'] = $jv_payee_det->name;
            
            // $jv_parent['address'] = $jv_payee_det->address;
        }else{
            $jv_parent['payee'] = $j_parent['payee'];
            // $jv_parent['address'] = $j_parent['address'];
        }
    
        $jv_entries_com = $this->populate_charts($request->chart_entry);
        $balance = $jv_entries_com['balance'];

        if(!$balance){
            $response['RESPONSE_CODE'] = "ERROR";
            $response['message'] = "Entry not balance";
            return $response;
        }

        $jv_entries = $jv_entries_com['entries'];
        $total_amount = $jv_entries_com['total'];
        $jv_parent['total_amount'] = $total_amount;
 
        // return $jv_entries_com;


        if($opcode == 0){
            DB::table('journal_voucher')
            ->insert($jv_parent);

            $id_journal_voucher = DB::table('journal_voucher')->where('type',1)->max('id_journal_voucher');
        }else{
            $id_journal_voucher = $request->id_journal_voucher;
            DB::table('journal_voucher')
            ->where('id_journal_voucher',$id_journal_voucher)
            ->update($jv_parent);            

            DB::table('journal_voucher_details')->where('id_journal_voucher',$id_journal_voucher)->delete();
        }


        
        for($i=0;$i<count($jv_entries);$i++){
            $jv_entries[$i]['id_journal_voucher'] =$id_journal_voucher;
        }
        DB::table('journal_voucher_details')
        ->insert($jv_entries);



        JVModel::setIsCash($id_journal_voucher);

        $response['RESPONSE_CODE'] = "SUCCESS";
        $response['id_journal_voucher'] = $id_journal_voucher;

        return response($response);

        return $jv_parent;
    }

    function populate_charts($chart_entry){
        $search_id_chart = array();
        foreach($chart_entry as $c){
            array_push($search_id_chart,$c['id_chart_account']);
        }

        $charts = DB::table('chart_account')
                 ->Select('id_chart_account','account_code','description')
                 ->whereIn('id_chart_account',$search_id_chart)
                 ->get();


        $g = new GroupArrayController();
        $charts = $g->array_group_by($charts,['id_chart_account']);
        $total = 0;
        $total_debit = 0;
        $total_credit = 0;
        for($i=0;$i<count($chart_entry);$i++){
            $chart_entry[$i]['account_code'] = $charts[$chart_entry[$i]['id_chart_account']][0]->account_code;
            $chart_entry[$i]['description'] = $charts[$chart_entry[$i]['id_chart_account']][0]->description;

            $total += $chart_entry[$i]['credit'];

            $total_credit += $chart_entry[$i]['credit'];
            $total_debit += $chart_entry[$i]['debit'];
        }
        if(ROUND($total_debit,2) != ROUND($total_credit,2)){
            $data['balance'] = false;

            return $data;
        }

        $data['balance'] = true;
        $data['entries'] = $chart_entry;
        $data['total'] = $total;
        return $data;
        return $chart_entry;

        return $search_id_chart;
    }

    function parsePayeeDetails($type,$reference){
        switch($type){
            case '1':
                 $details = DB::table('supplier')->select(DB::raw("name,address"))->where('id_supplier',$reference)->first();
                 return $details;
                break;
            case '2':
                $details = DB::table('member as m')->select(DB::raw("FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as name,address as address"))->where('id_member',$reference)->first();

                return $details;
                break;
            case '3':
                 $details = DB::table('employee as e')->select(DB::raw("FormatName(e.first_name,e.middle_name,e.last_name,e.suffix) as name,address as address"))->where('id_employee',$reference)->first();
                return $details;               
                break;
            default:

        }
    }
    public function parse_address(Request $request){
        $data['response'] = $this->parsePayeeDetails($request->type,$request->reference);
        return response($data);
    }
    public function search_jv_reference(Request $request){
        if($request->ajax()){
            $search = $request->term;   
            // if(strlen($search) < 3){
            //     $data['accounts'] = array();
            //     return response($data);
            // }
            // $data['accounts'] = 
            $jv = DB::table('journal_voucher')
            ->select(DB::raw("concat('JV# ',id_journal_voucher,' ',description) as tag_value,CONCAT('jv-',id_journal_voucher) as tag_id"))
            ->where(function ($query) use ($search){
                // $query->where(DB::raw("id_journal_voucher"), '=', "$search");

                $query->where(DB::raw("concat('JV #',id_journal_voucher)"), 'like', "%$search%");
            });

            
            $data['accounts'] =  DB::table('cash_disbursement')
            ->select(DB::raw("concat('CDV# ',id_cash_disbursement,' ',description) as tag_value,CONCAT('cdv-',id_cash_disbursement) as tag_id"))
            ->where(function ($query) use ($search){
                // $query->where(DB::raw("id_cash_disbursement"), '=', "$search");
                $query->where(DB::raw("concat('CDV #',id_cash_disbursement)"), 'like', "%$search%")->whereNotIn('type',[1,5]);
            })
            ->union($jv)
            ->get();
            return response($data);
        }
    }
    public function reversal_content(Request $request){
        // if($request->ajax()){
            $id_reference = $request->id_reference;

            $ex_reference = explode("-",$id_reference);

            $reference_type = $ex_reference[0];
            $id_reference_search = $ex_reference[1];





            if($reference_type == 'jv'){
                $table = "journal_voucher";
                $table_id = "id_journal_voucher";
                $entry_table = "journal_voucher_details";
            }elseif($reference_type == "cdv"){
                $table = "cash_disbursement";
                $table_id = "id_cash_disbursement";
                $entry_table ="cash_disbursement_details";
            }

            // return $table;

            $data['adj_details'] = DB::table($table)
                               ->where($table_id,$id_reference_search)
                               ->first();

            // return $data;
            switch($data['adj_details']->payee_type){
                case   '1':
                    $data['selected_reference_payee'] = DB::table('supplier')->select('id_supplier as id','name')->where('id_supplier',$data['adj_details']->id_supplier)->first();

                    break;
                case   '2':
                    $data['selected_reference_payee'] =DB::table('member as m')
                    ->select(DB::raw("concat(membership_id,' || ',FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)) as name,id_member as id"))
                    ->where('id_member',$data['adj_details']->id_member)
                    ->first();


                    break;
                case   '3':
                    $data['selected_reference_payee'] =DB::table('employee as e')
                    ->select(DB::raw("concat(id_employee,' || ',FormatName(e.first_name,e.middle_name,e.last_name,e.suffix)) as name,id_employee as id"))
                    ->where('id_employee',$data['adj_details']->id_employee)
                    ->first();
                    break;

                case '4':
                    $data['payee'] = $data['adj_details']->payee;
                    break;
                default:

            }
            // return $data;

            $data['entries'] = DB::table($entry_table)
                               ->select(DB::raw("*,credit as debit,debit as credit"))
                               ->where($table_id."",$id_reference_search)
                               ->get();
            return response($data);
        // }
    }
    public function view($id_journal_voucher){
        $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/journal_voucher');
        
        if(!$data['credential']->is_view && !$data['credential']->is_edit){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }

        $data['opcode'] = 1;
        $data['current_date'] = MySession::current_date();
        $data['charts'] = [];
        $data['branches'] = DB::table('tbl_branch')->get();
        $data['head_title'] = "Journal Voucher #$id_journal_voucher";
        // $data['charts'] = DB::table('chart_account')
        //                   ->select('id_chart_account','account_code','description')
        //                   ->get();
       
        $data['allow_post'] = false;
        $data['jv_details'] = DB::table('journal_voucher')
                              ->where('id_journal_voucher',$id_journal_voucher)
                              ->first();
        if($data['jv_details']->type == 1){
            if($data['jv_details']->status == 10){
                 $data['allow_post'] = false;
            }else{
                $data['allow_post'] = true;
                $data['charts'] = DB::table('chart_account')
                      ->select('id_chart_account','account_code','description')
                      ->get();                
            }
        }

        if($data['jv_details']->jv_type == 2){
           if($data['jv_details']->id_journal_voucher_reference > 0){
               $data['selected_reference'] = DB::table('journal_voucher')
                ->select(DB::raw("concat('JV# ',id_journal_voucher,' ',description) as tag_value,id_journal_voucher as tag_id"))
                ->where('id_journal_voucher',$data['jv_details']->id_journal_voucher_reference)
                ->first();            
            }elseif($data['jv_details']->id_cdv > 0){
               $data['selected_reference'] = DB::table('cash_disbursement')
                ->select(DB::raw("concat('CDV# ',id_cash_disbursement,' ',description) as tag_value,id_cash_disbursement as tag_id"))
                ->where('id_cash_disbursement',$data['jv_details']->id_cdv)
                ->first();                  
            }

        }

        switch($data['jv_details']->payee_type){
            case   '1':
                $data['selected_reference_payee'] = DB::table('supplier')->select('id_supplier as id','name')->where('id_supplier',$data['jv_details']->id_supplier)->first();

                break;
            case   '2':
                $data['selected_reference_payee'] =DB::table('member as m')
                ->select(DB::raw("concat(membership_id,' || ',FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)) as name,id_member as id"))
                ->where('id_member',$data['jv_details']->id_member)
                ->first();


                break;
            case   '3':
                $data['selected_reference_payee'] =DB::table('employee as e')
                ->select(DB::raw("concat(id_employee,' || ',FormatName(e.first_name,e.middle_name,e.last_name,e.suffix)) as name,id_employee as id"))
                ->where('id_employee',$data['jv_details']->id_employee)
                ->first();

                break;
            case '4':
                $data['payee'] = $data['jv_details']->payee;
                break;
            default:

                // $data['selected_emploee']
        }

        $data['chart_details'] = DB::table('journal_voucher_details')
                          ->select('id_chart_account','account_code','description','debit','credit','description','details')
                          ->where('id_journal_voucher',$id_journal_voucher)
                          ->get();
        // return $data;
        return view('journal_voucher.jv_form',$data);
        return $id_journal_voucher;
    }
    public function printJV($id_journal_voucher){
        $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/journal_voucher');
        if(!$data['credential']->is_view){
            return "INVALID";
            // return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
        $data['prepared_by'] = MySession::myName();
        $data['jv_details'] = DB::table('journal_voucher as jv')
        ->select(DB::raw("id_journal_voucher,payee,branch_name,jv.description,total_amount,DATE_FORMAT(date,'%m/%d/%Y') as date,jv.address, if(jv.type=1,if(jv.jv_type=1,'Normal',if(jv.jv_type=2,'Reversal','Adjustment')),jt.description) as jv_type,jv.reference"))
        ->leftJoin('tbl_branch','tbl_branch.id_branch','jv.id_branch')
        ->leftJoin('jv_type as jt','jt.id_jv_type','jv.type')
        ->where('id_journal_voucher',$id_journal_voucher)
        ->first();

        $data['entries'] = DB::table('journal_voucher_details')
                          ->select('account_code','description','debit','credit','description','details')
                          ->where('id_journal_voucher',$id_journal_voucher)
                          ->get();


        $html = view('journal_voucher.print_jv_new',$data);
        // // return $html;
        // $dompdf = new Dompdf();
        // $dompdf->loadHtml($html);
        // $dompdf->render();
        // $font = $dompdf->getFontMetrics()->get_font("serif");

        // $dompdf->getCanvas()->page_text(500, 50, "JV No. $id_journal_voucher", $font, 12, array(0,0,0));
        // $dompdf->getCanvas()->page_text(24, 18, "Page {PAGE_NUM} of {PAGE_COUNT}", $font, 8, array(0,0,0));
        // $canvas = $dompdf->getCanvas();
        // // $dompdf->set_paper("A4", 'landscape');
        // // $dompdf->getCanvas()->page_text(530, 5, "Page: {PAGE_NUM} of {PAGE_COUNT}", $font, 9, array(0,0,0));      
        // $dompdf->stream("Journal Voucher No $id_journal_voucher.pdf", array("Attachment" => false)); 

        $pdf = PDF::loadHtml($html);
        $pdf->setOption("encoding","UTF-8");
        $pdf->setOption('margin-bottom', '5mm');
        $pdf->setOption('margin-top', '7mm');
        $pdf->setOption('margin-right', '3mm');
        $pdf->setOption('margin-left', '3mm');
        $pdf->setOption('header-left', 'Page [page] of [toPage]');

        $pdf->setOption('header-font-size', 8);
        $pdf->setOption('header-font-name', 'Calibri');

        $pdf->setOption('enable-javascript', true);

        return $pdf->stream();  

        return $data;
        return $id_journal_voucher;
    }
    public function cancel(Request $request){
        if($request->ajax()){
            $id_journal_voucher = $request->id_journal_voucher;
            $reason = $request->cancel_reason;
      


            
            $validation = DB::table('journal_voucher')->where('id_journal_voucher',$id_journal_voucher)->first();
            if($validation->status == 10){
                $data['RESPONSE_CODE'] = "ERROR";
                $data['message'] = "Cash Disbursement is already cancelled";

                return response($data);
            }
    
 

            DB::table('journal_voucher')
            ->where('id_journal_voucher',$id_journal_voucher)
            ->where('status','<>',10)
            ->update(['cancellation_reason'=>$reason,
               'date_cancelled' => DB::raw('now()'),
               'status' => 10,
               'description'=>DB::raw("concat(description,' [CANCELLED]')")]);

            $data['RESPONSE_CODE'] = "SUCCESS";

            return response($data);
        }
    }
}
