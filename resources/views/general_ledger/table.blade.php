
<?php
$GLOBALS['export_type'] = $export_type ?? 1;


function check_amt($amt){

	$formated = ($GLOBALS['export_type']==1)?number_format($amt,2):$amt;
	return ($amt == 0)?'':$formated;
}

$g_total_debit = 0;
$g_total_credit = 0;
?>
@foreach($general_ledger as $account=>$gl)
<tr>
	<th colspan="7" style="text-align:left;font-weight:bold">{{$account}}</th>
</tr>
<?php
$total_debit = 0;
$total_credit = 0;
?>
@foreach($gl as $row)
<tr>
	<td></td>
	<td>{{$row->date}}</td>
	<td>{{$row->description}}</td>
	<td>{{$row->post_reference}}</td>
	<td class="class_amount"><?php echo check_amt($row->debit); ?></td>
	<td class="class_amount"><?php echo check_amt($row->credit); ?></td>
	<td>{{$row->remarks}}</td>
</tr>
<?php
$total_debit += $row->debit;
$total_credit += $row->credit;
?>
@endforeach
<tr style="text-align:left" class="add_border_bottom">
	<th colspan="4" style="font-weight:bold">TOTAL <span style="font-size:13px;font-weight: normal;">({{$gl[0]->ac_description}})</span></th>
	<th class="class_amount" style="font-weight:bold">{{check_amt($total_debit)}}</th>
	<th class="class_amount" style="font-weight:bold">{{check_amt($total_credit)}}</th>
	<th></th>
</tr>
<?php
	$g_total_debit += $total_debit;
	$g_total_credit += $total_credit;
?>
@endforeach


<tr style="text-align:left;" >
	<th colspan="4" class="row_g_total" style="font-weight:bold">GRAND TOTAL</th>
	<th class="class_amount row_g_total" style="font-weight:bold">{{check_amt($g_total_debit)}}</th>
	<th class="class_amount row_g_total" style="font-weight:bold">{{check_amt($g_total_credit)}}</th>
	<th></th>
</tr>
