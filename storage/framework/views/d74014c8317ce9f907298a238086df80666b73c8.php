<style type="text/css">
	.tbl_loan  tr>td,.tbl_loan  tr>th{

		vertical-align:top;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 15px;
		/*font-weight: bold;*/
	}
	.xl-text{
		font-size: 18px !important;
	}
	.repayment-pad tr>td,.repayment-pad tr>th{
		padding:2px;
	}
	.loan-pad tr>td,.loan-pad tr>th{
		padding:1px;
		padding-right: 5px;
	}
	.border{
		border: 1px solid black !important;
	}
	.col_amount,.col_amount2{
		text-align: right;
	}
	.center{
		text-align: center;
	}
	.border-top-bottom{
		border-top: 1px solid black !important;
		border-bottom: 1px solid black !important;
	}
	.border-bottom{
		border-bottom: 1px solid black !important;
		border-top: none !important;
	}
	.border-top{
		border-top: 1px solid black !important;

	}
	.no_border{
		border: none !important;
	}
	.col_amount:before{
		content: '₱';
	}
	.pad-left{
		padding-left: 10px !important;
	}
	.badge_table{
		font-size: 15px;
	}
	.sm-text{
		color: red;
		font-size: 15px;
	}
	.sm-text2{
		color: green;
		font-size: 15px;
	}

	tr.row-payment td, tr.row-payment th{
		font-size: 0.85rem !important;
		font-style: italic !important;
	}
</style>

