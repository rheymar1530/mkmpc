<table class="table table-bordered table-head-fixed tbl_pdc w-100" id="tbl_loan">
	<thead>
		<tr class="text-center">
			<th>BORROWER'S NAME</th>
			<th>LOAN TYPE</th>
			<th>AMOUNT</th>
		</tr>
	</thead>


	<?php if(count($loans) > 0): ?>
	<?php $__currentLoopData = $loans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id_member=>$loan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
	<tbody class="borders bmember hidden-member" data-member-id="<?php echo e($id_member); ?>">
		<?php
			$member_total = 0;
		?>
		<?php $__currentLoopData = $loan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c=>$lo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
		<tr class="rloan" data-loan="<?php echo e($lo->loan_token); ?>" data-id="<?php echo e($lo->id_loan); ?>" loan-due="<?php echo e($lo->current_due); ?>">
			<?php if($c == 0): ?>
			<td class="font-weight-bold nowrap" rowspan="<?php echo e(count($loan)); ?>"><i><?php echo e($lo->member); ?></i></td>
			<?php endif; ?>
			<td class="nowrap"><sup><a href="/loan/application/approval/<?php echo e($lo->loan_token); ?>" target="_blank">[<?php echo e($lo->id_loan); ?>] </a></sup><?php echo e($lo->loan_name); ?></td>

			<td class="in"><input class="form-control p-2 text-right txt-input-amount in-loan-due" value="<?php echo e(number_format($lo->loan_due,2)); ?>"></td>

			<!-- <td class="text-right "><?php echo e(number_format($lo->current_due,2)); ?></td> -->
			<?php 
				$GLOBALS['total'] += $lo->current_due; 
				$member_total += $lo->current_due;
			?> 
		</tr>
		<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
		<?php
			$GLOBALS['memtotal'][$id_member] = $member_total;
		?>
	</tbody>
	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
	<?php else: ?>
	<tr>
		<td class="text-center" colspan="3">No Data Found</td>
	</tr>
	<?php endif; ?>

</table>

<?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/repayment-statement/statement_table.blade.php ENDPATH**/ ?>