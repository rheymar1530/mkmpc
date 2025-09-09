
<style type="text/css">
	.modal-p-50 {
		max-width: 50% !important;
		min-width: 50% !important;
		margin: auto;
	}
	.filter_label{
		margin-top: 8px;
	}
	.font-bold{
		font-weight: bold;
	}
</style>
<?php

$transaction_type = [
	4=>"Check",
	1=>"Cash"
	
];
?>
<div id="summary_date_modal" class="modal fade"  role="dialog" aria-hidden="false">
	<div class="modal-dialog  modal-p-50" role="document" style="width: 50%">
		<form id="submit_summary_validate">
			<div class="modal-content " style="">
				<div class="modal-header panel_header">
					<h4 class="modal-title"><i class="fa fa-eye"></i> Loan Payment Summary</h4>

					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">×</span>
					</button>
				</div>
				<div class="modal-body" style="max-height: calc(100vh - 210px);
				overflow-y: auto;overflow-x: auto">    
				<div class="form-horizontal" >   
					<div class="form-group row" style="margin-top:10px">
						<div class="col-md-2">
							<div class="form-check">
							  <label class="form-check-label font-bold" style="margin-left:-18px">
							   	Transaction Date
							  </label>
							</div>
						</div>						
						<div class="col-md-4">
							<input type="date" title="Date" required="" class="form-control opt_sel form-in-text fil_date" id="txt_date_summary"  value="<?php echo e($current_date); ?>" name="date_start">
						</div>
					</div>
					<div class="form-group row" style="margin-top:10px">
						<div class="col-md-2">
							<div class="form-check">
							  <label class="form-check-label font-bold" style="margin-left:-18px">
							   	Payment Mode
							  </label>
							</div>
						</div>						
						<div class="col-md-4">
							<select class="form-control p-0" id="sel_transaction_type">
								<?php $__currentLoopData = $transaction_type; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val=>$text): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<option value="<?php echo e($val); ?>"><?php echo e($text); ?></option>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</select>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer modal_body">
				<button type="submit" class="btn btn-md  bg-gradient-primary but"><i class="fa fa-search"></i>&nbsp;&nbsp;Generate</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div><!-- /.modal-dialog -->
	</form>
</div>
</div>
<?php $__env->startPush('scripts'); ?>
<script type="text/javascript">
	$('#submit_summary_validate').submit(function(e){
		e.preventDefault()
		$.ajax({
			type           :         'GET',
			url              :         '/repayment/validate/summary',
			data             :       {'transaction_date' : $('#txt_date_summary').val(),
									  'transaction_type' : $('#sel_transaction_type').val()},
			beforeSend       :       function(){
										show_loader();
			},
			success         :        function(response){
				console.log({response});
				hide_loader();
				if(response.RESPONSE_CODE == "ERROR"){
					Swal.fire({
						title: response.message,
						text: '',
						icon: 'warning',
						showConfirmButton : false,
						timer  : 2500
					})
				}else if(response.RESPONSE_CODE == "SUCCESS"){
					print_page('/repayment/summary/'+response.transaction_date+'/'+response.transaction_type)
					$('#summary_date_modal').modal('hide');
				}
			},error: function(xhr, status, error) {
				hide_loader()
				var errorMessage = xhr.status + ': ' + xhr.statusText;
				Swal.fire({
					title: "Error-" + errorMessage,
					text: '',
					icon: 'warning',
					confirmButtonText: 'OK',
					confirmButtonColor: "#DD6B55"
				});
			}
		})
	})


</script>
<?php $__env->stopPush(); ?>

<?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/repayment/summary_date_modal.blade.php ENDPATH**/ ?>