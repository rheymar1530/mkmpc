<!DOCTYPE html>
<html lang="en">   
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>{{$mod_title ?? 'Report'}}</title>
	<style>

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
		}
		.columnLeft {         
			float: left;
			width: 50%;
		}
		.columnRight {         
			float: right;
			width: 50%;
		}
		.tbl_gl  tr>td,.tbl_gl  tr>th,.text_ex{
			vertical-align:top;
			/*font-family: Arial, Helvetica, sans-serif !important;*/
			/* font-family:"Calibri (Body)"; */
			font-family: Calibri, sans-serif;
			letter-spacing: 0.05mm;

		}


		.row_g_total{
			font-size: 18px !important;
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

		.class_amount{
			text-align: right;
			margin-right: 5px !important;
		}

		table, td, th {
			/*border: 1px solid;*/
		}

		table {
			width: 100%;
			border-collapse: collapse;
			/*border : 1px solid;	*/
		}
		.box{
/*			border: 1px solid;
			padding-right: 0.3cm;
			padding-left: 0.3cm;*/
			/*margin-bottom: 1cm;*/
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
		.tbl_gl tr{
			page-break-inside: avoid !important;
		}
		.mt-1{
			margin-top:-0.4cm;
		}
	
	</style>

	@if($financial_report_type == 1)
	<style type="text/css">

		.tbl_gl  tr>td,.tbl_gl  tr>th{
			padding:0.2mm;
/*			font-size: 4.3mm ;*/
			/* font-size: 4.5mm */
			font-size: 3.8mm;
		}	
	</style>

	<?php
		$f_mtop = 2;
	?>
	@else 
	<style type="text/css">
		.tbl_gl  tr>td,.tbl_gl  tr>th{
			padding:0mm;
			/*padding-top: 0 !important;*/
			/* font-size: 4.3mm; */
			font-size: 3.8mm;
		}	
	</style>
	<?php
		$f_mtop = 2;
	?>
	@endif
</head>

<body>
<header>
	<div class="header" style="margin-top: 100px !important;">
		<p style="font-size: 13pt;"><b>{{config('variables.coop_name')}}</b></p>
		<p style="font-size: 11pt;margin-top: -14px">{{config('variables.coop_address')}}</p>
		<p style="font-size: 11pt;margin-top: -14px">CDA Registration No. {{config('variables.fs_cda_reg_no')}}</p>
		<p style="font-size: 11pt;margin-top: -14px">Registration Date: {{config('variables.fs_reg_date')}}</p>
		<p style="font-size: 13pt;margin-top: -12px"><b>{{strtoupper($mod_title)}}</b></p>
		<p style="font-size: 11pt;margin-top: -14px">{{$title_period}}</p>
		@if($comparative_type == 1)
		<p style="font-size: 11pt;margin-top: -14px">{{$comparative_description}}</p>
		@endif
	</div> 
</header>
<div class="row">
	@if(count($financial_statement['data']) > 0)
	@include('financial_statement.table')
	@else
	<h4 style="text-align:center;">No Record Found</h4>
	@endif
</div>
<div class="row" style="margin-top:{{$f_mtop}}cm">
	@include('financial_statement.footer')
</div>
</body></html>






