<style type="text/css">
	.f-total{
		font-size: 18px !important;
	}
</style>
@foreach($transactions as $type=>$transaction)
<div class="box">
<table class="table table-bordered table-stripped table-head-fixed table-hover tbl_accounts tbl_gl" style="margin-top:20px">

	<thead>

		<tr style="text-align:center;" class="table_header_dblue_cust tbl_head">
			<th class="table_header_dblue_cust">Date</th>
			<!-- <th class="table_header_dblue_cust">Type</th> -->
			<th class="table_header_dblue_cust">{{($fil_type=='cash_receipt')?'Payor':'Payee'}}</th>
			<th class="table_header_dblue_cust" style="width:12cm">Reference</th>
			<th class="table_header_dblue_cust" style="width: 4cm;">Voucher Reference</th>
			@if($type == "Cash-in Summary")
			<th class="table_header_dblue_cust">OR #</th>

			@endif
			<th class="table_header_dblue_cust">Amount</th>
			<th class="table_header_dblue_cust" style="width:6.5cm">Remarks</th>

		</tr>
	</thead>
	<tbody>
		@if(count($transaction) > 0)
		<?php
			$total = 0;
		?>
		<?php
			$GLOBALS['export_type'] = $export_type ?? 1;
			function check_amt($amt){
				$formated = ($GLOBALS['export_type']==1)?number_format($amt,2):$amt;
				return ($amt == 0)?'':$formated;
			}
		?>
		@foreach($transaction as $tr)
		<tr class="row_trans" title="{{strtoupper($tr->type)}}">
			<td>{{$tr->date}}</td>
			<!-- <td>{{$tr->type}}</td> -->
			<td>{{$tr->payee}}</td>
			<td>{{$tr->description}}</td>

			<td style="text-align:center">{{$tr->reference}}</td>
			@if($type == "Cash-in Summary")
			<td style="text-align:center">{{$tr->or_no}}</td>

			@endif


			<td class="class_amount">{{check_amt($tr->amount)}}</td>
			<td>{{$tr->remarks}}</td>
		</tr>
		<?php $total+=$tr->amount; ?>
		@endforeach
		<tr class="row_total" style="border-top:1px solid">
			<th class="f-total" colspan="{{($type=='Cash-in Summary')?5:4}}" style="text-align:left;font-weight: bold;font-size: 12px;">GRAND TOTAL</th>
			<th class="class_amount f-total" style="font-weight: bold;font-size: 12px;">{{check_amt($total)}}</th>
			<th></th>
		</tr>
		@else
			<tr>
				<th colspan="{{($type=='Cash-in Summary')?7:6}}" style="text-align:center;">No data</th>
			</tr>
		@endif
		
	</tbody>

</table>  
</div>
@endforeach