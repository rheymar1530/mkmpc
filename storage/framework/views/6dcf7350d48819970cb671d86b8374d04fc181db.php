<?php $__env->startSection('content'); ?>
<style type="text/css">
	.form-row  label{
		margin-bottom: unset !important;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 15px;
		margin-top: 5px;
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
	.mandatory {
		border-color: rgba(232, 63, 82, 0.8)!important;
		box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(232, 63, 82, 0.6)!important;
		outline: 0 none;
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
		/*box-shadow: 0px 1px 13px 0px rgba(0,0,0,0.75);
		-webkit-box-shadow: 0px 1px 13px 0px rgba(0,0,0,0.75);
		-moz-box-shadow: 0px 1px 13px 0px rgba(0,0,0,0.75);*/
	}
	.class_amount{
		text-align: right;

	}
</style>
<script type="text/javascript">
	function resizeIframe(obj) {
		// alert(123);
		obj.style.height = 0;
		$height = (obj.contentWindow.document.documentElement.scrollHeight+50) + 'px';
		console.log({$height})
		obj.style.height = $height;
	}
</script>
<?php
$loan_app_type = [
	1=>"New Loan",
	2=>"Renewal"
];
?>
<div class="container main_form section_body" style="margin-top: -20px;">
	<?php $back_link = (request()->get('href') == '')?'/loan':request()->get('href'); ?>
	<a class="btn bg-gradient-secondary btn" href="<?php echo e($back_link); ?>" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Loan List</a>

		<div class="dropdown float-right">
			<button class="btn bg-gradient-danger2 dropdown-toggle" type="button"
			data-toggle="dropdown"><i class="fas fa-cog"></i>
			Options  
		</button>
		<div class="dropdown-menu">

		<?php if(($opcode == 1)): ?>
		<a class="dropdown-item" onclick="cancel_loan()"><i class="fas fa-times"></i>&nbsp;&nbsp;Cancel Loan Application</a>
	
		<?php if(MySession::isAdmin()): ?>
		<a class="dropdown-item" onclick='redirect_update_status("/loan/application/approval/<?php echo e($loan_token); ?>?href=<?php echo e(urlencode($back_link)); ?>")'><i class="fas fa-check"></i>&nbsp;&nbsp;Update Loan Status</a>
		<?php endif; ?>
		<?php endif; ?>	
			
			
			
		</div>
	</div>
	<div class="card">
		<div class="card-body col-md-12">
			<h3 class="head_lbl text-center mb-4">Loan Application Form <?php echo isset($loan_service->id_loan)?"(Loan ID# ".$loan_service->id_loan.")":"" ?>
			<?php if(isset($FirstLoan) && $FirstLoan): ?>
			<small>(First Loan Application)</small>
			<?php endif; ?>
		</h3>
		<?php if($opcode == 1 && $CHANGE_SERVICE): ?>
		<div class="col-sm-12 p-0" style="margin-top:10px">	
			<div class="alert bg-gradient-success" role="alert">
				Your loan application has been submitted. Please proceed to the Credit Committee and submit the following documents to process your loans:
				<ul>
					<?php $__currentLoopData = $requirements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<li><?php echo e($req->req_description); ?></li>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

				</ul>
			</div>				
		</div>
		<?php endif; ?>
		<div class="row">
			<div class="col-sm-12" style="margin-top:10px">	
				<form>		
					<input type="hidden" name="href" value="<?php echo e($back_link); ?>">
					<?php if($isAdmin): ?>
					<div class="form-row">
						<div class="form-group col-md-6">
							<label for="sel_loan_service">Select Member</label>

							<select class="form-control frm-inputs frm-parent-loan p-0" id="sel_id_member" key='id_member' name="id_member" required>
								<?php if(isset($selected_member)): ?>
								<option value="<?php echo e($selected_member->id_member); ?>"><?php echo e($selected_member->member_name); ?></option>
								<?php endif; ?>
							</select>
						</div>
					</div>
					<?php endif; ?>
					<div class="form-row d-flex align-items-end">
						<div class="form-group col-md-6">
							<label for="sel_loan_service">Select Loan Service</label>
							<select class="form-control frm-inputs frm-parent-loan p-0" id="sel_loan_service" key='id_loan_service' onchange="select_loan_service()" name="loan_reference" required <?php echo ($opcode==1)?"disabled":"";  ?>>
								<?php $__currentLoopData = $loan_service_lists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ls): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<?php
								$selected = "";
								if(isset($selected_service->id_loan_service)){
									if($ls->id_loan_service == $selected_service->id_loan_service){
										$selected = "selected";
									}
								}

								if($selected == "" && isset($red_reference)){
									if($ls->id_loan_service == $red_reference){
										$selected = "selected";

									}
								}
								?>
								<option value="<?php echo e($ls->id_loan_service); ?>" <?php echo $selected ?? '' ?> ><?php echo e($ls->name); ?></option>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</select>
						</div>
						<?php if($opcode==0): ?>
						<div class="form-group col-md-2">

							
							<button class="btn btn-sm bg-gradient-primary2">Check Loan Service</button>
						</div>
						<?php endif; ?>
					</div>
				</form>	
				<?php if($INVALID_APPLICATION_BLOCK): ?>
				<div class="form-row">
					<div class="col-md-12">
						<div class="alert bg-gradient-warning2" role="alert">
							<h4 class="alert-heading">Error Message</h4>
							<hr>
							<ul>
								<?php $__currentLoopData = $ERROR_MESSAGE_BLOCK; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<?php echo "<li>$err</li>";?>
								<!-- <li><?php echo e($err); ?></li> -->
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</ul>

						</div>
					</div>
				</div>					
				<?php endif; ?>


				<?php if($WITH_LOAN_SERVICE): ?>
				<!-- IF NO ERROR -->
				<?php if($INVALID_APPLICATION): ?>
				<div class="form-row">
					<div class="col-md-12">
						<div class="alert bg-gradient-warning2" role="alert">
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
				<!-- END IF NO ERROR -->

				<div class="form-row d-flex align-items-end">
					<div class="form-group col-md-4">
						<label for="txt_principal_amount">Principal Amount &nbsp;<span id="spn_amt_range">(<?php echo e($loanable_range); ?>)</span></label>
						<input type="text" class="form-control frm-inputs frm-parent-loan class_amount" key="principal_amount" id="txt_principal_amount" value="<?php echo e($default_amount); ?>">
						<small id="small-invalid-amount" class="form-text text-muted invalid-text"></small>
					</div>

				</div>
				<?php if($loan_service->id_loan_payment_type == 1): ?>
				<div class="form-row d-flex align-items-end">
					<div class="form-group col-md-4">
						<label for="sel_terms">Loan Terms &nbsp;</label>
						<select class="form-control frm-inputs p-0" id="sel_terms" key="term_token">
							<?php if(isset($terms)): ?>
							<?php
							$selected_terms = "";
							if(isset($TERMS_TOKEN)){
								$selected_terms = $TERMS_TOKEN;
							}
							?>
							<?php $__currentLoopData = $terms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<option value="<?php echo e($t->terms_token); ?>" <?php echo($t->terms_token==$selected_terms)?"selected":""; ?> ><?php echo e($t->terms_sel); ?></option>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							<?php endif; ?>
						</select>
					</div>
					<div class="form-group col-md-2">
						<button class="btn btn-sm bg-gradient-dark" onclick="calculate_loan()">Calculate Loan</button>
					</div>
				</div>
				<!-- IF ONE TIME PAYMENT -->
				<?php elseif($loan_service->id_loan_payment_type == 2): ?>
				<div class="form-row d-flex align-items-end">
					<div class="form-group col-md-3">
						<label for="sel_terms">Due Date &nbsp;</label>
						<select class="form-control frm-inputs p-0" id="sel_due_year">
							<?php for($i=0;$i<=3;$i++): ?>
							<option value="<?php echo e($LoanApplicationYear+$i); ?>" <?php echo ($SelectedDueYear == $LoanApplicationYear+$i)?'selected':''; ?> ><?php echo e($LoanApplicationYear+$i); ?></option>
							<?php endfor; ?>
						</select>
					</div>
					<div class="form-group col-md-2">

					<button class="btn btn-sm bg-gradient-dark" onclick="calculate_loan()">Calculate Loan</button>
					</div>
				</div>
				<?php endif; ?>

				<div class="col-md-12 p-0">
					<?php

					$show_table = ($opcode==1 && $CHANGE_SERVICE)?"":'none';
					$params = "";

					if($opcode==1 && $CHANGE_SERVICE){
						$params = "?id_loan_service=".$loan_service->id_loan_service."&term_reference=".$loan_service->terms_token."&principal_amount=".$loan_service->o_amount."&id_member=".$loan_service->id_member."&active_loan_payment=".urlencode(json_encode($loan_paid))."&other_deductions=".urlencode(json_encode($other_deductions))."&due_year=".$loan_service->year_due;


					}
						// 	
					?>
					<iframe id="iframe_loan_table" style="border:0;width: 100%;display: <?php echo $show_table;?>;" src="/loan/table_show<?php echo e($params); ?>" onload="resizeIframe(this)"></iframe>
					<div id="div_proceed_loan">
						<button class="btn btn-sm bg-gradient-success2 col-md-12" onclick="show_application_details(true)" id="btn_loan_proceed">Proceed to Loan Application</button>
					</div>
				</div>
				<?php if($WITH_LOAN_SERVICE): ?>

				<div style="display: none;" id="div_application_details">
					<div class="col-md-12" id="div_error_messages">
					</div>
					<?php echo $__env->make('loan.loan_application_details_form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
				</div>
				<?php endif; ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<div class="card-footer" >
		<div id="div_save_button">
			<button class="btn bg-gradient-success2 float-right" id="btn-submit-loan">Save</button>
		</div>
		

	</div>
</div>
</div>

<?php if(($opcode == 1)): ?>
<?php echo $__env->make('loan.approval_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?>

<?php if($WITH_LOAN_SERVICE): ?>
<?php echo $__env->make('loan.offset_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('loan.other_deduction', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>

<?php if($isAdmin): ?>
<script type="text/javascript">
	initialize_select_member();
	function initialize_select_member(){       
		$("#sel_id_member").select2({
			minimumInputLength: 2,
			width: '100%',
			createTag: function (params) {
				return null;
			},
			ajax: {
				tags: true,
				url: '/search_member/loan_application',
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
	$(document).on('change','#sel_id_member',function(){
		var val = $(this).val();
		console.log({val});	

		$.ajax({
			type        :       'GET',
			url         :       '/loan/ParseLoanServiceAvail',
			data        :       {'id_member' : val},
			success     :       function(response){
								console.log({response});
								$('#sel_loan_service').select2('destroy');
								var options = '';
								$.each(response.loan_services,function(i,item){
									options += `<option value="${item.id_loan_service}">${item.name}</option>`;
								});
								$('#sel_loan_service').html(options).select2();
			}

		})
	})

	function redirect_update_status(link){
		localStorage.setItem("trigger_update", 1);
		window.location = link;
	}
</script>
<?php endif; ?>

<script type="text/javascript">
	let CURRENT_MAKER_CBU = jQuery.parseJSON('<?php echo json_encode($maker_cbu ?? []); ?>');
	var $btn_loan_proceed = $('#btn_loan_proceed').detach();
	var $btn_submit_loan = $('#btn-submit-loan').detach();

	function animate_element(obj,is_show){
		
		if(is_show == 1){
			obj.hide();
			obj.show(300);
		}else{
			obj.hide(300, function(){ obj.remove(); });
		}
	}
	const $opcode = '<?php echo $opcode; ?>';
	$(document).ready(function(){
		initialize_loan_service_select();
	})
	$(document).on('select2:open', (e) => {
		const selectId = e.target.id
		$(".select2-search__field[aria-controls='select2-" + selectId + "-results']").each(function (
			key,
			value,
			) {
			value.focus()
		})
	})	
</script>
<script type="text/javascript">
	function initialize_loan_service_select(){
		$('#sel_loan_service').select2()
		// $('#sel_loan_service').select2({
		// 	minimumInputLength: 2,
		// 	width: '100%',
		// 	createTag: function (params) {
		// 		return null;
		// 	},
		// 	ajax: {
		// 		tags: true,
		// 		url: '/search_loan_service',
		// 		dataType: 'json',
		// 		type: "GET",
		// 		quietMillis: 1000,
		// 		data: function (params) {
		// 			var queryParameters = {
		// 				term: params.term
		// 			}
		// 			return queryParameters;
		// 		},
		// 		processResults: function (data) {
		// 			console.log({data});
		// 			return {
		// 				results: $.map(data.loan_services, function (item) {
		// 					return {
		// 						text: item.tag_value,
		// 						id: item.tag_id
		// 					}
		// 				})
		// 			};
		// 		}
		// 	}
		// });
	}

	function select_loan_service(){
		// var id_loan_service = $('#sel_loan_service').val();
		// $.ajax({
		// 	type         :         'GET',
		// 	url          :         '/loan/get_loan_service_details',
		// 	data         :         {'id_loan_service' : id_loan_service},
		// 	success      :         function(response){
		// 		set_selected_loan_details(response.loan_service,response.terms)

		// 		console.log({response});
		// 	}

		// });
	}

	function set_selected_loan_details(loan_details,terms){
		$('#txt_principal_amount').val(loan_details.default_amount)	
		$('#spn_amt_range').text('('+loan_details.amount_range+')');

		if(loan_details.id_loan_payment_type == 1){
			$terms_options = '';
			$.each(terms,function(i,item){
				$terms_options += '<option value="'+item.terms_token+'">'+item.terms_sel+'</option>';
			})
			$('#sel_terms').html($terms_options);
			$('#sel_terms').val('<?php echo $loan_service->terms_token ?? 0 ?>')

		}
	}
</script>
<?php if($WITH_LOAN_SERVICE): ?>
<script type="text/javascript">

	$min_amount = '<?php  echo (float)$min_amt; ?>';
	$max_amount = '<?php  echo (float)$max_amt; ?>';
	const LOAN_SERVICE_DETAILS =  jQuery.parseJSON('<?php echo json_encode($loan_service ?? []); ?>');


	var $list_of_requirements_html ='<ul style="text-align:left !important;margin-top:10px !important;">'+'<?php  
	foreach($requirements as $req){
		echo "<li>".$req->req_description."</li>";
	}
	?>'+'</ul>';




	const id_loan_service = '<?php echo $loan_service->id_loan_service?>';
	const id_member = '<?php echo $id_member ?>';

	var $terms_token_holder = '';

	var hide_proceed = 1;


	function set_terms_selected(terms_token){
		$terms_token_holder = terms_token;



	}		


	
	// var div_display = $('#div_loan_application_details_display').detach();



	<?php
	$terms_token = $loan_service->terms_token ?? 0;

	if(isset($TERMS_TOKEN)){
		$terms_token = $TERMS_TOKEN;
	}
	?>




	$('#sel_terms').val('<?php echo $terms_token;?>')
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
	$(document).on("keyup","#txt_principal_amount",function(){
		var val = parseFloat($(this).val());
		if(val >= $min_amount && val <= $max_amount){
			$(this).removeClass('mandatory');
			$invalid_text = "";
		}else{
			$invalid_text = "Invalid amount";
			$(this).addClass('mandatory');
		}
		$('#small-invalid-amount').text($invalid_text)
	})

	function calculate_loan(){
		var terms;
		if(parseInt('<?php echo $loan_service->id_loan_payment_type?>') == 2){
			terms = '<?php echo $loan_service->terms_token ?? "" ?>';
		}else{
			terms = $('#sel_terms').val();
		}	

		var params = {
			'id_loan_service' : id_loan_service,
			'term_reference'     : terms,
			'principal_amount' : decode_number_format($('#txt_principal_amount').val()),
			'id_member' : id_member,
			'due_year' : $('#sel_due_year').val()
		};

		var payments_act = [];
		$('tr.row_active_loan').each(function(){
			var paid_amt = decode_number_format($(this).find('.loan_paid_amt').val());
			var temp = {};

			if(paid_amt > 0){
				temp['loan_token'] = $(this).attr('data-token');
				temp['amount'] = paid_amt;
				// payments_act[] = paid_amt;
				payments_act.push(temp);
			}
		});

		// var ManualPayment =[];
		// ManualPayment=[
        //     {'id_loan_fees':15,'amount':1000,'remarks':'Test Interestxz'},
        //     {'id_loan_fees':16,'amount':200,'remarks':'Test Loan Paymentxz'}
		// ];
		
		// params['active_loan_payment'] =;

		// console.log({params});
		hide_proceed = 0;
		$('#iframe_loan_table').show();
		$link = '/loan/table_show?'+$.param(params)+'&active_loan_payment='+encodeURIComponent(JSON.stringify(payments_act))+'&other_deductions='+encodeURIComponent(JSON.stringify(DeductionData));
		$('#div_application_details').hide();
		$('#div_proceed_loan').html('')
		$('#div_save_button').html('');
		$('#iframe_loan_table').attr('src',$link);
		
		
	}
	function show_proceed_button(){
		// if($opcode == 0){
		$('#div_proceed_loan').html($btn_loan_proceed)

		// }
		
	}
	console.log({$min_amount,$max_amount})


	$(document).on('click','#btn-submit-loan',function(){
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
		var net_pay = parseNetPays();
		var other_lendings = parseOtherLendings();


		var payments_act = [];
		$('tr.row_active_loan').each(function(){
			var paid_amt = decode_number_format($(this).find('.loan_paid_amt').val());
			var temp = {};

			if(paid_amt > 0){
				temp['loan_token'] = $(this).attr('data-token');
				temp['amount'] = paid_amt;
				// payments_act[] = paid_amt;
				payments_act.push(temp);
			}
		});
		// var ManualPayment =[];
		// ManualPayment=[
        //     {'id_loan_fees':15,'amount':1000,'remarks':'Test Interestxz'},
        //     {'id_loan_fees':16,'amount':200,'remarks':'Test Loan Paymentxz'}
		// ];
		
		var application_data = {
			'opcode' : '<?php echo $opcode?>',
			'id_loan_service' :  id_loan_service,
			'id_member' : id_member,
			'terms_token' : $terms_token_holder,
			'principal_amount' : decode_number_format($('#txt_principal_amount').val()),
			'loan_remarks' : $('#txt_loan_remarks').val(),
			'net_pay' : net_pay,
			'other_lendings' : other_lendings,
			'comakers' : parseComakers(),
			'id_loan' : '<?php echo $loan_service->id_loan ?? 0 ?>',
			'has_other_loan' : ($('#chk_has_other_loan').prop('checked'))?1:0,
			'active_loan_payment' : payments_act,
			'manual_payment' : DeductionData,
			'year_due' : $('#sel_due_year').val()
		};

		console.log({application_data});


		$.ajax({
			type         :         'POST',
			url          :        '/loan/post',
			data         :         application_data,
			beforeSend   :         function(){
				$('#div_error_messages').html('');
				$('.mandatory').removeClass('mandatory');
				show_loader();
			},
			success      :         function(response){
				console.log({response});
				hide_loader();
				if(response.RESPONSE_CODE == "success"){
					var redirect_link ='/loan/application/view/'+response.LOAN_TOKEN+"?href="+encodeURIComponent('<?php echo $back_link;?>');
					var op = ('<?php echo $opcode?>' == 0)?"Submitted":"Updated";
					// Swal.fire({
					// 	title: "Loan Application Successfully Submitted",
					// 	text: '',
					// 	icon: 'success',
					// 	showConfirmButton : false,
					// 	timer  : 2500
					// }).then((result) => {
					// 	window.location = 
					// });	


					Swal.fire({
						
						title: "Loan Application Successfully "+op,
						html : "<a href='"+redirect_link+"'>Loan ID#"+response.LOAN_ID+"</a><br>Please proceed to the Credit Committee and submit the following documents to process your loans:"+$list_of_requirements_html,
						text: '',
						icon: 'success',
						showCancelButton : true,
						confirmButtonText: 'Create Another Loan',
						cancelButtonText: 'Back to List of Loan',
						showConfirmButton : true,     
						allowEscapeKey : false,
						allowOutsideClick: false
					}).then((result) => {
						if(result.isConfirmed) {
							window.location = "/loan/application/create?href="+encodeURIComponent('<?php echo $back_link;?>');
						}else{
							window.location = '<?php echo $back_link;?>';
						}
					});					
				}else if(response.RESPONSE_CODE == "INVALID_PARAMETERS"){
					toastr.error('LOAN SUBMISSION ERROR')
					//NET PAY
					$invalid_net = response.NET_PAY_INVALID;
					$.each($invalid_net,function(i,item){
						for(var $j =0; $j<item.length;$j++){
							$('tr.row_net:eq('+i+') [key="'+item[$j]+'"]').addClass('mandatory');
						}
						
					})
					//OTHER LENDER
					$invalid_other_lender = response.OTHER_LENDER_INVALID;
					$.each($invalid_other_lender,function(i,item){
						for(var $j =0; $j<item.length;$j++){
							$('tr.row_other_lending:eq('+i+') [key="'+item[$j]+'"]').addClass('mandatory');
						}
					})

					show_submit_error_message(response.ERROR_MESSAGES)
				}else if(response.RESPONSE_CODE == "CREDENTIAL_ERROR"){
					Swal.fire({
						title: response.message,
						text: '',
						icon: 'warning',
						showConfirmButton : false,
						timer : 2500
					});	
				}else if(response.RESPONSE_CODE == "INVALID_AMOUNT"){
					Swal.fire({
						title: response.message,
						text: '',
						icon: 'warning',
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
	}

	function show_application_details(bool){
		if(bool){
			var is_loan_proceed_valid = $('#iframe_loan_table')[0].contentWindow.get_loan_proceed();
			if(!is_loan_proceed_valid){
				Swal.fire({
					title: 'Total Loan Proceed must not be less than or equal to 0',
					text: '',
					icon: 'warning',
					showConfirmButton : false,
					timer : 2500
				});	
				return;
			}			
		}
		// alert(123);
		animate_element($('#div_application_details'),1);
		// $('#div_application_details').show();
		$('#div_proceed_loan').html('')
		$('#div_save_button').html($btn_submit_loan);
	}
	function show_submit_error_message(err){
		var out =		`
		<div class="alert bg-gradient-warning alert-dismissible fade show" role="alert">
		<h5>Loan Application Error Message</h5><hr>
		<ul>`;
		for(var $i=0;$i<err.length;$i++){
			out += `<li>`+err[$i]+`</li>`;
		}

		out+=					`</ul>
		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
		<span aria-hidden="true">&times;</span>
		</button>
		</div>
		`;
		console.log({out})
		$('#div_error_messages').html(out);
	}
</script>

<?php endif; ?>

<?php if($opcode == 1 && $CHANGE_SERVICE): ?>
<script type="text/javascript">
	const other_lendings = jQuery.parseJSON('<?php echo json_encode($other_lendings ?? []) ?>');
	set_other_lenders()
	set_loan_application_details()
	$('#div_loan_application_details_display').show();
	$(document).ready(function(){
		$('#div_proceed_loan').html("");
		show_application_details();
	})

	function set_other_lenders(){
		var checked = $('#chk_has_other_loan').prop('checked');
		$('.frm-other-lending').attr('disabled',!checked);
		if(checked){
			$('#other-lending-body').html('');
			$.each(other_lendings,function(i,item){
				append_lender();
				var last_row = $('div.row_other_lending').last();
				last_row.find('input.frm-other-lending').each(function(){
					var key = $(this).attr('key')
					$(this).val(item[key])
				})
				console.log({item})
			})
		}
	}

</script>
<?php endif; ?>

<?php if(($opcode == 1)): ?>
<!-- LOAN CANCELLATION -->
<script type="text/javascript">
	function cancel_loan(){
		$('#approval_modal').modal('show');
		$('#div_reason_cancel').html(div_reason);
	}
	$('#frm_loan_approval').submit(function(e){
		e.preventDefault();
		Swal.fire({
			title: 'Do you cancel this loan?',
			icon: 'warning',
			showDenyButton: false,
			showCancelButton: true,
			confirmButtonText: `Save`,
		}).then((result) => {
			if (result.isConfirmed) {
				// alert("POSTED");
				post_cancellation();
			} 
		})	
	})
	function post_cancellation(){
		$.ajax({
			type     :     'POST',
			url      :      '/loan/application/cancel',
			data      :     {'id_loan' : '<?php echo $loan_service->id_loan ?? 0 ?>',
			'cancellation_reason'   : $('#txt_cancel_reason').val()},
			beforeSend :    function(){
				show_loader();
			},
			success   :     function(response){
				console.log({response});
				hide_loader();
				if(response.RESPONSE_CODE == "SUCCESS"){
					Swal.fire({
						title: "Loan Application Successfully Cancelled",
						text: '',
						icon: 'success',
						showConfirmButton : false,
						timer  : 2500
					}).then((result) => {
						window.location = '/loan/application/view/'+response.LOAN_TOKEN+"?href="+'<?php echo $back_link;?>';
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
	}
</script>
<?php endif; ?>
<?php $__env->stopPush(); ?>




<!-- 		Swal.fire({
			title: 'Do you want to save this?',
            html : "<h5>Loan Application Requirements:</h5>- ID<br>- KK",
			icon: 'warning',
			showDenyButton: false,
			showCancelButton: true,
			confirmButtonText: `Save`,
		}).then((result) => {
			if (result.isConfirmed) {
				// alert("POSTED");
				post();
			} 
		}) -->

<?php echo $__env->make('adminLTE.admin_template', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/loan/loan_form.blade.php ENDPATH**/ ?>