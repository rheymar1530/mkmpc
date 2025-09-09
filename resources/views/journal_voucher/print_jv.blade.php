<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>

</head>
<style type="text/css">
	@page {      
		margin-left: 1cm;
		margin-right: 1cm;
		margin-top: 1cm;
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
		font-size: 14px !important;
	}
	.head_others{
		font-size: 15px !important;
	}
	.table_entry_font{
		font-size: 12px !important;
	}

	.font_head{
		font-size: 12px;
	}
	.foot-note{
		font-size: 10px;
	}
	.row:after {
		line-height: 10%;
		content: "";
		display: table;
		clear: both;
		height: 1px;
	}
	table, th, td {
		border-collapse: collapse;
		padding-left: 1mm;
	}
	.with_border{
		border: 1px solid black;
	}
	.class_amount{
		text-align: right;
		padding-right: 2mm;
	}
	.text-center{
		text-align: center!important;
	}
	.mb-0{
		margin-bottom: 0px !important;
		margin-top: 0px !important;
	}
</style>
<body>
	<div class="text-center">
		<h3 style="font-size:18px" class="head mb-0">{{config('variables.coop_name')}}</h3>
		<p class="head_others mb-0">{{config('variables.coop_district')}}</p>
		<p class="head_others mb-0">{{config('variables.coop_address')}}</p>
		<br>
		<h3 style="font-size:18px" class="head mb-0">JOURNAL VOUCHER</h3>
		<br>
	</div>
	<div class="row">
		<table width="100%" style="border-bottom: solid 2px;">
			<tr>
				<td class="font_head"  width="40%"><strong>Pay to:</strong>&nbsp;{{$jv_details->payee}}</td>
				<td class="font_head"  width="30%" style="text-align:center;"><strong>Branch:</strong>&nbsp;{{$jv_details->branch_name}}</td>
				<td class="font_head"  width="30%" ><strong>Date:</strong>&nbsp;{{$jv_details->date}}</td>
			</tr>
			<tr>
				<td class="font_head"   colspan="2"><strong>Address:</strong>&nbsp;{{$jv_details->address}}</td>
				<td class="font_head" ><strong>Type:</strong>&nbsp;{{$jv_details->jv_type}}</td>

				<!-- <td class="font_head" width="50%" style="text-align: right;margin-right: 1cm !important;"><strong>Amount:</strong>&nbsp;{{number_format($jv_details->total_amount,2)}}</td> -->

			</tr>
		</table>
	</div>
	<div class="row">
		<table width="100%" style="border-top: solid 1px;margin-top: 2px;border-bottom: solid 2px;">
			<tr>
				<th style="text-align:center;" class="font_head">Description / Reference</th>
			</tr>
			<tr>
				<td class="font_head">{{$jv_details->description}}</td>
			</tr>
		</table>
	</div>
	<?php
		$total_debit = 0;
		$total_credit = 0;
	?>

	<div class="row">
		
		<table style="width:100%;border:solid 1px;">

			<tr>
				<th style="text-align:center;" class="font_head with_border" colspan="5">J&nbsp;O&nbsp;U&nbsp;R&nbsp;N&nbsp;A&nbsp;L&nbsp;&nbsp;E&nbsp;N&nbsp;T&nbsp;R&nbsp;Y</th>
				
			</tr>
			<tr>
				<th style="text-align:center;" class="font_head with_border">Account Code</th>
				<th style="text-align:center;" class="font_head with_border">Description</th>
				
				<th style="text-align:center;" class="font_head with_border">Debit</th>
				<th style="text-align:center;" class="font_head with_border">Credit</th>
				<th style="text-align:center;" class="font_head with_border">Remarks</th>
			</tr>
			@foreach($entries as $jv)
			<tr>
				<td class="table_entry_font" width="2.5cm">{{$jv->account_code}}</td>
				<td class="table_entry_font">{{$jv->description}}</td>
				
				<td class="table_entry_font class_amount" width="2.5cm">{{ ($jv->debit > 0)?number_format($jv->debit,2):''}}</td>
				<td class="table_entry_font class_amount" width="2.5cm">{{ ($jv->credit > 0)?number_format($jv->credit,2):''}}</td>
				<td class="table_entry_font" style="font-size:10px !important">{{$jv->details}}</td>
				<?php
					$total_debit += $jv->debit;
					$total_credit += $jv->credit;
				?>
			</tr>
			@endforeach
			<tr>
				<th style="text-align:center;" class="font_head with_border" colspan="2">T&nbsp;O&nbsp;T&nbsp;A&nbsp;L&nbsp;</th>
				<th class="font_head with_border class_amount">{{number_format($total_debit,2)}}</th>
				<th class="font_head with_border class_amount">{{number_format($total_credit,2)}}</th>
				<th class="font_head with_border class_amount"></th>
			</tr>
		</table>
	</div>

	<div class="row">
		<table style="margin-top:0.5cm" width="100%">
			<tr>
				<td class="font_head foot-note" width="30%"><strong>Checked by:</strong>&nbsp;_______________________</td>
				<td class="font_head foot-note" width="30%"><strong>Approved by:</strong>&nbsp;______________________</td>
				<td class="font_head foot-note" width="40%"><strong>Received the payment above by:</strong>&nbsp;______________________</td>
			</tr>		
		</table>
	</div>
	<div class="row">
		<table style="margin-top:1mm" width="100%">
			<tr>
				<td class="font_head" width="30%"></td>
				<td class="font_head" width="30%"></td>
				<td class="font_head" width="40%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<small><i>(Print Name and Sign)</i></small></td>
			</tr>		
		</table>
	</div>
	<div class="row">
		<table style="margin-top:0.1cm" width="100%">
			<tr>
				<td class="font_head foot-note" colspan="3" style="text-align:right"><i><strong>Printed by:</strong>&nbsp;{{MySession::PrintNote()}}</i></td>
			</tr>		

		</table>
	</div>
</body>
</html>