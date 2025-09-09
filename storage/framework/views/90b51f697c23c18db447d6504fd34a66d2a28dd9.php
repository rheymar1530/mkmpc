<?php $__env->startSection('content'); ?>
<style type="text/css">
	.tbl_mem th,.tbl_mem td,.tbl-loans th{
		padding: 2px 5px 2px 8px;
		font-size: 0.9rem;
		cursor: pointer;

	}

	.tbl-loans td{
		padding: 0px;
	}
	.sel-member{
		background: #66ff99;
	}
	.spn-check{
		color:green;
	}
	tr.delete input, tr.delete select{
		color: #ff6666 !important;
	}
</style>

<div class="container-fluid">
	<div class="row">
		<div class="col-md-3">
			<div class="card">
				<div class="card-body">
					<form>
						<div class="row">
							<div class="col-md-9">
								<select class="form-control mb-3 p-0" name="q">
									<?php $__currentLoopData = $membership_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<option value="<?php echo e($mt->id_membership_type); ?>" <?php echo ($selected_type == $mt->id_membership_type)?'selected':''; ?> ><?php echo e($mt->description); ?></option>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

								</select>								
							</div>
							<div class="col-md-3">
								<button class="btn btn-sm bg-gradient-primary w-100"><i class="fa fa-search"></i></button>
							</div>
		
						</div>
					</form>


					<input type="text" class="form-control" id="txt_search" placeholder="Search...">
					<div class="mt-3" style="max-height: calc(100vh - 50px);overflow-y: auto;">
						<table class="table tbl_mem table-bordered table-head-fixed table-hover">
							<thead>
								<tr>
									<th>Member</th>
								</tr>
							</thead>
							<tbody>
								<?php $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $brgy_lgu=>$mem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

								<?php if($brgy_lgu !== ""): ?>
								<tr>
									<td class="bg-gradient-success font-weight-bold"><?php echo e($brgy_lgu); ?></td>
								</tr>
								<?php endif; ?>
								<?php $__currentLoopData = $mem; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr class="row-member " data-id="<?php echo e($m->id_member); ?>" title="Double click to add loan">
									<td class="td-member"><?php echo e($m->member); ?><?php if($m->count > 0): ?><i class="fas fa-check-circle float-right spn-check"></i><?php endif; ?></td>
								</tr>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-9">
			<div class="card">
				<div class="card-body">
					<div class="text-center">
						<h5 class="head_lbl">Member Loans</h5>
					</div>
					<h5 class="mt-3">Member: <span id="memb_name">-</span></h5>
					<table class="table tbl-loans table-bordered table-head-fixed">
						<thead>
							<tr>
								<th style="width:30%">Loan Service</th>
								<th>Principal Amt</th>
								<th>Interest Rate</th>
								<th>Terms</th>
								<th>Date Granted</th>
								<th>Paid Principal</th>
								<th>Paid Interest</th>
								<th width="3%"></th>
							</tr>
						</thead>
						<tbody id="body-loan">
							<tr class="loan-row">
								<td>
									<select class="form-control in-loan sel-loan-service" key="id_loan_service">
										<?php $__currentLoopData = $loan_services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ls): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<option value="<?php echo e($ls->id_loan_service); ?>"><?php echo e($ls->name); ?></option>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									</select>
								</td>
								<td>
									<input type="text" class="form-control in-loan text-right in_prin" value="0.00" key="principal_amount">
								</td>
								<td>
									<input type="text" class="form-control in-loan text-right in_interest" value="0.00" key="interest_rate">
								</td>
								<td>
									<input type="text" class="form-control in-loan in-terms" value="1" key="term">
								</td>
								<td>
									<input type="date" class="form-control in-loan"  key="date_approved" value="<?php echo e(MySession::current_date()); ?>">
								</td>
								<td>
									<input type="text" class="form-control in-loan text-right paid-prin" value="0.00" key="paid_principal">
								</td>
								<td>
									<input type="text" class="form-control in-loan text-right paid-int" value="0.00" key="paid_interest">
								</td>
								<td class="pl-1 td-but"><a class="btn btn-xs btn-danger" onclick="remove_loan_row(this)"><i class="fa fa-trash"></i></a></td>
							</tr>
						</tbody>
					</table>
					<button class="btn bg-gradient-primary mt-3" onclick="append_loan();"><i class="fa fa-plus"></i>&nbsp;Add Loan</button>

				</div>
				<div class="card-footer p-2">
					<button class="btn bg-gradient-success float-right" onclick="post()">Save</button>
				</div>
			</div>
		</div>
	</div>	
