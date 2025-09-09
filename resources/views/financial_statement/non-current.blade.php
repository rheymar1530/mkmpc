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
@foreach($lists as $cat=>$list)
	<tr style="">
		<td style="width: 0.5cm;" class="row_total"></td>
		<td colspan="{{count($headers)+2+$comp_header_col}}" style="font-weight:bold" class="row_total">
			@if($GLOBALS['export_type'] == 1)
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
			@else
				                          
			@endif
		{{$cat}}</td>
	</tr>

	<?php
		$totalCat =array();
		foreach($headers as $head){
			if(!in_array($head,$field_not_amount)){
				$totalCat[$head] =0;
			}
		}

		if($comparative_type == 1){
			if($date_type == 1){
				$totalCat['LAST_MONTH'] = 0;
			}
			$totalCat['LAST_YEAR'] = 0;
		}
	?>
	@foreach($list as $vals)
			<tr>
				<td colspan="2"></td>
				@foreach($vals as $key=>$col)
					@if(!in_array($key,$field_exclude))
					@if(in_array($key,$field_not_amount))
					<td style="padding-left: 0.7cm !important">{{$col}}</td>
					@else					
					<?php
						$totalCat[$key] += $col;
						$total_line[$key] += $col;
						$GLOBALS['totalType'][$type][$key] += $col;

					?>
					<td class="class_amount col_border {{td_class($col)}}">{{check_negative($col)}}</td>
					@endif
					@endif

				@endforeach

				@if($comparative_type == 1)
					@if($date_type == 1)
					<td class="class_amount col_border {{td_class($vals->A-$vals->B)}}">{{check_negative($vals->A-$vals->B)}}</td>
					<?php
					
						$totalCat['LAST_MONTH'] +=$vals->A-$vals->B;
						$total_line['LAST_MONTH'] +=$vals->A-$vals->B;
						$GLOBALS['totalType'][$type]['LAST_MONTH']+=$vals->A-$vals->B;
					?>
					@endif
					<?php
						$totalCat['LAST_YEAR'] +=$vals->A-$vals->C;
						$total_line['LAST_YEAR'] +=$vals->A-$vals->C;
						$GLOBALS['totalType'][$type]['LAST_YEAR']+=$vals->A-$vals->C;
					?>
					<td class="class_amount col_border {{td_class($vals->A-$vals->C)}}">{{check_negative($vals->A-$vals->C)}}</td>	
				@endif
			</tr>
		@endforeach
	<tr style="border-bottom: 1px solid;" class="row_total">
		<td></td>
		<td style="font-weight:bold;padding-left:0.7cm" colspan="2">Total {{$cat}}</td>
			@foreach($totalCat as $k=>$tot)
			<td class="class_amount col_border {{td_class($tot)}}" style="font-weight: bold;">{{check_negative($tot)}}</td>
			@endforeach
	</tr>

@endforeach

	<tr style="border-top: 1px solid;border-bottom: 1px solid;" class="row_total">
		<td></td>
		<td style="font-weight:bold;padding-left:0.7cm" colspan="2">Total {{$line}}</td>
			@foreach($total_line as $k=>$tot)
			<td class="class_amount col_border {{td_class($tot)}}" style="font-weight: bold;">{{check_negative($tot)}}</td>
			@endforeach
	</tr>
