@if($export_type == 1)
<style type="text/css">
.dbl_undline {
	text-decoration-line: underline !important;
	text-decoration-style: double !important;
}
.font-type{
	font-size: 20px !important;
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
    /*border:  1px solid;*/
}
.text-left{
	text-align: left;
}
.by{
	border-top: 1px solid;
	border-bottom: 1px solid;
}
</style>

@endif
<?php
	$GLOBALS['export_type'] = $export_type;
	function check_negative($val){
		if($GLOBALS['export_type'] == 1){
			$col_val = number_format(abs($val),2);
		}else{
			$col_val = $val;
		}
		


		if($val == 0){
			return '0.00';
		}

		if($val < 0 && $GLOBALS['export_type'] == 1){
			$col_val = "($col_val)";
		}

		return $col_val;
	}

	function td_class($val){
		if($val == 0){
			return "text-center";
		}
	}
	// $beg = 5514656;
	$beg = env('BEGINNING_CASH');
	$totals = array(
		'A'=>0,
		'B'=>0,
		'D'=>0
	);

?>
<table class="table borderless table-head-fixed table-hover tbl_accounts tbl_gl" style="margin-top:13px">
	<thead>
		<tr class="by">
			<th colspan="4"></th>
			<th class="text-center">{{$head_label['A']}}</th>
			<th class="text-center">{{$head_label['B']}}</th>
			<th>Increase/Decrease</th>
		</tr>
	</thead>
	<tbody>
		@foreach($cash_flow as $type=>$items)
		<?php
			$totals_temp = array(
				'A'=>0,'B'=>0
			);
		?>
		<tr>

			<th class="text-left font-type" colspan="7" style="font-weight:bold">CASH FLOW FROM {{strtoupper($type)}}</th>
		</tr>

		@if($type == "Operating Activities" || $type == "Financing Activities")
		<tr>
			<th style="width: 0.5cm;"></th>
			<th class="text-left" colspan="6" style="font-weight:bold">Cash provided from operation</th>
		</tr>
		@endif

		@foreach($items as $sub=>$item)
		@if($sub != "")
		<tr>
			<th style="width: 0.5cm;"></th>
			<th style="width: 0.5cm;"></th>
			<th class="text-left" colspan="5" style="font-weight:bold">{{$sub}}</th>
		</tr>

		@foreach($item as $row)
		<tr>
			<th style="width: 0.5cm;"></th>
			<th colspan="1" style="width:0.5cm"></th>
			<th colspan="1" style="width:0.5cm"></th>
			<td class="text-left">
				{{$row->cash_flow_description}} 

				@if($sub == "Changes in Assets & Liabilities" && $row->A != 0)
					@if($row->cash_flow_description == "Receivables")
						@if($row->A < 0)
						(Increased)
						@else
						(Decreased)
						@endif

					@elseif($row->cash_flow_description == "Liabilities")
						@if($row->A > 0)
						(Increased)
						@else
						(Decreased)
						@endif
					@endif
				@endif
				
			</td>
			<td class="class_amount">{{check_negative($row->A)}}</td>
			<td class="class_amount">{{check_negative($row->B)}}</td>
			<td class="class_amount">{{check_negative($row->A-$row->B)}}</td>

			<?php
				$totals['A'] += $row->A;
				$totals['B'] += $row->B;
				$totals['D'] += $row->D;

				$totals_temp['A'] +=$row->A;
				$totals_temp['B'] +=$row->B;

			?>
		</tr>
		@endforeach

		@else
		@foreach($item as $row)
		<tr>
			<th colspan="2"></th>
			<td class="text-left" colspan="2">{{$row->cash_flow_description}}</td>
			<td class="class_amount">{{check_negative($row->A)}}</td>
			<td class="class_amount">{{check_negative($row->B)}}</td>
			<td class="class_amount">{{check_negative($row->A-$row->B)}}</td>
		</tr>
			<?php
				$totals['A'] += $row->A;
				$totals['B'] += $row->B;
				$totals['D'] += $row->D;


				$totals_temp['A'] +=$row->A;
				$totals_temp['B'] +=$row->B;

			?>
		@endforeach
		@endif


		@endforeach

		<tr class="by">
			<th colspan="2"></th>
			<th colspan="2" class="text-left" style="font-weight:bold">Net Cashflow from {{$type}}</th>
			<th class="class_amount" style="font-weight:bold">{{check_negative($totals_temp['A'])}}</th>
			<th class="class_amount" style="font-weight:bold">{{check_negative($totals_temp['B'])}}</th>
			<th class="class_amount" style="font-weight:bold">{{check_negative($totals_temp['A'] - $totals_temp['B'])}}</th>
		</tr>
		@endforeach

		<?php
			$cash_beg = array();
			$cash_beg['D'] = $beg+$totals['D'];

			$cash_end['B'] = $cash_beg['D']+$totals['B'];
			$cash_end['A'] = $cash_end['B']+$totals['A'];
		?>
		<tr>
			<th colspan="2"></th>
			<th colspan="2" class="text-left" style="font-weight:bold">Net increase/(decrease) in cash</th>
			<th class="class_amount" style="font-weight:bold">{{check_negative($totals['A'])}}</th>
			<th class="class_amount" style="font-weight:bold">{{check_negative($totals['B'])}}</th>
			<th class="class_amount" style="font-weight:bold">{{check_negative($totals['A']-$totals['B'])}}</th>
		</tr>

		<tr>
			<th colspan="2"></th>
			<th colspan="2" class="text-left" style="font-weight:bold">Cash balance beginning ({{$cash_beg_bal}})</th>
			<th class="class_amount" style="font-weight:bold">{{check_negative($cash_end['B'])}}</th>
			<th class="class_amount" style="font-weight:bold">{{check_negative($cash_beg['D'])}}</th>
			<th class="class_amount" style="font-weight:bold">{{check_negative(0)}}</th>
		<!-- 	<th class="class_amount">{{check_negative($totals['A'])}}</th> -->
		</tr>
		<tr class="by">
			<th colspan="4" class="text-left font-type" style="font-weight:bold">Cash balance end ({{$ending}})</th>
			<th class="class_amount" style="font-weight:bold">{{check_negative($cash_end['A'])}}</th>
			<th class="class_amount" style="font-weight:bold">{{check_negative($cash_end['B'])}}</th>
			<th class="class_amount" style="font-weight:bold">{{check_negative(0)}}</th>
		</tr>
	</tbody>
</table>  


