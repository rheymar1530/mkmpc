<?php $__env->startSection('content'); ?>

<style type="text/css">
	.tbl_pdc th, .tbl_pdc td{
		padding: 0.2rem;
		font-size: 0.85rem;
/*		font-weight: 600;*/
	}
	.tbl_pdc td.in{
		padding: 0;
	}
	td.in input{
		height: 27px !important;
		font-size: 0.8rem;
	}
	.tbl_pdc th{
		padding: 0.4rem;
		font-size: 0.8rem;
	}
	.selected_rp{
		background: #ccffcc;
	}
	.borders{
		border-top: 3px solid gray!important;
		border-bottom: 3px solid gray!important;
	}
	td input.txt-input-amount{
		border: 2px solid !important;
	}
	.nowrap{
		white-space: nowrap;
	}
	.hidden-member{
		display: none;
	}
	.centered-text{
		vertical-align: middle !important;
	}
</style>

<div class="main_form" style="margin-top: -20px;">
	<?php $back_link = (request()->get('href') == '')?'/change-payable':request()->get('href'); ?>
	<a class="btn btn-default btn-sm round_button" href="<?php echo e($back_link); ?>" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Change Payable List</a>
	<?php if($opcode == 0): ?>
	<div class="card">
		<div class="card-body px-5">
			<div class="text-center mb-3">
				<h4 class="head_lbl">Change Payables</h4>
			</div>
			<div style="max-height: calc(100vh - 140px);overflow-y: auto;">
				<table class="table table-bordered table-head-fixed tbl_pdc w-100">
					<thead>
						<tr class="text-center">
							
							<th>ID</th>
							<th>Date</th>
							<th>Change for</th>
							<th>Check Date</th>
							<th>Check Bank</th>
							<th>Check Amount</th>
							<th>Total</th>
							<th>Deposit Status</th>
							<th>Change</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php $__currentLoopData = $ChangeList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idRepayment=>$Changes): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<?php 
						$rowCount = count($Changes); 
						$TotalCheckAmount = collect($Changes)->sum('amount');
						?>
						<?php $__currentLoopData = $Changes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c=>$Change): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<tr>
							<?php if($c == 0): ?>
							<td class="centered-text text-center" rowspan="<?php echo e($rowCount); ?>"><?php echo e($Change->id_repayment); ?></td>
							<td class="centered-text" rowspan="<?php echo e($rowCount); ?>"><?php echo e($Change->transaction_date); ?></td>
							<td><?php echo $Change->change_for;?></td>
							<?php endif; ?>
							<td><?php echo e($Change->check_date); ?></td>
							<td><?php echo e($Change->check_bank); ?></td>
							<td class="text-right"><?php echo e(number_format($Change->amount,2)); ?></td>

							<?php if($c==0): ?>
							<td class="text-right centered-text" rowspan="<?php echo e($rowCount); ?>"><?php echo e(number_format($TotalCheckAmount,2)); ?></td>
							<td class="centered-text" rowspan="<?php echo e($rowCount); ?>"><span class="badge badge-<?php echo e($Change->deposit_badge); ?> text-xs"><?php echo e($Change->deposit_status_description); ?></span></td>
							<td class="text-right centered-text" rowspan="<?php echo e($rowCount); ?>"><?php echo e(number_format($Change->change_balance,2)); ?></td>
							<td class="centered-text text-center" rowspan="<?php echo e($rowCount); ?>">
								<?php if($Change->deposit_status == 2): ?>
								<a class="btn bg-gradient-success2 btn-xs" onclick="ParseRepaymentDetails(<?php echo e($Change->id_repayment); ?>)">Create Change</a>
								<?php endif; ?>
							</td>
							<?php endif; ?>
						</tr>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<?php endif; ?>


	<div class="card">
		<div class="card-body px-5" id="crd-repayment-details">
			<div class="text-center">
				<h5 class="head_lbl mb-5">Change Application</h5>
			</div>
			<?php if($opcode == 1): ?>
			<div class="text-center mb-3">
				<h4 class="head_lbl">Change Payables <small>(ID# <?php echo e($change_details->id_change_payable); ?>)</small></h4>
			</div>
			<?php endif; ?>
			<div class="row">
				<div class="form-group col-md-2 mb-2">
					<label class="lbl_color my-0">Loan Payment ID : </label>
				</div>
				<div class="form-group col-md-9 mb-2"><span class="font-weight-normal lbl_color" id="spn-repayment-id"></span> <span class="font-weight-normal lbl_color" id="spn-change-for"></span></div>
			</div>

			<div class="row">
				<div class="form-group col-md-3 mb-2">
					<label class="lbl_color my-0">Loan Payment: </label>
				</div>
				<div class="form-group col-md-1 mb-2 text-right"><span class="font-weight-normal lbl_color" id="spn-loan-payment"></span></div>
			</div>
			<div class="row">
				<div class="form-group col-md-3 mb-2">
					<label class="lbl_color my-0">Check Amount: </label>
				</div>
				<div class="form-group col-md-1 mb-2 text-right"><span class="font-weight-normal lbl_color" id="spn-check-amount"></div>
			</div>
			<div class="row">
				<div class="form-group col-md-3 mb-2">
					<label class="lbl_color my-0">Change Released: </label>
				</div>
				<div class="form-group col-md-1 mb-2 text-right"> <span class="font-weight-normal lbl_color" id="spn-change-released-amount"></span></div>
			</div>
			<div class="row">
				<div class="form-group col-md-3 mb-2">
					<label class="lbl_color my-0">Change (<i>For Release</i>): </label>
				</div>
				<div class="form-group col-md-1 mb-2 text-right"> <span class="font-weight-normal lbl_color" id="spn-change"></span></div>
			</div>

			<hr clas="my-0">
			<div class="row">
				<div class="form-group col-md-3">
					<label class="lbl_color mb-0">Date</label>
					<input type="date" class="form-control form-control-border" id="txt_date" value="<?php echo e($change_details->date ?? MySession::current_date()); ?>">
				</div>
				<div class="form-group col-md-5">
					<label class="lbl_color mb-0">Remarks</label>
					<input class="form-control form-control-border" id="txt_remarks" value="<?php echo e($change_details->remarks ?? ''); ?>">
				</div>
			</div>

			<div class="row">
				<div class="col-md-12 mt-3">
					<div class="card c-border h-100">
						<div class="card-body px-5">
							<h5>Member Application <button type="button" class="btn btn-xs bg-gradient-info" data-toggle="modal" data-target="#member-modal"><i class="fa fa-plus"></i>&nbsp;Add</button></h5>
							<table class="table table-bordered table-head-fixed tbl_pdc w-100">
								<colgroup>
									<col width="5%">
									<col width="60%">
									<col>
									<col width="3%">
								</colgroup>
								<thead>
									<tr class="text-center">
										<th></th>
										<th>Member</th>
										<th>Amount</th>
										<th></th>
									</tr>
								</thead>


								<tbody id="memberBody">

								</tbody>
							</table>	
						</div>
					</div>
					
				</div>
				<div class="col-md-12 mt-3">
					<div class="card c-border h-100">
						<div class="card-body px-5">
							<h5>Income <button class="btn btn-xs bg-gradient-info" onclick="appendIncome()"><i class="fa fa-plus"></i>&nbsp;Add</button></h5>
							<table class="table table-bordered table-head-fixed tbl_pdc w-100">
								<tr class="text-center">
									<th></th>
									<th>Account</th>
									<th>Amount</th>
									<th width="5%"></th>
								</tr>
								<tbody id="incomeBody">
									<tr class="row-other-income">
										<td class="text-center spn-counter" width="5%">1</td>
										<td class="p-0">
											<select class="form-control sel-account p-0">
												<?php $__currentLoopData = $chartAccounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ca): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<option value="<?php echo e($ca->id_chart_account); ?>"><?php echo e($ca->description); ?></option>
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											</select>
										</td>
										<td class="p-0"><input class="form-control form-control-border txt-input-amount txt-change-payable text-right" value="0.00"></td>
										<td class="p-0"><a class="btn btn-xs bg-gradient-danger" onclick="removeIncome(this)"><i class="fa fa-times"></i></a></td>
									</tr>
								</tbody>
							</table>					
						</div>
					</div>
				</div>
			</div>
			<div class="row mt-3">
				<div class="col-md-12">
					<h5>Total Change : <span id="spn-total-change"></span></h5>
				</div>
			</div>
		</div>
		<div class="card-footer py-2">
			<button class="btn bg-gradient-success2 btn-md float-right" onclick="post()">Save</button>
		</div>
	</div>
