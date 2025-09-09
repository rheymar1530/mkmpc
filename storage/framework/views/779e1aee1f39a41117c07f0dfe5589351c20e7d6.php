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
	.tbl_jv tr>th {
		padding: 5px;
		vertical-align: top;
		font-size: 14px;
	}

	.tbl_jv tr.jv_row>td{
		padding: 0px !important;

	}
	.tbl_jv tr.jv_row_display>td{
/*		padding: 3px !important;*/
		padding: 5px;
		font-weight: bold;
		font-size: 15px;

	}
	.tbl_jv input:not([type='checkbox']),.tbl_jv select {
		height: 25px !important;
		width: 100%;

	}	
	.table_header_dblue{
		text-align: center;
	}
	.btn_delete_jv{
		width: 100%;
		height: 23px !important;
		padding: 0px 2px 0px 2px !important;
	}
	.tbl_jv .select2-selection {
		height: 25px !important;
	}

	.select2-container--default .select2-selection--single {
		padding: unset;
	}
	.td_counter{
		font-weight: bold;
		text-align: center;
	}
	.select2-selection__rendered {
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 13px;
	}
	.class_amount{
		text-align: right;

	}
	.col_jv_entry{
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 13px;
	}



	.form-row  label{
		margin-bottom: unset !important;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 15px;
	}
	.form-label{
		margin-bottom: 4px !important;
	}


	.text_center{
		text-align: center;
	}
	.text_bold{
		font-weight: bold;
	}

/*	.wrapper2{
		width: 1300px !important;
		margin: 0 auto;
	}*/
	.text-red{
		color: red;
	}

.select2-results__options {
        max-height: 250px; //This can be any height you want
        overflow:scroll;
    }
/*	.select-dropdown {
		position: static !important;
	}
	.select-dropdown .select-dropdown--above {
		margin-top: 336px !important;
	}*/
.select2-chosen {
    background: #FFF;
    border-radius: 7px;
    margin-right: 0 !important;
}
.select2-drop.select2-drop-above {
	z-index: -1;
}
span {
  transition: all 0.25s;
  -moz-transition: all 0.25s;
  /* Firefox 4 */
  -webkit-transition: all 0.25s;
  -o-transition: all 0.25s;
  /* Opera */
}
/* remove transition for select2 span to stop scroll-to-top behaviour on click */
span.select2-container {
  transition: none;
  -moz-transition: none;
  -webkit-transition: none;
  -o-transition: none;
}
.footer_fix {
	padding: 3px !important;
	background-color: #fff;
	border-bottom: 0;
	box-shadow: inset 0 1px 0 #dee2e6, inset 0 -1px 0 #dee2e6;
	position: -webkit-sticky;
	position: sticky;
	bottom: 0;
	z-index: 10;
}
.col-form-label{
	font-size: 14px !important;
}
.font-total{
	font-size: 17px !important;
}
  .select2-dropdown--below {
    width: 170%;
  }
</style>
<?php
	$payee_type = [
		1=>"Supplier",
		2=>"Members",
		3=>"Employee",
		4=>"Others"
	];
	$jv_type = [
		1=>'Normal',
		3=>'Adjustment',
		2=>'Reversal'
	];
