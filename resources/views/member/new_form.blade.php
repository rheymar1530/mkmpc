@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
	.main_form{
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		width: 1200px;
	}
	.form-row  label{
		margin-bottom: unset !important;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 15px;
	}
	.form-row{
		margin-top: -3px;
	}
	.nav-step .active{
		background: #dc3545 linear-gradient(180deg,#e15361,#dc3545) repeat-x !important;

	}
	.nav-pills .nav-link{
		margin-right: 15px !important;
	}
	.dark-mode a.nav_sel:hover{
		color: white !important;
		background: #e6e6e64D;
	}
	a.nav_sel:hover{
		color: black !important;
		background: #e6e6e64D;
	}
	.class_amount,.class_number{
		text-align: right;

	}
	input[type=number]::-webkit-inner-spin-button, 
	input[type=number]::-webkit-outer-spin-button { 
		-webkit-appearance: none; 
		margin: 0; 
	}
	.dark-mode .mandatory{
		border-color: rgba(232, 63, 82, 0.8) !important;
		box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(232, 63, 82, 0.6) !important;
		outline: 0 none !important;
	}
	.mandatory{
		border-color: rgba(232, 63, 82, 0.8);
		box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(232, 63, 82, 0.6);
		outline: 0 none;
	}
	.mandatory:focus{
		border-color: rgba(232, 63, 82, 0.8) !important;
		box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(232, 63, 82, 0.6) !important;
		outline: 0 none !important;
	}

</style>
<?php
$acc_status = array(
	'1' => 'Active',
	'0' => 'Inactive'
);
?>
<div class="container main_form section_body">
	<div class="text-center">
		<h3 class="head_lbl">Membership Form @if($opcode == 1)<small>({{ $details->membership_id }})</small>@endif
	</div>
	<?php $back_link = (request()->get('href') == '')?'/member/list':request()->get('href'); ?>
	<a class="btn bg-gradient-secondary btn-sm round_button" href="{{$back_link}}" style=""><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Member List</a>
	@if($opcode == 1 && MySession::isAdmin())
	<select class="form-control p-0 col-md-2 float-right" id="sel_mem_status">
		@foreach($acc_status as $val=>$ac)
		<option value="{{$val}}" <?php echo($details->status==$val)?'selected':''; ?> >{{$ac}}</option>
		@endforeach					
	</select>
	@endif
	
	@if($opcode == 1 && !$completed_form)
	<div class="alert alert-warning">
		<p class="mb-0">Please complete membership info</p>
	</div>
	@endif

	<div class="card c-border mt-3">
		<div class="card-header bg-gradient-primary2">			
			<div class="text-center">
				<h4>Personal Information</h4>
			</div>
		</div>
		<div class="card-body mt-3">

			@include('member.personal_information')
		</div>
	</div>
	<div class="card c-border mt-3">
		<div class="card-header bg-gradient-primary2">			
			<div class="text-center">
				<h4>Financial Information</h4>
			</div>
		</div>
		<div class="card-body mt-3">
			@include('member.financial_information')
		</div>
	</div>		
	<div class="card c-border mt-3">
		<div class="card-header bg-gradient-primary2">			
			<div class="text-center">
				<h4>Member Information</h4>
			</div>
		</div>
		<div class="card-body mt-3">

			@include('member.member_information')
		</div>
	</div>		

	<div class="card c-border mt-3">
		<div class="card-header bg-gradient-primary2">			
			<div class="text-center">
				<h4>Attachments</h4>
			</div>
		</div>
		<div class="card-body mt-3">
			@include('member.attachment')
		</div>
	</div>	
	<div class="card c-border mt-3">
		<div class="card-header bg-gradient-primary2">			
			<div class="text-center">
				<h4>Photo</h4>
			</div>
		</div>
		<div class="card-body mt-3">

			@include('member.photo')	
		</div>
	</div>	
		@if($opcode == 0 && $credential->is_create)
		<button class="btn bg-gradient-primary2 float-right round_button" id="btn_submit_member_add" style="margin-left:12px"><i class="fa fa-check"></i>&nbsp;Save and Add More</button>
	@endif
	@if(($opcode ==0 && $credential->is_create) || ($opcode == 1 && $credential->is_edit) || MySession::MemberCode() == $details->member_code )
	<button class="btn bg-gradient-success2 float-right round_button" id="btn_submit_member"><i class="fa fa-check"></i>&nbsp;Save</button>
	@endif
