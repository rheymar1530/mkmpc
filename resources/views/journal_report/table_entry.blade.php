<style type="text/css">
	.descripition_col{
		width: 4cm !important;
	}
	.name_col{
		width: 4cm !important;
	}
</style>
@foreach($transactions as $type=>$transaction)
<div class="box">
<table class="table table-bordered table-stripped table-head-fixed table-hover tbl_accounts tbl_gl" style="margin-top:20px">

	<thead>
		<tr style="text-align:center;" class="table_header_dblue_cust">
			<th class="table_header_dblue_cust" colspan="{{($type=='Cash Receipt')?10:9}}" style="text-align:center;font-size: 17px">{{strtoupper($type)}}</th>
		</tr>
		<tr style="text-align:center;border-bottom:1px solid" class="table_header_dblue_cust">
			<th class="table_header_dblue_cust">Date</th>
			<th class="table_header_dblue_cust">Reference</th>
			@if($type == "Cash Receipt")
			<th class="table_header_dblue_cust">OR #</th>

			@endif
			<th class="table_header_dblue_cust name_col">Name</th>
			<th class="table_header_dblue_cust descripition_col" >Description</th>

			<th class="table_header_dblue_cust">Acc Code</th>
			<th class="table_header_dblue_cust">Account</th>
			<th class="table_header_dblue_cust">Debit</th>
			<th class="table_header_dblue_cust">Credit</th>
			<th class="table_header_dblue_cust">Remarks</th>
		</tr>
	</thead>
	<tbody>
		<?php
			$debit_g_total = 0;
			$credit_g_total = 0;
		?>
		@if(count($transaction) > 0)
			@foreach($transaction as $ref=>$trans_ref)
				<?php
					$r_span = count($trans_ref);
				?>
				<?php
					$ac_debit=0;
					$ac_credit=0;
				?>
				@foreach($trans_ref as $c=>$items)
				<tr class="row_trans">
					@if($c==0)
					<td rowspan="{{$r_span}}">{{$items->date}}</td>
					<td rowspan="{{$r_span}}" style="text-align:center;">{{$items->reference}}</td>
					@if($type == "Cash Receipt")
					<td rowspan="{{$r_span}}" style="text-align:center;">{{$items->or_no}}</td>
					@endif
					<td rowspan="{{$r_span}}">{{$items->payee}}</td>
					<td rowspan="{{$r_span}}">{{$items->entry_description}}</td>
					@endif
					<td style="text-align:center;">{{$items->account_code}}</td>
					<td>{{$items->account_name}}</td>
					<?php
						$cred = ($items->credit >0)?(number_format($items->credit,2).($items->status==10?'*':'')):'';
						$deb = ($items->debit >0)?(number_format($items->debit,2).($items->status==10?'*':'')):'';
					?>
					<td class="class_amount">{{$deb}}</td>
					<td class="class_amount">{{$cred}}</td>
					<td>{{$items->details}}</td>

					<?php
						$ac_debit+=$items->debit;
						$ac_credit+=$items->credit;

						if($items->status != 10){
							$debit_g_total += $items->debit;
							$credit_g_total += $items->credit;
						}
					?>
				</tr>
				@endforeach
				<tr class="row_trans row_total" style="border-bottom:1px solid;border-top: 1px solid;">
					<td style="font-weight:bold;text-align:right;" colspan="{{($type=='Cash Receipt')?7:6}}">TOTAL</td>
					<td style="font-weight:bold" class="class_amount">{{number_format($ac_debit,2)}}{{($items->status==10?'*':'')}}</td>
					<td style="font-weight:bold" class="class_amount">{{number_format($ac_credit,2)}}{{($items->status==10?'*':'')}}</td>
					<td></td>
				</tr>
			@endforeach
				<tr class="row_total" style="border-bottom:1px solid;">
					<td style="font-weight:bold;font-size: 18px;" colspan="{{($type=='Cash Receipt')?7:6}}">{{$type}} Grand Total</td>
					<td style="font-weight:bold;font-size: 18px" class="class_amount">{{number_format($debit_g_total,2)}}</td>
					<td style="font-weight:bold;font-size: 18px" class="class_amount">{{number_format($credit_g_total,2)}}</td>
					<td></td>
				</tr>
		@endif
		
		
	</tbody>

</table>  
</div>
@endforeach