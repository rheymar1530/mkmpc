<table  class="table tbl_loan repayment-pad" style="white-space: nowrap;margin-top: 5px;border: 1px solid black !important;display: none;">
	<tr class="table_header_dblue">
		<th colspan="4" class="center">LOAN AMORTIZATION</th>
	</tr>

	<tr>
		<td class="no_border pad-left" style="width: 15%;">Principal</td>
		<td class="col_amount no_border" style="width: 15%;"><?php echo e(number_format($loan['LOAN_TABLE'][0]['repayment_amount'],2)); ?></td>
		<td class="no_border" colspan="2"></td>
	</tr>	
	<?php if($loan['LOAN_TABLE'][0]['interest_amount'] > 0): ?>
	<tr>
		<td class="no_border pad-left">Interest</td>
		<td class="col_amount no_border"><?php echo e(number_format($loan['LOAN_TABLE'][0]['interest_amount'],2)); ?></td>
		<td class="no_border" colspan="2"></td>
	</tr>	
	<?php endif; ?>
	<?php if($loan['LOAN_TABLE'][0]['fees'] > 0): ?>
	<tr>
		<td class="no_border pad-left">Fees</td>
		<td class="col_amount no_border"><?php echo e(number_format($loan['LOAN_TABLE'][0]['fees'],2)); ?></td>
		<td class="no_border" colspan="2"></td>
	</tr>	
	<?php endif; ?>
	<tr>
		<th class="border-top-bottom xl-text pad-left" colspan="3">Total Amortization</th>
		<th class="border-top-bottom col_amount xl-text" colspan="1"><?php echo e(number_format($loan['LOAN_TABLE'][0]['total_due'],2)); ?></th>
	</tr>	

</table>

<div class="card c-border">
	<div class="card-header bg-gradient-primary2 py-1">
		<h5 class="text-center mb-0">Loan Amortization</h5>
	</div>
	<div class="card-body">
		<!-- <div class="text-center">
			<h5 class="lbl_color badge bg-light text-lg text-center">Loan Amortization</h5>
		</div> -->
		<table  class="table tbl_loan repayment-pad lbl_color" style="white-space: nowrap;margin-top: 5px;">
			<tr>
				<td class="no_border pad-left" style="width: 15%;">Principal</td>
				<td class="col_amount no_border" style="width: 15%;"><?php echo e(number_format($loan['LOAN_TABLE'][0]['repayment_amount'],2)); ?></td>
				<td class="no_border" colspan="2"></td>
			</tr>	
			<?php if($loan['LOAN_TABLE'][0]['interest_amount'] > 0): ?>
			<tr>
				<td class="no_border pad-left">Interest</td>
				<td class="col_amount no_border"><?php echo e(number_format($loan['LOAN_TABLE'][0]['interest_amount'],2)); ?></td>
				<td class="no_border" colspan="2"></td>
			</tr>	
			<?php endif; ?>
			<?php if($loan['LOAN_TABLE'][0]['fees'] > 0): ?>
			<tr>
				<td class="no_border pad-left">Fees</td>
				<td class="col_amount no_border"><?php echo e(number_format($loan['LOAN_TABLE'][0]['fees'],2)); ?></td>
				<td class="no_border" colspan="2"></td>
			</tr>	
			<?php endif; ?>
			<tr>
				<th class=" xl-text pad-left" colspan="3">Total Amortization</th>
				<th class=" col_amount xl-text" colspan="1"><?php echo e(number_format($loan['LOAN_TABLE'][0]['total_due'],2)); ?></th>
			</tr>	
		</table>
	</div>
</div>






