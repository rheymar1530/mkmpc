<table  class="table tbl_loan repayment-pad" style="white-space: nowrap;margin-top: 5px;border: 1px solid black !important;display: none;">
	<tr class="table_header_dblue">
		<th colspan="4" class="center">ACTIVE <?php echo e($service_details->name); ?> BALANCE</th>
	</tr>
	<?php $__currentLoopData = $active_multiple; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $am): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
	<tr>
		<td class="no_border pad-left" style="width: 10%;"><a href="/loan/application/approval/<?php echo e($am->loan_token); ?>" target="_blank">ID# <?php echo e($am->id_loan); ?></a></td>
		<td class="col_amount no_border" style="width: 15%;"><?php echo e(number_format($am->balance,2)); ?></td>
		<td class="no_border" colspan="2"></td>
	</tr>
	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>	
</table>

<div class="card c-border">
	<div class="card-header bg-gradient-primary2 py-1">
		<h5 class="text-center mb-0">Active <?php echo e($service_details->name); ?> Balance</h5>
	</div>
	<div class="card-body">
		<!-- <div class="text-center">
			<h5 class="lbl_color badge bg-light text-lg text-center">Active <?php echo e($service_details->name); ?> Balance</h5>
		</div> -->
		<table  class="table tbl_loan repayment-pad" style="white-space: nowrap;margin-top: 5px;">
			<?php $__currentLoopData = $active_multiple; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $am): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			<tr>
				<td class="no_border pad-left" style="width: 10%;"><a href="/loan/application/approval/<?php echo e($am->loan_token); ?>" target="_blank">Loan ID# <?php echo e($am->id_loan); ?></a></td>
				<td class="col_amount no_border" style="width: 15%;"><?php echo e(number_format($am->balance,2)); ?></td>
				<td class="no_border" colspan="2"></td>
			</tr>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>	
		</table>
	</div>
</div><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/loan/active_multiple.blade.php ENDPATH**/ ?>