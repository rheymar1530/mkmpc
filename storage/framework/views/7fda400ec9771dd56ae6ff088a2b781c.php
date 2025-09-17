<?php $__env->startSection('content'); ?>
<style type="text/css">
	.main_form{
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		width: 1200px;
	}
	.form-row  label{
		margin-bottom: unset !important;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 15px;
	}
	.form-row{
		margin-top: -3px;
	}
	.nav-step .active{
		background: #dc3545 linear-gradient(180deg,#e15361,#dc3545) repeat-x !important;

	}
	.nav-pills .nav-link{
		margin-right: 15px !important;
	}
	.dark-mode a.nav_sel:hover{
		color: white !important;
		background: #e6e6e64D;
	}
	a.nav_sel:hover{
		color: black !important;
		background: #e6e6e64D;
	}
	.class_amount,.class_number{
		text-align: right;

	}
	/*remove the spinner on number*/
	input[type=number]::-webkit-inner-spin-button, 
	input[type=number]::-webkit-outer-spin-button { 
		-webkit-appearance: none; 
		margin: 0; 
	}
	.dark-mode .mandatory{
		border-color: rgba(232, 63, 82, 0.8) !important;
		box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(232, 63, 82, 0.6) !important;
		outline: 0 none !important;
	}
	.mandatory{
		border-color: rgba(232, 63, 82, 0.8);
		box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(232, 63, 82, 0.6);
		outline: 0 none;
	}
	.mandatory:focus{
		border-color: rgba(232, 63, 82, 0.8) !important;
		box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(232, 63, 82, 0.6) !important;
		outline: 0 none !important;
	}
	#tbl_payments tr>th{
		padding: 5px;
		padding-left: 5px;
		padding-right: 5px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 12px;
	}
	#tbl_payments tr>td{
		padding: 0px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 12px;
	}
	.frm-ben{
		height: 28px !important;
		width: 100%;    
	}
	.form-row  label{
		margin-bottom: unset !important;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 13px;
	}
	button.swal2-deny.swal2-styled {
		padding: .375rem .75rem;
	}
	.bgd_cancel{
		font-size: 25px !important;
	}
	.spn_details{
		font-size: 14px;
	}
