<style type="text/css">
	.tbl_loan  tr>td,.tbl_loan  tr>th{

		vertical-align:top;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 15px;
		/*font-weight: bold;*/
	}
	.xl-text{
		font-size: 18px !important;
	}
	.repayment-pad tr>td,.repayment-pad tr>th{
		padding:2px;
	}
	.loan-pad tr>td,.loan-pad tr>th{
		padding:1px;
		padding-right: 5px;
	}
	.border{
		border: 1px solid black !important;
	}
	.col_amount,.col_amount2{
		text-align: right;
	}
	.center{
		text-align: center;
	}
	.border-top-bottom{
		border-top: 1px solid black !important;
		border-bottom: 1px solid black !important;
	}
	.border-bottom{
		border-bottom: 1px solid black !important;
		border-top: none !important;
	}
	.border-top{
		border-top: 1px solid black !important;

	}
	.no_border{
		border: none !important;
	}
	.col_amount:before{
		content: '₱';
	}
	.pad-left{
		padding-left: 10px !important;
	}
	.badge_table{
		font-size: 15px;
	}
	.sm-text{
		color: red;
		font-size: 15px;
	}
	.sm-text2{
		color: green;
		font-size: 15px;
	}

	tr.row-payment td, tr.row-payment th{
		font-size: 0.85rem !important;
		font-style: italic !important;
	}
</style>

