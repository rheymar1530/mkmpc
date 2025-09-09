<style type="text/css">
	.tbl_no_border td,.tbl_no_border th{
		border: none !important;
	}
	.tbl_no_border td{
		padding: 0.3rem;
	}

	.class_amount:before{
		content : '₱';
	}
</style>
<?php
$inv_details = $investment_data['INVESTMENT_PROD'];
$fees = $investment_data['FEES'];
	// $show_table = $show_table ?? true;

$WITH_TABLE = $investment_data['WITH_TABLE'];
$show_withdrawal_details = $show_withdrawal_details ?? false;
?>

<div class="card c-border">
	<div class="card-body">
		<div class="row">
			<div class="col-sm-12 col-12">
				<p class="mb-0 lbl_color text-md"><b class="lbl_color pr-2"><u>{{$inv_details->product_name}} </u></b> </p>
			</div>
			<div class="col-sm-6 col-12">
				<!-- <h5 class="lbl_color">Investment Product Details: </h5> -->
				<p class="mb-0 lbl_color"><b class="lbl_color pr-2">Interest Rate: </b> {{$inv_details->interest_rate}}% ({{$inv_details->interest_period}}) - {{$inv_details->interest_type}}</p>
				<p class="mb-0 lbl_color"><b class="lbl_color pr-2">Duration: </b> {{$inv_details->duration_text}}(s)</p>
				<!-- <p class="mb-0 lbl_color"><b class="lbl_color pr-2">Withdrawable Part: </b> {{$inv_details->withdrawable_part}}</p> -->
				@if($WITH_TABLE)
				<p class="mb-0 lbl_color"><b class="lbl_color pr-2">Amount: </b> <span class="class_amount">
				{{number_format($investment_data['INVESTMENT_AMOUNT'],2)}}</span></p>
				@endif
			</div>
			<div class="col-sm-6 col-12">

				@if(isset($inv_details->investment_date))
				<p class="mb-0 lbl_color"><b class="lbl_color pr-2">Investment Date: </b> <span>{{$inv_details->investment_date}}</span></p>
				@endif
				@if(isset($inv_details->maturity_date))
				<p class="mb-0 lbl_color"><b class="lbl_color pr-2">Maturity Date: </b> <span>{{$inv_details->maturity_date}}</span></p>
				@endif
			</div>
			<div class="col-sm-12 col-12 mt-2">
				<h6 class="lbl_color text-md">Fees: </h6>
				<ul class="lbl_color">
					@if(count($fees) > 0)

					@foreach($fees as $f)
					<li>{{$f->fee_name}} 
						@if($f->fee_description != "")
						<small><i> ({{$f->fee_description}})</i></small>
						@endif
						@if($f->amount > 0)
						- <span class="class_amount">{{number_format($f->amount,2)}}</span></li>
						@endif
					@endforeach
					@else
					<li>No Fees</li>
					@endif
				</ul>
			</div>
		</div>
	</div>
</div>

@if($investment_data['STATUS'] == "ERROR")
<div class="col-md-12">
	<div class="alert bg-gradient-danger" role="alert">
		<h4 class="alert-heading">Error Message</h4>
		<hr>
		<ul>
			<li>{{$investment_data['message']}}</li>
		</ul>
		
	</div>
</div>
@endif


@if($WITH_TABLE)
<div class="card c-border">
	<div class="card-header bg-gradient-primary2 py-1">
		<h5 class="text-center mb-0">Investment Table</h5>
	</div>
	<div class="card-body p-4">
		<div class="table-responsive" style="overflow-x:auto;">
			<table  class="table tbl_loan repayment-pad lbl_color tbl_no_border" style="white-space: nowrap;margin-top: -3px;">
				<tr class="text-center">
					
					@if($investment_data['SHOW_DATE'])
					<th>Date</th>
					@else
					<th></th>
					@endif
					<th>Interest Gained</th>
					<th>Total Interest Gained</th>
					<th>Ending Amount</th>
				</tr>
				<tr class="text-center">
					<td>-</td>
					<td>-</td>
					<td>-</td>
					<td class="text-right class_amount">{{number_format($investment_data['INVESTMENT_AMOUNT'],2)}}</td>
				</tr>
				@foreach($investment_data['INVESTMENT_TABLE'] as $c=>$inv_table)
				<tr>
					@if($investment_data['SHOW_DATE'])
					<td>{{date('m/d/Y', strtotime($inv_table['date']))}}</td>
					@else
					<td>{{$inv_details->unit}} {{($c+1)}}</td>
					@endif
					<td class="text-right class_amount">{{number_format($inv_table['interest_amount'],2)}}</td>
					<td class="text-right class_amount">{{number_format($inv_table['end_interest'],2)}}</td>
					<td class="text-right class_amount">{{number_format($inv_table['end_amount'],2)}}</td>
				</tr>
				@endforeach
			</table>
		</div>		
		@if($show_withdrawal_details)
		<div class="card c-border">
			<div class="card-body">
				<div class="text-center">
					<h5 class="mb-0 lbl_color">Withdrawal Details</h5>

				</div>
				<p class="lbl_color mb-0"><b>Principal: </b>{{number_format($withdrawal_summary->principal,2)}}</p>
				<p class="lbl_color mb-0"><b>Interest: </b>{{number_format($withdrawal_summary->interest,2)}}</p>
				<p class="lbl_color mb-0"><b>Total Withdrawal: </b>{{number_format($withdrawal_summary->total,2)}}</p>
				@if($renewed_amount > 0)
				<p class="lbl_color mb-0"><b>Renewed Amount: </b>{{number_format($renewed_amount,2)}}</p>
				@endif

			</div>		

		</div>
		@endif	
	</div>
</div>
@endif
