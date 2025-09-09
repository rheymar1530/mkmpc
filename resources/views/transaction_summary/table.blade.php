@foreach($transactions as $type=>$transaction)
<div class="box">
<table class="table table-bordered table-stripped table-head-fixed table-hover tbl_accounts tbl_gl" style="margin-top:20px">

	<thead>
		<tr style="text-align:center;" class="table_header_dblue_cust">
			<th class="table_header_dblue_cust" colspan="{{($type=='Cash Receipt Voucher')?6:6}}" style="text-align:center;font-size: 17px">{{strtoupper($type)}}</th>
		</tr>
		<tr style="text-align:center;border-bottom:1px solid" class="table_header_dblue_cust tbl_head">
			<th class="table_header_dblue_cust">Date</th>
			<!-- <th class="table_header_dblue_cust">Type</th> -->
			<th class="table_header_dblue_cust">Name</th>
			<th class="table_header_dblue_cust" style="width:10cm">Reference</th>
			<th class="table_header_dblue_cust" style="width: 2cm;">Voucher Reference</th>
			<th class="table_header_dblue_cust">Amount</th>
			<th class="table_header_dblue_cust" style="width:6.5cm">Remarks</th>

		</tr>
	</thead>
	<tbody>
		@if(count($transaction) > 0)
		<?php
			$total = 0;
		?>
		@foreach($transaction as $tr)
		<tr class="row_trans" title="{{strtoupper($tr->type)}}">
			<td>{{$tr->date}}</td>
			<!-- <td>{{$tr->type}}</td> -->
			<td>{{$tr->payee}}</td>
			<td>{{$tr->description}}</td>

			<td style="text-align:center">{{$tr->reference}}</td>
			<?php
				$amt = ($tr->amount ==0)?'':number_format($tr->amount,2);
			?>
			<td class="class_amount">{{$amt}}</td>
			<td>{{$tr->remarks}}</td>
		</tr>
		<?php $total+=$tr->amount; ?>
		@endforeach
		<tr class="row_total" style="border-top: 1px solid;">
			<th colspan="{{($type=='Cash Receipt Voucher')?4:4}}" style="text-align:left;">TOTALx</th>
			<th class="class_amount">{{number_format($total,2)}}</th>
			<th></th>
		</tr>
		@else
			<tr>
				<th colspan="{{($type=='Cash Receipt Voucher')?7:6}}" style="text-align:center;">No data</th>
			</tr>
		@endif
		
	</tbody>

</table>  
</div>
@endforeach