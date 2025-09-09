<?php $__env->startSection('content'); ?>
<style type="text/css">
	.tbl_loan_req tr>th{
		padding: 5px;
		padding-left: 5px;
		padding-right: 5px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 12px;
	}
	.tbl_loan_req tr>td{
		padding: 3px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 12px;
	}
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
	.spn_t{
		font-weight: bold;
		font-size: 14px;
	}
	.spn_txt{
		word-wrap:break-word;
		overflow: hidden;
	}
	.spn_loan_service_details{
		margin-top: -17px;
	}
</style>
<?php
$loan_app_type = [
	1=>"New Loan",
	2=>"Renewal"
];

// 0 - info
// // 1,6 - primary
// 2,3 - success
// 4,5 - danger

$status_badge = [
	0=>"info",
	1=>"primary",
	2=>"success",
	3=>"success",
	4=>"danger",
	5=>"danger",
	6=>"primary",
];
?>
<div class="container main_form section_body" style="margin-top: -20px;">
	<?php $back_link = (request()->get('href') == '')?'/loan':request()->get('href'); ?>
	<a class="btn bg-gradient-secondary btn-sm" href="<?php echo e($back_link); ?>" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Loan List</a>


	<div class="container px-0 mb-2">
		<div class="btn-group d-none d-md-flex justify-content-center">
			<?php if(isset($SHOW_EDIT_BUTTON) && $SHOW_EDIT_BUTTON): ?>
			<?php
			$redirect_loan_link = "/loan/application/view/".$service_details->loan_token."?href=".$back_link;
			?>
			<a class="btn bg-gradient-primary2 btn-sm" href="<?php echo e($redirect_loan_link); ?>" ><i class="far fa-edit"></i>&nbsp;&nbsp;Edit Loan</a>
			<?php endif; ?>

			<?php if($service_details->status == 1 || $service_details->status == 2): ?>
			<a class="btn bg-gradient-danger2 btn-sm float-right"  onclick="print_waiver()"><i class="fas fa-print"></i>&nbsp;&nbsp;Print Waiver</a>
			<?php endif; ?>

			<?php if($service_details->status == 3 && $service_details->lstatus == 1): ?>
			<a class="btn bg-gradient-info2 btn-sm float-right" style="" href="/loan/application/create?href=<?php echo e(urlencode($back_link)); ?>&id_member=<?php echo e($service_details->id_member); ?>&loan_reference=<?php echo e($service_details->id_loan_service); ?>&terms_token=<?php echo e($service_details->terms_token); ?>" target="_blank"><i class="fa-solid fa-arrow-rotate-right"></i>&nbsp;&nbsp;Renew Loan</a>
			<?php endif; ?>

			<?php if(MySession::isAdmin()): ?>
			<?php if($service_details->status == 3 || $service_details->status == 6): ?>

			<a class="btn bg-gradient-warning2 btn-sm float-right" style="" onclick="print_waiver()"><i class="fas fa-print"></i>&nbsp;&nbsp;Print Waiver</a>
			<?php if($service_details->id_cash_disbursement > 0): ?>
			<a class="btn bg-gradient-danger2 btn-sm float-right"  onclick="print_cash_disbursement()"><i class="fas fa-print"></i>&nbsp;&nbsp;Print Cash Disbursement Voucher</a>
			<?php endif; ?>
			<?php endif; ?>
			<?php endif; ?>
		</div>
		<div class="dropdown d-md-none">
			<button class="btn bg-gradient-danger2 dropdown-toggle" type="button"
			data-toggle="dropdown">
			Options  
		</button>
		<div class="dropdown-menu">

			<?php if(isset($SHOW_EDIT_BUTTON) && $SHOW_EDIT_BUTTON): ?>
			<?php
			$redirect_loan_link = "/loan/application/view/".$service_details->loan_token."?href=".$back_link;
			?>
			<a class="dropdown-item" href="<?php echo e($redirect_loan_link); ?>" >Edit Loan</a>
			<?php endif; ?>

			<?php if($service_details->status == 1 || $service_details->status == 2): ?>
			<a class="dropdown-item"  onclick="print_waiver()">Print Waiver</a>
			<?php endif; ?>

			<?php if($service_details->status == 3 && $service_details->lstatus == 1): ?>
			<a class="dropdown-item" style="" href="/loan/application/create?href=<?php echo e(urlencode($back_link)); ?>&id_member=<?php echo e($service_details->id_member); ?>&loan_reference=<?php echo e($service_details->id_loan_service); ?>&terms_token=<?php echo e($service_details->terms_token); ?>" target="_blank">Renew Loan</a>
			<?php endif; ?>

			<?php if(MySession::isAdmin()): ?>
			<?php if($service_details->status == 3 || $service_details->status == 6): ?>

			<a class="dropdown-item" style="" onclick="print_waiver()">Print Waiver</a>
			<?php if($service_details->id_cash_disbursement > 0): ?>
			<a class="dropdown-item" onclick="print_cash_disbursement()">Print Cash Disbursement Voucher</a>
			<?php endif; ?>
			<?php endif; ?>
			<?php endif; ?>
		</div>
	</div>
