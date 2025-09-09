<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>{{$file_name}}</title>
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
		p{
			font-size: 13pt;
		}

		* {
			box-sizing: border-box;
			font-family:"Calibri" !important;
		}
		.head{
			font-size: 17pt !important;
		}
		.head_others{
			font-size: 15pt !important;
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
		.columnLeft {         
			float: left;
			width: 50%;
			line-height: 10%; 
		}

		.columnRight {         
			float: right;
			width: 50%;
			line-height: 10%;
		}

		table.loan,table.loan th,table.loan td {
			border-collapse: collapse;
			padding-left: 1mm;
		}
		table.loan td,table.loan th{
			border : 1px solid;
		}
		table.loan{
/*			border: 2px solid;*/
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
.bold{
	font-weight: bold;
}
.name {
	display: inline-block;
}

.spacer {
	display: block;
	width: fit-content; /* Adjust the width to match the first <p> */

}
div.x {
	display: inline-block;
}
.foot_tbl td{
	font-size: 13pt;
}
/*		div.x p{
			padding-left: 5mm;
			padding-right: 5mm;
		} */
	</style>
</head>
<body>
	<div class="text-center">
		<h3 style="font-size:14pt !important;font-weight: normal;" class="head mb-0">Republic of the Philippines</h3>
		<h3 style="font-size:14pt !important;font-weight: normal;" class="head mb-0">Province of Iloilo</h3>
		<h3 style="font-size:14pt !important;font-weight: normal;" class="head mb-0">Municipality of Maasin</h3>
		<h3 style="font-size:14pt !important;font-weight: normal;" class="head mb-0">@if($details->type_code ==1) {{$details->group_}} {{$details->baranggay_lgu}} @else {{$details->baranggay_lgu}} {{$details->group_}}@endif</h3>
		<h3 style="font-size:14pt !important;font-weight: normal;margin-top: 5mm !important" class="head mb-0">Statement of loan amount remitted to {{config('variables.coop_name')}}</h3>
		<h3 style="font-size:14pt !important;font-weight: normal;" class="head mb-0">For the month of {{$details->month_due}}</h3>

		
<!-- 				<br>
		<h1 style="font-size:25pt !important" class="mb-0">MANAGER CERTIFICATION</h1>
		<br> -->
	</div>

	<div class="row" style="margin-top: 0.5cm;">
		<p class="mb-0" style="margin-left: 6.5in;">No.: {{$details->month_year}}-{{$details->id_repayment_statement}}</p>
		
	</div>
	<div class="row">
		<table class="tbl-mngr loan" width="100%">
			<thead>
				<tr class="">
					<th class="" style="width: 1cm;"></th>
					<th style="width: 7.5cm;">NAME</th>
					<th class="">LOAN TYPE</th>
					<th style="width: 2.5cm;">LOAN DUE</th>
					<th style="width: 2.5cm;">AMOUNT</th>
					<th style="width: 2cm;">REFERENCE</th>
				</tr>
			</thead>
			<?php
				$total = 0;
				$count = 1;
				$total_due = 0;
			?>
			@foreach($loans as $id_member=>$loan)
			<tbody>
				@foreach($loan as $c=>$lo)
				<tr class="rloan">
					@if($c == 0)
					<td class="font-weight-bold nowrap text-center" rowspan="{{count($loan)}}">{{$count}}</td>
					<td class="font-weight-bold nowrap" rowspan="{{count($loan)}}">{{$lo->member}}</td>
					@endif

					<td class="nowrap" style="font-size:11pt !important"><sup>[{{$lo->id_loan}}]</sup>{{$lo->loan_name}}</td>
					<td class="class_amount">{{number_format($lo->current_due,2)}}</td>
					<td class="class_amount">
						@if($lo->act_amount_paid > 0)
						{{number_format($lo->act_amount_paid,2)}}
						@endif
					</td>
					@if($c == 0)
					<td class="nowrap" rowspan="{{count($loan)}}">
						@if(isset($lo->id_cash_receipt_voucher))
						CRV-{{$lo->id_cash_receipt_voucher}}
						@endif
					</td>
					@endif
					<?php
						$total += $lo->act_amount_paid;
						$total_due += $lo->current_due;
					?>
				</tr>
				@endforeach
			</tbody>
			<?php $count++;?>
			@endforeach
			<footer>
				<tr>
					<th colspan="3" class="class_amount">TOTAL</th>
					<th class="footer_fix class_amount" >{{number_format($total_due,2)}}</th>
					<th class="footer_fix class_amount" >
					@if($total > 0)
					{{number_format($total,2)}}
					@endif
					</th>
					<th></th>
				</tr>
			</footer>

		</table>
	</div>
	<div class="row" style="margin-top: 0.5cm;">
		<table class="foot_tbl" width="40%" style="border-collapse: collapse;">
			<colgroup>
				<col width="35%">
			</colgroup>

			<tr>
				<td class="text_ex">Receipt No.:</td>
				<td class="text_ex" style="border-bottom: 1px solid;">{{$details->or_number}}</td>
				
			</tr>
			<tr>
				<td class="text_ex">Check No.:</td>
				<td class="text_ex" style="border-bottom: 1px solid;">{{$details->check_no}}</td>
				
			</tr>
			<tr>
				<td class="text_ex">Date Received:</td>
				<td class="text_ex" style="border-bottom: 1px solid;">
					@if(isset($details->date_received))
					{{date('m/d/Y',strtotime($details->date_received))}}
					@endif
				</td>
			</tr>
		</table>
	</div>

	<div class="row" style="margin-top:0.7in">
		<table class="foot_tbl" width="100%" style="border-spacing: 0.5cm !important;border-collapse: separate;">
			<tr>
				<td class="text_ex" style=";width: 40%;padding-bottom: 5mm;">Prepared by:</td>
				<td class="text_ex" style="width: 20%"></td>

				<td class="text_ex" style=";width: 40%">Noted by:</td>
			</tr>
			<tr>
				<td class="text_ex" style="border-bottom: 1px solid;width: 40%;text-align: center;">{{$details->treasurer}}&nbsp;</td>
				<td class="text_ex" style="width: 20%"></td>

				<td class="text_ex" style="border-bottom: 1px solid;width: 40%"></td>
			</tr>
		</table>
		<table class="foot_tbl" width="100%" style="margin-top: -0.3cm;font-size:3.9mm !important">
			<tr>
				<td class="text_ex" style="width: 40%;text-align: center;">{{$details->group_}} Treasurer</td>
				<td class="text_ex" style="width: 20%;text-align: center">&nbsp;</td>
				<td class="text_ex" style="width: 40%;text-align: center">&nbsp;Punong Barangay</td>

			
			</tr>
		</table>
	</div>
</body>
</html>