
<style type="text/css">
	.modal-p-50 {
		max-width: 50% !important;
		min-width: 50% !important;
		margin: auto;
	}
	.filter_label{
		margin-top: 8px;
	}
	.font-bold{
		font-weight: bold;
	}
</style>
<?php
$loan_status=[
	"ALL"=>"**ALL**",
	"0" =>"Submitted",
	"1" =>"Processing",
	"2" => "Approved",
	"3" => "Active",
	"4" => "Cancelled",
	"5" => "Disapproved",
	"6" => "Closed"
];
$filter_date_type = [
	1 => "Date Created",
	2 => "Date Released"
];
$filter_type_selection = [
	1 => "Date Received",
	2 => "Date Created",
	3 => "OR No"
];

$receive_from = [
	1=>"**ALL**",
	2=>"Member",
	3=>"Non-member"
];
?>
<div id="view_options" class="modal fade"  role="dialog" aria-hidden="false">
	<div class="modal-dialog  modal-lg" role="document">
		<form id="filter_target">
			<div class="modal-content " style="">
				<div class="modal-header panel_header">
					<h4 class="modal-title"><i class="fa fa-eye"></i> View Options</h4>

					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">×</span>
					</button>
				</div>
				<div class="modal-body" style="max-height: calc(100vh - 210px);
				overflow-y: auto;overflow-x: auto">    
				<div class="form-horizontal" id="submit_filter">   

					<?php if(MySession::isAdmin()): ?>
					<div class="form-group row">
						<?php
							$checked_sel_member =(request()->get('id_member')=='')?"":"checked";
							$disabled_sel_member =(request()->get('id_member')=='')?"disabled":"";
						?>	
						<div class="col-md-2">
							<div class="form-check">
							  <input class="form-check-input" type="checkbox" value="" id="chk_sel_member" <?php echo $checked_sel_member; ?>>
							  <label class="form-check-label font-bold" for="chk_sel_member">
							   	Borrower
							  </label>
							</div>
						</div>
					
						<div class="col-md-9" id="div_sel_id_member">
							<select id="sel_id_member" class="form-control p-0" name="id_member" required  <?php echo $disabled_sel_member; ?> >
								<?php if(isset($selected_member)): ?>
									<option value="<?php echo e($selected_member->id_member); ?>"><?php echo e($selected_member->member_name); ?></option>
								<?php endif; ?>

							</select>
						</div>	
					</div>
					<?php endif; ?>
					<?php

						// $checked_fil_date =(request()->get('filter_date_type')=='')?"":"checked";
						// $disabled_fil_date =(request()->get('filter_date_type')=='')?"disabled":"";

						$checked_fil_date =($date_check == 0)?"":"checked";
						$disabled_fil_date =($date_check == 0)?"disabled":"";

						// $selected_fil_date =(request()->get('filter_date_type')=='')?"":request()->get('filter_date_type');
						$selected_fil_date = $sel_filter_date_type;




						// $start_date = (request()->get('filter_start_date')!='')?request()->get('filter_start_date'):$current_date;
						// $end_date = (request()->get('filter_end_date')!='')?request()->get('filter_end_date'):$current_date;

						$start_date = $fill_date_start;
						$end_date = $fill_date_end;
					?>
					<div class="form-group row">
						<div class="col-md-2">
							<div class="form-check">
							  <input class="form-check-input" type="checkbox" value="" id="chk_date_type" <?php echo $checked_fil_date ?> >
							  <label class="form-check-label font-bold" for="chk_date_type">
							   	Date
							  </label>
							</div>
						</div>						
						<div class="col-md-5" id="div_sel_id_member">
							<select id="sel_fil_date" class="form-control p-0 fil_date"  required <?php echo $disabled_fil_date; ?> >
								<?php $__currentLoopData = $filter_date_type; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val=>$desc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<option value="<?php echo e($val); ?>" <?php echo ($selected_fil_date == $val)?"selected":""; ?> ><?php echo e($desc); ?></option>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</select>
						</div>	
					</div>
					<div class="form-group row">
						<div class="col-md-2">
							&nbsp;
						</div>						
						<div class="col-md-4">
							<input type="date" title="Date" required="" class="form-control opt_sel form-in-text fil_date" id="txt_fil_date_from"  value="<?php echo e($start_date); ?>" name="date_start" <?php echo $disabled_fil_date; ?>>
						</div>
						<div class="col-md-4">
							<input type="date" title="Date" required="" class="form-control opt_sel form-in-text fil_date" id="txt_fil_date_to"  value="<?php echo e($end_date); ?>" name="date_end" <?php echo $disabled_fil_date; ?>>
						</div>	
					</div>	
					<div class="form-group row">
						<?php
							$checked_loan_service =(request()->get('filter_loan_service')=='')?"":"checked";
							$disabled_loan_service =(request()->get('filter_loan_service')=='')?"disabled":"";
							$selected_loan_service =(request()->get('filter_loan_service')=='')?"":request()->get('filter_loan_service');
						?>	
						<div class="col-md-2">
							<div class="form-check">
							  <input class="form-check-input" type="checkbox" value="" id="chk_loan_service" <?php echo $checked_loan_service; ?> >
							  <label class="form-check-label font-bold" for="chk_loan_service" >
							   	Loan Service
							  </label>
							</div>
						</div>						
						<div class="col-md-8">
							<select id="sel_loan_service" class="form-control p-0"  required <?php echo $disabled_loan_service; ?> >
								<option value="0">**ALL**</option>
								<?php $__currentLoopData = $loan_service_lists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loan_s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<option value="<?php echo e($loan_s->id_loan_service); ?>" <?php echo ($selected_loan_service == $loan_s->id_loan_service)?"selected":""; ?> ><?php echo e($loan_s->name); ?></option>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</select>
						</div>
					</div>	
				</div>
			</div>
			<div class="modal-footer modal_body">
				<button type="submit" class="btn btn-md  bg-gradient-primary but"><i class="fa fa-search"></i>&nbsp;&nbsp;Search</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div><!-- /.modal-dialog -->
	</form>
