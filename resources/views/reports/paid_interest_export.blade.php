<!DOCTYPE html>
<html lang="en">   
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
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

		.tbl_gl  tr>td,.tbl_gl  tr>th{
			padding:3px;
			vertical-align:top;
			font-family: Arial, Helvetica, sans-serif; !important;
			letter-spacing: 1px;
			font-size: 0.14in ;
		}



		.class_amount{
			text-align: right;
			margin-right: 5px !important;
		}

		table, td, th {
			border: 1px solid;
		}

		table {
			width: 100%;
			border-collapse: collapse;
			border : 1px solid;	
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
	</style>
</head>

<body>
	<header>
		<div class="header" style="margin-top: 100px !important;">
			<p style="font-size: 20px;margin-top: -15px"><b>{{config('variables.coop_name')}}</b></p>
			<p style="font-size: 17px;margin-top: -21px">{{config('variables.coop_address')}}</p>
			<p style="font-size: 20px;margin-top: -15px"><b>Schedule of Interest Paid by Members</b></p>
			<p style="font-size: 17px;margin-top: -20px">For the Period {{$title_range}}</p>
			
			<p></p>
			<p></p>
		</div> 
	</header>

	<div class="row">
		@if(count($interest_table) > 0)
		@include('reports.interest_table')
		@else
		<h4 style="text-align:center;">No Record Found</h4>
		@endif
		
	</div>

</body>
</html>

