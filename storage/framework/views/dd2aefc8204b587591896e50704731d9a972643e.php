<div class="row">
	<div class="col-lg col-12 mb-3">
		<div class="card h-100">
			<div class="card-body pb-1">
				<div class="row">
					<div class="col-7">
						<label class="text-bold lbl_color text-lg mb-0">Revenue</label>
					</div>
					<div class="col-5 text-right lbl_color">
						<p class="mt-1 mb-0"><?php echo e($FILTER_MONTH); ?></p>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						
						<h4 class="lbl_color mb-0">₱<?php echo e(number_format($REVENUE_CURRENT_MONTH_AMOUNT,2)); ?></h4>
						<?php
						$percentage = $REVENUE_PERCENTAGE_DIFF;
						?>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<?php if($percentage != 0): ?>
						<span class="font-<?php echo e(($percentage < 0)?'down':'up'); ?> text-md">
							<i class="fas fa-arrow-<?php echo e(($percentage < 0)?'down':'up'); ?>"></i>&nbsp;<?php echo e(abs($percentage)); ?>% Since Last Month
						</span>
						<?php else: ?>
						<span class="text-muted text-md">
							<i>No Changes Since Last Month</i>
						</span>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg col-12 mb-3">
		<div class="card h-100">
			<div class="card-body pb-1">
				<div class="row">
					<div class="col-7">
						<label class="text-bold lbl_color text-lg mb-0">Expenses</label>
					</div>
					<div class="col-5 text-right lbl_color">
						<p class="mt-1 mb-0"><?php echo e($FILTER_MONTH); ?></p>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<h4 class="lbl_color mb-0">₱<?php echo e(number_format($EXPENSES_CURRENT_MONTH_AMOUNT,2)); ?></h4>
						<?php
						$percentage = $EXPENSES_PERCENTAGE_DIFF;
						?>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<?php if($percentage != 0): ?>
						<span class="font-<?php echo e(($percentage > 0)?'down':'up'); ?> text-md">
							<i class="fas fa-arrow-<?php echo e(($percentage < 0)?'down':'up'); ?>"></i>&nbsp;<?php echo e(abs($percentage)); ?>% Since Last Month
						</span>
						<?php else: ?>
						<span class="text-muted text-md">
							<i>No Changes Since Last Month</i>
						</span>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg col-12 mb-3">
		<div class="card h-100">
			<div class="card-body pb-1">
				<div class="row">
					<div class="col-7">
						<label class="text-bold text-lg lbl_color mb-0">Net Surplus</label>
					</div>
					<div class="col-5 text-right lbl_color">
						<p class="mt-1 mb-0"><?php echo e($FILTER_MONTH); ?></p>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<h4 class="lbl_color mb-0">₱<?php echo e(number_format($NET_SURPLUS_CURRENT_MONTH_AMOUNT,2)); ?></h4>
						<?php
						$percentage = $NET_SURPLUS_PERCENTAGE_DIFF;
						?>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<?php if($percentage != 0): ?>
						<span class="font-<?php echo e(($percentage < 0)?'down':'up'); ?> text-md">
							<i class="fas fa-arrow-<?php echo e(($percentage < 0)?'down':'up'); ?>"></i>&nbsp;<?php echo e(abs($percentage)); ?>% Since Last Month
						</span>
						<?php else: ?>
						<span class="text-muted text-md">
							<i>No Changes Since Last Month</i>
						</span>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-4 col-12 mb-3">
		<div class="card h-100">
			<div class="card-body pb-1">
				<div class="row">
					<div class="col-6 col-lg-6">
						<label class="text-bold lbl_color mb-0" style="font-size:1.2rem;line-height: 1.2rem">No of Loan Transactions</label>
					</div>
					<div class="col-6 col-lg-6 text-right">
						<p class="mt-0 mb-0 lbl_color"><?php echo e($FILTER_MONTH); ?></p>
					</div>
				</div>
				<div class="row">
					<div class="col-6">
						<h5 class="lbl_color mb-0 pt-2"><?php echo e($NO_LOAN_TRANSACTION); ?></h5>
					</div>
					<div class="col-6">
						<p class="lbl_color mb-0 text-md" >Total Principal</p>
						<p class="lbl_color mb-0 mt-n1" style="font-size:1.1rem">₱<?php echo e(number_format($TOTAL_PRINCIPAL,2)); ?></p>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-6 col-6">
						<?php
						$percentage = $NO_LOAN_TRANSACTION_PREVIOUS_PERCENTAGE;
						?>
						<?php if($percentage != 0): ?>
						<span class="font-<?php echo e(($percentage < 0)?'down':'up'); ?>" style="font-size: 0.9rem;">
							<i class="fas fa-arrow-<?php echo e(($percentage < 0)?'down':'up'); ?>"></i>&nbsp;<?php echo e(abs($percentage)); ?>% Since Last Month
						</span>
						<?php else: ?>
						<span class="text-muted text-md">
							<i>No Changes Since Last Month</i>
						</span>
						<?php endif; ?>
					</div>
					<div class="col-lg-6 col-6">
						<?php
						$percentage = $TOTAL_PRINCIPAL_PREV_PERCENTAGE;
						?>
						<?php if($percentage != 0): ?>
						<span class="font-<?php echo e(($percentage < 0)?'down':'up'); ?>" style="font-size: 0.9rem;">
							<i class="fas fa-arrow-<?php echo e(($percentage < 0)?'down':'up'); ?>"></i>&nbsp;<?php echo e(abs($percentage)); ?>% Since Last Month
						</span>
						<?php else: ?>
						<span class="text-muted text-md">
							<i>No Changes Since Last Month</i>
						</span>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/admin_dashboard/dashboard_stats.blade.php ENDPATH**/ ?>