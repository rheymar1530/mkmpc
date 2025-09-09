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
<?php $__currentLoopData = $lists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat=>$list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
	<tr style="">
		<td style="width: 0.5cm;" class="row_total"></td>
		<td colspan="<?php echo e(count($headers)+2+$comp_header_col); ?>" style="font-weight:bold" class="row_total">
			<?php if($GLOBALS['export_type'] == 1): ?>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
			<?php else: ?>
				                          
			<?php endif; ?>
		<?php echo e($cat); ?></td>
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
	<?php $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vals): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			<tr>
				<td colspan="2"></td>
				<?php $__currentLoopData = $vals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<?php if(!in_array($key,$field_exclude)): ?>
					<?php if(in_array($key,$field_not_amount)): ?>
					<td style="padding-left: 0.7cm !important"><?php echo e($col); ?></td>
					<?php else: ?>					
					<?php
						$totalCat[$key] += $col;
						$total_line[$key] += $col;
						$GLOBALS['totalType'][$type][$key] += $col;

					?>
					<td class="class_amount col_border <?php echo e(td_class($col)); ?>"><?php echo e(check_negative($col)); ?></td>
					<?php endif; ?>
					<?php endif; ?>

				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

				<?php if($comparative_type == 1): ?>
					<?php if($date_type == 1): ?>
					<td class="class_amount col_border <?php echo e(td_class($vals->A-$vals->B)); ?>"><?php echo e(check_negative($vals->A-$vals->B)); ?></td>
					<?php
					
						$totalCat['LAST_MONTH'] +=$vals->A-$vals->B;
						$total_line['LAST_MONTH'] +=$vals->A-$vals->B;
						$GLOBALS['totalType'][$type]['LAST_MONTH']+=$vals->A-$vals->B;
					?>
					<?php endif; ?>
					<?php
						$totalCat['LAST_YEAR'] +=$vals->A-$vals->C;
						$total_line['LAST_YEAR'] +=$vals->A-$vals->C;
						$GLOBALS['totalType'][$type]['LAST_YEAR']+=$vals->A-$vals->C;
					?>
					<td class="class_amount col_border <?php echo e(td_class($vals->A-$vals->C)); ?>"><?php echo e(check_negative($vals->A-$vals->C)); ?></td>	
				<?php endif; ?>
			</tr>
		<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
	<tr style="border-bottom: 1px solid;" class="row_total">
		<td></td>
		<td style="font-weight:bold;padding-left:0.7cm" colspan="2">Total <?php echo e($cat); ?></td>
			<?php $__currentLoopData = $totalCat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$tot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			<td class="class_amount col_border <?php echo e(td_class($tot)); ?>" style="font-weight: bold;"><?php echo e(check_negative($tot)); ?></td>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
	</tr>

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

	<tr style="border-top: 1px solid;border-bottom: 1px solid;" class="row_total">
		<td></td>
		<td style="font-weight:bold;padding-left:0.7cm" colspan="2">Total <?php echo e($line); ?></td>
			<?php $__currentLoopData = $total_line; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$tot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			<td class="class_amount col_border <?php echo e(td_class($tot)); ?>" style="font-weight: bold;"><?php echo e(check_negative($tot)); ?></td>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
	</tr>
<?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/financial_statement/non-current.blade.php ENDPATH**/ ?>