<div class="row">

	@if(isset($show_repayment))
	@if(isset($CURRENT_DUE) && $TOTAL_DUE > 0 && $service_details->lstatus != 2)
	<div class="col-md-12">
		<div class="alert bg-gradient-warning2" role="alert">
			<h4 class="alert-heading">Notice:</h4>
			<!-- <p>Aww yeah, you successfully read this important alert message. This example text is going to run a bit longer so that you can see how spacing within an alert works with this kind of content.</p> -->
			<hr>
			<p class="mb-0">As of {{$CURRENT_DUE->cur_date}}</p>
			<table class="mb-0">
				@if($CURRENT_DUE->prin > 0)
				<tr>
					<td>Principal Due:</td>
					<td class="text-right">{{number_format($CURRENT_DUE->prin,2)}}</td>
				</tr>
				@endif
				@if($CURRENT_DUE->interest > 0)
				<tr>
					<td>Interest Due:</td>
					<td class="text-right">{{number_format($CURRENT_DUE->interest,2)}}</td>
				</tr>
				@endif
				@if($CURRENT_DUE->fees > 0)
				<tr>
					<td>Fees Due:</td>
					<td class="text-right">{{number_format($CURRENT_DUE->fees,2)}}</td>
				</tr>
				@endif
				@if($CURRENT_DUE->surcharge > 0)
				<tr>
					<td>Surcharge Due:</td>
					<td  class="text-right">&nbsp;&nbsp;&nbsp;&nbsp;{{number_format($CURRENT_DUE->surcharge,2)}}</td>
				</tr>
				@endif

				<tr style="border-top:1px solid;">
					<td>Total</td>
					<td>&nbsp;&nbsp;{{number_format($TOTAL_DUE,2)}}</td>
				</tr>
			</table>
		</div>
	</div>
	@endif
	@endif
	<div class="col-md-12">
		<?php
		$lt_max_index = count($loan['LOAN_TABLE'])-1;
		$month_duration = $service_details->month_duration ?? 1;
		?>
		<div class="card c-border">
			<div class="card-body">
				<div class="row">
					<div class="col-md-6 col-12">
						<p class="mb-0"><b class="lbl_color">Applicant Name: </b>{{$member_details->name ?? $service_details->member_name}}</p>
						<p class="mb-0"><b class="lbl_color">Loan Service: </b> {{$service_details->name}}</p>
						<p class="mb-0"><B class="lbl_color">No of Loan Payment(s): </B> {{$loan['repaymentCount']}}</p>
						@if(isset($show_repayment))
						<p class="mb-0"><B class="lbl_color">Loan Period:  </B> 
							@if($service_details->id_loan_payment_type == 1)
				            {{$loan['LOAN_TABLE'][0]['due_date']}} - {{$loan['LOAN_TABLE'][$loan['repaymentCount']-1]['due_date']}}
				            @else
				            {{$service_details->date_granted}} - {{$loan['LOAN_TABLE'][$loan['repaymentCount']-1]['due_date']}}
           				 @endif
						</p>
						@endif
					</div>
					<div class="col-md-6 col-12">
						<p class="mb-0"><b class="lbl_color">@if($service_details->id_loan_payment_type == 1)
							Terms: 
							@else
							Period:
						@endif</b> {{$service_details->terms_desc}}</p>
						<p class="mb-0"><b class="lbl_color">Interest: </b> {{$service_details->interest_rate}}%
							@if($month_duration > 1)
							<small><i class="text-muted">({{number_format($service_details->interest_show,2)}}% x {{$month_duration}} months)</i></small>
							@endif
						</p>
						@if(isset($show_repayment))
						<p class="mb-0"><b class="lbl_color">Date Granted: </b> {{$service_details->date_granted}}</p>
						@endif
					</div>
				</div>
			</div>
		</div>
		<div class="card c-border">
			<div class="card-header bg-gradient-primary2 py-1">
				<h5 class="text-center mb-0">Loan Proceeds Computation</h5>
			</div>
			<div class="card-body p-2">
				<!-- <div class="text-center">
					<h5 class="lbl_color badge bg-light text-lg text-center">Loan Proceeds Computation</h5>
				</div> -->

				<table  class="table tbl_loan loan-pad lbl_color" style="margin-bottom: unset!important;margin-top: -3px;" width="100%">

					<!-- LOAN AMOUNT -->
					<tr>
						<th class="xl-text pad-left no_border" colspan="3">Principal Amount</th>
						<th class="col_amount xl-text no_border" colspan="1">{{number_format($loan['PRINCIPAL_AMOUNT'],2)}}</th>
					</tr>
					<tr>
						<th colspan="4" class="pad-left no_border">Deductions:</th>
					</tr>	
					@foreach($loan['DEDUCTED_CHARGES'] as $charges)
					<!-- DEDUCTIONS LIST -->
					<tr>
						<td colspan="2" class="no_border pad-left">{{$charges['charge_complete_details']}}</td>
						<td class="col_amount no_border">{{number_format($charges['calculated_charge'],2)}}</td>
						<td class="no_border"></td>
					</tr>	
					@endforeach
					<!-- BALANCE FROM THE CURRENT LOAN -->
					@if($loan['TOTAL_LOAN_BALANCE'] > 0)
					<tr>
						<th colspan="4" class="pad-left">Balance from the current loans:</th>
					</tr>
					<tr>
						<td class="no_border pad-left">Loan balance</td>
						<td class="col_amount no_border">{{number_format($loan['LOAN_BALANCE'],2)}}</td>
						<td class="no_border" colspan="2"></td>
					</tr>	

					@if(($loan['SURCHARGE_BALANCE_RENEW'] ?? 0) > 0)
					<tr>
						<td class="no_border pad-left">Surcharge</td>
						<td class="col_amount no_border">{{number_format($loan['SURCHARGE_BALANCE_RENEW'],2)}}</td>
						<td class="no_border" colspan="2"></td>
					</tr>	
					@endif

					@if(($loan['REBATES'] ?? 0) > 0)
					<tr>
						<td class="no_border pad-left">Rebates on loan protection</td>
						<td class="col_amount no_border">({{number_format($loan['REBATES'],2)}})</td>
						<td class="no_border" colspan="2"></td>
					</tr>
					@endif
					
					<tr>
						<td class="no_border pad-left" colspan="2">Remaining loan balance to pay</td>
						<td class="col_amount no_border">{{number_format($loan['TOTAL_LOAN_BALANCE'],2)}}</td>
						<td class="no_border"></td>
					</tr>
					@endif
					@if(isset($loan['PREV_LOAN_OFFSET']))
					@if(count($loan['PREV_LOAN_OFFSET']) > 0)
					<tr>
						<th colspan="4" class="pad-left">Active Loan Payment:</th>
					</tr>
					@foreach($loan['PREV_LOAN_OFFSET'] as $p)
					<tr>
						<td class="no_border pad-left">{{$p->loan}}</td>
						<td class="col_amount no_border">{{number_format($p->payment,2)}}</td>
						<td class="no_border" colspan="2"></td>
					</tr>	

					@if($p->rebates > 0)
					<tr>
						<td class="no_border pad-left">{{$p->loan}} - REBATES</td>
						<td class="col_amount no_border">({{number_format($p->rebates,2)}})</td>
						<td class="no_border" colspan="2"></td>
					</tr>	
					@endif
					@endforeach
					<tr>
						<td class="no_border pad-left" colspan="2">Total Active Loan Payment</td>
						<td class="col_amount no_border">{{number_format($loan['TOTAL_LOAN_OFFSET'],2)}}</td>
						<td class="no_border"></td>
					</tr>
					@endif

					@endif


					<!-- TOTALS -->
					<tr>
						<th class="xl-text pad-left" colspan="3">Total Deductions</th>
						<th class="col_amount xl-text" colspan="1">{{number_format($loan['TOTAL_DEDUCTED_CHARGES'],2)}}</th>
					</tr>	

					<!-- TOTALS -->
					<tr>
						<th class="xl-text pad-left" colspan="3">Total Loan Proceeds</th>
						<th class="col_amount xl-text" colspan="1">{{number_format($loan['TOTAL_LOAN_PROCEED'],2)}}</th>
					</tr>
				</table>
			</div>
		</div>




		<table  class="table tbl_loan loan-pad" style="white-space: nowrap;margin-bottom: unset!important;margin-top: -3px;border: 1px solid black !important;display: none;">
			<tr class="table_header_dblue">
				<th colspan="4" class="center">LOAN PROCEEDS COMPUTATION</th>
			</tr>
			<!-- LOAN AMOUNT -->
			<tr>
				<th class="border-top-bottom xl-text pad-left" colspan="3">Principal Amount</th>
				<th class="border-top-bottom col_amount xl-text" colspan="1">{{number_format($loan['PRINCIPAL_AMOUNT'],2)}}</th>
			</tr>
			<tr>
				<th colspan="4" class="pad-left">Deductions:</th>
			</tr>	
			@foreach($loan['DEDUCTED_CHARGES'] as $charges)
			<!-- DEDUCTIONS LIST -->
			<tr>
				<td colspan="2" class="no_border pad-left">{{$charges['charge_complete_details']}}</td>
				<td class="col_amount no_border">{{number_format($charges['calculated_charge'],2)}}</td>
				<td class="no_border"></td>
			</tr>	
			@endforeach
			<!-- BALANCE FROM THE CURRENT LOAN -->
			@if($loan['TOTAL_LOAN_BALANCE'] > 0)
			<tr>
				<th colspan="4" class="border-top pad-left">Balance from the current loans:</th>
			</tr>
			<tr>
				<td class="no_border pad-left">Loan balance</td>
				<td class="col_amount no_border">{{number_format($loan['LOAN_BALANCE'],2)}}</td>
				<td class="no_border" colspan="2"></td>
			</tr>	
			<tr>
				<td class="no_border pad-left">Rebates on loan protection</td>
				<td class="col_amount no_border">({{number_format($loan['REBATES'],2)}})</td>
				<td class="no_border" colspan="2"></td>
			</tr>
			<tr>
				<td class="no_border pad-left" colspan="2">Remaining loan balance to pay</td>
				<td class="col_amount no_border">{{number_format($loan['TOTAL_LOAN_BALANCE'],2)}}</td>
				<td class="no_border"></td>
			</tr>
			@endif
			@if(isset($loan['PREV_LOAN_OFFSET']))
			@if(count($loan['PREV_LOAN_OFFSET']) > 0)
			<tr>
				<th colspan="4" class="border-top pad-left">Active Loan Payment:</th>
			</tr>
			@foreach($loan['PREV_LOAN_OFFSET'] as $p)
			<tr>
				<td class="no_border pad-left">{{$p->loan}}</td>
				<td class="col_amount no_border">{{number_format($p->payment,2)}}</td>
				<td class="no_border" colspan="2"></td>
			</tr>	

			@if($p->rebates > 0)
			<tr>
				<td class="no_border pad-left">{{$p->loan}} - REBATES</td>
				<td class="col_amount no_border">({{number_format($p->rebates,2)}})</td>
				<td class="no_border" colspan="2"></td>
			</tr>	
			@endif
			@endforeach
			<tr>
				<td class="no_border pad-left" colspan="2">Total Active Loan Payment</td>
				<td class="col_amount no_border">{{number_format($loan['TOTAL_LOAN_OFFSET'],2)}}</td>
				<td class="no_border"></td>
			</tr>
			@endif

			@endif

			<!-- TOTALS -->
			<tr>
				<th class="border-top-bottom xl-text pad-left" colspan="3">Total Deductions</th>
				<th class="border-top-bottom col_amount xl-text" colspan="1">{{number_format($loan['TOTAL_DEDUCTED_CHARGES'],2)}}</th>
			</tr>	

			<!-- TOTALS -->
			<tr>
				<th class="border-top-bottom xl-text pad-left" colspan="3">Total Loan Proceeds</th>
				<th class="border-top-bottom col_amount xl-text" colspan="1">{{number_format($loan['TOTAL_LOAN_PROCEED'],2)}}</th>
			</tr>
		</table>

		@include('loan.loan_amortization')

		@if(isset($loan['REPAYMENT_TABLE']))
		@include('loan.repayment_table')
		@endif


		@if(isset($active_multiple) && count($active_multiple) > 0)
		@include('loan.active_multiple')
		@endif
	</div>
</div>