</div>


<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script type="text/javascript">

	const loan_row = `<tr class="loan-row">${$('tr.loan-row').html()}</tr>`;
	const no_loan = '<tr class="text-center" id="row-no-loan"><td colspan="8" class="p-3">No Record Found</td></tr>';

	let CURRENT_MEMBER = 0;
	$('tr.loan-row').remove();
	$('#body-loan').html(no_loan);
	$(document).on('dblclick','tr.row-member',function(){
		var id = $(this).attr('data-id');
		$('tr.sel-member').removeClass('sel-member');
		$(this).addClass('sel-member');
		getLoan(id);
	});
	$(document).ready(function(){
		$("#txt_search").on("keyup", function() {
			var value = $(this).val().toLowerCase();
			$("tr.row-member").filter(function() {
				$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
			});
		});
	});
	const remove_loan_row=(obj)=>{
		parent_row = $(obj).closest('tr.loan-row');
		// <a class="btn btn-xs btn-danger" onclick="remove_loan_row(this)"><i class="fa fa-trash"></i></a>
		if(parent_row.attr('loan-id')){
			parent_row.addClass('delete');
			parent_row.find('.td-but').html(`<a class="btn btn-xs btn-primary" onclick="undo_loan(this)"><i class="fa fa-recycle"></i></a>`);
		}else{
			parent_row.remove();
			if($('tr.loan-row').length == 0){
				$('#body-loan').html(no_loan);
			}
		}

	}

	const undo_loan = (obj)=>{
		parent_row = $(obj).closest('tr.loan-row');
		parent_row.find('.td-but').html(`<a class="btn btn-xs btn-danger" onclick="remove_loan_row(this)"><i class="fa fa-trash"></i></a>`);
		parent_row.removeClass('delete');
	}
	const getLoan =(id_member)=>{
		$.ajax({
			type          :        'GET',
			url           :        '/sync-loan/parse-member-loan',
			data          :        {'id_member' : id_member},
			beforeSend    :        function(){
								   show_loader();
			},
			success       :        function(response){
									hide_loader();
								   $('#body-loan').html('');
								   CURRENT_MEMBER = id_member;

								   $('#memb_name').text(response.member_details.member);
								   if(response.loans.length > 0){
									   $.each(response.loans,function(i,item){
									   	append_loan();

									   	$last = $('tr.loan-row').last();
									   	$last.attr('loan-id',item.id_loan);

									   	$last.find('.in-loan[key="id_loan_service"]').val(item.id_loan_service);
									   	$last.find('.in-loan[key="principal_amount"]').val(number_format(item.principal_amount,2));
									   	$last.find('.in-loan[key="interest_rate"]').val(number_format(item.interest_rate,2));
									   	$last.find('.in-loan[key="term"]').val(item.term);
									   	$last.find('.in-loan[key="date_approved"]').val(item.date_approved);
									   	$last.find('.in-loan[key="paid_principal"]').val(number_format(item.paid_principal,2));
									   	$last.find('.in-loan[key="paid_interest"]').val(number_format(item.paid_interest,2));
									   });


									   // $('.in_prin').each(function(){
									   // 		// $(this).trigger('blur');
									   // })
								   }else{
								   		$('#body-loan').html(no_loan);
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
	const append_loan = ()=>{
		$('#row-no-loan').remove();
		$('#body-loan').append(loan_row);
	}
	$(document).on('select2:open', (e) => {
		const selectId = e.target.id

		$(".select2-search__field[aria-controls='select2-" + selectId + "-results']").each(function (
			key,
			value,
			) {
			value.focus()
		})
	})	
	$(document).on("focus",".in-loan.text-right",function(){
		var val = $(this).val();
		if(val == '' || val == 'NaN'){
			val = '0.00';
		}
		$(this).val(decode_number_format(val));	
	})
	$(document).on("blur",".in-terms",function(){

		var parent_row = $(this).closest('.loan-row');
		var principal = decode_number_format(parent_row.find('.in_prin').val());
		var interest = decode_number_format(parent_row.find('.in_interest').val());
		var term = parent_row.find('.in-terms').val();
		var print_amt = number_format(principal/term,2);
		var interest_amount = principal*(interest/100);
		parent_row.find('.paid-prin').attr('title',number_format(principal/term,2));
		parent_row.find('.paid-int').attr('title',number_format(interest_amount,2))

		console.log({principal,interest,term,print_amt,interest_amount});


	})

	$(document).on("blur",".in-loan.text-right",function(){
		var val = $(this).val();
		if(!$.isNumeric(val)){
			val = 0;
		}
		$(this).val(number_format(parseFloat(val)));

		var parent_row = $(this).closest('.loan-row');
		var principal = decode_number_format(parent_row.find('.in_prin').val());
		var interest = decode_number_format(parent_row.find('.in_interest').val());
		var term = parent_row.find('.in-terms').val();
		var print_amt = number_format(principal/term,2);
		var interest_amount = principal*(interest/100);
		parent_row.find('.paid-prin').attr('title',number_format(principal/term,2));
		parent_row.find('.paid-int').attr('title',number_format(interest_amount,2))

		console.log({principal,interest,term,print_amt,interest_amount});

	});

	const post = ()=>{
		Swal.fire({
			title: 'Do you save this?',
			icon: 'warning',
			showDenyButton: false,
			showCancelButton: true,
			confirmButtonText: `Save`,
		}).then((result) => {
			if (result.isConfirmed) {
				// alert("POSTED");
				post_request();
			} 
		})	
		
	}

	// $(document).on('keyup','.in_prin',function(){
	// 	var parent_row = $(this).closest('tr.loan-row');
	// 	var val = $(this).val();

		
	// 	var interest = parent_row.find('.in_interest').val();

	// 	console.log({val,interest});
	// })s


	const post_request = () =>{
		var loans = [];
		var deleted=[];
		$('tr.loan-row').each(function(){
			if(!$(this).hasClass('delete')){
				var t= {};
				$(this).find('.in-loan').each(function(){
					var val = ($(this).hasClass('text-right'))?decode_number_format($(this).val()):$(this).val();
					t[$(this).attr('key')] = val;
				})
				t['id_loan'] = $(this).attr('loan-id');
				loans.push(t);
			}else{
				deleted.push($(this).attr('loan-id'));
			}


		})
		$.ajax({
			type          :       'GET',
			url           :       '/sync-loan/post',
			data          :        {'id_member' : CURRENT_MEMBER , 'loans' : loans, 'deleted' : deleted},
			beforeSend    :        function(){
									$('.mandatory').removeClass('mandatory');
								   show_loader();
			},
			success       :        function(response){
								   console.log({response});
								   hide_loader()
								   if(response.RESPONSE_CODE == "SUCCESS"){
										Swal.fire({
											title: response.message,
											text: '',
											icon: 'success',
											showConfirmButton : false,
											timer : 2500
										});	
										getLoan(CURRENT_MEMBER)
										$(`tr.row-member[data-id="${CURRENT_MEMBER}"]`).find('.spn-check').remove();
										if(response.show_check){
											$(`tr.row-member[data-id="${CURRENT_MEMBER}"]`).find('td.td-member').append('<i class="fas fa-check-circle float-right spn-check"></i>');
										}
								   }else if(response.RESPONSE_CODE == "ERROR"){
										Swal.fire({
											title: response.message,
											text: '',
											icon: 'warning',
											showConfirmButton : false,
											timer : 2500
										});	
								   	$.each(response.invalid,function(index,inv){
								   		console.log({index,inv});
								   		for(var i=0;i < inv.length;i++){
								   			$('tr.loan-row').eq(index).find(`.in-loan[key="${inv[i]}"]`).addClass('mandatory')
								   		}
								   		
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
<?php $__env->stopPush(); ?>
<?php echo $__env->make('adminLTE.admin_template', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/loan-sync/index.blade.php ENDPATH**/ ?>