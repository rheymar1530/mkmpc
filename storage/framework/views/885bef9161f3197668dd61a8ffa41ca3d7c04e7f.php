<?php $__env->startSection('content'); ?>
<style type="text/css">
	.tbl_loan th, .tbl_loan td{
		padding: 0.2rem;
		font-size: 0.85rem;
	}
	.tbl_loan td.in{
		padding: 0;
	}
	td.in input{
		height: 27px !important;
		font-size: 0.8rem;
	}
	.tbl_loan th{
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
	.tbl_loan tfoot td {
		background: #666;
		color: white;
	}
</style>
<?php
$paymentModes=array(
	4=>'Check',1=>'Cash'
);
$check_types = [1=>"On-date",2=>"Post dated"];

?>
<div class="container main_form" style="margin-top: -20px;">
	<?php $back_link = (request()->get('href') == '')?'/repayment-statement':request()->get('href'); ?>

	<a class="btn btn-default btn-sm round_button" href="<?php echo e($back_link); ?>" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Loan Payment Statement List</a>



	<?php if($allow_post && $details->status == 0): ?>
	<button class="btn btn-warning btn-sm round_button float-right" onclick="show_status_modal()"><i class="fa fa-times"></i>&nbsp;Cancel Loan Payment Statement</button>

	<?php endif; ?>



	<?php if($details->status == 0): ?>
	<a class="btn btn-primary btn-sm round_button float-right mr-2" href="/repayment-statement/edit/<?php echo e($details->id_repayment_statement); ?>?href=<?php echo e($back_link); ?>"><i class="fa fa-print"></i>&nbsp;Edit Statement</a>
	<?php endif; ?>

	<button class="btn btn-danger btn-sm round_button float-right mr-2" onclick="print_page('/repayment-statement/print/generated/<?php echo e($details->id_repayment_statement); ?>')"><i class="fa fa-print"></i>&nbsp;Print Statement</button>


<!-- 	<div class="btn-group float-right">
		<button type="button" class="btn btn-sm  bg-gradient-danger2 dropdown-toggle mr-2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-print"></i>&nbsp;
			Print
		</button>
		<div class="dropdown-menu">
			<a class="dropdown-item" href="javascript:void(0)" onclick="print_page('/repayment-statement/print/generated/<?php echo e($details->id_repayment_statement); ?>')">Print Statement</a>
			if($details->status == 1)
			<a class="dropdown-item" onclick="print_page('/repayment-statement/print/remitted/<?php echo e($details->id_repayment_statement); ?>')">Print Remitted Statement</a>
			endif
		</div>
	</div> -->
	<div class="card">
		<div class="card-body px-5 py-4">
			<div class="text-center mb-3">
				<h4 class="head_lbl">Loan Payment Statement <?php if($details->status > 0): ?><span class="badge bg-gradient-<?php echo e($details->status_class); ?>2 text-md"><?php echo e($details->status_description); ?></span> <?php endif; ?></h4>
			</div>
			<div class="card c-border">
				<div class="card-body">
					<div class="row">
						<div class="col-lg-6 col-12">
							<div class="d-flex flex-column">
								<span class="text-sm  font-weight-bold lbl_color">No.: <?php echo e($details->month_year); ?>-<?php echo e($details->id_repayment_statement); ?></span>
								<span class="text-sm  font-weight-bold lbl_color"><?php echo e($details->group_); ?>: <span class="ms-sm-2 font-weight-normal ml-2"><?php echo e($details->baranggay_lgu); ?></span></span>
								<span class="text-sm  font-weight-bold lbl_color">Treasurer: <span class="ms-sm-2 font-weight-normal ml-2"><?php echo e($details->treasurer); ?></span></span>
							</div>		
						</div>
						<div class="col-lg-6 col-12">
							<div class="d-flex flex-column">

								<span class="text-sm  font-weight-bold lbl_color">Statement Date: <span class="ms-sm-2 font-weight-normal ml-2"><?php echo e($details->statement_date); ?></span></span>
								<span class="text-sm  font-weight-bold lbl_color">Month Due: <span class="ms-sm-2 font-weight-normal ml-2"><?php echo e($details->month_due); ?></span></span>
								<span class="text-sm  font-weight-bold lbl_color">Total Amount: <span class="ms-sm-2 font-weight-normal ml-2" id="amount_due">0.00</span></span>
							</div>		
						</div>
					</div>
					<?php if($details->status == 10): ?>
					<div class="row">
						<div class="col-lg-12 col-12">
							<div class="d-flex flex-column">
								<span class="text-sm  font-weight-bold lbl_color">Cancellation Reason: <span class="ms-sm-2 font-weight-normal ml-2"><?php echo e($details->status_remarks); ?> <i>(<?php echo e($details->status_date); ?>)</i></span></span>
							</div>
						</div>
					</div>
					<?php endif; ?>
				</div>
			</div>
			<div class="row mt-3">
				<div class="col-md-12">
					<?php
					$GLOBALS['total'] = 0;
					?>
					<div class="table">
						<table class="table table-bordered table-head-fixed tbl_loan w-100" id="tbl_loan">
							<thead>
								<tr class="text-center">
									<th>BORROWER'S NAME</th>
									<th>LOAN TYPE</th>
									<th>AMOUNT</th>
								</tr>
							</thead>
							<?php $__currentLoopData = $loans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id_member=>$loan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<tbody class="borders bmember" data-member-id="<?php echo e($id_member); ?>">
								<?php $__currentLoopData = $loan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c=>$lo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr class="rloan" data-loan="<?php echo e($lo->loan_token); ?>" data-id="<?php echo e($lo->id_loan); ?>" loan-due="<?php echo e($lo->current_due); ?>" rp-id="<?php echo e($lo->id_repayment_statement_details); ?>">
									<?php if($c == 0): ?>
									<td class="font-weight-bold nowrap" rowspan="<?php echo e(count($loan)); ?>"><i><?php echo e($lo->member); ?></i></td>
									<?php endif; ?>
									<td class="nowrap"><sup><a href="/loan/application/approval/<?php echo e($lo->loan_token); ?>" target="_blank">[<?php echo e($lo->id_loan); ?>] </a></sup><?php echo e($lo->loan_name); ?></td>
									<td class="text-right "><?php echo e(number_format($lo->current_due,2)); ?></td>
									<?php $GLOBALS['total'] += $lo->current_due; ?> 
								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</tbody>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							<tfoot>
								<td colspan="2">Grand Total</td>
								<td class="text-right"><?php echo e(number_format($GLOBALS['total'],2)); ?></td>
								

							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>


	</div>
</div>


<?php echo $__env->make('global.print_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php if($allow_post): ?>

<?php echo $__env->make('repayment-statement.status_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>


<?php $__env->startPush('scripts'); ?>


<script type="text/javascript">
	const BACK_LINK = `<?php echo $back_link; ?>`;
	const CheckDiv = $('#div_check div').detach();
	const ID_REPAYMENT_STATEMENT = <?php echo e($details->id_repayment_statement ?? 0); ?>;
	$(document).ready(function(){
		init_paymode();
		$('#amount_due').text(number_format('<?php echo e($GLOBALS["total"]); ?>'));
		ComputeAll();
	})	
	const init_paymode=()=>{
		var val = $('#sel-paymode').val();
		if(val == 1){
			//Cash
			$('#div_check').html('');
		}else{
			//Check
			$('#div_check').html(CheckDiv);
		}
	}
	const ComputeAll=()=>{
		let TotalPayment = 0;
		$('.in-payment').each(function(){
			var p = $(this).val();
			let payment = ($.isNumeric(p))?roundoff(p):decode_number_format(p);
			TotalPayment += payment;
		});
		$('#txt-total-payment').val(number_format(TotalPayment));
		$('.td-total-payment').text(number_format(TotalPayment));
	}
</script>

<?php if($allow_post): ?>


<?php else: ?>
<script type="text/javascript">
	$(document).ready(function(){
		$('.frm-paymode,.in-payment').prop('disabled',true);
	})
	
</script>
<?php endif; ?>


<?php if($details->status == 0): ?>
<script type="text/javascript">
	$(document).ready(function(){
		if(window.localStorage.getItem("for_print") == ID_REPAYMENT_STATEMENT){
			window.localStorage.removeItem("for_print");
			print_page(`/repayment-statement/print/generated/${ID_REPAYMENT_STATEMENT}`);
		}
	})
</script>
<?php elseif($details->status == 1): ?>
<script type="text/javascript">
	$(document).ready(function(){
	
		if(window.localStorage.getItem("for_print") == ID_REPAYMENT_STATEMENT){
			print_page(`/repayment-statement/print/remitted/${ID_REPAYMENT_STATEMENT}`);
			window.localStorage.removeItem("for_print");
			
		}
	})
</script>
<?php endif; ?>

<?php $__env->stopPush(); ?>
<?php echo $__env->make('adminLTE.admin_template', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/repayment-statement/view.blade.php ENDPATH**/ ?>