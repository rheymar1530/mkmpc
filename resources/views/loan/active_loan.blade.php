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
					<th style="width:5cm !important"> {{ ($type==1)?'Name of Borrower':'Loan Service' }} </th>


					@if($type == 2)
					<th>Loan ID</th>
					@endif
					<th style="width:6cm !important">{{ ($type==1)?'Loan Service':'Name of Borrower' }} </th>
					@if($type == 2)
					<th>Terms</th>
					@endif
					<th>Date Granted</th>
					<th>Interest rate</th>
					<th>Principal Amort</th>
					<th>Interest Amort</th>
					<th>Scheduled Amort</th>
					<th>Principal</th>
					<th>Total Paid Principal</th>
					<th>Total Interest Paid</th>
					<th>Principal Balance</th>
					<th>Unpaid Interest</th>
					<th>Penalties & Surcharges</th>
					<th>Total Loan Balance</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$no = 1;
					$grand_total_balance = 0;


					$g_total = array();
					$sum_key = ['principal_amt','interest_amt','schedulued_am','principal_amount','total_pr_paid','total_in_paid','principal_balance','unpaid_interest','penalties'];

					foreach($sum_key as $kk){
						$g_total[$kk] = 0;
 					}
				?>
				@foreach($loan_list as $borrower=>$items)
				<?php
					$schedulued_am = 0;
					$principal_amt = 0;
					$interest_amt = 0;
					$principal_amount = 0;
					$total_pr_paid = 0;
					$total_in_paid = 0;
					$principal_balance = 0;
					$unpaid_interest = 0;
					$penalties = 0;
					$total_receivables = 0;
				?>
					@foreach($items as $c=>$item)
					<?php $row_span = count($items); ?>
					<tr>
						@if($c == 0)
						<td rowspan="{{$row_span}}">{{$no}}</td>
						<td rowspan="{{$row_span}}">{{$item->{$group_key} }}</td>
						@endif

						@if($type == 2)
						<td style="text-align:center">{{$item->id_loan}}</td>
						@endif

						<?php
							$key_2 = ($type == 1)?"loan_service_name":"borrower_name";
						?>
						<td>{{$item->{$key_2} }}</td>
						@if($type == 2)
						<td style="text-align:center">{{$item->terms}}</td>
						@endif
						<td>{{$item->date_granted}}</td>
						<td style="text-align: center">{{$item->interest_rate}}%</td>
						<td class="col_amount">{{number_format($item->principal_amt,2)}}</td>
						<td class="col_amount">{{number_format($item->interest_amt,2)}}</td>


						
						<td class="col_amount">{{number_format($item->schedulued_am,2)}}</td>
						<td class="col_amount">{{number_format($item->principal_amount,2)}}</td>
						<td class="col_amount">{{number_format($item->total_pr_paid,2)}}</td>
						<td class="col_amount">{{number_format($item->total_in_paid,2)}}</td>
						<td class="col_amount">{{number_format($item->principal_balance,2)}}</td>
						<td class="col_amount">{{number_format($item->unpaid_interest,2)}}</td>
						<td class="col_amount">{{number_format($item->penalties,2)}}</td>
						<td class="col_amount">{{number_format($item->total_receivables,2)}}</td>
						<?php
							// $schedulued_am += $item->schedulued_am;;
							$principal_amt += $item->principal_amt;
							$interest_amt += $item->interest_amt;
							$schedulued_am += $item->schedulued_am;
							$principal_amount += $item->principal_amount;
							$total_pr_paid += $item->total_pr_paid;
							$total_in_paid += $item->total_in_paid;
							$principal_balance += $item->principal_balance;
							$unpaid_interest += $item->unpaid_interest;
							$penalties += $item->penalties;
							$total_receivables += $item->total_receivables;

							$grand_total_balance += $item->total_receivables;
						?>
					</tr>
					@endforeach

					<?php
						foreach($sum_key as $s_key){
							$g_total[$s_key] += ${$s_key};
						}
					?>
					<tr>
						<th colspan="{{ 5 + (($type==1)?0:2) }}" style="text-align:left">Total 
							@if($type == 2)({{$item->{$group_key} }}) @endif</th>
						<th class="col_amount">{{number_format($principal_amt,2)}}</th>
						<th class="col_amount">{{number_format($interest_amt,2)}}</th>
						<th class="col_amount">{{number_format($schedulued_am,2)}}</th>
						<th class="col_amount">{{number_format($principal_amount,2)}}</th>
						<th class="col_amount">{{number_format($total_pr_paid,2)}}</th>
						<th class="col_amount">{{number_format($total_in_paid,2)}}</th>
						<th class="col_amount">{{number_format($principal_balance,2)}}</th>
						<th class="col_amount">{{number_format($unpaid_interest,2)}}</th>
						<th class="col_amount">{{number_format($penalties,2)}}</th>
						<th class="col_amount">{{number_format($total_receivables,2)}}</th>
					</tr>						
					<?php $no++;?>
				@endforeach
				<tr style="">
					<th colspan="{{ 5 + (($type==1)?0:2) }}" style="text-align:left;font-size:20px !important">Grand Total</th>

					@foreach($sum_key as $key)
					<th class="col_amount" style="font-size:15px !important">{{number_format($g_total[$key],2)}}</th>
					@endforeach
					<th class="col_amount" style="font-size:20px !important">{{number_format($grand_total_balance,2)}}</th>
				</tr>
			</tbody>

		</table>
	</div>




	</body>
	</html>

