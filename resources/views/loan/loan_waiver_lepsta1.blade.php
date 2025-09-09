<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>TEST</title>

	<style type="text/css">
		@page {      
			margin-left: 0.8cm;
			margin-right: 0.8cm;
			margin-top: 0.5cm;
			margin-bottom: 0.5cm; 

			size: legal portrait; 
		}
		div.a {
			line-height: 50%;
		}

		* {
			box-sizing: border-box;
			font-family:"Calibri, sans-serif";
		}
		.head{
			font-size: 13px !important;
		}
		.head_others{
			font-size: 15px !important;
		}
		.fill{
			font-weight: bold;
		}
		.p_body{
			line-height: 17px;
			font-size: 13px;

		}
		.p_body3,.p_body2{
/*			line-height: 17px;
			font-size: 15px;*/
			line-height: 0.8rem;
		}
		.body_div{
			margin-top: -15px;
			white-space: normal;
			line-height: 18px;
			text-align: justify
		}
		.p_body:before,.p_body3:before { 
			content: "\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0 "; 
		}
		.columnLeft {         
			float: left;
			width: 60%;
/*			line-height: 10%; */
}

.columnRight {         
	float: right;
	width: 40%;
/*			line-height: 10%;*/
}

.row:after {
	line-height: 10%;
	content: "";
	display: table;
	clear: both;
	height: 1px; 
}
.font{
	font-size: 13px;
}
.fill_bottom{
	width: 300px;
	border-bottom: 1px solid;
	text-align: center;
	padding-bottom: 5px;
}
.fill_label{
	width: 300px;
	text-align: center;
}
.page_break { 
	page-break-before: always; 
}
.font-sd{
	font-size: 16px;
}
tr.foot_lbl  td{
/*			padding-top: -2cm !important;*/
font-size: 0.75rem;
text-align: center;
vertical-align: bottom; !important;
/*			background: red;*/
}
.text-right{
	text-align: right;
}

.pl{
	padding-left: 0.3cm;
}
.p-top{
	padding-top: 1cm !important;
}
.list_ts p{
	margin-bottom: 0rem;
	line-height: 0.8rem;

}
ol.list_ts > li{
	margin-top: -0.6rem !important;
	font-size: 0.8rem;
}
.text-center{
	text-align: center;	
}


table.bordered,.bordered td,.bordered th {
  border: 1px solid;
}

