<?php $__env->startSection('content'); ?>
<style type="text/css">
	.tbl_pdc th, .tbl_pdc td{
		padding: 0.2rem;
		font-size: 0.8rem;
	}
	.tbl_pdc td.in{
		padding: 0;
	}
	td.in input{
		height: 27px !important;
		font-size: 0.8rem;
	}
	.tbl_pdc th{
		padding: 0.4rem;
		font-size: 0.8rem;
	}
	.selected_rp{
		background: #ccffcc;
	}
	.borders{
		border-top: 3px solid !important;
		border-bottom: 3px solid !important;
	}
	td input.txt-input-amount{
		border: 2px solid !important;
	}
	.nowrap{
		white-space: nowrap;
	}
	.vcenter{
/*		vertical-align: middle !important;*/
	}

</style>
<?php
	function status_class($status){
		switch ($status) {
			case 0:
				return 'primary';
				break;
			case 1:
				return 'success';
				break;
			case 10:
				return 'danger';
				break;
			default:
				// code...
				break;
		}
	}


	if($details->id_paymode == 4){
		$PaymentOBJ = array(
			'Check Type'=>'check_type',
			'Check Bank'=>'check_bank',
			'Check Date'=>'check_date',
			'Check No'=>'check_no',
			'Amount'=>'amount',
			'Remarks'=>'remarks'
		);
		$PColCount = 5;
	}else{

	}

	$totalPayment = 0;
