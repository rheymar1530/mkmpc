<table  class="table tbl_loan repayment-pad" style="white-space: nowrap;margin-top: 5px;border: 1px solid black !important;display: none;">
	<tr class="table_header_dblue">
		<th colspan="4" class="center">LOAN AMORTIZATION</th>
	</tr>

	<tr>
		<td class="no_border pad-left" style="width: 15%;">Principal</td>
		<td class="col_amount no_border" style="width: 15%;">{{number_format($loan['LOAN_TABLE'][0]['repayment_amount'],2) }}</td>
		<td class="no_border" colspan="2"></td>
	</tr>	
	@if($loan['LOAN_TABLE'][0]['interest_amount'] > 0)
	<tr>
		<td class="no_border pad-left">Interest</td>
		<td class="col_amount no_border">{{number_format($loan['LOAN_TABLE'][0]['interest_amount'],2) }}</td>
		<td class="no_border" colspan="2"></td>
	</tr>	
	@endif
	@if($loan['LOAN_TABLE'][0]['fees'] > 0)
	<tr>
		<td class="no_border pad-left">Fees</td>
		<td class="col_amount no_border">{{number_format($loan['LOAN_TABLE'][0]['fees'],2) }}</td>
		<td class="no_border" colspan="2"></td>
	</tr>	
	@endif
	<tr>
		<th class="border-top-bottom xl-text pad-left" colspan="3">Total Amortization</th>
		<th class="border-top-bottom col_amount xl-text" colspan="1">{{number_format($loan['LOAN_TABLE'][0]['total_due'],2) }}</th>
	</tr>	

</table>

<div class="card c-border">
	<div class="card-header bg-gradient-primary2 py-1">
		<h5 class="text-center mb-0">Loan Amortization</h5>
	</div>
	<div class="card-body">
		<!-- <div class="text-center">
			<h5 class="lbl_color badge bg-light text-lg text-center">Loan Amortization</h5>
		</div> -->
		<table  class="table tbl_loan repayment-pad lbl_color" style="white-space: nowrap;margin-top: 5px;">
			<tr>
				<td class="no_border pad-left" style="width: 15%;">Principal</td>
				<td class="col_amount no_border" style="width: 15%;">{{number_format($loan['LOAN_TABLE'][0]['repayment_amount'],2) }}</td>
				<td class="no_border" colspan="2"></td>
			</tr>	
			@if($loan['LOAN_TABLE'][0]['interest_amount'] > 0)
			<tr>
				<td class="no_border pad-left">Interest</td>
				<td class="col_amount no_border">{{number_format($loan['LOAN_TABLE'][0]['interest_amount'],2) }}</td>
				<td class="no_border" colspan="2"></td>
			</tr>	
			@endif
			@if($loan['LOAN_TABLE'][0]['fees'] > 0)
			<tr>
				<td class="no_border pad-left">Fees</td>
				<td class="col_amount no_border">{{number_format($loan['LOAN_TABLE'][0]['fees'],2) }}</td>
				<td class="no_border" colspan="2"></td>
			</tr>	
			@endif
			<tr>
				<th class=" xl-text pad-left" colspan="3">Total Amortization</th>
				<th class=" col_amount xl-text" colspan="1">{{number_format($loan['LOAN_TABLE'][0]['total_due'],2) }}</th>
			</tr>	
		</table>
	</div>
</div>






