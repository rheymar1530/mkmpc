<?php $__env->startSection('content'); ?>
<style type="text/css">
	.separator{
		margin-top: -0.5em;
	}
	.badge_term_period_label{
		font-size: 20px;
	}

	.main_form{
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif !important;

	}
	.charges_text{
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif !important;
	}
	.text_undeline{
		text-decoration: underline;
		font-size: 20px;
	}
	.tbl_loans tr>th ,.tbl_fees tr>th,.tbl-inputs tr>th{
		padding: 5px;
		padding-left: 5px;
		padding-right: 5px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 14px;
		text-align: center;
	}
	.tbl-inputs-text{
		padding-left: 5px !important;
		padding-right: 5px !important;
		/*padding: px !important;*/
	}
	.tbl_loans tr>td,.tbl_fees tr>td,.tbl_repayment_display tr>td{
		padding: 3px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 15px;
	}
	.tbl-inputs tr>td{
		padding: 0px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 15px;
	}
	.frm_loans,.frm-requirements{
		height: 27px !important;
		width: 100%;    
		font-size: 13px;
	}

	.class_amount{
		text-align: right;

	}
	.cus-font{
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 15px !important;       
	}
	.form-row  label{
		margin-bottom: unset !important;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 15px;
	}
	.form-label{
		margin-bottom: 4px !important;
	}

	.modal-conf {
		max-width:98% !important;
		min-width:98% !important;

	}
	.text_center{
		text-align: center;
	}
	.text_bold{
		font-weight: bold;
	}
	.spn_t{
		font-weight: bold;
		font-size: 16px;
	}
	.spn_txt{
		word-wrap:break-word;
		overflow: hidden;
		text-align: right;
	}
	.label_totals{
		margin-top: -13px !important;
	}
	.border-top{
		border-top:2px solid !important; 
	}

	.text-red{
		color: red;
	}
	.swal2-deny{
		padding: 0.375rem 0.75rem !important;
	}
	.foot_loan{
		text-align:center;background: #808080;color: white;
	}
	.col_payment_type{
		text-align: left !important;
	}
	.badge_loan_type{
		font-size: 14px;
	}
</style>
<?php
$transaction_type = [
	4=>"Check",
	1=>"Cash",
	2=>"ATM Swipe",
	3=>"Payroll"
];

