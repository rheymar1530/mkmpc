<!DOCTYPE html>
<html lang="en">   
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Prime Withdrawal Request Batch</title>
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
		.tbl_request  tr>td,.tbl_request  tr>th{
			padding:3px;
			vertical-align:top;
			font-family: Calibri, sans-serif;
			font-size: 14px ;
			page-break-inside: avoid !important;
		}
		.tbl_request  tr>td{
			vertical-align: middle;
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

		.tbl_request tr{
			page-break-inside: avoid !important;
		}
		.nmb{
			margin-bottom: 5px !important;
		}
		.text-center{
			text-align: center !important;
		}
	</style>
</head>

<body>
	<header>
		<div class="header" style="margin-top: 100px !important;">
			    <p style="font-size: 20px;margin-top: -15px;"><b>{{config('variables.coop_abbr')}}</b></p>
			    <p style="font-size: 16px;margin-top: -15px;"><b>Prime Withdrawal Request</b></p>
				<p style="font-size: 16px;margin-top: -15px;"><b>Batch No.{{$details->id_prime_withdrawal_batch}}</b></p>
				<p></p>
			</div> 
		</header>
		<p class="nmb"><b>Date released:</b> {{$details->date_released}} &nbsp;&nbsp; <b>Reason:</b> {{$details->reason}}</p>
		<div class="row">
			<table width="100%" style=";border-collapse: collapse;border-top: 1px solid black;page-break-inside: always !important;" class="tbl_request">
				<thead>
					<tr>
						<th width="1cm">No</th>
						<th style="width:2.5cm">Request ID</th>
						<th>Released to</th>
						<th>CDV#</th>
						<th>Amount</th>
						<th style="width:8cm !important">Signature</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$total = 0;
					?>
					@foreach($prime_withdrawals as $c=>$pw)
					<tr>
						<td class="text-center">{{($c+1)}}</td>
						<td class="text-center">{{$pw->id_prime_withdrawal}}</td>
						<td>{{$pw->member}}</td>
						<td class="text-center">{{$pw->id_cash_disbursement}}</td>
						<td class="col_amount">{{number_format($pw->amount,2)}}</td>
						<td style="height:0.9cm"></td>
						<?php 
							$total += $pw->amount;
						?>
					</tr>
					@endforeach
				</tbody>
						<tr>
							<th colspan="4" style="text-align:left">Grand Total</th>
							<th class="col_amount">{{number_format($total,2)}}</th>
							<th></th>

						</tr>	
			</table>
		</div>




	</body>
	</html>

