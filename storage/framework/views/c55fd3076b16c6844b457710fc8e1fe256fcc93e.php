
<?php
$GLOBALS['export_type'] = $export_type ?? 1;


function check_amt($amt){

	$formated = ($GLOBALS['export_type']==1)?number_format($amt,2):$amt;
	return ($amt == 0)?'':$formated;
}

$g_total_debit = 0;
$g_total_credit = 0;
?>
<?php $__currentLoopData = $general_ledger; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account=>$gl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<tr>
	<th colspan="7" style="text-align:left;font-weight:bold"><?php echo e($account); ?></th>
</tr>
<?php
$total_debit = 0;
$total_credit = 0;
?>
<?php $__currentLoopData = $gl; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<tr>
	<td></td>
	<td><?php echo e($row->date); ?></td>
	<td><?php echo e($row->description); ?></td>
	<td><?php echo e($row->post_reference); ?></td>
	<td class="class_amount"><?php echo check_amt($row->debit); ?></td>
	<td class="class_amount"><?php echo check_amt($row->credit); ?></td>
	<td><?php echo e($row->remarks); ?></td>
</tr>
<?php
$total_debit += $row->debit;
$total_credit += $row->credit;
?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<tr style="text-align:left" class="add_border_bottom">
	<th colspan="4" style="font-weight:bold">TOTAL <span style="font-size:13px;font-weight: normal;">(<?php echo e($gl[0]->ac_description); ?>)</span></th>
	<th class="class_amount" style="font-weight:bold"><?php echo e(check_amt($total_debit)); ?></th>
	<th class="class_amount" style="font-weight:bold"><?php echo e(check_amt($total_credit)); ?></th>
	<th></th>
</tr>
<?php
	$g_total_debit += $total_debit;
	$g_total_credit += $total_credit;
?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


<tr style="text-align:left;" >
	<th colspan="4" class="row_g_total" style="font-weight:bold">GRAND TOTAL</th>
	<th class="class_amount row_g_total" style="font-weight:bold"><?php echo e(check_amt($g_total_debit)); ?></th>
	<th class="class_amount row_g_total" style="font-weight:bold"><?php echo e(check_amt($g_total_credit)); ?></th>
	<th></th>
</tr>
<?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/general_ledger/table.blade.php ENDPATH**/ ?>