</div>

@include('member.camera_modal')
@endsection

@push('scripts')
<script src="{{URL::asset('plugins/camerajs/js/camera-js.js')}}"></script>
<script type="text/javascript">
	let personal_info_fields = ["txt_mem_date","txt_first_name","txt_middle_name","txt_last_name","txt_suffix","txt_birthdate","txt_place_birth","sel_gender","sel_civil_status","txt_address","txt_mobile","txt_tin","txt_email","txt_religion","sel_educ_att","sel_branch","sel_mem_type","sel_brgy_lgu","txt_bod_reso","txt_no_shares","txt_amt_shares","sel_income_source","txt_annual_income","txt_spouse","txt_spouse_occupation","txt_spouse_annual_income","txt_no_dependent","txt_in_capital","txt_position"];
	var opcode = {{$opcode}}; //0 - add; 1 - update
	var personal_clear = false;
	var financial_clear = false;
	var details = jQuery.parseJSON('<?php echo json_encode($details ?? []); ?>');
	const BRGY_LGU_OBJ = jQuery.parseJSON('<?php echo json_encode($bg_lgu ?? []);?>');


	$(document).ready(function(){
		
		if(opcode == 1){
			for(var $i=0;$i<=personal_info_fields.length;$i++){
				var element = $('#'+personal_info_fields[$i]);
				var input_value = (element.hasClass("class_amount"))?number_format(parseFloat(details[element.attr("input-key")] ?? 0)):details[element.attr("input-key")]
				element.val(input_value);

				if(personal_info_fields[$i] == "sel_mem_type"){
					init_brgy_lgu();
				}
			}
		}else{
			$('#sel_mem_type').val(1);
			init_brgy_lgu();
		}
	})
	function post(btn_click){ //parameter is for save and save and add more
		var form_data = new FormData();
		var data = {};
		var keys = [];

		// form_data.append("att_descriptions",JSON.stringify(att_descriptions));

		for(var $i=0; $i<personal_info_fields.length;$i++){
			var element = $("#"+personal_info_fields[$i]);
			keys.push(element.attr("input-key"))
			var val = (element.hasClass("class_amount"))?decode_number_format(element.val()):element.val();
			form_data.append(element.attr("input-key"),val);
			data[element.attr("input-key")] = val
		}
		console.log({keys});

		form_data.append("membership_id",$.trim($('#txt_mem_id').val()));
		form_data.append("opcode",opcode);
		form_data.append('image',$('#txt_photo').prop('files')[0])
		form_data.append('gov_id',($('#file_gov_id').length > 0)?$('#file_gov_id').prop('files')[0]:"");
		form_data.append('gov_id_remarks',$('#gov_id_remarks').val());
		form_data.append('bod_mem',($('#file_bod_mem').length > 0)?$('#file_bod_mem').prop('files')[0] : "");
		form_data.append('bod_mem_remarks',$('#bod_mem_remarks').val());		
		form_data.append("remove_gov_id",remove_gov_id);
		form_data.append("remove_bod_id",remove_bod_id);
		form_data.append("is_remove_image",is_remove_image);
		form_data.append('id_member',{{$details->id_member ?? 0}})
		form_data.append('save_and_add',btn_click);
		form_data.append('image_capture_via',image_via);
		form_data.append('base_64_img',(image_via == 1)?$('#preview_image').attr('src'):"");


		$.ajax({
			type             :           'POST',
			url              :           '/member/post',
			contentType		 :           false,
			data             :           form_data,
			cache			 : 			 false, // To unable request pages to be cached
			processData	     : 	 	     false,
			beforeSend      :       function(){
				show_loader();
			},success       :        function(response){
				console.log({response});
				hide_loader();

				// return;
				if(response.RESPONSE_CODE == "success"){
					
					Swal.fire({
						title: "Member successfully saved",
						text: '',
						icon: 'success',
						showConfirmButton : false,
						timer  : 1300
					}).then((result) => {
                        // location.reload()
                        if(opcode == 1){ // if update


                        	location.reload();
                        }else{
                        	if(response.save_and_add){

                        		location.reload();
                        	}else{
                        		window.location = '/member/view/'+response.member_code+'?href='+'{{urlencode($back_link)}}';
                        	}
                        }
                    });						
				}else if(response.RESPONSE_CODE == "DUPLICATE_MEMBER_ID_FOUND"){
					Swal.fire({
						title: 'Duplicate Membership ID Found',
						text: response.message,
						icon: 'warning',
						showConfirmButton : false,
						timer : 1500
					});	

					return;				
				}else if(response.RESPONSE_CODE == "CREDENTIAL_ERROR"){
					Swal.fire({
						title: response.message,
						text: '',
						icon: 'warning',
						showConfirmButton : false,
						timer : 1500
					});	
				}
			},error: function(xhr, status, error) {
				hide_loader()
				var errorMessage = xhr.status + ': ' + xhr.statusText
				isClick = false;
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
	function save(){
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
	}
	$('#txt_no_shares').on('keyup',function(){
		var val = $(this).val();
		var initial_capital_paidup = val *100;
		console.log({initial_capital_paidup})

		$('#txt_in_capital').val(number_format(initial_capital_paidup));
	})

	const init_brgy_lgu = ()=>{
		// sel_mem_type
		let options = '';
		var mem_type = $('#sel_mem_type').val();
		let disabledBrgyLgu = true;
		console.log({mem_type});
		if(mem_type >= 2){
			let mkey = (mem_type==2)?1:2;
			let optionOBJ = BRGY_LGU_OBJ[mkey];
			let lbl = (mem_type == 2)?'Baranggay':'LGU';
			$.each(optionOBJ,function(i,item){
				options += `<option value="${item.id_baranggay_lgu}">${item.name}</option>`;
			});
			disabledBrgyLgu = false;
			$('#lbl-brgy-lgu').text(lbl);
		}

		$('#sel_brgy_lgu').prop('disabled',disabledBrgyLgu).html(options);
	}
	$('#sel_mem_type').on('change',function(){
		init_brgy_lgu();
	})
</script>
@if($opcode == 1)
<script type="text/javascript">
	var previous_status = $('#sel_mem_status').val();
	$(document).on('change','#sel_mem_status',function(){
		Swal.fire({
			title: 'Do you want to save this?',
			icon: 'warning',
			showDenyButton: false,
			showCancelButton: true,
			confirmButtonText: `Save`,
		}).then((result) => {
			if (result.isConfirmed) {
				// alert("POSTED");
				update_mem_status();
			}else{
				$('#sel_mem_status').val(previous_status);
			}
		})	
	})

	function update_mem_status(){
		var val = $('#sel_mem_status').val();
		var id_member = {{$details->id_member ?? 0}};
		previous_status = val;

		$.ajax({
			type      :     'POST',
			url       :     '/member/post_status',
			data      :     {'status' : val, 'id_member' : id_member},
			beforeSend :    function(){
				show_loader();
			},
			success  :    function(response){
				console.log({response});
				hide_loader();

				if(response.RESPONSE_CODE == "SUCCESS"){
					Swal.fire({
						title: 'Account Status Successfully Updated',

						icon: 'success',
						showConfirmButton : false,
						timer : 2500
					}).then(function(){
						location.reload();
					});	
				}else{
					Swal.fire({
						title: response.message,

						icon: 'warning',
						showConfirmButton : false,
						timer : 2500
					})
				}
			},error: function(xhr, status, error) {
				hide_loader()
				var errorMessage = xhr.status + ': ' + xhr.statusText
				isClick = false;
				Swal.fire({
					title: "Error-" + errorMessage,
					text: '',
					icon: 'warning',
					confirmButtonText: 'OK',
					confirmButtonColor: "#DD6B55"
				});
			}
		})


		console.log({val,id_member});
	}
</script>
@endif
@endpush