</div>

<?php echo $__env->make('change-payable.member-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>




<script type="text/javascript">
	const OPCODE = <?php echo e($opcode); ?>;
	const ID_CHANGE_PAYABLE = <?php echo e($change_details->id_change_payable ?? 0); ?>;
	const OTHER_INCOME_HTML = `<tr class="row-other-income">${$('tr.row-other-income').detach().html()}</tr>`;
	const NO_OTHER_INCOME = `<tr id="tr-no-income">
	<td colspan="4" class="text-center">No other income selected</td>
	</tr>`;
	const NO_MEMBER = `<tr id="tr-no-member">
	<td colspan="3" class="text-center">Please Select Loan Payment</td>
	</tr>`;

	let CURRENT_REPAYMENT_DETAILS = {'id_repayment' : 0, 'total_change' : 0};
	const BACK_LINK = `<?php echo $back_link; ?>`;


	const initRepaymentDetails = (details)=>{
		$('#spn-repayment-id').text(details.id_repayment);
		$('#spn-loan-payment').text(number_format(details.total_amount,2));
		$('#spn-check-amount').text(number_format(details.total_payment,2));
		$('#spn-change').text(number_format(details.change_payable,2));
		$('#spn-change-for').html(details.change_for);

		$('#spn-change-released-amount').text(number_format(details.released_change,2));
		CURRENT_REPAYMENT_DETAILS={
			'id_repayment' : details.id_repayment,
			'total_change' : details.change_payable
		}		
	}

	const initMember = (members)=>{
		$('#tr-no-member').remove();
		$('#memberBody').html('');
		let memberOut = ``;
		$.each(members,function(i,item){
			memberOut += `<tr class="row-member" data-id="${item.id_member}" data-add="${item.data_add}">
			<td class="text-center tc">${i+1}</td>
			<td class="">${item.member_name}</td>
			<td class="p-0"><input class="form-control form-control-border txt-input-amount text-right txt-change-payable" value="0.00"></td>
			<td>`;

			if(item.data_add == 1){
				memberOut += `<a class="btn btn-xs bg-gradient-danger w-100" onclick="removeMember(this)"><i class="fa fa-times"></i></a>`;
			}
			memberOut += `</td>
			</tr>`;
		});
		$('#memberBody').html(memberOut);
	}
	$(document).on("focus",".txt-input-amount",function(){
		var val = $(this).val();
		if(val == '' || val == 'NaN'){
			val = '0.00';
		}
		$(this).val(decode_number_format(val));	
	});

	$(document).on("blur",".txt-input-amount",function(){
		var val = $(this).val();
		if(!$.isNumeric(val)){
			var decoded_val = decode_number_format(val);
			val = (!isNaN(decoded_val))?decoded_val:0;
		}
		$(this).val(number_format(parseFloat(val)));

		ComputeAll();
	});

	const ComputeAll =()=>{
		let TotalChange = 0;
		$('.txt-change-payable').each(function(){
			var p = $(this).val();
			let payment = ($.isNumeric(p))?roundoff(p):decode_number_format(p);
			TotalChange += payment;
		});

		$('#spn-total-change').text(number_format(TotalChange,2));

		$('#spn-total-change').removeClass('text-danger text-success text-primary');
		if(roundoff(TotalChange) > roundoff(CURRENT_REPAYMENT_DETAILS['total_change'])){
			$('#spn-total-change').addClass('text-danger');
		}else if(roundoff(TotalChange) == roundoff(CURRENT_REPAYMENT_DETAILS['total_change'])){
			$('#spn-total-change').addClass('text-success');
		}else{
			$('#spn-total-change').addClass('text-primary');
		}

		console.log({TotalChange});
	}

	const appendIncome = ()=>{
		// $('#tr-no-income').remove();
		$('#incomeBody').append(OTHER_INCOME_HTML);
		refreshIncomeCounter();
	}

	const removeIncome=(obj)=>{
		var parentRow = $(obj).closest('tr.row-other-income');
		parentRow.remove();
		refreshIncomeCounter();
	}

	const refreshIncomeCounter = ()=>{
		var l = 0;
		$('.spn-counter').each(function(i){

			$(this).text(i+1);
			l++;
		})
		if(l == 0){
			$('#incomeBody').html(NO_OTHER_INCOME);
		}else{
			$('#tr-no-income').remove();
		}
		ComputeAll();
	}
	const post = ()=>{
		Swal.fire({
			title: 'Are you sure you want to save this ?',
			icon: 'warning',
			showDenyButton: false,
			showCancelButton: true,
			confirmButtonText: `Yes`,
			allowOutsideClick: false,
			allowEscapeKey: false,
		}).then((result) => {
			if (result.isConfirmed) {
				postChange();
			}
		});
	}

	const clearCurrentRepayment = ()=>{
		CURRENT_REPAYMENT_DETAILS = {'id_repayment' : 0, 'total_change' : 0};
		$('#tr-no-member').remove();
		$('#crd-repayment-details').find('span').text("");
		$('#incomeBody').html(NO_OTHER_INCOME);
		$('#memberBody').html(NO_MEMBER);
		ComputeAll();

	}

	const postChange = ()=>{
		if(CURRENT_REPAYMENT_DETAILS['id_repayment'] == 0){
			return;
		}

		//compile member change
		let MemberChange = [];
		$('tr.row-member').each(function(){
			var p = $(this).find('.txt-change-payable').val();
			amount = ($.isNumeric(p))?roundoff(p):decode_number_format(p);

			var id_member = $(this).attr('data-id');
			MemberChange.push({'id_member' : id_member,'amount' : amount})
			if(amount > 0){

			}
			console.log({amount});
		});

		//other income
		let OtherIncome = [];
		$('tr.row-other-income').each(function(){
			var p = $(this).find('.txt-change-payable').val();
			amount = ($.isNumeric(p))?roundoff(p):decode_number_format(p);

			if(amount > 0){
				var id_account = $(this).find('.sel-account').val();
				OtherIncome.push({'id_account' : id_account,'amount' : amount})
			}
			console.log({amount});
		});

		let AjaxParam = {
			'id_repayment' : CURRENT_REPAYMENT_DETAILS['id_repayment'],
			'member_change' : MemberChange,
			'other_income' : OtherIncome,
			'date' : $('#txt_date').val(),
			'remarks' : $('#txt_remarks').val(),
			'opcode' : OPCODE,
			'id_change_payable' : ID_CHANGE_PAYABLE
		};

		$.ajax({

			type          :       'GET',
			url           :       '/change-payable/post',
			data          :       AjaxParam,
			beforeSend  :  function(){
				show_loader();
			},
			success       :       function(response){
				console.log({response});
				hide_loader();
				if(response.RESPONSE_CODE == "SUCCESS"){
					Swal.fire({
						title: response.message,
						icon: 'success',
						showCancelButton: false,
						showConfirmButton : false,
						cancelButtonText : 'Back to Change Payable list',
						confirmButtonText: `Close`,
						allowOutsideClick: false,
						allowEscapeKey: false,
						timer : 2500
					}).then((result) => {
											// location.reload();
						window.location ='/change-payable/view/'+response.ID_CHANGE_PAYABLE+"?href="+encodeURIComponent(BACK_LINK);
					})
				}else if(response.RESPONSE_CODE == "ERROR"){
					Swal.fire({
						title: response.message,
						text: '',
						icon: 'warning',
						showCancelButton : false,
						showConfirmButton : false,
						timer : 2500
					});	

										// let invalid_fields = response.invalid_fields ?? [];

										// for(var i=0;i<invalid_fields.length;i++){
										// 	$(`.frm-paymode[name="${invalid_fields[i]}"]`).addClass('mandatory');
										// }

				}
			},error: function(xhr, status, error) {
				hide_loader();
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

		console.log({AjaxParam});
	}
</script>
<?php if($opcode == 0): ?>
<script type="text/javascript">
	const ParseRepaymentDetails=(id_repayment)=>{
		$.ajax({
			type          :      'GET',
			url           :      '/change-payable/parseRepayment',
			data          :      {'id_repayment' : id_repayment},
			beforeSend  :  function(){
				show_loader();
			},
			success       :      function(response){
				console.log({response});
				hide_loader();
				if(response.RESPONSE_CODE == "SUCCESS"){
					initRepaymentDetails(response.details)
					initMember(response.memberList);
					ComputeAll();
				}else if(response.RESPONSE_CODE == "ERROR"){
									// Swal.fire({
									// 	title: response.message,
									// 	text: '',
									// 	icon: 'warning',
									// 	showCancelButton : false,
									// 	showConfirmButton : false,
									// 	timer : 2500
									// });	



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
		});
	}
	$(document).ready(function(){
		refreshIncomeCounter();
		$('#memberBody').html(NO_MEMBER);
	});
</script>
<?php else: ?>
<script type="text/javascript">
	const CHANGE_DETAILS = jQuery.parseJSON('<?php echo json_encode($details); ?>');
	const MEMBERS = jQuery.parseJSON('<?php echo json_encode($memberList); ?>');

	const MEMBER_APPLICATION = jQuery.parseJSON('<?php echo json_encode($ChangeApplication[1] ?? []); ?>');
	const OTHER_APPLICATION = jQuery.parseJSON('<?php echo json_encode($ChangeApplication[2] ?? []); ?>');
	console.log({CHANGE_DETAILS,MEMBER_APPLICATION,OTHER_APPLICATION});
	$(document).ready(function(){
		initRepaymentDetails(CHANGE_DETAILS);
		initMember(MEMBERS);

		$.each(MEMBER_APPLICATION,function(i,item){
			$(`tr.row-member[data-id="${item.id_member}"]`).find('input.txt-change-payable').val(number_format(item.amount,2));
		});

		if(OTHER_APPLICATION.length > 0){
			$.each(OTHER_APPLICATION,function(i,item){
				appendIncome();
				var $last_other_row = $('tr.row-other-income').last();

				$last_other_row.find('.sel-account').val(item.id_chart_account);
				$last_other_row.find('input.txt-change-payable').val(number_format(item.amount,2));
				
			});
		}

		ComputeAll();
		refreshIncomeCounter();



		// refreshIncomeCounter();
		// $('#memberBody').html(NO_MEMBER);
	});	
</script>
<?php endif; ?>

<?php $__env->stopPush(); ?>
<?php echo $__env->make('adminLTE.admin_template', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/change-payable/form.blade.php ENDPATH**/ ?>