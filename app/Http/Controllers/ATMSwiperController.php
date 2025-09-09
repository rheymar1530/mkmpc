<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MySession;
use App\CDVModel;
use App\JVModel;
use DB;
use App\CredentialModel;
use Dompdf\Dompdf;
use PDF;
use App\WebHelper;
class ATMSwiperController extends Controller
{
    public function index(Request $request){
        $data['credential']= CredentialModel::GetCredential(MySession::myPrivilegeId());
        if(!$data['credential']->is_view){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
        $data['head_title'] = "ATM Swipe";
        $data['current_date'] = MySession::current_date();
        $data['atm_swipes'] = DB::table("atm_swipe as atm")
                              ->select(DB::raw("atm.id_atm_swipe,DATE_FORMAT(atm.date,'%M %d,%Y') as date,
                                                CASE
                                                WHEN client_type =2 THEN FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)
                                                WHEN client_type = 3 THEN  FormatName(e.first_name,e.middle_name,e.last_name,e.suffix)
                                                ELSE client END as client,atm.amount,atm.transaction_charge,atm.id_cash_disbursement,atm.status,DATE_FORMAT(atm.date_created,'%M %d,%Y') as date_created,change_payable,atm.id_journal_voucher
                                                "))
                                ->leftJoin('member as m','m.id_member',DB::raw("if(atm.client_type=2,atm.id_member,0)"))
                                ->leftJoin('employee as e','e.id_employee',DB::raw("if(atm.client_type=3,atm.id_employee,0)"))
                                ->orDerby('atm.id_atm_swipe','DESC')
                                ->get();
        // return $data;
        return view('atm_swipe.index',$data);


        return response($data);

    }
    public function create(){

        $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/atm_swipe');
        if(!$data['credential']->is_create){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
        $data['head_title'] = "Create ATM Swipe";
        $data['current_date'] = MySession::current_date();
        $data['banks'] = DB::table('tbl_bank')->get();
        $data['opcode'] = 0;
        $data['allow_post'] = true;
        // return $data;
        return view('atm_swipe.form',$data);
    }

    public function view($id_atm_swipe){
        $data['credential']= CredentialModel::GetCredentialFrame(MySession::myPrivilegeId(),'/atm_swipe');
        if(!$data['credential']->is_view && !$data['credential']->is_edit){
            return redirect('/redirect/error')->with('message', "privilege_access_invalid");
        }
        $data['current_date'] = MySession::current_date();
        $data['banks'] = DB::table('tbl_bank')->get();
        $data['opcode'] = 1;
        $data['allow_post'] = true;
        $data['head_title'] = "ATM Swipe# $id_atm_swipe";

        $data['atm_swipe_details'] = DB::table('atm_swipe')->where('id_atm_swipe',$id_atm_swipe)->first();

        // return $data;

        switch($data['atm_swipe_details']->client_type){
            case   '2':
            $data['selected_reference_payee'] =DB::table('member as m')
            ->select(DB::raw("concat(membership_id,' || ',FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)) as name,id_member as id"))
            ->where('id_member',$data['atm_swipe_details']->id_member)
            ->first();
            break;
            case   '3':
            $data['selected_reference_payee'] =DB::table('employee as e')
            ->select(DB::raw("concat(id_employee,' || ',FormatName(e.first_name,e.middle_name,e.last_name,e.suffix)) as name,id_employee as id"))
            ->where('id_employee',$data['atm_swipe_details']->id_employee)
            ->first();
            break;
            case '4':

            break;
            default:

                // $data['selected_emploee']
        }

        return view('atm_swipe.form',$data);
    }

    public function post(Request $request){
        if($request->ajax()){
            $data['RESPONSE_CODE'] = "SUCCESS";
            $opcode = $request->opcode;
            $id_atm_swipe = $request->id_atm_swipe;

            //VALIDATE AMOUNT





            $swiping = $request->swiping;

            $amount = $swiping['amount'];
            $transaction_charge = $swiping['transaction_charge'];
            $change_payable = $amount-$transaction_charge;

            if($change_payable <= 0){
                $data['RESPONSE_CODE'] = "ERROR";
                $data['message'] = "Invalid Amount";
                $data['highlight_amount'] = true;

                return response($data);
            }
            $swiping['change_payable'] = $change_payable;

            $key_reference ='';
            switch($swiping['client_type']){
                case '2':
                $key_reference = "id_member";
                $swiping['id_employee'] = 0;
                $swiping['client'] = null;
                break;
                case '3':
                $key_reference = "id_employee";
                $swiping['id_member'] = 0;
                $swiping['client'] = null;
                break;
                case '4':
                $swiping['id_member'] = 0;
                $swiping['id_employee'] = 0;
                $swiping['client_reference']=0;
                break;
                default:
            }






            if($key_reference != ''){
                $swiping[$key_reference] = $swiping['client_reference'];
            }
 

            if($opcode == 0){
                DB::table('atm_swipe')
                ->insert($swiping);

                $id_atm_swipe = DB::table('atm_swipe')->max('id_atm_swipe');                
            }else{
                DB::table('atm_swipe')
                ->where('id_atm_swipe',$id_atm_swipe)
                ->update($swiping);
            }

            $data['id_atm_swipe'] = $id_atm_swipe;

            // return response($data);
            $data['id_cdv'] = CDVModel::ATMSwipeCDV($id_atm_swipe);
            $data['id_jv'] = JVModel::ATMSwipeJV($id_atm_swipe);
            // $data['id_cdv'] = 115;
            return response($data);
        }
    }
    
    public function cancel(Request $request){
        if($request->ajax()){

            $id_atm_swipe = $request->id_atm_swipe;
            $cancel_reason =  $request->cancel_reason;

            $output['RESPONSE_CODE'] = "SUCCESS";
            $test['details'] =$details = DB::table('atm_swipe')->where('id_atm_swipe',$id_atm_swipe)->first();

            if($details->status ==  10){
                $output['RESPONSE_CODE'] = "ERROR";
                $output['message'] = "ATM Swipe is already Cancelled";

                return response($output);
            }

            DB::table('atm_swipe')
            ->where('id_atm_swipe',$id_atm_swipe)
            ->update(['status'=>10,
                      'date_cancelled'=>DB::raw("now()"),
                      'cancellation_reason'=>$cancel_reason]);

            DB::table('cash_disbursement')
            ->where('id_cash_disbursement',$details->id_cash_disbursement)
            ->update(['status'=>10,
                      'date_cancelled'=>DB::raw("now()"),
                      'description'=>DB::raw("concat(description,' [CANCELLED]')"),
                      'cancellation_reason'=>$cancel_reason]);


            DB::table('journal_voucher')
            ->where('id_journal_voucher',$details->id_journal_voucher)
            ->update(['status'=>10,
                      'date_cancelled'=>DB::raw("now()"),
                      'description'=>DB::raw("concat(description,' [CANCELLED]')"),
                      'cancellation_reason'=>$cancel_reason]);

            return response($output);



            return response($test);
            return response($request);
        }
    }

    public function print_entry($id_atm_swipe){
        $d['e']=$atm_swipe= DB::table('atm_swipe')
        ->select('id_cash_disbursement','id_journal_voucher')
        ->where('id_atm_swipe',$id_atm_swipe)
        ->first();


        $data['prepared_by'] = MySession::myName();

        $data['jv_details'] = DB::table('journal_voucher as jv')
        ->select(DB::raw("id_journal_voucher,payee,branch_name,jv.description,total_amount,DATE_FORMAT(date,'%m/%d/%Y') as date,jv.address, if(jv.type=1,if(jv.jv_type=1,'Normal',if(jv.jv_type=2,'Reversal','Adjustment')),jt.description) as jv_type"))
        ->leftJoin('tbl_branch','tbl_branch.id_branch','jv.id_branch')
        ->leftJoin('jv_type as jt','jt.id_jv_type','jv.type')
        ->where('id_journal_voucher',$atm_swipe->id_journal_voucher)
        ->first();

        $data['jv_entries'] = DB::table('journal_voucher_details')
                          ->select('account_code','description','debit','credit','description','details')
                          ->where('id_journal_voucher',$atm_swipe->id_journal_voucher)
                          ->get();



        $data['cdv_details'] = DB::table('cash_disbursement')
        ->select(DB::raw("id_cash_disbursement,payee,branch_name,concat(description,if(cash_disbursement.status=10,'','')) as description,total,DATE_FORMAT(date,'%m/%d/%Y') as date,cash_disbursement.address,paymode,check_no"))
        ->leftJoin('member as m','m.id_member','cash_disbursement.id_member')
        ->leftJoin('tbl_branch','tbl_branch.id_branch','cash_disbursement.id_branch')
        ->where('id_cash_disbursement',$atm_swipe->id_cash_disbursement)
        ->first();

        $data['cdv_items'] = DB::table('cash_disbursement_details as cdv')
        ->select('id_cash_disbursement','account_code','description','debit','credit','remarks','details')
        ->where('id_cash_disbursement',$atm_swipe->id_cash_disbursement)
        ->orDerby('id_cash_disbursement_details')
        ->get();

        $html = view('atm_swipe.entry',$data);
        // return $html;
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->render();
        $font = $dompdf->getFontMetrics()->get_font("serif");

        // $dompdf->getCanvas()->page_text(500, 50, "CDV No. $id_cash_disbursement", $font, 12, array(0,0,0));
        // $dompdf->getCanvas()->page_text(24, 18, "Page {PAGE_NUM} of {PAGE_COUNT}", $font, 8, array(0,0,0));
        $canvas = $dompdf->getCanvas();
        // $dompdf->set_paper("A4", 'landscape');
        // $dompdf->getCanvas()->page_text(530, 5, "Page: {PAGE_NUM} of {PAGE_COUNT}", $font, 9, array(0,0,0));      
        $dompdf->stream("ATM SWIPE# $id_atm_swipe.pdf", array("Attachment" => false));   
        return $data;
        return $data;
    }

    public function update_records(){
        //UPDATE CHANGE PAYABLE
        DB::table('atm_swipe')
        ->update(['change_payable'=>DB::raw("amount-transaction_charge")]);

        //REFRESH ENTRY
        $ids = DB::table('atm_swipe')
                ->select('id_atm_swipe','status','cancellation_reason','date_cancelled')
                ->get();
        foreach($ids as $i){
            CDVModel::ATMSwipeCDV($i->id_atm_swipe);
            JVModel::ATMSwipeJV($i->id_atm_swipe);

            $id_journal_voucher = DB::table('journal_voucher')->where('reference',$i->id_atm_swipe)->max('id_journal_voucher');

            if($i->status == 10){
                DB::table('journal_voucher')
                ->where('id_journal_voucher',$id_journal_voucher)
                ->update(['status'=>10,
                'date_cancelled'=>$i->date_cancelled,
                'description'=>DB::raw("concat(description,' [CANCELLED]')"),
                'cancellation_reason'=>$i->cancellation_reason]);
            }
            
        }

        return "success";
    }

    public function print_atm_swipe_form($id_atm_swipe){
        $data = array();

        $data['details'] = DB::table("atm_swipe as atm")
                              ->select(DB::raw("atm.id_atm_swipe,DATE_FORMAT(atm.date,'%m/%d/%Y') as date,
                                                CASE
                                                WHEN client_type =2 THEN FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)
                                                WHEN client_type = 3 THEN  FormatName(e.first_name,e.middle_name,e.last_name,e.suffix)
                                                ELSE client END as client,atm.amount,atm.transaction_charge,atm.id_cash_disbursement,atm.status,DATE_FORMAT(atm.date_created,'%m/%d/%Y %r') as date_created,change_payable,atm.id_journal_voucher,if(atm.client_type=2,'Member',if(atm.client_type=3,'Employee','Others')) as client_type,tb.bank_name,atm.remarks
                                                "))
                                ->leftJoin('member as m','m.id_member',DB::raw("if(atm.client_type=2,atm.id_member,0)"))
                                ->leftJoin('employee as e','e.id_employee',DB::raw("if(atm.client_type=3,atm.id_employee,0)"))
                                ->leftJoin('tbl_bank as tb','tb.id_bank','atm.id_bank')
                                ->where('atm.id_atm_swipe',$id_atm_swipe)
                                ->first();
        // dd($data);
        $html =  view('atm_swipe.print_form',$data);

        // return $html;
        $pdf = PDF::loadHtml($html);
        $pdf->setOption("encoding","UTF-8");
        $pdf->setOption('margin-bottom', '5mm');
        $pdf->setOption('margin-top', '7mm');
        $pdf->setOption('margin-right', '5mm');
        $pdf->setOption('margin-left', '5mm');
        
        // $pdf->setOption('header-left', 'Page [page] of [toPage]');

        // $pdf->setOption('header-font-size', 8);
        // $pdf->setOption('header-font-name', 'Calibri');
        // $pdf->setOrientation('landscape');

        return $pdf->stream("TEST.pdf");
    }

    public function atm_swipe_summary($date_start,$date_end){
        $data['atm_swipes'] = DB::select("SELECT atm.id_atm_swipe,atm.id_cash_disbursement as reference,
                                            CASE
                                            WHEN client_type =2 THEN FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)
                                            WHEN client_type = 3 THEN  FormatName(e.first_name,e.middle_name,e.last_name,e.suffix)
                                            ELSE client END as client,atm.amount as amount_swiped,atm.transaction_charge,atm.change_payable
                                            FROM atm_swipe as atm
                                            LEFT JOIN member as m on m.id_member =if(atm.client_type=2,atm.id_member,0)
                                            LEFT JOIN employee as e on e.id_employee =if(atm.client_type=3,atm.id_employee,0)
                                            WHERE atm.date >= ? and atm.date <= ?
                                            ORDER BY atm.id_atm_swipe,atm.date;",[$date_start,$date_end]);

        $data['date'] = WebHelper::ReportDateFormatter($date_start,$date_end);

        // dd($data);

        // return $data['date'];

        $html = view('atm_swipe.print_summary',$data);
        $pdf = PDF::loadHtml($html);
        $pdf->setOption("encoding","UTF-8");
        $pdf->setOption('margin-bottom', '5mm');
        $pdf->setOption('margin-top', '7mm');
        $pdf->setOption('margin-right', '5mm');
        $pdf->setOption('margin-left', '5mm');
        $pdf->setOption('header-left', 'Page [page] of [toPage]');

        $pdf->setOption('header-font-size', 8);
        $pdf->setOption('header-font-name', 'Calibri');
        // $pdf->setOrientation('landscape');

        return $pdf->stream();  
    }
}
