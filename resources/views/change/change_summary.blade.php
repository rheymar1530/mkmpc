<!DOCTYPE html>
<html lang="en">   
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<style>

		@page {      
			margin-left: 1cm;
			margin-right: 1cm;
			margin-top:2cm;
			size : letter landscape;
		}
		div.header{
			text-align: center;
			/*line-height: 1px;*/
			font-size: 16px;
			font-family: Calibri, sans-serif;
		}

		* {
			font-family: Calibri, sans-serif;
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
		.tbl_change  tr>td,.tbl_change  tr>th{
			padding:3px;
			vertical-align:top;
			font-family: Calibri, sans-serif;
			font-size: 14px ;
			page-break-inside: avoid !important;
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

		.tbl_change tr{
			page-break-inside: avoid !important;
		}
	</style>
</head>

<body>
	<header>
		<div class="header" style="margin-top: 100px !important;">
			    <p style="font-size: 20px;margin-top: -15px;"><b>{{config('variables.coop_abbr')}}</b></p>
			    <p style="font-size: 16px;margin-top: -15px;"><b>Change Payables</b></p>
				<p></p>
				<p></p>
			</div> 
		</header>
		<div class="row">
			<table width="100%" style=";border-collapse: collapse;border-top: 1px solid black;page-break-inside: always !important;" class="tbl_change">
				<thead>
					<tr>
						<th width="1cm">No</th>
						<th>Transaction Date</th>
						<th>Loan Payment ID</th>
						<th>Payee</th>
						<th>Change Amount</th>
						<th>Change Released Amount</th>
						<th>Date Released</th>
						<th>Signature</th>
						<th>Remarks</th>
						
					</tr>
				</thead>
				<tbody>
					<?php
						$total_pay_change = 0;
					?>
					@foreach($changes as $count=>$c)
					<tr>
						<td style="text-align:center;">{{$count+1}}</td>
						<td style="width:2.5cm">{{$c->transaction_date}}</td>
						<td style="width:2cm;text-align: center;">{{$c->id_repayment_transaction}}</td>
						<td style="width:5.5cm">{{$c->borrower}}</td>
						<td style="width:2.5cm" class="col_amount">{{number_format($c->change,2)}}</td>
						<td style="width:3cm"></td>
						<td style="width:2.5cm"></td>
						<td></td>
						<td></td>
					</tr>
					<?php
						$total_pay_change += $c->change;
					?>
					@endforeach

						<tr>
							<th colspan="4" style="text-align:left">Grand Total</th>
							<th class="col_amount">{{number_format($total_pay_change,2)}}</th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
						</tr>	
				</tbody>

			</table>
		</div>




	</body>
	</html>

