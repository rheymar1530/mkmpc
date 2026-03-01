<!DOCTYPE html>
<html lang="en">   
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>{{$head_title}} - Export</title>
	<style>

		@page {      
			margin-left: 1cm;
			margin-right: 1cm;
			margin-top:2cm;
			size : letter landscape;
		}
		div.header{
			text-align: center;
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
		#tbl-or  tr>td,#tbl-or  tr>th{
			padding:3px;
			vertical-align:top;
			font-family: Calibri, sans-serif;
			font-size: 12pt;
/*			letter-spacing: 1px;*/
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

		 th {
			border: 1px #d6dee1 solid;
		}
		thead tr{
			border-top: 1px #d6dee1 solid;
			border-bottom: 1px #d6dee1 solid;
		}

		tr.nb-top th{
			border-top: unset !important;
		}

		tr.nb-botom th{
			border-botom: unset !important;
		}
		table {
			width: 100%;
			border-collapse: collapse;
			border: 1px #d6dee1 solid;

		}

		td{
			border: 1px #d6dee1 solid;
		}

		.bold{
			font-weight: bold;
		}
		.add_border_bottom{
			border-bottom: 1px #d6dee1 solid;
		}
		#tbl-or tr{
			page-break-inside: avoid !important;
		}
		.center {
			margin-left: auto;
			margin-right: auto;
		}
	
		.nowrap{
			white-space: nowrap;
		}
		a:link, a:visited, a:hover, a:active {
		  color: black; /* Change to your desired color */
		}
		.series-row td{
			font-weight: bold;
		}
		.text-right{
			text-align: right;
		}
	</style>
</head>
<body>
	<header>
		<div class="header" style="margin-top: 100px !important;">
			<p style="font-size: 20px;margin-top: -15px"><b>{{config('variables.coop_abbr')}} </b></p>
			<p style="font-size: 15px;margin-top: -15px"><b>{{$head_title}}</b></p>
			<p style="font-size: 15px;margin-top: -15px"><b>{{$date}}</b></p>
			<p></p>
			<p></p>
		</div> 
	</header>
	<div class="row">
		@include('ora.table')
	</div>



</body>
</html>

