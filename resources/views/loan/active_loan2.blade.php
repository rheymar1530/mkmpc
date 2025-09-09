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
		.tbl_repayment_summary  tr>td,.tbl_repayment_summary  tr>th{
			padding:3px;
			vertical-align:top;
			font-family: Calibri, sans-serif;
			font-size: 13px ;
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

		.tbl_repayment_summary tr{
			page-break-inside: avoid !important;
		}
	</style>
</head>

<body>
	<header>
		<div class="header" style="margin-top: 100px !important;">
			   <p style="font-size: 20px;margin-top: -15px;"><b>{{config('variables.coop_abbr')}}</b></p>
			    <p style="font-size: 16px;margin-top: -15px;"><b>Summary of Active Loan</b></p>
			    <p style="font-size: 16px;margin-top: -15px;"><b>{{$current_date}}</b></p>
				<p></p>
				<p></p>
			</div> 
		</header>
		<div class="row">
			<table width="100%" style=";border-collapse: collapse;border-top: 1px solid black;page-break-inside: always !important;" class="tbl_repayment_summary">
				<thead>
					<tr>
						<th width="1cm">No</th>
						<th style="width:5cm !important">Name of Borrower</th>
						<th style="width:6cm !important">Loan Service</th>
						<th>Principal</th>
						<th>Interest rate</th>
						<th>Terms</th>
						<th>Loan Amount</th>
						<th>Paid Principal</th>
						<th>Paid Interest</th>
						<th>Total Amount Paid</th>
						<th>Loan Balance</th>
						<th>Remarks</th>
						
					</tr>
				</thead>
				<tbody>
					<?php
						$no = 1;
					?>
					@foreach($loan_list as $borrower=>$items)
					<?php
						$total_loan_amount = 0;
						$total_amount_paid = 0;
						$total_loan_balance = 0;
						$total_pr_paid = 0;
						$total_in_paid = 0;
					?>
						@foreach($items as $c=>$item)
						<?php $row_span = count($items); ?>
						<tr>
							@if($c == 0)
							<td rowspan="{{$row_span}}">{{$no}}</td>
							<td rowspan="{{$row_span}}">{{$item->borrower_name}}</td>
							@endif
							<td>{{$item->loan_service_name}}</td>
							<td class="col_amount">{{number_format($item->principal_amount,2)}}</td>
							<td style="text-align: center">{{$item->interest_rate}}%</td>
							<td>{{$item->terms}} {{$item->term_desc}}</td>
							<td class="col_amount">{{number_format($item->loan_amount,2)}}</td>
							<td class="col_amount">{{number_format($item->total_pr_paid,2)}}</td>
							<td class="col_amount">{{number_format($item->total_in_paid,2)}}</td>
							<td class="col_amount">{{number_format($item->total_amount_paid,2)}}</td>
							<td class="col_amount">{{number_format($item->loan_balance,2)}}</td>
							<td></td>
							<?php
								$total_loan_amount += $item->loan_amount;
								$total_amount_paid += $item->total_amount_paid;
								$total_loan_balance += $item->loan_balance;
								$total_pr_paid += $item->total_pr_paid;
								$total_in_paid += $item->total_in_paid;
							?>
						</tr>
						@endforeach
						<tr>
							<th colspan="6" style="text-align:left">Total</th>


							
							<th class="col_amount">{{number_format($total_loan_amount,2)}}</th>
							<th class="col_amount">{{number_format($total_pr_paid,2)}}</th>
							<th class="col_amount">{{number_format($total_in_paid,2)}}</th>
							<th class="col_amount">{{number_format($total_amount_paid,2)}}</th>
							<th class="col_amount">{{number_format($total_loan_balance,2)}}</th>
							<th></th>
						</tr>
						<?php $no++;?>
					@endforeach

				</tbody>

			</table>
		</div>




	</body>
	</html>

