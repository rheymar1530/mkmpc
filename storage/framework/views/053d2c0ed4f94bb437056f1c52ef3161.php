<?php $__env->startSection('content'); ?>
<style type="text/css">
	@media (min-width: 1200px) {
		.container{
			max-width: 780px;
		}
	}
	.tbl_pdc th, .tbl_pdc td{
		padding: 0.2rem;
		font-size: 0.85rem;
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
		border-top: 3px solid gray!important;
		border-bottom: 3px solid gray!important;
	}
	td input.txt-input-amount{
		border: 2px solid !important;
	}
	.nowrap{
		white-space: nowrap;
	}
	.hidden-member{
		display: none;
	}
</style>


<div class="main_form container" style="margin-top: -20px;">
	<?php $back_link = (request()->get('href') == '')?'/change-payable':request()->get('href'); ?>
	<a class="btn btn-default btn-sm round_button" href="<?php echo e($back_link); ?>" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Change Payable List</a>


	<button class="btn bg-gradient-info btn-sm round_button float-right" onclick="print_page('/change-payable/print/<?php echo e($details->id_change_payable); ?>')"><i class="fa fa-print"></i>&nbsp;Print</button>
	<?php if($details->status == 0): ?>
	<button class="btn bg-gradient-warning2 btn-sm round_button float-right mr-2" onclick="show_status_modal()"><i class="fa fa-times"></i>&nbsp;Cancel Change Payable</button>

	<a class="btn bg-gradient-primary2 btn-sm round_button float-right mr-2" href="/change-payable/edit/<?php echo e($details->id_change_payable); ?>?href=<?php echo e($back_link); ?>"><i class="fa fa-edit"></i>&nbsp;Edit Change Payable</a>
	<?php endif; ?>

	<?php
		$types = array();
	?>

	<div class="card">
		<div class="card-body px-5">
			<div class="text-center mb-5">
				<h4 class="head_lbl">Change Payable</h4>
			</div>
			<div class="card c-border">
				<div class="card-body">
					<div class="row">
						<div class="col-lg-5 col-12">
							<div class="d-flex flex-column">
								<span class="text-md  font-weight-bold lbl_color">Change ID: <?php echo e($details->id_change_payable); ?></span>
								
								<span class="text-md  font-weight-bold lbl_color">Date: <span class="ms-sm-2 font-weight-normal ml-2"><?php echo e($details->date); ?></span></span>
								<span class="text-md  font-weight-bold lbl_color">Loan Payment ID: <span class="ms-sm-2 font-weight-normal ml-2"><?php echo e($details->id_repayment); ?></span></span>
							</div>		
						</div>
						<div class="col-lg-7 col-12">
							<div class="d-flex flex-column">
								<span class="text-md  font-weight-bold lbl_color">Total Amount: <span class="ms-sm-2 font-weight-normal ml-2"><?php echo e(number_format($details->total_amount,2)); ?></span></span>
								<span class="text-md  font-weight-bold lbl_color">Remarks: <span class="ms-sm-2 font-weight-normal ml-2"><?php echo e($details->remarks); ?></span></span>
								<span class="text-md  font-weight-bold lbl_color">Status: <span class="ms-sm-2 font-weight-normal ml-2 badge bg-gradient-<?php echo e($details->status_badge); ?> text-md"><?php echo e($details->status_description); ?></span></span>
							</div>		
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12 col-12">
							<div class="d-flex flex-column">
								<span class="text-md  font-weight-bold lbl_color">Change For:  <span class="ms-sm-2 font-weight-normal ml-2"><?php echo $details->change_for; ?></span></span>
							</div>		
						</div>
					</div>
					<?php if($details->status == 10): ?>
					<div class="row mt-2">
						<div class="col-lg-12 col-12">
							<div class="d-flex flex-column">
								<span class="text-sm  font-weight-bold lbl_color">Cancellation Reason: <span class="ms-sm-2 font-weight-normal ml-2"><?php echo e($details->reason); ?> <i>(<?php echo e($details->status_date); ?>)</i></span></span>
							</div>
						</div>
					</div>
					<?php endif; ?>
				</div>
			</div>

			<?php if(isset($Applications[1])): ?>
				<h5 class="lbl_color mb-0">Member</h5>
				<div style="max-height: calc(100vh - 140px);overflow-y: auto;">
					<table class="table table-bordered table-head-fixed tbl_pdc w-100" id="tbl_loan">
						<thead>
							<tr class="text-center">
							
								<th width="5%"></th>
								<th>Member Name</th>
								<th>Amount</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php $__currentLoopData = $Applications[1]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c=>$row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<tr>
								<td class="text-center"><?php echo e($c+1); ?></td>
								<td><?php echo e($row->reference); ?></td>
								<td class="text-right"><?php echo e(number_format($row->amount,2)); ?></td>
								<td>
									<?php if($row->id_cash_disbursement  > 0): ?>
									<a class="btn btn-xs bg-gradient-danger h-100" onclick="print_page('/cash_disbursement/print/<?php echo e($row->id_cash_disbursement); ?>')"><i class="fa fa-print"></i>&nbsp;Print CDV (<?php echo e($row->id_cash_disbursement); ?>)</a>
									<?php endif; ?>

								</td>
							</tr>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</tbody>
					</table>
				</div>
			<?php endif; ?>
			
			<?php if(isset($Applications[2])): ?>
				<h5 class="lbl_color mb-0">Other Income <a class="btn btn-xs bg-gradient-danger h-100" onclick="print_page('/cash_disbursement/print/<?php echo e($details->id_cash_disbursement); ?>')"><i class="fa fa-print"></i>&nbsp;Print CDV (<?php echo e($details->id_cash_disbursement); ?>)</a></h5>
				<div style="max-height: calc(100vh - 140px);overflow-y: auto;">
					<table class="table table-bordered table-head-fixed tbl_pdc w-100" id="tbl_loan">
						<thead>
							<tr class="text-center">
							
								<th width="5%"></th>
								<th>Account</th>
								<th>Amount</th>
							</tr>
						</thead>
						<tbody>
							<?php $__currentLoopData = $Applications[2]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c=>$row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<tr>
								<td class="text-center"><?php echo e($c+1); ?></td>
								<td><?php echo e($row->reference); ?></td>
								<td class="text-right"><?php echo e(number_format($row->amount,2)); ?></td>
							</tr>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

						</tbody>
					</table>
				</div>
			<?php endif; ?>
			<h5 class="float-right">Total Change: <span class="text-success"><?php echo e(number_format($details->total_amount,2)); ?></span></h5>

		</div>

	</div>

	
</div>


<?php if($details->status == 0): ?>
<?php echo $__env->make('change-payable.status_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?>

<?php echo $__env->make('global.print_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->stopSection(); ?>







<?php $__env->startPush('scripts'); ?>
<script type="text/javascript">
	const BACK_LINK = `<?php echo $back_link; ?>`;
	const ID_CHANGE_PAYABLE = <?php echo e($details->id_change_payable ?? 0); ?>;

	
</script>

<?php $__env->stopPush(); ?>
<?php echo $__env->make('adminLTE.admin_template', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/change-payable/view.blade.php ENDPATH**/ ?>