</div>
<div class="card">
	<div class="card-body col-md-12">
		<h3 class="head_lbl text-center">Loan Application Form  <?php echo isset($service_details->id_loan)?"(Loan ID# ".$service_details->id_loan.")":"" ?> <span class="badge badge-<?php echo e($status_badge[$service_details->status_code]); ?>"><?php echo e($service_details->loan_status); ?></span></h3>
		<div class="row">
			<div class="col-sm-12" style="margin-top:10px">	
				<?php if($service_details->status<2 && $INVALID_APPLICATION): ?>
				<div class="form-row">
					<div class="col-md-12">
						<div class="alert bg-gradient-warning" role="alert">
							<h4 class="alert-heading">Error Message</h4>
							<hr>
							<ul>
								<?php $__currentLoopData = $ERROR_MESSAGE; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<li><?php echo e($err); ?></li>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</ul>

						</div>
					</div>
				</div>
				<?php endif; ?>
			</div>
			<div class="col-sm-12" style="margin-top:10px">	
				<?php if($service_details->status_code == 5 || $service_details->status_code == 4): ?>
				<span><b>Remarks: </b><?php echo e($service_details->cancellation_reason); ?></span>
				<?php endif; ?>
				<?php echo $__env->make('loan.loan_table', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
			</div>
			<?php if(isset($show_repayment)): ?>
			<div class="col-md-12" style="margin-top:15px">
				<button class="btn btn-sm bg-gradient-success2 col-md-12" onclick="show_loan_repayments()">Show Repayments History</button>
			</div>
			<?php endif; ?>
			<div class="col-md-12 p-1" style="margin-top:15px">
				<div class="card c-border">
					<div class="card-body">
						<h5 class="head_lbl text-center mb-2">Loan Application Details</h5>
						<div class="text-left">
							<!-- <h5 class="lbl_color badge bg-light text-lg text-center">Latest Payslip</h5> -->
							<h5 class="lbl_color text-lg font-weight-bold">Latest Payslip</h5>
						</div>
						<div class="row p-0">
							<?php if(count($net_pays) > 0): ?>
							<?php $__currentLoopData = $net_pays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c=>$net): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<div class="col-md-6 col-12">
								<div class="card c-border row_net">
									<div class="card-body">
										<h6 class="lbl_color"><u>Payslip <?php echo e($c+1); ?></u></h6>
										<div class="form-row d-flex align-items-end mt-3">
											<div class="form-group col-md-12 col-12 mb-1">
												<label class="lbl_color">Period: <span class="font-weight-normal"><?php echo e($net->period_start); ?> - <?php echo e($net->period_end); ?></span></label>
											</div>
											<div class="form-group col-md-12 col-12 mb-1">
												<label class="lbl_color">Amount: <span class="font-weight-normal"><?php echo e($net->amount); ?></span></label>
											</div>			                                
										</div>
									</div>
								</div>
							</div>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							<?php else: ?>
							<div class="col-md-12 col-12">
								<div class="card c-border">
									<div class="card-body">
										<h6 class="lbl_color text-center">No Payslips</h6>
									</div>
								</div>
							</div>
							<?php endif; ?>
						</div>
						<div class="text-left mt-2">
							<h5 class="lbl_color text-lg font-weight-bold">Other Lending</h5>
						</div>
						<div class="row">
							<div class="col-md-12 col-12">
								<?php if(count($other_lendings) > 0): ?>
								<?php $__currentLoopData = $other_lendings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c=>$lend): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<div class="card c-border row_net">
									<div class="card-body">
										<h6 class="lbl_color"><u>Lending <?php echo e($c+1); ?></u></h6>
										<div class="form-row d-flex align-items-end mt-3">
											<div class="form-group col-md-12 col-12 mb-1">
												<label class="lbl_color">Lending Institution: <span class="font-weight-normal"><?php echo e($lend->name); ?></span></label>
											</div>	
											<div class="form-group col-md-12 col-12 mb-1">
												<label class="lbl_color">Loan Period: <span class="font-weight-normal"><?php echo e($lend->date_started); ?> - <?php echo e($lend->date_ended); ?></span></label>
											</div>
											<div class="form-group col-md-12 col-12 mb-1">
												<label class="lbl_color">Amount: <span class="font-weight-normal"><?php echo e($lend->amount); ?></span></label>
											</div>			                                
										</div>
									</div>
								</div>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								<?php else: ?>
								<div class="col-md-12 col-12">
									<div class="card c-border">
										<div class="card-body">
											<h6 class="lbl_color text-center">No Other Lendings</h6>
										</div>
									</div>
								</div>
								<?php endif; ?>
							</div>
						</div>
						
						<div class="text-left mt-2">
							<h5 class="lbl_color text-lg font-weight-bold">Comaker(s)</h5>
						</div>
						<div class="row">
							<div class="col-md-12">
								<div class="card c-border">
									<div class="card-body">
										<div class="form-row d-flex align-items-end mt-3">
											<?php $__currentLoopData = $comakers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c=>$com): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
											<div class="form-group col-md-12 col-12 mb-1">
												<label class="lbl_color"><?php echo e($c+1); ?>. <span class="font-weight-normal pl-2"><?php echo e($com->name); ?></span> 
												<?php if(isset($maker_cbu[$com->id_member])): ?>
													<span class="badge badge-warning text-sm">Current CBU (₱<?php echo e(number_format($maker_cbu[$com->id_member],2)); ?>) is below ₱<?php echo e(number_format($service_details->maker_min_cbu,2)); ?></span></label>
												<?php endif; ?>
											</div>	
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
										</div>
									</div>
								</div> 
							</div>
						</div>
						<div class="text-left mt-2">
							<h5 class="lbl_color text-lg font-weight-bold">Loan Purpose</h5>
						</div>
						<div class="row">
							<div class="col-md-12">
								<textarea class="form-control" rows="3" style="resize:none;" disabled><?php echo e($service_details->loan_remarks ?? ''); ?></textarea>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- CONDITION FOR APPROVAL AND STATUS -->
			<?php if($for_approval && $service_details->status<=2): ?>
			<div class="col-md-12" style="margin-top:10px">
				<button class="btn bg-gradient-success2 btn-sm col-md-12" onclick="show_approval_modal()">Update Loan Status</button>
			</div>
			<?php elseif(MySession::isSuperAdmin() && ($service_details->status == 2 || ($service_details->status == 3 && count($REPAYMENT_TABLE ?? []) == 0))): ?>
			<div class="col-md-12" style="margin-top:10px">
				<button class="btn bg-gradient-danger2 btn-sm col-md-12" onclick="show_cancel_modal()">Cancel Loan [Admin]</button>
			</div>
			<?php endif; ?>
			<!-- END CONDITION FOR APPROVAL AND STATUS -->
		</div>

	</div>
