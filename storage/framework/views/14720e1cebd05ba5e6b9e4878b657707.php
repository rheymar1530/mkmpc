<?php $__env->startSection('content'); ?>

<style type="text/css">
	.tbl_pdc th, .tbl_pdc td{
		padding: 0.2rem;
		font-size: 0.85rem;
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
	.footer_fix{
		font-size: 11pt !important;
	}
</style>

<?php
$paymentModes=array(
	4=>'Check',1=>'Cash'
);
$check_types = [1=>"On-date",2=>"Post dated"];
?>

<div class="container-fluid main_form section_body">
	<?php $back_link = (request()->get('href') == '')?'/repayment-bulk':request()->get('href'); ?>
	<a class="btn btn-default btn-sm round_button" href="<?php echo e($back_link); ?>" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Loan Payment (Bulk) List</a>
	<div class="row">
		<!-- include('repayment-bulk.payment-for') -->
		<div class="col-md-12">
			<div class="card">
				<div class="card-body">
					<div class="text-center">
						<h4 class="head_lbl mb-3">Bulk Loan Payment <?php if($opcode==1): ?><small>(ID# <?php echo e($details->id_repayment); ?>)</small><?php endif; ?></h4>
					</div>

					<div class="row d-flex align-items-end">
						<div class="form-group col-md-4">
							<label class="lbl_color mb-0">Payment for</label>
							<select class="form-control" id="sel-payment-for">
								<?php $__currentLoopData = $repayment_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val=>$type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<option value="<?php echo e($val); ?>"><?php echo e($type); ?></option>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</select>
						</div>
						<div class="form-group col-md-4">
							<button class="btn bg-gradient-primary2 btn-md round_button" id="btn-add-payment-for" onclick="ShowPaymentForModal()">Select Member</button>
						</div>
					</div>

					<table class="table table-bordered table-head-fixed tbl_pdc w-100" id="tbl_loan">
						<thead>
							<tr class="text-center">
								<th>Member</th>
								<th>Loan Service</th>
								<th>Principal<br>Balance</th>
								<th>Interest<br>Balance</th>
								<th>Surcharge<br>Balance</th>
								<th>Total<br>Balance</th>
								<th id="spn-rep-type"><?php if($opcode==1): ?><?php if($details->payment_for == 1): ?> Amortization <?php else: ?> Statement Amount <?php endif; ?> <?php endif; ?></th>
								<th>Payment</th>
								<th width="4%"></th>
							</tr>
						</thead>

						<tfoot>
							<tr>
								<th colspan="2" class="footer_fix text-left font-weight-normal" style="text-align:center;background: #808080 !important;color: white;"></th>
								<th class="footer_fix text-right"  style="background: #808080 !important;color: white;" id="txt-principal"><?php echo e(number_format(0,2)); ?></th>
								<th class="footer_fix text-right"  style="background: #808080 !important;color: white;" id="txt-interest"><?php echo e(number_format(0,2)); ?></th>
								
								<th class="footer_fix text-right"  style="background: #808080 !important;color: white;" id="txt-surcharge"><?php echo e(number_format(0,2)); ?></th>
								<th class="footer_fix text-right"  style="background: #808080 !important;color: white;" id="txt-balance"><?php echo e(number_format(0,2)); ?></th>
								<th class="footer_fix text-right"  style="background: #808080 !important;color: white;" id="txt-ref"><?php echo e(number_format(0,2)); ?></th>
								<th class="footer_fix text-right td-total-payment"  style="background: #808080 !important;color: white;"><?php echo e(number_format(0,2)); ?></th>
								<th class="footer_fix text-left font-weight-normal" style="text-align:center;background: #808080 !important;color: white;"></th>
							</tr>
						</tfoot>
					</table>

					<div class="mt-2" >
						<div class="card c-border">
							<div class="card-body px-5">
								<h5 class="lbl_color">Payment Details</h5>
								<div class="form-row mt-4">
									<div class="form-group col-md-3">
										<label class="lbl_color text-sm mb-0">Date Received</label>
										<input type="date" class="form-control form-control-border frm-payment-details" value="<?php echo e($details->date ?? MySession::current_date()); ?>" name="date">
									</div>	
									<div class="form-group col-md-3">
										<label class="lbl_color text-sm mb-0">OR No.</label>
										<input type="text" class="form-control form-control-border frm-payment-details" value="<?php echo e($details->or_number ?? ''); ?>" name="or_number">
									</div>	
									<div class="form-group col-md-4">
										<label class="lbl_color text-sm mb-0">Payment Mode</label>
										<select class="form-control form-control-border frm-payment-details" name="paymode" id="sel-paymode">
											<?php $__currentLoopData = $paymentModes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val=>$desc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
											<option value="<?php echo e($val); ?>" <?php echo (($details->id_paymode ?? 4) == $val)?'selected':''; ?> ><?php echo e($desc); ?></option>
											<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
										</select>
									</div>

								</div>
							</div>
						</div>
						
						
					</div>
					<div id="div-payment-type">
						<?php echo $__env->make('repayment-bulk.payment_types', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
					</div>
					
					<div class="row" id="div-row-payment-button">
						<button class="btn btn-sm bg-gradient-dark ml-2" onclick="appendPayment()"><i class="fa fa-plus"></i>&nbsp;Add Payments</button>
					</div>
					<div class="row mt-2">
						<div class="col-md-12">
							<h4 class="float-right lbl_color">Total Payment : <span id="spn_total_payment">0.00</span></h4>
						</div>
												<div class="col-md-12">
							<h5 class="float-right lbl_color" id="chnge_hold">Change : <span id="spn_change">0.00</span></h5>
						</div>
					</div>
				</div>
				<div class="card-footer py-2">
					<button class="btn round_button btn bg-gradient-success2 float-right" onclick="post()">Save</button>
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo $__env->make('repayment-bulk.payment-for-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script type="text/javascript">
	const PAYMENTS = jQuery.parseJSON('<?php echo json_encode($Payments ?? []); ?>');
	const OPCODE = <?php echo e($opcode); ?>;
	const ID_REPAYMENT = <?php echo e($details->id_repayment ?? 0); ?>;
	const INDIVIDUAL = $('#div-individual').detach().html();
	const STATEMENT = $('#div-statement').detach().html();

	const BACK_LINK = `<?php echo $back_link; ?>`;


	let CURRENT_PAYMENT_FOR = $('#sel-payment-for').val();
	const on_change_text = "Changing this will remove all selected transactions. Are you sure do you want to still proceed ?";

	let MODE = 1;

	let CURRENT_MEMBER_DETAILS = {
		'member' : '',
		'id_member' : 0
	};

	let CURRENTLOANS = {};
	$(document).ready(function(){
		initPaymentFor(true);

		if(PAYMENTS.length > 0){
			$('#div-payment-type').html('');
			$.each(PAYMENTS,function(i,item){	
				appendPayment();
				var lastRow = $('.card-payment').last();
				lastRow.find('.frm-paymode').each(function(){
					var key = $(this).attr('name');
					var val = (key == 'amount')?number_format(item[key],2):item[key];
					$(this).val(val);

					// console.log();
				});
				// lastRow.find
			})
		}
	})
	$(document).on('change','#sel-payment-for',function(e,trigger){
		var sel = $(this);
		var transaction_length = $('#tbl_loan').find('tbody.payment-body').length;

		if(transaction_length > 0){
			Swal.fire({
				title: on_change_text,
				icon: 'warning',
				showDenyButton: false,
				showCancelButton: true,
				confirmButtonText: `Yes`,
				allowOutsideClick: false,
				allowEscapeKey: false,
			}).then((result) => {
				if (result.isConfirmed) {
					CURRENT_PAYMENT_FOR = $(sel).val();
					initPaymentFor(trigger);
					clear_transaction();
				}else{
					$(sel).val(CURRENT_PAYMENT_FOR);
				}
			});	
		}else{
			initPaymentFor(trigger);
		}
	});


	const initPaymentFor = (trigger=false)=>{
		var val = $('#sel-payment-for').val();
		MODE = val;

		
		$('#tbl_loan').find('.statement-head').remove();
		ComputeAll();
		if(val == 1){
			// Individual
			if(!trigger){
				$('#tbl_loan').find('tbody.payment-body').html('');
			}
			initIndividual(trigger);
		}else if(val == 2){
			$('#tbl_loan').find('tbody.payment-body').html('');
			// Statement

			// if ($('#sel-id-member').data('select2')) {}
			initStatementScripts(trigger);
		}
	}
	$(document).on('select2:open', () => {
		document.querySelector('.select2-search__field').focus();
	});	

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
	
	const ComputeAll=()=>{
		let TotalPayment = 0;
		$('.in-loan-payment').each(function(){
			var p = $(this).val();
			let payment = ($.isNumeric(p))?roundoff(p):decode_number_format(p);
			TotalPayment += payment;
		});
		$('.td-total-payment').text(number_format(TotalPayment));

		//total payments
		let AppliedPayment = 0;
		$('.chk-amount').each(function(){
			var ps = $(this).val();
			let payments = ($.isNumeric(ps))?roundoff(ps):decode_number_format(ps);
			AppliedPayment += payments;
		});

		// let spn_class = (roundoff(AppliedPayment) == roundoff(TotalPayment))?'text-success':'text-danger';
		let spn_class;

		var ap = roundoff(AppliedPayment);
		var tp = roundoff(TotalPayment);
		console.log({ap,tp});
		$('#chnge_hold').hide();
		if(ap == tp){
			spn_class = 'text-success';

		}else if(ap > tp){
			spn_class = 'text-primary';
			$('#spn_change').text(number_format(roundoff(ap-tp),2))
			$('#chnge_hold').show();
		}else{
			spn_class = 'text-danger';
		}

		console.log({spn_class});
		$('#spn_total_payment').text(number_format(AppliedPayment,2));
		$('#spn_total_payment').removeClass('text-success text-danger text-primary');
		$('#spn_total_payment').addClass(spn_class);

		var bal=0,ref=0,surcharge=0;
		$('tr.row-loan').each(function(){
			bal += decode_number_format($(this).find('.td-bal').text());
			ref += decode_number_format($(this).find('.td-ref').text());
			surcharge += decode_number_format($(this).find('.td-surcharge').text());
		});

		$('#txt-balance').text(number_format(bal,2));
		$('#txt-ref').text(number_format(ref,2));
		$('#txt-surcharge').text(number_format(surcharge,2));
	}

	const SelectTransaction = ()=>{
		if(MODE == 1){
			getLoan();
			$('#spn-rep-type').text('Amortization');
		}else{
			getStatements();
			$('#spn-rep-type').text('Statement Amount');
			
		}
		$('#payment-for-modal').modal('hide');
	}

	const ShowPaymentForModal = ()=>{
		if(MODE == 1){
			$('#payment-for-modal').modal('show');
		}else{
			parseStatements();
		}
	}
</script>



<script type="text/javascript" id="IndividualRepaymentScripts">
	const initIndividual = (trigger) =>{
		$('#div-type-field').html(INDIVIDUAL);
		$('#btn-select-transaction').text("Select Loan");
		$('#btn-add-payment-for').text("Select Member");
		initMemberSelect();
	}

	function initMemberSelect(){		
		$("#sel-member").select2({
			minimumInputLength: 2,
			width: '100%',
			dropdownParent: $('#payment-for-modal'),
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

	$(document).on('change','#sel-member',function(){
		var id_member = $(this).val();
		parseLoans(id_member);
	});	

	const parseLoans = (id_member)=>{
		$.ajax({
			type             :         'GET',
			url              :         '/repayment-bulk/member-loan',
			data             :         {'id_repayment' : ID_REPAYMENT, 'id_member' : id_member},
			success  	:  function(response){
				console.log({response});
				hide_loader();
				if(response.RESPONSE_CODE == "SUCCESS"){
					var out = '';
					
					CURRENT_MEMBER_DETAILS['member'] = response.member;
					CURRENT_MEMBER_DETAILS['id_member'] = response.id_member;
					$.each(response.loans,function(i,item){
						CURRENTLOANS[item.id_loan] = {
							'balance' : item.balance,
							'payment' : item.payment,
							'loan_token' : item.loan_token,
							'loan_name' : item.loan_name,
							'ref_amount' : item.ref_amount,
							'principal' : item.principal_balance,
							'interest' : item.interest_balance,
							'surcharge' : item.surcharge_balance,
							
						};
						var checked = $(`tr.row-loan[data-id-loan="${item.id_loan}"]`).length > 0?'checked':'';

						out += `<tr class="row-select-loan" data-id="${item.id_loan}">
						<td class="text-center"><input type="checkbox" class="chk-loan" ${checked}></td>
						<td>${item.loan_name}</td>
						<td class="text-right">${number_format(item.balance,2)}</td>
						</tr>`;
						
					})
					$('#body-loan-select').html(out);
				}else if(response.RESPONSE_CODE == "ERROR"){
					Swal.fire({
						title: response.message,
						text: '',
						icon: 'warning',
						showCancelButton : false,
						showConfirmButton : false,
						timer : 2500
					});	
					$('#body-loan-select').html('');
				}
			},error: function(xhr, status, error) {
				hide_loader();
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


	const  getLoan=()=>{
		$('.chk-loan').each(function(){
			var checked = $(this).prop('checked');
			var parentRow = $(this).closest('tr.row-select-loan');
			var idLoan = parentRow.attr('data-id');
			if(checked){
				if($(`tbody.payment-body[data-reference='${CURRENT_MEMBER_DETAILS['id_member']}']`).length == 0){
					$('#tbl_loan').append(`<tbody class="payment-body" data-reference='${CURRENT_MEMBER_DETAILS['id_member']}'></tbody>`);
				}

				$balance = {
					'principal' : CURRENTLOANS[idLoan]['principal'],
					'interest' : CURRENTLOANS[idLoan]['interest'],
					'surcharge' : CURRENTLOANS[idLoan]['surcharge']

				}

				appendLoanRow(CURRENT_MEMBER_DETAILS['id_member'],CURRENT_MEMBER_DETAILS['id_member'],CURRENT_MEMBER_DETAILS['member'],idLoan,CURRENTLOANS[idLoan]['loan_token'],CURRENTLOANS[idLoan]['loan_name'],CURRENTLOANS[idLoan]['balance'],CURRENTLOANS[idLoan]['payment'],0,CURRENTLOANS[idLoan]['ref_amount'],$balance);
			}else{
				removeLoanRow(idLoan,CURRENT_MEMBER_DETAILS['id_member'])
			}			
		});

		var length = $(`tbody.payment-body[data-reference='${CURRENT_MEMBER_DETAILS['id_member']}']`).find('tr').length;


		$(`tbody.payment-body[data-reference='${CURRENT_MEMBER_DETAILS['id_member']}']`).find('tr:gt(0) td.td-mem:first-child').remove();


		$(`tbody.payment-body[data-reference='${CURRENT_MEMBER_DETAILS['id_member']}']`).find(`tr.row-loan`).eq(0).find('.td-mem').attr('rowspan',length)
	}

	const removeLoanRow = (idLoan,id_member)=>{
		var memName = $(`tr.row-loan[data-id-loan="${idLoan}"]`).find('.td-mem').text();
		$(`tr.row-loan[data-id-loan="${idLoan}"]`).remove();
		var currentBody = $(`tbody.payment-body[data-reference='${id_member}']`);
		if(currentBody.find('tr').length == 0){
			currentBody.remove();
		}else{
			var memTD = $(`tr.row-loan[data-id-member="${id_member}"]`).eq(0);

			if(memTD.find('td.td-mem').length == 0){
				memTD.prepend(`<td class="font-weight-bold nowrap td-mem" rowspan="${memTD.find('td.td-mem').length}"><i>${memName}</i></td>`);
			}
		}
		$(`.row-select-loan[data-id="${idLoan}"]`).find('.chk-loan').prop('checked',false);

		ComputeAll();
	}

</script>

<script type="text/javascript" id="RepaymentStatementScripts">
	const initStatementScripts = (trigger) =>{
		$('#div-type-field').html(STATEMENT);
		$('#btn-select-transaction').text("Select Statement");
		$('#btn-add-payment-for').text("Select Statement");

		if(!trigger){
			parseStatements(trigger);
		}
	}	

	const parseStatements = (trigger)=>{
		$.ajax({
			type             :         'GET',
			url              :         '/repayment-bulk/parse-statement',
			data             :         {'id_repayment' : ID_REPAYMENT, 'id_brgy_lgu' : $('#sel-brgy-lgu').val()},
			beforeSend      :    function(){
				show_loader();
			},
			success  	:  function(response){
				console.log({response});
				hide_loader();
				if(response.RESPONSE_CODE == "SUCCESS"){
					var out = '';
					
					$.each(response.STATEMENTS,function(i,item){
						var checked = $(`tbody.payment-body[data-statement-id="${item.id_repayment_statement}"]`).length > 0?'checked':'';

						out += `<tr class="row-select-statement" data-id="${item.id_repayment_statement}">
						<td class="text-center"><input type="checkbox" class="chk-statement" ${checked}></td>
						<td>${item.brgy_lgu} (Statement No. ${item.id_repayment_statement}) [${item.statement_ref}]</td>
						<td class="text-right">${number_format(item.due,2)}</td>
						</tr>`;
					});

					$('#body-loan-select').html(out);
					$('#payment-for-modal').modal('show');
				}else if(response.RESPONSE_CODE == "ERROR"){
					Swal.fire({
						title: response.message,
						text: '',
						icon: 'warning',
						showCancelButton : false,
						showConfirmButton : false,
						timer : 2500
					});	
					$('#body-loan-select').html('');
				}
			},error: function(xhr, status, error) {
				hide_loader();
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
	const getStatements = ()=>{
		let Statements = [];
		$('input.chk-statement:checked').each(function(){
			Statements.push($(this).closest('tr.row-select-statement').attr('data-id'));
		});
		$.ajax({
			type             :         'GET',
			url              :         '/repayment-bulk/get-statements',
			data             :         {'id_repayment' : ID_REPAYMENT,'id_repayment_statement' : Statements},
			beforeSend      :    function(){
				show_loader();
			},
			success  	:  function(response){
				console.log({response});
				hide_loader();
				if(response.RESPONSE_CODE == "SUCCESS"){
					DrawStatementPayment(response.STATEMENTS,response.statement_details);
				}else if(response.RESPONSE_CODE == "ERROR"){
					Swal.fire({
						title: response.message,
						text: '',
						icon: 'warning',
						showCancelButton : false,
						showConfirmButton : false,
						timer : 2500
					});	
					$('#body-loan-select').html('');
				}
			},error: function(xhr, status, error) {
				hide_loader();
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
	const DrawStatementPayment = ($statements,$statementDetails)=>{
		$('#tbl_loan').find('tbody.payment-body,.statement-head').remove();
		$.each($statements,function(id_statement,members){
			$('#tbl_loan').children('tfoot').before(`<tr class="statement-head bg-gradient-success2 font-weight-bold"><td colspan="9"><span class="statement-brgy">${$statementDetails[id_statement][0]['brgy_lgu']}</span> (Statement No. ${id_statement}) [${$statementDetails[id_statement][0]['statement_ref']}]</td></tr>`);


			$.each(members,function(i,items){
				console.log({items});
				$('#tbl_loan').children('tfoot').before(`<tbody class="payment-body" data-reference='${i}' data-statement-id="${id_statement}"></tbody>`);
				
				$.each(items,function(x,item){
					$balance = {
						'principal' : item.principal,
						'interest' : item.interest,
						'surcharge' : item.surcharge
					}
					appendLoanRow(item.id_member,item.id_member,item.member,item.id_loan,item.loan_token,item.loan_name,item.balance,item.current_due,id_statement,item.ref_amount,$balance);
				});

				var length = $(`tbody.payment-body[data-reference='${i}'][data-statement-id="${id_statement}"]`).find('tr').length;


				$(`tbody.payment-body[data-reference='${i}'][data-statement-id="${id_statement}"]`).find('tr:gt(0) td.td-mem:first-child').remove();


				$(`tbody.payment-body[data-reference='${i}'][data-statement-id="${id_statement}"]`).find(`tr.row-loan`).eq(0).find('.td-mem').attr('rowspan',length)


				// var lastStatementBody = $('#tbl_loan').find('tbody.payment-body').last();
			});
			
		})
	}
</script>


<script type="text/javascript">
	const appendLoanRow = ($reference,$id_member,$member_name,$id_loan,$loan_token,$loan_name,$balance,$payment,$id_statement,$ref_amount,$balanceObj)=>{
		if(MODE == 1){
			if($(`tr.row-loan[data-id-loan="${$id_loan}"]`).length > 0){
				return;
			}			
		}	
		console.log({$balanceObj});

		let HasMemberSelected = ($(`tr.row-loan[data-id-member="${$id_member}"]`).length > 0)?true:false;

		let LoanRoWHTML = `<tr class="row-loan" data-id-member="${$id_member}" data-id-loan="${$id_loan}">`;
		LoanRoWHTML += `<td class="font-weight-bold nowrap td-mem"><i>${$member_name}</i></td>`;
		// if(!HasMemberSelected){

		// }
		
		LoanRoWHTML += `<td class="nowrap"><sup><a href="/loan/application/approval/${$loan_token}" target="_blank">[${$id_loan}] </a></sup>${$loan_name}</td>
		<td class="text-right td-principal">${number_format($balanceObj.principal,2)}</td>
		<td class="text-right td-interest">${number_format($balanceObj.interest,2)}</td>
		<td class="text-right td-surcharge">${number_format($balanceObj.surcharge,2)}</td>
		<td class="text-right td-bal">${number_format($balance,2)}</td>
		<td class="text-right td-ref">${number_format($ref_amount,2)}</td>
		
		
		<td class="in"><input class="form-control p-2 text-right txt-input-amount in-loan-payment w_border text-sm" value="${number_format($payment,2)}"></td>`;
		if(MODE == 1){
			LoanRoWHTML += `<td class="in text-center"><a class="btn btn-xs bg-gradient-danger" onclick="removeLoanRow(${$id_loan},${$id_member})"><i class="fa fa-trash"></i></a></td>`
		}else{
			LoanRoWHTML += `<td></td>`;
		}
		LoanRoWHTML += `</tr>`;


		if(MODE == 1){

			$(`tbody.payment-body[data-reference='${$reference}']`).append(LoanRoWHTML);
		}else{
			$(`tbody.payment-body[data-reference='${$reference}'][data-statement-id="${$id_statement}"]`).append(LoanRoWHTML);
		}
		

		// if(MODE == 1){
		// 	var length = $(`tbody.payment-body[data-reference='${$reference}']`).find('tr').length;
		// }else{
		// 	var length = $(`tbody.payment-body[data-reference='${$reference}'][data-statement-id="${$id_statement}"]`).find('tr').length;
		// 	console.log("123");
		// }
		console.log({length});
		// var length = $(`tr.row-loan[data-id-member="${$id_member}"]`).length;


		// $(`tr.row-loan[data-id-member="${$id_member}"]`).eq(0).find('.td-mem').attr('rowspan',length);

		ComputeAll();
	}
</script>

<script type="text/javascript">

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
				postRepayment();
			}
		});
	}


	const postRepayment = ()=>{
		var LoanPayments = [];
		$('tbody.payment-body').each(function(){
			let StatementID = $(this).attr('data-statement-id') ?? 0;
			$(this).find('tr.row-loan').each(function(){
				var temp = {
					'id_repayment_transaction' : 0,
					'id_repayment_statement' : StatementID,
					'id_member' : $(this).attr('data-id-member'),
					'id_loan': $(this).attr('data-id-loan'),
					'amount_paid': decode_number_format($(this).find('.in-loan-payment').val())
				};
				LoanPayments.push(temp);
			});
		});

		let PAYMENT = [];
		$('.card-payment').each(function(){
			var tempOBJ = {};
			$(this).find('.frm-paymode').each(function(){
				tempOBJ[$(this).attr('name')] = $(this).val();
			});
			tempOBJ['amount'] = decode_number_format(tempOBJ['amount']);
			console.log({tempOBJ});

			PAYMENT.push(tempOBJ);
		});



		let RepaymentDetails = {};

		$('.frm-payment-details').each(function(){
			RepaymentDetails[$(this).attr('name')] = $(this).val();
		})
		console.log({PAYMENT});


		let AjaxParam = {
			'Mode' : MODE,
			'Payments' : LoanPayments,
			'opcode' : OPCODE,
			'id_repayment' : ID_REPAYMENT,
			'PaymentDetails' : PAYMENT,
			'RepaymentDetails' : RepaymentDetails
		};
		
		$.ajax({
			type            :            'GET',
			url             :            '/repayment-bulk/post',
			data            :            AjaxParam,
			beforeSend      :       function(){
				show_loader();
				$('.mandatory').removeClass('mandatory');
				$('.in-loan-payment').removeClass('text-danger');
			},
			success         :            function(response){
					console.log({response});
					hide_loader();
					if(response.RESPONSE_CODE == "SUCCESS"){
						Swal.fire({
							title: response.message,
							icon: 'success',
							showCancelButton: false,
							showConfirmButton : false,
							cancelButtonText : 'Back to Loan Payment Bulk list',
							confirmButtonText: `Close`,
							allowOutsideClick: false,
							allowEscapeKey: false,
							timer : 3000
						}).then((result) => {
					
							window.location ='/repayment-bulk/view/'+response.ID_REPAYMENT+"?href="+encodeURIComponent(BACK_LINK);
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

						var errorField = response.errorField ?? [];
						$.each(errorField,function(i,item){
							$(`.frm-payment-details[name="${item}"]`).addClass('mandatory')
						});

						var errorPayment = response.errorPayment ?? [];
						$.each(errorPayment,function(i,items){
							// 
							var cCard = $('.card-payment').eq(i);
							$.each(items,function(j,field){
								cCard.find(`.frm-paymode[name="${field}"]`).addClass('mandatory');
							});
						})

						var InvalidLoans = response.invalidLoans ?? [];
						for(var i=0;i<InvalidLoans.length;i++){
							$(`tr.row-loan[data-id-loan="${InvalidLoans[i]}"]`).find('.in-loan-payment').addClass('text-danger');
						}				

					}
			},error: function(xhr, status, error) {
					hide_loader();
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
	

</script>

<?php if($details->payment_for == 1): ?>
<script type="text/javascript">
	const LOANS = jQuery.parseJSON(`<?php  echo json_encode($Loans ?? []); ?> `);

	$(document).ready(function(){
		$.each(LOANS,function(i,item){
			CURRENT_MEMBER_DETAILS['member'] = item.member;
			CURRENT_MEMBER_DETAILS['id_member'] = item.id_member;
			CURRENTLOANS[item.id_loan] = {
				'balance' : item.balance,
				'payment' : item.payment,
				'loan_token' : item.loan_token,
				'loan_name' : item.loan_name,
				'principal' : item.principal_balance,
				'interest' : item.interest_balance,
				'surcharge' : item.surcharge_balance,
			};


			var idLoan = parseInt(item.id_loan);
				// alert(CURRENTLOANS[idLoan]['balance']);


			if($(`tbody.payment-body[data-reference='${CURRENT_MEMBER_DETAILS['id_member']}']`).length == 0){
				$('#tbl_loan').append(`<tbody class="payment-body" data-reference='${CURRENT_MEMBER_DETAILS['id_member']}'></tbody>`);
			}

			$balance = {
				'principal' : CURRENTLOANS[idLoan]['principal'],
				'interest' : CURRENTLOANS[idLoan]['interest'],
				'surcharge' : CURRENTLOANS[idLoan]['surcharge']

			}



			appendLoanRow(CURRENT_MEMBER_DETAILS['id_member'],CURRENT_MEMBER_DETAILS['id_member'],CURRENT_MEMBER_DETAILS['member'],idLoan,CURRENTLOANS[idLoan]['loan_token'],CURRENTLOANS[idLoan]['loan_name'],CURRENTLOANS[idLoan]['balance'],CURRENTLOANS[idLoan]['payment'],0,item.ref_amount,$balance);

			// var length = $(`tbody.payment-body[data-reference='${CURRENT_MEMBER_DETAILS['id_member']}']`).find('tr').length;
			// $(`tbody.payment-body[data-reference='${CURRENT_MEMBER_DETAILS['id_member']}']`).find('tr:gt(0) td.td-mem:first-child').remove();
			// $(`tbody.payment-body[data-reference='${CURRENT_MEMBER_DETAILS['id_member']}']`).find(`tr.row-loan`).eq(0).find('.td-mem').attr('rowspan',length);

		})
		ComputeAll();

	})





	

</script>
<?php else: ?>
<script type="text/javascript">
	$(document).ready(function(){
		const STATEMENT_DATA = jQuery.parseJSON(`<?php  echo json_encode($StatementData ?? []); ?> `);
		DrawStatementPayment(STATEMENT_DATA.STATEMENTS,STATEMENT_DATA.statement_details);
		ComputeAll();
	});
</script>
<?php endif; ?>


<?php endif; ?>

<?php $__env->stopPush(); ?>
<?php echo $__env->make('adminLTE.admin_template', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/repayment-bulk/form.blade.php ENDPATH**/ ?>