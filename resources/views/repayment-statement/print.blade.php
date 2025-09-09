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

		table, th, td {
			border-collapse: collapse;
			padding-left: 1mm;
		}
		table td,table th{
			border : 1px solid;
		}
		table{
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
/*		div.x p{
			padding-left: 5mm;
			padding-right: 5mm;
		} */
	</style>
</head>
<body>
	<div class="text-center">
		<h3 style="font-size:14pt !important;font-weight: normal;" class="head mb-0">{{config('variables.coop_name')}}</h3>
		<h3 style="font-size:14pt !important;font-weight: normal;" class="head mb-0">Maasin, Iloilo</h3>
		<p class="head_others mb-0">CDA Registration No. {{config('variables.fs_cda_reg_no')}}</p>
<!-- 				<br>
		<h1 style="font-size:25pt !important" class="mb-0">MANAGER CERTIFICATION</h1>
		<br> -->
	</div>
	<div class="row" style="margin-top:1cm">
		<div style="">
			<p class="n mb-0" ><u>&nbsp;{{$details->statement_date}}&nbsp;</u></p>
		</div>
		<div class="x">
			<p class="n mb-0 name" ><u>&nbsp;{{$details->treasurer}}&nbsp;&nbsp;&nbsp;&nbsp;</u></p>
			<p class="mb-0 spacer" >{{$details->group_}} Treasurer&nbsp;</p>
			<p class="mb-0 spacer">{{$details->group_shortcut}} {{$details->baranggay_lgu}}&nbsp;</p>
		</div>
	</div>
	<div class="row" style="margin-top: 0.5cm;">
		<p class="mb-0">Maam/Sir:</p>
		<p>Good Day!</p>
		<p>I would like to inform you that the following names with have an approved/released loan at Maasin Kawayan MPC. Below is the deduction for the month of {{$details->month_due}}</p>
	</div>
	<div class="row">
		<table class="tbl-mngr" width="100%">
			<thead>
				<tr class="">
					<th class="" style="width: 1cm;"></th>
					<th style="width: 6cm;">BORROWERS'S NAME</th>
					<th class="">LOAN TYPE</th>
					<th>PREVIOUS<br>BALANCE</th>
					<th>CURRENT<br>BALANCE</th>
					<th>SURCHARGE</th>
					<th style="width: 3cm;">AMOUNT</th>
				</tr>
			</thead>
			<?php
				$total = 0;
				$count = 1;

				$keysTotal = ['previous','current','surcharge'];

				$totalsKey = [];
			?>
			@foreach($loans as $id_member=>$loan)
			<tbody>
				@foreach($loan as $c=>$lo)
				<tr class="rloan">
					@if($c == 0)
					<td class="font-weight-bold nowrap text-center" rowspan="{{count($loan)}}">{{$count}}</td>
					<td class="font-weight-bold nowrap" rowspan="{{count($loan)}}">{{$lo->member}}</td>
					@endif
					<td class="nowrap"><sup>[{{$lo->id_loan}}]</sup>{{$lo->loan_name}}</td>
					<td class="font-weight-bold nowrap class_amount">{{number_format($lo->previous,2)}}</td>
					<td class="font-weight-bold nowrap class_amount">{{number_format($lo->current,2)}}</td>
					<td class="font-weight-bold nowrap class_amount">{{number_format($lo->surcharge,2)}}</td>
					<td class="class_amount">{{number_format($lo->statement_amount,2)}}</td>
					<?php
						$total += $lo->statement_amount;
						foreach($keysTotal as $k){
							$totalsKey[$k] = ($totalsKey[$k] ?? 0) + $lo->{$k};
						}
					?>
				</tr>
				@endforeach
			</tbody>
			<?php $count++;?>
			@endforeach
			<footer>
				<tr>
					<th colspan="3" class="class_amount">TOTAL</th>
					@foreach($keysTotal as $k)
					<th class="class_amount">{{number_format($totalsKey[$k],2)}}</th>
					@endforeach
					<th class="class_amount">{{number_format($total,2)}}</th>

				</tr>
			</footer>

		</table>
	</div>
	<div class="row" style="margin-top: 0.5cm;">
		<p>Please implement the deduction of borrower's in their monthly ammortization, MKMPC, Liga and Barangay have MOA.</p>
	</div>
	<div class="row" style="margin-top: -0.5cm;">
		<p>Thank you, GO Cooperative....</p>
	</div>
	<div class="row" style="">
		<p>Very Truly yours,</p>
	</div>
	<div class="row" style="margin-top: 1cm;">
		<div class="x">
			<p class="n mb-0 name" >&nbsp;JOSEPHINE MANERO&nbsp;</p>
			<p class="mb-0 spacer text-center" >Manager</p>
		</div>
	</div>
</body>
</html>