?>

	<div class="container-fluid main_form section_body" id="jv_main_div" style="margin-top: -20px;" >
		<?php $back_link = (request()->get('href') == '')?'/journal_voucher':request()->get('href'); ?>
		<a class="btn bg-gradient-secondary btn-sm" href="<?php echo e($back_link); ?>" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Journal Voucher List</a>

		<div class="card" id="repayment_main_div">
			<form id="frm_post_jv">
			<div class="card-body col-md-12">
				<h3 class="text-center head_lbl">Journal Voucher 
					<?php if($opcode ==1): ?>
						(ID# <?php echo e($jv_details->id_journal_voucher); ?>)
						<?php if($jv_details->status == 10): ?>
						<span class="badge badge-danger bgd_cancel">Cancelled</span>
						<?php endif; ?>
					<?php endif; ?>
				</h3>
				<div class="row mt-4">
					<div class="col-md-12 row">	
						<div class="col-md-12" style="margin-top:15px">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group row p-0" >
										<label for="sel_jv_type" class="col-sm-2 control-label col-form-label" style="text-align: left">JV Type&nbsp;</label>
										<div class="col-sm-9">
											<select class="form-control form_input p-0 jv_parent" id="sel_jv_type" key="jv_type">
												<?php $__currentLoopData = $jv_type; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val=>$desc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<?php
														$selected = (isset($jv_details->jv_type)?$jv_details->jv_type:1);
													?>
												<option value="<?php echo e($val); ?>" <?php echo ($selected == $val)?'selected':'';?> ><?php echo e($desc); ?></option>
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											</select>
										</div>
									</div>
									<div id="id_jv_refernce_holder"></div>

									<div class="form-group row p-0" id="div_reference_jv">
										<label for="sel_jv_reference" class="col-sm-2 control-label col-form-label" style="text-align: left">Ref #&nbsp;</label>
										<div class="col-sm-9">
											<select class="form-control form_input p-0 jv_parent" id="sel_jv_reference" key="id_adj_reference" required>
												<?php if(isset($selected_reference)): ?>
													<option value="<?php echo e($selected_reference->tag_id); ?>"><?php echo e($selected_reference->tag_value); ?></option>
												<?php endif; ?>
											</select>
										</div>
									</div>
									<div class="form-group row p-0" >
										<label for="txt_transaction_date" class="col-sm-2 control-label col-form-label" style="text-align: left">Date&nbsp;</label>
										<div class="col-sm-9">
											<input type="date" name="" class="form-control jv_parent" value="<?php echo e($jv_details->date ?? $current_date); ?>" id="txt_transaction_date" key="date">
										</div>
									</div>
									<div class="form-group row p-0" >
										<label for="sel_payee_type" class="col-sm-2 control-label col-form-label" style="text-align: left">Payee Type&nbsp;</label>
										<div class="col-sm-9">
											<select class="form-control form_input p-0 jv_parent" id="sel_payee_type" key="payee_type" required>
												<?php $__currentLoopData = $payee_type; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val=>$desc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
												<option value="<?php echo e($val); ?>"><?php echo e($desc); ?></option>
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											</select>
										</div> 
									</div>
									<div id="id_payee_holder">
										
									</div>
									<div class="form-group row p-0" id="div_sel_reference">
										<label for="sel_payee_type" class="col-sm-2 control-label col-form-label" style="text-align: left">&nbsp;</label>
										<div class="col-sm-9">
											<select class="form-control form_input p-0 jv_parent" id="sel_reference" key="payee_reference" required>
												<?php if(isset($selected_reference_payee)): ?>
												<option value="<?php echo e($selected_reference_payee->id); ?>"><?php echo e($selected_reference_payee->name); ?></option>
												<?php endif; ?>
											</select>
										</div> 
									</div>							
									<div class="form-group row p-0" id="div_payee_others" >
										<label for="txt_payee_others" class="col-sm-2 control-label col-form-label" style="text-align: left">&nbsp;</label>
										<div class="col-sm-9">
											<input type="text" name="" class="form-control jv_parent" value="<?php echo e($payee ?? ''); ?>" id="txt_payee_others" key="payee" required>
										</div>
									</div>	
									<div class="form-group row p-0">
										<label for="txt_address" class="col-sm-2 control-label col-form-label" style="text-align: left">Address&nbsp;</label>
										<div class="col-sm-9">
											<input type="text" name="" class="form-control jv_parent" id="txt_address" key="address" value="<?php echo e($jv_details->address ?? ''); ?>">
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group row p-0" >
										<label for="sel_branch" class="col-sm-2 control-label col-form-label" style="text-align: left">Branch&nbsp;</label>
										<div class="col-sm-9">
											<select class="form-control form_input p-0 jv_parent" id="sel_branch" key="id_branch" required>
												<?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $branch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

												<option value="<?php echo e($branch->id_branch); ?>" <?php echo (isset($jv_details->id_branch)  && $jv_details->id_branch == $branch->id_branch)?'selected':''; ?> ><?php echo e($branch->branch_name); ?></option>
												<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
											</select>
										</div> 
									</div>
									<div class="form-group row p-0">
										<label for="txt_reference" class="col-sm-2 control-label col-form-label" style="text-align: left">Reference&nbsp;</label>
										<div class="col-sm-9">
											<input type="text" name="" class="form-control jv_parent" id="txt_reference" key="reference" value="<?php echo e($jv_details->reference ?? ''); ?>">
										</div>
									</div>
									<div class="form-row">
										<div class="form-group col-md-12">
											<label for="txt_description">Description</label>
											<textarea class="form-control jv_parent" rows="3" style="resize:none;" id="txt_description" key="description" required><?php echo e($jv_details->description ?? ''); ?></textarea>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-12">
							<!-- <div class="card" style="margin-top:10px;" id="div_jv"> -->
								<!-- <div class="card-header bg-gradient-primary custom_card_header">
									<h5>Entries</h5>
								</div> -->
								<!-- <div class="card-body card_dimension_body"> -->
									<div class="table-responsive" style="max-height: calc(100vh - 300px);overflow-y: auto;margin-top: 5px !important;overflow-x: auto">
										<table class="table table-bordered table-stripped table-head-fixed tbl_jv" style="white-space: nowrap;">
											<thead>
												<tr>
													<th class="table_header_dblue"  width="20px"></th>
													<th class="table_header_dblue" width="100px">Account Code</th>
													<th class="table_header_dblue" width="350px">Description</th>
													<th class="table_header_dblue" width="150px">Debit</th>
													<th class="table_header_dblue" width="150px">Credit</th>
													<th class="table_header_dblue">Details</th>

													<th class="table_header_dblue" width="30px"></th>
												</tr>
											</thead>
											<tbody id="jv_body">
												<?php
													$total_debit = 0;
													$total_credit = 0;
												?>
												<?php if($allow_post): ?>
												<tr class="jv_row">
													<td class="td_counter">1</td>
													<td>
														<select class="form-control p-0 select2 sel_account_code sel_chart col_jv_entry w_border" id="def-sel-code">
															<?php $__currentLoopData = $charts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chart): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<option value="<?php echo e($chart->id_chart_account); ?>" data-key="<?php echo e($chart->account_code); ?>"><?php echo e($chart->account_code); ?> - <?php echo e($chart->description); ?></option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
														</select>
													</td>
													<td>
														<select class="form-control p-0 select2 sel_account_description sel_chart col_jv_entry w_border" id="def-sel-desc" >
															<?php $__currentLoopData = $charts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chart): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
															<option value="<?php echo e($chart->id_chart_account); ?>"><?php echo e($chart->account_code); ?> - <?php echo e($chart->description); ?></option>
															<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
														</select>
													</td>
													<td><input class="col_jv_entry form-control w_border class_amount txt_debit" value=""></td>
													<td><input class="col_jv_entry form-control w_border class_amount txt_credit" value=""></td>
													<td><input class="col_jv_entry form-control w_border txt_details" value=""></td>
													<td><a class="btn btn-xs bg-gradient-danger btn_delete_jv" onclick="remove_jv(this)"><i class="fa fa-trash"></i></a></td>
												</tr>

												<?php else: ?>
													<?php $__currentLoopData = $chart_details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c=>$cd): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
													<tr class="jv_row_display">
														<td><?php echo e($c+1); ?></td>
														<td><?php echo e($cd->account_code); ?></td>
														<td><?php echo e($cd->description); ?></td>
														<td class="class_amount" style="padding-right:10px !important"><?php echo e(($cd->debit==0)?'':number_format($cd->debit,2)); ?></td>
														<td class="class_amount" style="padding-right:10px !important"><?php echo e(($cd->credit==0)?'':number_format($cd->credit,2)); ?></td>
														<td><?php echo e($cd->details); ?></td>
														<td></td>
													</tr>
													<?php
														$total_debit += $cd->debit;
														$total_credit += $cd->credit;
													?>
													<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
												<?php endif; ?>
											</tbody>
											<tfoot>
												<tr>
													<th class="footer_fix font-total" colspan="3" style="text-align:center;background: #808080;color: white;">T&nbsp;&nbsp;O&nbsp;&nbsp;T&nbsp;&nbsp;A&nbsp;&nbsp;L</th>
													<th class="footer_fix class_amount font-total" style="padding-right:12px;background: #808080;color: white;" id="td_tot_debit"><?php echo e(number_format($total_debit,2)); ?></th>
													<th class="footer_fix class_amount font-total" style="padding-right:12px;background: #808080;color: white;" id="td_tot_credit"><?php echo e(number_format($total_credit,2)); ?></th>
													<th class="footer_fix" colspan="2" style="background: #808080;color: white;"></th>
												</tr>
											</tfoot>
										</table>    
									</div> 


								<!-- </div> -->

								<!-- <div class="card-footer custom_card_footer"> -->
									<?php if($allow_post): ?>

									<button type="button" class="btn btn-xs bg-gradient-primary2 col-md-12" onclick="add_jv()"><i class="fa fa-plus"></i> Add Item</button>
									<?php endif; ?>
								<!-- </div> -->
							</div>
												<?php if($opcode == 1): ?>
							<div class="mt-3 ml-2">
								<span class="text-muted spn_details"><b>Date Created:</b> <?php echo e($jv_details->date_created); ?></span><br>
								<?php if($jv_details->status == 10): ?>
								<span class="text-muted spn_jv_details"><b>Date Cancelled:</b> <?php echo e($jv_details->date_cancelled); ?></span><br>
								<span class="text-muted spn_jv_details"><b>Cancellation Reason:</b> <?php echo e($jv_details->cancellation_reason); ?></span>
								<?php endif; ?>
							</div>
					<?php endif; ?>
						<!-- </div> -->
					</div>	

				</div>
			</div>
			<div class="card-footer">
				<?php if($allow_post): ?>
					<button class="btn bg-gradient-success2 float-right" id="post_jv">Post JV</button>
					<?php if($opcode == 1 && $credential->is_cancel): ?>
						<button type="button" class="btn bg-gradient-warning2 float-right mr-2" onclick="$('#cancel_jv_modal').modal('show')"><i class="fa fa-times"></i> Cancel JV</button>
					<?php endif; ?>
					
				<?php endif; ?>

				<?php if($opcode == 1): ?>
				<button type="button" class="btn bg-gradient-danger2 float-right" style="margin-right:10px" onclick="print_page('/journal_voucher/print/<?php echo e($jv_details->id_journal_voucher); ?>')"><i class="fas fa-print" ></i>&nbsp;Print Journal Voucher</button>
				<?php endif; ?>
				<?php if($opcode == 1 && $allow_post): ?>
				<button type="button" class="btn bg-gradient-primary2 float-right mr-2" onclick="redirect_scheduler()"><i class="fa fa-calendar"></i>&nbsp;Add to scheduler</button>
				<?php endif; ?>
			</div>	

			</form>
		</div>

	</div>