<?php if(isset($AMTZ_SCHED)): ?>
<div class="card c-border">
	<div class="card-header bg-gradient-primary2 py-1">
		<h5 class="text-center mb-0">Amortization Table</h5>
	</div>
	<div class="card-body">
		<div class="table-responsive" style="overflow-x:auto;">
			<table  class="table tbl_loan repayment-pad lbl_color" style="white-space: nowrap;margin-top: -3px;">
				<thead>
					<tr>
						<th class="no_border center">Due Date</th>
						<th class="no_border center text-sm">Principal</th>
						<th class="no_border center">Interest</th>
						<th class="no_border center">Surcharge</th>
						<th class="no_border center">Total</th>
						<th class="no_border center">Principal Balance</th>
						<th class="no_border center"></th>
					</tr>	
				</thead>
				<?php
					$totalPrincipalPaid = 0;
				?>
				<?php $__currentLoopData = $AMTZ_SCHED; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $amtz): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<tr class="font-weight-bold">
						<td class="no_border"><?php echo e($amtz->date); ?> <?php if($amtz->accrued == 1): ?> <span class="badge badge-danger text-xs">Overdue</span> <?php endif; ?></td>
						<td class="no_border col_amount"><?php echo e(number_format($amtz->principal,2)); ?></td>
						<td class="no_border col_amount"><?php echo e(number_format($amtz->interest,2)); ?></td>
						<td class="no_border col_amount"><?php echo e(number_format($amtz->surcharge,2)); ?></td>
						<td class="no_border col_amount"><?php echo e(number_format($amtz->total,2)); ?></td>
						<td class="no_border"></td>
						<!-- <?php echo e(number_format(($service_details->principal_amount - $totalPrincipalPaid),2)); ?> -->
						<td class="no_border"></td>
					</tr>

					<?php if(isset($AMTZ_PAYMENT[$amtz->term_code])): ?>
						<!-- <tr class="row-payment">
							<td class="no_border center font-weight-bold">Payment Date</td>
							<td class="no_border center text-sm font-weight-bold" colspan="6">Payments</td>
						</tr>	 -->
						<?php 
							$tPaidPrincipal =0; $tPaidInterest = 0 ; $tPaidSurcharge = 0; $tPaidTotal = 0;
						?>
						<?php $__currentLoopData = $AMTZ_PAYMENT[$amtz->term_code]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<?php 
							$tPaidPrincipal += $payment->principal; 
							$tPaidInterest += $payment->interest ; 
							$tPaidSurcharge += $payment->surcharge; 
							$tPaidTotal += $payment->total;

							$totalPrincipalPaid += $payment->principal;
							$refID = ($payment->id_repayment > 0)? $payment->id_repayment : $payment->id_repayment_transaction;


							if($payment->payment_reference == ""){
								if($payment->id_repayment > 0){
									$linkRef = "/repayment-bulk/view/{$payment->id_repayment}";
								}else{
									$linkRef = "/repayment/view/{$payment->repayment_token}";
								}
							}


						?>
						<tr class="row-payment">
							<td class="text-sm no_border"><?php echo e($payment->date); ?> </td>
							<td class="text-sm no_border col_amount"><?php echo e(number_format($payment->principal,2)); ?></td>
							<td class="text-sm no_border col_amount"><?php echo e(number_format($payment->interest,2)); ?></td>
							<td class="text-sm no_border col_amount"><?php echo e(number_format($payment->surcharge,2)); ?></td>
							<td class="text-sm no_border col_amount"><?php echo e(number_format($payment->total,2)); ?></td>
							<td class="text-sm no_border col_amount"><?php echo e(number_format($service_details->principal_amount-$totalPrincipalPaid,2)); ?></td>
							<td class="text-sm no_border pl-4">
								<?php if($payment->payment_reference == ""): ?>
								<?php echo e($payment->or_no); ?> <sup><a href="<?php echo e($linkRef); ?>" target="_blank">[<?php echo e($refID); ?>]</a></sup>
								<?php else: ?>
								<?php echo e($payment->payment_reference); ?>

								<?php endif; ?>
							</td>
						</tr>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

						<tr class="row-payment" style="border-bottom: 1px solid;">
							<td class="text-sm no_border font-weight-bold">Balance</td>
							<td class="text-sm no_border col_amount"><?php echo e(number_format($amtz->principal-$tPaidPrincipal,2)); ?></td>
							<td class="text-sm no_border col_amount"><?php echo e(number_format($amtz->interest-$tPaidInterest,2)); ?></td>
							<td class="text-sm no_border col_amount"><?php echo e(number_format($amtz->surcharge-$tPaidSurcharge,2)); ?></td>
							<td class="text-sm no_border col_amount"><?php echo e(number_format($amtz->total-$tPaidTotal,2)); ?></td>
							<td class="text-sm no_border"></td>
							<td class="text-sm no_border"></td>
						</tr>
					<?php endif; ?>
			
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>		
			</table>
		</div>
	</div>
</div>
<?php endif; ?><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/loan/loan_amortization.blade.php ENDPATH**/ ?>