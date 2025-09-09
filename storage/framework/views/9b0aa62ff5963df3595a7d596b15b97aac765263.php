<?php if($exportMode <= 1): ?>
<?php
	function number_formats($val){
		return number_format($val,2);
	}

	$style = "";
?>
<?php else: ?>
<?php
	function number_formats($val){
		return $val;
	}
	$style = 'style="text-align:center"';
?>
<?php endif; ?>

<?php
	if($type == 1){
		// per borrower
		$groupFieldHead = "Borrower";
		$groupField = 'member';

		$referenceHead = "Loan Service";
		$reference = 'loan_service';
	}elseif($type >= 2){
		// per loan service
		$groupFieldHead = "Loan Service";
		$groupField = 'loan_service';

		$referenceHead = "Borrower";
		$reference = 'member';
	}else{
		// per Brgy
		$groupFieldHead = "Loan Service";
		$groupField = 'loan_service';

		$referenceHead = "Borrower";
		$reference = 'member';
	}
?>

<?php
	$ref = '';

	if($exportMode == 0){
		$ref = "href='#' onclick='view(this)'";
	}

	$rspanTotal = ($type == 1)?2:(($type==2)?1:2);
?>

<table class="table table-bordered table-head-fixed" id="tbl_overdues" width="100%">
	<?php if($exportMode == 1): ?>
	<colgroup>
		<col width="25%">
		<?php if($type == 1): ?>
		<col width="20%">
		<?php endif; ?>
	</colgroup>
	<?php endif; ?>
	<thead>
		<tr class="nb-bottom">
			<?php if($type == 1): ?>
			<th class="table_header_dblue" rowspan="2" <?php echo $style; ?>><b><?php echo e($groupFieldHead); ?></b></th>
			<?php endif; ?>
			<th class="table_header_dblue" rowspan="2" <?php echo $style; ?>><b><?php echo e($referenceHead); ?></b></th>

			<?php if($type == 3): ?>
			<th class="table_header_dblue" rowspan="2" <?php echo $style; ?>><b>LOAN SERVICE</b></th>
			<?php endif; ?>

			<th class="table_header_dblue" rowspan="2" <?php echo $style; ?>><b>PRINCIPAL <br>LOAN</b></th>
			<th class="table_header_dblue" rowspan="2" <?php echo $style; ?>><b>Loan Period</b></th>
			<th class="table_header_dblue" rowspan="2" <?php echo $style; ?>><b>Month<br>Elapsed</b></th>

			<th class="table_header_dblue" colspan="3" <?php echo $style; ?>><b>DUES AS OF <?php echo e(strtoupper($asOf)); ?></b></th>
			<th class="table_header_dblue" rowspan="2" <?php echo $style; ?>><b>TOTAL PAYMENT</b></th>
			<th class="table_header_dblue" rowspan="2" <?php echo $style; ?>><b>BALANCE</b></th>


		</tr>
		<tr class="nb-top">
			<th class="table_header_dblue" <?php echo $style; ?>><b>PRINCIPAL</b></th>
			<th class="table_header_dblue" <?php echo $style; ?>><b>INTEREST</b></th>
			<th class="table_header_dblue" <?php echo $style; ?>><b>TOTAL</b></th>
		</tr>
	</thead>
	
	<tbody>
		<?php
			$counter = 1;
			$totals = array();

			$sumField = [
				'principal_amount','principal_due','interest_due','total_due','total_payment','balance'
			];

			foreach($sumField as $f){
				$totals[$f] = 0;
			}
		?>
		<?php $__currentLoopData = $overdues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idMember=>$loans): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
		<?php if($type >= 2): ?>
		<tr>
			<td colspan="9"><b><?php echo e($idMember); ?></b></td>
		</tr>
		<?php endif; ?>
		<?php
			$lCount = ($type == 1)?count($loans):1;
			$Stotal = [];
			foreach($sumField as $f){
				$Stotal[$f] = 0;
			}
		?>
			<?php $__currentLoopData = $loans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c=>$loan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<?php
					$class= ($c == 0 && $type == 1)?'bb':'';
				?>
				<tr class="<?php echo e($class); ?>" tk="<?php echo e($exportMode==0?$loan->loan_token:0); ?>">
					<?php if($c == 0 && $type == 1): ?>
					<!-- <td rowspan="<?php echo e($lCount); ?>"><?php echo e($counter); ?>.</td> -->
					<td rowspan="<?php echo e($lCount); ?>"><?php echo e($loan->{$groupField}); ?></td>
					<?php endif; ?>
					<td><?php echo e($loan->{$reference}); ?> &nbsp;<?php if($type < 3): ?><a <?php echo $ref;?> ><sup>[<?php echo e($loan->id_loan); ?>]</sup></a><?php endif; ?></td>

					<?php if($type == 3): ?>
					<td><?php echo e($loan->loan_service); ?> <?php if($type == 3): ?><a <?php echo $ref;?> ><sup>[<?php echo e($loan->id_loan); ?>]</sup></a><?php endif; ?></td>
					<?php endif; ?>

					<td class="class_amount"><?php echo e(number_formats($loan->principal_amount)); ?></td>
					<td class="font-xs"><?php echo e(date("m/d/Y", strtotime($loan->start_period))); ?> to <?php echo e(date("m/d/Y", strtotime($loan->maturity_date))); ?></td>
					<td class="text-center"><?php echo e($loan->elapsed_month); ?></td>


					<td class="class_amount"><?php echo e(number_formats($loan->principal_due)); ?></td>
					<td class="class_amount"><?php echo e(number_formats($loan->interest_due)); ?></td>
					<td class="class_amount"><?php echo e(number_formats($loan->total_due)); ?></td>

					<td class="class_amount"><?php echo e(number_formats($loan->total_payment)); ?></td>
					<td class="class_amount"><?php echo e(number_formats($loan->balance)); ?></td>

				</tr>
				<?php
					foreach($sumField as $field){
						$Stotal[$field] += $loan->{$field};
						$totals[$field] += $loan->{$field};
					}
					$counter++;
				?>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

			<?php if(count($loans) > 1): ?>
				<?php if($type >= 2): ?>
				<tr class="bbottom">
					<td colspan="<?php echo e($rspanTotal); ?>" style="font-weight: bold">Sub-Total</td>
					<td class="class_amount" style="font-weight: bold"><?php echo e(number_formats($Stotal['principal_amount'])); ?></td>
					<td></td>
					<td></td>
					<td class="class_amount" style="font-weight: bold"><?php echo e(number_formats($Stotal['principal_due'])); ?></td>
					<td class="class_amount" style="font-weight: bold"><?php echo e(number_formats($Stotal['interest_due'])); ?></td>
					<td class="class_amount" style="font-weight: bold"><?php echo e(number_formats($Stotal['total_due'])); ?></td>
					<td class="class_amount" style="font-weight: bold"><?php echo e(number_formats($Stotal['total_payment'])); ?></td>

					<td class="class_amount" style="font-weight: bold"><?php echo e(number_formats($Stotal['balance'])); ?></td>

				</tr>
				<?php endif; ?>	
			<?php endif; ?>	
		<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
	</tbody>
	<tr class="btop">
		<td colspan="<?php echo e($rspanTotal); ?>" style="font-weight: bold">Grand Total</td>
		<td class="class_amount" style="font-weight: bold"><?php echo e(number_formats($totals['principal_amount'])); ?></td>
		<td></td>
		<td></td>
		<td class="class_amount" style="font-weight: bold"><?php echo e(number_formats($totals['principal_due'])); ?></td>
		<td class="class_amount" style="font-weight: bold"><?php echo e(number_formats($totals['interest_due'])); ?></td>
		<td class="class_amount" style="font-weight: bold"><?php echo e(number_formats($totals['total_due'])); ?></td>
		<td class="class_amount" style="font-weight: bold"><?php echo e(number_formats($totals['total_payment'])); ?></td>

		<td class="class_amount" style="font-weight: bold"><?php echo e(number_formats($totals['balance'])); ?></td>

	</tr>
</table><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/loan-deliquent/table.blade.php ENDPATH**/ ?>