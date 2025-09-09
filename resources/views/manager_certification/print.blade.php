<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>{{$file_name}}</title>

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
		line-height: 10%; }

		.columnRight {         
			float: right;
			width: 50%;
			line-height: 10%;}


			table, th, td {
				border-collapse: collapse;
				padding-left: 1mm;
			}
			table td,table th{
				border : 1px solid;
			}
			table{
				border: 2px solid;
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
				<h3 style="font-size:20pt" class="head mb-0">{{config('variables.coop_name')}}</h3>
				<h3 style="font-size:20pt" class="head mb-0">Maasin, Iloilo</h3>
				<p class="head_others mb-0">CDA Registration No. {{config('variables.fs_cda_reg_no')}}</p>
				<p class="head_others mb-0">Date Registered {{config('variables.fs_reg_date')}}</p>
				<p class="head_others mb-0">Cinno: 0106060270</p>
				<br>
				<h1 style="font-size:25pt !important" class="mb-0">MANAGER CERTIFICATION</h1>
				<br>
			</div>
			<div class="row">
				<div style="margin-left: 6in;">
					<p class="n mb-0">MC-{{$details->mc_year}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$details->formatted_reference}}</p>
					<p class="n mb-0">Date: {{$formatted_date}}</p>
				</div>
				<div style="margin-top:0.5cm;margin-bottom: 0.3cm;">
					<p class="mb-0">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;This is to certify that the following PAYEE were duly issued by {{config('variables.coop_name')}} complete with respective Disbursement Voucher and Documents.</p>
				</div>
			</div>
			<div class="row">
				<table class="tbl-mngr" width="100%">
					<thead>
						<tr class="">
							<th class="table_header_dblue" style="width: 2.5cm;">DATE</th>
							<th class="table_header_dblue">CASH <br> VOUCHER</th>
							<th class="table_header_dblue" style="width:8cm">PAYEE</th>
							<th class="table_header_dblue">AMOUNT</th>
							<th class="table_header_dblue">PURPOSE</th>

						</tr>
					</thead>
					<tbody id="loan-body">
						<?php
							$total = 0;
						?>
						@if(count($cdvs) > 0)
						@foreach($cdvs as $cdv)
						<tr class="loan-row">
							<td>{{$cdv->date}}</td>
							<td>CV - {{$cdv->id_cash_disbursement}}</td>
							<td>{{$cdv->payee}}</td>
							<td class="class_amount">{{number_format($cdv->amount,2)}}</td>
							<td>{{$cdv->purpose}}</td>
						</tr>
						<?php
							$total += $cdv->amount;
						?>
						@endforeach
						@endif
					</tbody>
					<footer>
						<tr>
							<th colspan="3" class="footer_fix"></th>
							<th class="footer_fix class_amount" >{{number_format($total,2)}}</th>
							<th class="footer_fix"></th>
						</tr>
					</footer>

				</table>
			</div>
			<div style="margin-top:0.3cm !important">
				<p class="mb-0">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;This certification is issued to the Internal Arrangement of the Cooperative as a condition for the encashment of said Voucher.</p>
			</div>

			<div class="row" style="page-break-inside: avoid;margin-top: 0.5cm;">
				<div class="columnLeft">
					<div style="margin-top:2.2in">
						<p class="">Prepared by: <b style="margin-left: 0.5cm;">{{config('variables.treasurer')}}</b></p> 
						<p class="" style="margin-left:3cm;margin-top: 0.5cm;">Treasurer</p>
					</div>	
				</div>
				<div class="columnRight">
					<p>Very truly yours</p>
					<p style="margin-top:1.5cm;margin-bottom: 0.5cm;"><b>JOSEPHINE S. MANERO</b></p>
					<p>Manager</p>
					<p style="margin-top:1.5cm">Noted by:</p>
					<p style="margin-top:1.5cm;margin-bottom: 0.5cm"><b>JUAN M. RENTOY JR</b></p>
					<p>BOD - Chairman</p>
				</div>

<!-- 		<div style="margin-left : 6in">
			<p class="mb-0">Very truly yours</p>
			<p style="margin-top:0.9cm;margin-bottom: 0.1cm;"><b>JOSEPHINE S. MANERO</b></p>
			<p class="mb-0">Manager</p>
			<br>
			<br>
			<p class="mb-0">Noted by:</p>
			<br>
			<br>
			<p class="mb-0"><b>JUAN M. RENTOY JR</b></p>
			<p class="mb-0">BOD - Chairman</p>
		</div> -->
	</div>
</body>
</html>

