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
	.tbl_loans tr>th ,.tbl_fees tr>th,.tbl-inputs tr>th{
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
	.tbl_loans tr>td,.tbl_fees tr>td,.tbl_repayment_display tr>td{
		padding: 3px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 15px;
	}
	.tbl-inputs tr>td{
		padding: 0px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 15px;
	}
	.frm_loans,.frm-requirements{
		height: 27px !important;
		width: 100%;    
		font-size: 13px;
	}

	.class_amount,.txt_loan_payment_amount{
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
		max-width:98% !important;
		min-width:98% !important;

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
	.wrapper2{
	/*	width: 1300px !important;
		margin: 0 auto;*/
	}
	.text-red{
		color: red;
	}
	.swal2-deny{
		padding: 0.375rem 0.75rem !important;
	}
	.foot_loan{
		text-align:center;background: #808080;color: white;
	}
	.col_payment_type{
		text-align: left !important;
	}
	.badge_loan_type{
		font-size: 14px;
	}
</style>
<?php
$transaction_type = [
	
	1=>"Cash",
	4=>"Check",
	2=>"ATM Swipe",
	3=>"Payroll"
];

$js_allow_post = ($allow_post)?1:0;
?>
<div class="wrapper2">
	<div class="container-fluid main_form section_body" style="margin-top: -20px;" >
		<?php $back_link = (request()->get('href') == '')?'/repayment':request()->get('href'); ?>
		<a class="btn bg-gradient-secondary btn-sm" href="<?php echo e($back_link); ?>" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Loan Payment Transaction List</a>
		<?php if(($opcode == 1 && $allow_post)): ?>
		<a class="btn bg-gradient-danger btn-sm float-right" style="margin-bottom:10px" onclick="cancel_repayment()"><i class="fas fa-times"></i>&nbsp;&nbsp;Cancel Loan Payment</a>
		<?php endif; ?>
		<div class="card" id="repayment_main_div">
			<div class="card-body col-md-12">
				<h3>Loan Payment <?php if($opcode == 1): ?>(Loan Payment ID# <?php echo e($repayment_transaction->id_repayment_transaction); ?>)<?php endif; ?></h3>
				<div class="row">
					<div class="col-md-12 row">	
						<div class="col-md-12">
							<div class="form-row">
								<div class="form-group col-md-3" style="margin-top:20px">
									<label for="txt_transaction_date">Transaction Date</label>
									<input type="date" name="" class="form-control" value="<?php echo e($repayment_transaction->transaction_date ?? $current_date); ?>" id="txt_transaction_date" min="<?php echo e($min_date); ?>" onkeydown="return false">
								</div>
								<div class="form-group col-md-3" style="margin-top:20px">
									<label for="sel_transaction_type">Payment Mode</label>
									<select class="form-control p-0" id="sel_transaction_type">
										<?php $__currentLoopData = $transaction_type; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val=>$text): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<?php
										$selected = ($opcode == 1 && $repayment_transaction->transaction_type == $val)?"selected":"";
										?>
										<option value="<?php echo e($val); ?>" <?php echo e($selected); ?>><?php echo e($text); ?></option>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									</select>
								</div>
							</div>
							<div class="form-row">
								<div class="form-group col-md-5">
									<label for="txt_description">Select Borrower</label>
									<select class="form-control select2" id="sel_borrower">
										<?php if($opcode == 1): ?>
										<option value="<?php echo e($repayment_transaction->id_member); ?>"><?php echo e($repayment_transaction->selected_member); ?></option>
										<?php endif; ?>
									</select>
								</div>
								<?php if($opcode == 0): ?>
								<!-- <div class="form-group col-md-1">
									<label for="txt_description">&nbsp;</label>
									<button class="btn btn-sm bg-gradient-success form-control" onclick="reload_member_dues()">Reload</button>
								</div> -->
								<?php endif; ?>
							</div>
							<div class="form-row">
								<div class="form-group col-md-5">
									<label for="txt_description">Due Date</label>
									<select class="form-control p-0" id="txt_date">
										<?php if($opcode == 1): ?>
										<option value="<?php echo e($repayment_transaction->date); ?>"><?php echo e($repayment_transaction->due_date_text); ?></option>
										<?php endif; ?>
									</select>
								</div>
								<div class="form-group col-md-2">
									<label>&nbsp;</label>
									<span id="span_btn_half">
										<button class="btn btn-sm bg-gradient-info form-control" id="btn_comp_half" onclick="compute_half()">Half Payment</button>
									</span>
								</div>
							</div>

							<div class="form-row" id="div_swiping_amount_holder">
								<div id="">
									<div class="form-group col-md-4 div_swiping_amount">
										<label for="txt_description">Bank</label>
										<select class="form-control" id="sel_bank" required>
											<?php
											if($opcode == 0){
												$selected_bank = 2;
											}else{
												$selected_bank = ($repayment_transaction->id_bank > 0)?$repayment_transaction->id_bank:2;
											}

											?>
											<?php $__currentLoopData = $banks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bank): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
											<option value="<?php echo e($bank->id_bank); ?>" <?php echo ($selected_bank==$bank->id_bank)?'selected':''; ?> ><?php echo e($bank->bank_name); ?></option>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
										</select>
									</div>
									<div class="form-group col-md-4 div_swiping_amount">
										<label for="txt_description">Swiping Amount</label>
										<input type="text" name="" required class="form-control class_amount" id="swiping_amount" value="<?php echo e(number_format(($repayment_transaction->swiping_amount ?? 0),2)); ?>">
									</div>
									<div class="form-group col-md-3 div_or">
										<label for="txt_description">OR No.</label>
										<input type="text" name="" required class="form-control" id="txt_or_rep" value="<?php echo e($repayment_transaction->or_no ?? ''); ?>">
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-12" style="margin-top:20px">
							<div class="form-group col-md-12 p-0" style="margin-bottom:unset;">
								<?php echo $__env->make('repayment.active_loans', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
								<?php echo $__env->make('repayment.payment_table', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
							</div>
						</div>
						<div class="col-md-12" style="margin-top:10px">
							<div class="row">
								<div class="col-md-8">
									<div class="col-md-12 p-0">
										<h5>Other Charges&nbsp;&nbsp;<?php if($allow_post): ?><button class="btn btn-sm bg-gradient-primary" onclick="append_penalty()"><i class="fa fa-plus"></i>&nbsp;Add</button><?php endif; ?></h5>
										<div class="table-responsive" style="max-height: calc(100vh - 200px);overflow-y: auto;margin-top: 5px !important;overflow-x: auto">
											<table class="table table-bordered table-stripped tbl-inputs" style="white-space: nowrap;" id="tbl_penalty">
												<thead>
													<tr>
														<th class="table_header_dblue" width="8%"></th>
														<th class="table_header_dblue" width="50%">Penalty Type</th>
														<th class="table_header_dblue">Amount</th>
														<th class="table_header_dblue" width="5%"></th>
													</tr>
												</thead>
												<tbody id="repayment_penalty_body">
													<tr class="row_penalty">
														<td class="col-count text_center">1</td>
														<td><select class="form-control p-0 sel_penalty frm_loans sel_penalty" key="id_payment_type">
															<?php $__currentLoopData = $penalties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<option value="<?php echo e($p->id_payment_type); ?>"><?php echo e($p->description); ?></option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
														</select></td>
														<td><input type="text" name="" required class="form-control class_amount frm_loans txt_penalty_amount" value="0.00" key="amount"></td>
														<td class="text_center"><a onclick="remove_penalty(this)" style="margin-top: 10px !important;" class="frm-other-lending"><i class="fa fa-times"></i></a></td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
									<div class="col-md-8 p-0">
										<h5>Other Fees</h5>
										<div class="table-responsive" style="max-height: calc(100vh - 200px);overflow-y: auto;margin-top: 5px !important;overflow-x: auto">
											<table class="table table-bordered table-stripped table-head-fixed tbl-inputs" style="white-space: nowrap;">
												<thead>
													<tr>
														<th class="table_header_dblue" width="8%"></th>
														<th class="table_header_dblue">Fee</th>
														<th class="table_header_dblue">Amount</th>
													</tr>
												</thead>
												<tbody id="loan_fee_display">
													<?php $__currentLoopData = $repayment_fee; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $count=>$rf): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<tr class="row_fees" data-id="<?php echo e($rf->id_payment_type); ?>">
														<td class="text_center"><?php echo e($count+1); ?></td>
														<td style="padding-left: 10px"><?php echo e($rf->description); ?></td>
														<td><input type="text" name="" required class="form-control class_amount frm_loans txt_fee_amount" value="<?php echo e(number_format($rf->amount,2)); ?>" key="amount"></td>
													</tr>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												</tbody>
											</table>
										</div>
									</div>
								</div>
								<div class="col-md-4">
									<div class="card">
										<div class="card-body">
											<h5 id="or_text_holder">
												<?php if(isset($repayment_transaction->or_no)): ?>
												<span >OR No. <?php echo e($repayment_transaction->or_no); ?></span>
												<a style="margin-left:20px;font-size: 14px;" href="#" onclick="update_or()"><i class="nav-icon fas fa-cog"></i></a>
												<?php endif; ?>
											</h5>
											<h5><u>Loan Payment Summary</u></h5>
											<table style="width:100%">
												<tr>
													<td class="spn_t">Loan Amount Due:</td>
													<td class="class_amount" width="30%" id="spn_total_loan_amount_due">0.00</td>
													<td></td>
												</tr>
												<tr>
													<td class="spn_t">Others Fees:</td>
													<td class="class_amount" width="30%" id="spn_total_loan_fees">0.00</td>
													<td></td>
												</tr>
												<tr>
													<td class="spn_t">Loan Penalty:</td>
													<td class="class_amount" width="30%" id="spn_total_loan_penalty">0.00</td>
													<td></td>
												</tr>
												<tr>
													<td class="spn_t">Total Amount Due:</td>
													<td class="class_amount" width="30%" id="spn_total_amount_due">0.00</td>
													<td></td>
												</tr>
												<tr class="row_swiping_amount">
													<td class="spn_t">Swiping Amount:</td>
													<td class="class_amount" width="30%" id="spn_swiping_amount" style="font-weight:bold">0.00</td>
													<td></td>
												</tr>
												<tr>
													<td class="spn_t">Payments:</td>
													<td class="class_amount" width="30%"></td>
													<td></td>
												</tr>
												<tr>
													<td class="spn_t"><span style="font-weight: normal !important;padding-left: 15px;">Loan Dues</span></td>
													<td class="class_amount" width="30%" id="spn_loan_dues"></td>
													<td></td>
												</tr>
												<tr>
													<td class="spn_t"><span style="font-weight: normal !important;padding-left: 15px;">Rebates</span></td>
													<td class="class_amount" width="30%" id="spn_rebates"></td>
													<td></td>
												</tr>
												<tr>
													<td class="spn_t"><span style="font-weight: normal !important;padding-left: 15px;">Loan Penalty/Surcharges</span></td>
													<td class="class_amount" width="30%" id="spn_surcharges"></td>
													<td></td>
												</tr>
												<tr>
													<td class="spn_t"><span style="font-weight: normal !important;padding-left: 15px;">Other Fees and Charges</span></td>
													<td class="class_amount" width="30%" id="spn_oth_fee_charg"></td>
													<td></td>
												</tr>
												<tr style="border-top: 1px solid;">
													<td class="spn_t" ></td>
													<td class="class_amount" width="30%" id="spn_total_paid_amount" style="font-weight:bold">0.00</td>
													<td></td>
												</tr>
												<tr class="row_swiping_amount">
													<td class="spn_t">Change:</td>
													<td class="class_amount" width="30%" id="spn_change">0.00</td>
													<td></td>
												</tr>
											</table>

										</div>
									</div>
									<?php if(isset($repayment_change) && count($repayment_change) > 0): ?>
									<div class="card">
										<div class="card-body">
											<h5><u>Change Released</u></h5>
											<table style="width:100%">

												<tr>
													<th>Ref#</th>
													<th>Date</th>
													<th>Amount</th>
												</tr>
												<?php $total_change = 0; ?>
												<?php $__currentLoopData = $repayment_change; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<tr>
													<td><?php echo e($rc->id_repayment_change); ?></td>
													<td><?php echo e($rc->date); ?></td>
													<td class="class_amount"><?php echo e(number_format($rc->amount,2)); ?></td>
												</tr>
												<?php $total_change += $rc->amount;?>
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												<tr style="border-top:1px solid;">
													<th colspan="2">Total</th>
													<th class="class_amount"><?php echo e(number_format($total_change,2)); ?></th>
												</tr>
												<tr>
													<th colspan="2">Remaining Change</th>
													<th class="class_amount"><?php echo e(number_format(($repayment_transaction->change-$total_change),2)); ?></th>
												</tr>
											</table>
										</div>
									</div>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>	
					<div class="col-md-12" style="margin-top:10px">

						<fieldset class="border" style="padding-left: 10px;padding-right: 10px;padding-bottom: 10px;">
							<legend class="w-auto p-1">Loan Payment Remarks</legend>
							<textarea class="form-control" rows="3" style="resize:none;" id="txt_remarks"><?php echo e($repayment_transaction->remarks ?? ''); ?></textarea>
						</fieldset>					
					</div>
				</div>
			</div>
			
			<div class="card-footer">
				<?php if($allow_post): ?>
				<!-- && $repayment_transaction->or_no == null -->
				<?php if($opcode == 0 || ($opcode ==1 )): ?>
				<button class="btn bg-gradient-success float-right" onclick="save_repayment()" disabled id="btn_save_repayment">Save & Print Loan Payment</button>
				<?php endif; ?>
				<?php endif; ?>
				<?php if($opcode == 1): ?>
				<?php if($repayment_transaction->transaction_type ==2 || $repayment_transaction->transaction_type ==3): ?>
				<button type="button" class="btn bg-gradient-danger float-right" style="margin-right:10px" onclick="print_page('/journal_voucher/print/<?php echo e($repayment_transaction->id_journal_voucher); ?>')" id="btn_entry"><i class="fas fa-print" ></i>&nbsp;Print Journal Voucher (JV #<?php echo e($repayment_transaction->id_journal_voucher); ?>)</button>
				<?php else: ?>
				<?php if($repayment_transaction->id_cash_receipt_voucher > 0): ?>
				<button type="button" class="btn bg-gradient-danger float-right" style="margin-right:10px" onclick="print_page('/cash_receipt_voucher/print/<?php echo e($repayment_transaction->id_cash_receipt_voucher); ?>')" id="btn_entry"><i class="fas fa-print" ></i>&nbsp;Print CRV (CRV #<?php echo e($repayment_transaction->id_cash_receipt_voucher); ?>)</button>
				<?php endif; ?>
				<?php endif; ?>
				<button class="btn bg-gradient-primary float-right" onclick="print_or()" id="btn_print_or" style="margin-right: 10px;">Print OR</button>
				<?php endif; ?>
			</div>
			
		</div>
	</div>
</div>



<?php if($opcode == 1): ?>
<?php echo $__env->make('repayment.repayment_or', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('global.print_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php if($credential->is_cancel): ?>
<?php echo $__env->make('repayment.cancel_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?>
<?php endif; ?>




<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>


<script type="text/javascript">

	function gg(){

	}
	$penalty_row = '<tr class="row_penalty">'+$('tr.row_penalty').html()+'</tr>';
	$no_penalty_row = '<tr class="row_no_penalty"><td colspan="4" style="text-align:center">No Loan Payment Penalty</td></tr>'
	$div_swiping_amount = $('.div_swiping_amount').detach();
	$div_or = $('.div_or').detach();
	$btn_comp_half = $('#btn_comp_half').detach();
	$('#repayment_penalty_body').html($no_penalty_row)
	const opcode = '<?php echo $opcode;?>';
	let total_loan_due = 0;

	initialize_transaction_type()
	$(document).on('select2:open', (e) => {
		const selectId = e.target.id
		$(".select2-search__field[aria-controls='select2-" + selectId + "-results']").each(function (
			key,
			value,
			) {
			value.focus()
		})
	}) 
	intialize_select2();
	function intialize_select2(){       
		$("#sel_borrower").select2({
			minimumInputLength: 2,
			width: '100%',
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
	function append_penalty(){
		$('#repayment_penalty_body').append($penalty_row)
		animate_element($('.row_penalty').last(),1);
		// animate_element($('tr.row_no_penalty'),2);
		$('tr.row_no_penalty').remove();
		set_penalty_count()
	}

	function set_penalty_count(){
		var count = 0;
		$('tr.row_penalty').each(function(i){
			$(this).find('.col-count').text(i+1);
			count = i+1;
		})
		if(count == 0){
			$('#repayment_penalty_body').html($no_penalty_row)
		}
	}
	function remove_penalty(obj){
		var parent_row = $(obj).closest('tr.row_penalty');
		// animate_element(parent_row,2);
		parent_row.remove();
		set_penalty_count();
		computeAll();
	}
	function parseTotalPenalty(){
		var total_penalty = 0;
		$('tr.row_penalty').each(function(i){
			total_penalty += decode_number_format($(this).find('.txt_penalty_amount').val());
		})
		$('#spn_total_loan_penalty').text(number_format(total_penalty,2));

		return total_penalty;
	}
	function parseTotalFees(){
		var total_fees = 0;
		$('tr.row_fees').each(function(i){
			total_fees += decode_number_format($(this).find('.txt_fee_amount').val());
		})
		$('#spn_total_loan_fees').text(number_format(total_fees,2));
		return total_fees;
	}
	function parseTotalLoanPayment(){
		var total_loan_due_payment = 0;
		var total_principal=0,total_interest=0,total_fees=0,total_surcharges =0,total_amt_paid=0;

		var out = [];
		out['total_loan_due_payment'] = 0;
		out['total_loan_due'] = 0;
		out['total_penalty'] =0;
		out['total_rebates'] = 0;
		// $('tr.row_loan_dues').each(function(i){
		// 	total_loan_due_payment += decode_number_format($(this).find('.txt_loan_payment_amount').val());
		// })
		$('tr.row_loan_dues').each(function(i){
			$(this).find('.txt_loan_payment_amount').each(function(){
				if($(this).is(":visible")){
					var v = $(this).is(":visible");
					var class_ = $(this).attr('class');
					// console.log({v,class_})
					var val = $(this).val();
					val = (val=="")?0:decode_number_format(val);
					if($(this).hasClass('txt_prin')){
						total_principal+=val;
						out['total_loan_due'] +=val;
					}else  if($(this).hasClass('txt_in')){
						total_interest+=val;
						out['total_loan_due'] +=val;
					}else if($(this).hasClass('txt_fees')){
						total_fees += val;
						out['total_loan_due'] +=val;
					}else if($(this).hasClass('txt_sur')){
						total_surcharges += val;
						out['total_penalty'] += val;
					}else{
						total_amt_paid += val;
						out['total_loan_due'] +=val;
					}
					console.log({val,total_principal,total_fees,total_surcharges,total_amt_paid})

					total_loan_due_payment += val;
				}
				// swiping_amount = (!$.isNumeric(swiping_amount))?decode_number_format(swiping_amount):swiping_amount;
			});

			$(this).find('.txt_rebates').each(function(){
				var val = $(this).attr('rebates-value');
				val = (val=="")?0:decode_number_format(val);	
				out['total_rebates'] += val;	
			})

			// total_loan_due_payment += decode_number_format($(this).find('.txt_loan_payment_amount').val());
		});



		out['total_loan_due_payment'] = total_loan_due_payment;
		$('#txt_pay_principal').text(number_format(total_principal))
		$('#txt_pay_interest').text(number_format(total_interest))
		$('#txt_pay_fees').text(number_format(total_fees))
		$('#txt_pay_total').text(number_format(total_loan_due_payment+out['total_rebates'],2));
		$('#txt_pay_sur').text(number_format(total_surcharges,2));
		$('#txt_pay_amt_paid').text(number_format(total_amt_paid,2));
		$('#txt_rebates_total').text('('+number_format(out['total_rebates']*-1)+')');
		return out;
	}
	function computeAll(is_fill_dues){
		
		var total_fees = parseTotalFees();
		var total_penalty = parseTotalPenalty();


		var transaction_type = $('#sel_transaction_type').val();

		var total_amount_due = total_fees+total_penalty+total_loan_due;
		$('#spn_total_amount_due').text(number_format(total_amount_due,2));

		var swiping_amount = $('#swiping_amount').val();
		swiping_amount = (swiping_amount == undefined)?0:decode_number_format(swiping_amount);

		console.log({swiping_amount})
		if(swiping_amount >= total_amount_due && is_fill_dues && transaction_type == 2){
			FillDues();
			console.log("_______________________________________________")
		}
		var total_paid_loan_due = parseTotalLoanPayment();
		var total_paid_amount  = total_paid_loan_due['total_loan_due_payment']+total_penalty+total_fees+total_paid_loan_due['total_rebates'];
		
		if(transaction_type == 2){
			swiping_amount = (!$.isNumeric(swiping_amount))?decode_number_format(swiping_amount):swiping_amount;
		}else{
			swiping_amount = total_paid_amount;
		}

		
		$('#spn_swiping_amount').text(number_format(parseFloat(swiping_amount),2));
		
		var change = swiping_amount - total_paid_amount;
		var swiping_amount_valid = true;

		if(change < 0){
			if(transaction_type == 2){
				$('#spn_total_paid_amount').addClass('text-red');
			}else{
				$('#spn_total_paid_amount').removeClass('text-red');
			}
			swiping_amount_valid = false;
		}else{
			$('#spn_total_paid_amount').removeClass('text-red');
		}

		$('#spn_loan_dues').text(number_format(total_paid_loan_due['total_loan_due'],2))
		$('#spn_surcharges').text(number_format(total_paid_loan_due['total_penalty'],2))
		$('#spn_oth_fee_charg').text(number_format((total_fees+total_penalty),2))
		$('#spn_rebates').text("("+number_format(total_paid_loan_due['total_rebates']*-1,2)+")")

		console.log({total_paid_amount})

		$('#spn_total_paid_amount').text(number_format(parseFloat(total_paid_amount),2));
		$('#spn_change').text(number_format(parseFloat(change),2));

		return swiping_amount_valid;

		// $('#spn_total_loan_amount_due').
	}
	$(document).on('change','#sel_borrower',function(){
		var val = $(this).val();

		if(val != null){
			// parseMemberLoanDues(val);
			parseDueDates(val);
		}
	});
	$(document).on('change','#txt_date',function(){
		var val = $(this).val();
		if(val != null){
			parseMemberLoanDues(val);

			// parseDueDates(val);
		}
	})
	function reload_member_dues(){
		var id_member = $('#sel_borrower').val();
		if(id_member == null){
			Swal.fire({
				title: "Please Select Borrower",
				text: '',
				icon: 'warning',
				showCancelButton : false,
				showConfirmButton : false,
				timer : 1500
			});
			return;
		}
		parseMemberLoanDues(id_member);
	}

	let repayment_date ='<?php echo $repayment_transaction->date ?? null;?>';
	let id_member_holder = '<?php echo $repayment_transaction->id_member ?? 0;?>';



	function display_loan_dues(active_loans,loan_dues,is_load){

		console.log({active_loans});

		draw_active_loan(active_loans,is_load);

		$('#spn_total_loan_amount_due').text(number_format(parseFloat(total_loan_due),2));
		var autofill = (is_load==1)?false:true;
		computeAll(autofill);

		return;
	}

	function compute_half(){
		$('tr.row_loan_dues').each(function(){
			$(this).find('.txt_loan_payment_amount').each(function(){
				if($(this).attr("payment-type") == "current" && !$(this).prop('disabled')){
					$(this).val(number_format(parseFloat($(this).attr('due'))/2,2));
					compute_payment_row($(this));
				}
			})
		})
		parseTotalLoanPayment();
		computeAll(false);
	}
	// ,.txt_loan_payment_amount
	$(document).on('keyup','.txt_fee_amount,.txt_penalty_amount',function(){
		var val = $(this).val();
		if(!$.isNumeric(val)){
			$(this).val(0);
		}
		computeAll();
	});

	$(document).on('keyup','#swiping_amount',function(){
		var val = $(this).val();
		if(!$.isNumeric(val)){
			$(this).val(0);
		}
		computeAll(true);
	});   

	$(document).on("focus",".txt_loan_payment_amount",function(){
		var val = $(this).val();
		if(val == '' || val == 'NaN'){
			val = '';
			$(this).val(val);
			return; 
		}
		$(this).val(decode_number_format(val)); 
	})
	$(document).on("blur",".class_amount",function(){
		var val = $(this).val();

		val = decode_number_format(val);
		if(isNaN(val)){
			$(this).val("");

			return ;
		}
		$(this).val(number_format(parseFloat(val)));
	}) 
	$(document).on('keyup','.txt_loan_payment_amount',function(){
		var val = $(this).val();
		// val = decode_number_format(val);
		// console.log({})
		if(!$.isNumeric(val)){
			$(this).val('');
		}
		compute_payment_row($(this));    	
		computeAll();
		
	})

	function compute_payment_row(obj,load){
		var parent_row = $(obj).closest('tr.row_loan_dues');
		var data_token = $(parent_row).attr('data-token');

		var total = 0;

		var total_loan_only = 0;
		$(parent_row).find('.txt_loan_payment_amount').each(function(){
			if($(this).is(':visible')){
				var val = parseFloat(decode_number_format($(this).val()));
				val = isNaN(val)?0:val;
				total += val;		
				if(!$(this).hasClass('txt_sur')){
					total_loan_only += val;
				}		
			}
		})
		var rebates_val =0;
		if(loan_balances[data_token] == total_loan_only){
			rebates_val = $(parent_row).find('.txt_rebates').attr('rebates-def')*-1
		}

		if(!load || load == undefined){
			$(parent_row).find('.txt_rebates').attr('rebates-value',rebates_val)
			$(parent_row).find('.txt_rebates').val('('+number_format(rebates_val*-1,2)+')')			
		}



		var rebates = $(parent_row).find('.txt_rebates').attr('rebates-value');
		rebates = (rebates=="")?0:decode_number_format(rebates);	


		total += rebates;



		parent_row.find('.total_loan_paid').val(number_format(total,2));
		console.log({total,total_loan_only})
	}
	$(document).on('dblclick','.total_loan_paid',function(){
		var parent_row = $(this).closest('tr.row_loan_dues');
		$(parent_row).find('.txt_loan_payment_amount').each(function(){
			$(this).trigger('dblclick');
		})
	})
	$(document).on("blur",".txt_loan_payment_amount",function(){
		var val = decode_number_format($(this).val());
		if(!$.isNumeric(val)){
			$(this).val('');
			return;
		}
		$(this).val(number_format(parseFloat(val)));
	}) 
	function FillDues(){
		$('tr.row_loan_dues').each(function(){
			$(this).find('.txt_loan_payment_amount').each(function(){
				if(!$(this).hasClass('txt_sur')){
					$(this).trigger('dblclick',true);
				}
				
			})
		})
		parseTotalLoanPayment();
	}
	function fill_zero(){
		$('.txt_loan_payment_amount').val("0.00");
		computeAll();
	}

	// $(document).on('dblclick','tr.row_loan_dues',function(){
	// 	var parent_row = $(this);
	// 	parent_row.find('.txt_loan_payment_amount').val(number_format(parseFloat(parent_row.find('.txt_loan_payment_amount').attr('due')),2));
	// 	computeAll();
	// })

	$(document).on('dblclick','input.txt_loan_payment_amount',function(fill_total_dues){
		if(!$(this).attr('disabled') && !$(this).hasClass('txt_sur')){
			var val = parseFloat($(this).attr('due'));

			if(fill_total_dues){
				val = parseFloat($(this).attr('total-due'));
			}
			$(this).val(number_format(val,2));
			compute_payment_row($(this));
			computeAll();
		}
	})

	$(document).on('change','#sel_transaction_type',function(){
		var val = $(this).val();
		initialize_transaction_type()
		computeAll();
		console.log({val})
	})

	function initialize_transaction_type(){
		var transaction_type = $('#sel_transaction_type').val();
		var swiping_fee = 0;
		var disable_swiping = false;


		if(transaction_type == 2){
			swiping_fee = 20;
			$('#div_swiping_amount_holder').html($div_swiping_amount);
			animate_element($('#div_swiping_amount_holder'),1);
			// $('.row_swiping_amount').show();
			animate_element($('.row_swiping_amount'),1);
			computeAll();			
		}else{
			if(transaction_type == 1){
				$('#div_swiping_amount_holder').html($div_or);
			}else{
				$('#div_swiping_amount_holder').html('');
			}
			$('.row_swiping_amount').hide();
			$('#spn_total_paid_amount').removeClass('text-red');
			disable_swiping = true;
		}



		$('tr.row_fees[data-id="18"]').find('.txt_fee_amount').val(number_format(swiping_fee,2));
		$('tr.row_fees[data-id="18"]').find('.txt_fee_amount').attr('disabled',disable_swiping);
	}


</script>
<!-- FOR OR PRINTING -->
<script type="text/javascript">

</script>
<?php if($opcode == 1): ?>
<script type="text/javascript">
	const active_loans = jQuery.parseJSON('<?php echo json_encode($active_loans ?? [])?>');
	const repayment_penalties = jQuery.parseJSON('<?php echo json_encode($repayment_penalty ?? [])?>');
	const repayment_fee_val = jQuery.parseJSON('<?php echo json_encode($repayment_fee_val ?? [])?>');


	$('#sel_borrower,#txt_date,#sel_transaction_type').attr('disabled',true);
	$(document).ready(function(){

		var redirect_data = jQuery.parseJSON(localStorage.getItem("redirect_print_or"));
		console.log({redirect_data});
		if(redirect_data != null){
			if(redirect_data.show_print_or == 1){
				if(redirect_data.repayment_token == '<?php echo $repayment_transaction->repayment_token; ?>'){
					print_or();
					// alert(123);
					localStorage.removeItem("redirect_print_or");
				}
			}
		}
		var redirect_data = jQuery.parseJSON(localStorage.getItem("redirect_print_entry"));
		console.log({redirect_data});
		if(redirect_data != null){
			if(redirect_data.show_print_entry == 1){
				if(redirect_data.repayment_token == '<?php echo $repayment_transaction->repayment_token; ?>'){
					$('#btn_entry').trigger('click')
					localStorage.removeItem("redirect_print_entry");
				}
			}
		}

		console.log({active_loans});

		display_loan_dues(active_loans.active_loans,[],1);
		//Initialize Loan Fees
		$.each(repayment_fee_val,function(i,item){


			$('tr[data-id="'+item.id_payment_type+'"]').find('.txt_fee_amount').val(number_format(parseFloat(item.amount ?? 0),2))
		})
		$('#repayment_penalty_body').html('')

		//Initialize penalties
		$.each(repayment_penalties,function(i,item){
			$('#repayment_penalty_body').append($penalty_row);
			$('.sel_penalty').last().val(item.id_payment_type);
			$('.txt_penalty_amount').last().val(number_format(parseFloat(item.amount),2))
			set_penalty_count()
		})

		if(repayment_penalties.length == 0){
			$('#repayment_penalty_body').html($no_penalty_row);
		}

		$('#btn_save_repayment').attr("disabled",false)
		computeAll(false);
	})
	console.log({active_loans})
</script>
<?php endif; ?>


<?php if($opcode == 0): ?>
<script type="text/javascript">
	$('#txt_transaction_date').on('change',function(){
		if(id_member_holder == '0'){
			return;
		}
	})
	function parseMemberLoanDues($date){
		$.ajax({
			type          :         'GET',
			url           :         '/repayment/member/loan_dues',
			data          :          {'id_member':id_member_holder,
			'date' : $date,
			'transaction_date' : $('#txt_transaction_date').val()},
			beforeSend    :          function(){
				show_loader();
			},
			success       :          function(response){
				hide_loader();
				console.log({response});
				if(response.is_valid_loan){
					repayment_date = response.repayment_date;
					id_member_holder = response.member_info.id_member;

				}else{
					repayment_date = "";
					id_member_holder = 0;

					Swal.fire({
						title: "No Active Loan(s) Found",
						text: '',
						icon: 'warning',
						showCancelButton : false,
						showConfirmButton : false,
						timer : 2500
					});
				}
				$('#btn_save_repayment').attr("disabled",!response.is_valid_loan)

				display_loan_dues(response.active_loans,response.loan_dues);

				// computeAll(true);
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

	function parseDueDates($id_member){
		$.ajax({
			type        :     'GET',
			url         :     'repayment/get_due_dates/',
			data        :     {'id_member' : $id_member,
			'transaction_date' : $('#txt_transaction_date').val()},
			beforeSend    :          function(){
				$('#spn_current_balance').text(number_format(0,2));
				$('#spn_previous_balance').text(number_format(0,2));
				$('#loan_dues_body').html('')	
				display_loan_dues([],[],0)
				computeAll();
				show_loader();
			},
			success     :     function(response){
				var out = '';
				hide_loader();
				if(response.dues.length > 0){
					id_member_holder = $id_member;
					$.each(response.dues,function(i,item){
						out += '<option value="'+item.due_date+'">'+item.due_date_text+'</option>';
					});


				}else{
					id_member_holder = 0;

					Swal.fire({
						title: "No Loan Dues Found",
						text: '',
						icon: 'warning',
						showCancelButton : false,
						showConfirmButton : false,
						timer : 2500
					});			

				}
				$('#txt_date').html(out)
				$('#txt_date').val(response.def_due);

				if($('#txt_date').val() != null){
					parseMemberLoanDues($('#txt_date').val());
				}

				console.log({response});
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

<?php if($allow_post): ?>
<script type="text/javascript">
	function post(){
		var current_payments = {};
		var previous_payments = {};


		var loan_payments = {};
		var amount_valid = true;
		$('.txt_loan_payment_amount').removeClass('mandatory');
		$('.txt_loan_payment_amount').each(function(){

			if($(this).is(":visible")){
				// var payment_type = $(this).attr('payment-type');
				var token = $(this).attr('loan-token');
				var amount_type = $(this).attr('amount-type');
				var due = $(this).attr("due");

				if(decode_number_format($(this).val()) > parseFloat(due)){
					// if(payment_type == "previous"){
					// 	amount_valid = false;
					// 	$(this).addClass('mandatory');					
					// }
				}
				
				var val =decode_number_format($(this).val());

				val  = isNaN(val)?0:val;

				if(val > 0){
					try{
						loan_payments[token][amount_type] = val;
					}catch(e){
						loan_payments[token] = {};
						loan_payments[token][amount_type] = val;
					}
				}

			}
		})
		console.log({loan_payments})


		if(!amount_valid){
			Swal.fire({
				title: "Paid Amount must not be greater than dues",
				text: '',
				icon: 'warning',
				showCancelButton : false,
				showConfirmButton : false,
				timer : 2500
			});
			return;
		}
		var is_swiping_amount_valid = computeAll();
		if(!is_swiping_amount_valid){
			Swal.fire({
				title: "Swiping Amount must be greater than or equal to total payment",
				text: '',
				icon: 'warning',
				showCancelButton : false,
				showConfirmButton : false,
				timer : 2500
			});

			return;
		}	
    	//Penalties
		var penalties = [];
		$('.row_penalty').each(function(){
			var temp = {};
			temp['id_payment_type'] = $(this).find('.sel_penalty').val();
			temp['amount'] = decode_number_format($(this).find('.txt_penalty_amount').val());
			penalties.push(temp);
		})

    	//fees
		var fees = [];
		$('.row_fees').each(function(){
			var id = $(this).attr('data-id');
			fees[id] = decode_number_format($(this).find('.txt_fee_amount').val());
		})

		var sp = ($('#sel_transaction_type').val() == 2)?decode_number_format($('#swiping_amount').val()):0;


		$.ajax({
			type        :          "GET",
			url         :          "/test/post",
			data        :          {'transaction_type' : $('#sel_transaction_type').val(),
			'input_mode' : $('#sel_input_mode').val(),
			'transaction_date' : $('#txt_transaction_date').val(),
			'id_bank' : $('#sel_bank').val(),
			'opcode' : '<?php echo $opcode?>',
			'loan_payments' : loan_payments,
			'repayment_date' : repayment_date,
			'id_member' : id_member_holder,
			'swiping_amount' :sp,
			'fees' : fees,
			'penalties' : penalties,

			'repayment_token' : '<?php echo $repayment_transaction->repayment_token ?? '' ?>',
			'remarks' : $('#txt_remarks').val(),
			'or_no' : $('#txt_or_rep').val()},
			beforeSend    :          function(){
				$('.mandatory').removeClass('mandatory');
				show_loader();
			},
			success     :          function(response){
				console.log({response});
				hide_loader()
				if(response.RESPONSE_CODE == "SUCCESS"){
					var link = "/repayment/view/"+response.REPAYMENT_TOKEN+"?href="+encodeURIComponent('<?php echo $back_link;?>');
					Swal.fire({
						title: "Loan Payment Successfully Saved !",
						html : "<a href='"+link+"'>Loan Payment ID# "+response.ID_REPAYMENT_TRANSACTION+"</a>",
						text: '',
						icon: 'success',
						showCancelButton : true,
						confirmButtonText: 'Add Loan Payment',
						showDenyButton: true,
						denyButtonText: `Print OR`,
						cancelButtonText: 'Close',
						showConfirmButton : true,     
						allowEscapeKey : false,
						allowOutsideClick: false,
						didOpen: (toast) => {
					    	// $('.swal2-actions').append('<button class="btn btn bg-gradient-success" onclick="print"> Print</button>')

					    	if($('#sel_transaction_type').val() != 4){
					    		$('<button class="btn btn bg-gradient-success" onclick="redirect_print_entry(`'+response.REPAYMENT_TOKEN+'`,`'+link+'`)" >'+response.entry_type+'</button>').insertBefore('.swal2-cancel')
					    	}
							


						}
					}).then((result) => {
						if(result.isConfirmed) {
							window.location = "/repayment/create?href="+encodeURIComponent('<?php echo $back_link;?>');
						}else if (result.isDenied) {
							var redirect_data = {
								'show_print_or' : 1,
								'repayment_token' : response.REPAYMENT_TOKEN
							}
							localStorage.setItem("redirect_print_or",JSON.stringify(redirect_data));
							window.location = 	link;
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

					if(response.invalid_inputs != null){
						$.each(response.invalid_inputs,function(i,val){
							$.each(val,function(k,token){
								console.log({token});
								$("input[amount-type='"+i+"'][loan-token='"+token+"']").addClass('mandatory');
							})
						})
    					// $.each(response.invalids.current,function(i,val){
    					// 	$(".txt_prin[loan-token='"+val+"'][payment-type='current']").addClass('mandatory')
    					// 	console.log({val});
    					// })
					}
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

		});
		console.log({current_payments});
	}

	function redirect_print_entry(reference,link){
		var redirect_data = {
			'show_print_entry' : 1,
			'repayment_token' : reference
		}
		localStorage.setItem("redirect_print_entry",JSON.stringify(redirect_data));
		window.location = 	link;
	}

	function save_repayment(){
		var id_member = $('#sel_borrower').val();
		if(id_member == null){
			Swal.fire({
				title: "Please Select Borrower",
				text: '',
				icon: 'warning',
				showCancelButton : false,
				showConfirmButton : false,
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
		}).then((result) => {
			if (result.isConfirmed) {
				post();
			} 
		})	
    	// alert(123);
	}


</script>
<?php endif; ?>
<script type="text/javascript">
	function update_or(){
		$or_opcode = 1;
		$('#or_modal').modal('show')
	}
</script>

<?php if(!$allow_post): ?>
<script type="text/javascript">
	$('#repayment_main_div').find('input,select').attr('disabled',true)
</script>
<?php endif; ?>



<?php $__env->stopPush(); ?>

<?php echo $__env->make('adminLTE.admin_template', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/repayment/repayment_form.blade.php ENDPATH**/ ?>