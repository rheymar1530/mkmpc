<style type="text/css">
.dbl_undline {
	text-decoration-line: underline !important;
	text-decoration-style: double !important;
}

.tbl_gl  u{
	text-decoration: none !important;

}
.pad_head td, .pad_head th{

}
.text-center{
	text-align: center !important;
}
.class_amount{
	padding-left: 12px !important;
}
.borderless td,.borderless th{
    border-top: none;
    border-left: none;
    border-right: none;
    border-bottom: none;
}
.font-emp{
	font-size: 15px !important;
}
</style>

@if(($export_type ?? 1) == 1)
<?php
	function format_num($num) {

		if($num == 0){
			return '0.00';
		}
    	return $num < 0 ? '(' . number_format(abs($num),2) . ')' : number_format($num,2);
	}
?>

@else
<?php
	function format_num($num) {
		if($num == 0){
			return '0.00';
		}

		return $num;
    	return $num < 0 ? '(' .abs($num).')' : $num;
	}
?>

@endif


<!-- borderless  -->
<table class="table borderless table-head-fixed table-hover tbl_accounts tbl_gl mt-1">

	@if(($export_type ?? 1) == 1)
	<colgroup>
		<col width="3%">
		<col width="3%">
	</colgroup>
	@endif

	<tbody>
		@if(($export_type ?? 1) == 2)
		<tr>
			<td colspan="6" style="text-align: center;"><b>{{config('variables.coop_name')}}</b></td>
		</tr>
		<tr>
			<td colspan="6" style="text-align: center;">{{config('variables.coop_address')}}</td>
		</tr>
			<tr>
			<td colspan="6" style="text-align: center;">CDA Registration No. {{config('variables.fs_cda_reg_no')}}</td>
		</tr>
			<tr>
			<td colspan="6" style="text-align: center;">Registration Date: {{config('variables.fs_reg_date')}}</td>
		</tr>
			<tr>
			<td colspan="6" style="text-align: center;"><b>{{strtoupper($RPTType)}}</b></td>
		</tr>
			<tr>
			<td colspan="6" style="text-align: center;">{{$DateDesc}}</td>
		</tr>
		@endif

		<?php
			$ColExpTotals = [];
		?>

		@foreach($report_output['entries'] as $OType=>$datas)
		<?php
			$tempTotal = 0;
		?>
		<tr>
			<td colspan="6"><b>{{$OType}}</b></td>
		</tr>

			@foreach($datas as $cat=>$catData)
			<tr>
				<td></td>
				<td colspan="5"><b>{{$catCode[$cat]}}</b></td>
			</tr>
				<?php
					// dd($catData);
					$catDataLength = count($catData) - 1;
					$subTotal = collect($catData)->sum('amount');
					$tempTotal += $subTotal;

					
					// dd($DataCount);

				?>
				@if($cat != "CRTx")
					@foreach($catData as $c=>$cd)
					<?php
						$class=($c == $catDataLength)?'b-bottom':'';
					?>
						<tr class="{{$class}}">
							<td colspan="2"></td>
							<td colspan="2">{{$cd->description}}</td>
							<td class="text-right">{{format_num($cd->amount)}}</td>
							<td class="text-right">
								@if($c == $catDataLength)
								<b>{{format_num($subTotal)}}</b>
								@endif
							</td>
						</tr>
					@endforeach
				@else
					@foreach($catData as $c=>$cd)
						<tr>
							<td colspan="2"></td>
							<td>CRV # {{$cd->id_cash_receipt_voucher}}</td>
							<td class="text-sm">{{$cd->description}}</td>
							<td class="text-right">{{format_num($cd->amount)}}</td>
							<td class="text-right">
								@if($c == $catDataLength)
								<b>{{format_num($subTotal)}}</b>
								@endif
							</td>
						</tr>
					@endforeach				
				@endif

			@endforeach		
			<!-- Sub Total -->
			<?php
				$ColExpTotals[$OType] = $tempTotal;
			?>
			<tr class="b-bottom">
				<td colspan="5"><b>Total {{$OType}}</b></td>
				<td class="text-right"><b>{{format_num($tempTotal)}}</b></td>
			</tr>

		@endforeach
	</tbody>
	<?php
		$adjustmentTotal = 0;
	?>
	@if(count($report_output['adjustments']) > 0 || count($report_output['adjustmentsAccount']) > 0)
	<?php
		$adjustmentTotal = (collect($report_output['adjustments'])->sum('amount') ?? 0) + (collect($report_output['adjustmentsAccount'])->sum('amount') ?? 0);
	?>
	<tbody id="body-adjustments">
		<tr>
			<td colspan="6"><b>Adjustments (JV)</b>&nbsp;Increase/(Decrease)</td>
		</tr>
		@foreach($report_output['adjustments'] as $ro)
		<tr>
			<td colspan="2"></td>
			<td colspan="2">{{$ro->description}}</td>
			<td class="text-right">{{format_num($ro->amount)}}</td>
			<td></td>
		</tr>
		@endforeach


		@foreach($report_output['adjustmentsAccount'] as $ro)
		<tr>
			<td colspan="2"></td>
			<td colspan="2">{{$ro->description}}</td>
			<td class="text-right">{{format_num($ro->amount)}}</td>
			<td></td>
		</tr>
		@endforeach

		<tr class="bx">
			<td colspan="5"><b>Total Adjustments</b></td>
			<td class="text-right"><b>{{format_num($adjustmentTotal)}}</b></td>
		</tr>
	</tbody>
	@endif




	<!-- NET CASH -->
	<?php
		$netCash = ROUND(($ColExpTotals['Collection'] ?? 0) - ($ColExpTotals['Disbursement'] ?? 0) + $adjustmentTotal ,2)
	?>
	<tr>
		<td colspan="5"><b>Net Cash</b></td>
		<td class="text-right"><b>{{format_num($netCash)}}</b></td>
	</tr>
	<?php
		$cash = $report_output['Cash'];
		$i = 1;
	?>
	<tbody>
		@if(isset($cash['Cash on Hand']))
			@foreach($cash['Cash on Hand'] as $c)
				<tr class="cash">
					<td colspan="2" class="text-right">{{($i==1)?'Add:':''}}</td>
					<td colspan="2">{{$c->description}} ({{($report_output['asOf'])}})</td>
					<td class="text-right">{{format_num($c->amount)}}</td>
					<td></td>
				</tr>
				<?php $i++; ?>
					
				
			@endforeach
		@endif

		@if(isset($cash['Cash in Bank']))
			<tr>
				<td colspan="2" class="text-right">{{($i==1)?'Add:':''}}</td>
				<td colspan="4">Cash in Bank ({{($report_output['asOf'])}})</td>
			</tr>
			<?php 
				$maxCount = count($cash['Cash in Bank']) - 1;
				$i++; 
			?>
			@foreach($cash['Cash in Bank'] as $counter=>$c)
				<tr class="cash">
					<td colspan="2" class="text-right"></td>
					<td></td>
					<td>{{$c->description}}</td>
					<td class="text-right">{{format_num($c->amount)}}</td>
					<td class="text-right">
						@if($counter == $maxCount)
							{{format_num($report_output['TotalCash'])}}
						@endif
					</td>
				</tr>
				
			@endforeach
		@endif
	</tbody>

	<?php
		$totalCheck = collect($report_output['Checks'] ?? [])->sum('amount');
	?>
	@if(count($report_output['Checks']) > 0)
	<?php
		$check = $report_output['Checks'];

	?>
	<tr>
		<td class="text-right" colspan="2">Less:</td>
		<td colspan="2">Check on hand</td>
		<td></td>
		<td class="text-right">{{format_num($check[0]->amount)}}</td>
	</tr>
	@endif






	
	<?php
		$totalCash = $netCash + ($report_output['TotalCash'] ?? 0) - $totalCheck;
	?>
	<tr class="bx">
		<td colspan="5"><b>Total Cash ({{$currentAsOf}})</b></td>
		<td class="text-right"><b>{{format_num($totalCash)}}</b></td>
	</tr>

</table>