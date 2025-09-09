<?php $__env->startSection('content'); ?>
<style type="text/css">
	.outline-left-side{
/*		border-left: 5px solid blue;*/
}
.class_amount{
	text-align: right;
	padding-right: 20px;
}
.main_amt{
	font-size: 20px;
}
.balance_totals{
	font-size:1rem;

}
.row_bal{
	border-top: 1px solid;
}
</style>
<!-- <div style="max-height: calc(100vh - 150px);overflow-y: auto;margin-top: 50px;"> -->

	<div class="card c-border m-3">
		<div class="card-body">
			<h5 class="head_lbl text-center">Loan Payments <small>(Loan ID# <?php echo e($LOAN_ID); ?>)</small></h5>
			<p class="lbl_color mb-0 text-md"><b>Total Paid Principal:</b> <?php echo e(number_format($details->paid_principal,2)); ?></p>
			<p class="lbl_color mb-0 text-md"><b>Total Paid Interest:</b> <?php echo e(number_format($details->paid_interest,2)); ?></p>
			<p class="lbl_color mb-0 text-md"><b>Total Paid Fees:</b> <?php echo e(number_format($details->paid_fees,2)); ?></p>
			
			<div style="overflow-y: auto;" class="mt-3">
				<div class="col-md-12 p-0">
					<?php $__currentLoopData = $repayment_transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $due=>$transactions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<div class="card c-border">
						<div class="card-body" style="overflow-x:auto;">

							<h5 class="lbl_color font-weight-bold"><u><?php echo e($due); ?> <?php if($transactions[0]->accrued == 1): ?><span class="badge bg-danger text-sm ml-2">Overdue</span> <?php endif; ?></u></h5>
							<table style="width:100%" class="lbl_color text-sm">
								<thead>
									<tr>
										<th width="10%">Reference</th>
										<th width="10%">Date</th>
										<th>Principal Amount</th>
										<th>Interest Amount</th>
										<th>Fees Amount</th>
										<th>Total Due</th>
									</tr>
									<?php
									$repayment_amount = $transactions[0]->repayment_amount;
									$interest_amount = $transactions[0]->interest_amount;
									$fees = $transactions[0]->fees;
									$total_due = $transactions[0]->total_due;
									?>
									<tr>
										<th></th>
										<th></th>
										<th class="class_amount main_amt"><?php echo e(number_format($transactions[0]->repayment_amount,2)); ?></th>
										<th class="class_amount main_amt"><?php echo e(number_format($transactions[0]->interest_amount,2)); ?></th>
										<th class="class_amount main_amt"><?php echo e(number_format($transactions[0]->fees,2)); ?></th>
										<th class="class_amount main_amt"><?php echo e(number_format($transactions[0]->total_due,2)); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trans): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

									<?php
									$total_repayment_dues = $trans->paid_principal+$trans->paid_interest+$trans->paid_fees;
									$repayment_amount = $repayment_amount-$trans->paid_principal;
									$interest_amount = $interest_amount-$trans->paid_interest;
									$fees = $fees-$trans->paid_fees;
									$total_due = $total_due-$total_repayment_dues;
									?>
									<tr>
										<td>
											<?php if($trans->repayment_reference == "-"): ?>
											-
											<?php else: ?>
											<?php if(MySession::isAdmin()): ?>
											<a href="/repayment/view/<?php echo e($trans->repayment_token); ?>" target="_blank"><?php echo e($trans->repayment_reference); ?></a>
											<?php else: ?>
											<a href="/payments/<?php echo e($trans->repayment_reference); ?>" target="_blank"><?php echo e($trans->repayment_reference); ?></a>
											<?php endif; ?>
											<?php endif; ?>

										</td>
										<td><?php echo e($trans->repayment_date); ?></td>
										<td class="class_amount"><?php echo e(number_format($trans->paid_principal*-1,2)); ?></td>
										<td class="class_amount"><?php echo e(number_format($trans->paid_interest*-1,2)); ?></td>
										<td class="class_amount"><?php echo e(number_format($trans->paid_fees*-1,2)); ?></td>
										<td class="class_amount"><?php echo e(number_format($total_repayment_dues*-1,2)); ?></td>
									</tr>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</tbody>
								<tfoot>
									<tr class="row_bal">
										<th colspan="2">Balance</th>
										<th class="class_amount balance_totals"><?php echo e(number_format($repayment_amount,2)); ?></th>
										<th class="class_amount balance_totals"><?php echo e(number_format($interest_amount,2)); ?></th>
										<th class="class_amount balance_totals"><?php echo e(number_format($fees,2)); ?></th>
										<th class="class_amount balance_totals"><?php echo e(number_format($total_due,2)); ?></th>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

				</div>
			</div>
		</div>
	</div>
	<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminLTE.admin_template_frame', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/loan/repayment_transaction_frame.blade.php ENDPATH**/ ?>