<style type="text/css">
.dbl_undline {
	text-decoration-line: underline !important;
	text-decoration-style: double !important;
}
</style>
<?php
	$date_type = $type;
	$field_not_amount = ['Account','line','type'];
	$field_exclude = ['line','type'];

	$headers = $financial_statement['headers'];
	$fin_data_type = $financial_statement['data'];

	$comp_header_col = 0;

	if($comparative_type == 1){
		$comp_header_col = ($date_type==1)?2:1;
	}

	$key_total_excluded = ['LAST_MONTH','LAST_YEAR'];

	//total per type

	$total_type = array();
	$allocation = array();

	function check_negative($val){
		$col_val = number_format(abs($val),2);

		if($val < 0){
			$col_val = "($col_val)";
		}

		return $col_val;
	}
?>
<table class="table table-bordered table-stripped table-head-fixed table-hover tbl_accounts tbl_gl" style="margin-top:15px">
	<thead>
		@if($comparative_type == 1)
		<tr style="text-align:center;" class="tbl_head">
			<th colspan="3" style="width: 1cm;"></th>
			@foreach($comp_header as $hh)
			<th style="text-align:center;" >@if($date_type==1){{$hh}}@endif</th>
			@endforeach

			@if($date_type == 1)
			<th style="text-align:center;" >Last Month</th>
			@endif
			<th style="text-align:center;" >Last Year</th>
		</tr>

		@endif
		<tr style="text-align:center;" class="tbl_head">
			
				<th colspan="3" style="width: 1cm;"></th>

				@foreach($headers as $head)
				<?php
					$cl = (!in_array($head,$field_not_amount))?"col_border":"";
				?>
				<th style="text-align:center;" class="{{$cl}}">{{$header_keys[$head] ?? $head}}</th>
				@endforeach
				@if($comparative_type ==1)
				@if($date_type == 1)
				<th>Increased (Decreased)</th>
				@endif
				<th>Increased (Decreased)</th>

				@endif
			
		</tr>
	</thead>
	<tbody>
		@foreach($fin_data_type as $type=>$fin_data)
		<tr style="border-bottom:1px solid;">
			<td colspan="{{count($headers)+3+$comp_header_col}}" style="font-weight:bold" class="row_total">{{$type}}</td>
		</tr>
		<?php
			$total_type[$type] = array();
			foreach($headers as $head){
				if(!in_array($head,$field_not_amount)){
					$total_type[$type][$head] =0;
				}
			}
			if($comparative_type == 1){
				if($date_type == 1){
					$total_type[$type]['LAST_MONTH'] = 0;
				}
				

				$total_type[$type]['LAST_YEAR'] = 0;
			}
		?>
		@foreach($fin_data as $line=>$lists)
		<tr style="border-bottom:1px solid;">
			<td style="width: 0.5cm;" class="row_total"></td>
			<td colspan="{{count($headers)+2+$comp_header_col}}" style="font-weight:bold" class="row_total">{{$line}}</td>
		</tr>
		<?php
			$total_line =array();
			foreach($headers as $head){
				if(!in_array($head,$field_not_amount)){
					$total_line[$head] =0;
				}
			}

			if($comparative_type == 1){
				if($date_type == 1){
					$total_line['LAST_MONTH'] = 0;
				}
				
				$total_line['LAST_YEAR'] = 0;
			}
		?>

		@foreach($lists as $vals)
			<tr>
				<td colspan="2"></td>
				@foreach($vals as $key=>$col)
					@if(!in_array($key,$field_exclude))
					@if(in_array($key,$field_not_amount))
					<td>&nbsp;&nbsp;&nbsp;{{$col}}</td>
					@else					
					<?php
						$total_line[$key] += $col;
						$total_type[$type][$key] += $col;

					?>
					<td class="class_amount col_border">{{check_negative($col)}}</td>

					@endif
					@endif

				@endforeach

				@if($comparative_type == 1)
					@if($date_type == 1)
					<td class="class_amount col_border">{{check_negative($vals->A-$vals->B)}}</td>
					<?php
						$total_line['LAST_MONTH'] +=$vals->A-$vals->B;
						$total_type[$type]['LAST_MONTH']+=$vals->A-$vals->B;
					?>
					@endif
					<?php
						$total_line['LAST_YEAR'] +=$vals->A-$vals->C;
						$total_type[$type]['LAST_YEAR']+=$vals->A-$vals->C;
					?>
					<td class="class_amount col_border">{{check_negative($vals->A-$vals->C)}}</td>	
				@endif
			</tr>
		@endforeach

		<tr style="border-top: 1px solid;border-bottom: 1px solid;" class="row_total">
			<td></td>
			<td style="font-weight:bold" colspan="2">{{$line}} Total</td>
				@foreach($total_line as $k=>$tot)
				<td class="class_amount col_border" style="font-weight: bold;">{{check_negative($tot)}}</td>
				@endforeach
		</tr>
		
		@endforeach
		<tr style="border-top: 1px solid;border-bottom: 1px solid;" class="row_total">
			
			<td style="font-weight:bold" colspan="3">{{$type}} Total</td>
				@foreach($total_type[$type] as $k=>$tot)
				<td class="class_amount col_border" style="font-weight: bold;"><u>{{check_negative($tot)}}</u></td>
				@endforeach
		</tr>
		@endforeach
		<!-- SPACE -->
		<tr style="border-top: 1px solid;border-bottom: 1px solid;">
			<td colspan="3"></td>
			@foreach($headers as $h)
			@if(!in_array($h,$field_not_amount))
			<td class="col_border">&nbsp;</td>
			@endif
			@endforeach

			@if($comparative_type == 1)
				@if($date_type == 1)
					<td class="col_border">&nbsp;</td>
				@endif
				<td class="col_border">&nbsp;</td>
			@endif
		</tr>
		@if($financial_report_type == 1)
		<tr>
			<td colspan="3" style="font-weight:bold">Total Liabilities and Equity</td>
			@foreach($headers as $h)
			@if(!in_array($h,$field_not_amount))
			<td class="class_amount col_border" style="font-weight: bold;"><u>{{check_negative($total_type['Liabilities'][$h] + $total_type['Equity'][$h])}}</u></td>
			@endif
			@endforeach
			@if($comparative_type ==1)
				@if($date_type == 1)
				<td class="class_amount col_border" style="font-weight: bold;"><u>{{check_negative($total_type['Liabilities']['LAST_MONTH'] + $total_type['Equity']['LAST_MONTH'])}}</u></td>
				@endif
				<td class="class_amount col_border" style="font-weight: bold;"><u>{{check_negative($total_type['Liabilities']['LAST_YEAR'] + $total_type['Equity']['LAST_YEAR'])}}</u></td>
			@endif
		</tr>
		@else
		<tr>
			<td colspan="3" style="font-weight:bold">Net Surplus Allocation</td>
			@foreach($headers as $h)
			@if(!in_array($h,$field_not_amount))
			<?php
				$alloc = $total_type['Income'][$h] - $total_type['Expenses'][$h];
				$allocation[$h] = $alloc;
			?>
			<td class="class_amount col_border" style="font-weight: bold;"><u>{{check_negative($alloc)}}</u></td>
			@endif
			@endforeach
		</tr>
		<!-- SPACE -->
		<tr style="border-top: 1px solid;border-bottom: 1px solid;">
			<td colspan="3"></td>
			@foreach($headers as $h)
			@if(!in_array($h,$field_not_amount))
			<td class="col_border">&nbsp;</td>
			@endif
			@endforeach
			@if($comparative_type == 1)
				@if($date_type == 1)
					<td class="col_border">&nbsp;</td>
				@endif
				<td class="col_border">&nbsp;</td>
			@endif
		</tr>

		<!-- ALLOCATIONS -->
		<?php
			$allocation_totals = array();
		?>
		@foreach($allocations as $al)
		<tr style="border-top: 1px solid;border-bottom: 1px solid;">
			<td colspan="3" style="font-weight:bold">{{$al->description}}</td>
			@foreach($headers as $h)
			@if(!in_array($h,$field_not_amount))
			<?php
				$al_val = $allocation[$h]*($al->percentage/100);
				$allocation_totals[$h] = $allocation_totals[$h]  ?? 0;
				$allocation_totals[$h]+= $al_val;
			?>

			<td class="col_border class_amount" style="font-weight: normal;">{{ check_negative($al_val) }}</td>
			@endif
			@endforeach
			
			@if($comparative_type == 1)
				@if($date_type == 1)
				 <?php
				 	$alloc_last_month = ($allocation['A'] - $allocation['B'])*($al->percentage/100);
				 	$allocation_totals['LAST_MONTH'] = $allocation_totals['LAST_MONTH'] ?? 0;
				 	$allocation_totals['LAST_MONTH'] +=$alloc_last_month;
				 ?>
				 <td class="col_border class_amount" style="font-weight: normal;">{{ check_negative($alloc_last_month) }}</td>
				@endif
				<?php
				 	$alloc_last_year = ($allocation['A'] - $allocation['C'])*($al->percentage/100);
				 	$allocation_totals['LAST_YEAR'] = $allocation_totals['LAST_YEAR'] ?? 0;
				 	$allocation_totals['LAST_YEAR'] +=$alloc_last_year;
				 ?>
				 <td class="col_border class_amount" style="font-weight: normal;">{{ check_negative($alloc_last_year) }}</td>
			@endif



		</tr>
		@endforeach
		<tr>
			<td colspan="3" style="font-weight:bold">Total</td>
			@foreach($headers as $h)
			@if(!in_array($h,$field_not_amount))

			<td class="class_amount col_border" style="font-weight: bold;"><u>{{ check_negative($allocation_totals[$h])}}</u></td>
			@endif
			@endforeach

			@if($comparative_type == 1)
				@if($date_type == 1)
					<td class="class_amount col_border" style="font-weight: bold;"><u>{{ check_negative($allocation_totals['LAST_MONTH'])}}</u></td>
				@endif
			@endif
			<td class="class_amount col_border" style="font-weight: bold;"><u>{{ check_negative($allocation_totals['LAST_YEAR'])}}</u></td>
		</tr>


		@endif
		<!-- TOTAL LIABILITIES AND EQUITY -->

	</tbody>


</table>  


<?php
	// echo json_encode($allocation);	
?> 