@if(isset($AMTZ_SCHED))
<div class="card c-border">
	<div class="card-header bg-gradient-primary2 py-1">
		<h5 class="text-center mb-0">Amortization Table</h5>
	</div>
	<div class="card-body">
		<div class="table-responsive" style="overflow-x:auto;">
			<table  class="table tbl_loan repayment-pad lbl_color" style="white-space: nowrap;margin-top: -3px;">
				<thead>
					<tr>
						<th class="no_border center">Due Date</th>
						<th class="no_border center text-sm">Principal</th>
						<th class="no_border center">Interest</th>
						<th class="no_border center">Surcharge</th>
						<th class="no_border center">Total</th>
						<th class="no_border center">Principal Balance</th>
						<th class="no_border center"></th>
					</tr>	
				</thead>
				<?php
					$totalPrincipalPaid = 0;
				?>
				@foreach($AMTZ_SCHED as $amtz)
					<tr class="font-weight-bold">
						<td class="no_border">{{$amtz->date}} @if($amtz->accrued == 1) <span class="badge badge-danger text-xs">Overdue</span> @endif</td>
						<td class="no_border col_amount">{{number_format($amtz->principal,2)}}</td>
						<td class="no_border col_amount">{{number_format($amtz->interest,2)}}</td>
						<td class="no_border col_amount">{{number_format($amtz->surcharge,2)}}</td>
						<td class="no_border col_amount">{{number_format($amtz->total,2)}}</td>
						<td class="no_border"></td>
						<!-- {{number_format(($service_details->principal_amount - $totalPrincipalPaid),2)}} -->
						<td class="no_border"></td>
					</tr>

					@if(isset($AMTZ_PAYMENT[$amtz->term_code]))
						<!-- <tr class="row-payment">
							<td class="no_border center font-weight-bold">Payment Date</td>
							<td class="no_border center text-sm font-weight-bold" colspan="6">Payments</td>
						</tr>	 -->
						<?php 
							$tPaidPrincipal =0; $tPaidInterest = 0 ; $tPaidSurcharge = 0; $tPaidTotal = 0;
						?>
						@foreach($AMTZ_PAYMENT[$amtz->term_code] as $payment)
						<?php 
							$tPaidPrincipal += $payment->principal; 
							$tPaidInterest += $payment->interest ; 
							$tPaidSurcharge += $payment->surcharge; 
							$tPaidTotal += $payment->total;

							$totalPrincipalPaid += $payment->principal;
							$refID = ($payment->id_repayment > 0)? $payment->id_repayment : $payment->id_repayment_transaction;


							if($payment->payment_reference == ""){
								if($payment->id_repayment > 0){
									$linkRef = "/repayment-bulk/view/{$payment->id_repayment}";
								}else{
									$linkRef = "/repayment/view/{$payment->repayment_token}";
								}
							}


						?>
						<tr class="row-payment">
							<td class="text-sm no_border">{{$payment->date}} </td>
							<td class="text-sm no_border col_amount">{{number_format($payment->principal,2)}}</td>
							<td class="text-sm no_border col_amount">{{number_format($payment->interest,2)}}</td>
							<td class="text-sm no_border col_amount">{{number_format($payment->surcharge,2)}}</td>
							<td class="text-sm no_border col_amount">{{number_format($payment->total,2)}}</td>
							<td class="text-sm no_border col_amount">{{number_format($service_details->principal_amount-$totalPrincipalPaid,2)}}</td>
							<td class="text-sm no_border pl-4">
								@if($payment->payment_reference == "")
								{{$payment->or_no}} <sup><a href="{{$linkRef}}" target="_blank">[{{$refID}}]</a></sup>
								@else
								{{$payment->payment_reference}}
								@endif
							</td>
						</tr>
						@endforeach

						<tr class="row-payment" style="border-bottom: 1px solid;">
							<td class="text-sm no_border font-weight-bold">Balance</td>
							<td class="text-sm no_border col_amount">{{number_format($amtz->principal-$tPaidPrincipal,2)}}</td>
							<td class="text-sm no_border col_amount">{{number_format($amtz->interest-$tPaidInterest,2)}}</td>
							<td class="text-sm no_border col_amount">{{number_format($amtz->surcharge-$tPaidSurcharge,2)}}</td>
							<td class="text-sm no_border col_amount">{{number_format($amtz->total-$tPaidTotal,2)}}</td>
							<td class="text-sm no_border"></td>
							<td class="text-sm no_border"></td>
						</tr>
					@endif
			
				@endforeach		
			</table>
		</div>
	</div>
</div>
@endif