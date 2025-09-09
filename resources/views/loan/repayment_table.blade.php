<?php
$principal_f = $loan['PRINCIPAL_AMOUNT'];
?>

<?php
$principal_f = $loan['PRINCIPAL_AMOUNT'];
?>
<div class="card c-border">
	<div class="card-header bg-gradient-primary2 py-1">
		<h5 class="text-center mb-0">Payments</h5>
	</div>
	<div class="card-body">
<!-- 		<div class="text-center">
			<h5 class="lbl_color badge bg-light text-lg text-center mb-2">Payments</h5>
		</div> -->
		<div class="table-responsive" style="overflow-x:auto;">
			<table  class="table tbl_loan repayment-pad lbl_color" style="white-space: nowrap;margin-top: -3px;">
				<tr>
					<th class="no_border center">No</th>
					<th class="no_border center text-sm">Statement</th>
					<th class="no_border center">Reference</th>
					<th class="no_border center">Date</th>
					<th class="no_border center">Principal</th>
					<th class="no_border center">Interest</th>
					<!-- <th class="no_border center">Fees</th> -->
					<th class="no_border center">Penalty</th>
					<th class="no_border center">Total Payment</th>
					<th class="no_border center">Principal Balance</th>
				</tr>			
				<?php
					$total_principal = 0;
					$total_interest = 0;
					$total_fees = 0;
					$total_surcharge = 0;
					$total_over = 0;
				?>
				@if(count($loan['REPAYMENT_TABLE']) > 0)
				@foreach($loan['REPAYMENT_TABLE'] as $count=>$row)
				<tr>
					<td class="no_border center">{{($count+1)}}</td>
					<td class="no_border text-sm">
						@if(isset($row->payment_source))
						{{$row->payment_source}} <a href="/repayment-statement/view/{{$row->id_repayment_statement}}" target="_blank">{{$row->id_repayment_statement}}</a>
						@else
						-
						@endif
					</td>
					<td class="text-sm no_border">
						@if($row->payment_reference == '-')
						RT #<a href="/repayment/view/{{$row->repayment_token}}" target="_blank">{{$row->reference}}</a>
						@elseif($row->payment_reference == 'Beginning')
						Beginning
						@elseif($row->payment_reference == "Individual")
						ID# <a href="/repayment-bulk/view/{{$row->id_repayment}}" target="_blank">{{$row->id_repayment}}</a>
						[Individual] 
						@elseif($row->payment_reference == "Statement")
						ID# <a href="/repayment-bulk/view/{{$row->id_repayment}}" target="_blank">{{$row->id_repayment}}</a>
						[Statement]
						@elseif($row->payment_reference == "Loan")
						Loan ID# <a href="/loan/application/approval/{{$row->loan_token}}" target="_blank">{{$row->pay_on_id_loan}}</a>
						@endif
					</td>
					<td class="no_border">{{$row->transaction_date}}</td>
					<td class="no_border col_amount">{{number_format($row->paid_principal,2)}}</td>
					<td class="no_border col_amount">{{number_format($row->paid_interest,2)}}</td>
					<!-- <td class="no_border col_amount">{{number_format($row->paid_fees,2)}}</td> -->
					<td class="no_border col_amount">{{number_format($row->paid_surcharge,2)}}</td>
					<td class="no_border col_amount">{{number_format(($row->paid_principal+$row->paid_interest+$row->paid_fees+$row->paid_surcharge),2)}}</td>
					<?php
						$principal_f -=$row->paid_principal;
						$total_principal += $row->paid_principal;
						$total_interest += $row->paid_interest;
						$total_fees += $row->paid_fees;
						$total_surcharge += $row->paid_surcharge;
						$total_over += ($row->paid_principal+$row->paid_interest+$row->paid_fees+$row->paid_surcharge);
					?>
					<td class="no_border col_amount">{{number_format($principal_f,2)}}</td>
				</tr>
				@endforeach
				<tr>
					<th class="no_border xl-text" colspan="4">Total Payments</th>
					<th class="no_border col_amount xl-text">{{number_format($total_principal,2)}}</th>
					<th class="no_border col_amount xl-text">{{number_format($total_interest,2)}}</th>
					<!-- <th class="no_border col_amount xl-text">{{number_format($total_fees,2)}}</th> -->
					<th class="no_border col_amount xl-text">{{number_format($total_surcharge,2)}}</th>
					<th class="no_border col_amount xl-text">{{number_format($total_over,2)}}</th>
					<th class="no_border center xl-text"></th>
				</tr>
				@else
				<tr>
					<td style="text-align:center;" colspan="9">No Record</td>
				</tr>
				@endif
			</table>
		</div>
	</div>
</div>

<div class="card c-border">
	<div class="card-header bg-gradient-primary2 py-1">
		<h5 class="text-center mb-0">Loan Balance</h5>
	</div>
	<div class="card-body">
		<div class="row">
			<div class="col-md-2">
				<label class="lbl_color text-lg">Principal Balance</label>
				<p class="font-weight-bold lbl_color text-lg">{{number_format($LOAN_BALANCE[0]->principal_balance,2)}}</p>
			</div>
			<div class="col-md-2">
				<label class="lbl_color text-lg">Interest Balance</label>
				<p class="font-weight-bold lbl_color text-lg">{{number_format($LOAN_BALANCE[0]->interest_balance,2)}}</p>
			</div>
			<div class="col-md-2">
				<label class="lbl_color text-lg">Surcharge Balance</label>
				<p class="font-weight-bold lbl_color text-lg">{{number_format($LOAN_BALANCE[0]->surcharge_balance,2)}}</p>
			</div>
			<div class="col-md-2">
				<label class="lbl_color text-lg font-italic">Discount</label>
				
				@if(($DISCOUNT ?? 0) > 0)
				<p class="font-weight-bold text-lg text-danger font-italic">({{number_format($DISCOUNT ?? 0,2)}})</p>
				@else
				<p class="font-weight-bold lbl_color text-lg">-</p>
				@endif
				
			</div>
			<div class="col-md-2">
				<label class="lbl_color text-lg">Total Balance</label>
				<p class="font-weight-bold lbl_color text-lg">{{number_format(($LOAN_BALANCE[0]->loan_balance)-($DISCOUNT ?? 0),2)}}</p>
			</div>
		</div>
	</div>
</div>


