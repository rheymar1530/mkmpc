<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use PDF;
use DateTime;
use MySession;
use App\CredentialModel;

use Excel;
use App\Exports\InterestExport;


class PaidInterestSummaryController extends Controller
{

	public function index(Request $request){

		$credential= CredentialModel::GetCredential(MySession::myPrivilegeId());
		if(!$credential->is_view){
			return redirect('/redirect/error')->with('message', "privilege_access_invalid");
		}


		date_default_timezone_set('Asia/Manila');
		$dt = new DateTime();
		$year = $request->year ?? $dt->format('Y');
		$m = ($year == $dt->format('Y'))?MySession::current_month():12;
		$data = $this->parseData(1,$m,$year);

		$data['head_title'] = "Paid Interest Summary";
		$data['credential'] = $credential;

		// return $data;

		return view('reports.paid_interest',$data);

		return $data;
	}

	public function parseData($start_month,$end_month,$year){
		// $start_month = 1;
		// $end_month = 12;
		// $year = 2022;

		$data['selected_year'] = $year;
		$data['end_month'] = $end_month;
		$month_query= "";
		$month_query_ar = array();
		for($i=$start_month;$i<=$end_month;$i++){

			$dt_s = date("Y-m-01", strtotime("$year-$i-01"));
			$dt_e = date("Y-m-t", strtotime($dt_s));
			$month_text = date("M", strtotime($dt_s));

			$q = "SUM(CASE WHEN transaction_date >= '$dt_s' AND transaction_date <= '$dt_e' THEN amount ELSE 0 END) as '$month_text'";

			array_push($month_query_ar,$q);

		}

		// dd($dt_query_start);


		$dt_query_start = date("Y-m-01", strtotime("$year-$start_month-01"));
		$dt_query_end = date("Y-m-t", strtotime("$year-$end_month-01"));


		// dd($dt_query_start,$dt_query_end);


		$data['title_range'] = date("F 01", strtotime("$dt_query_start"))." - ".date("F t, Y", strtotime("$year-$end_month-01"));
		$data['description'] = $month_text = date("F 01", strtotime($dt_query_start))." to ".$month_text = date("F t", strtotime($dt_query_end))." 2022";


		$month_query = implode(",",$month_query_ar);

		// and (id_journal_voucher > 0 OR id_cash_receipt_voucher  > 0)
// 		$sql_query = "SELECT concat(m.last_name,if(m.suffix='','',concat(' ',m.suffix,' ')),', ',m.first_name,' ',if(m.middle_name <> '',UPPER(concat(LEFT(m.middle_name,1),'.')),'')) as Names,
// 		$month_query
// 		,SUM(amount) as Total
// 		FROM (
// 			/**************REPAYMENT INTEREST*******************/
// 			Select id_member,rt.transaction_date,paid_interest as amount
// 			FROM repayment_transaction as rt
// 			LEFT JOIN repayment_loans as rl on rl.id_repayment_transaction = rt.id_repayment_transaction
// 			WHERE rt.status <> 10 and rt.transaction_date >= ? and rt.transaction_date <= ? and rl.status <> 10 and rl.paid_interest > 0 AND rt.pay_on_id_loan =0
// 			-- UNION ALL
// 			/********************REPAYMENT PENTALTY AND SURCHARGES**************************************/
// 			-- Select id_member,rt.transaction_date,SUM(amount) as amount
// 			-- FROM repayment_transaction as rt
// 			-- LEFT JOIN repayment_loan_surcharges as rl on rl.id_repayment_transaction = rt.id_repayment_transaction
// 			-- WHERE rt.status <> 10 and rt.transaction_date >= ? and rt.transaction_date <= ?  and rl.amount > 0
// 			-- GROUP BY rt.id_member,MONTH(transaction_date),YEAR(transaction_date)
// 			-- UNION ALL
// 			/*******************DEDUCTED INTEREST FROM LOAN*********************************/
// 			-- SELECT id_member,date_released,lc.value FROM loan 
// 			-- LEFT JOIN loan_charges as lc on loan.id_loan = lc.id_loan
// 			-- where deduct_interest =1 and loan.status <> 4 and date_released >= ? AND date_released <= ? AND id_loan_fees=12

// 			/*************************CASH RECEIPT**********************************/
// 			UNION ALL
// 			SELECT cr.id_member,cr.date_received,crd.amount FROM cash_receipt as cr
// 			LEFT JOIN cash_receipt_details as crd on crd.id_cash_receipt = cr.id_cash_receipt
// 			LEFT JOIN tbl_payment_type as tpt on tpt.id_payment_type = crd.id_payment_type
// 			WHERE cr.type = 1 and cr.status <> 10 and cr.payee_type = 1 and tpt.id_chart_account = 35 and cr.date_received >= ? AND cr.date_received <= ?
// 			/*************************JOURNAL VOUCHER ENTRY (MANUAL)*******************************/
// 			UNION ALL
// 			SELECT jv.id_member,jv.date as transaction_date,(credit-debit) as amount FROM journal_voucher as jv
// 			LEFT JOIN journal_voucher_details as jvd on jvd.id_journal_voucher = jv.id_journal_voucher
// 			WHERE jv.date >= ? and jv.date <= ? and jvd.id_chart_account in (35) and jv.id_member > 0 and jv.status <> 10 and jv.type = 1
// 			/***************************CDV OTHERS*************************************************/
// 			UNION ALL
// 			SELECT cd.id_member,cd.date as transaction_date,(credit-debit) as amount FROM cash_disbursement as cd
// 			LEFT JOIN cash_disbursement_details as cdd on cdd.id_cash_disbursement = cd.id_cash_disbursement
// 			WHERE cd.date >= ? and cd.date <= ? AND cdd.id_chart_account in (35) AND cd.status <> 10
// 			UNION ALL
// 			/******************INTEREST BEGINNING*****************/
// 			SELECT id_member,date as transaction_date,amount FROM interest_beginning
// 			WHERE date >= ? AND date <= ?
// 			/***************************MEMBER FOR AUTOFILL*************************************/
// 			UNION ALL
// 			SELECT id_member,'2022-01-01' as date,0 as amount
// 			FROM member
// 			WHERE status = 1
// 			) as interest_table
// 		LEFT JOIN member as m on m.id_member = interest_table.id_member
// 		GROUP BY interest_table.id_member
// 		ORDER BY Names;";
// //  and cd.type=4
			
// 		$data['interest_table'] = DB::select($sql_query,[env('BEGINNING_DATE'),$dt_query_end,$dt_query_start,$dt_query_end,$dt_query_start,$dt_query_end,$dt_query_start,$dt_query_end,$dt_query_start,$dt_query_end]);


		$acc = config('variables.paid_interest_summary_account');
		$sql_query = "SELECT concat(m.last_name,if(m.suffix='','',concat(' ',m.suffix,' ')),', ',m.first_name,' ',if(m.middle_name <> '',UPPER(concat(LEFT(m.middle_name,1),'.')),'')) as Names,
		$month_query
		,SUM(amount) as Total
		FROM (
		/*******JV****/
		SELECT jv.id_member,jv.date as transaction_date,(credit-debit) as amount FROM journal_voucher as jv
		LEFT JOIN journal_voucher_details as jvd on jvd.id_journal_voucher = jv.id_journal_voucher
		WHERE jvd.id_chart_account in ($acc) and jv.status <> 10 and jv.date >= ? and jv.date <= ? AND jv.id_member >0
		UNION ALL
		/*******CRV****/
		SELECT jv.id_member,jv.date as transaction_date,(credit-debit) FROM cash_receipt_voucher as jv
		LEFT JOIN cash_receipt_voucher_details as jvd on jvd.id_cash_receipt_voucher = jv.id_cash_receipt_voucher
		WHERE jvd.id_chart_account in ($acc) and jv.status <> 10 and jv.date >= ? and jv.date <= ? AND jv.id_member >0
		UNION ALL
		/*******CDV****/
		SELECT jv.id_member,jv.date as transaction_date,(credit-debit) FROM cash_disbursement as jv
		LEFT JOIN cash_disbursement_details as jvd on jvd.id_cash_disbursement = jv.id_cash_disbursement
		WHERE  jvd.id_chart_account in ($acc) and jv.status <> 10 and jv.date >= ? and jv.date <= ? AND jv.id_member >0
		UNION ALL 
		/*******BEGINNING****/
		SELECT id_member,date as transaction_date,amount FROM interest_beginning
		WHERE date >= ? AND date <= ?
		UNION ALL
		/*******AUTO FILL MEMBER****/
		SELECT id_member,'2022-01-01' as date,0 as amount
		FROM member
		WHERE status = 1			
			) as interest_table
		LEFT JOIN member as m on m.id_member = interest_table.id_member
		GROUP BY interest_table.id_member
		ORDER BY Names;";


		$data['interest_table'] = DB::select($sql_query,[$dt_query_start,$dt_query_end,$dt_query_start,$dt_query_end,$dt_query_start,$dt_query_end,$dt_query_start,$dt_query_end]);

		// dd($data);
		return $data;
		return $data;

	}

