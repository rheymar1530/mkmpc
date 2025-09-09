<?php $__env->startSection('content'); ?>
<style type="text/css">
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
	.text_undeline{
		text-decoration: underline;
		font-size: 20px;
	}
	.tbl_payroll_employee tr>th,.tbl-inputs tr>th{
		padding: 5px;
		padding-left: 5px;
		padding-right: 5px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 14px;
		text-align: center;
	}
	.tbl-inputs-text{
		padding-left: 5px !important;
		padding-right: 5px !important;
		/*padding: px !important;*/
	}
	.tbl_payroll_employee tr>td,.tbl_repayment_display tr>td{
		padding: 3px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 15px;
	}
	.tbl-inputs tr>td{
		padding: 0px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 15px;
	}

	.class_amount{
		text-align: right;

	}
	.cus-font{
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 15px !important;       
	}
	.form-row  label{
		margin-bottom: unset !important;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 15px;
	}
	.form-label{
		margin-bottom: 4px !important;
	}

	.modal-conf {
		max-width:90% !important;
		min-width:90% !important;

	}
	.text_center{
		text-align: center;
	}
	.text_bold{
		font-weight: bold;
	}
	.spn_t{
		font-weight: bold;
		font-size: 16px;
	}
	.spn_txt{
		word-wrap:break-word;
		overflow: hidden;
		text-align: right;
	}
	.label_totals{
		margin-top: -13px !important;
	}
	.border-top{
		border-top:2px solid !important; 
	}
/*	.wrapper2{
		width: 1300px !important;
		margin: 0 auto;
	}*/
	.text-red{
		color: red;
	}
	.swal2-deny{
		padding: 0.375rem 0.75rem !important;
	}
	.control-label{
		font-size: 13px !important;
	}
	@media (min-width: 768px) {

		.col-md-cus{
			flex: 12.666667% !important;
			max-width: 12.666667% !important;
		}	
	}
	.text_danger{
		font-weight: bold;
		color: red;
		font-size: 15px;
	}
	.row_danger{
		background: #ffb3b3 !important;
	}

</style>
<?php