?>
<div class="wrapper2">
	<div class="container-fluid main_form" style="margin-top: -20px;" >
		<?php $back_link = (request()->get('href') == '')?'/repayment':request()->get('href'); ?>
		<a class="btn bg-gradient-secondary btn-sm" href="<?php echo e($back_link); ?>" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Loan Payment Transaction List</a>
		<?php
		$is_cancelled = ($repayment_transaction->status >=10)?true:true;
		?>

		<div class="card" id="repayment_main_div">
			<div class="card-body col-md-12">
				<h3>Loan Payment (Loan Payment ID# <?php echo e($repayment_transaction->id_repayment_transaction); ?>)</h3>
				<div class="row">
					<div class="col-md-12 row">	
						<div class="col-md-12">
							<div class="form-row">
								<div class="form-group col-md-3" style="margin-top:20px">
									<label for="txt_transaction_date">Transaction Date</label>
									<input type="date" name="" class="form-control" value="<?php echo e($repayment_transaction->transaction_date ?? $current_date); ?>" id="txt_transaction_date" disabled>
								</div>
								<div class="form-group col-md-3" style="margin-top:20px">
									<label for="sel_transaction_type">Payment Mode</label>
									<select class="form-control p-0" id="sel_transaction_type" disabled>
										<?php $__currentLoopData = $transaction_type; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val=>$text): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<?php
										$selected = ($opcode == 1 && $repayment_transaction->transaction_type == $val)?"selected":"";
										?>
										<option value="<?php echo e($val); ?>" <?php echo e($selected); ?>><?php echo e($text); ?></option>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									</select>
								</div>
							</div>
							<div class="form-row">
								<div class="form-group col-md-5">
									<label for="txt_description">Borrower</label>
									<select class="form-control select2" id="sel_borrower" disabled>
										
										<option value="<?php echo e($repayment_transaction->id_member); ?>"><?php echo e($repayment_transaction->selected_member); ?></option>

									</select>
								</div>

							</div>
							<div class="form-row">
								<div class="form-group col-md-5">
									<label for="txt_description">Due Date</label>
									<select class="form-control p-0" id="txt_date" disabled>
										
										<option value="<?php echo e($repayment_transaction->date); ?>"><?php echo e($repayment_transaction->due_date_text); ?></option>
										
									</select>
									<!-- <input type="date" name="" class="form-control" value="<?php echo e($repayment_transaction->date ?? $current_date); ?>" id="txt_date"> -->
								</div>
							</div>
							<?php if($repayment_transaction->transaction_type == 2): ?>
							<div class="form-row" id="div_swiping_amount_holder">
								<div class="form-group col-md-4" id="div_swiping_amount">
									<label for="txt_description">Swiping Amount</label>
									<input type="text" name="" required class="form-control class_amount" id="swiping_amount" value="<?php echo e(number_format(($repayment_transaction->swiping_amount ?? 0),2)); ?>" disabled>
								</div>
							</div>
							<?php endif; ?>
						</div>
						<div class="col-md-12" style="margin-top:20px">
							<div class="form-group p-0" style="margin-bottom:unset;">
	
									<table class="table table-bordered table-stripped table-head-fixed tbl-inputs table-hover" style="" width="100%">
										<thead>
											
											<tr>
												<th class="table_header_dblue">Loan Paid</th>
												<th class="table_header_dblue">Principal</th>
												<th class="table_header_dblue">Interest</th>
												<th class="table_header_dblue">Fees</th>
												<th class="table_header_dblue">Surcharges/Penalty</th>
												<th class="table_header_dblue">Total Payment</th>
											</tr>

										</thead>
										<tbody id="loan_dues_body">
											<?php 
											$total_loan_paid = 0 ;
											$total_loan_due = 0 ;
											$total_other = 0;
											$total_penalties = 0;
											$swiping_amount = $repayment_transaction->swiping_amount;


											$total_prin=0;$total_in=0;$total_fee=0;$total_surcharges =0;
											?>

											<?php $__currentLoopData = $active_loans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $act): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
											<tr>
												<?php

												$total_prin += $act->paid_principal;
												$total_in += $act->paid_interest;
												$total_fee += $act->paid_fees;

												$total_surcharges += $act->surcharges;
												$row_total = $act->paid_principal+$act->paid_interest+$act->paid_fees+$act->surcharges;

												$total_loan_paid += $row_total;
												?>
												<td>ID#<a href="/loan/application/approval/<?php echo e($act->loan_token); ?>"><?php echo e($act->id_loan); ?></a> <?php echo e($act->loan_name); ?></td>
												<td class="class_amount"><?php echo e(number_format($act->paid_principal,2)); ?></td>
												<td class="class_amount"><?php echo e(number_format($act->paid_interest,2)); ?></td>
												<td class="class_amount"><?php echo e(number_format($act->paid_fees,2)); ?></td>
												<td class="class_amount"><?php echo e(number_format($act->surcharges,2)); ?></td>
												<td class="tbl-inputs-text class_amount"><?php echo e(number_format($row_total,2)); ?></td>
											</tr>

											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

											<?php
												$total_paid_loan = $total_prin+$total_in+$total_fee;
											?>
											
										</tbody>
										<tfoot>
											<tr class="foot_loan">
												<td class="tbl-inputs-text text_bold">TOTAL</td>
												<td class="class_amount text_bold tbl-inputs-text"><?php echo e(number_format($total_prin,2)); ?></td>
												<td class="class_amount text_bold tbl-inputs-text"><?php echo e(number_format($total_in,2)); ?></td>
												<td class="class_amount text_bold tbl-inputs-text"><?php echo e(number_format($total_fee,2)); ?></td>
												<td class="class_amount text_bold tbl-inputs-text"><?php echo e(number_format($total_surcharges,2)); ?></td>

												
												<td class="class_amount text_bold tbl-inputs-text"><?php echo e(number_format($total_loan_paid,2)); ?></td>
											</tr>
										</tfoot>
									</table>    
					
							</div>

						</div>

						<div class="col-md-12" style="margin-top:10px">
							<div class="row">
								<div class="col-md-8">
									<div class="col-md-12 p-0">
										<h5>Other Charges&nbsp;&nbsp;</h5>
										<div class="table-responsive" style="max-height: calc(100vh - 200px);overflow-y: auto;margin-top: 5px !important;overflow-x: auto">
											<table class="table table-bordered table-stripped table-head-fixed tbl-inputs" style="white-space: nowrap;" id="tbl_penalty">
												<thead>
													<tr>
														<th class="table_header_dblue" width="8%"></th>
														<th class="table_header_dblue" width="50%">Penalty Type</th>
														<th class="table_header_dblue">Amount</th>

													</tr>
												</thead>
												<tbody id="repayment_penalty_body">
													<?php if(count($repayment_penalty) > 0): ?>
													<?php $__currentLoopData = $repayment_penalty; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c=>$rp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<tr class="row_penalty">
														<td class="col-count text_center"><?php echo e($c+1); ?></td>
														<td class="tbl-inputs-text"><?php echo e($rp->description); ?></td>
														<td class="tbl-inputs-text class_amount"><?php echo e(number_format($rp->amount,2)); ?></td>
													</tr>
													<?php 
													$total_penalties += $rp->amount ?? 0; 
													?>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													<?php else: ?>
													<tr class="row_no_penalty"><td colspan="4" style="text-align:center">No Loan Payment Penalty</td></tr>
													<?php endif; ?>
												</tbody>
											</table>	
										</div>
									</div>
									<div class="col-md-8 p-0">
										<h5>Other Fees</h5>
										<div class="table-responsive" style="max-height: calc(100vh - 200px);overflow-y: auto;margin-top: 5px !important;overflow-x: auto">
											<table class="table table-bordered table-stripped table-head-fixed tbl-inputs" style="white-space: nowrap;">
												<thead>
													<tr>
														<th class="table_header_dblue" width="8%"></th>
														<th class="table_header_dblue">Fee</th>
														<th class="table_header_dblue">Amount</th>
													</tr>
												</thead>
												<tbody id="loan_fee_display">
													<?php $__currentLoopData = $repayment_fee_val; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $count=>$rf): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<tr class="row_fees" data-id="<?php echo e($rf->id_payment_type); ?>">
														<td class="text_center"><?php echo e($count+1); ?></td>
														<td class="tbl-inputs-text" style="padding-left: 10px"><?php echo e($rf->description); ?></td>
														<td class="tbl-inputs-text class_amount"><?php echo e(number_format($rf->amount,2)); ?></td>
													</tr>
													<?php 
													$total_other += $rf->amount ?? 0; 
													?>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</tbody>
											</table>
										</div>
									</div>
								</div>
								<?php
								$total_paid_amount  = $total_loan_paid+$total_other+$total_penalties;
								$change = $swiping_amount - $total_paid_amount;
								?>
								<div class="col-md-4">
									<div class="card">
										<div class="card-body">
											<h5><u>Loan Payment Summary</u></h5>
											<table style="width:100%">
