<style type="text/css">
	.descripition_col{
		width: 4cm !important;
	}
	.name_col{
		width: 4cm !important;
	}
	.center {
		margin-left: auto;
		margin-right: auto;
	}
	.pad_left{
		padding-right: 10px !important;
		/*color: red;*/
	}

	.f-total{
		font-size: 18px !important;
	}
	.bold-text{
		font-weight: bold !!important;
	}
</style>

<?php
$GLOBALS['export_type'] = $export_type ?? 1;
function check_zero($val){
	$f = ($GLOBALS['export_type'] == 1)?number_format($val,2):$val;
	return ($val == 0)?'':$f;
}
function check_zero2($val){
	$f = ($GLOBALS['export_type'] == 1)?number_format($val,2):$val;
	return ($val == 0)?'-':$f;
}


?>
<?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type=>$transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php
$total_credit = 0;
$total_debit = 0;

$total_loan_deb = 0;
$total_loan_cred = 0;
?>
<div class="box">
	<table class="table table-bordered table-stripped table-head-fixed table-hover tbl_accounts tbl_gl center" style="margin-top:20px;width:80% !important;">
		<thead>
			<tr style="text-align:center;" class="table_header_dblue_cust">
				<th class="table_header_dblue_cust" colspan="4" style="text-align:center;font-size: 17px"><?php echo e(strtoupper($type)); ?></th>
			</tr>
			<tr style="text-align:center;border-bottom:1px solid" class="table_header_dblue_cust">
				<th style="width:1cm"></th>
				<th>Account</th>
				<!-- <th style="width: 6cm;">Remarks</th> -->
				<th style="width: 3.5cm;">Debit</th>
				<th  style="width: 3.5cm;">Credit</th>
			</tr>
		</thead>
		

			<?php $__currentLoopData = $transaction; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account=>$row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			<?php if(count($row) == 1): ?>
			<?php $__currentLoopData = $row; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			<tr>
				<td colspan="2"><?php echo e($item->account); ?></td>
				<!-- <td></td> -->
				<td class="class_amount"><?php echo e(check_zero($item->debit)); ?></td>
				<td class="class_amount pad_left"><?php echo e(check_zero($item->credit)); ?></td>
			</tr>
			<?php
			$total_debit += $item->debit;
			$total_credit += $item->credit;
			?>

			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			<?php else: ?>
			<tr>
				<td colspan="4"><?php echo e($row[0]->account); ?></td>
			</tr>
			<?php $__currentLoopData = $row; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			<tr>
				<td></td>
				<td><?php echo e($item->reference); ?></td>
				<td class="class_amount"><?php echo e(check_zero($item->debit)); ?></td>
				<td class="class_amount pad_left"><?php echo e(check_zero($item->credit)); ?></td>
				<?php
					$total_loan_deb += $item->debit;
					$total_loan_cred += $item->credit;

					$total_debit += $item->debit;
					$total_credit += $item->credit;
				?>
			</tr>

			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			<tr>
				<td colspan="2">&nbsp;&nbsp;</td>
				<td class="class_amount ovrline"><?php echo e(check_zero($total_loan_deb)); ?></td>
				<td class="class_amount pad_left ovrline" ><?php echo e(check_zero($total_loan_cred)); ?></td>
			</tr>

			<?php endif; ?>

			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

			<tr class="row_total">
				<th class="f-total bold-text" style="text-align: left !important;font-weight: bold;font-size: 12px;" colspan="2">Total</th>
				<th class="class_amount f-total ovrline" style="font-weight: bold;font-size: 12px;"><?php echo e(check_zero2($total_debit)); ?></th>
				<th class="class_amount f-total pad_left ovrline" style="font-weight: bold;font-size: 12px;"><?php echo e(check_zero2($total_credit)); ?></th>
			</tr>
	</table>  
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/transaction_summary/table_account.blade.php ENDPATH**/ ?>