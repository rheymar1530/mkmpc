<?php $__env->startSection('content'); ?>

<?php echo $__env->make('loan.loan_table', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<button class="btn btn-sm bg-gradient-danger2 col-md-12" onclick="parent.load_loan_offset()" style="margin-top:15px"><i class="fas fa-mouse-pointer"></i>&nbsp;&nbsp;Click to View and Pay Active Loan</button>



<button class="btn btn-sm bg-gradient-warning col-md-12" onclick="parent.showDeductionModal()" style="margin-top:15px"><i class="fas fa-mouse-pointer"></i>&nbsp;&nbsp;Click to Add Other Deductions</button>

<?php $__env->startPush('scripts'); ?>
<script type="text/javascript">
	$(document).ready(function(){
		parent.set_terms_selected('<?php echo $service_details->terms_token ?? '' ?>');
		if(parent.hide_proceed == 0){
			parent.show_proceed_button();
		}
	})

	function get_loan_proceed(){
		var $loan_proceed = '<?php echo $loan['TOTAL_LOAN_PROCEED']?>';
		if($loan_proceed < 0){
			return false;
		}

		return true;
	}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<!-- <i class="fa-solid fa-arrow-pointer"></i> -->
<?php echo $__env->make('adminLTE.admin_template_frame', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/loan/loan_table_frame.blade.php ENDPATH**/ ?>