	public function export(Request $request){
		date_default_timezone_set('Asia/Manila');
		$dt = new DateTime();
		$year = $request->year ?? $dt->format('Y');
		$m = ($year == $dt->format('Y'))?MySession::current_month():12;

		$data = $this->parseData(1,$m,$year);
		$html =  view('reports.paid_interest_export',$data);

        // return $html;
		$pdf = PDF::loadHtml($html);
		$pdf->setOption("encoding","UTF-8");
		$pdf->setOption('margin-bottom', '0.7480in');
		$pdf->setOption('margin-top', '0.7480in');
		$pdf->setOption('margin-right', '0.33in');
		$pdf->setOption('margin-left', '0.42in');
		$pdf->setOption('header-left', 'Page [page] of [toPage]');

		$pdf->setOption('header-font-size', 8);
		$pdf->setOption('header-font-name', 'Calibri');
		$pdf->setOrientation('landscape');



		return $pdf->stream();


		return view('reports.paid_interest_export',$data);

		return $data;
	}

	public function export_excel(Request $request){
		date_default_timezone_set('Asia/Manila');
		$dt = new DateTime();
		$year = $request->year ?? $dt->format('Y');
		$m = ($year == $dt->format('Y'))?MySession::current_month():12;

		$data = $this->parseData(1,$m,$year);

        $data['file_name'] = "Interest YEAR $year";
        return Excel::download(new InterestExport($data,(int)$m), $data['file_name'].".xlsx");

		return view('reports.paid_interest_excel_export',$data);
		dd($data);
	}
}
