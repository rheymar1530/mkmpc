<style type="text/css">
	.modal-p-60 {
		max-width: 60% !important;
		min-width: 60% !important;
		margin: auto;
	}
</style>
<div class="modal fade" id="repayment_modal" tabindex="-1" role="dialog" aria-labelledby="PrintModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-body p-0">
				
				<iframe id="frame_repayment" class="embed-responsive-item" frameborder="0" style="border:0;height:700px;width: 100%" src="/loan/repayment_transaction/<?php echo e($service_details->loan_token); ?>"></iframe>
			</div>

		</div>
	</div>
</div>
<?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/loan/repayment_transaction_modal.blade.php ENDPATH**/ ?>