</div>
</div>
<!-- CONDITION FOR APPROVAL AND STATUS -->
<?php if($for_approval && $service_details->status<=2): ?>
<?php echo $__env->make('loan.approval_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php elseif(MySession::isSuperAdmin() && ($service_details->status == 2 || ($service_details->status == 3 && count($REPAYMENT_TABLE ?? []) == 0))): ?>
<?php echo $__env->make('loan.status_admin_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?>

<?php if($service_details->status == 1 || $service_details->status == 2 || $service_details->status == 3 || $service_details->status == 6): ?>
<?php echo $__env->make('global.print_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?>

<!-- END CONDITION FOR APPROVAL AND STATUS -->
<?php if(isset($show_repayment)): ?>
<?php echo $__env->make('loan.repayment_transaction_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script type="text/javascript">
	function show_loan_repayments(){
		$('#frame_repayment').attr("src", $('#frame_repayment').attr("src"));
		$('#repayment_modal').modal('show')
	}
	$(document).ready(function(){
		var trigger_update = localStorage.getItem("trigger_update") ?? 0;

		if(trigger_update == 1){
			show_approval_modal();
			localStorage.removeItem("trigger_update")
		}
		
	})
</script>
<!-- CONDITION FOR APPROVAL AND STATUS -->
<?php if($for_approval && $service_details->status<=2): ?>
<script type="text/javascript">
	function show_approval_modal(){
		$('#approval_modal').modal('show')
	}
	$('#frm_loan_approval').submit(function(e){
		e.preventDefault();
		Swal.fire({
			title: 'Do you want to save this?',
			icon: 'warning',
			showDenyButton: false,
			showCancelButton: true,
			confirmButtonText: `Save`,
		}).then((result) => {
			if (result.isConfirmed) {
					// alert("POSTED");
				post();
			} 
		})	
	})
	function post(){
		$.ajax({
			type       :        'POST',
			url        :        '/loan/application/loan_approval',
			data       :        {'loan_token' : '<?php echo $service_details->loan_token; ?>',
			'cancellation_reason' : $('#txt_cancel_reason').val(),
			'status' : $('#sel_status').val(),
			'date_released' : $('#txt_date_released').val(),
			'disburse_mode' : $('#sel_bank').val()
		},
		beforeSend :        function(){
			show_loader()
		},
		success    :        function(response){
			hide_loader();
			$('#approval_modal').modal('hide')
			if(response.RESPONSE_CODE == "SUCCESS"){
				Swal.fire({
					title: "Loan Status Update Successfully Submitted",
					text: '',
					icon: 'success',
					showConfirmButton : false,
					timer  : 1500
				}).then((result) => {
					if(response.SHOW_PRINT == 1){
						var redirect_data = {
							'show_print_waiver' : 1,
							'loan_token' : '<?php echo $service_details->loan_token; ?>'
						}
						localStorage.setItem("redirect_loan_approval",JSON.stringify(redirect_data));							
					}else if(response.SHOW_PRINT_VOURCHER == 1){
						var redirect_data = {
							'show_print_voucer' : 1,
							'id_cash_disbursement' : response.id_cash_disbursement
						}
						localStorage.setItem("redirect_loan_voucher",JSON.stringify(redirect_data));		
					}
					location.reload();
				});	
			}else if(response.RESPONSE_CODE == "INVALID_STATUS"){
				Swal.fire({
					title: "Invalid Request",
					text: '',
					icon: 'warning',
					showConfirmButton : false,
					timer  : 1500
				})
			}
			console.log({response})
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

	});
	}
</script>
<?php endif; ?>

<?php if($service_details->status == 1 || $service_details->status == 2): ?>
<script type="text/javascript">

	$(document).ready(function(){
		var redirect_data = jQuery.parseJSON(localStorage.getItem("redirect_loan_approval"));
		console.log({redirect_data});
		if(redirect_data != null){
			if(redirect_data.show_print_waiver == 1){
				if(redirect_data.loan_token == '<?php echo $service_details->loan_token; ?>'){
					print_page("/loan/print_application_waiver/"+redirect_data.loan_token)
					console.log("SHOW PRINT MODAL")
					localStorage.removeItem("redirect_loan_approval");
				}
			}
		}

			// var localStorage.setItem("show_print_waiver", 1);
	})
	function print_waiver(){

		print_page("/loan/print_application_waiver/"+'<?php echo $service_details->loan_token; ?>')
	}
</script>
<?php endif; ?>

<?php if($service_details->status == 3 || $service_details->status == 6): ?>
<script type="text/javascript">
	var $id_cash_disbursement = '<?php echo $service_details->id_cash_disbursement ?>';

	$(document).ready(function(){
		var redirect_data = jQuery.parseJSON(localStorage.getItem("redirect_loan_voucher"));
		console.log({redirect_data});
		if(redirect_data != null){
			if(redirect_data.show_print_voucer == 1){
				if(redirect_data.id_cash_disbursement == '<?php echo $service_details->id_cash_disbursement; ?>'){
					print_page("/cash_disbursement/print/"+redirect_data.id_cash_disbursement)
					localStorage.removeItem("redirect_loan_voucher");
				}
			}
		}
	})
	
	function print_cash_disbursement(){
		print_page("/cash_disbursement/print/"+$id_cash_disbursement)
		
	}
	function print_waiver(){

		print_page("/loan/print_application_waiver/"+'<?php echo $service_details->loan_token; ?>')
	}
</script>

<?php endif; ?>
<!-- END CONDITION FOR APPROVAL AND STATUS -->
<?php $__env->stopPush(); ?>











<?php echo $__env->make('adminLTE.admin_template', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/loan/loan_form_display.blade.php ENDPATH**/ ?>