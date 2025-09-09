<!DOCTYPE html>
<html lang="en">   
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<style>

		@page {      

		}


		* {
			box-sizing: border-box;
			font-family: Arial, sans-serif;

		}
		.columnLeft {         
			float: left;
			width: 50%;
		}
		.columnRight {         
			float: right;
			width: 50%;
		}
/*		.tbl_payroll_summary  tr>td,.tbl_payroll_summary  tr>th{
			padding:3px;
			vertical-align:top;
			font-family: "Calibri, sans-serif";
			font-size: 12px ;
		}*/

		.details_tbl_lbl{
			text-align: right !important;
			width: 40% !important;
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
			padding-right: 0.5cm !important;
		}
		.payslip_content{
			font-size: 4.2mm;
		}
		table, td, th {
			/*border: 1px solid;*/
		}

		table {
			width: 100%;
			border-collapse: collapse;
		}
		.box{
			border: 1px solid;
/*			height: 4.258in;*/
			width: 100%;
		}
		.box.nb{
			border: none !important;
		}
		.no_b{
			/*border-style: dotted;*/
			border: none !important;
		}
		.bold{
			font-weight: bold;
		}
		.align-right{
			text-align: right;
		}
		.test td{
			/*border: 1px solid;*/
		}

		.normal_pad{
			padding-left: 0.5cm !important;
		}
		.normal_pad2{
			padding-left: 0.7cm !important;
		}
		.head_padding{
			padding-left: 0.3cm !important;
		}
		.bold{
			font-weight: bold;
		}
		.payslip_details{
			padding-left: 3mm;
		}

		table#tbl_payslip,table#tbl_payslip th,table#tbl_payslip td {
		  border-right: 1px dashed;
		  border-left: none !important;
		  border-collapse: collapse;

		  font-size: 4mm;
		}
		.ad_font{
			font-size: 4.1mm !important;
		}
		.ad_font2{
			font-size: 3.9mm !important;
		}
		#tbl_payslip{

		}

		.bb{

			border-bottom: 1px solid;
		}
		table.print_tbl th,table.print_tbl td{
/*			padding : 3mm !important;*/
		}
		tr.with_pad th,tr.with_pad td{
			padding : 3mm !important;
		}
		.pt{
			padding-top: 3mm !important;
		}
		tr.row_content td{
			padding: 0.75mm 1.5mm 0.75mm 1.5mm;
/*			padding-bottom: 0.75mm;*/
			vertical-align: top;
		}
		.b-top{
			border-top: 1px solid;
		}
		.b-bot{
			border-bottom: 1px solid;
		}
	</style>
</head>

<body>
	<div class="row" style="margin-top: 0.4cm ;page-break-inside:avoid !important;" > 
		<div class="box nb">
			<table width="100%" class="no_b print_tbl">
				<tr>
					<th class="no_b pt" colspan="6" style="font-size:4mm">{{config('variables.coop_name')}}</th>
				</tr>
				<tr>
					<th class="no_b" colspan="6">ATM Swipe Transaction</th>
				</tr>
			</table>
		</div>
		<div class="box" style="margin-top:4mm;padding: 1mm 0 0 0 !important;">
			<table width="100%" class="no_b">
				<tr class="row_content">
					<td><b>Name: </b>{{$details->client}}</td>
					<td><b>Date: </b>{{$details->date}}</td>
				</tr>
				<tr class="row_content">
					<td><b>Client Type: </b>{{$details->client_type}}</td>
					<td><b>Bank: </b>{{$details->bank_name}}</td>
				</tr>
				<tr class="row_content b-top b-bot">
					<td colspan="2" style="text-align:center;"><b>PARTICULARS</b></td>
				</tr>
				<tr class="row_content">
					<td>Swiped Amount</td>
					<td class="align-right">{{number_format($details->amount,2)}}</td>
				</tr>
				<tr class="row_content">
					<td>Transaction Charge</td>
					<td class="align-right"><b>{{number_format($details->transaction_charge,2)}}</b></td>
				</tr>
				<tr class="row_content b-bot">
					<td>Change Payable</td>
					<td class="align-right"><b>{{number_format($details->change_payable,2)}}</b></td>
				</tr>
				<tr class="row_content">
					<td colspan="2" style="height:1in !important"><b>Remarks:</b>{{$details->remarks}}</td>
				</tr>
				
			</table>
			<table width="100%" class="no_b">
				<tr class="row_content b-top">
					<td><b>TXN No: </b>{{$details->id_atm_swipe}}</td>
					<td><b>JV No: </b>{{$details->id_journal_voucher}}</td>
					<td class="align-right"><b>CDV No: </b>{{$details->id_cash_disbursement}}</td>
				</tr>
				<tr class="row_content b-top">
					<td colspan="3" style="height:0.8cm !important">Change Release By:</td>
				</tr>
				<tr class="row_content b-top">
					<td colspan="3" style="height:0.8cm !important">Change Received By:</td>
				</tr>
				<tr class="row_content b-top">
					<td colspan="3">Date Created: {{$details->date_created}}</td>
					
				</tr>
			</table>
		</div>
	</div>

</body>
</html>



