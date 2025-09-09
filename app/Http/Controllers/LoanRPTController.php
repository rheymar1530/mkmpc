<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use PDF;
use DateTime;
use MySession;
use App\CredentialModel;


class LoanRPTController extends Controller
{   
    public function index(){
        $data['loan_services'] = DB::table('loan_service')
                                 ->select('id_loan_service','name')
                                 ->get();

        return view('loan_rpt.index',$data);

    }
    public function export(Request $request){
        $current_month = date("n", strtotime(MySession::current_date()));
        $current_year = date("Y", strtotime(MySession::current_date()));

        $month = $request->month ?? $current_month;
        $year = $request->year ?? $current_year;

        $id_loan_service = $request->id_loan_service ?? 1;

        $data['details']=$ls = DB::table('loan_service')
                      ->select(DB::raw("id_loan_service,name,id_loan_payment_type"))
                      ->where('id_loan_service',$id_loan_service)
                      ->first();

        $start = date("Y-m-01", strtotime("{$year}-{$month}-01"));
        $end = date("Y-m-t", strtotime("$start"));


        $data['month_selected'] = date("F Y", strtotime("{$year}-{$month}-01"));


        if($ls->id_loan_payment_type == 1){
            //installment
            $data['mode'] = 1;
            $data['loans'] = $this->Installment($id_loan_service,$start,$end);
        }else{
            //one time
            $data['mode'] = 2;
            $data['loans'] = $this->OneTime($id_loan_service,$start,$end);
        }

        // dd($data['loans']);

       

        
        // $data['loans'] = $this->Installment(4,$start,$end);
        

        // dd($data);

        $html =  view('loan_rpt.export',$data);

        // return $html;
        $pdf = PDF::loadHtml($html);
        $pdf->setOption("encoding","UTF-8");
        $pdf->setOption('margin-bottom', '0.7480in');
        $pdf->setOption('margin-top', '0.3in');
        $pdf->setOption('margin-right', '0.33in');
        $pdf->setOption('margin-left', '0.42in');
        $pdf->setOption('header-left', 'Page [page] of [toPage]');

        $pdf->setOption('header-font-size', 8);
        $pdf->setOption('header-font-name', 'Calibri');
        $pdf->setOrientation('landscape');

        return $pdf->stream();
   }

   public function Installment($id_loan_service,$start_date,$end_date){
      $loans = DB::select("
        WITH offseting as(
        SELECT loan.id_loan,SUM(lc.value) as amount 
        FROM loan 
        LEFT JOIN loan_charges as lc on lc.id_loan = loan.id_loan
        WHERE loan.date_released >= :st1 AND loan.date_released <= :en2 AND loan.id_loan_service = :id_ls AND lc.id_loan_fees = 15
        GROUP BY loan.id_loan)  
       SELECT l.*,lt.repayment_amount,lt.interest_amount,(lt.repayment_amount+lt.interest_amount) as total_amtz FROM (
       SELECT k.*
      ,ifnull(SUM(lo.amount),0)+loan_bal+ifnull(offseting.amount,0) as loan_offset FROM (
      SELECT loan.loan_token,loan.id_loan, FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member,bl.name as brgy_lgu,loan.principal_amount
      ,SUM(CASE WHEN lc.id_loan_fees = 1 THEN lc.calculated_charge ELSE 0 END) as sf
      ,SUM(CASE WHEN lc.id_loan_fees = 14 THEN lc.calculated_charge ELSE 0 END) as ff
      ,SUM(CASE WHEN lc.id_loan_fees = 2 THEN lc.calculated_charge ELSE 0 END) as cbu
      ,SUM(CASE WHEN lc.id_loan_fees = 11 THEN lc.calculated_charge ELSE 0 END) as insurance,
      ifnull(ppb.previous_principal+ppb.previous_interest+ppb.previous_fees,0) as loan_bal,
      total_loan_proceeds
      FROM loan
      LEFT JOIN member as m on m.id_member = loan.id_member
      LEFT JOIN baranggay_lgu as bl on bl.id_baranggay_lgu = loan.id_baranggay_lgu
      LEFT JOIN loan_charges as lc on lc.id_loan = loan.id_loan
      LEFT JOIN paid_previous_balance as ppb on ppb.id_loan_current = loan.id_loan
      WHERE loan.id_loan_service=:id_loan_service AND loan.date_released >= :start_date AND loan.date_released <= :end_date AND loan.loan_status > 0
      GROUP BY loan.id_loan) as k
      LEFT JOIN loan_offset as lo on lo.id_loan = k.id_loan
      LEFT JOIN offseting on offseting.id_loan = k.id_loan
      GROUP BY k.id_loan ) as l
      LEFT JOIN loan_table  as lt on lt.id_loan = l.id_loan
      GROUP BY l.id_loan
      ORDER BY l.id_loan;",['id_loan_service'=>$id_loan_service,'start_date'=>$start_date,'end_date'=>$end_date,'st1'=>$start_date,'en2'=>$end_date,'id_ls'=>$id_loan_service]);

      return $loans;
   }
   public function OneTime($id_loan_service,$start_date,$end_date){
    // dd(['id_loan_service'=>$id_loan_service,'start_date'=>$start_date,'end_date'=>$end_date]);
      $loans = DB::select("SELECT l.*,DATE_FORMAT(lt.due_date,'%M %Y') as due_date FROM (SELECT loan.loan_token,loan.id_loan, FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member,bl.name as brgy_lgu,loan.principal_amount
      ,SUM(CASE WHEN lc.id_loan_fees = 1 THEN lc.calculated_charge ELSE 0 END) as sf
      ,SUM(CASE WHEN lc.id_loan_fees = 14 THEN lc.calculated_charge ELSE 0 END) as ff
      ,SUM(CASE WHEN lc.id_loan_fees = 2 THEN lc.calculated_charge ELSE 0 END) as cbu
      ,SUM(CASE WHEN lc.id_loan_fees = 12 THEN lc.calculated_charge ELSE 0 END) as interest,
      total_loan_proceeds
      FROM loan
      LEFT JOIN member as m on m.id_member = loan.id_member
      LEFT JOIN baranggay_lgu as bl on bl.id_baranggay_lgu = loan.id_baranggay_lgu
      LEFT JOIN loan_charges as lc on lc.id_loan = loan.id_loan
       WHERE loan.id_loan_service=:id_loan_service AND loan.date_released >= :start_date AND loan.date_released <= :end_date AND loan.loan_status > 0
      GROUP BY loan.id_loan) as l
      LEFT JOIN loan_table as lt on lt.id_loan = l.id_loan
      GROUP BY l.id_loan
      ORDER BY l.id_loan;",['id_loan_service'=>$id_loan_service,'start_date'=>$start_date,'end_date'=>$end_date]);

      $g = new GroupArrayController();

      return $g->array_group_by($loans,['due_date']);
      // dd($loans);

      return $loans;

   }
}
