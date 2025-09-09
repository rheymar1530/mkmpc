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
			padding:2mm;
			vertical-align:top;
/*			font-family: Calibri, sans-serif;*/
/*			font-size: 14px ;*/
font-size: 4mm !important;
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
.text-center{
	text-align: center !important;
}

</style>
</head>
<?php
$total_swiped = 0;
$total_transaction_charge = 0;
$total_change_payable = 0;
?>
<body>
	<header>
		<div class="header" style="margin-top: 100px !important;">
			<p style="font-size: 20px;margin-top: -15px;"><b>{{config('variables.coop_abbr')}}</b></p>
			<p style="font-size: 16px;margin-top: -15px;"><b>Summary of ATM Swipe Transaction</b></p>
			<p style="font-size: 16px;margin-top: -15px;"><b>For the period {{$date}}</b></p>
			<p></p>
			<p></p>
		</div> 
	</header>
	<div class="row">
		<table width="100%" style=";border-collapse: collapse;border-top: 1px solid black;page-break-inside: always !important;" class="tbl_change">
			<thead>
				<tr>

					<th>ID</th>
					<th>Ref No.</th>
					<th>Client</th>
					<th>Swiped Amount</th>
					<th>Transaction Charge</th>
					<th>Change Payable</th>
					<th>Change Received By</th>
					<th>Signature</th>
					<th>Date Received</th>

				</tr>
			</thead>
			<tbody>
				@foreach($atm_swipes as $atm)
				<tr>
					<td class="text-center">{{$atm->id_atm_swipe}}</td>
					<td>CDV# {{$atm->reference}}</td>
					<td>{{$atm->client}}</td>
					<td class="col_amount">{{number_format($atm->amount_swiped,2)}}</td>
					<td class="col_amount">{{number_format($atm->transaction_charge,2)}}</td>
					<td class="col_amount">{{number_format($atm->change_payable,2)}}</td>
					<td></td>
					<td></td>
					<td></td>
					<?php
					$total_swiped += $atm->amount_swiped;
					$total_transaction_charge += $atm->transaction_charge;
					$total_change_payable += $atm->change_payable;
					?>

				</tr>
				@endforeach
			</tbody>

				<tr>
					<td class="bold-text" colspan="3" style="font-size:1rem !important">Grand Total</td>
					<td class="col_amount bold-text" style="font-size:1rem !important">{{number_format($total_swiped,2)}}</td>
					<td class="col_amount bold-text" style="font-size:1rem !important">{{number_format($total_transaction_charge,2)}}</td>
					<td class="col_amount bold-text" style="font-size:1rem !important">{{number_format($total_change_payable,2)}}</td>
					<td></td>
					<td></td>
					<td></td>	
				</tr>				
	

		</table>
	</div>




</body>
</html>

