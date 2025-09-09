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
			padding:3px;
			vertical-align:top;
			font-family: Calibri, sans-serif;
			font-size: 14px ;
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
			border : 1px solid;	
		}
		.box{
/*			border: 1px solid;
			padding-right: 0.3cm;
			padding-left: 0.3cm;*/
			margin-bottom: 1cm;
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
			border-bottom: 1px solid;
		}
		.border-left{
			border-left: 1px solid;
		}
		tr.row_trans > td{
			border-left: 1px solid !important;
		}
	</style>
</head>

<body>

	<header>
		<div class="header" style="margin-top: 100px !important;">
			<p style="font-size: 20px;margin-top: -15px"><b>SMESTCCO </b></p>
			<p style="font-size: 15px;margin-top: -15px"><b>Transaction Summary</b></p>
			<p style="font-size: 15px;margin-top: -15px"><b>{{$date}}</b></p>
			<p></p>
			<p></p>
		</div> 
	</header>

	<div class="row">
		@if($selected_type == 1)
		@include('transaction_summary.table')
		@else
		@include('transaction_summary.table_entry')
		@endif
		
	</div>

</body>
</html>

