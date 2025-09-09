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
		.tbl_gl  tr>td,.tbl_gl  tr>th{
			vertical-align:top;
			/*font-family: Arial, Helvetica, sans-serif !important;*/
			font-family:"Calibri (Body)";
			/*letter-spacing: 0.05mm;*/
			padding: 3px;
			font-size : 19px;

		}
		
		.text_ex{
			font-family: "Calibri (Body)";
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
	</style>

</head>

<body>
	<header>
		<div class="header" style="margin-top: 100px !important;">
			<p style="font-size: 19px;margin-top: -15px"><b>{{config('variables.coop_name')}}</b></p>
			<p style="font-size: 17px;margin-top: -21px">{{config('variables.coop_address')}}</p>
			<p style="font-size: 17px;margin-top: -20px">CDA Registration No. {{config('variables.fs_cda_reg_no')}}</p>
			<p style="font-size: 17px;margin-top: -20px">Registration Date: {{config('variables.fs_reg_date')}}</p>
			<p style="font-size: 19px;margin-top: -10px"><b>CASH FLOW STATEMENT</b></p>
			<p style="font-size: 17px;margin-top: -20px">For the year ended {{$ending}}</p>
			<p style="font-size: 17px;margin-top: -20px">(With comparative figures for the year {{$comp_date}})</p>


			
		
			<p></p>
			<p></p>
		</div> 
	</header>
	<div class="row">
		@include('cash_flow.table')
	</div>
	<div class="row" style="margin-top:2cm">
	@include('financial_statement.footer')
	</div>
</body>
</html>



