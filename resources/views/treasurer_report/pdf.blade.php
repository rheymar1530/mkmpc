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
		.tbl_gl  tr>td,.tbl_gl  tr>th{
			padding:0.5mm;
			font-size: 13pt;
		}	

		.b-bottom{
			border-bottom: 1px solid !important;
		}
		.bx{
			border-top: 1px solid !important;
			border-bottom: 1px solid !important;
		}
		.text-right{
			text-align: right;
		}
	</style>

</head>

<body>
<header>
	<div class="header" style="margin-top: 100px !important;">
		<p style="font-size: 13pt;"><b>{{config('variables.coop_name')}}</b></p>
		<p style="font-size: 13pt;margin-top: -14px">{{config('variables.coop_address')}}</p>
		<p style="font-size: 13pt;margin-top: -14px">CDA Registration No. {{config('variables.fs_cda_reg_no')}}</p>
		<p style="font-size: 13pt;margin-top: -14px">Registration Date: {{config('variables.fs_reg_date')}}</p>
		<p style="font-size: 13pt;margin-top: -12px"><b>{{strtoupper($RPTType)}}</b></p>
		<p style="font-size: 13pt;margin-top: -14px">{{$DateDesc}}</p>

	</div> 
</header>
<div class="row">
	@include('treasurer_report.table')
</div>

</body></html>