?>
<div class="container-fluid">
	<?php $back_link = (request()->get('href') == '')?'/repayment-bulk':request()->get('href'); ?>
	<a class="btn btn-default btn-sm round_button" href="<?php echo e($back_link); ?>" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Loan Payment (Bulk) List</a>


	<?php if($details->status == 0 && $details->deposit_status == 0): ?>
	<a class="btn bg-gradient-primary2 btn-sm round_button float-right" href="/repayment-bulk/edit/<?php echo e($details->id_repayment); ?>?href=<?php echo e($back_link); ?>" style="margin-bottom:10px"><i class="fas fa-edit"></i>&nbsp;&nbsp;Edit Loan Payment</a>
	<?php endif; ?>

	<?php if($details->id_cash_receipt_voucher > 0 && $details->change_payable > 0): ?>
		<a class="btn bg-gradient-danger2 btn-sm round_button float-right mr-2" onclick="print_page('/cash_receipt_voucher/print/<?php echo e($details->id_cash_receipt_voucher); ?>')"><i class="fa fa-print"></i>&nbsp;Print Change CRV (<?php echo e($details->id_cash_receipt_voucher); ?>)</a>
	<?php endif; ?>
	<a class="btn bg-gradient-warning2 btn-sm round_button float-right mr-2" onclick="printOR('<?php echo e($details->id_repayment); ?>')" style="margin-bottom:10px"><i class="fas fa-print"></i>&nbsp;&nbsp;Print OR</a>

	<button class="btn bg-gradient-info btn-sm round_button float-right mr-2" onclick="print_page('/repayment-bulk/export/<?php echo e($details->id_repayment); ?>')"><i class="fa fa-print"></i>&nbsp;Print Loan Payment</button>
	<div class="card">
		<div class="card-body">
			<div class="text-center">
				<h4 class="head_lbl">Loan Payment <small>(ID# <?php echo e($details->id_repayment); ?>)</small>
						<span class="badge badge-<?php echo e(status_class($details->status)); ?>"><?php echo e($details->status_description); ?></span>
				</h4>
			</div>

			<div class="card c-border">
				<div class="card-body">
					<div class="row">
						<div class="col-lg-12 col-12">
							
						</div>
						<div class="col-lg-6 col-12">
							<div class="d-flex flex-column">
								
								<span class="text-sm  font-weight-bold lbl_color">Date: <span class="ms-sm-2 font-weight-normal ml-2"><?php echo e($details->date); ?></span></span>
								<span class="text-sm  font-weight-bold lbl_color">OR Number: <span class="ms-sm-2 font-weight-normal ml-2"><?php echo e($details->or_number); ?></span></span>
								
								<span class="text-sm  font-weight-bold lbl_color">Paymode: <span class="ms-sm-2 font-weight-normal ml-2"><?php echo e($details->paymode); ?></span></span>
							
								
							</div>
						</div>
							<div class="col-lg-6 col-12">
								<div class="d-flex flex-column">
									<span class="text-sm  font-weight-bold lbl_color">Total Amount: <span class="ms-sm-2 font-weight-normal ml-2"><?php echo e(number_format($details->total_amount,2)); ?></span></span>
									<span class="text-sm  font-weight-bold lbl_color">Remarks: <span class="ms-sm-2 font-weight-normal ml-2"><?php echo e($details->remarks); ?></span></span>
							</div>
						</div>
					</div>

				</div>
			</div>	
			<?php
				$total = 0;
			?>			
			<table class="table table-bordered table-head-fixed tbl_pdc w-100" id="tbl_loan">
				<thead>
					<tr class="text-center">
						<th>Member</th>
						<th>Loan Service</th>
		
						<th>Payment</th>
						<th width="10%"></th>
						
					</tr>
				</thead>
				<?php if($details->payment_for_code == 2): ?>


					<?php $__currentLoopData = $statamentData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $statementDescription=>$MemberData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<tr class="statement-head bg-gradient-success2 font-weight-bold">
						<td colspan="4"><?php echo e($statementDescription); ?></td>
					</tr>

						<?php $__currentLoopData = $MemberData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m=>$data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<?php $length = count($data);?>
							<?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c=>$row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

							<tr>
								<?php if($c == 0): ?>
								<td class="font-weight-bold nowrap td-mem" rowspan="<?php echo e($length); ?>"><i><?php echo e($row->member); ?> </i></td>
								<?php endif; ?>
								<td><sup><a href="/loan/application/approval/<?php echo e($row->loan_token); ?>" target="_blank">[<?php echo e($row->id_loan); ?>] </a></sup><?php echo e($row->loan_name); ?></td>
								<td class="text-right"><?php echo e(number_format($row->payment,2)); ?></td>
								<?php $total += $row->payment; ?>
								<?php if($c == 0): ?>
								<td class="font-weight-bold text-center" rowspan="<?php echo e($length); ?>">
									<?php if($row->id_cash_receipt_voucher  > 0): ?>
									<a class="btn btn-xs bg-gradient-danger h-100" onclick="print_page('/cash_receipt_voucher/print/<?php echo e($row->id_cash_receipt_voucher); ?>')"><i class="fa fa-print"></i>&nbsp;Print CRV (<?php echo e($row->id_cash_receipt_voucher); ?>)</a>
									<?php endif; ?>
								</td>
								<?php endif; ?>
							</tr>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

				<?php else: ?>
					<?php $__currentLoopData = $Loans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member=>$rows): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<?php
						$length = count($rows);
					?>
						<?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c=>$row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<tr>
							<?php if($c == 0): ?>
							<td class="font-weight-bold nowrap td-mem" rowspan="<?php echo e($length); ?>"><i><?php echo e($row->member); ?> </i></td>
							<?php endif; ?>
							<td><sup><a href="/loan/application/approval/<?php echo e($row->loan_token); ?>" target="_blank">[<?php echo e($row->id_loan); ?>] </a></sup> <?php echo e($row->loan_name); ?></td>
							<td class="text-right"><?php echo e(number_format($row->payment,2)); ?></td>
							<?php $total += $row->payment; ?>
							<?php if($c == 0): ?>
								<td class="font-weight-bold text-center" rowspan="<?php echo e($length); ?>">
									<?php if($row->id_cash_receipt_voucher  > 0): ?>
									<a class="btn btn-xs bg-gradient-danger h-100" onclick="print_page('/cash_receipt_voucher/print/<?php echo e($row->id_cash_receipt_voucher); ?>')"><i class="fa fa-print"></i>&nbsp;Print CRV (<?php echo e($row->id_cash_receipt_voucher); ?>)</a>
									<?php endif; ?>
								</td>
							<?php endif; ?>
						</tr>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

				<?php endif; ?>
				<tfoot>
					<tr>
						<th colspan="2" class="footer_fix text-left font-weight-normal" style="text-align:center;background: #808080 !important;color: white;"></th>
						<th class="footer_fix text-right td-total-payment"  style="background: #808080 !important;color: white;"><?php echo e(number_format($total,2)); ?></th>
						<th class="footer_fix text-left font-weight-normal" style="text-align:center;background: #808080 !important;color: white;"></th>
						
					</tr>
				</tfoot>
			</table>

			<?php if($details->id_paymode ==4): ?>
			<div class="row mt-4">
				<div class="col-md-12">
					<table class="table table-bordered table-head-fixed tbl_pdc w-100" id="tbl_loan">
						<thead>
							<tr class="text-center">
								<th></th>
								<?php $__currentLoopData = $PaymentOBJ; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $head=>$key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<th><?php echo e($head); ?></th>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

								
								<th>Deposit Details</th>
								
							</tr>
						</thead>
						<tbody>
							<?php $__currentLoopData = $paymentDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i=>$pd): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<tr>
								<td class="text-center"><?php echo e($i+1); ?></td>
								<?php $__currentLoopData = $PaymentOBJ; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<td class="<?php echo ($key=='amount')?'text-right':''; ?>" >
									<?php if($key=='amount'): ?>
									<?php echo e(number_format($pd->{$key},2)); ?>

									<?php
									$totalPayment += $pd->{$key};
									?>
									
									<?php else: ?>
									<?php echo e($pd->{$key}); ?>

									<?php endif; ?>
									</td>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

								<td>
									<?php if(isset($deposits[$pd->id_repayment_payment])): ?>
									<?php $dep = $deposits[$pd->id_repayment_payment][0]; ?>

									ID# <a href="/check-deposit/view/<?php echo e($dep->id_check_deposit); ?>" target="_blank"><?php echo e($dep->id_check_deposit); ?></a>  - <?php echo e($dep->bank_name); ?> [<?php echo e($dep->date_deposit); ?>]
									<?php endif; ?>

								</td>
							</tr>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</tbody>
						<?php if(count($paymentDetails) > 1): ?>
						<tr>
							<td colspan="<?php echo e($PColCount); ?>" class="font-weight-bold">Total</td>
							<td class="text-right font-weight-bold"><?php echo e(number_format($totalPayment,2)); ?></td>
							<td></td>
							<td></td>
						</tr>
						<?php endif; ?>
						<?php if($details->change_payable > 0): ?>
						<tr class="">
							<td colspan="<?php echo e($PColCount); ?>" class="font-weight-bold">Change</td>
							<td class="text-right font-weight-bold"><?php echo e(number_format($details->change_payable,2)); ?></td>
							<td colspan="2"></td>
						</tr>
						<?php
							$totalChange = collect($changes)->sum('total_amount');
							$bal =ROUND($details->change_payable-$totalChange,2);
							$totalClass = ($bal== 0)?'success':'danger'; 
						?>
						<tr class="<?php echo e($bal == 0?'text-success':''); ?>">
							<td colspan="<?php echo e($PColCount); ?>" class="font-weight-bold">Total Change Released</td>
							<td class="text-right font-weight-bold"><?php echo e(number_format($totalChange,2)); ?></td>
							<td colspan="2"></td>
						</tr>
						<?php if($bal > 0): ?>
						<tr class="text-danger">
							<td colspan="<?php echo e($PColCount); ?>" class="font-weight-bold">Balance</td>
							<td class="text-right font-weight-bold"><?php echo e(number_format($bal,2)); ?></td>
							<td colspan="2"></td>
						</tr>						
						<?php endif; ?>
						<?php endif; ?>
					</table>

				</div>

			</div>
			<?php endif; ?>

			<?php if(count($changes) > 0): ?>
			<h6 class="lbl_color font-weight-bold mt-2"><u>Change List</u></h6>
			<div class="row">
				<div class="col-md-12">
					<table class="table table-bordered table-head-fixed tbl_pdc w-100" id="tbl_loan">
						<thead>
							<tr class="text-center">
								<th></th>
								<th>ID</th>	
								<th>Date</th>
								<th>Total Amount</th>
								<th>Remarks</th>
								<th>Date Posted</th>
							</tr>	
						</thead>	
						<tbody>
							<?php $__currentLoopData = $changes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c=>$change): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<tr>
								<td class="text-center"><?php echo e($c+1); ?></td>
								<td class="text-center"><a href="/change-payable/view/<?php echo e($change->id_change_payable); ?>" target="_blank"><?php echo e($change->id_change_payable); ?></a></td>
								<td><?php echo e($change->change_date); ?></td>
								<td class="text-right"><?php echo e(number_format($change->total_amount,2)); ?></td>
								<td><?php echo e($change->remarks); ?></td>
								<td class="text-xs"><?php echo e($change->date_posted); ?></td>
							</tr>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</tbody>			
						<tr>
							<td class="font-weight-bold" colspan="3">Total</td>
							<td class="text-right font-weight-bold"><?php echo e(number_format($totalChange,2)); ?></td>
							<td colspan="2"></td>
								
							
							
						</tr>
					</table>
				</div>
			</div>
			<?php endif; ?>

			<?php if($details->status == 10): ?>
			<p class="text-muted">Reason : <?php echo e($details->reason); ?> (<?php echo e($details->status_date); ?>)</p>
			<?php endif; ?>
		</div>
		<?php if($details->status == 0 && $details->deposit_status == 0): ?>
		<div class="card-footer">
			<button class="btn bg-gradient-danger2 round_button float-right" onclick="show_status_modal()">Cancel</button>
		</div>

		<?php endif; ?>
	</div>
</div>

<iframe id="print_frame_or"  style="display: none;"> </iframe>


<?php echo $__env->make('global.print_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php if($details->status == 0): ?>
<?php echo $__env->make('repayment-bulk.status_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script type="text/javascript">
	const ID_REPAYMENT = <?php echo e($details->id_repayment); ?>;
	const printOR = (id_repayment) =>{
		$('#print_frame_or').attr('src',`/repayment-bulk/print-or/${id_repayment}`);
	}
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('adminLTE.admin_template', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/repayment-bulk/view.blade.php ENDPATH**/ ?>