<?php echo $__env->make('global.print_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php if($opcode == 1 && $credential->is_cancel): ?>
<?php echo $__env->make('journal_voucher.cancellation_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?>


<?php $__env->stopSection(); ?>




<?php $__env->startPush('scripts'); ?>
<?php if($opcode == 1): ?>
<script type="text/javascript">
	function redirect_scheduler(){
		<?php if($jv_details->id_scheduler > 0): ?>
			window.open('/scheduler/view/'+<?php echo e($jv_details->id_scheduler); ?>,'_blank');
		<?php else: ?>
			var p = {
				type : 1,
				reference : <?php echo e($jv_details->id_journal_voucher ?? 0); ?>

			};
			var encodedObject = encodeURIComponent(JSON.stringify(p));
			window.open('/scheduler/create?s_data='+encodedObject,'_blank');
		<?php endif; ?>
	}
</script>
<?php endif; ?>
<script type="text/javascript">
	const jv_row_html = '<tr class="jv_row">'+$('tr.jv_row').html()+'</tr>';
	var $div_sel_reference = $('#div_sel_reference').detach();
	var $div_payee_others = $('#div_payee_others').detach();
	var $div_reference_jv = $('#div_reference_jv').detach();
	$('#sel_payee_type').val('<?php echo $jv_details->payee_type ?? 0 ?>');
	$('tr.jv_row').remove();



	const chart = <?php echo json_encode($charts) ?>;
	const OPCODE = <?php echo $opcode;?>;
	var chart_option = {};

	$(document).on('change','#sel_payee_type',function(){
		initialize_payee_type(true,null);
	})
	$(document).on('change','#sel_jv_type',function(){
		initialize_jv_type();
	})


	function initialize_jv_type(){
		var val = $('#sel_jv_type').val();

		if(val == 1 || val ==3){
			$('#id_jv_refernce_holder').html('');
		}else{
			$('#sel_jv_reference').val(0).trigger("change");
			$('#id_jv_refernce_holder').html($div_reference_jv);
			intialize_select2_jv_reference()
		}
		animate_element($('#id_jv_refernce_holder'),1);
		
	}
	function initialize_payee_type(reset_reference,val){
		var payee_type = $('#sel_payee_type').val();
		$('#id_payee_holder').html('')
		if(payee_type <= 3){

			
			$('#id_payee_holder').html($div_sel_reference);
			if(reset_reference){
				$('#sel_reference').val(0).trigger("change");


			}
			if(val != null){
				$('#sel_reference').html(val);
			}
			intialize_select2(payee_type)
		}else{

			$('#id_payee_holder').html($div_payee_others);

			$('#txt_payee_others').val(val);
		}
		animate_element($('#id_payee_holder'),1)
	}

	$.each(chart,function(i,item){
		var temp = {};
		temp['id_chart_account'] = item.id_chart_account;
		temp['account_code'] = item.account_code;
		temp['description'] = item.description;

		chart_option[item.id_chart_account] = temp;
	})
	// $(document).on('select2:opening', (e) => {
	// 	const selectId = e.target.id
	// 	if($(e.target).hasClass('sel_account_code')){	
	// 		$('#select2-'+selectId+'-results').closest('span.select2-dropdown--below').css({'width' : '1000px !important','color':'red'});
	// 		console.log("-----------------------")
	// 	}
	// }) 

	$(document).on('select2:open', (e) => {
		const selectId = e.target.id
		if($(e.target).hasClass('sel_account_code')){	
			// $('#select2-'+selectId+'-results').closest('span.select2-dropdown').css({'color':'red','width' : '1000px !important'});
		}
		
		$(".select2-search__field[aria-controls='select2-" + selectId + "-results']").each(function(key,value,){
			value.focus()
		})

		// $(this).
		// 
		
	}) 
	$(document).ready(function(){
		for(var $i=0;$i<2;$i++){
			add_jv();
			// $('.btn_delete_jv').last().remove();
		}
		
	})
	function add_jv(){
		$('#jv_body').append(jv_row_html);
		$('tr.jv_row').last().hide().show(300);
		var id_acc_code =makeid(8);
		var id_account_name = makeid(8);
		$('tr.jv_row').last().find('select.sel_account_code').attr('id',id_acc_code);
		$('tr.jv_row').last().find('select.sel_account_description').attr('id',id_account_name);

		$('#'+id_acc_code).val(0);
		$('#'+id_account_name).val(0);

		$('#'+id_acc_code).select2({dropdownAutoWidth :true,dropdownPosition: 'below'});
		$('#'+id_account_name).select2({width : '355px',dropdownAutoWidth :true,dropdownPosition: 'below'});
		set_counter_table()
	}
	function makeid(length){
		var result           = '';
		var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		var charactersLength = characters.length;
		for(var i = 0; i < length; i++ ) {
			result += characters.charAt(Math.floor(Math.random() * charactersLength));
		}
		return result;
	}

	$(document).on('change','.sel_account_code',function(e,wasTriggered){
		if(!wasTriggered){
			fill_description_v_code($(this));
		}
		var id = $(this).attr("id");
		var val = parseInt($(this).val());
		$('span#select2-'+id+'-container').text(chart_option[val]['account_code'])
	})
	$(document).on('change','.sel_account_description',function(e,wasTriggered){
		if(!wasTriggered){
			fill_code_v_description($(this));
		}
		var id = $(this).attr("id");
		var val = parseInt($(this).val());
		$('span#select2-'+id+'-container').text(chart_option[val]['description'])
	})
	function fill_description_v_code(obj){

		var parent_row = $(obj).closest('tr.jv_row');
		var val = $(obj).val();
		parent_row.find('.sel_account_description').val(val).trigger('change',true)
		set_entry_total()
		
	}
	function fill_code_v_description(obj){

		var parent_row = $(obj).closest('tr.jv_row');
		var val = $(obj).val();
		parent_row.find('.sel_account_code').val(val).trigger('change',true)

		set_entry_total();
		
	}
	function remove_jv(obj){
		var parent_row = $(obj).closest('tr.jv_row');
		parent_row.fadeOut(300, function(){ 
			$(this).remove();
			set_counter_table();
			set_entry_total();

		});

	}
	function set_counter_table(){
		$('td.td_counter').each(function(i){
			$(this).text(i+1);
		})
	}
	$(document).on('keyup','.txt_credit,.txt_debit',function(){
		set_entry_total()
	})
	function set_entry_total(){
		var $credit = 0;
		var $debit = 0;

		$('tr.jv_row').each(function(){
			var parent_row = $(this);
			var id_chart_account = $(this).find('.sel_account_code').val();

			if(id_chart_account != null){
				var deb = parent_row.find('.txt_debit').val();
				deb = (deb=='')?0:deb;
				deb = (!$.isNumeric(deb))?decode_number_format(deb):parseFloat(deb);
				$debit+= deb;

				var cred = parent_row.find('.txt_credit').val();
				cred = (cred=='')?0:cred;
				cred = (!$.isNumeric(cred))?decode_number_format(cred):parseFloat(cred);
				$credit+= cred;
			}
			
		})
		$debit = (isNaN($debit))?0:$debit;
		$credit = (isNaN($credit))?0:$credit;
		$('#td_tot_debit').text(number_format($debit,2));
		$('#td_tot_credit').text(number_format($credit,2));

		var output = {};


		output['debit'] = parseFloat($debit.toFixed(2));
		output['credit'] = parseFloat($credit.toFixed(2));;
		console.log({$debit,$credit});

		return output;
	}
	$(document).on("focus",".class_amount",function(){
		var val = $(this).val();
		if(val == '' || val == 'NaN'){
			val = '0.00';

			$(this).val('');

			return; 
		}
		$(this).val(decode_number_format(val)); 
	})
	$(document).on("blur",".class_amount",function(){
		var val = $(this).val();
		if(!$.isNumeric(val)){
			val = 0;
			$(this).val('');

			return;
		}
		$(this).val(number_format(parseFloat(val)));
	})
	function intialize_select2(type){		
		var $link = '';
		if(type == 1){
			$link = '/search_supplier';
		}else if(type == 2){
			$link = '/search_member'
		}else if(type == 3){
			$link = '/search_employee';
		}

		$("#sel_reference").select2({
			minimumInputLength: 2,
			width: '100%',
			createTag: function (params) {
				return null;
			},
			ajax: {
				tags: true,
				url: $link,
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

	function intialize_select2_jv_reference(){		


		$("#sel_jv_reference").select2({
			minimumInputLength: 2,
			width: '100%',
			createTag: function (params) {
				return null;
			},
			ajax: {
				tags: true,
				url: '/search_jv_reference',
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

	$(document).on('change','#sel_jv_reference',function(){
		var val = $(this).val();
		$.ajax({
			type       :        'GET',
			url        :        '/journal_voucher/reversal/content',
			data       :        {'id_reference' : val},
			beforeSend :        function(){
													show_loader();
			},
			success    :        function(response){
													hide_loader();
													console.log({response})
													$('#sel_payee_type').val(response.adj_details.payee_type)
													$('#sel_branch').val(response.adj_details.id_branch);

													var val = '';

													if(response.selected_reference_payee != null){
														var id_payee = response.selected_reference_payee.id;
														var payee_name = response.selected_reference_payee.name;	
														val = '<option value="'+id_payee+'">'+payee_name+'</option>';								
													}else{
														val = response.payee;
													}

													$('#txt_address').val(response.adj_details.address)


												
													initialize_payee_type(true,val)
													
													
													if(response.entries.length > 0){
														$('#jv_body').html('');
														$.each(response.entries,function(i,item){
															add_jv();
															$('.sel_account_code,sel_account_description').last().val(item.id_chart_account).trigger('change')
															$('.txt_debit').last().val((parseFloat(item.debit) > 0)?number_format(parseFloat(item.debit)):"")
															$('.txt_credit').last().val((parseFloat(item.credit) > 0)?number_format(parseFloat(item.credit)):"")
														})
														set_entry_total()
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
	})
</script>



<script type="text/javascript">



	$(document).on('change','#sel_reference',function(){
		parseAddress();
		console.log("QWe")
	})
	function parseAddress(){

		if($('#sel_reference').val() == null){
			return;
		}
	
		$.ajax({
			type          :           'GET',
			url           :           '/journal_voucher/parse_address',
			data          :           {'reference'   :    $('#sel_reference').val(),
									   'type'        :    $('#sel_payee_type').val()},
			success       :           function(response){
									  console.log({response});
									  $('#txt_address').val(response.response.address ?? '');
			},error: function(xhr, status, error) {
  
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
	$(document).on('change','#sel_jv_reference',function(){
		var val = $(this).val();

		if(val != null){
			val = val.split("-");
			var text = '';

			if(val[0] == 'cdv'){
				text = "ADJUSTMENT FOR CDV #";
			}else if(val[0] == 'jv'){
				text =  'ADJUSTMENT/REVERSAL FOR JV#';
			}
			text+=val[1];
			$('#txt_description').val(text);
		}
	})	
	$('#frm_post_jv').submit(function(e){
		e.preventDefault();

		var validation_row = [];
		$('.mandatory').removeClass('mandatory')
		$('tr.jv_row').each(function(){
			var parent_row = $(this);
			var id_chart_account = $(this).find('.sel_account_code').val();
			var temp = {};

			$deb = decode_number_format(parent_row.find('.txt_debit').val());
			$cred = decode_number_format(parent_row.find('.txt_credit').val());

			$deb = isNaN($deb)?0:$deb;
			$cred = isNaN($cred)?0:$cred;

			console.log({$deb,$cred})



			if(($deb == 0 && $cred == 0) || (id_chart_account == null) || ($deb > 0 && $cred > 0)){
				validation_row.push(parent_row);
			}
		})
		if(validation_row.length > 0 || $('tr.jv_row').length < 2){
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
		if(totals['credit'] == 0 || totals['debit'] == 0){
			Swal.fire({
    			title: "Invalid Amount",
    			text: '',
    			icon: 'warning',
    			showCancelButton : false,
    			showConfirmButton : false,
    			timer : 2500
    		});

    		return;
		}

		if(totals['credit'] != totals['debit']){
			Swal.fire({
    			title: "Entry Not Balance",
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
		var jv_parent = {};
		var chart_entry = [];
		$('.jv_parent').each(function(){
			var key = $(this).attr('key')
			var val = $(this).val();
			jv_parent[key] = val;
		});

		$('tr.jv_row').each(function(){
			var parent_row = $(this);
			var id_chart_account = $(this).find('.sel_account_code').val();
			var temp = {};

			if(id_chart_account != null){
				temp['id_chart_account'] = id_chart_account;
				$deb = decode_number_format(parent_row.find('.txt_debit').val());
				$cred = decode_number_format(parent_row.find('.txt_credit').val());
				temp['debit'] = (isNaN($deb))?0:$deb;
				temp['credit'] =  (isNaN($cred))?0:$cred;
				temp['details'] = parent_row.find('.txt_details').val();
				chart_entry.push(temp);
			}
		})
		$.ajax({

			type         :          "POST",
			url          :          "/journal_voucher/post",
			data         :          {'jv_parent'  : jv_parent,
									             'chart_entry' : chart_entry,
									           	 'opcode'   :   OPCODE,
									           	  'id_journal_voucher' : <?php echo e($jv_details->id_journal_voucher ?? 0); ?>},
			beforeSend   :          function(){
									show_loader();
			},
			success      :          function(response){
				console.log({response});
				hide_loader();
				if(response.RESPONSE_CODE == "ERROR"){
					Swal.fire({
		    			title: response.message,
		    			text: '',
		    			icon: 'warning',
		    			showCancelButton : false,
		    			showConfirmButton : false,
		    			timer : 2500
		    		});
				}else if(response.RESPONSE_CODE == "SUCCESS"){
    				var link = "/journal_voucher/view/"+response.id_journal_voucher+"?href="+encodeURIComponent('<?php echo $back_link;?>');
    				Swal.fire({

    					title: "JV Successfully Saved !",
    					html : "<a href='"+link+"'>JV ID# "+response.id_journal_voucher+"</a>",
    					text: '',
    					icon: 'success',
    					showCancelButton : true,
    					confirmButtonText: 'Create More JV',
    					showDenyButton: true,
    					denyButtonText: `Print JV`,
    					cancelButtonText: 'Close',
    					showConfirmButton : true,     
    					allowEscapeKey : false,
    					allowOutsideClick: false
    				}).then((result) => {
    					if(result.isConfirmed) {
    						window.location = "/journal_voucher/create?href="+encodeURIComponent('<?php echo $back_link;?>');
    					}else if (result.isDenied) {
    						var redirect_data = {
    							'show_print_jv' : 1,
    							'id_journal_voucher' : response.id_journal_voucher
    						}
    						localStorage.setItem("redirect_print_jv",JSON.stringify(redirect_data));
    						window.location = 	link;
    					}else{
    						window.location = '<?php echo $back_link;?>';
    					}
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


		console.log({jv_parent,chart_entry})
	}
</script>

<?php if($opcode == 1): ?>
<script type="text/javascript">
	<?php
		if(isset($payee)){
			echo "initialize_payee_type(false,'".$payee."'); ";
		}else{
			echo "initialize_payee_type(false,null); ";
		}
	?>
	
	initialize_jv_type();

		$(document).ready(function(){
			var redirect_data = jQuery.parseJSON(localStorage.getItem("redirect_print_jv"));
			console.log({redirect_data});
			if(redirect_data != null){
				if(redirect_data.show_print_jv == 1){
					if(redirect_data.id_journal_voucher == '<?php echo $jv_details->id_journal_voucher; ?>'){
						print_page("/journal_voucher/print/"+redirect_data.id_journal_voucher)
						console.log("SHOW PRINT MODAL")
						localStorage.removeItem("redirect_print_jv");
					}
				}
			}

			// var localStorage.setItem("show_print_waiver", 1);
		})
</script>

<?php if($allow_post): ?>
<script type="text/javascript">
	  const entries = jQuery.parseJSON('<?php echo json_encode($chart_details ?? [])?>');

		$(document).ready(function(){
			$('#jv_body').html('')
			$.each(entries,function(i,item){
				add_jv()	
				// alert(item.id_chart_account);
				$('tr.jv_row').last().find('.sel_account_code').val(item.id_chart_account).trigger("change");
				$('tr.jv_row').last().find('.txt_debit').val(check_zero(item.debit))
				$('tr.jv_row').last().find('.txt_credit').val(check_zero(item.credit))
				$('tr.jv_row').last().find('.txt_details').val(item.details)
				// $('tr.entry_row').last().find('.sel_account_code').val(item.id_chart_account).trigger("change");

				console.log({item})
			});
			set_entry_total();
		})
		function check_zero($in){
		$in = parseFloat($in);
		return ($in == 0 || isNaN($in))?'':number_format($in,2);
	}
</script>

<?php endif; ?>
<?php endif; ?>

<?php if(!$allow_post): ?>
<script type="text/javascript">
	$('#jv_main_div').find('input,select,a,textarea').attr('disabled',true)
</script>



<?php endif; ?>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('adminLTE.admin_template', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/journal_voucher/jv_form.blade.php ENDPATH**/ ?>