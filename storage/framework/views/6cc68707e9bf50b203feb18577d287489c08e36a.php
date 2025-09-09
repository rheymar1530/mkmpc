<style type="text/css">
.dbl_undline {
	text-decoration-line: underline !important;
	text-decoration-style: double !important;
}

.tbl_gl  u{
	text-decoration: none !important;

}
.pad_head td, .pad_head th{

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
}
.font-emp{
	font-size: 15px !important;
}
</style>

<?php if(($export_type ?? 1) == 1): ?>
<?php
	function format_num($num) {

		if($num == 0){
			return '0.00';
		}
    	return $num < 0 ? '(' . number_format(abs($num),2) . ')' : number_format($num,2);
	}
?>

<?php else: ?>
<?php
	function format_num($num) {
		if($num == 0){
			return '0.00';
		}

		return $num;
    	return $num < 0 ? '(' .abs($num).')' : $num;
	}
?>

<?php endif; ?>


<!-- borderless  -->
<table class="table borderless table-head-fixed table-hover tbl_accounts tbl_gl mt-1">

	<?php if(($export_type ?? 1) == 1): ?>
	<colgroup>
		<col width="3%">
		<col width="3%">
	</colgroup>
	<?php endif; ?>

	<tbody>
		<?php if(($export_type ?? 1) == 2): ?>
		<tr>
			<td colspan="6" style="text-align: center;"><b><?php echo e(config('variables.coop_name')); ?></b></td>
		</tr>
		<tr>
			<td colspan="6" style="text-align: center;"><?php echo e(config('variables.coop_address')); ?></td>
		</tr>
			<tr>
			<td colspan="6" style="text-align: center;">CDA Registration No. <?php echo e(config('variables.fs_cda_reg_no')); ?></td>
		</tr>
			<tr>
			<td colspan="6" style="text-align: center;">Registration Date: <?php echo e(config('variables.fs_reg_date')); ?></td>
		</tr>
			<tr>
			<td colspan="6" style="text-align: center;"><b><?php echo e(strtoupper($RPTType)); ?></b></td>
		</tr>
			<tr>
			<td colspan="6" style="text-align: center;"><?php echo e($DateDesc); ?></td>
		</tr>
		<?php endif; ?>

		<?php
			$ColExpTotals = [];
		?>

		<?php $__currentLoopData = $report_output['entries']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $OType=>$datas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
		<?php
			$tempTotal = 0;
		?>
		<tr>
			<td colspan="6"><b><?php echo e($OType); ?></b></td>
		</tr>

			<?php $__currentLoopData = $datas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat=>$catData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			<tr>
				<td></td>
				<td colspan="5"><b><?php echo e($catCode[$cat]); ?></b></td>
			</tr>
				<?php
					// dd($catData);
					$catDataLength = count($catData) - 1;
					$subTotal = collect($catData)->sum('amount');
					$tempTotal += $subTotal;

					
					// dd($DataCount);

				?>
				<?php if($cat != "CRTx"): ?>
					<?php $__currentLoopData = $catData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c=>$cd): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<?php
						$class=($c == $catDataLength)?'b-bottom':'';
					?>
						<tr class="<?php echo e($class); ?>">
							<td colspan="2"></td>
							<td colspan="2"><?php echo e($cd->description); ?></td>
							<td class="text-right"><?php echo e(format_num($cd->amount)); ?></td>
							<td class="text-right">
								<?php if($c == $catDataLength): ?>
								<b><?php echo e(format_num($subTotal)); ?></b>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				<?php else: ?>
					<?php $__currentLoopData = $catData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c=>$cd): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<tr>
							<td colspan="2"></td>
							<td>CRV # <?php echo e($cd->id_cash_receipt_voucher); ?></td>
							<td class="text-sm"><?php echo e($cd->description); ?></td>
							<td class="text-right"><?php echo e(format_num($cd->amount)); ?></td>
							<td class="text-right">
								<?php if($c == $catDataLength): ?>
								<b><?php echo e(format_num($subTotal)); ?></b>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>				
				<?php endif; ?>

			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>		
			<!-- Sub Total -->
			<?php
				$ColExpTotals[$OType] = $tempTotal;
			?>
			<tr class="b-bottom">
				<td colspan="5"><b>Total <?php echo e($OType); ?></b></td>
				<td class="text-right"><b><?php echo e(format_num($tempTotal)); ?></b></td>
			</tr>

		<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
	</tbody>
	<?php
		$adjustmentTotal = 0;
	?>
	<?php if(count($report_output['adjustments']) > 0 || count($report_output['adjustmentsAccount']) > 0): ?>
	<?php
		$adjustmentTotal = (collect($report_output['adjustments'])->sum('amount') ?? 0) + (collect($report_output['adjustmentsAccount'])->sum('amount') ?? 0);
	?>
	<tbody id="body-adjustments">
		<tr>
			<td colspan="6"><b>Adjustments (JV)</b>&nbsp;Increase/(Decrease)</td>
		</tr>
		<?php $__currentLoopData = $report_output['adjustments']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ro): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
		<tr>
			<td colspan="2"></td>
			<td colspan="2"><?php echo e($ro->description); ?></td>
			<td class="text-right"><?php echo e(format_num($ro->amount)); ?></td>
			<td></td>
		</tr>
		<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


		<?php $__currentLoopData = $report_output['adjustmentsAccount']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ro): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
		<tr>
			<td colspan="2"></td>
			<td colspan="2"><?php echo e($ro->description); ?></td>
			<td class="text-right"><?php echo e(format_num($ro->amount)); ?></td>
			<td></td>
		</tr>
		<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

		<tr class="bx">
			<td colspan="5"><b>Total Adjustments</b></td>
			<td class="text-right"><b><?php echo e(format_num($adjustmentTotal)); ?></b></td>
		</tr>
	</tbody>
	<?php endif; ?>




	<!-- NET CASH -->
	<?php
		$netCash = ROUND(($ColExpTotals['Collection'] ?? 0) - ($ColExpTotals['Disbursement'] ?? 0) + $adjustmentTotal ,2)
	?>
	<tr>
		<td colspan="5"><b>Net Cash</b></td>
		<td class="text-right"><b><?php echo e(format_num($netCash)); ?></b></td>
	</tr>
	<?php
		$cash = $report_output['Cash'];
		$i = 1;
	?>
	<tbody>
		<?php if(isset($cash['Cash on Hand'])): ?>
			<?php $__currentLoopData = $cash['Cash on Hand']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<tr class="cash">
					<td colspan="2" class="text-right"><?php echo e(($i==1)?'Add:':''); ?></td>
					<td colspan="2"><?php echo e($c->description); ?> (<?php echo e(($report_output['asOf'])); ?>)</td>
					<td class="text-right"><?php echo e(format_num($c->amount)); ?></td>
					<td></td>
				</tr>
				<?php $i++; ?>
					
				
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
		<?php endif; ?>

		<?php if(isset($cash['Cash in Bank'])): ?>
			<tr>
				<td colspan="2" class="text-right"><?php echo e(($i==1)?'Add:':''); ?></td>
				<td colspan="4">Cash in Bank (<?php echo e(($report_output['asOf'])); ?>)</td>
			</tr>
			<?php 
				$maxCount = count($cash['Cash in Bank']) - 1;
				$i++; 
			?>
			<?php $__currentLoopData = $cash['Cash in Bank']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $counter=>$c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<tr class="cash">
					<td colspan="2" class="text-right"></td>
					<td></td>
					<td><?php echo e($c->description); ?></td>
					<td class="text-right"><?php echo e(format_num($c->amount)); ?></td>
					<td class="text-right">
						<?php if($counter == $maxCount): ?>
							<?php echo e(format_num($report_output['TotalCash'])); ?>

						<?php endif; ?>
					</td>
				</tr>
				
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
		<?php endif; ?>
	</tbody>

	<?php
		$totalCheck = collect($report_output['Checks'] ?? [])->sum('amount');
	?>
	<?php if(count($report_output['Checks']) > 0): ?>
	<?php
		$check = $report_output['Checks'];

	?>
	<tr>
		<td class="text-right" colspan="2">Less:</td>
		<td colspan="2">Check on hand</td>
		<td></td>
		<td class="text-right"><?php echo e(format_num($check[0]->amount)); ?></td>
	</tr>
	<?php endif; ?>






	
	<?php
		$totalCash = $netCash + ($report_output['TotalCash'] ?? 0) - $totalCheck;
	?>
	<tr class="bx">
		<td colspan="5"><b>Total Cash (<?php echo e($currentAsOf); ?>)</b></td>
		<td class="text-right"><b><?php echo e(format_num($totalCash)); ?></b></td>
	</tr>

</table><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/treasurer_report/table.blade.php ENDPATH**/ ?>