<?php $__env->startSection('content'); ?>
<style type="text/css">
	.form-row  label{
		margin-bottom: unset !important;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 15px;
	}
	.separator{
		margin-top: -0.5em;
	}
	.badge_term_period_label{
		font-size: 20px;
	}

	.select2-selection__choice{
		background: #4d94ff !important;
	}
	.select2-search__field{
		color :black !important;
	}
	.custom_card_header{
		padding: 2px 2px 2px 10px !important;
	}
	.custom_card_header > h5{
		margin-bottom: unset;
		font-size:25px;
	}
	.custom_card_footer{
		padding: 5px 5px 5px 5px !important;
	}
	.card{
	/*	box-shadow: 0px 1px 13px 0px rgba(0,0,0,0.75);
		-webkit-box-shadow: 0px 1px 13px 0px rgba(0,0,0,0.75);
		-moz-box-shadow: 0px 1px 13px 0px rgba(0,0,0,0.75);*/
	}
</style>
<div class="container-fluid main_form section_body" style="margin-top:-15px">
	<?php
	$months = [
		1=>"January",
		2=>"February",
		3=>"March",
		4=>"April",
		5=>"May",
		6=>"June",
		7=>"July",
		8=>"August",
		9=>"September",
		10=>"October",
		11=>"November",
		12=>"December"
	];
	$status_list = [
		1=>"Active",
		0=>"Inactive"
	];

	?>

	<div class="row">
		<?php $back_link = (request()->get('href') == '')?'/loan_service':request()->get('href'); ?>
		<a class="btn bg-gradient-secondary btn-sm" href="<?php echo e($back_link); ?>" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Loan Service List</a>
		<div class="card">
			<form id="frm_loan_service">
				<div class="card-body">
					<h3 class="head_lbl text-center mb-3">Loan Service <?php if($opcode==1): ?> (<?php echo e($details->id_loan_service); ?>)  <?php endif; ?></h3>
					<div class="row">
						<div class="col-md-6" id="div_loan_service_parent">
							
							<div class="row">
								
								<div class="col-md-12 p-0">
									<div class="card c-border">

										<div class="card-body">
											<h5 class="lbl_color text-center mb-3">
												Loan Details
											</h5>
											<?php if($opcode == 1): ?>
											<div class="form-group col-md-5">
												<label class="mb-0" for="sel_status">Status</label>
												<select class="form-control parent_loan_service p-0 loan_service" id="sel_status" key="status">
													<?php $__currentLoopData = $status_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val=>$text): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<option value="<?php echo e($val); ?>" <?php echo ($val == $details->status)?"selected":""; ?> ><?php echo e($text); ?></option>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</select>
											</div>
											<?php endif; ?>
											<div class="col-md-12">
												<div class="form-row form_check">
													<div class="form-group col-md-12">
														<label for="txt_loan_name">Loan Name</label>
														<input type="text" class="form-control parent_loan_service loan_service" id="txt_loan_name" placeholder="Loan Name"  key="name">
													</div>
												</div>
												<div class="form-row form_check">
													<div class="form-group col-md-12">
														<label for="txt_loan_description">Loan Description</label>
														<input type="text" class="form-control parent_loan_service loan_service" id="txt_loan_description" placeholder="Loan Description"  key="description">
													</div>
												</div>
												<div class="form-row form_check">
													<div class="form-group col-md-5">
														<label for="txt_loan_description">Disbursement Type</label>
														<select class="form-control parent_loan_service p-0 loan_service" id="sel_disbursement_type" key="id_disbursement_type">
															<?php $__currentLoopData = $disbursement_type; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dis): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<option value="<?php echo e($dis->id_disbursement_type); ?>"><?php echo e($dis->description); ?></option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
														</select>
													</div>
													<div class="form-group col-md-5">
														<label for="sel_status">Loan for</label>
														<select class="form-control parent_loan_service p-0 loan_service" id="sel_status" key="id_membership_type">
															<?php $__currentLoopData = $membership_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<option value="<?php echo e($m->id_membership_type); ?>"
																<?php
																if($opcode ==0){
																	if($m->id_membership_type == 1){
																		echo "selected";
																	}
																}
																?>
																><?php echo e($m->description); ?></option>
																<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
															</select>
														</div>
														<div class="form-group col-md-7">
															<label for="sel_status">Terms Condition</label>
															<select class="form-control parent_loan_service p-0 loan_service" id="sel_terms_condition" key="id_terms_condition">
																<option value="0"> - </option>
																<?php $__currentLoopData = $terms_condition; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																<option value="<?php echo e($tc->id_terms_condition); ?>"><?php echo e($tc->description); ?></option>
																<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
															</select>
														</div>

													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="col-md-12 p-0">
										<div class="card c-border">
											<div class="card-body">
												<h5 class="lbl_color text-center mb-4">
													Loan Amount Range
												</h5>
												<div class="col-md-12">

													<div class="form-row form_check">
														<div class="form-group col-md-4">
															<label for="txt_min_amount">Minimum Amount</label>
															<input type="text" class="form-control parent_loan_service loan_service class_amount" id="txt_min_amount" key="min_amount" value="0.00">
														</div>
														<div class="form-group col-md-4">
															<label for="txt_max_amount">Maximum Amount</label>
															<input type="text" class="form-control parent_loan_service loan_service class_amount" id="txt_max_amount" key="max_amount" value="0.00">
														</div>
														<div class="form-group col-md-4">
															<label for="txt_default_amount">Default Amount</label>
															<input type="text" class="form-control parent_loan_service loan_service class_amount" id="txt_default_amount" key="default_amount" value="0.00">
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="col-md-12 p-0">
										<div class="card c-border">
											<div class="card-body">
												<div class="col-md-12">
													<h5 class="lbl_color text-center mb-4">
														Interest Method & Payment Type
													</h5>
													<div class="form-row form_check">
														<div class="form-group col-md-6">
															<label for="sel_interest_method">Interest Method</label>
															<select class="form-control parent_loan_service p-0 loan_service" id="sel_interest_method" key="id_interest_method">
																<?php $__currentLoopData = $interest_method; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $int): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																<option value="<?php echo e($int->id_interest_method); ?>"><?php echo e($int->description); ?></option>
																<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
															</select>
														</div>
														<div class="form-group col-md-6">
															<label for="sel_payment_type">Payment Type</label>
															<select class="form-control parent_loan_service p-0 loan_service" id="sel_payment_type" key="id_loan_payment_type">
																<?php $__currentLoopData = $loan_payment_type; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																<option value="<?php echo e($pay->id_loan_payment_type); ?>"><?php echo e($pay->description); ?></option>
																<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
															</select>
														</div>
													</div>
													<div id="div_period_holder"></div>

													<?php
													$selected_period_start = $details->start_month_period ?? 1;
													$selected_period_end = $details->end_month_period ?? 1;
													?>
													<div class="form-row form_check" id="div_period">
														
														<div class="form-group col-md-4">
															<label for="sel_start_month_period">Type</label>
															<select class="form-control parent_loan_service p-0 loan_service" id="sel_one_type" key="id_one_time_type">
																<?php $__currentLoopData = $one_time_type; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																<option value="<?php echo e($ot->id_one_time_type); ?>" <?php echo (($details->id_one_time_type ?? 0) == $ot->id_one_time_type)?"selected":""; ?> ><?php echo e($ot->description); ?></option>
																<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
															</select>
														</div>
														<div class="form-group col-md-4">
															<label for="sel_start_month_period">Start Period</label>
															<select class="form-control parent_loan_service p-0 loan_service sel_period" id="sel_start_month_period" key="start_month_period">
																<?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val=>$month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																<option value="<?php echo e($val); ?>" <?php echo ($selected_period_start == $val)?"selected":"" ?> ><?php echo e($month); ?></option>
																<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
															</select>
														</div>
														<div class="form-group col-md-4">
															<label for="sel_end_month_period">End Period</label>
															<select class="form-control parent_loan_service p-0 loan_service sel_period" id="sel_end_month_period" key="end_month_period">
																<?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val=>$month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																<option value="<?php echo e($val); ?>" <?php echo ($selected_period_end == $val)?"selected":"" ?>><?php echo e($month); ?></option>
																<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
															</select>
														</div>
														<div class="form-group col-md-6">
															<label for="sel_repayment_schedule">Loan Payment Schedule</label>
															<select class="form-control parent_loan_service p-0 loan_service" id="sel_repayment_schedule" key="repayment_schedule">

															</select>
														</div>
													</div>
													<div id="div_term_sel">
														<div class="form-row form_check">
															<div class="form-group col-md-4">
																<label for="sel_term_period">Loan Term Period</label>
																<select class="form-control parent_loan_service p-0 loan_service" key="id_term_period" id="sel_term_period">
																	<?php $__currentLoopData = $period; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $per): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

																	<option value="<?php echo e($per->id_period); ?>" <?php echo ($per->id_period == 3)?'selected':'' ?> ><?php echo e($per->description); ?></option>
																	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
																</select>
															</div>
															<div class="form-group col-md-4">
																<label for="sel_interest_period">Interest Period</label>
																<select class="form-control parent_loan_service p-0 loan_service" key="id_interest_period">
																	<?php $__currentLoopData = $period; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $per): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																	<option value="<?php echo e($per->id_period); ?>" <?php echo ($per->id_period == 3)?'selected':'' ?>><?php echo e($per->description); ?></option>
																	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
																</select>
															</div>
															<div class="form-group col-md-4">
																<label for="sel_repayment_period">Loan Payment Period</label>
																<select class="form-control parent_loan_service p-0 loan_service" key="id_repayment_period">
																	<?php $__currentLoopData = $repayment_period; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rep_per): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																	<option value="<?php echo e($rep_per->id_repayment_period); ?>" <?php echo ($rep_per->id_repayment_period == 2)?'selected':'' ?>><?php echo e($rep_per->description); ?></option>
																	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
																</select>
															</div>
														</div>

													</div>
												</div>
											</div>
										</div>
									</div>

									<?php echo $__env->make('loan_service.charges', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
									<?php echo $__env->make('loan_service.condition', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
									<?php echo $__env->make('loan_service.requirements', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

									<div class="col-md-12 p-0">
										<div class="card c-border">
											<div class="card-body">
												<h5 class="lbl_color text-center mb-4">
													Loan Approver(s)
												</h5>
												<div class="col-md-12">

													<div class="form-row">
														<div class="form-group col-md-12">
															<select class="form-control select2" id="sel_approver" multiple="multiple" required="">

																<?php $__currentLoopData = $approvers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ap): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																<option value="<?php echo e($ap->id); ?>"><?php echo e($ap->name); ?></option>
																<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
															</select>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div id="div_term_period">
									<?php echo $__env->make('loan_service.terms', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
									<?php echo $__env->make('loan_service.period', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>	
									<div class="col-md-12 p-0" style="padding-left:20px !important;" id="loan_term_condition">
										<div class="card c-border">
											<div class="card-body">
												<h5 class="lbl_color mb-4" id="h_term_lbl">Loan terms up to <span id="spn_terms"></span> Month(s)</h6>
												<div class="row mt-3">
													<div class="form-group col-md-6">
														<label class="lbl_color mb-0">Interest Rate</label>
														<input type="text" class="form-control parent_loan_service loan_service col_number" value="<?php echo e($details->ls_interest_rate ?? 0); ?>" key="ls_interest_rate">
													</div>
													<div class="form-group col-md-6">
														<label class="lbl_color mb-0">Loan Protection</label>
														<input type="text" class="form-control parent_loan_service loan_service col_number" value="<?php echo e($details->ls_loan_protection ?? 0); ?>" key="ls_loan_protection">
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>

							</div>
						</div>
					</div>
					<div class="card-footer">
						<?php if($opcode == 1 && $credential->is_create): ?>
						<button class="btn bg-gradient-primary2 float-right button_submit" style="margin-left: 10px;" id="btn_save_as">Save As</button>
						<?php endif; ?>
						<?php if(($opcode ==0 && $credential->is_create) || ($opcode ==1 && $credential->is_edit)): ?>
						<button class="btn bg-gradient-success2 float-right button_submit" id="btn_save">Save</button>
						<?php endif; ?>

					</div>
				</form>
			</div>
		</div>
	</div>
	<?php $__env->stopSection(); ?>

	<?php $__env->startPush('scripts'); ?>
	<script type="text/javascript">
		var $term_period = $('#period_body').html();
		var period = jQuery.parseJSON('<?php echo json_encode($months) ?>');
		$period_selection = $('#div_period').detach();
		$term_selection = $('#div_term_sel').detach();
	// $div_deduct_interest = $('#div_deduct_interest').detach();
		$loan_installment_terms = $('#loan_installment_terms').detach();
		$loan_period_one_time = $('#loan_period_one_time').detach();
		$loan_term_condition = $('#loan_term_condition').detach();
		$period_data = [];
		$('#sel_approver').select2({ placeholder: "Select Approver",});


		if(<?php echo e($opcode); ?> == 0){
			$('#sel_charges_group').val(0)
		}
		function animate_element(obj,is_show){

			if(is_show == 1){
				obj.hide();
				obj.show(300);
			}else{
				obj.hide(300, function(){ obj.remove(); });
			}
		}

		$(document).ready(function(){
			initialize_period()
			intialize_select_charge()
			intialize_term_period();
		})
	// $(document).on('select2:open','#sel_charges_group', () => {
	// 	$("[aria-controls='select2-sel_charges_group-results']").focus();
	// });	
		$(document).on('select2:open', (e) => {
			const selectId = e.target.id

			$(".select2-search__field[aria-controls='select2-" + selectId + "-results']").each(function (
				key,
				value,
				) {
				value.focus()
			})
		})	
		function append_period(){
			$start_month = parseInt($('#sel_start_month_period').val());
			$end_month = parseInt($('#sel_end_month_period').val());

			// if(parseInt($start_month) > $end_month){
			// 	alert("Invalid Range");

			// 	return;	
			// }

			var options_repayment_period = '';


			$max_days = max_month_days($end_month);

			var remarks = ($end_month == '2')?'*****If Applicable*****':'';
			console.log({$max_days})
			for(var days =1;days<=$max_days;days++){
				var dt_remarks = ((days == $max_days))?remarks:'';
				options_repayment_period += '<option value="'+days+'">'+period[$end_month]+' '+days+' '+dt_remarks+'</option>';
			} 
			$('#sel_repayment_schedule').html(options_repayment_period)

			$('#period_body').html('');
			$ArrMonths = [];

			if($end_month >= $start_month){
				for(var $i=$start_month;$i<=$end_month;$i++){
					$ArrMonths.push($i);
				}
			}else{
				// for(var $i=$end;$i<=$start;$i++){
				// 	$ArrMonths.push($i);
				// }
				$length = $start_month+$end_month+1;
				for(var $i=$start_month;$i<=($length);$i++){
					$monthIn = ($i > 12)?$i-12:$i;
					$ArrMonths.push($monthIn);
				}
			}

			$.each($ArrMonths,function($i,$m){
				$('#period_body').append($term_period);
				$('.period_count').last().text(period[$m]);
				$('.period_card').last().attr('data-period',$m);
				try{
					initialize_period_form_details($m);
				}catch(err){
					console.log({err})
				}	
			});

			animate_element($('#period_body'),1);
			
			// console.log({$ArrMonths});


			// return;


			// for(var $i=$start_month;$i<=$end_month;$i++){
			// 	$('#period_body').append($term_period);
			// 	$('.period_count').last().text(period[$i])
			// 	$('.period_card').last().attr('data-period',$i);
			// 	try{
			// 		initialize_period_form_details($i)
			// 	}catch(err){
			// 		console.log({err})
			// 	}	 	
			// }
			// animate_element($('#period_body'),1);
		}
	// $(document).on('change','#sel_start_month_period',function(){

	// })

		$(document).on('change','#sel_payment_type',function(){
			if($('#sel_terms_condition').val() > 0){
				return;
			}
			initialize_period()
		})

		$(document).on('change','.sel_period',function(){
			append_period();
		});

		$(document).on('change','#sel_term_period',function(){
			intialize_term_period();
		})
		function initialize_period(init=true){
			var payment_type = $('#sel_payment_type').val();
			$('#div_period_holder').html('')


			if(payment_type == 2){

				$('#div_period_holder').html($period_selection);
				$('#div_term_period').html($loan_period_one_time);
			// $('#div_deduct_interest_holder').html($div_deduct_interest)


				if(init){
					append_period();


					animate_element($('#div_period_holder'),1);
					animate_element($('#div_term_period'),1);					
				}
			}else{
				$('#div_period_holder').html($term_selection);
				$('#div_term_period').html($loan_installment_terms);
			// $('#div_deduct_interest_holder').html('')

				if(init){
					animate_element($('#div_period_holder'),1);
					animate_element($('#div_term_period'),1);					
				}

			}
		}
		let post_save_as = 0;
		$('.button_submit').click(function(){
			var id=$(this).attr('id');
			post_save_as = (id=="btn_save_as")?1:0;
		})
		$('#frm_loan_service').submit(function(e){
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
			var parent_loan_service = {};
			$('.parent_loan_service').each(function(){
				var key = $(this).attr("key");
				if($(this).attr('type') == "checkbox"){
					parent_loan_service[key] = ($(this).prop('checked'))?1:0;
				}else{
					parent_loan_service[key] =  ($(this).hasClass('class_amount'))?decode_number_format($(this).val()):$(this).val();
				}

			})

			console.log({parent_loan_service});
			// return;
		//LOAN INSTALLMENT TERMS
			var terms_period = [];

			$('.service_card').each(function(){
				var details = $(this).find('.term_period_form');
				var terms_array = [];
				var temp_det = {};
				temp_det['period'] = $(this).attr('data-period') ?? null;
				$(details).each(function(){
					var attr = $(this).attr('key');
					if($(this).attr('type') == "checkbox"){
						temp_det[attr] = ($(this).prop('checked'))?1:0;
					}else{
						temp_det[attr] = ($(this).hasClass('class_amount'))?decode_number_format($(this).val()):$(this).val();
					}
				})
				terms_period.push(temp_det);
			});

			console.log({terms_period});

			$.ajax({
				type         :          'GET',
				url          :          '/loan_service/post',
				data         :          {
					'loan_service' : parent_loan_service,
					'terms' :  terms_period,
					'net_pay' : populate_net(),
					'opcode'  : '<?php echo e($opcode); ?>',
					'id_loan_service' : '<?php echo $details->id_loan_service ?? 0 ?>',
					'loan_approvers' : $('#sel_approver').val(),
					'requirements' : populate_requirements(),
					'post_save_as' : post_save_as
				},
				beforeSend   :          function(){
					show_loader();
					$('.mandatory').removeClass('mandatory')
				},
				success      :          function(response){
					hide_loader()
					console.log({response})
					if(response.RESPONSE_CODE == "success"){
					// Swal.fire({
					// 	title: "Loan Service successfully saved",
					// 	text: '',
					// 	icon: 'success',
					// 	showConfirmButton : false,
					// 	timer  : 1300
					// }).then((result) => {
					// 	window.location = '/loan_service/view/'+response.id_loan_service;
					// });	
						var redirect_link = '/loan_service/view/'+response.id_loan_service;
						Swal.fire({

							title: "Loan Service successfully Saved ",
							html : "<a href='"+redirect_link+"'>Loan Service ID#"+response.id_loan_service+"</a>",
							text: '',
							icon: 'success',
							showCancelButton : true,
							confirmButtonText: 'Create Another Loan Service',
							cancelButtonText: 'Back to Loan Service List',
							showConfirmButton : true,     
							allowEscapeKey : false,
							allowOutsideClick: false
						}).then((result) => {
							if(result.isConfirmed) {
								window.location = "/loan_service/create?href="+'<?php echo $back_link;?>';
							}else{
								window.location = '<?php echo $back_link;?>';
							}
						});						
					}else if(response.RESPONSE_CODE == "INVALID_INPUT"){
						toastr.error('Please fill all mandatory fields')
						$invalid_inputs = response.INVALID.invalid_input;
						for(var $i=0;$i<$invalid_inputs.length;$i++){
							$('[key="'+$invalid_inputs[$i]+'"]').addClass("mandatory");
							if($invalid_inputs[$i] == "id_charges_group"){
								toastr.error('Please Select Charges')
							}
						}

						$invalid_net = response.INVALID.net_pay_invalid;
						$.each($invalid_net,function(i,item){
							for(var $j =0; $j<item.length;$j++){
								$('tr.row_net:eq('+i+') [key="'+item[$j]+'"]').addClass('mandatory');
							}

						})


						$terms_invalid = response.INVALID.terms_invalid;
						$.each($terms_invalid,function(i,item){
							for(var $j =0; $j<item.length;$j++){
								$('div.service_card:eq('+i+') [key="'+item[$j]+'"]').addClass('mandatory');
							}
						})	

						$requirements_invalid = response.INVALID.requirements_invalid;
						$.each($requirements_invalid,function(i,item){
							for(var $j =0; $j<item.length;$j++){
								$('tr.row_requirements:eq('+i+') [key="'+item[$j]+'"]').addClass('mandatory');

							}
						})	

						console.log({$invalid_net});
					}else if(response.RESPONSE_CODE == "CREDENTIAL_ERROR"){
						Swal.fire({
							title: response.message,
							text: '',
							icon: 'warning',
							showConfirmButton : false,
							timer : 1500
						});	
					}else if(response.RESPONSE_CODE == "DUPLICATE_NAME"){
						Swal.fire({
							title: response.message,
							text: '',
							icon: 'warning',
							showConfirmButton : false,
							timer : 2500
						});	

						return;					
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
		function populate_net(){
			var net_pay = [];

			$('tr.row_net').each(function(){
				var row_data ={};
				$(this).find('.frm-net').each(function(key,val){
					var key = $(this).attr('key');
					row_data[key]  = decode_number_format($(this).val());				
				})
				net_pay.push(row_data);
			})

			console.log({net_pay});

			return net_pay;
		}
		function populate_requirements(){

			var requirements = [];

			$('tr.row_requirements').each(function(){
				var row_data ={};
				$(this).find('.frm-requirements').each(function(key,val){
					var key = $(this).attr('key');
					row_data[key]  = $(this).val();				
				})
				requirements.push(row_data);
			})

			console.log({requirements});

			return requirements;

		}

		function intialize_term_period(){
			var $period = "(Per "+$( "#sel_term_period option:selected").text()+")";
			$('#spn_term_period').text($period)
			console.log($period);
		}

		$(document).on("focus",".class_amount",function(){
			var val = $(this).val();
			if(val == '' || val == 'NaN'){
				val = '0.00';
			}
			$(this).val(decode_number_format(val));	
		})

		$(document).on("blur",".class_amount",function(){
			var val = $(this).val();
			if(!$.isNumeric(val)){
				val = 0;
			}
			$(this).val(number_format(parseFloat(val)));
		})


		function can_be_renew_after_no_pay(obj){
			var checked = $(obj).prop('checked');

			$('#txt_renew_payments').val('');
			$('#txt_renew_payments').prop('disabled',!checked);
		}

		const maker_cbu = (obj) =>{
			var checked = $(obj).prop('checked');
			$('#txt_comaker_cbu').val('');
			$('#txt_comaker_cbu').prop('disabled',!checked);

		}

	</script>


	<?php if($opcode == 1): ?>
	<script type="text/javascript">
		const $details = jQuery.parseJSON('<?php echo json_encode($details ?? []) ?>');
		const $terms = jQuery.parseJSON('<?php echo json_encode($terms ?? []) ?>');
		const $net_pay = jQuery.parseJSON('<?php echo json_encode($net_pay ?? []) ?>');
		const $approvers = jQuery.parseJSON('<?php echo $loan_approvers?>');
		const $requirements = jQuery.parseJSON('<?php echo $requirements?>');

		const $term_condition_details = jQuery.parseJSON('<?php echo json_encode($terms_condition_details);?>');


		$(document).ready(function(){

			$('.parent_loan_service').each(function(){
				var key = $(this).attr('key');
				if($(this).attr('type') == "checkbox"){
					var checked = ($details[key] == 1)?"checked":"";
					$(this).prop('checked',checked);
				}else{
					$(this).val($details[key] ?? 0);
				}
				if($(this).attr('id') == "sel_charges_group"){
					intialize_select_charge()
				}
			})
			initialize_period();


			if($('#sel_terms_condition').val() > 0){
				init_terms_cond($term_condition_details);
			}
			if($('#sel_one_type').val() == 2){
				init_one_time();
			}
			

		//SET THE OPTION FORM CHARGES

			$('#sel_repayment_schedule').val(<?php echo e($details->repayment_schedule ?? 1); ?>)


		if($details['id_loan_payment_type'] == 2){ // one time payment
			$.each($terms,function(i,item){
				$period_data[item.period] = item;
				initialize_period_form_details(item.period);


				// var crd_input = $('[data-period="'+item.period+'"]').find('.onetime_period');
				// var enable_no_payments_renew = (item.is_renew_no_pay=="1")?0:1;
				// $('[data-period="'+item.period+'"]').find('input.txt_renew_payments').prop("disabled",enable_no_payments_renew)
				// $(crd_input).each(function(){
				// 	var k = $(this).attr('key');
				// 	if($(this).attr('type') == "checkbox"){
				// 		var checked = (item[k] == 1)?"checked":"";
				// 		$(this).prop('checked',item[k]);
				// 	}else{
				// 		var val = item[k];
				// 		$(this).val(val);
				// 	}
				// })
			})
		}else{ // installment
			$('#terms_body').html('');
			$.each($terms,function(i,item){
				$('#terms_body').append($term_card);

				set_term_counter();

				var crd_input = $('.term_card').last().find('.installment_terms');
				var enable_no_payments_renew = (item.is_renew_no_pay=="1")?0:1;
				$('.term_card').last().find('input.txt_renew_payments').prop("disabled",enable_no_payments_renew)
				$(crd_input).each(function(){
					if($(this).hasClass('sel_charges')){
						$(this).html('<option value="'+item['id_charges_group']+'">'+item['charge_name']+'</option>')
					}
					
					var k = $(this).attr('key');
					var kkk = item[k];
					if($(this).attr('type') == "checkbox"){
						var checked = (item[k] == 1)?"checked":"";

						$(this).prop('checked',item[k]);
					}else{
						var val = item[k];
						$(this).val(val);
					}
					
				})		
				// charges_text
				console.log({$charges_array})
			});
		}

		//Initialize can be renew
		var enable_no_payments_renew = ($details['is_renew_no_pay']=="1")?0:1;
		$('#txt_renew_payments').prop("disabled",enable_no_payments_renew);

		var enable_cbu_maker = ($details['with_maker_cbu']=="1")?0:1;
		$('#txt_comaker_cbu').prop("disabled",enable_cbu_maker);

		//Initialize net pay
		initialize_net_pay_table()
		//Initialize requirements table
		initialize_requirements();

		//Initialize charge list
		if($details['charges_list'] != null){
			var $charges_array = $details['charges_list'].split("!!");
			var charges_html = "";
			for(var j=0;j<$charges_array.length;j++){
				charges_html+= '<li>'+$charges_array[j]+'</li>'
			}

			$('#charges_text').html(charges_html);
		}
		$('.class_amount').each(function(){
			$(this).val(number_format(parseFloat($(this).val())))
		})
		//INITIALIZE THE LOAN APPROVER
		$('#sel_approver').val($approvers).change();

		intialize_term_period();
	})
		function initialize_net_pay_table(id_terms,$card){

			$('#net_body').html('');

			$($net_pay).each(function($i,$item){
				$('#net_body').append($net_row);
				var forms = $('tr.row_net').last().find('.frm-net');
				$(forms).each(function(){
					var key = $(this).attr('key');
					$(this).val($item[key])	
				})
			})
			return;
		}
		function initialize_requirements(){
			$('#requirements_body').html('');

			$($requirements).each(function($i,$item){
				$('#requirements_body').append($requirements_row);
				var requirements = $('tr.row_requirements').last().find('.frm-requirements');
				$(requirements).each(function(){
					var key = $(this).attr('key');
					$(this).val($item[key])	
				})
			})

			numbering_requirments()
			return;
		}
		function initialize_period_form_details(period){

			var item = $period_data[period];
			console.log({item})
			var crd_input = $('[data-period="'+item.period+'"]').find('.onetime_period');
			var enable_no_payments_renew = (item.is_renew_no_pay=="1")?0:1;

			$('[data-period="'+item.period+'"]').find('input.txt_renew_payments').prop("disabled",enable_no_payments_renew)
			$(crd_input).each(function(){
				if($(this).hasClass('sel_charges')){
					$(this).html('<option value="'+item['id_charges_group']+'">'+item['charge_name']+'</option>')
				}
				var k = $(this).attr('key');
				if($(this).attr('type') == "checkbox"){
					var checked = (item[k] == 1)?"checked":"";
					$(this).prop('checked',item[k]);
				}else{
					var val = item[k];
					$(this).val(val);
				}
			})
		// $('.sel_charges').last().attr('id',makeid(10));
		// intialize_select_charge()
			if(item.charges_list != null){
				var $charges_array = item.charges_list.split("!!");
				var charges_html = "";
				for(var j=0;j<$charges_array.length;j++){
					charges_html+= '<li>'+$charges_array[j]+'</li>'
				}

				$('[data-period="'+item.period+'"]').find('.charges_text').html(charges_html);
			}
		}

	</script>
	<?php endif; ?>
	<script type="text/javascript">
		function max_month_days($month){
			$31_days = [1,3,5,7,8,10,12];
			if($.inArray(parseInt($month),$31_days) >= 0){
				return 31;
			}else if($month == 2){
				return 29;
			}else{
				return 30;
			}
		}
	</script>

	<script type="text/javascript">
		$(document).on('change','#sel_terms_condition',function(){
			var val = $(this).val();
			console.log({val});

			$.ajax({
				type            :      'GET',
				url             :     '/loan_service/parseTermsCondition',
				data            :     {'id_terms_condition' : val},
				success         :     function(response){
					console.log({response});
					if(val > 0){
						init_terms_cond(response.details);
			
						// animate_element($('#div_term_period'),0);
					}else{
						$('#sel_payment_type').val(1).trigger('change').prop('disabled',false);
						// initialize_period();
						// animate_element($('#div_term_period'),1);
					}
				}
			})
		})

		const init_terms_cond = (details)=>{
			$('#sel_payment_type').val(1).trigger('change').prop('disabled',true);
			initialize_period(false);
			$('#div_term_period').html($loan_term_condition);
			$('#h_term_lbl').show();
			$('#spn_terms').text(details.terms_max);
			animate_element($('#div_term_period'),1);			
		}
		$(document).on('change','#sel_one_type',function(){
			init_one_time();
		});
		const init_one_time = ()=>{
			var val = $('#sel_one_type').val();
			if(val == 1){
				initialize_period();
				$('#sel_start_month_period').prop('disabled',false);
			}else{
				$('#sel_start_month_period').prop('disabled',true);
				initialize_period(false);
				$('#div_term_period').html($loan_term_condition);
				$('#h_term_lbl').hide();
			}			
		}
	</script>
	<?php $__env->stopPush(); ?>

<?php echo $__env->make('adminLTE.admin_template', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/loan_service/loan_service_form.blade.php ENDPATH**/ ?>