</div>
</div>
<?php $__env->startPush('scripts'); ?>
<script type="text/javascript">
	var $date_form = $('#fil_form_date').detach();
	var $or_form = $('#fil_form_or').detach();
	var $selection = jQuery.parseJSON('<?php echo json_encode($member_selected ?? [])?>');
	$(document).on('select2:open', () => {
		document.querySelector('.select2-search__field').focus();
	});	

	$('#sel_loan_service,#sel_loan_status').select2()
	intialize_select2()



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
	$(document).on('click','#chk_sel_member',function(){
		var checked = $(this).prop('checked');
		$('#sel_id_member').prop('disabled',!checked);
		console.log({checked})
	})
	$(document).on('click','#chk_date_type',function(){
		var checked = $(this).prop('checked');
		$('.fil_date').prop('disabled',!checked);
		console.log({checked})
	})
	$(document).on('click','#chk_loan_service',function(){
		var checked = $(this).prop('checked');
		$('#sel_loan_service').prop('disabled',!checked);
		console.log({checked})
	})	
	$('#filter_target').submit(function(e){
		e.preventDefault();
		var filter_data = {};
		filter_data['filter_status'] = $('#sel_loan_status').val();

		//MEMBER
		if($('#chk_sel_member').prop('checked')){
			var id_member = $('#sel_id_member').val();
			filter_data['id_member'] = id_member;
		}

		//DATE
		if($('#chk_date_type').prop('checked')){
			filter_data['date_check'] = 1;
			filter_data['filter_date_type'] = $('#sel_fil_date').val();
			filter_data['filter_start_date'] = $('#txt_fil_date_from').val();
			filter_data['filter_end_date'] = $('#txt_fil_date_to').val();
		}else{
			filter_data['date_check'] = 0;
		}
		//Loan Service
		if($('#chk_loan_service').prop('checked')){
			filter_data['filter_loan_service'] = $('#sel_loan_service').val();
		}

		var location_reload = '/loan?'+$.param(filter_data);
		window.location = location_reload;
		console.log({location_reload});
	})



</script>
<?php $__env->stopPush(); ?>

<?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/loan/filter_option_modal.blade.php ENDPATH**/ ?>