<!-- 												<tr>
													<td class="spn_t">Total Loan Paid:</td>
													<td class="class_amount" width="30%" id="spn_total_loan_amount_due"><?php echo e(number_format($total_loan_paid,2)); ?></td>
													<td></td>
												</tr> -->
												<?php if(!$is_cancelled): ?>
												<tr>
													<td class="spn_t">Loan Amount Due:</td>
													<td class="class_amount" width="30%" id="spn_total_loan_amount_due">0.00</td>
													<td></td>
												</tr>
												<?php endif; ?>
												<!-- <tr>
													<td class="spn_t">Others Fees:</td>
													<td class="class_amount" width="30%" id="spn_total_loan_fees"><?php echo e(number_format($total_other,2)); ?></td>
													<td></td>
												</tr>
												<tr>
													<td class="spn_t">Loan Penalty:</td>
													<td class="class_amount" width="30%" id="spn_total_loan_penalty"><?php echo e(number_format($total_penalties,2)); ?></td>
													<td></td>
												</tr> -->
												<?php if(!$is_cancelled): ?>
												<tr>
													<td class="spn_t">Total Amount Due:</td>
													<td class="class_amount" width="30%" id="spn_total_amount_due">0.00</td>
													<td></td>
												</tr>
												<?php endif; ?>
												<?php if($repayment_transaction->transaction_type == 2): ?>
												<tr class="row_swiping_amount">
													<td class="spn_t">Swiping Amount:</td>
													<td class="class_amount" width="30%" id="spn_swiping_amount" style="font-weight:bold"><?php echo e(number_format($swiping_amount,2)); ?></td>
													<td></td>
												</tr>
												<?php endif; ?>
											<!-- 	<tr>
													<td class="spn_t">Total Paid Amount:</td>
													<td class="class_amount" width="30%" id="spn_total_paid_amount"><?php echo e(number_format($total_paid_amount,2)); ?></td>
													<td></td>
												</tr> -->
												<tr>
													<td class="spn_t">Payments:</td>
													<td class="class_amount" width="30%"></td>
													<td></td>
												</tr>
												<tr>
													<td class="spn_t"><span style="font-weight: normal !important;padding-left: 15px;">Loan Dues</span></td>
													<td class="class_amount" width="30%" id="spn_loan_dues"><?php echo e(number_format($total_paid_loan,2)); ?></td>
													<td></td>
												</tr>
												<tr>
													<td class="spn_t"><span style="font-weight: normal !important;padding-left: 15px;">Loan Penalty/Surcharges</span></td>
													<td class="class_amount" width="30%" id="spn_surcharges"><?php echo e(number_format($total_surcharges,2)); ?></td>
													<td></td>
												</tr>
												<tr>
													<td class="spn_t"><span style="font-weight: normal !important;padding-left: 15px;">Other Fees and Charges</span></td>
													<td class="class_amount" width="30%" id="spn_oth_fee_charg"><?php echo e(number_format(($total_other+$total_penalties),2)); ?></td>
													<td></td>
												</tr>
												<tr style="border-top: 1px solid;">
													<td class="spn_t" ></td>
													<td class="class_amount" width="30%" id="spn_total_paid_amount" style="font-weight:bold"><?php echo e(number_format($total_paid_amount,2)); ?></td>
													<td></td>
												</tr>
												<?php if($repayment_transaction->transaction_type == 2): ?>
												<tr class="row_swiping_amount">
													<td class="spn_t">Change:</td>
													<td class="class_amount" width="30%" id="spn_change"><?php echo e(number_format($change,2)); ?></td>
													<td></td>
												</tr>
												<?php endif; ?>
											</table>
										</div>
									</div>
									<?php if(isset($repayment_change) && count($repayment_change) > 0): ?>
									<div class="card">
										<div class="card-body">
											<h5><u>Change Released</u></h5>
											<table style="width:100%">

												<tr>
													<th>Ref#</th>
													<th>Date</th>
													<th>Amount</th>
												</tr>
												<?php $total_change = 0; ?>
												<?php $__currentLoopData = $repayment_change; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<tr>
													<td><?php echo e($rc->id_repayment_change); ?></td>
													<td><?php echo e($rc->date); ?></td>
													<td class="class_amount"><?php echo e(number_format($rc->amount,2)); ?></td>
												</tr>
												<?php $total_change += $rc->amount;?>
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												<tr style="border-top:1px solid;">
													<th colspan="2">Total</th>
													<th class="class_amount"><?php echo e(number_format($total_change,2)); ?></th>
												</tr>
												<tr>
													<th colspan="2">Remaining Change</th>
													<th class="class_amount"><?php echo e(number_format(($repayment_transaction->change-$total_change),2)); ?></th>
												</tr>
											</table>
										</div>
									</div>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>	
					<div class="col-md-12" style="margin-top:10px">

						<fieldset class="border" style="padding-left: 10px;padding-right: 10px;padding-bottom: 10px;">
							<legend class="w-auto p-1">Loan Payment Remarks</legend>
							<textarea class="form-control" rows="3" style="resize:none;" id="txt_remarks" disabled><?php echo e($repayment_transaction->remarks ?? ''); ?></textarea>
						</fieldset>					
					</div>
					<?php if($repayment_transaction->status >= 10): ?>
					<div class="col-md-12" style="margin-top:10px">
						<fieldset class="border" style="padding-left: 10px;padding-right: 10px;padding-bottom: 10px;">
							<legend class="w-auto p-1">Cancellation Reason</legend>
							<textarea class="form-control" rows="3" style="resize:none;" id="txt_remarks" disabled><?php echo e($repayment_transaction->cancel_reason ?? ''); ?></textarea>
						</fieldset>					
					</div>
					<?php endif; ?>
				</div>
			</div>
			
			<div class="card-footer">
				<?php if($opcode == 1): ?>
				<?php if($repayment_transaction->transaction_type ==2): ?>
				<button type="button" class="btn bg-gradient-danger float-right" style="margin-right:10px" onclick="print_page('/journal_voucher/print/<?php echo e($repayment_transaction->id_journal_voucher); ?>')"><i class="fas fa-print" ></i>&nbsp;Print Journal Voucher (JV #<?php echo e($repayment_transaction->id_journal_voucher); ?>)</button>
				<?php else: ?>
				<button type="button" class="btn bg-gradient-danger float-right" style="margin-right:10px" onclick="print_page('/cash_receipt_voucher/print/<?php echo e($repayment_transaction->id_cash_receipt_voucher); ?>')"><i class="fas fa-print" ></i>&nbsp;Print CRV (CRV #<?php echo e($repayment_transaction->id_cash_receipt_voucher); ?>)</button>
				<?php endif; ?>
				<?php endif; ?>
			</div>
			
		</div>
	</div>
</div>


<?php echo $__env->make('global.print_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>



<?php $__env->stopSection(); ?>


<?php echo $__env->make('adminLTE.admin_template', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/repayment/repayment_view.blade.php ENDPATH**/ ?>