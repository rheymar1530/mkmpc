<table class="table table-head-fixed tbl_in_prod table-bordered table-hover" id="tbl-loans">
	<thead>
		<tr class="table_header_dblue">
			@if($allow_post)
			<th class="table_header_dblue"><input type="checkbox" id="chk-sel-all"></th>
			@endif
			<th class="table_header_dblue">DATE</th>
			<th class="table_header_dblue">CASH VOUCHER</th>
			<th class="table_header_dblue">PAYEE</th>
			<th class="table_header_dblue">AMOUNT</th>
			<th class="table_header_dblue">PURPOSE</th>

		</tr>
	</thead>
	<tbody id="loan-body">
		<?php
			$total = 0;
		?>
		@if(count($loans) > 0)
		@foreach($loans as $loan)
			<tr class="loan-row {{($loan->checked == 1)?'selected':''}}" data-id="{{$loan->id_loan}}" >
				@if($allow_post)

				<td class="text-center"><input type="checkbox" class="chk-loan">{{$loan->checked}}</td>
				@endif
				<td>{{$loan->date}}</td>
				<td>CV - {{$loan->cdv}}</td>
				<td>{{$loan->payee}}</td>
				<td class="text-right">{{number_format($loan->amount,2)}}</td>
				<td>{{$loan->purpose}}</td>
			</tr>
			<?php
				$total += $loan->amount;
			?>
		@endforeach
		@else
		<tr>
			<th colspan="{{$colspan1}}" class="text-center">No Data</th>
		</tr>
		@endif
	</tbody>
	<footer>
		<tr>
			<th colspan="{{$colspan2}}" class="footer_fix" style="text-align:center;background: #808080;color: white;"></th>
			<th class="footer_fix text-right"  style="background: #808080;color: white;">{{number_format($total,2)}}</th>
			<th class="footer_fix"  style="background: #808080;color: white;"></th>
		</tr>
	</footer>

</table>
