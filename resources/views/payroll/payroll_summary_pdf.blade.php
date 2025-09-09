<!DOCTYPE html>
<html lang="en">   
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>{{$file_name}}</title>
	<style>

		@page {      

		}
		div.header{
			text-align: center;
			/*line-height: 1px;*/
			font-size: 10px;
			font-family: Arial, Helvetica, sans-serif;
		}

		* {
			box-sizing: border-box;
		}
		.columnLeft {         
			float: left;
			width: 50%;
		}
		.columnRight {         
			float: right;
			width: 50%;
		}
		.tbl_payroll_summary  tr>td,.tbl_payroll_summary  tr>th{
			padding:3px;
			vertical-align:top;
			font-family: Arial, Helvetica, sans-serif;
			font-size: 12px ;
		}
		.bold_lbl{
			font-weight: bold;
		}
		.details_tbl_lbl{
			text-align: right !important;
			width: 40% !important;
		}	
		.pd_left_10{
			/*padding-left: 10px !important;*/
		}
		.row:after {
			line-height: 10%;
			content: "";
			display: table;
			clear: both;
			height: 1px; 
		}

		.col_amount, .col_number{
			text-align: right;
			margin-right: 5px !important;
		}

		table, td, th {
			border: 1px solid;
		}

		table {
			width: 100%;
			border-collapse: collapse;
		}
		.highlight_amount{
			font-weight: bold;
			text-decoration: underline;
			font-size: 12px !important;
		}
		.bold-text{
			font-weight: bold;
		}


	</style>
</head>

<body>
		<div class="header">
			<p style="font-size: 18px;margin-top: 100px !important;"><b>{{config('variables.coop_abbr')}} </b></p>
				<p style="font-size: 18px;margin-top: -15px;"><b>PAYROLL SUMMARY ({{$payroll[0]->payroll_type}})</b></p>
				<p style="font-size: 14px;margin-top: -12px;"><b>{{$payroll[0]->period}}</b></p>
						<p></p>
						<p></p>
					</div> 		

				<div class="row">
					<table width="100%" style="border-collapse: collapse;border-top: 1px solid black;font-size:18px" class="tbl_payroll_summary">
						<thead>

							<tr>
								<th>EMP NO</th>
								<th>EMPLOYEE NAME</th>
								<th>BANK</th>
								<th>ACCOUNT NO</th>
								<th>BRANCH</th>
								<th>DEPT</th>
								<th>STATUS</th>
								<th>POSITION</th>
								<th>BASIC</th>
								<!-- <th>COLA</th> -->
								<th>OT</th>
								<!-- <th>Night Shift Dif</th> -->
								<th>ALLOWANCES</th>
								<th>13TH MONTH</th>
								<th>INCENTIVE</th>
								<th>PAID LEAVES</th>
								<!-- <th>HOLIDAY PAY</th> -->
								<th>OTHERS</th>
								<th>ADJUSTMENT</th>
								<th>GROSS PAY </th>
								<th>ABSENCES</th>
								<th>LATE</th>
								<th>SSS</th>
								<th>PAG-IBIG</th>
								<th>PHILHEALTH</th>
								<th>W/HOLDING TAX</th>
								<th>INSURANCE</th>
								<th>SSS LOAN</th>
								<th>PAG-IBIG LOAN</th>
								<th>CASH ADVANCES</th>

								<th>TOTAL DEDUCTIONS</th>
								<th>NET PAY</th>
								<th>REMARKS</th>
							</tr>
						</thead>
						<tbody>
							<?php
							//,"cola"","night_shift_dif","holiday"
								$keys = ["basic_pay","ot","total_allowance","thir_month","incentives","paid_leaves","others","salary_adjustment","gross_income","absences","late","sss","hdmf","philhealth","wt","insurance","sss_loan","hdmf_loan","ca","total_deduction","net_income"];
									$totals = array();
									foreach($keys as $k){
										$totals[$k] = 0;
									}
							?>
							@foreach($payroll as $p)
							<tr>
								<td style="text-align: center;">{{$p->id_employee}}</td>
								<td style="white-space:nowrap">{{$p->name}}</td>
								<td></td>
								<td></td>
								<td>{{$p->branch_name}}</td>
								<td>{{$p->department}}</td>
								<td>{{$p->emp_status}}</td>
								<td>{{$p->position}}</td>
								@foreach($keys as $key)
								<td style="text-align:right;">{{ number_format($p->{$key},2)}}</td>
								<?php
									$totals[$key] += $p->{$key};
								?>
								@endforeach
								<td>{{$p->remarks}}</td>


							</tr>
							@endforeach

						</tbody>
						<tr >
							<th colspan="8"  style="text-align: left;font-size:14px !important">Grand Total</th>
							@foreach($keys as $k)
								<th  class="col_amount" style="font-size:14px !important">{{number_format($totals[$k],2)}}</th>
							@endforeach
							<td></td>
						</tr>
					</table>
				</div>




			</body>
			</html>