?>
<div class="wrapper2">
	<div class="container-fluid main_form section_body" style="margin-top: -20px;" >
		<?php $back_link = (request()->get('href') == '')?'/payroll':request()->get('href'); ?>
		<a class="btn bg-gradient-secondary btn-sm" href="<?php echo e($back_link); ?>" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Payroll List</a>

		<div class="card" id="repayment_main_div">
			<form id="frm_payroll">
				<div class="card-body col-md-12">
					<h3 class="head_lbl text-center">Payroll
						<?php if($opcode == 1): ?>
						<small>(ID# <?php echo e($payroll->id_payroll); ?>)</small>
						<?php if($payroll->status == 10): ?>
						<span class="badge badge-danger">Cancelled</span>
						<?php endif; ?>
						<?php endif; ?>
					</h3>
					
					<div class="row">
						<div class="col-md-12">	
							<div class="form-group row p-0"  style="margin-top:15px">
								<label class="col-md-1 control-label col-form-label col-md-cus" style="text-align: left">Date Released&nbsp;</label>
								<div class="col-md-3">
									<input type="date" class="form-control in_payroll" id="txt_date_released" key="date_released" value="<?php echo e($payroll->date_released ?? $current_date); ?>">
								</div>
							</div>
							<div class="form-group row p-0">
								<label class="col-md-1 control-label col-form-label col-md-cus" style="text-align: left">Disbursed to&nbsp;</label>
								<div class="col-md-7">
									<select class="form-control in_payroll p-0 sel_employee"  key="disbursed_by" id="sel_disbursed_by">
										<?php if(isset($disbursed_by)): ?>
										<option value="<?php echo e($disbursed_by->tag_id); ?>"><?php echo e($disbursed_by->tag_value); ?></option>
										<?php endif; ?>

									</select>
								</div>
							</div>
							<div class="form-group row p-0">
								<label for="sel_id_payroll_mode" class="col-md-1 control-label col-form-label col-md-cus" style="text-align: left">Payroll Mode&nbsp;</label>
								<div class="col-md-3">
									<select class="form-control in_payroll p-0" id="sel_id_payroll_mode" key="id_payroll_mode">
										<?php $__currentLoopData = $payroll_mode; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<?php $selected_id_payroll_mode = $payroll->id_payroll_mode ?? 1 ;?>
										<option value="<?php echo e($pm->id_payroll_mode); ?>" <?php echo ($pm->id_payroll_mode == $selected_id_payroll_mode)?"selected":"";  ?> ><?php echo e($pm->description); ?></option>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									</select>
								</div>
							</div>										
							<div class="form-group row p-0">
								<label class="col-md-1 control-label col-form-label col-md-cus" style="text-align: left">Salary Mode&nbsp;</label>
								<div class="col-md-3">
									<select class="form-control in_payroll p-0" id="sel_mode" key="id_bank">
										<?php $selected_bank = $payroll->id_bank ?? 0; ?>
										<option value="0">Cash</option>
										<?php $__currentLoopData = $banks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<option value="<?php echo e($b->id_bank); ?>" <?php echo ($selected_bank==$b->id_bank)?"selected":""  ?> >Bank - <?php echo e($b->bank_name); ?></option>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									</select>
								</div>
							</div>
							<div class="form-group row p-0">
								<label class="col-md-1 control-label col-form-label col-md-cus" style="text-align: left">Period&nbsp;</label>
								<div class="col-md-3">
									<input type="date" class="form-control in_payroll" id="sel_period_start" key="period_start" value="<?php echo e($payroll->period_start ?? $temp_period_start); ?>">
								</div>
								<div class="col-md-3">
									<input type="date" class="form-control in_payroll" id="sel_period_end" key="period_end" value="<?php echo e($payroll->period_end ?? $temp_period_end); ?>">
								</div>
							</div>
							<div id="div_working_days_holder"></div>
							<div class="form-group row p-0" id="div_working_days">
								<label class="col-md-2 control-label col-form-label col-md-cus" style="text-align: left">No. of Working Days</label>
								<div class="col-md-2">
									<input type="number" class="form-control in_payroll" id="txt_no_days" key="no_days" value="<?php echo e($payroll->no_days ?? 0); ?>">
								</div>
							</div>
						</div>
						<?php if($allow_post): ?>
						<div class="col-md-12">
							<a class="btn btn-sm bg-gradient-success2" onclick="$('#payroll_employee_modal').modal('show')"><i class="fas fa-plus"></i>&nbsp;Add Employee</a>
							<a class="btn btn-sm bg-gradient-primary2" onclick="parse_master_list()"><i class="fas fa-users"></i>&nbsp;Employee Master List</a>
							<!-- <a href="#t_35">TEST</a> -->
						</div>	
						<?php endif; ?>
						<div class="col-md-12">

							<div class="table-responsive" style="max-height: calc(100vh - 200px);overflow-y: auto;margin-top: 5px !important;overflow-x: auto">
								<table class="table table-bordered table-striped table-head-fixed tbl_payroll_employee" style="white-space: nowrap;">
									<thead id="payroll_table_header">
										
									</thead>
									<tbody id="payroll_body">

									</tbody>
									<tfoot>
										<tr id="payroll_footer" style="background: gray;color: white;">
											
										</tr>

									</tfoot>
								</table>    
							</div> 
							<!-- <div class="form-row">
								<div class="form-group col-md-12">
									<label for="txt_remarks">Remarks</label>
									<textarea class="form-control in_payroll" rows="2" style="resize:none;" id="txt_remarks" key="remarks"><?php echo e($payroll->remarks ?? ''); ?></textarea>
								</div>
							</div> -->
							<?php if($opcode == 1 && $payroll->status == 10): ?>
							<p>Note: <?php echo e($payroll->cancellation_reason); ?></p>
							<?php endif; ?>
						</div>
					</div>
				</div>
				<div class="card-footer">
					<?php if($allow_post): ?>
					<button class="btn bg-gradient-success2 float-right">Save Payroll</button>
					<?php endif; ?>
					<?php if($opcode == 1): ?>
					<div class="btn-group float-right" style="margin-right:10px">
						<button type="button" class="btn bg-gradient-danger2 dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							Print / Export
						</button>
						<div class="dropdown-menu">
							<?php if($allow_post): ?>
							<a class="dropdown-item" href="javascript:void(0)" onclick="print_page('/payroll/print_summary/<?php echo e($payroll->id_payroll); ?>')" >Print Payroll Summary</a>
							<a class="dropdown-item" href="javascript:void(0)" onclick="print_page('/payroll/print_payroll_payslip/<?php echo e($payroll->id_payroll); ?>')" >Print Payslip</a>
							<div class="dropdown-divider"></div>
							<a class="dropdown-item" href="/payroll/excel_summary/<?php echo e($payroll->id_payroll); ?>" target="_blank">Export Payroll Summary (Excel)</a>
							<div class="dropdown-divider"></div>
							<?php endif; ?>
							<a class="dropdown-item" onclick="print_page('/cash_disbursement/print/<?php echo e($payroll->id_cash_disbursement); ?>')">Print Cash Disbursement (CDV #<?php echo e($payroll->id_cash_disbursement); ?>)</a>
							


						</div>
					</div>
					<?php endif; ?>
					<?php if($allow_post): ?>
					<?php if($opcode == 1): ?>
					<button class="btn bg-gradient-warning2 float-right" id="btn_cancel_payroll" style="margin-right:10px;color: white;" type="button">Cancel Payroll</button>
					<?php endif; ?>
					<?php endif; ?>
				</div>
			</form>
		</div>
	</div>
</div>


<?php if($opcode==1): ?>
<?php echo $__env->make('global.print_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?>


<?php if($opcode == 1 && $allow_post): ?>

<?php echo $__env->make('payroll.status_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?>
<?php echo $__env->make('payroll.payroll_employee_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('payroll.master_list', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
<script type="text/javascript">
	let $payroll_employee = {};
	const $div_working_days = $('#div_working_days').detach();
	const $div_daily_rate = $('#div_daily_rate').detach();
	let $payroll_mode_prev = $('#sel_id_payroll_mode').val()
	let view_mode = 0;
	$(function() {
		$.contextMenu({
			selector: '.row_employee',
			callback: function(key, options) {
				var m = "clicked: " + key;
				var id_employee = $(this).attr('row-key');
				console.log({id_employee})
				if(key == "view"){
					view_payroll_employee(id_employee);
				}else if(key == "remove"){
					remove_payroll_employee(id_employee,$(this))
				}
			},
			items: {
				"view": {name: "View Payroll Details", icon: "fas fa-eye"},
				"remove": {name: "Remove", icon: "fas fa-trash"},
				"sep1": "---------",
				"quit": {name: "Close", icon: "fas fa-times" }
			}
		});   
	});
	$(document).ready(function(){
		$('#payroll_employee_modal').on('shown.bs.modal', function () {
			$('#label_no_days').html('<i>x'+$('#txt_no_days').val()+' Days</i>')
		})
		$('#payroll_employee_modal').on('hidden.bs.modal', function (e) {
			if(view_mode == 1){
				clear_modal();
			}
			view_mode = 0;
		})
		initialize_payroll_mode();
	})
	draw_table_header();
	show_no_employee()

	$(document).on('change','#sel_id_payroll_mode',function(){
		// var length  = $('.row_employee[row-key="'+$payroll_emp['employee_details']['id_employee']+'"]').length;
		// if($(this).val() != $payroll_mode_prev){
		// 	$(this).val(2)
		// }else{

		// }
		initialize_payroll_mode();
	});
	function initialize_payroll_mode(){
		var length = Object.keys($payroll_employee).length;
		var val = $('#sel_id_payroll_mode').val();

		if(length > 0){
			$('#sel_id_payroll_mode').val($payroll_mode_prev);
			// $(this).val($payroll_mode_prev);
			toastr.warning("Invalid.")
			return;
		}else{
			$payroll_mode_prev = val;
			initialize_no_working_days();
			clear_modal();			
		}
	}

	function initialize_no_working_days(){
		var val = $('#sel_id_payroll_mode').val();
		if(val == 3){
			$('#div_working_days_holder').html($div_working_days);
			$('#div_daily_rate_holder').html($div_daily_rate);
		}else{
			$('#div_working_days_holder').html('');
			$('#div_daily_rate_holder').html('');
		}
	}

	function remove_payroll_employee($id_employee,obj){


		Swal.fire({
			title: 'Do you want to remove '+$payroll_employee[$id_employee]['employee_details']['id_employee']+' || '+$payroll_employee[$id_employee]['employee_details']['name']+' from payroll record ?',
			icon: 'warning',
			showDenyButton: false,
			showCancelButton: true,
			confirmButtonText: `Yes`,
		}).then((result) => {
			if (result.isConfirmed) {
				delete $payroll_employee[$id_employee];
				$(obj).remove();
				var length  = $('.row_employee').length;

				if(length == 0){
					show_no_employee();
				}
				

				hide_zero_columns();
				set_totals();
			} 
		})	

	}
	$(document).on('keyup','#txt_no_days',function(){
		var val = $(this).val();
		if(val <= 0){
			$(this).val(1)
		}


		var length = Object.keys($payroll_employee).length;
		if(length > 0){
			test_compute_object();
		}
		set_totals();
	})

	
	function test_compute_object(){
		// var $payroll_employee = '{"6":{"additional_compensation":{"ot":0,"night_shift_dif":0,"holiday":0,"paid_leaves":0,"salary_adjustment":0,"13th_month":0},"employee_deductions":{"absences":0,"late":0,"sss_loan":0,"hdmf_loan":0},"cash_advance":{"balance":0,"ca_amount":0},"deducted_benefits":{"sss":600,"philhealth":250,"hdmf":100,"wt":200,"insurance":100},"employee_allowance":{"2":100,"3":1500},"employee_details":{"name":"Jhon Rheymar Caluza Jr","id_employee":6,"daily_rate":"350.00","monthly_rate":"15000.00","sss_amount":"600.00","philhealth_amount":"250.00","hdmf_amount":"100.00","withholding_tax":"200.00","insurance":"100.00"},"allowances":[{"description":"Allowance 2","id_allowance_name":"2","amount":100},{"description":"Allowance 3","id_allowance_name":"3","amount":1500}],"totals":{"total_additional_compensation":0,"total_employee_deduction":0,"total_deducted_benefits":1250,"total_deducted_cash_advance":0,"total_employee_allowance":1600},"basic_pay":1750,"income":{"gross":3350,"net":2100}},"7":{"additional_compensation":{"ot":0,"night_shift_dif":0,"holiday":0,"paid_leaves":0,"salary_adjustment":0,"13th_month":0},"employee_deductions":{"absences":0,"late":0,"sss_loan":0,"hdmf_loan":0},"cash_advance":{"balance":0,"ca_amount":0},"deducted_benefits":{"sss":123,"philhealth":123,"hdmf":123,"wt":123,"insurance":123},"employee_allowance":{},"employee_details":{"name":"Kirk Hammett Jr","id_employee":7,"daily_rate":"350.00","monthly_rate":"15000.00","sss_amount":"123.00","philhealth_amount":"123.00","hdmf_amount":"123.00","withholding_tax":"123.00","insurance":"123.00"},"allowances":[],"totals":{"total_additional_compensation":0,"total_employee_deduction":0,"total_deducted_benefits":615,"total_deducted_cash_advance":0,"total_employee_allowance":0},"basic_pay":1750,"income":{"gross":1750,"net":1135}}}';


		// var $pp = '{"6":{"additional_compensation":{"ot":150,"night_shift_dif":100,"holiday":100,"paid_leaves":250,"salary_adjustment":100,"13th_month":3000,"others":1500},"employee_deductions":{"absences":700,"late":500,"sss_loan":350,"hdmf_loan":755},"cash_advance":{"balance":0,"ca_amount":0},"deducted_benefits":{"sss":600,"philhealth":250,"hdmf":100,"wt":200,"insurance":100},"employee_allowance":{"3":1500},"employee_details":{"name":"Jhon Rheymar Caluza Jr","id_employee":6,"daily_rate":"350.00","monthly_rate":"10000.00","sss_amount":"600.00","philhealth_amount":"250.00","hdmf_amount":"100.00","withholding_tax":"200.00","insurance":"100.00","ca_balance":"0.00"},"allowances":[{"description":"Allowance 3","id_allowance_name":"3","amount":1500,"type":"2"}],"totals":{"total_additional_compensation":5200,"total_employee_deduction":2305,"total_deducted_benefits":1250,"total_deducted_cash_advance":0,"total_employee_allowance":1500},"basic_pay":10000,"income":{"gross":16700,"net":13145}},"7":{"additional_compensation":{"ot":100,"night_shift_dif":200,"holiday":300,"paid_leaves":400,"salary_adjustment":500,"13th_month":3000,"others":600},"employee_deductions":{"absences":100,"late":200,"sss_loan":300,"hdmf_loan":400},"cash_advance":{"balance":0,"ca_amount":0},"deducted_benefits":{"sss":600,"philhealth":250,"hdmf":200,"wt":10,"insurance":50},"employee_allowance":{},"employee_details":{"name":"Kirk Hammett Jr","id_employee":7,"daily_rate":"350.00","monthly_rate":"15000.00","sss_amount":"600.00","philhealth_amount":"250.00","hdmf_amount":"200.00","withholding_tax":"10.00","insurance":"50.00","ca_balance":"0.00"},"allowances":[],"totals":{"total_additional_compensation":5100,"total_employee_deduction":1000,"total_deducted_benefits":1110,"total_deducted_cash_advance":0,"total_employee_allowance":0},"basic_pay":15000,"income":{"gross":20100,"net":17990}}}';

		

		// $payroll_employee = jQuery.parseJSON($pp);




		var no_of_days = $('#txt_no_days').val();
		var payroll_mode = 3;
		$.each($payroll_employee,function(id_employee,information){
			if(payroll_mode == 3){
				information['basic_pay'] = parseFloat(information['employee_details']['daily_rate']) * no_of_days;
			}

			information['income']['gross'] = information['basic_pay'] + information['totals']['total_employee_allowance']+information['totals']['total_additional_compensation'];
			information['income']['net'] = information['income']['gross'] - information['totals']['total_deducted_benefits']-information['totals']['total_deducted_cash_advance']-information['totals']['total_employee_deduction'];
		})


		$.each($payroll_employee,function(id_employee,info){
			table_append(info);
		})

	}
	function show_no_employee(){
		var colspan = $('#payroll_table_header th:visible').length;
		var out = '<tr id="row_no_employee"><td colspan='+colspan+' style="text-align:center">No Employee Selected</td></tr>';
		$('#payroll_body').html(out)
	}



</script>

<script type="text/javascript">
	$('#frm_payroll').submit(function(e){
		e.preventDefault();
		var length = Object.keys($payroll_employee).length;

		if(length == 0){

			Swal.fire({
				title: "No Employee Selected",
				text: '',
				icon: 'warning',
				showCancelButton : false,
				showConfirmButton : false,
				timer : 2500
			});	

			return;	
		}

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
		var payroll_parent = {
			'id_payroll_mode': $('#sel_id_payroll_mode').val(),
			'period_start': $('#sel_period_start').val(),
			'period_end': $('#sel_period_end').val(),
			'no_days': $('#txt_no_days').val() ?? 0,
			'remarks' : $('#txt_remarks').val(),
			'id_bank' : $('#sel_mode').val(),
			'date_released' : $('#txt_date_released').val(),
			'disbursed_by' : $('#sel_disbursed_by').val()
		};
		$('.row_danger').removeClass('row_danger');

		$.ajax({
			type      :       'POST',
			url       :       '/payroll/post',
			beforeSend :      function(){
				show_loader();
			},
			data      :       {'payroll_details' : $payroll_employee,
			'payroll_parent' : payroll_parent,
			'opcode' : '<?php echo $opcode;?>',
			'id_payroll' : '<?php echo $payroll->id_payroll ?? 0 ?>'},
			success   :       function(response){
				hide_loader();
				console.log({response});
				if(response.RESPONSE_CODE == "SUCCESS"){
					var link = "/payroll/view/"+response.id_payroll+"?href="+encodeURIComponent('<?php echo $back_link;?>');
					Swal.fire({
						title: "Payroll Successfully Saved !",
						html : "<a href='"+link+"'>Payroll ID# "+response.id_payroll+"</a>",
						text: '',
						icon: 'success',
						showCancelButton : true,
						confirmButtonText: 'Create Payroll',
						showDenyButton: false,
						denyButtonText: `Print Payslip`,
						cancelButtonText: 'Close',
						showConfirmButton : true,     
						allowEscapeKey : false,
						allowOutsideClick: false
					}).then((result) => {
						if(result.isConfirmed) {
							window.location = "/payroll/create?href="+encodeURIComponent('<?php echo $back_link;?>');
						}else if (result.isDenied) {
    						// var redirect_data = {
    						// 	'show_print_or' : 1,
    						// 	'repayment_token' : response.REPAYMENT_TOKEN
    						// }
    						// localStorage.setItem("redirect_print_or",JSON.stringify(redirect_data));
    						// window.location = 	link;
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
				}else if(response.RESPONSE_CODE == "INVALID_EMPLOYEE"){
					Swal.fire({
						title: "Some employee has a payroll record within this period",
						text: '',
						icon: 'warning',
						showCancelButton : false,
						showConfirmButton : false,
						timer : 2500
					});	

					$.each(response.employee_error,function(i,item){
						$('.row_employee[row-key="'+item+'"]').addClass('row_danger');
					})
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
	}
</script>

<?php if($opcode == 1): ?>
<script type="text/javascript">
	var $pp = '<?php echo json_encode($payroll_employee ?? [])?>';
	$(document).ready(function(){
		$payroll_employee = jQuery.parseJSON($pp);
		$.each($payroll_employee,function(id_employee,info){
			table_append(info);
		});
		set_totals()
	})

	$('#btn_cancel_payroll').on('click',function(){
		$('#status_modal').modal('show')
	})
</script>
<?php endif; ?>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('adminLTE.admin_template', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/payroll/payroll_form.blade.php ENDPATH**/ ?>