table.bordered {
  width: 100%;
  border-collapse: collapse;
}
</style>
</head>
<body>
	<div class="a" style="text-align:center;">
		<h3 style="font-size:18px" class="head"><span>{{config('variables.coop_name')}}</span></h3>
		<p style="margin-top:-5px" ><span class="head_others">{{config('variables.coop_district')}}</span></p>
		<p style="margin-top:-5px" ><span class="head_others">{{config('variables.coop_address')}}</span></p>
		<h3 style="font-size:18px" class="head"><span>LOAN APPLICATION FORM</span></h3>
		<br>
	</div>
	<div class="a body_div">
		<!-- ,<u class="fill">[Position]</u> -->
		<p class="p_body">
			I <u class="fill">{{$loan_details->name}}</u> of <u class="fill">{{config('variables.school_name')}}</u>	                                                 
			applying the <u class="fill">{{$loan_details->loan_service}}</u>   Loan in the     {{config('variables.coop_abbr')}}  in   the amount of <u class="fill">{{$principal_worded}}</u> <u class="fill">(<span style="font-family: DejaVu Sans; sans-serif;">&#8369;</span>{{$loan_details->principal_amount}})</u> payable within <u class="fill">{{$loan_details->duration}}</u> with a monthly amortization of  <u class="fill"><span style="font-family: DejaVu Sans; sans-serif;">&#8369;</span>{{$loan_details->total_due}}</u> for the purpose of <u class="fill">{{$loan_details->loan_remarks}}</u>.
		</p>
	</div>

	<h3 style="font-size:18px;text-align:center" class="head"><span style=";">PROMISSORY NOTE</span></h3>
	<br>
	<div class="a body_div" style="font-size:0.75rem !important">
		<p class="p_body" style="margin-top: 0cm;margin-bottom: 0cm !important;">
			For the value received,I <u class="fill">{{$loan_details->name}}</u> hereby promise to pay the LEPSTA 1 Credit Cooperative through its Treasurer every month and thereafter until this loan, including interests and other charges shall have been paid.
		</p>
		<p class="p_body" style="margin-top: -0.1cm;margin-bottom: 0cm !important;">
			With my accounts to the Leon Public School Teachers Association Credit Cooperative (LEPSTA 1-CC),I hereby authorized the District Supervisor/ CreCom and Disbursing Officer/School Head of my school to deduct from my monthly salary the amount due to me or remit my monthly dues to the Leon I Public School Teachers Association Credit Cooperative (LEPSTA 1-CC).
		</p>
		<p class="p_body" style="margin-top: -0.1cm;margin-bottom: 0cm !important;">
			I hereby agree, that <b><i>I will deposit my Automated Teller Machine (ATM) card to the cooperative representative and give full authority to withdraw the amount equivalent to my monthly amortization and other charges with the LEPSTA 1 Credit Cooperative</i></b>.
		</p>
		<p class="p_body" style="margin-top: -0.1cm;margin-bottom: 0cm !important;">
			I hereby agree also that in case of default in payments the entire amount of my loan shall become immediately due and payable at the option of the cooperative. Each party to this loan application and waiver whether a loaner/member or a co-maker severally waives presentation of payment, demand and notice of protest and dishonor at the same.
		</p>
		<p class="p_body" style="margin-top:  -0.1cm;margin-bottom: 0cm !important;">
			In case of the above mentioned cases, I hereby assign in favor of LEPSTA 1 Credit Cooperative without further, so much of my capital deposits including earned dividends with the LEPSTA 1 Credit Cooperative that would be sufficient to pay off the entire outstanding balance of his loan, including stipulated interests and other charges.
		</p>
		<p class="p_body" style="margin-top: -0.1cm;margin-bottom: 0cm !important;">
			I further agree by party hereto that incase payment shall be made at maturity by maker/loaner, the co-maker shall pay the total loan amount due by the maker/loaner and promise to pay a fine in accordance with the terms of the By-Laws of LEPSTA 1 Credit Cooperative.
		</p>

	</div>
	<div class="row" style="margin-top:0.8cm">
		<table width="100%" style="border-spacing: 10px 0px 10px 0px;border-collapse: separate;font-size: 0.7rem;">
			<tr>
				<td class="text_ex" style="width: 40%;border-bottom: 1px solid;text-align: center;">{{$loan_details->name}}</td>
				<td class="text_ex" style="width: 20%;border-bottom: 1px solid;margin-right: 0.5cm !important;"></td>
				<td class="text_ex" style="width: 40%;border-bottom: 1px solid;"></td>
			</tr>
			<tr class="foot_lbl">
				<td class="text_ex" style="width: 40%;">Name & Signature of Maker</td>
				<td class="text_ex" style="width: 20%;">Date</td>
				<td class="text_ex" style="width: 40%;">Contact Number/FB Account</td>
			</tr>
			<tr>
				<td colspan="3"></td>
			</tr>
			<tr style="">
				<td class="text_ex" colspan="2" style="border-bottom: 1px solid;text-align: center;">

					<td class="text_ex p-top" style="width: 40%;"></td>
				</tr>

				<tr class="foot_lbl">
					<td class="text_ex" colspan="2">Beneficiary and Address</td>

					<td class="text_ex" style="width: 30%;"></td>
				</tr>
			</table>

			<table width="100%" style="border-spacing: 10px 0px 10px 0px;border-collapse: separate;font-size: 0.7rem;">
				@foreach($comakers as $cm)
				<tr>
					<td class="text_ex p-top" style="width: 50%;border-bottom: 1px solid;text-align: center;">{{$cm->comaker_name}}</td>
					<td class="text_ex p-top" style="width: 50%;border-bottom: 1px solid;">{{$cm->address}}</td>
				</tr>
				<tr class="foot_lbl">
					<td class="text_ex" style="width: 50%;">Name & Signature of Co-Maker</td>
					<td class="text_ex" style="width: 50%;">Address</td>
				</tr>
				<tr>
					<td colspan="2"></td>
				</tr>
				@endforeach
				<tr>
					<td class="text_ex p-top" style="width: 50%;border-bottom: 1px solid;text-align: center;"></td>
					<td class="text_ex p-top" style="width: 50%;"></td>
				</tr>
				<tr class="foot_lbl">
					<td class="text_ex" style="width: 50%;">Name and Signature of DS/SH</td>
					<td class="text_ex" style="width: 50%;"></td>
				</tr>
			</table>
		</div>
		<div class="row" style="margin-top:0.1cm;border: 1px solid;padding:0.7rem">
			<div class="a">
				<h3  class="head"><center style="font-size:14px !important">AUTHORIZATION FOR SALARY DEDUCTION</center></h3>
				<br>
			</div>
			<div class="a body_div">
				<p class="p_body3" style="font-size:0.75rem">
					I hereby authorized the {{config('variables.coop_name')}} ({{config('variables.coop_abbr')}}) to deduct from my check/ATM salary the amount equaled to my monthly amortizations, including interests and other charges and every month thereafter until this loan have been paid.
				</p>
			</div>
			<table width="100%" style="border-spacing: 10px 0px 10px 0px;border-collapse: separate;font-size: 0.7rem;">
				<tr>
					<td style="width:50%">&nbsp;</td>
					<td class="text_ex" style="width: 50%;border-bottom: 1px solid;text-align: center;">{{$loan_details->name}}</td>
				</tr>
				<tr class="foot_lbl">
					<td class="text_ex" style="width: 50%;"></td>
					<td class="text_ex" style="width: 50%;">Name and Signature of Maker</td>
				</tr>
			</table>
			<div class="a" style="margin-top:0.5cm">
				<h3  class="head"><center style="font-size:12px !important">SIGNED IN THE PRESENCE OF: </center></h3>
				<br>
			</div>
			<table width="100%" style="border-spacing: 10px 0px 10px 0px;border-collapse: separate;font-size: 0.7rem;">
				<tr>
					<td class="text_ex p-top" style="width: 50%;border-bottom: 1px solid;"></td>
					<td class="text_ex p-top" style="width: 50%;border-bottom: 1px solid;"></td>
				</tr>
				<tr class="foot_lbl">
					<td class="text_ex" style="width: 50%;">Witness</td>
					<td class="text_ex" style="width: 50%;">Witness</td>
				</tr>
			</table>
			<div class="a body_div" style="font-size: 0.75rem !important;margin-top: 0.5cm;">
				<p class="p_body3">
					<b>SUBSCRIBE AND SWORN to before me this</b> <u class="fill">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </u> day of <u class="fill">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u> &nbsp;Iloilo City, affiant exhibited to me his/her Community Tax Cert. No. <u class="fill">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>&nbsp; issued in <u class="fill">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>&nbsp; on <u class="fill">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>.
				</p>
			</div>
			<div class="a body_div" style="font-size: 0.75rem !important;">
				<p class="p_body2">Doc. No. :__________________</p>
				<p class="p_body2" style="margin-top:-10px">Page No.:__________________</p>
				<p class="p_body2" style="margin-top:-10px">Book No.:__________________</p>
				<p class="p_body2" style="margin-top:-10px">Series of 20 ________________</p>
			</div>
		</div>
		<div class="page_break"></div>

		<div class="row" style="border: 1px solid;padding:0.75rem;">
			<div class="row" style="margin-top:-0.4cm;">
				<div class="a">
					<h5  class="text-center"><i>To be filled up by the LEPSTA 1 CC</i></h5>
				</div>
				<div class="columnLeft" style="">
					<table style="font-size: 0.7rem;white-space: normal;border-right: 0.05rem solid" width="100%">
						<tr>
							<th width="40%">Loan Type</th>
							<th>Prev Loan</th>
							<th>Balance</th>
							<th>Mo. Dues</th>
						</tr>
						<?php
						$monthly_due = 0;
						$cbu = 300;
						$swiping_fee = 20;
						?>
						@foreach($active_loan as $act)
						<tr>
							<td style="">ID {{$act->id_loan}} {{$act->loan}}</td>
							<td class="text-right">{{number_format($act->principal_amount,2)}}</td>
							<td class="text-right">{{number_format($act->balance,2)}}</td>
							<td class="text-right">{{number_format($act->amortization,2)}}</td>
						</tr>
						<?php
						$monthly_due += $act->amortization;
						?>
						@endforeach
						<tr>
							<td>Swiping Fee</td>
							<td colspan="3" class="text-right">20.00</td>
						</tr>
						<tr>
							<td colspan="2">Monthly CBU Contribution</td>
							<td colspan="2" class="text-right">300.00</td>
						</tr>
						<tr>
							<td colspan="2">Total Monthly Dues</td>
							<td colspan="2" class="text-right">{{number_format(($monthly_due+$swiping_fee+$cbu),2)}}</td>
						</tr>

						<tr>
							<td colspan="2">Net Pay/Payslip</td>
							<td colspan="2" class="text-right">{{number_format($net_pay,2)}}</td>
						</tr>

					</table>
				</div>
				<div class="columnRight" style="border-left: 0.05rem solid;">
					<table style="font-size: 0.7rem;white-space: normal;margin-top: 0.5cm;margin-left: 0.1cm;" width="100%">
						<tr>
							<td colspan="2"><b>LOAN TYPE :</b> {{$loan_details->loan_service}}</td>
						</tr>
						<tr>
							<td colspan="2"><b>AMOUNT :</b> {{$loan_details->principal_amount}}</td>
						</tr>
						<tr>
							<td colspan="2"></td>
						
						</tr>
						<tr>
							<td colspan="2"><b>MONTHLY DUE/S:</b> {{$loan_details->total_due}}</td>
						</tr>

						<tr>
							<td colspan="2"><b>DEDUCTIONS: </b></td>
						</tr>
						@foreach($loan['DEDUCTED_CHARGES'] as $charges)
						<!-- DEDUCTIONS LIST -->
						<tr>
							<td class="pl"><b>{{$charges['charge_complete_details']}}: </b></td>
							<td class="text-right">{{number_format($charges['calculated_charge'],2)}}</td>

						</tr>	
						@endforeach

						@if($loan['TOTAL_LOAN_BALANCE'] > 0)
						<tr>
							<td class="pl"><b>Current: </b></td>
							<td class="text-right">{{number_format($loan['TOTAL_LOAN_BALANCE'],2)}}</td>
						</tr>
						@endif

						@if($loan['TOTAL_LOAN_OFFSET'] > 0)
						<tr>
							<td class="pl"><b>Prev Loan Payment: </b></td>
							<td class="text-right">{{number_format($loan['TOTAL_LOAN_OFFSET']-$loan['TOTAL_LOAN_OFFSET_REBATES'],2)}}</td>
						</tr>
						@endif

						<tr>
							<td class="pl"><b>TOTAL DEDUCTIONS: </b></td>
							<td class="text-right">{{number_format($loan['TOTAL_DEDUCTED_CHARGES'],2)}}</td>
						</tr>
						<tr>
							<td class="pl"><b>NET PROCEEDS: </b></td>
							<td class="text-right">{{number_format($loan['TOTAL_LOAN_PROCEED'],2)}}</td>
						</tr>

					</table>
				</div>
				<div>
				</div>
			</div>

			<div class="row">
				<h5>CREDIT COMMITTEE ACTION</h5>
				<table width="100%" style="font-size: 0.75rem;white-space: normal;margin-top: -0.5cm;">
					<tr>
						<td>(&nbsp;&nbsp;&nbsp;) Approved</td>
						<td>(&nbsp;&nbsp;&nbsp;) Disapproved</td>
						<td style="text-align: right;">Reasons:</td>
						<td style="border-bottom: 1px solid;" width="50%"></td>
					</tr>
				</table>
				<table width="100%" style="border-spacing: 10px 0px 10px 0px;border-collapse: separate;font-size: 0.7rem;">
				<tr>
					<td class="text_ex p-top text-center" style="width: 33.33%;border-bottom: 1px solid;">ARLENE JOY C. CABARLES</td>
					<td class="text_ex p-top text-center" style="width: 33.33%;border-bottom: 1px solid;">MILDRED V. CAIGOY</td>
					<td class="text_ex p-top text-center" style="width: 33.33%;border-bottom: 1px solid;">ANA MAE C. TAN</td>
				</tr>
				<tr class="foot_lbl">
					<td class="text_ex" style="width: 33.33%;">Credit Committee - Secretary</td>
					<td class="text_ex" style="width: 33.33%;">Credit Committee Co-Chair</td>
					<td class="text_ex" style="width: 33.33%;">Credit Committee Chair</td>
				</tr>
				<tr>
					<td class="text_ex" colspan="3" style="padding-top:0.5cm;padding-bottom: 0.5cm;"><b>APPROVED:</b></td>
				</tr>
				<tr class="foot_lbl">
					<td class="text_ex" style="border-bottom: 1px solid">LIZA C. CALA-OR</td>
					<td class="text_ex" colspan="2"></td>
				</tr>
				<tr class="foot_lbl">
					<td class="text_ex" style="width: 33.33%;"><b>LEPSTA 1- CC, General Manager</b></td>
					<td class="text_ex" colspan="2"></td>
					
				</tr>
			</table>
			</div>
			</div>
			<div class="row">
				<h5 class="text-center"><i>POLICY GUIDELINES ON THE AVAILMENT OF LOANS:</i></h5>
				<h6 style="margin-top:-0.3cm">CRITERIA FOR LOAN APPROVAL</h6>
									
				<ol class="list_ts" style="margin-top: -0.5cm;">
					<li><p>Applicant must be in good standing. However, applicants who are NEW MEMBERS can avail of all loans provided he/she meets the minimum paid up share capital of P 24,000.00</p></li>

					<li><p>Applicant must have a net take home pay of at least Five Thousand Pese (P 5,000.00) based on his/her attached recent payslip.</p></li>

					<li><p>Applicant for loan must have contributed at least Twenty Five percent (25%) of the gross loanable amount. If the contribution/share capital is less than 25%, the balance will be deducted from the proceeds of the loan to cover the minimum requirement.</p></li>

					<li><p>Applicant must have no pending criminal/administrative case.</p></li>

					<li><p>The loan may be RENEWED upon payment of Six (6) months of payment for loans with terms of Sixty (60) months, and at least Three (3) months of payments for loan with terms of Twelve (12), Eighteen (18), Twenty Four (24) and Forty-Eight (48) months and is subjected to the provisions of the By:aws of the LEPSTA 1-CC</p></li>

					<li><p>The loan may be transferred to another term upon payments of at least Four (4) months.</p></li>

					<li><p>
						The maximum age requirement for availment/renewal of consol loan shall be Fifty Six (56) years old and above provided the amount to be granted would be CBU based.

					</p></li>
				</ol>
						
			</div>
			<div class="row">
				<h6 style="margin-top:-0.3cm">INTEREST RATE:</h6>
				<ol class="list_ts" style="margin-top: -0.7cm;">
					<li><p>Applicant may choose any of the following terms of payment and the corresponding interest rate, to wit:</p></li>
				</ol>
				<table class="bordered" style="font-size:0.75rem;margin-left: 1cm;width: 17cm !important;">
					<thead>
						<tr>
							<th>TERMS OF PAYMENT</th>
							<th>INTEREST RATE PER MONTH</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="pl">Tukod Loan</td>
							<td class="text-center">3%</td>	
						</tr>
						<tr>
							<td class="pl">Kapit Bisig 12 months</td>
							<td class="text-center">1.8%</td>	
						</tr>
						<tr>
							<td class="pl">Kapit Bisig 18 months</td>
							<td class="text-center">1.8%</td>	
						</tr>
						<tr>
							<td class="pl">Kapit Bisig 24 months</td>
							<td class="text-center">1.8%</td>	
						</tr>
						<tr>
							<td class="pl">Kapit Bisig 48 months</td>
							<td class="text-center">1.8%</td>	
						</tr>
						<tr>
							<td class="pl">Consolidated</td>
							<td class="text-center">1.4%</td>	
						</tr>
					</tbody>
				</table>
			</div>
			<div class="row">
				<h6 style="margin-top:0.5cm">LOAN CHARGES:</h6>
				<ol class="list_ts" style="margin-top: -0.7cm;">
					<li><p>Service Fee - 1%</p></li>
					<li><p>Capital Build Up - 5%; Renewal (Consol Loan - 2.5%)</p></li>
					<li><p>Notarial Fee - P 100.00 for Loan of Php 50,000.00 and above only.</p></li>
					<li><p>Loan Protection: A member will pay his/her premiums based on the amount loaned multiplied by 0.01250 for 12 months, .01880 for 18 months, .0250 for 24 months and .0300 for 48 and 60 months to pay loans. </p></li>
				</ol>
			</div>
		</div>

	</body>
	</html>