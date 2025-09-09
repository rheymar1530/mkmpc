<div class="table-responsive">
	<table class="table m-0 font-normal">
		<thead>
			<tr class="text-muted text-center">
				<th class="border-top-0">Ref</th>
				<th class="border-top-0">Type</th>
				<th class="border-top-0">Date</th>
				<th class="border-top-0">OR No</th>
				<th class="border-top-0">Amount Pd</th>
				<th class="border-top-0">Check Amt</th>
				<th class="border-top-0">Change</th>

			</tr>
		</thead>
		<tbody>
			@foreach($payments as $payment)
			<tr>
				<td><a href='/payments/{{$payment->reference}}' target="_blank" class="badge bg-gradient-dark text-sm ml-2 mb-2 rounded-pill">{{$payment->reference}}</a></td>
				<td class="lbl_color">{{$payment->transaction_type}}</td>
				<td class="lbl_color">{{$payment->transaction_date}}</td>
				<td class="lbl_color">{{$payment->or_no}}</td>
				<td class="lbl_color">₱{{number_format($payment->total_payment,2)}}</td>
				<td class="lbl_color">₱{{number_format($payment->swiping_amount,2)}}</td>
				<td class="lbl_color">
					@if($payment->change > 0)
					₱{{number_format($payment->change,2)}}
					@else
					-
					@endif
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>
</div>




					