<style type="text/css">
	#tbl_attachment  tr>td{
		padding:0px ;
		vertical-align:top;
		font-family: Arial !important;
		font-size: 12px !important;
	}
	#tbl_attachment input:not([type='checkbox']),#tbl_attachment select {
		height: 25px !important;
		width: 100%;
	}

</style>
<div class="col-md-12">
	<div class="card c-border">
		<div class="card-body" style="padding-bottom: 0px !important;">
			<h5 class="lbl_color text-center mb-4"><?php echo e($alt_title); ?> Entry</h5>
			<div class="table-responsive" style="max-height: calc(100vh - 300px);overflow-y: auto;margin-top: 5px !important;overflow-x: auto">
				<table class="table table-bordered table-stripped table-head-fixed tbl_entry" style="white-space: nowrap;">
					<thead>
						<tr>
							<th class="table_header_dblue"  width="20px"></th>
							<th class="table_header_dblue" width="500px"><?php echo e($alt_title); ?> Account</th>
							<th class="table_header_dblue" width="250px">Amount</th>
							<th class="table_header_dblue">Details</th>
							<th class="table_header_dblue" width="30px"></th>
						</tr>
					</thead>
					<tbody id="entry_body">
						<?php
						$total_debit = 0;
						$total_credit = 0;
						?>
						<tr class="entry_row" id="row_paymode">
							<td class="td_counter">1</td>
							<td>
								<select class="form-control w_border p-0 select2 sel_account_code sel_chart col_entry_entry sel_entry_paymode" data-key="id_chart_account">
									<?php $__currentLoopData = $charts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chart): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<option value="<?php echo e($chart->id_chart_account); ?>" data-key="<?php echo e($chart->account_code); ?>"><?php echo e($chart->account_code); ?> - <?php echo e($chart->description); ?></option>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</select>
							</td>

							<td><input class="col_entry_entry form-control w_border class_amount txt_debit" value="" data-key="debit"></td>
							<td><input class="col_jv_entry form-control w_border txt_details" value="" data-key="remarks"></td>
							<td>
								<?php if($allow_post): ?>
								<a class="btn btn-xs bg-gradient-danger2 btn_delete_entry" onclick="remove_entry(this)"><i class="fa fa-trash"></i></a>
								<?php endif; ?>
							</td>
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<th class="footer_fix font-total" colspan="2" style="text-align:center;background: #808080;">T&nbsp;&nbsp;O&nbsp;&nbsp;T&nbsp;&nbsp;A&nbsp;&nbsp;L</th>
							<th class="footer_fix class_amount font-total" style="padding-right:12px;background: #808080;" id="td_tot_debit"><?php echo e(number_format($total_debit,2)); ?></th>
							<th class="footer_fix" colspan="2" style="background: #808080;"></th>
						</tr>
					</tfoot>
				</table>    
			</div> 
		</div>
		<?php if($allow_post): ?>
		<div class="card-footer">
			<button type="button" class="btn btn-xs bg-gradient-success2 col-md-12" onclick="add_entry()" style="margin-top:-15px"><i class="fa fa-plus"></i> Add <?php echo e($title_mod); ?></button>
		</div>
		<?php endif; ?>
	</div>
</div>
<?php echo $__env->make('cash_disbursement.attachment', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->startPush('scripts'); ?>

<script type="text/javascript">
	$(document).on('keyup','.txt_debit,#txt_amount',function(){
		set_entry_total()
	})
	$(document).on('change','.sel_account_code',function(e,wasTriggered){
		set_entry_total();
	})
	function set_entry_total(){
		var $credit = 0;
		var $debit = 0;

		$('tr.entry_row').each(function(){
			var parent_row = $(this);
			var id_chart_account = $(this).find('.sel_account_code').val();

			if(id_chart_account != null){
				var deb = parent_row.find('.txt_debit').val();
				deb = (deb=='')?0:deb;
				deb = (!$.isNumeric(deb))?decode_number_format(deb):parseFloat(deb);
				$debit+= deb;
			}
		})
		$debit = (isNaN($debit))?0:$debit;

		var in_amount = $('#txt_amount').val();
		if(in_amount == ""){
			in_amount = 0;
		}else{
			in_amount = ($.isNumeric(in_amount))?parseFloat(in_amount):decode_number_format(in_amount);
		}

		

		$('#td_tot_debit').text(number_format($debit,2));

		var output = {};
		output['debit'] = parseFloat($debit.toFixed(2));
		output['credit'] =  parseFloat(in_amount.toFixed(2));;


		if(output['debit'] != output['credit']){
			$('#td_tot_debit').addClass("not_balance_amount")
		}else{
			$('#td_tot_debit').removeClass("not_balance_amount")
		}
		console.log({output});

		return output;
	}
</script>


<?php if($allow_post): ?>
<script type="text/javascript">
	$('#frm_post_entry').submit(function(e){
		e.preventDefault();


		var validation_row = [];
		$('.mandatory').removeClass('mandatory')
		var in_amount = $('#txt_amount').val();
		if(in_amount == ""){
			in_amount = 0;
		}else{
			in_amount = ($.isNumeric(in_amount))?parseFloat(in_amount):decode_number_format(in_amount);
		}

		if(in_amount == 0){
			$('#txt_amount').addClass('mandatory')
			Swal.fire({
				title: "Please Enter Amount",
				text: '',
				icon: 'warning',
				showCancelButton : false,
				showConfirmButton : false,
				timer : 2500
			});
			return;
		}


		$cred = in_amount;
		$('tr.entry_row').each(function(){
			var parent_row = $(this);
			var id_chart_account = $(this).find('.sel_account_code').val();
			var temp = {};

			$deb = decode_number_format(parent_row.find('.txt_debit').val());
			console.log({$deb})
			

			if(($deb == 0) || (id_chart_account == null) || isNaN($deb)){
				validation_row.push(parent_row);
			}
		})
		if(validation_row.length > 0){
			for(var $i=0;$i<validation_row.length;$i++){
				$(validation_row[$i]).find('input,.select2-selection').addClass('mandatory');
			}
			Swal.fire({
				title: "Invalid Entry",
				text: '',
				icon: 'warning',
				showCancelButton : false,
				showConfirmButton : false,
				timer : 2500
			});
			return;
		}

		var totals = set_entry_total();

		if(totals['credit'] != totals['debit']){
			Swal.fire({
				title: "Total amount of "+'<?php echo e(strtolower($title_mod)); ?>'+' is not balance',
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
	function parseEntryRow(){
		var entry_account = [];
		$('tr.entry_row').each(function(){
			var temp ={};
			temp['id_chart_account'] = $(this).find('.sel_account_code').val();
			temp['debit'] = decode_number_format($(this).find('.txt_debit').val());
			temp['remarks'] = $(this).find('.txt_details').val();
			entry_account.push(temp);
		})

		return entry_account;
	}
<?php endif; ?>
</script>
<?php $__env->stopPush(); ?><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/cash_disbursement/form_table.blade.php ENDPATH**/ ?>