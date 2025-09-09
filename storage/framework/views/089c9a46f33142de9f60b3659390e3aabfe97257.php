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
</style>

<div class="main_form" style="margin-top: -20px;">
	<?php $back_link = (request()->get('href') == '')?'/repayment-statement':request()->get('href'); ?>
	<a class="btn btn-default btn-sm round_button" href="<?php echo e($back_link); ?>" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Loan Payment Statement List</a>

	<div class="card">
		<div class="card-body px-5">
			<div class="text-center mb-5">
				<h4 class="head_lbl">Loan Payment Statement</h4>
			</div>
			<form>
				<div class="row d-flex align-items-end mt-3">
					<div class="form-group col-md-4">
						<label class="lbl_color mb-0">Date</label>
						<input type="date" name="date" class="form-control form-control-border" value="<?php echo e($date); ?>" <?php echo ($opcode == 1)?'disabled':''; ?>>
					</div>
					<div class="form-group col-md-4">
						<label class="lbl_color mb-0">Barangay/LGU</label>
						<select class="form-control form-control-border p-0" name="br" <?php echo ($opcode == 1)?'disabled':''; ?> >
							<?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch_n=>$branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<optgroup label="<?php echo e($branch_n); ?>">
								<?php $__currentLoopData = $branch; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $br): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<option value="<?php echo e($br->id_baranggay_lgu); ?>" <?php echo ($br->id_baranggay_lgu == $selected_branch)?'selected':''; ?> ><?php echo e($br->name); ?></option>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</optgroup>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</select>
					</div>

					<?php if($opcode == 0): ?>
					<div class="form-group col-md-3">
						<button class="btn bg-gradient-primary2 btn-md"><i class="fa fa-search"></i>&nbsp;Generate Loan Dues</button>
					</div>
					<?php endif; ?>
				</div>
			</form>

		</div>

	</div>

	<div class="row">
		<div class="col-md-3">
			<div class="card">
				<div class="card-body">
					<input type="text" class="form-control" placeholder="Search Member" name="">
					<div class="form-group my-1 mt-2" id="div_books">
						<div class="form-check">
							<input class="form-check-input" id="chk-sel-member" type="checkbox">
							<label class="form-check-label" for="chk_cancel">Select all member</label>
						</div>						
					</div>
					<table class="table table-bordered tbl_pdc">
						<?php $__currentLoopData = $loans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id_member=>$loan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<?php
							$selected = ($opcode == 0 || ($opcode == 1 && in_array($id_member,$selected_member)))?'checked':'';
						?>
						<tr class="row-member" data-id="<?php echo e($id_member); ?>">
							<td class="text-center"><input type="checkbox" class="chk-member" <?php echo $selected;?>></td>
							<td><?php echo e($loan[0]->member); ?></td>
						</tr>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</table>
				</div>
			</div>
		</div>
		<div class="col-md-9">
			<div class="card">
				<div class="card-body">
					
					<div class="row">
						<div class="col-md-12">
							<?php
							$GLOBALS['memtotal'] = array();
							$GLOBALS['total'] = 0;
							?>
							<?php if(!$with_statement): ?>
							<p class="text-muted font-italic">Due date as of <?php echo e(date('m/d/Y',strtotime($date_due))); ?></p>
							<div class="table">
								<?php echo $__env->make('repayment-statement.statement_table', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
							</div>

							<?php else: ?>
							<div class="alert alert-warning">
								<strong>
									<?php if($branch_details->type == 1): ?>Barangay <?php echo e($branch_details->name); ?> <?php else: ?> <?php echo e($branch_details->name); ?> LGU <?php endif; ?></strong> (Due date : <?php echo e(date('m/d/Y',strtotime($date_due))); ?>) has already statement. <a href="/repayment-statement/view/<?php echo e($statement_details->id_repayment_statement); ?>">[Statement No. <?php echo e($statement_details->month_year); ?>-<?php echo e($statement_details->id_repayment_statement); ?>]</a>
									<br>   
								</div>
								<?php endif; ?>
							</div>
							<?php if(!$with_statement): ?>
							<div class="col-md-12">
								<h6 class="lbl_color float-right">Statement Total: <span class="font-weight-bold ml-3" id="txt-total-payment"><?php echo e(number_format($GLOBALS['total'],2)); ?></span></h6>
							</div>
							<?php endif; ?>
						</div>
					</div>
					<?php if(!$with_statement): ?>
					<div class="card-footer py-2">
						<button class="btn round_button bg-gradient-success2 float-right" onclick="post()"><i class="fa fa-save"></i>&nbsp;Generate Statement</button>
					</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
	<?php $__env->stopSection(); ?>


	<?php $__env->startPush('scripts'); ?>
	<script type="text/javascript">
		const BACK_LINK = `<?php echo $back_link; ?>`;
		const OPCODE = '<?php echo e($opcode); ?>';
		const ID_BARANGGAY_LGU = <?php echo e($selected_branch); ?>;
		const DATE = '<?php echo e($date); ?>';
		const ID_REPAYMENT_STATEMENT = <?php echo e($details->id_repayment_statement ?? 0); ?>;

		const MEMBTOTAL = jQuery.parseJSON(`<?php echo json_encode($GLOBALS['memtotal'] ?? 0); ?>`);
		
		$(document).on('click','.chk-member',function(){
			var checked = $(this).prop('checked');
			var member_id = $(this).closest('.row-member').attr('data-id');
			console.log({checked,member_id});
			var MEMBERBODY = $(`tbody.bmember[data-member-id="${member_id}"]`);
			if(checked){
				$(MEMBERBODY).removeClass('hidden-member');
			}else{
				$(MEMBERBODY).addClass('hidden-member');
			}
			ComputeSelected();
		});

		const ComputeSelected = ()=>{
			var total = 0;
			$('.chk-member:checked').each(function(){
				var id_member = $(this).closest('.row-member').attr('data-id');
				total += MEMBTOTAL[id_member];
				console.log({total});
			});
			$('#gtotal').text(number_format(total,2));
			ComputeAll();
		}

		$(document).ready(function(){
			GenerateLoanBody();
		});

		const GenerateLoanBody = ()=>{
			$('.chk-member').each(function(){
				var id_member = $(this).closest('.row-member').attr('data-id');
				var MEMBERBODY = $(`tbody.bmember[data-member-id="${id_member}"]`);
				var checked = $(this).prop('checked');

				if(checked){
					$(MEMBERBODY).removeClass('hidden-member');
					
				}else{
					$(MEMBERBODY).addClass('hidden-member');
				}
				
			});				
			ComputeSelected();
		}

		$('#chk-sel-member').click(function(){
			var checked = $(this).prop('checked');
			$('.chk-member').prop('checked',checked);
			GenerateLoanBody();	
		})

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
					PostStatement();
				}
			});
		}
		const PostStatement = ()=>{
			let DataLoan = [];

			let SelectedMembers = [];

			$('.chk-member:checked').each(function(){
				var id_member = $(this).closest('.row-member').attr('data-id');
				SelectedMembers.push(id_member);
			});			


			let LoanAmounts = {};

			$('tr.rloan').each(function(){
				var idLoan = $(this).attr('data-id');
				LoanAmounts[idLoan] = decode_number_format($(this).find('.in-loan-due').val());
			});

			let AjaxParam = {
				'loans' : DataLoan,
				'date' : DATE, 
				'id_barangay_lgu' : ID_BARANGGAY_LGU,
				'opcode' : OPCODE,
				'members' : SelectedMembers,
				'LoanAmounts' : LoanAmounts,
				'id_repayment_statement' : ID_REPAYMENT_STATEMENT
			};

			$.ajax({
				type            :       'GET',
				url             :       '/repayment-statement/post',
				data            :       AjaxParam,
				beforeSend      :       function(){
					show_loader();
				},
				success         :       function(response){
					console.log({response});
					hide_loader();
					if(response.RESPONSE_CODE == "SUCCESS"){
						Swal.fire({
							title: response.message,
							icon: 'success',
							showCancelButton: false,
							showConfirmButton : false,
							cancelButtonText : 'Back to Loan Payment Statement list',
							confirmButtonText: `Close`,
							allowOutsideClick: false,
							allowEscapeKey: false,
							timer : 3000
						}).then((result) => {
							window.localStorage.setItem('for_print',response.ID_REPAYMENT_STATEMENT);
							window.location ='/repayment-statement/view/'+response.ID_REPAYMENT_STATEMENT+"?href="+encodeURIComponent(BACK_LINK);
						})
					}else if(response.RESPONSE_CODE == "ERROR"){
						Swal.fire({
							title: response.message,
							text: response.message2,
							icon: 'warning',
							cancelButtonText : 'Close',
							showCancelButton : true,
							showConfirmButton : false,
							timer :5000
						});	

								
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
	<script type="text/javascript">

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
		});
		$(document).on('change','#sel-paymode',function(){
			init_paymode();
		})

		$(document).on('keyup','.in-loan-due',function(){
			ComputeAll();

		});
		const ComputeAll=()=>{
			let TotalPayment = 0;
			$('.in-loan-due:visible').each(function(){
				var p = $(this).val();
				let payment = ($.isNumeric(p))?roundoff(p):decode_number_format(p);
				TotalPayment += payment;

				console.log({payment});
			});
			$('#txt-total-payment').text(number_format(TotalPayment));
		}
	</script>
	<?php $__env->stopPush(); ?>
<?php echo $__env->make('adminLTE.admin_template', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/repayment-statement/form.blade.php ENDPATH**/ ?>