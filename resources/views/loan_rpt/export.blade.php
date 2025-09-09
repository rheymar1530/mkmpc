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
			font-family: Calibri, sans-serif;
		}

		.tbl_gl  tr>td,.tbl_gl  tr>th{
			padding:3px;
			vertical-align:top;
			font-family: Arial, Helvetica, sans-serif !important;
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
		.text-centered{
			text-align: center;
		}
		.mid{
/*			text-align: center;*/
			vertical-align:middle !important;
		}
		#head-tbl th{
			vertical-align: middle !important;
		}
		.mb-0{
			margin-bottom: 0 !important;
		}
	</style>
</head>

<body>
	<?php
		if($mode == 2){
			$totalsKey = ['principal_amount','sf','ff','cbu','interest','total_loan_proceeds'];
		}else{
			$totalsKey = ['principal_amount','sf','ff','cbu','insurance','loan_offset','total_loan_proceeds','repayment_amount','interest_amount','total_amtz'];
		}
		$totals=array();
		foreach($totalsKey as $k){
			$totals[$k] = 0;
		}
	?>
	<header>
		<div class="header" style="margin-top: 100px !important;">
			<p style="font-size: 20px;margin-top: -15px"><b>{{config('variables.coop_name')}}</b></p>
			<p style="font-size: 17px;margin-top: -21px">{{config('variables.coop_address')}}</p>
			<p style="font-size: 20px;margin-top: -15px"><b>Lending Summary Update</b></p>
			<p style="font-size: 20px;margin-top: -15px"><b><u>{{$details->name}}</u></b></p>
			
			
			<p></p>
			<p></p>
		</div> 
	</header>

	<div class="row">
		<p class="mb-0">The MKMPC Loan Applications of the following members have been approved and released of {{strtoupper($month_selected)}}</p>	
	
		@if($mode == 2)
		<table class="table table-bordered table-head-fixed table-hover tbl_accounts tbl_gl" style="" width="100%">
			<thead id="head-tbl">
				<tr>
					<th width="3%" rowspan="2"></th>
					<th class="text-centered" rowspan="2">Loan ID</th>
					<th class="text-centered" rowspan="2">Name of Borrower</th>
					<th class="text-centered" rowspan="2">Amount Approved</th>
					<th class="text-centered" colspan="4">DEDUCTIONS</th>
					<th class="text-centered" rowspan="2">Net Proceeds</th>
					<th class="text-centered" rowspan="2">Payment Schedule</th>

				</tr>
				<tr>
					<th class="text-centered">SF</th>
					<th class="text-centered">FF</th>
					<th class="text-centered">CBU</th>
					<th class="text-centered">Adv. Interest</th>
				</tr>
			</thead>
			<tbody>

				@foreach($loans as $due=>$loan)

					@foreach($loan as $c=>$l)
						<tr>
							<td class="text-centered">{{$c+1}}</td>
							<td class="text-centered">{{$l->id_loan}}</td>
							<td>{{$l->member}}</td>
						
							<td class="class_amount">{{number_format($l->principal_amount,2)}}</td>
							<td class="class_amount">{{number_format($l->sf,2)}}</td>
							<td class="class_amount">{{number_format($l->ff,2)}}</td>
							<td class="class_amount">{{number_format($l->cbu,2)}}</td>
							<td class="class_amount">{{number_format($l->interest,2)}}</td>
							<td class="class_amount">{{number_format($l->total_loan_proceeds,2)}}</td>

							@if($c == 0)
							<td rowspan="{{count($loan)}}" class="mid">{{$l->due_date}}</td>
							@endif
						</tr>
						<?php
							foreach($totalsKey as $k){
								$totals[$k] += $l->{$k};
							}
						?>
					@endforeach	


				@endforeach




			</tbody>
			<tfoot>
				<tr>
					<td style="font-weight: bold;" colspan="3">GRAND TOTAL</td>
					@foreach($totals as $val)
					<td class="class_amount">{{number_format($val,2)}}</td>
					@endforeach
					<td></td>

				</tr>
			</tfoot>
		</table>	
		@else
		<table class="table table-bordered table-head-fixed table-hover tbl_accounts tbl_gl" style="" width="100%">
			<thead id="head-tbl">
				<tr>
					<th width="3%" rowspan="2"></th>
					<th class="text-centered" rowspan="2">Loan ID</th>
					<th class="text-centered" rowspan="2">Borrower</th>
					<th class="text-centered" rowspan="2">Co-maker</th>
					<th class="text-centered" rowspan="2">Barangay</th>
					<th class="text-centered" rowspan="2">Loan Amount</th>
					<th class="text-centered" colspan="5">DEDUCTIONS</th>
					<th class="text-centered" rowspan="2">Net Proceeds</th>
					<th class="text-centered" colspan="3">Amortization Due</th>
				</tr>
				<tr>
					<th class="text-centered">SF</th>
					<th class="text-centered">FF</th>
					<th class="text-centered">CBU</th>
					<th class="text-centered">Insurance</th>
					<th class="text-centered">Loan Balance</th>
					<th class="text-centered">Principal</th>
					<th class="text-centered">Interest</th>
					<th class="text-centered">Total</th>
				</tr>
			</thead>
			<tbody>
				@foreach($loans as $c=>$l)
					<tr>
						<td class="text-centered">{{$c+1}}</td>
						<td class="text-centered">{{$l->id_loan}}</td>
						<td>{{$l->member}}</td>
						<td></td>
						<td>{{$l->brgy_lgu}}</td>
						<td class="class_amount">{{number_format($l->principal_amount,2)}}</td>
						<td class="class_amount">{{number_format($l->sf,2)}}</td>
						<td class="class_amount">{{number_format($l->ff,2)}}</td>
						<td class="class_amount">{{number_format($l->cbu,2)}}</td>
						<td class="class_amount">{{number_format($l->insurance,2)}}</td>
						<td class="class_amount">{{number_format($l->loan_offset,2)}}</td>

						<td class="class_amount">{{number_format($l->total_loan_proceeds,2)}}</td>
						<td class="class_amount">{{number_format($l->repayment_amount,2)}}</td>
						<td class="class_amount">{{number_format($l->interest_amount,2)}}</td>
						<td class="class_amount">{{number_format($l->total_amtz,2)}}</td>
					</tr>
					<?php
						foreach($totalsKey as $k){
							$totals[$k] += $l->{$k};
						}
					?>
				@endforeach
			</tbody>
			<tfoot>
				<tr>
					<td style="font-weight: bold;" colspan="5">GRAND TOTAL</td>
					@foreach($totals as $val)
					<td class="class_amount">{{number_format($val,2)}}</td>
					@endforeach

				</tr>
			</tfoot>
		</table>			
		@endif

		<p class="mb-0">Prepared by: </p>	
		<p class="mb-0" style="margin-left: 1in;font-weight: bold;">Josephine S. Manero </p>	
		<p class="mb-0" style="margin-left: 1in;margin-top: 0;">Manager</p>	
	</div>

</body>
</html>

