<!DOCTYPE html>
<html lang="en">   
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<style>

		@page {      
			margin-left: 1cm;
			margin-right: 1cm;
			margin-top:2cm;
			size : letter portrait;
		}
		div.header{
			text-align: center;
			line-height: 1px;
			font-size: 10px;
			font-family: Arial, Helvetica, sans-serif;
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
		.tbl_repayment_summary  tr>td,.tbl_repayment_summary  tr>th{
			padding:3px;
			vertical-align:top;
			font-family: Arial, Helvetica, sans-serif;
			font-size: 11px ;
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

		.col_amount, .col_number{
			text-align: right;
			margin-right: 5px !important;
		}

		table, td, th {
		  border: 1px solid;
		}

		table {
		  width: 100%;
		  border-collapse: collapse;
		}
		.highlight_amount{
			font-weight: bold;
			text-decoration: underline;
			font-size: 12px !important;
		}
		.bold-text{
			font-weight: bold;
		}


	</style>
</head>

<body>
	<header>
		<div class="header" style="margin-top: -36px">
			    <p style="font-size: 15px;"><b>SMESTCCO <b></p>
			    <p style="font-size: 12px;"><b>Summary of Loan Loan Payment<b></p>
			    <p style="font-size: 12px;"><b>{{$transaction_date}} ({{$trans}})<b></p>
				<p></p>
				<p></p>
			</div> 
		</header>

		<div class="row">
			<table width="100%" style=";border-collapse: collapse;border-top: 1px solid black" class="tbl_repayment_summary">
				<thead>
					<tr>
						<th width="1cm">Item</th>
						<th>Name of Borrower</th>
						<th>Loan Dues</th>
						<th width="3cm">Amount Due</th>
						<th width="3cm">Payment</th>
						<th width="3cm">Signature</th>
					</tr>
				</thead>
				<tbody>
					<?php $counter = 1; ?>
					@foreach($repayment_summary as $name=>$rs)
						<?php $item_content_count = count($rs); ?>
						@foreach($rs as $c=>$val)
							<tr>
								@if($c == 0)
									<td rowspan="{{$item_content_count}}" style="text-align:center;">{{$counter}}</td>
									<td rowspan="{{$item_content_count}}">{{$name}}</td>
								@endif
									
									<?php
										$highlight_amount = (in_array($val->description,['TOTAL','Swiping Amount','Change']))?"highlight_amount":"";
										$bold_text = (in_array($val->description,['TOTAL','Swiping Amount','Change']))?"bold-text":"";
									?>
									<td class="{{$bold_text}}">{{$val->description}}</td>
									<td class="col_amount">{{number_format($val->due,2)}}</td>
									<td class="col_amount">{{number_format($val->amount,2)}}</td>
								@if($c == 0)
									<td rowspan="{{$item_content_count}}"></td>
								@endif
									

							</tr>
						@endforeach
					<?php $counter++; ?>
					@endforeach

				</tbody>

			</table>
		</div>




	</body>
	</html>

