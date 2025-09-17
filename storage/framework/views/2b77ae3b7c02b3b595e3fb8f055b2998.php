<?php $__env->startSection('content'); ?>
<style type="text/css">
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
		.font-weight-bold{
			font-weight: bold !important;
		}
</style>



<?php
	function status_class($status){
		switch ($status) {
			case 0:
				return 'success';
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
?>

<div class="main_form" style="margin-top: -20px;">
	<?php $back_link = (request()->get('href') == '')?'/check-deposit':request()->get('href'); ?>
	<a class="btn btn-default btn-sm round_button" href="<?php echo e($back_link); ?>" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Check Deposit List</a>
	
	<?php if($details->status == 0): ?>
	<button class="btn bg-gradient-warning2 btn-sm round_button float-right" onclick="show_status_modal()"><i class="fa fa-times"></i>&nbsp;Cancel Check Deposit</button>

	<a class="btn bg-gradient-primary2 btn-sm round_button float-right mr-2" href="/check-deposit/edit/<?php echo e($details->id_check_deposit); ?>?href=<?php echo e($back_link); ?>"><i class="fa fa-edit"></i>&nbsp;Edit Check Deposit</a>
	<?php endif; ?>


	<?php if($details->id_cash_receipt_voucher > 0): ?>
		<a class="btn bg-gradient-danger2 btn-sm round_button float-right mr-2" onclick="print_page('/cash_receipt_voucher/print/<?php echo e($details->id_cash_receipt_voucher); ?>')"><i class="fa fa-print"></i>&nbsp;Print CRV (<?php echo e($details->id_cash_receipt_voucher); ?>)</a>
	<?php endif; ?>
	<button class="btn bg-gradient-info btn-sm round_button float-right mr-2" onclick="print_page('/check-deposit/print/<?php echo e($details->id_check_deposit); ?>')"><i class="fa fa-print"></i>&nbsp;Print Check Deposit</button>

	<?php
		$types = array();
	?>
	<div class="card">
		<div class="card-body px-5">
			<div class="text-center mb-5">
				<h4 class="head_lbl">Check Deposit</h4>
			</div>
			<div class="card c-border">
				<div class="card-body">
					<div class="row">
						<div class="col-lg-5 col-12">
							<div class="d-flex flex-column">
								<span class="text-md  font-weight-bold lbl_color">Deposit ID: <?php echo e($details->id_check_deposit); ?></span>
								
								<span class="text-md  font-weight-bold lbl_color">Date: <span class="ms-sm-2 font-weight-normal ml-2"><?php echo e($details->date); ?></span></span>
								<span class="text-md  font-weight-bold lbl_color">Bank: <span class="ms-sm-2 font-weight-normal ml-2"><?php echo e($details->bank_name); ?></span></span>
							</div>		
						</div>
						<div class="col-lg-7 col-12">
							<div class="d-flex flex-column">
								<span class="text-md  font-weight-bold lbl_color">Total Amount: <span class="ms-sm-2 font-weight-normal ml-2"><?php echo e(number_format($details->amount,2)); ?></span></span>
								<span class="text-md  font-weight-bold lbl_color">Remarks: <span class="ms-sm-2 font-weight-normal ml-2"><?php echo e($details->remarks); ?></span></span>
								<span class="text-md  font-weight-bold lbl_color">Status: <span class="ms-sm-2 font-weight-normal ml-2 badge bg-gradient-<?php echo e(status_class($details->status)); ?>2 text-md"><?php echo e($details->status_description); ?></span></span>
							
							</div>		
						</div>
					</div>
					<?php if($details->status == 10): ?>
					<div class="row">
						<div class="col-lg-12 col-12">
							<div class="d-flex flex-column">
								<span class="text-sm  font-weight-bold lbl_color">Cancellation Reason: <span class="ms-sm-2 font-weight-normal ml-2"><?php echo e($details->reason); ?> <i>(<?php echo e($details->status_date); ?>)</i></span></span>
							</div>
						</div>
					</div>
					<?php endif; ?>
				</div>
			</div>



			<div style="max-height: calc(100vh - 140px);overflow-y: auto;">
				<table class="table table-bordered table-head-fixed tbl_pdc w-100" id="tbl_loan">
					<thead>
						<tr class="text-center">
						
							<th>Date</th>
							<th>Description</th>
							<th>Reference</th>
							<th>Check No</th>
							<th>Check Bank</th>
							<th>Check Date</th>
							<th>Amount</th>
						</tr>
					</thead>
					<tbody>
						<?php $__currentLoopData = $deposits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<tr class="lbl_color row-check">
							<td><?php echo e($ch->transaction_date); ?></td>
							<td><?php echo e($ch->description); ?></td>
							<td><?php echo e($ch->reference); ?></td>
							<td><?php echo e($ch->check_no); ?></td>
							<td><?php echo e($ch->check_bank); ?></td>
							<td><?php echo e($ch->check_date); ?></td>
							<td class="text-right pr-2 col-amount"><?php echo e(number_format($ch->amount,2)); ?></td>
						</tr>

						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</tbody>


				</table>
			</div>
			


		</div>

	</div>

	
</div>

<?php echo $__env->make('global.print_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


<?php if($details->status == 0): ?>
<?php echo $__env->make('check_deposit.status_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>


<?php $__env->startPush('scripts'); ?>
<script type="text/javascript">
	const BACK_LINK = `<?php echo $back_link; ?>`;
	const ID_CHECK_DEPOSIT = <?php echo e($details->id_check_deposit ?? 0); ?>;

	
</script>

<?php $__env->stopPush(); ?>
<?php echo $__env->make('adminLTE.admin_template', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/check_deposit/view.blade.php ENDPATH**/ ?>