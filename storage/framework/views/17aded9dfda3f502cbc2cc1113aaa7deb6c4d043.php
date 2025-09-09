<?php $__env->startSection('content'); ?>
<style type="text/css">
	@media (min-width: 1200px) {
		.container{
			max-width: 780px;
		}
	}
	.swal2-deny{
		padding: 0.375rem 0.75rem !important;
	}
</style>
<?php


$sel_reason = $details->reason ?? 1;
?>
<div class="container section_body">
	<?php $back_link = (request()->get('href') == '')?'/cbu_withdraw':request()->get('href'); ?>
	<a class="btn bg-gradient-secondary btn-sm" href="<?php echo e($back_link); ?>" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to CBU Withdrawal List</a>
	<form id="frm_post_cbu">
		<div class="card">
			<div class="card-body">
				<h3 class="text-center head_lbl">CBU Withdrawal
					<?php if($opcode == 1): ?>
					<small>(ID# <?php echo e($details->id_cbu_withdrawal); ?>)</small>
					<?php if($details->status > 0): ?>
					<span class="badge badge-<?php echo e(($details->status ==1)?'primary':(($details->status==2)?'success':'danger')); ?>"><?php echo e($details->status_desc); ?></span>
					<?php endif; ?>
					<?php endif; ?>
				</h3>

				<?php if(MySession::isAdmin()): ?>
				<div class="row">
					<div class="col-md-8 col-12">
						<label class="mb-0">Member</label>
						<select class="form-control select2 in-request" id="sel_member" required>
							<?php if(isset($selected_member)): ?>
							<option value="<?php echo e($selected_member->tag_id); ?>"><?php echo e($selected_member->tag_value); ?></option>
							<?php endif; ?>
						</select>
					</div>
				</div>
				<?php endif; ?>

				<div class="row mt-2 d-flex align-items-end">
					<div class="col-md-4 col-12">
						<label class="mb-0">Reason</label>
						<select class="form-control select2 p-0 in-request" id="sel_reason">
							<?php $__currentLoopData = $reasons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<option value="<?php echo e($r->id_cbu_withdrawal_reason); ?>" <?php echo($r->id_cbu_withdrawal_reason==$sel_reason)?'selected':''; ?> ><?php echo e($r->description); ?></option>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</select>
					</div>
					<div class="col-md-8 col-12" id="others_holder">
						<input type="text" class="form-control in-request" id="text_others" value="<?php echo e($details->other_reason ?? ''); ?>">
					</div>
				</div>
				<div class="row mt-2 d-flex align-items-end">
					<div class="col-md-4 col-12">
						<label class="mb-0">Amount</label>

						<input type="text" class="form-control class_amount text-right in-request" id="txt_amount" value="<?php echo e(number_format(($details->amount ?? 0),2)); ?>">

					</div>
					<?php if($allow_post): ?>
					<div class="col-md-4 col-12">
						<p class="mb-0" id="cbu_amt_holder">
							<?php if(isset($current_cbu)): ?>
							(<?php echo e(number_format($current_cbu,2)); ?>)
							<?php endif; ?>
						</p>
					</div>
					<?php endif; ?>
				</div>
			</div>
			<div class="card-footer  py-1">
				<?php if($allow_post): ?>
				<button type="submit" class="btn bg-gradient-success2 float-right">Save</button>
				<?php endif; ?>
				<?php if($opcode == 1 && MySession::isAdmin()): ?>
				<?php if($details->status <= 1): ?>
				<button type="button" class="btn bg-gradient-primary2 float-right mr-1" onclick="show_status_modal()">Update Status</button>
				<?php endif; ?>

				<?php if($opcode == 1 && MySession::isadmin() && $details->id_cash_disbursement > 0): ?>

				<button type="button" class="btn bg-gradient-danger2 float-right" style="margin-right:10px" onclick="print_page('/cash_disbursement/print/<?php echo e($details->id_cash_disbursement); ?>')"><i class="fas fa-print" ></i>&nbsp;Print CDV (#<?php echo e($details->id_cash_disbursement); ?>)</button>
				<?php endif; ?>
				<?php endif; ?>
			</div>
		</div>
	</form>
</div>

<?php if($opcode == 1): ?>
<?php echo $__env->make('cbu_withdraw.withdraw_status', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('global.print_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<?php if($opcode == 1): ?>
<script>
	$(document).ready(function(){
		var redirect_data = jQuery.parseJSON(localStorage.getItem("redirect_print_entry"));

		if(redirect_data != null){
			if(redirect_data.show_print_entry == 1){
				print_page('/cash_disbursement/print/<?php echo e($details->id_cash_disbursement); ?>');
				localStorage.removeItem("redirect_print_entry");
			}
		}
	})
</script>
<?php endif; ?>
<script type="text/javascript">
	let others_reason_html = $('#text_others').detach();
		let CURRENT_CBU = parseFloat('<?php echo e($current_cbu ?? 0); ?>');
	init_reason();

	$(document).on('change','#sel_reason',function(){
		init_reason();
	});

	function init_reason(){
		var val = $('#sel_reason').val();
		if(val == 4){
			$('#others_holder').html(others_reason_html);
		}else{
			$('#others_holder').html('');
			if(val == 1){
				$('#txt_amount').prop('disabled',true);
				<?php
					if(!isset($details->amount)){
						echo "$('#txt_amount').val(number_format(CURRENT_CBU,2)); ";
					}
				?>
			}else{
				$('#txt_amount').prop('disabled',false);
			}
		}

	}


</script>

<?php if($allow_post): ?>
<script type="text/javascript">

	$(document).on('ready',function(){
		
	});
	intialize_select2();
	function intialize_select2(){		
		$link = '/search_member'
		$("#sel_member").select2({
			minimumInputLength: 2,
			width: '100%',
			createTag: function (params) {
				return null;
			},
			ajax: {
				tags: true,
				url: $link,
				dataType: 'json',
				type: "GET",
				quietMillis: 1000,
				data: function (params) {
					var queryParameters = {
						term: params.term
					}
					return queryParameters;
				},
				processResults: function (data) {
					console.log({data});
					return {
						results: $.map(data.accounts, function (item) {
							return {
								text: item.tag_value,
								id: item.tag_id
							}
						})
					};
				}
			}
		});
	}

	$(document).on('change','#sel_member',function(){
		$.ajax({
			type       :     'GET',
			url        :     '/cbu_withdraw/get_member_cbu',
			data       :     {'id_member' : $(this).val()},
			success    :     function(response){
				console.log({response});
				$('#cbu_amt_holder').text(`(${number_format(response.cbu_amount,2)})`);
				CURRENT_CBU = response.cbu_amount;
				init_reason();

				$('#txt_amount').val(number_format(CURRENT_CBU,2));	
			}
		})
	})
	$(document).on('select2:open', (e) => {
		const selectId = e.target.id
		$(".select2-search__field[aria-controls='select2-" + selectId + "-results']").each(function(key,value,){
			value.focus()
		})
	}) 
	$(document).on("focus",".class_amount",function(){
		var val = $(this).val();
		if(val == '' || val == 'NaN'){
			val = '0.00';

			$(this).val('');

			return; 
		}
		$(this).val(decode_number_format(val)); 
	})
	$(document).on("blur",".class_amount",function(){
		var val = $(this).val();
		if(!$.isNumeric(val)){
			val = 0;
			$(this).val('');

			return;
		}
		$(this).val(number_format(parseFloat(val)));
	})
	$('#frm_post_cbu').on('submit',function(e){
		e.preventDefault();
		Swal.fire({
			title: 'Do you want to save this?',
			icon: 'warning',
			showDenyButton: false,
			showCancelButton: true,
			confirmButtonText: `Save`,
		}).then((result) => {
			if (result.isConfirmed) {
				post();
			} 
		})	
	})
	function post(){
		var post_data = {
			'id_member' :  $('#sel_member').val(),
			'reason' : $('#sel_reason').val(),
			'others' : $('#text_others').val(),
			'amount' : decode_number_format($('#txt_amount').val()),
			'opcode' : '<?php echo e($opcode); ?>',
			'id_cbu_withdrawal' : '<?php echo e($details->id_cbu_withdrawal ?? 0); ?>'
		}

		$.ajax({
			type          :       'POST',
			url           :       '/cbu_withdraw/post',
			data          :       post_data,
			beforeSend :   function(){
				show_loader();
				$('.mandatory').removeClass('mandatory');
			},
			success       :       function(response){
				console.log({response});
				hide_loader();
				if(response.RESPONSE_CODE == "SUCCESS"){
					var html_swal = '';
					var link = "/cbu_withdraw/view/"+response.id_cbu_withdrawal+"?href="+encodeURIComponent('<?php echo $back_link;?>');


					html_swal = "<a href='"+link+"'>Withdrawal ID# "+response.id_cbu_withdrawal+"</a>";

					Swal.fire({
						title: "Withdrawal Successfully Saved",
						html : html_swal,
						text: '',
						icon: 'success',
						showCancelButton : true,
						confirmButtonText: 'Create Another Withdrawal',
						cancelButtonText: 'Back to List of Withdrawal',
						showDenyButton: false,

						showConfirmButton : true,     
						allowEscapeKey : false,
						allowOutsideClick: false
					}).then((result) => {
						if(result.isConfirmed) {
							window.location = "/cbu_withdraw/create?href="+encodeURIComponent('<?php echo $back_link;?>');
						}else{
							window.location = '<?php echo $back_link;?>';
						}
					});	
				}else if(response.RESPONSE_CODE == "ERROR"){
					Swal.fire({
						title: response.message,
						text: '',
						icon: 'warning',
						showCancelButton : false,
						showConfirmButton : false,
						timer : 2500
					});


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

		console.log({post_data});
	}
</script>
<?php else: ?>
<script type="text/javascript">
	$('#frm_post_cbu').on('submit',function(e){
		e.preventDefault();
	})

	$(function(){
		$('.in-request').prop('disabled',true);
	})
</script>
<?php endif; ?>

<?php $__env->stopPush(); ?>
<?php echo $__env->make('adminLTE.admin_template', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/cbu_withdraw/form.blade.php ENDPATH**/ ?>