</style>
<div class="container main_form section_body" style="margin-top:-15px">
	<?php $back_link = (request()->get('href') == '')?'/cash_receipt':request()->get('href'); ?>
	<a class="btn bg-gradient-secondary btn-sm" href="<?php echo e($back_link); ?>" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Cash Receipt list</a>
	<?php if($opcode == 1 && $credential->is_create): ?>
	<a class="btn bg-gradient-info2 btn-sm" href="/cash_receipt/add" style="margin-bottom:10px;"><i class="fas fa-plus"></i>&nbsp;&nbsp;Create New Cash Receipt</a>
	<?php endif; ?>
	<div class="row">
		<div class="col-md-12">
			<div class="card">
				<form id="frm_submit_cash_receipt">
					<div class="card-body">
						<h3 class="lbl_color text-center"><?php if($opcode == 0): ?>Create <?php endif; ?> Cash Receipt 
							<?php if($opcode == 1): ?> <small>(ID# <?php echo e($details->id_cash_receipt); ?>)</small>  &nbsp;             
							<?php if($details->status == 10): ?>
							<span class="badge badge-danger bgd_cancel">Cancelled</span>
							<?php endif; ?>
						<?php endif; ?></h3>
						<div class="row">
							<div class="col-md-12">
								<div style="margin-top:20px">
									<div class="col-md-12 p-0">
										<div class="form-row">
											<div class="form-group col-md-6">
												<label for="txt_received_date">Date Received</label>
												<input type="date" class="form-control cash_receipt" id="txt_received_date" key="date_received" value="<?php echo e($details->date_received ?? $current_date); ?>">
											</div>
										</div>
										<div class="form-row">
											<div class="form-group col-md-4">
												<label for="sel_received_from">Received from</label>
												<select id="sel_received_from" class="form-control cash_receipt p-0" key="payee_type">
													<option value="1">Member</option>
													<option value="2">Others</option>
												</select>
											</div>
											<div class="form-group col-md-8" id="form_member">
												<label>Select member</label>
												<select id="sel_id_member" class="form-control cash_receipt p-0" key="id_member" required>
													<?php if(isset($member_selected)): ?>
													<option value="<?php echo e($member_selected->tag_id); ?>"><?php echo e($member_selected->tag_value); ?></option>
													<?php endif; ?>
												</select>
											</div>
										</div>
									</div>
									<div class="col-md-12 p-0">
										<div class="card c-border">
											<div class="card-body">
												<h5 class="lbl_color">Paymode</h5>
												<div class="form-row mt-3">
													<div class="form-group col-md-3">
														<select id="sel_paymode" class="form-control cash_receipt p-0" key="id_paymode">
															<?php $__currentLoopData = $paymode; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<option value="<?php echo e($pay->id_paymode); ?>"><?php echo e($pay->description); ?></option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
														</select>
													</div>
												</div>
												<div id="paymode_form"></div>

												<div class="form-row form_check">
													<div class="form-group col-md-4">
														<label for="sel_check_type">Check Type</label>
														<select id="sel_check_type" class="form-control cash_receipt p-0" key="id_check_type">
															<option value="1">On-dated</option>
															<option value="2">PDC	</option>
														</select>
													</div>
												</div>

												<div class="form-row form_check">
													<div class="form-group col-md-4">
														<label for="txt_check_no">Check No.</label>
														<input type="text" class="form-control cash_receipt" id="txt_check_no" placeholder="Check No."  key="check_no">
													</div>	
													<div class="form-group col-md-4">
														<label for="txt_check_date">Check Date</label>
														<input type="date" class="form-control cash_receipt" id="txt_check_date" placeholder="Check Date"  key="check_date">
													</div>	
												</div>

												<div class="form-row form_check">
													<div class="form-group col-md-8">
														<label for="txt_bank">Bank</label>
														<input type="text" class="form-control cash_receipt" id="txt_bank" placeholder="Bank"  key="bank">
													</div>										
												</div>

												<div class="form-row form_bank">
													<div class="form-group col-md-8">
														<label for="sel_bank_account">Bank</label>
														<select id="sel_bank_account" class="form-control cash_receipt p-0" key="id_bank">
															<?php $__currentLoopData = $banks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bank): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<option value="<?php echo e($bank->id_bank); ?>"><?php echo e($bank->bank_name); ?></option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
														</select>
													</div>
												</div>

												<div class="form-row form_bank">
													<div class="form-group col-md-4">
														<label for="txt_date_credited">Date Credited</label>
														<input type="date" class="form-control cash_receipt" id="txt_date_credited"  key="date_credited">
													</div>	
													<div class="form-group col-md-8">
														<label for="txt_reference_no">Reference No</label>
														<input type="text" class="form-control cash_receipt" id="txt_reference_no" placeholder="Reference No"  key="bank_reference_no">
													</div>	
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="card c-border">
									<div class="card-body">
										<h5 class="lbl_color">Payments</h5>
										<?php if($credential->is_create || $credential->is_edit): ?>
										<button type="button" class="btn btn-sm bg-gradient-primary2 float-right" onclick="append_payment()"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add Payment</button>
										<?php endif; ?>
										<div class="table-responsive" style="max-height: calc(100vh - 200px);overflow-y: auto;margin-top: 5px !important;overflow-x: auto">
											<table class="table table-bordered table-stripped table-head-fixed" id="tbl_payments" style="white-space: nowrap;">
												<thead>
													<tr class="table_header_dblue">
														<th class="table_header_dblue" style="width:60%">Payment Type</th>
														<th class="table_header_dblue">Amount</th>
														<th class="table_header_dblue" style="min-width: 20px;max-width: 20px;"></th>
													</tr>
												</thead>
												<tbody id="payment_body">
													<?php if($opcode == 1): ?>
													<?php $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<tr class="row_payments">
														<td>
															<select class="form-control frm-ben p-0 sel_id_payment" key="id_payment_type">
																<?php $__currentLoopData = $payment_type; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
																<?php $selected_payment = ($p->id_payment_type == $pay->id_payment_type)?"selected":""?>
																<option value="<?php echo e($p->id_payment_type); ?>" <?php echo e($selected_payment); ?>><?php echo e($p->description); ?></option>
																<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
															</select>
														</td>
														<td><input type="text" name="" class="form-control frm-ben class_amount txt_pay_amount t_amount" value="<?php echo e(number_format($pay->amount,2)); ?>" key="amount"></td>
														<td><a onclick="remove_row(this)" style="margin-left:5px;margin-top: 10px !important;"><i class="fa fa-times"></i></a></td>
													</tr>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
													<?php endif; ?>
												</tbody>
											</table>
										</div>	
									</div>
								</div>

								<div class="card c-border">
									<div class="card-body">
										<h5 class="lbl_color">Cash Receipt</h5>
										<div class="row p-0 mt-3">
											<div class="col-sm-12 p-1">
												<div class="form-group row p-0 div_collection">
													<label for="txt_amount" class="col-sm-3 control-label col-form-label" style="text-align: left">Total Amount :&nbsp;</label>
													<div class="col-sm-4">
														<input type="text" name="" class="form-control class_amount" id="txt_total_amount" value="0.00" readonly>
													</div> 
												</div>			
												<div class="form-group row p-0 div_collection">
													<label for="txt_or_no" class="col-sm-3 control-label col-form-label" style="text-align: left">OR Number :&nbsp;</label>
													<div class="col-sm-4">
														<input type="text" name="" class="form-control cash_receipt" id="txt_or_no" key="or_no" placeholder="" required>
													</div> 
												</div>	
												<div class="form-group row p-0 div_collection">
													<label for="txt_ar_no" class="col-sm-3 control-label col-form-label" style="text-align: left">AR Number :&nbsp;</label>
													<div class="col-sm-4">
														<input type="text" name="" class="form-control cash_receipt" id="txt_ar_no" key="ar_no" placeholder="(Optional)">
													</div> 
												</div>	
												<div class="form-group row p-0 div_collection">
													<label for="txt_reference" class="col-sm-3 control-label col-form-label" style="text-align: left">Reference :&nbsp;</label>
													<div class="col-sm-4">
														<input type="text" name="" class="form-control cash_receipt" id="txt_reference" key="reference_no" placeholder="(Optional)">
													</div> 
												</div>	
												<div class="form-group row p-0 div_collection">
													<label for="txt_reference" class="col-sm-3 control-label col-form-label" style="text-align: left">Remarks :&nbsp;</label>
													<div class="col-sm-8">
														<input type="text" name="" class="form-control cash_receipt" id="txt-remarks" key="remarks">
													</div> 
												</div>	
											</div>
										</div>

									</div>
									
								</div>
								<?php if($opcode == 1): ?>
								<div style="padding-top: 10px !important;">
									<span class="text-muted spn_details"><b>Date Created:</b> <?php echo e($details->date_created); ?></span><br>
									<?php if($details->status == 10): ?>
									<span class="text-muted spn_details"><b>Date Cancelled:</b> <?php echo e($details->date_cancelled); ?></span><br>
									<span class="text-muted spn_details"><b>Cancellation Reason:</b> <?php echo e($details->cancel_reason); ?></span>
									<?php endif; ?>
								</div>
								<?php $print_route ='/cash_receipt/print?reference='.$details->id_cash_receipt; ?>
								<?php endif; ?>
							</div>
						</div>
					</div>
					<div class="card-footer p-2">
						<?php if(($opcode == 0 && $credential->is_create) || ($opcode == 1 && $details->status != 10 && $credential->is_edit)): ?>
						<button class="btn bg-gradient-success2 float-right"><i class="far fa-save"></i>&nbsp;Save Cash Receipt</button>
						<?php endif; ?>
						<?php if($opcode == 1): ?>
						<!-- <button type="button" class="btn bg-gradient-danger float-right" style="margin-right:10px" onclick="print_frame('<?php echo e($print_route); ?>')"><i class="fas fa-print" ></i>&nbsp;Print Cash Receipt</button> -->
						<div class="btn-group float-right" style="margin-right:10px">
							<button type="button" class="btn bg-gradient-danger2 dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								Print
							</button>
							<div class="dropdown-menu">
								<?php if($details->status != 10): ?>
								<a class="dropdown-item" href="javascript:void(0)" onclick="print_frame('<?php echo e($print_route); ?>')">Print Cash Receipt</a>
								<?php endif; ?>
								<a class="dropdown-item" onclick="print_page('/cash_receipt_voucher/print/<?php echo e($details->id_cash_receipt_voucher); ?>')">Print CRV (CRV #<?php echo e($details->id_cash_receipt_voucher); ?>)</a>
							</div>
						</div>

						<?php if($details->status != 10 && $credential->is_cancel): ?>
						<button type="button" class="btn bg-gradient-warning2 float-right" style="margin-right:10px;color:white" onclick="show_cancel_modal('<?php echo e($details->id_cash_receipt); ?>')"><i class="fas fa-times" ></i>&nbsp;Cancel Cash Receipt</button>
						<?php endif; ?>
						<?php endif; ?>
						<?php if($opcode ==1): ?>

						<!-- 	<button type="button" class="btn bg-gradient-danger float-right" style="margin-left:10px" onclick="print_page('/journal_voucher/print/<?php echo e($details->id_cash_receipt_voucher); ?>')"><i class="fas fa-print" ></i>&nbsp;Print Journal Voucher (JV# <?php echo e($details->id_cash_receipt_voucher); ?>)</button> -->
						<?php endif; ?>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<?php if($opcode == 1): ?>

<?php echo $__env->make('cash_receipt.cancel_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('cash_receipt.or_print_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('global.print_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?>

<?php $__env->stopSection(); ?>



<?php $__env->startPush('scripts'); ?>
<?php if($opcode == 1): ?>
<script type="text/javascript">
	$(document).ready(function(){
		// console.log("EXECUTEE EDITT CHECK PRINT")
		var is_print = window.localStorage.getItem("print_data");
		if(is_print != null){
			is_print = JSON.parse(is_print);
			if(is_print.show_print && is_print.id_cash_receipt == details['id_cash_receipt']){
				console.log("------SHOW PRINT----------");
				print_frame('<?php echo e($print_route); ?>')

				window.localStorage.removeItem("print_data")
			}
		}
	})

	function print_frame(route){
		$('#print_frame_or').attr('src',route);
	}

</script>
<?php endif; ?>
<script type="text/javascript">

	const opcode = <?php echo e($opcode); ?>; //0-add;1-edit
	
	var details = jQuery.parseJSON('<?php echo json_encode($details ?? []); ?>');
	$options = jQuery.parseJSON('<?php echo json_encode($payment_type); ?>');
	$input_member = '<label>&nbsp;&nbsp;</label><input type="text" class="form-control cash_receipt" id="txt_payee_text" key="payee_text" value="'+(details['payee_text'] ?? '')+'" required>';
	if(opcode == 1){
		$('.cash_receipt').each(function(){
			var key = $(this).attr('key');
			$(this).val(details[key]);
			console.log({key});
		})
		compute_total_amount();
	}else{
		append_payment();
	}

	$form_check = $('.form_check').detach();
	$form_bank = $('.form_bank').detach();
	$select_member = $('#form_member').html();
	initialize_paymode();
	initialize_received_from();

	$(document).on('select2:open', () => {
		document.querySelector('.select2-search__field').focus();
	});	
	$(document).ready(function(){
		intialize_select2();
	})
	$(document).on('change','#sel_paymode',function(){
		initialize_paymode();
	})

	function initialize_paymode(){
		var val = $('#sel_paymode').val();
		if(val == 1){
			$('#paymode_form').html('');
		}else if(val == 2){
			$('#paymode_form').html($form_check);
		}else if(val == 3){
			$('#paymode_form').html($form_bank);
		}
		animate_element($('#paymode_form'),1);
	}

	$(document).on('change','#sel_received_from',function(){
		initialize_received_from();
	})

	function initialize_received_from(){
		var val = $('#sel_received_from').val();
		if(val == 1){ // Member
			$('#form_member').html($select_member);
			intialize_select2();
		}else{ //Others
			$('#form_member').html($input_member);
		}		
	}
	function append_payment(){
		var out = '';
		var options = '';

		$.each($options,function(i,item){
			options += '<option value="'+item.id_payment_type+'">'+item.description+'</option>';
		})

		out += '<tr class="row_payments">';
		out += '<td><select class="form-control frm-ben p-0 sel_id_payment" key="id_payment_type">'+options+'</select></td>';
		out += '<td><input type="text" name="" class="form-control frm-ben class_amount txt_pay_amount t_amount" value="0.00" key="amount"></td>';
		out += '<td><a onclick="remove_row(this)" style="margin-left:5px;margin-top: 10px !important;"><i class="fa fa-times"></i></a></td>';
		out += '</tr>';

		$('#payment_body').append(out);

		animate_element($('.row_payments').last(),1)
	}


	function remove_row(obj){
		var parent_row = $(obj).closest('tr')
		parent_row.remove()
		// animate_element(parent_row,2)
		compute_total_amount()
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
	function intialize_select2(){		
		$("#sel_id_member").select2({
			minimumInputLength: 2,
			width: '80%',
			createTag: function (params) {
				return null;
			},
			ajax: {
				tags: true,
				url: '/search_member',
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
	function number_format(number){
		var result =  number.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
		return result;
	}
	function decode_number_format(number){
		var result=number.replace(/\,/g,''); // 1125, but a string, so convert it to number
		result=parseFloat(result,10);
		return result;
	}
	function compute_total_amount(){
		var total = 0;
		$('.txt_pay_amount').each(function(){
			var amt;
			if($.isNumeric($(this).val())){
				amt = parseFloat($(this).val());
			}else{
				if($.isNumeric(decode_number_format($(this).val()))){
					amt = decode_number_format($(this).val());
				}else{
					amt = 0;
				}
			}
			total+=amt;
		})
		$('#txt_total_amount').val(number_format(total));
	}
	$('#frm_submit_cash_receipt').submit(function(e){
		e.preventDefault();
		var payment_count = $('tr.row_payments').length;
		if(payment_count == 0){
			Swal.fire({
				title: "Please select atleast 1 payment",
				text: '',
				icon: 'warning',
				showConfirmButton : false,
				confirmButtonText: 'OK',
				confirmButtonColor: "#DD6B55",
				timer : 1500
			}); 

			return;
		}

		Swal.fire({
			title: 'Do you want to save this?',
			icon: 'warning',
			showDenyButton: false,
			showCancelButton: true,
			confirmButtonText: `Save`,
			showDenyButton: false,
			// denyButtonText: 'Save and Print',
		}).then((result) => {
			if (result.isConfirmed) {
				post('save');
			}else if(result.isDenied){
				post("save_and_print")
			}
		})	
	})
	$(document).on('keyup','.txt_pay_amount',function(){
		compute_total_amount();
	});
	function populate_data(){
		var formData = {};
		$('.cash_receipt').each(function(){
			var key = $(this).attr('key');
			formData[key] = $(this).val();
		})

		return formData;
		console.log({formData});
	}
	function populate_payments(){

		return $payments;
		console.log({$payments});
	}
	$(document).on('keyup','.mandatory',function(){
		$(this).removeClass('mandatory');
	})
	function post(btn_post){
		$('.mandatory').removeClass('mandatory')
		var $data = {};
		$data['fields'] = populate_data();


		var detect_0 = false;
		//populate_payments
		var $payments = [];
		$('.row_payments').each(function(){
			var temp = {};
			temp['id_payment_type'] = $(this).find('.sel_id_payment').val();
			var amt  = decode_number_format($(this).find('.t_amount').val());

			if(amt == 0){
				detect_0 = true;
				$(this).find('.t_amount').addClass('mandatory');
			}
			temp['amount'] = amt;
			$payments.push(temp);
		})

		if(detect_0){
			Swal.fire({
				title: "Invalid Amount",
				text: '',
				icon: 'warning',
				showConfirmButton : false,
				confirmButtonText: 'OK',
				confirmButtonColor: "#DD6B55",
				timer : 1500
			}); 

			return;			
		}

		$data['payments'] = $payments;
		$data['opcode'] = opcode;
		$data['id_cash_receipt'] = details['id_cash_receipt'] ?? 0;

		$.ajax({
			type        :         'POST',
			url         :         '/cash_receipt/post',
			data        :         $data,
			beforeSend  :         function(){
				show_loader();
			},
			success     :         function(response){
				console.log({response});
				hide_loader();
				if(response.RESPONSE_CODE == "SUCCESS"){
					var link = '/cash_receipt/view/'+response.id_cash_receipt+"?href="+encodeURIComponent('<?php echo $back_link;?>');
					Swal.fire({

						title: "Cash Receipt successfully saved",
						html : "<a href='"+link+"'>Cash Receipt ID# "+response.id_cash_receipt+"</a>",
						text: '',
						icon: 'success',
						showCancelButton : true,
						confirmButtonText: 'Create More ',
						showDenyButton: true,
						denyButtonText: `Print Cash Receipt`,
						cancelButtonText: 'Close',
						showConfirmButton : true,     
						allowEscapeKey : false,
						allowOutsideClick: false
					}).then((result) => {
						if(result.isConfirmed) {
							window.location =  "/cash_receipt/add?href="+encodeURIComponent('<?php echo $back_link;?>');
						}else if (result.isDenied) {
							var print_obj = {};
							print_obj['show_print'] = true;
							print_obj['id_cash_receipt'] = response.id_cash_receipt;
							window.localStorage.setItem("print_data",JSON.stringify(print_obj));

							window.location = 	link;
						}else{
							window.location = '<?php echo $back_link;?>';
						}
					});
										/******************************************************/
										// Swal.fire({
										// 	title: "Cash Receipt successfully saved",
										// 	text: '',
										// 	icon: 'success',
										// 	showConfirmButton : false,
										// 	timer  : 1300
										// }).then((result) => {
					     //                    // location.reload()
					     //                    if(btn_post == "save_and_print"){
					     //                    	var print_obj = {};
					     //                    	print_obj['show_print'] = true;
					     //                    	print_obj['id_cash_receipt'] = response.id_cash_receipt;
					     //                    	window.localStorage.setItem("print_data",JSON.stringify(print_obj));
					     //                    }
					     //                    if(opcode == 0){ // if add
					     //                    	window.location = '/cash_receipt/view/'+response.id_cash_receipt;
					     //                    }else{
					     //                    	location.reload();
					     //                    }
					     //                });	
				}else if(response.RESPONSE_CODE == "INVALID_OR"){
					Swal.fire({
						title: response.message,
						text: '',
						icon: 'warning',
						showConfirmButton : false,
						timer  : 1500
					})
				}else if(response.RESPONSE_CODE == "CREDENTIAL_ERROR"){
					Swal.fire({
						title: response.message,
						text: '',
						icon: 'warning',
						showConfirmButton : false,
						timer : 1500
					});	
				}
			},error: function(xhr, status, error) {
				hide_loader()
				var errorMessage = xhr.status + ': ' + xhr.statusText
				Swal.fire({
					title: "Error-" + errorMessage,
					text: '',
					icon: 'warning',
					confirmButtonText: 'OK',
					confirmButtonColor: "#DD6B55"
				});
			}
		})
		console.log({$data});
	}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('adminLTE.admin_template', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/cash_receipt/cash_receipt_form.blade.php ENDPATH**/ ?>