<div class="row">

	<?php if(isset($show_repayment)): ?>
	<?php if(isset($CURRENT_DUE) && $TOTAL_DUE > 0 && $service_details->lstatus != 2): ?>
	<div class="col-md-12">
		<div class="alert bg-gradient-warning2" role="alert">
			<h4 class="alert-heading">Notice:</h4>
			<!-- <p>Aww yeah, you successfully read this important alert message. This example text is going to run a bit longer so that you can see how spacing within an alert works with this kind of content.</p> -->
			<hr>
			<p class="mb-0">As of <?php echo e($CURRENT_DUE->cur_date); ?></p>
			<table class="mb-0">
				<?php if($CURRENT_DUE->prin > 0): ?>
				<tr>
					<td>Principal Due:</td>
					<td class="text-right"><?php echo e(number_format($CURRENT_DUE->prin,2)); ?></td>
				</tr>
				<?php endif; ?>
				<?php if($CURRENT_DUE->interest > 0): ?>
				<tr>
					<td>Interest Due:</td>
					<td class="text-right"><?php echo e(number_format($CURRENT_DUE->interest,2)); ?></td>
				</tr>
				<?php endif; ?>
				<?php if($CURRENT_DUE->fees > 0): ?>
				<tr>
					<td>Fees Due:</td>
					<td class="text-right"><?php echo e(number_format($CURRENT_DUE->fees,2)); ?></td>
				</tr>
				<?php endif; ?>
				<?php if($CURRENT_DUE->surcharge > 0): ?>
				<tr>
					<td>Surcharge Due:</td>
					<td  class="text-right">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo e(number_format($CURRENT_DUE->surcharge,2)); ?></td>
				</tr>
				<?php endif; ?>

				<tr style="border-top:1px solid;">
					<td>Total</td>
					<td>&nbsp;&nbsp;<?php echo e(number_format($TOTAL_DUE,2)); ?></td>
				</tr>
			</table>
		</div>
	</div>
	<?php endif; ?>
	<?php endif; ?>
	<div class="col-md-12">
		<?php
		$lt_max_index = count($loan['LOAN_TABLE'])-1;
		$month_duration = $service_details->month_duration ?? 1;
		?>
		<div class="card c-border">
			<div class="card-body">
				<div class="row">
					<div class="col-md-6 col-12">
						<p class="mb-0"><b class="lbl_color">Applicant Name: </b><?php echo e($member_details->name ?? $service_details->member_name); ?></p>
						<p class="mb-0"><b class="lbl_color">Loan Service: </b> <?php echo e($service_details->name); ?></p>
						<p class="mb-0"><B class="lbl_color">No of Loan Payment(s): </B> <?php echo e($loan['repaymentCount']); ?></p>
						<?php if(isset($show_repayment)): ?>
						<p class="mb-0"><B class="lbl_color">Loan Period:  </B> 
							<?php if($service_details->id_loan_payment_type == 1): ?>
				            <?php echo e($loan['LOAN_TABLE'][0]['due_date']); ?> - <?php echo e($loan['LOAN_TABLE'][$loan['repaymentCount']-1]['due_date']); ?>

				            <?php else: ?>
				            <?php echo e($service_details->date_granted); ?> - <?php echo e($loan['LOAN_TABLE'][$loan['repaymentCount']-1]['due_date']); ?>

           				 <?php endif; ?>
						</p>
						<?php endif; ?>
					</div>
					<div class="col-md-6 col-12">
						<p class="mb-0"><b class="lbl_color"><?php if($service_details->id_loan_payment_type == 1): ?>
							Terms: 
							<?php else: ?>
							Period:
						<?php endif; ?></b> <?php echo e($service_details->terms_desc); ?></p>
						<p class="mb-0"><b class="lbl_color">Interest: </b> <?php echo e($service_details->interest_rate); ?>%
							<?php if($month_duration > 1): ?>
							<small><i class="text-muted">(<?php echo e(number_format($service_details->interest_show,2)); ?>% x <?php echo e($month_duration); ?> months)</i></small>
							<?php endif; ?>
						</p>
						<?php if(isset($show_repayment)): ?>
						<p class="mb-0"><b class="lbl_color">Date Granted: </b> <?php echo e($service_details->date_granted); ?></p>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
		<div class="card c-border">
			<div class="card-header bg-gradient-primary2 py-1">
				<h5 class="text-center mb-0">Loan Proceeds Computation</h5>
			</div>
			<div class="card-body p-2">
				<!-- <div class="text-center">
					<h5 class="lbl_color badge bg-light text-lg text-center">Loan Proceeds Computation</h5>
				</div> -->

				<table  class="table tbl_loan loan-pad lbl_color" style="margin-bottom: unset!important;margin-top: -3px;" width="100%">

					<!-- LOAN AMOUNT -->
					<tr>
						<th class="xl-text pad-left no_border" colspan="3">Principal Amount</th>
						<th class="col_amount xl-text no_border" colspan="1"><?php echo e(number_format($loan['PRINCIPAL_AMOUNT'],2)); ?></th>
					</tr>
					<tr>
						<th colspan="4" class="pad-left no_border">Deductions:</th>
					</tr>	
					<?php $__currentLoopData = $loan['DEDUCTED_CHARGES']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $charges): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<!-- DEDUCTIONS LIST -->
					<tr>
						<td colspan="2" class="no_border pad-left"><?php echo e($charges['charge_complete_details']); ?></td>
						<td class="col_amount no_border"><?php echo e(number_format($charges['calculated_charge'],2)); ?></td>
						<td class="no_border"></td>
					</tr>	
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					<!-- BALANCE FROM THE CURRENT LOAN -->
					<?php if($loan['TOTAL_LOAN_BALANCE'] > 0): ?>
					<tr>
						<th colspan="4" class="pad-left">Balance from the current loans:</th>
					</tr>
					<tr>
						<td class="no_border pad-left">Loan balance</td>
						<td class="col_amount no_border"><?php echo e(number_format($loan['LOAN_BALANCE'],2)); ?></td>
						<td class="no_border" colspan="2"></td>
					</tr>	

					<?php if(($loan['SURCHARGE_BALANCE_RENEW'] ?? 0) > 0): ?>
					<tr>
						<td class="no_border pad-left">Surcharge</td>
						<td class="col_amount no_border"><?php echo e(number_format($loan['SURCHARGE_BALANCE_RENEW'],2)); ?></td>
						<td class="no_border" colspan="2"></td>
					</tr>	
					<?php endif; ?>

					<?php if(($loan['REBATES'] ?? 0) > 0): ?>
					<tr>
						<td class="no_border pad-left">Rebates on loan protection</td>
						<td class="col_amount no_border">(<?php echo e(number_format($loan['REBATES'],2)); ?>)</td>
						<td class="no_border" colspan="2"></td>
					</tr>
					<?php endif; ?>
					
					<tr>
						<td class="no_border pad-left" colspan="2">Remaining loan balance to pay</td>
						<td class="col_amount no_border"><?php echo e(number_format($loan['TOTAL_LOAN_BALANCE'],2)); ?></td>
						<td class="no_border"></td>
					</tr>
					<?php endif; ?>
					<?php if(isset($loan['PREV_LOAN_OFFSET'])): ?>
					<?php if(count($loan['PREV_LOAN_OFFSET']) > 0): ?>
					<tr>
						<th colspan="4" class="pad-left">Active Loan Payment:</th>
					</tr>
					<?php $__currentLoopData = $loan['PREV_LOAN_OFFSET']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<tr>
						<td class="no_border pad-left"><?php echo e($p->loan); ?></td>
						<td class="col_amount no_border"><?php echo e(number_format($p->payment,2)); ?></td>
						<td class="no_border" colspan="2"></td>
					</tr>	

					<?php if($p->rebates > 0): ?>
					<tr>
						<td class="no_border pad-left"><?php echo e($p->loan); ?> - REBATES</td>
						<td class="col_amount no_border">(<?php echo e(number_format($p->rebates,2)); ?>)</td>
						<td class="no_border" colspan="2"></td>
					</tr>	
					<?php endif; ?>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					<tr>
						<td class="no_border pad-left" colspan="2">Total Active Loan Payment</td>
						<td class="col_amount no_border"><?php echo e(number_format($loan['TOTAL_LOAN_OFFSET'],2)); ?></td>
						<td class="no_border"></td>
					</tr>
					<?php endif; ?>

					<?php endif; ?>


					<!-- TOTALS -->
					<tr>
						<th class="xl-text pad-left" colspan="3">Total Deductions</th>
						<th class="col_amount xl-text" colspan="1"><?php echo e(number_format($loan['TOTAL_DEDUCTED_CHARGES'],2)); ?></th>
					</tr>	

					<!-- TOTALS -->
					<tr>
						<th class="xl-text pad-left" colspan="3">Total Loan Proceeds</th>
						<th class="col_amount xl-text" colspan="1"><?php echo e(number_format($loan['TOTAL_LOAN_PROCEED'],2)); ?></th>
					</tr>
				</table>
			</div>
		</div>




		<table  class="table tbl_loan loan-pad" style="white-space: nowrap;margin-bottom: unset!important;margin-top: -3px;border: 1px solid black !important;display: none;">
			<tr class="table_header_dblue">
				<th colspan="4" class="center">LOAN PROCEEDS COMPUTATION</th>
			</tr>
			<!-- LOAN AMOUNT -->
			<tr>
				<th class="border-top-bottom xl-text pad-left" colspan="3">Principal Amount</th>
				<th class="border-top-bottom col_amount xl-text" colspan="1"><?php echo e(number_format($loan['PRINCIPAL_AMOUNT'],2)); ?></th>
			</tr>
			<tr>
				<th colspan="4" class="pad-left">Deductions:</th>
			</tr>	
			<?php $__currentLoopData = $loan['DEDUCTED_CHARGES']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $charges): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			<!-- DEDUCTIONS LIST -->
			<tr>
				<td colspan="2" class="no_border pad-left"><?php echo e($charges['charge_complete_details']); ?></td>
				<td class="col_amount no_border"><?php echo e(number_format($charges['calculated_charge'],2)); ?></td>
				<td class="no_border"></td>
			</tr>	
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			<!-- BALANCE FROM THE CURRENT LOAN -->
			<?php if($loan['TOTAL_LOAN_BALANCE'] > 0): ?>
			<tr>
				<th colspan="4" class="border-top pad-left">Balance from the current loans:</th>
			</tr>
			<tr>
				<td class="no_border pad-left">Loan balance</td>
				<td class="col_amount no_border"><?php echo e(number_format($loan['LOAN_BALANCE'],2)); ?></td>
				<td class="no_border" colspan="2"></td>
			</tr>	
			<tr>
				<td class="no_border pad-left">Rebates on loan protection</td>
				<td class="col_amount no_border">(<?php echo e(number_format($loan['REBATES'],2)); ?>)</td>
				<td class="no_border" colspan="2"></td>
			</tr>
			<tr>
				<td class="no_border pad-left" colspan="2">Remaining loan balance to pay</td>
				<td class="col_amount no_border"><?php echo e(number_format($loan['TOTAL_LOAN_BALANCE'],2)); ?></td>
				<td class="no_border"></td>
			</tr>
			<?php endif; ?>
			<?php if(isset($loan['PREV_LOAN_OFFSET'])): ?>
			<?php if(count($loan['PREV_LOAN_OFFSET']) > 0): ?>
			<tr>
				<th colspan="4" class="border-top pad-left">Active Loan Payment:</th>
			</tr>
			<?php $__currentLoopData = $loan['PREV_LOAN_OFFSET']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			<tr>
				<td class="no_border pad-left"><?php echo e($p->loan); ?></td>
				<td class="col_amount no_border"><?php echo e(number_format($p->payment,2)); ?></td>
				<td class="no_border" colspan="2"></td>
			</tr>	

			<?php if($p->rebates > 0): ?>
			<tr>
				<td class="no_border pad-left"><?php echo e($p->loan); ?> - REBATES</td>
				<td class="col_amount no_border">(<?php echo e(number_format($p->rebates,2)); ?>)</td>
				<td class="no_border" colspan="2"></td>
			</tr>	
			<?php endif; ?>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			<tr>
				<td class="no_border pad-left" colspan="2">Total Active Loan Payment</td>
				<td class="col_amount no_border"><?php echo e(number_format($loan['TOTAL_LOAN_OFFSET'],2)); ?></td>
				<td class="no_border"></td>
			</tr>
			<?php endif; ?>

			<?php endif; ?>

			<!-- TOTALS -->
			<tr>
				<th class="border-top-bottom xl-text pad-left" colspan="3">Total Deductions</th>
				<th class="border-top-bottom col_amount xl-text" colspan="1"><?php echo e(number_format($loan['TOTAL_DEDUCTED_CHARGES'],2)); ?></th>
			</tr>	

			<!-- TOTALS -->
			<tr>
				<th class="border-top-bottom xl-text pad-left" colspan="3">Total Loan Proceeds</th>
				<th class="border-top-bottom col_amount xl-text" colspan="1"><?php echo e(number_format($loan['TOTAL_LOAN_PROCEED'],2)); ?></th>
			</tr>
		</table>

		<?php echo $__env->make('loan.loan_amortization', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

		<?php if(isset($loan['REPAYMENT_TABLE'])): ?>
		<?php echo $__env->make('loan.repayment_table', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
		<?php endif; ?>


		<?php if(isset($active_multiple) && count($active_multiple) > 0): ?>
		<?php echo $__env->make('loan.active_multiple', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
		<?php endif; ?>
	</div>
</div>
<?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/loan/loan_table.blade.php ENDPATH**/ ?>