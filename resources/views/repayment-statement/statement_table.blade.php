<table class="table table-bordered table-head-fixed tbl_pdc w-100" id="tbl_loan">
	<thead>
		<tr class="text-center">
			<th>BORROWER'S NAME</th>
			<th>LOAN TYPE</th>
			<th>PREVIOUS<br>BALANCE</th>
			<th>CURRENT<br>BALANCE</th>
			<th>SURCHARGE</th>
			<th>STATEMENT<br>AMOUNT</th>
		</tr>
	</thead>


	@if(count($loans) > 0)
	@foreach($loans as $id_member=>$loan)
	<tbody class="borders bmember hidden-member" data-member-id="{{$id_member}}">
		<?php
			$member_total = 0;
		?>
		@foreach($loan as $c=>$lo)
		<tr class="rloan" data-loan="{{$lo->loan_token}}" data-id="{{$lo->id_loan}}" loan-due="{{$lo->balance}}">
			@if($c == 0)
			<td class="font-weight-bold nowrap" rowspan="{{count($loan)}}"><i>{{$lo->member}}</i></td>
			@endif
			<td class="nowrap"><sup><a href="/loan/application/approval/{{$lo->loan_token}}" target="_blank">[{{$lo->id_loan}}] </a></sup>{{$lo->loan_name}}</td>
			<td class="font-weight-bold nowrap text-right">{{number_format($lo->previous,2)}}</td>
			<td class="font-weight-bold nowrap text-right">{{number_format($lo->current,2)}}</td>
			<td class="font-weight-bold nowrap text-right">{{number_format($lo->surcharge,2)}}</td>

			<td class="in"><input class="form-control p-2 text-right txt-input-amount in-loan-due" value="{{number_format($lo->statement_amount,2)}}"></td>

	
			<?php 
				$GLOBALS['total'] += $lo->balance; 
				$member_total += $lo->balance;
			?> 
		</tr>
		@endforeach
		<?php
			$GLOBALS['memtotal'][$id_member] = $member_total;
		?>
	</tbody>
	@endforeach
	@else
	<tr>
		<td class="text-center" colspan="3">No Data Found</td>
	</tr>
	@endif

</table>

