<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>{{$file_name}}</title>
	<style>
		.row:after {
			line-height: 10%;
			content: "";
			display: table;
			clear: both;
			height: 1px; 
		}
		.columnLeft {         
			float: left;
			width: 50%;
		}
		.columnRight {         
			float: right;
			width: 50%;
		}
		@page {      
			margin-left: 1in;
			margin-right: 1in;
			margin-top:1in;
			size : letter landscape;
		}
		div.header{
			text-align: center;
			/*line-height: 1px;*/
			font-size: 15px;
			font-family: Calibri, sans-serif;
		}

		* {
			box-sizing: border-box;
			font-family: Calibri, sans-serif;
		}

		.tbl_gl  tr>td,.tbl_gl  tr>th{
			padding:3px;
			vertical-align:top;
			font-family: Arial, Helvetica, sans-serif !important;
			letter-spacing: 1px;
			font-size: 0.14in ;
		}



		.class_amount{
			text-align: right;
			padding-right: 2mm !important;
		}

		table, td, th {
			border: 1px solid;
		}

		table {
			width: 100%;
			border-collapse: collapse;
			border : 1px solid;	
		}

		.highlight_amount{
			font-weight: bold;
			text-decoration: underline;
			font-size: 12px !important;
		}
		.bold-text{
			font-weight: bold;
		}
		.tbl_head{
			border-top:  2px solid;
			border-bottom: 2px solid;
		}
		.col_border{
			/*border-left: 1px solid;*/
		}
		.year_head th{
			font-weight: normal !important;
		}
		.text-centered{
			text-align: center;
		}
		#head-tbl th{
			vertical-align: middle !important;
		}
		.mb-0{
			margin-bottom: 0 !important;
		}
		.my-0{
			margin-top: 0 !important;
			margin-bottom: 0 !important;
		}
		.font-weight-bold{
			font-weight: bold !important;
		}
	</style>
</head>
<body>
	<header>
		<div class="header" style="margin-top: 100px !important;">
			<p style="font-size: 20px;margin-top: -15px"><b>{{config('variables.coop_name')}}</b></p>
			<p style="font-size: 17px;margin-top: -21px">{{config('variables.coop_address')}}</p>
			<p style="font-size: 20px;margin-top: -15px"><b>Check Deposit Summary</b></p>

		</div> 
	</header>
	<?php
		$total = 0;
	?>
	<div class="row" style="margin-top: 0.5cm;">
		<div class="columnLeft">
			<p class="my-0"><b>Deposit ID: </b>{{$details->id_check_deposit}}</p>
			<p class="my-0"><b>Date: </b>{{$details->date}}</p>
			<p class="my-0"><b>Bank: </b>{{$details->bank_name}}</p>
		</div>
		<div class="columnRight">
			<p class="my-0"><b>Total Amount: </b>{{number_format($details->amount,2)}}</p>
			<p class="my-0"><b>CRV No.: </b>{{$details->id_cash_receipt_voucher}}</p>
			<p class="my-0"><b>Remarks: </b>{{$details->remarks}}</p>
			
		</div>		
	</div>
	<div class="row">
		<table class="tbl-mngr loan" width="100%">
			<thead>
				<tr class="text-center">
					<th width="5%"></th>
					<th>Date</th>
					<th>Description</th>
					<th>Reference</th>
					<th>Check No</th>
					<th>Check Bank</th>
					<th>Check Date</th>
					<th>Amount</th>
				</tr>
			</thead>
			<tbody>
				@foreach($deposits as $c=>$ch)
				<tr class="lbl_color row-check">
					<td class="text-centered">{{($c+1)}}</td>
					<td>{{$ch->transaction_date}}</td>
					<td>{{$ch->description}}</td>
					<td>{{$ch->reference}}</td>
					<td>{{$ch->check_no}}</td>
					<td>{{$ch->check_bank}}</td>
					<td>{{$ch->check_date}}</td>
					<td class="class_amount">{{number_format($ch->amount,2)}}</td>
					<?php
						$total += $ch->amount;
					?>
				</tr>

				@endforeach
				<tr>
					<td colspan="7" class="font-weight-bold">Total</td>
					<td class="class_amount font-weight-bold">{{number_format($total,2)}}</td>
				
				</tr>
			</tbody>
			
		</table>
	</div>

</body>
</html>