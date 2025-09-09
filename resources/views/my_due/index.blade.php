@extends('adminLTE.admin_template')
@section('content')


<style type="text/css">
	.tbl_loans tr>td,.tbl_fees tr>td,.tbl_repayment_display tr>td{
		padding: 3px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 15px;
	}
	.tbl_loans tr>th ,.tbl_fees tr>th,.tbl-inputs tr>th{
		padding: 5px;
		padding-left: 5px;
		padding-right: 5px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 14px;
		text-align: center;
	}

	.foot_loan{
		text-align:center;background: #808080;color: white;
	}
</style>


<div class="container-fluid main_form">
	<div class="card">
		<div class="card-body">
			<h3 class="lbl_color">My Dues</h3>
			<div class="table-responsive" style="margin-top: 5px !important">
				<table class="table table-bordered table-stripped table-head-fixed tbl-inputs tbl_loans mt-3" style="white-space: nowrap;">
					<thead>
						<!-- <tr>
							<th colspan="3" style="text-align:center;" class="bg-gradient-primary2">Loan Details</th>
							<th colspan="2" style="text-align:center;" class="bg-gradient-primary2">Amortization Sched</th>
							<th colspan="2" style="text-align:center;" class="bg-gradient-success2">Balance</th>
							<th colspan="4" style="text-align:center;" class="bg-gradient-warning2">Total Dues</th>
						</tr> -->

						<tr>
							<th class="bg-gradient-primary2" width="5%"></th>
							<th class="bg-gradient-primary2">Loan Service</th>
							<th class="bg-gradient-primary2">Date Granted</th>

							<!-- <th class="bg-gradient-primary2">Principal</th>
							<th class="bg-gradient-primary2">Interest</th> -->
							<th class="bg-gradient-primary2">Amortization Schedule</th>



							<th class="bg-gradient-primary2">Loan Balance</th>

							

							
							<th class="bg-gradient-primary2">Current Due</th>				
						</tr>
					</thead>
					<tbody id="active_loans_body">
						<?php
							$total_principal_amt = 0;$total_interest_amt = 0;$total_loan_balance = 0;$total_principal_balance = 0;
							$total_principal=0;$total_interest=0;$total_fees=0;$grand_total=0;
						
						?>
						@foreach($loans as $c=>$row)
						<?php
							$principal = $row->previous_p+$row->current_p;
							$interest = $row->previous_i+$row->current_i;
							$fees = $row->previous_f+$row->current_f;
							$total = $principal+$interest+$fees;
							$loan_bal = $row->principal_balance+$interest+$fees;

							$total_principal_amt += ($row->principal_amt+$row->interest_amt);
							$total_interest_amt += $row->interest_amt;

							$total_loan_balance += $loan_bal;
							$total_principal_balance += $row->principal_balance;

							$total_principal += $principal;
							$total_interest += $interest;
							$total_fees += $fees;


							$grand_total += $total;
						?>
						<tr class="lbl_color">
							<td class="text-center">{{($c+1)}}</td>
							
							<td class="tbl-inputs-text">ID#<a href="/loan/application/view/{{$row->loan_token}}" target="_blank">{{$row->id_loan}}	</a> {{$row->loan_name}}
								@if($row->overdue > 0)
									<span class="badge badge-danger">LAPSED MD - {{$row->overdue}} Month(s)</span>
								@endif
							</td>
							<td>{{$row->date_released}}</td>
							<td class="text-right">{{number_format($row->principal_amt+$row->interest_amt,2)}}</td>
							

							<td class="text-right">{{number_format($loan_bal,2)}}</td>
							
							
							<td class="text-right text-bold">{{number_format($total,2)}}</td>
						</tr>

						@endforeach

					</tbody>
					<tfoot>
						<tr class="foot_loan bg-light">
							<td class="tbl-inputs-text text-bold text-center" colspan="3">TOTAL</td>
							<td class="text-right text-bold">{{number_format($total_principal_amt,2)}}</td>
							
							<td class="text-right text-bold">{{number_format($total_loan_balance,2)}}</td>
							

							
							<td class="text-right text-bold">{{number_format($grand_total,2)}}</td>

							
							
						</tr>
					</tfoot>
				</table>    
			</div>	
		</div>
	</div>
</div>


@endsection

<!-- ((item.overdue > 0)?('<span class="badge badge-danger">LAPSED MD - $row->overdue Month(s)</span>'):'') -->