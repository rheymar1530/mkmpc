
<?php
$GLOBALS['export_type'] = $export_type ?? 1;

function check_amt($amt){

	$formated = ($GLOBALS['export_type']==1)?number_format($amt,2):$amt;
	return ($amt == 0)?'':$formated;
}


?>

<?php
$total_debit = 0;
$total_credit = 0;
?>
@foreach($general_ledger as $row)
<tr>
	<td>{{$row->account}}</td>
	<td class="class_amount"><?php echo check_amt($row->debit); ?></td>
	<td class="class_amount"><?php echo check_amt($row->credit); ?></td>
</tr>
<?php
$total_debit += $row->debit;
$total_credit += $row->credit;
?>
@endforeach
<tr style="text-align:left" class="add_border_bottom">
	<th colspan="1" class="row_g_total" style="font-weight:bold">GRAND TOTAL</th>
	<th class="class_amount row_g_total" style="font-weight:bold">{{check_amt($total_debit)}}</th>
	<th class="class_amount row_g_total" style="font-weight:bold">{{check_amt($total_credit)}}</th>
</tr>

