

			<!-- <div class="form-group col-md-4">
				<label class="lbl_color text-sm mb-0">Date Received</label>
				<input type="date" class="form-control form-control-border frm-paymode" value="<?php echo e($details->date_received ?? MySession::current_date()); ?>" name="date_received">
			</div>
			<div class="form-group col-md-4">
				<label class="lbl_color text-sm mb-0">OR No.</label>
				<input type="text" class="form-control form-control-border frm-paymode" value="<?php echo e($details->or_number ?? ''); ?>" name="or_number">
			</div>			 -->	


<div class="card c-border card-payment">
	<div class="card-body px-5">
		<!-- <h5 class="lbl_color mb-4">Payment</h5> -->
		<div class="row mb-3">
			<div class="col-md-12">
				<a class="btn btn-sm btn-danger float-right btn-remove-payment" onclick="removeCard(this)"><i class="fa fa-trash"></i></a>
			</div>
		</div>

		<div class="row div-card-check">
			<div class="form-group col-md-2 col-12">
				<label class="lbl_color mb-0 text-sm">Check Type</label>
				<select class="form-control form-control-border p-0 frm-paymode" name="check_type">
					<?php $__currentLoopData = $check_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val=>$desc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<option value="<?php echo e($val); ?>" <?php echo (($details->id_check_type ?? 1) == $val)?'selected':'';  ?> ><?php echo e($desc); ?></option>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				</select>
			</div>
			<div class="form-group col-md-4 col-12">
				<label class="lbl_color mb-0 text-sm">Check Bank</label>
				<input type="text" class="form-control form-control-border frm-paymode" name="check_bank" value="<?php echo e($details->check_bank ?? ''); ?>">
				
			</div>
			<div class="form-group col-md-3 col-12">
				<label class="lbl_color mb-0 text-sm">Check Date</label>
				<input type="date" class="form-control form-control-border frm-paymode" name="check_date" value="<?php echo e($details->check_date ?? MySession::current_date()); ?>">
			</div>
			<div class="form-group col-md-3 col-12">
				<label class="lbl_color mb-0 text-sm">Check No.</label>
				<input type="text" class="form-control form-control-border frm-paymode" name="check_no" value="<?php echo e($details->check_no ?? ''); ?>">
			</div>
			
		</div>
		<div class="row div_check">
		</div>
		<div class="row mt-2">

			<div class="form-group col-md-3 col-12">
				<label class="lbl_color mb-0 text-sm">Amount</label>
				<input type="text" class="form-control form-control-border frm-paymode txt-input-amount text-right chk-amount" name="amount" value="<?php echo e(number_format($details->amount ?? 0,2)); ?>">
			</div>
			<div class="form-group col-md-9">
				<label class="lbl_color mb-0 text-sm">Remarks</label>
				<input type="text" class="form-control form-control-border frm-paymode" value="<?php echo e($details->remarks ?? ''); ?>" name="remarks">
			</div>
		</div>
	</div>
</div>



<?php $__env->startPush('scripts'); ?>
<script type="text/javascript">
	const CheckDiv = $('.div-card-check').detach().html();
	const PAYMENT_CARD = '<div class="card c-border card-payment">'+$('.card-payment').detach().html()+'</div>';
	
	$(document).ready(function(){
		appendPayment();
		init_paymode();
	});

	const appendPayment = ()=>{
		$('#div-payment-type').append(PAYMENT_CARD);
		init_paymode($('.card-payment').last());
	}
	const init_paymode=(obj=null)=>{

		var cardClass = (obj !== null)?$(obj):$('.card-payment');
		var val = $('#sel-paymode').val();
		if(val == 1){
			//Cash
			cardClass.find('.div_check').html('');
			$('#div-row-payment-button').html(``);
			$('.btn-remove-payment').remove();
		}else{
			//Check
			cardClass.find('.div_check').html(CheckDiv);
			$('#div-row-payment-button').html(`<button class="btn btn-sm bg-gradient-dark ml-2" onclick="appendPayment()"><i class="fa fa-plus"></i>&nbsp;Add Payments</button>`);
		}
	}
	$(document).on('change','#sel-paymode',function(){
		// let PCard = $(this).closest('.card-payment');

		$('.card-payment').remove();
		appendPayment();
		init_paymode();
		ComputeAll();
	});
	const removeCard = (obj)=>{
		var parentCard = $(obj).closest('.card-payment');
		$(parentCard).remove();
		ComputeAll();
	}
</script>
<?php $__env->stopPush(); ?><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/repayment-bulk/payment_types.blade.php ENDPATH**/ ?>