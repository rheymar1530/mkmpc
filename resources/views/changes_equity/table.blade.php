@if($export_type == 1)
<style type="text/css">
	.dbl_undline {
		text-decoration-line: underline !important;
		text-decoration-style: double !important;
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
.bt{
	border-top: 1px solid;
}
.div_totals{
	border-top:1px solid;
	margin-left:0.7cm !important;
	width:4cm;
	font-weight: bold;
}
.div_gtotals{
	border-bottom:1px solid;
	margin-left:0.7cm !important;
	width:4cm;
	font-weight: bold;
}
.dbl_underline{
	text-decoration-line: underline !important;
  	text-decoration-style: double !important;
}

</style>

@endif
<?php
$GLOBALS['export_type'] = $export_type ?? 1;
function check_negative($val){
	if($GLOBALS['export_type'] == 1){
		$col_val = number_format(abs($val),2);
	}else{
		$col_val = $val;
	}
	


	if($val == 0){
		return '-';
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
$g_total = array(
	'A'=>0,
	'B'=>0
);

?>
<table class="table borderless table-head-fixed table-hover tbl_accounts tbl_gl" style="margin-top:13px">
	<thead>
		<tr class="by">
			<th colspan="2"></th>
			<th class="text-center" style="width:5cm !important">{{$head_label['A']}}</th>
			
			<th class="text-center"  style="width:5cm !important">{{$head_label['B']}}</th>
			<th style="width:5cm !important">Difference</th>
		</tr>
	</thead>
	<tbody>
		@foreach($equity as $type=>$items)
		<tr>
			<th class="text-left font-type" colspan="5" style="font-weight:bold">{{$type}}</th>
		</tr>

		@if($type == "SHARE CAPITAL")
		<tr>
			<td style="width: 1cm;"></td>
			<td>Beginning Balance</td>
			<td class="class_amount">{{check_negative($items[0]->current_beg)}}</td>
			<td class="class_amount" >{{check_negative($items[0]->prev_beg)}}</td>
			<td class="class_amount" >{{check_negative($items[0]->current_beg-$items[0]->prev_beg)}}</td>
		</tr>
		<tr>
			<td style="width: 1cm;"></td>
			<td>Donation and Grants</td>
			<td class="class_amount">{{check_negative(0)}}</td>
			<td class="class_amount">{{check_negative(0)}}</td>
			<td class="class_amount"></td>
		</tr>
		<tr>
			<td style="width: 1cm;"></td>
			<td>Add Additional Capital Share</td>
			<td class="class_amount">{{check_negative($items[0]->current_add)}}</td>
			<td class="class_amount">{{check_negative($items[0]->prev_add)}}</td>
			<td class="class_amount">{{check_negative($items[0]->current_add-$items[0]->prev_add)}}</td>
		</tr>
		<tr>
			<td style="width: 1cm;"></td>
			<td>Less Withdrawals in Capital Share</td>
			<td class="class_amount">{{check_negative($items[0]->current_adj)}}</td>
			<td class="class_amount">{{check_negative($items[0]->prev_adj)}}</td>
			<td class="class_amount">{{check_negative($items[0]->current_adj-$items[0]->prev_adj)}}</td>
		</tr>
		<tr>
			<td style="width: 1cm;"></td>
			<td style="font-weight:bold;">Total Share Capital</td>
			<td class="class_amount" style="font-weight:bold;"><div class="div_totals">{{check_negative($items[0]->current_total)}}</div></td>
			<td class="class_amount" style="font-weight:bold;"><div class="div_totals">{{check_negative($items[0]->prev_total)}}</div></td>
			<td class="class_amount" style="font-weight:bold;"><div class="div_totals">{{check_negative($items[0]->current_total-$items[0]->prev_total)}}</div></td>
		</tr>

		<?php
		$g_total['A'] += $items[0]->current_total;
		$g_total['B'] += $items[0]->prev_total;
		?>

		@else
		@foreach($items as $item)
		<tr>
			<th class="text-left font-type" colspan="5" style="font-weight:bold;">{{strtoupper($item->description)}}</th>
		</tr>	
		<tr>
			<td style="width: 1cm;"></td>
			<td>Beginning Balance</td>
			<td class="class_amount">{{check_negative($item->current_beg)}}</td>
			<td class="class_amount">{{check_negative($item->prev_beg)}}</td>
			<td class="class_amount">{{check_negative($item->current_beg-$item->prev_beg)}}</td>
		</tr>
		<tr>
			<td style="width: 1cm;"></td>
			<td colspan="4">Add (Less)</td>
		</tr>
		<tr>

			<td style="width: 1cm;"></td>
			<td class="pl-3">Provisions</td>
			<td class="class_amount">{{check_negative($item->current_add)}}</td>
			<td class="class_amount">{{check_negative($item->prev_add)}}</td>
			<td class="class_amount">{{check_negative($item->current_add-$item->prev_add)}}</td>
		</tr>
		<tr>

			<td style="width: 1cm;"></td>
			<td class="pl-3">Deductions/Adj</td>
			<td class="class_amount">{{check_negative($item->current_adj)}}</td>
			<td class="class_amount">{{check_negative($item->prev_adj)}}</td>
			<td class="class_amount">{{check_negative($item->current_adj-$item->prev_adj)}}</td>
		</tr>
		<tr>
			<td style="width: 1cm;"></td>
			<td colspan="1" style="font-weight:bold;">Ending Balance</td>
			<td class="class_amount" style="font-weight:bold;"><div class="div_totals">{{check_negative($item->current_total)}}</div></td>
			<td class="class_amount" style="font-weight:bold;"><div class="div_totals">{{check_negative($item->prev_total)}}</div></td>
			<td class="class_amount" style="font-weight:bold;"><div class="div_totals">{{check_negative($item->current_total-$item->prev_total)}}</div></td>
		</tr>
		<?php
			$g_total['A'] += $item->current_total;
			$g_total['B'] += $item->prev_total;
		?>

		@endforeach
		@endif
		<tr>
			<th colspan="5">&nbsp;</th>
		</tr>
		@endforeach
		<tr>
			<th class="text-left font-type" colspan="2" style="font-weight:bold;">TOTAL EQUITY</th>
			<td class="class_amount font-type" style="font-weight:bold;"><div class="div_gtotals">{{check_negative($g_total['A'])}}</div></td>
			<td class="class_amount font-type" style="font-weight:bold;"><div class="div_gtotals">{{check_negative($g_total['B'])}}</div></td>
			<td class="class_amount font-type" style="font-weight:bold;"><div class="div_gtotals">{{check_negative($g_total['A']-$g_total['B'])}}</div></td>
		</tr>	
	</tbody>
</table>  


