@extends('adminLTE.admin_template')
@section('content')
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
	.tbl_in_employee tr>th ,.tbl_fees tr>th,.tbl-inputs tr>th{
		padding: 3px !important;
	/*	padding-left: 5px;
		padding-right: 5px;*/
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 14px;
		text-align: center;
	}
	.tbl-inputs-text{
		padding-left: 5px !important;
		padding-right: 5px !important;
		/*padding: px !important;*/
	}
	.tbl_in_employee tr>td,.tbl_fees tr>td,.tbl_repayment_display tr>td{
		padding: 0px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 15px;
	}
	.tbl-inputs tr>td{
		padding: 0px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 15px;
	}
	.frm_emp_in,.frm_allowances{
		height: 27px !important;
		width: 100%;    
		font-size: 13px;
	}

	.class_amount{
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
/*		width: 1300px !important;
		margin: 0 auto;*/
	}
	.text-red{
		color: red;
	}
	.swal2-deny{
		padding: 0.375rem 0.75rem !important;
	}
</style>
<?php
$allowance_type = [
	1=>"Daily",
	2=>"Monthly"
];
$status = [
	1=>'Active',
	2=>'Inactive'
];
?>
<div class="wrapper2">
	<div class="container-fluid main_form section_body" style="margin-top: -20px;" >
		<?php $back_link = (request()->get('href') == '')?'/employee':request()->get('href'); ?>
		<a class="btn bg-gradient-secondary btn-sm" href="{{$back_link}}" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Employee List</a>

		<div class="card" id="repayment_main_div">
			<form id="frm_submit_employee">
				<div class="card-body col-md-12">
					<h3 class="head_lbl text-center">Employee Information
						@if($opcode == 1)
						<small>(ID# {{$employee_details->id_employee}})</small>
						@endif
					</h3>
					<div class="row">
						<div class="col-md-12 p-0" style="margin-top:15px">
							<!-- <a class="btn bg-gradient-success" style="margin-bottom:10px" onclick="search_member_modal()">Link from member</a> -->
							@if($opcode == 1)
							<div class="form-row">
								<div class="form-group col-md-4" style="">
									<label for="sel_status">Status</label>
									<select class="form-control in_employee p-0" id="sel_status" key="status">
										@foreach($status as $val=>$desc)

										<option value="{{$val}}">{{$desc}}</option>
										@endforeach
									</select>
									
								</div>
							</div>	
							@endif
							<div class="form-row" style="margin-bottom:-10px">
								<div class="form-group col-md-12">
									<div class="custom-control custom-checkbox">
										<?php
											$checked = (isset($employee_details->id_member) && $employee_details->id_member > 0)?'checked':'';
										?>
										<input type="checkbox" class="custom-control-input" id="chk_member" {{$checked}}>
										<label class="custom-control-label chk_deduct_cbu" for="chk_member">Coop Member ?</label>

									</div>									
								</div>
							</div>
							<div id="sel_member_holder"></div>
								
							<div class="form-row" style="margin-top:15px">
								<div class="form-group col-md-3" style="">
									<label for="sel_status">Employee Type</label>
									<select class="form-control in_employee p-0" id="sel_status" key="id_employee_type">
										@foreach($employee_type as $et)

										<option value="{{$et->id_employee_type}}">{{$et->description}}</option>
										@endforeach
									</select>
									
								</div>
							</div>	
							<div class="form-group row" id="div_sel_member">				
								<div class="col-md-5">
									<select id="sel_id_member" class="form-control p-0 in_employee" key="id_member">
										@if(isset($selected_member))
										<option value="{{$selected_member->id_member}}">{{$selected_member->member_name}}</option>
										@endif

									</select>

								</div>	
								<div class="col-md-1">
									<a class="btn bg-gradient-warning2 btn-sm" onclick="remove_member()">Remove</a>
								</div>
							</div>
							<div class="card c-border">

								<div class="card-body">
									<h5 class="lbl_color text-center">Personal Information</h5>
									<div class="form-row mt-4">
										<div class="form-group col-md-4" style="">
											<label for="txt_first_name">First Name</label>
											<input type="text" name="" class="form-control in_employee" value="" id="txt_first_name" key="first_name">
										</div>
										<div class="form-group col-md-3" style="">
											<label for="txt_middle_name">Middle Name</label>
											<input type="text" name="" class="form-control in_employee" value="" id="txt_middle_name" key="middle_name">
										</div>
										<div class="form-group col-md-4" style="">
											<label for="txt_last_name">Last Name</label>
											<input type="text" name="" class="form-control in_employee" value="" id="txt_last_name" key="last_name">
										</div>
										<div class="form-group col-md-1" style="">
											<label for="txt_suffix">Suffix</label>
											<input type="text" name="" class="form-control in_employee" value="" id="txt_suffix" key="suffix">
										</div>
									</div>	
									<div class="form-row">
										<div class="form-group col-md-8" style="">
											<label for="txt_address">Address</label>
											<input type="text" name="" class="form-control in_employee" value="" id="txt_address" key="address">
										</div>
										<div class="form-group col-md-4" style="">
											<label for="txt_birthday">Birthday</label>
											<input type="date" name="" class="form-control in_employee" value="" id="txt_birthday" key="birthday">
										</div>
									</div>	

									<div class="form-row">
										<div class="form-group col-md-3" style="">
											<label for="txt_tin_no">TIN No.</label>
											<input type="text" name="" class="form-control in_employee" value="" id="txt_tin_no" key="tin_no">
										</div>
										<div class="form-group col-md-3" style="">
											<label for="txt_sss_gsis_no">SSS/GSIS</label>
											<input type="text" name="" class="form-control in_employee" value="" id="txt_sss_gsis_no" key="sss_gsis_no">
										</div>
										<div class="form-group col-md-3" style="">
											<label for="txt_philhealth_no">Philhealth No.</label>
											<input type="text" name="" class="form-control in_employee" value="" id="txt_philhealth_no" key="philhealth_no">
										</div>
										<div class="form-group col-md-3" style="">
											<label for="txt_hdmf_no">HDMF No.</label>
											<input type="text" name="" class="form-control in_employee" value="" id="txt_hdmf_no" key="hdmf_no">
										</div>
									</div>	
									<div class="form-row">
										<div class="form-group col-md-4" style="">
											<label for="txt_email">Email</label>
											<input type="email" name="" class="form-control in_employee" value="" id="txt_email" key="email">
										</div>
										
									</div>	
								</div>
							</div>
						</div>
						<div class="col-md-12 p-0" style="margin-top:15px">
							<div class="card c-border">
								
								<div class="card-body">
									<h5 class="lbl_color text-center">Employee Details</h5>
									<div class="form-row mt-4">
										<div class="form-group col-md-3" style="">
											<label for="txt_date_hired">Date Hired</label>
											<input type="date" name="" class="form-control in_employee" value="" id="txt_date_hired" key="date_hired">
										</div>
									</div>	
									<div class="form-row">
										<div class="form-group col-md-3" style="">
											<label for="sel_branch">Branch</label>
											<select class="form-control in_employee p-0" id="sel_branch" key="id_branch">
												@foreach($branches as $branch)
												<option value="{{$branch->id_branch}}">{{$branch->branch_name}}</option>
												@endforeach
											</select>
										</div>
										<div class="form-group col-md-3" style="">
											<label for="sel_department">Department</label>
											<select class="form-control in_employee p-0" id="sel_department" key="id_department">
												@foreach($department as $dep)
												<option value="{{$dep->id_department}}">{{$dep->name}}</option>
												@endforeach
											</select>
										</div>
										<div class="form-group col-md-3" style="">
											<label for="sel_position">Position</label>
											<select class="form-control in_employee p-0" id="sel_position" key="id_position">
												@foreach($position as $pos)
												<option value="{{$pos->id_position}}">{{$pos->description}}</option>
												@endforeach
											</select>
										</div>
										<div class="form-group col-md-3" style="">
											<label for="sel_emp_status">Employment Status</label>
											<select class="form-control in_employee p-0" id="sel_emp_status" key="id_employee_status">
												@foreach($employee_status as $es)
												<option value="{{$es->id_employee_status}}">{{$es->description}}</option>
												@endforeach
											</select>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-12 p-0" style="margin-top:15px">
							<div class="card c-border">
								<div class="card-body">
									<h5 class="lbl_color text-center">Employee Salary</h5>
									<div class="form-row mt-4">
										<div class="form-group col-md-4" style="">
											<label for="sel_compensation">Employee Compensation</label>
											<select class="form-control in_employee p-0" id="sel_compensation" key="id_employee_compensation">
												@foreach($employee_compensation as $ec)
												<option value="{{$ec->id_employee_compensation}}">{{$ec->description}}</option>
												@endforeach
											</select>
										</div>
										<div class="form-group col-md-3" style="">
											<label for="txt_rate">Basic Rate</label>
											<input type="text" name="" class="form-control in_employee class_amount" value="0.00" id="txt_rate" key="rate">
										</div>
									</div>	
								</div>
							</div>
						</div>

						<div class="col-md-12 p-0" style="margin-top:15px">
							<div class="card c-border">
								<div class="card-body">
									<h5 class="lbl_color text-center">Allowances</h5>
									<div class="form-row">
										<div class="col-md-8">
										<table class="table tbl_in_employee">
											<thead>
												<tr class="table_header_dblue">
													<th width="50%">Description</th>
													<th>Amount</th>
													<th width="3%"></th>
												</tr>
											</thead>
											<tbody id="allowance_body">
												<tr class="row_allowance">
													<td>
														<select class="form-control frm_allowances p-0"  key="id_allowance_name">
															@foreach($allowance_name as $an)
																<option value="{{$an->id_allowance_name}}">{{$an->description}}</option>
															@endforeach
														</select>
													</td>
													<td><input type="text" class="form-control frm_allowances class_amount" key="amount" value="0.00"></td>
													
													<td><a class="btn btn-xs bg-gradient-danger btn_delete_entry" onclick="remove_allowance(this)" style="margin-left: 5px !important;"><i class="fa fa-trash"></i></a></td>

												</tr>
											</tbody>
										</table>
										</div>
									</div>	
									<div class="form-row">
										<div class="col-md-8">
											<button class="btn btn-sm bg-gradient-info2 float-right" type="button" onclick="add_allowance()"><i class="fas fa-plus"></i>&nbsp;Add Allowance</button>
										</div>
										
									</div>
								</div>
								<div class="card-footer custom_card_footer">
									
								</div>
							</div>
						</div>
						<div class="col-md-12 p-0" style="margin-top:15px">
							<div class="card c-border">
								<div class="card-body">
									<h5 class="lbl_color text-center">Benefits Deduction (Monthly)</h5>
									<div class="form-row mt-3">
										<table class="table tbl_in_employee table-bordered">
											<thead>
												<tr class="table_header_dblue">
													<th>SSS</th>
													<th>PhilHealth</th>
													<th>HDMF</th>
													<th>Withholding Tax</th>
													<th>Insurance</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td><input type="text" class="form-control  frm_emp_in class_amount in_employee" id="txt_sss_amount" key="sss_amount" value="0.00"></td>
													<td><input type="text" class="form-control frm_emp_in class_amount in_employee" id="txt_philhealth_amount" key="philhealth_amount" value="0.00"></td>
													<td><input type="text" class="form-control frm_emp_in class_amount in_employee" id="txt_hdmf_amount" key="hdmf_amount" value="0.00"></td>
													<td><input type="text" class="form-control frm_emp_in class_amount in_employee" id="txt_withholding_amount" key="withholding_tax" value="0.00"></td>
													<td><input type="text" class="form-control frm_emp_in class_amount in_employee" id="txt_insurance_amount" key="insurance" value="0.00"></td>
												</tr>
											</tbody>
										</table>
									</div>	
								</div>
							</div>
						</div>
						<div class="col-md-12 row">	



						</div>	

					</div>
				</div>

				<div class="card-footer">
					<button class="btn  bg-gradient-success2 float-right">Save</button>
				</div>
			</form>

		</div>
	</div>
</div>


@endsection

@push('scripts')
<script type="text/javascript">
	const allowance_html = "<tr class='row_allowance'>"+$('.row_allowance').html()+"</tr>";
	const no_allowance ="<tr class='row_na'><td colspan='4' style='text-align:center'>No Allowance</td></tr>";
	const $opcode = '<?php echo $opcode; ?>';
	const div_sel_member = $('#div_sel_member').detach();
	$('#allowance_body').html(no_allowance);

	$(document).on('click','#chk_member',function(){
		initialize_member_select();
	})

	function initialize_member_select(){
		var is_check = $('#chk_member').prop('checked');
		if(is_check){
			$('#sel_member_holder').html(div_sel_member);
			intialize_select2();
		}else{
			$('#sel_member_holder').html('');
		}
		console.log({is_check});
	}
	function add_allowance(){
		if($('tr.row_na').length >= 1){
			$('tr.row_na').remove();
		}
		$('#allowance_body').append(allowance_html);
	}
	function remove_allowance(obj){
		var parent_row = $(obj).closest('tr.row_allowance');
		parent_row.remove();

		if($('tr.row_allowance').length == 0){
			$('#allowance_body').html(no_allowance)
		}
	}

	$('#frm_submit_employee').submit(function(e){
		e.preventDefault();
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
		var $employee_data = {};
		$('.in_employee').each(function(){
			var key = $(this).attr('key');
			var val = $(this).val();
			if($(this).hasClass('class_amount')){
				val = decode_number_format(val);
			}
			$employee_data[key] = val;
		})

		var $allowance = [];
		$('tr.row_allowance').each(function(){
			var temp = {};
			$(this).find('.frm_allowances').each(function(){
				var key = $(this).attr('key');
				var val = $(this).val();
				if($(this).hasClass('class_amount')){
					val = decode_number_format(val);
				}
				temp[key] = val;
				
			})
			$allowance.push(temp);
		})

		console.log({$employee_data,$allowance});

		$('.mandatory').removeClass('mandatory')

		$.ajax({
			type        :          'POST',
			url         :          '/employee/post',
			data        :          {'employee_data' : $employee_data,
			'allowance' : $allowance,
			'opcode' : $opcode,
			'id_employee' : '<?php echo $employee_details->id_employee ?? 0 ?>'},
			beforeSend  :          function(){
				show_loader();
			},
			success     :          function(response){
				console.log({response})
				hide_loader()
				if(response.RESPONSE_CODE == "INVALID_INPUT"){
					var invalid_fields = response.invalid_details.invalid_fields;
					$.each(invalid_fields,function(i,item){
						$('.in_employee[key="'+item+'"]').addClass('mandatory');
					})

					var invalid_allowance = response.invalid_details.invalid_allowance;
					$.each(invalid_allowance,function(i,item){
						$.each(item,function(c,value){

							$('.row_allowance:eq('+i+')').find('.frm_allowances[key="'+value+'"]').addClass('mandatory')
						})
					})
					Swal.fire({
						title: response.message,
						text: '',
						icon: 'warning',
						showCancelButton : false,
						showConfirmButton : false,
						timer : 2500
					});
				}else if(response.RESPONSE_CODE == "SUCCESS"){
					var link = "/employee/view/"+response.id_employee+"?href="+encodeURIComponent('<?php echo $back_link;?>');
					Swal.fire({
						title: "Employee Successfully Saved !",
						html : "<a href='"+link+"'>Employee ID# "+response.id_employee+"</a>",
						text: '',
						icon: 'success',
						showCancelButton : true,
						confirmButtonText: 'Add More Employee',
						showDenyButton: false,
						cancelButtonText: 'Close',
						showConfirmButton : true,     
						allowEscapeKey : false,
						allowOutsideClick: false
					}).then((result) => {
						if(result.isConfirmed) {
							window.location = "/employee/create?href="+encodeURIComponent('<?php echo $back_link;?>');
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
</script>





<script type="text/javascript">
	$(document).on('select2:open', () => {
		document.querySelector('.select2-search__field').focus();
	});	

	

	function intialize_select2(){		
		$("#sel_id_member").select2({
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
	$(document).on('change','#sel_id_member',function(){
		var val = $(this).val();
		if(val != null){
			parseMemberDetails(val);
		}
		
	})
	function parseMemberDetails($id_member){
		$.ajax({

			type       :      'GET',
			url        :      '/employee/sync_member',
			data       :      {'id_member'  : $id_member},
			beforeSend :      function(){
							  show_loader();
			},
			success    :      function(response){
							  console.log({response});
							  hide_loader();
							  var member_details = response.member_details;
							  $.each(member_details,function(i,value){
							  	$('.in_employee[key="'+i+'"]').val(value);
							  })
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
	function remove_member(){
		$('#sel_id_member').val(0).trigger("change");
	}
</script>

@if($opcode == 1)
<script type="text/javascript">
	var employee_details = jQuery.parseJSON('<?php echo json_encode($employee_details ?? []); ?>');
	var allowances = jQuery.parseJSON('<?php echo json_encode($allowances ?? []); ?>');


	initialize_member_select()
	console.log({employee_details,allowances});
	$(document).ready(function(){
		$.each(employee_details,function(key,value){
			var element = $('.in_employee[key="'+key+'"]');
			var val = value;
			if($(element).hasClass('class_amount')){
				val = number_format(parseFloat(value),2);
			}
			$(element).val(val);
		})

		if(allowances.length > 0){
			$.each(allowances,function(i,allowance){
				add_allowance();
				var new_allowance_row = $('.row_allowance').last();
				$.each(allowance,function(key,value){
					var val = value;
					var element = new_allowance_row.find('.frm_allowances[key="'+key+'"]');
					if($(element).hasClass('class_amount')){
						val = number_format(parseFloat(value));
					}
					$(element).val(val)
				})
			})
		}
	})
</script>
@endif
@endpush

