<!DOCTYPE html>
<html lang="en">   
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Investment Withdrawal Request Batch</title>
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
			    <p style="font-size: 16px;margin-top: -15px;"><b>Investment Withdrawal Summary</b></p>
				<p style="font-size: 16px;margin-top: -15px;"><b>Batch No.{{$details->id_investment_withdrawal_batch}} ({{$details->date_released}})</b></p>
				<p></p>
			</div> 
		</header>
		
		<div class="row">
			<table width="100%" style=";border-collapse: collapse;border-top: 1px solid black;page-break-inside: always !important;" class="tbl_request">
				<thead>
					<tr>
						<th>Investor</th>
						<th>Investment</th>
						<th>CDV #</th>
<!-- 						<th>Principal</th>
						<th>Interest</th> -->
						<th>Amount</th>
						<th style="width:4.5cm">Signature</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$gt_total = 0;
						$gp_total =0;
						$gi_total=0;						
					?>
					@foreach($withdrawals as $investor=>$rows)
						<?php
							$p_total =0;
							$i_total=0;
							$t_total=0;
						?>
						@foreach($rows as $c=>$row)
						<tr>
							@if($c == 0)
							<td rowspan="{{count($rows)}}">{{$row->investor}}</td>
							@endif
							<td>ID#{{$row->id_investment}} {{$row->product_name}}</td>
							<td class="text-center">{{$row->id_cash_disbursement}}</td>
<!-- 							<td class="col_amount">{{number_format($row->principal,2)}}</td>
							<td class="col_amount">{{number_format($row->interest,2)}}</td> -->
							<td class="col_amount">{{number_format($row->total_amount,2)}}</td>
							
							<td style="height: 1cm !important;"></td>
							
							<?php
								$p_total+=$row->principal;
								$i_total+=$row->interest;
								$t_total+=$row->total_amount;		
							?>
						</tr>
						@endforeach
<!-- 						<tr>
							<td colspan="2"><b>Subtotal</b></td>
							<td class="col_amount">{{number_format($p_total,2)}}</td>
							<td class="col_amount">{{number_format($i_total,2)}}</td>
							<td class="col_amount">{{number_format($t_total,2)}}</td>
							
						</tr> -->
						<?php
							$gt_total +=$t_total;
							$gp_total +=$p_total;
							$gi_total+=$i_total;	
						?>
					@endforeach
					
				</tbody>
						<tr>
							<th colspan="3" style="text-align:left">Grand Total</th>
							<!-- <th class="col_amount">{{number_format($gp_total,2)}}</th>
							<th class="col_amount">{{number_format($gi_total,2)}}</th> -->
							<th class="col_amount">{{number_format($gt_total,2)}}</th>
							<th></th>

						</tr>	
			</table>
		</div>




	</body>
	</html>

