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
	/*remove the spinner on number*/
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


<div class="container main_form section_body" style="margin-top: -20px;">
	<?php $back_link = (request()->get('href') == '')?'/member/list':request()->get('href'); ?>
	<a class="btn bg-gradient-secondary btn-sm round_button" href="{{$back_link}}" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Member List</a>
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
	<div class="card">
		<!-- <form id="frm_post_member" enctype="multipart/form-data" method="post"> -->
			<div class="card-body col-md-12">
				<h3 class="lbl_color">Membership Form @if($opcode == 1)<small>({{ $details->membership_id }})</small>@endif

					
				</h3>
				<div class="row">
					<div class="col-sm-12" style="margin-bottom:10px">
						<div class="form-row">
							<div class="form-group" style="margin-top:5px !important">
								<ul class="nav nav-pills nav-step">
									<li class="nav-item"><a class="nav-link active nav_sel" href="#personal_information" >Personal Information</a></li>
									<li class="nav-item"><a class="nav-link nav_sel" href="#financial_information" >Financial Information</a></li>
									<li class="nav-item"><a class="nav-link nav_sel" href="#member_information" >Member Information</a></li>
									<li class="nav-item"><a class="nav-link nav_sel" href="#attachment" >Attachment</a></li>
									<li class="nav-item"><a class="nav-link nav_sel" href="#review_and_save"  onclick="set_review_and_save()">Review & Save</a></li>
								</ul>
							</div>
						</div>
					</div>
					<div class="tab-content col-sm-12 p-0">
						<div class="tab-pane active" id="personal_information">
							<form id="next_personal_info">
								@include('member.personal_information')
							</form>
							
						</div>
						<div class="tab-pane" id="financial_information">
							<form id="next_financial_info">
								@include('member.financial_information')
							</form>
						</div>
						<div class="tab-pane" id="member_information">
							<form id="next_member_info">
								@include('member.member_information')
							</form>

						</div>

						<div class="tab-pane" id="attachment">
							<form id="next_attachment">
								@include('member.attachment')
							</form>
						</div>
						<div class="tab-pane" id="review_and_save">
							@include('member.review_and_save')
						</div>
					</div>
				</div>
			</div>
<!-- 			<div class="card-footer">
				<button class="btn bg-gradient-success float-right" onclick="save()">Save</button>
			</div> -->
			<!-- </form> -->
		</div>
	</div>
	@include('member.camera_modal')
	@endsection
	@push('scripts')

	<script src="{{URL::asset('plugins/camerajs/js/camera-js.js')}}"></script>
	<script type="text/javascript">
		// "txt_in_capital",
		let tab_sequence = ["personal_information","financial_information","member_information","attachment","review_and_save"];
		let personal_info_fields = ["txt_mem_date","txt_first_name","txt_middle_name","txt_last_name","txt_suffix","txt_birthdate","txt_place_birth","sel_gender","sel_civil_status","txt_address","txt_mobile","txt_tin","txt_email","txt_religion","sel_educ_att","sel_branch","sel_mem_type","txt_bod_reso","txt_no_shares","txt_amt_shares","sel_income_source","txt_annual_income","txt_spouse","txt_spouse_occupation","txt_spouse_annual_income","txt_no_dependent","txt_in_capital"];
	var opcode = {{$opcode}}; //0 - add; 1 - update
	var personal_clear = false;
	var financial_clear = false;


	var details = jQuery.parseJSON('<?php echo json_encode($details ?? []); ?>');

	if(opcode == 1){
		for(var $i=0;$i<=personal_info_fields.length;$i++){
			var element = $('#'+personal_info_fields[$i]);
			var input_value = (element.hasClass("class_amount"))?number_format(parseFloat(details[element.attr("input-key")] ?? 0)):details[element.attr("input-key")]
			element.val(input_value)
		}
	}

	function test_input(){
		$('#txt_mem_date').val('2021-12-05');
		$('#txt_first_name').val('Rhey');
		$('#txt_middle_name').val('Doloroso');
		$('#txt_last_name').val('Caluza');
		$('#txt_birthdate').val("2021-09-15");
		$('#sel_gender').val("Male");
		$('#sel_civil_status').val(1);
		$('#txt_email').val("caluzarheymar@gmail.com");
		$('#txt_address').val("Callan Pototan Iloilo");
		$('#txt_mobile').val("09959630865");
		$('#sel_position').val(1);
		$('#sel_coop_position').val(1);
		$('#sel_pos_stat').val(1);
		$('#txt_school').val("BNNCHS")
	}

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
	$('.class_amount').keyup(function(){

	})
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

	function number_format(number){
		var result =  number.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
		return result;
	}
	function decode_number_format(number){
		var result=number.replace(/\,/g,''); // 1125, but a string, so convert it to number
		result=parseFloat(result,10);
		return result;
	}
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
	$('#next_personal_info,#next_member_info,#next_financial_info,#next_attachment').submit(function(e){
		e.preventDefault();
	})

	function highlight_required(inputs){
		for(var $i=0;$i<inputs.length;$i++){
			$('#'+inputs[$i]).addClass("mandatory")
		}
	}

	function check_form_completed(form_to_check){

		var $activeTab = form_to_check;
		var this_form = $activeTab.attr("id");
		var fields = [];
		var no_input = [];
		var completed = true;

		// if(this_form == "personal_information"){
		// 	fields = ["txt_mem_date","txt_first_name","txt_last_name","txt_birthdate","txt_place_birth","sel_gender","sel_civil_status","txt_address","txt_mobile","txt_email","sel_educ_att"];
		// }else if(this_form == "member_information"){
		// 	fields = ["sel_branch","sel_mem_type","txt_bod_reso","txt_no_shares","txt_amt_shares","txt_in_capital"]; 
		// 	// ,"txt_in_capital"
		// }else if(this_form == "financial_information"){
		// 	fields = ["sel_income_source","txt_annual_income"];

		// 	// ,"txt_spouse","txt_spouse_occupation","txt_no_dependent"
		// }

		if(this_form == "personal_information"){
			fields = [];
		}else if(this_form == "member_information"){
			fields = []; 
			// ,"txt_in_capital"
		}else if(this_form == "financial_information"){
			fields = [];

			// ,"txt_spouse","txt_spouse_occupation","txt_no_dependent"
		}

		for(var $i=0;$i<fields.length;$i++){
			var element = $('#'+fields[$i]);
			var elem_val = $.trim(element.val())
			if(elem_val == "" || elem_val == null || elem_val == undefined){
				no_input.push(fields[$i]);
			}	
		}
		if(no_input.length > 0){
			highlight_required(no_input);
			completed = false;
		}

		return completed;
	}
	$(document).on('keyup','.mandatory',function(){
		if($(this).val() != ""){
			$(this).removeClass("mandatory");
		}
	})

	$(document).on('click','.nav_sel',function(e){
		e.preventDefault();
		var $activeTab = $('.tab-pane.active');
		var $activePill = $('.nav_sel.active');
		var transfer_tab = ($(this).attr('href')).replace("#","");



		var to_check_tab = [];

		if(transfer_tab == "member_information"){
			to_check_tab = ["personal_information","financial_information"];
		}else if(transfer_tab == "attachment" || transfer_tab == "review_and_save" ){
			to_check_tab = ["personal_information","financial_information","member_information"];
		}else if(transfer_tab == "financial_information"){
			to_check_tab = ["personal_information"];
		}

		if(to_check_tab.length > 0){
			for(var $i=0;$i<to_check_tab.length;$i++){
				var is_complete = check_form_completed($('#'+to_check_tab[$i]));
				if(!is_complete){
					func_transfer_tab(to_check_tab[$i],$('#'+to_check_tab[$i]),"tab")
					Swal.fire({
						title: "Please fill the required field(s)",
						text: '',
						icon: 'warning',
						showConfirmButton : false,
						confirmButtonText: 'OK',
						confirmButtonColor: "#DD6B55",
						timer : 1500
					}); 
					return;
				}
			}
			// return;
		}
		if(transfer_tab == undefined){
			console.log("*********************************************************************************")
		}

		func_transfer_tab(transfer_tab,$('.tab-pane.active'),'tab')
		console.log({transfer_tab})
	})
	// $('.next-step, .prev-step').on('click', function (e){
	// 	// e.preventDefault();
	// 	var $activeTab = $('.tab-pane.active');
	// 	var $activePill = $('.nav_sel.active');
	// 	var btn;
	// 	var current_tab = $activeTab.attr('id');
	// 	var index = $.inArray(current_tab,tab_sequence);

	// 	console.log({index});
	// 	if ( $(e.target).hasClass('next-step') ){
	// 		// transfer_tab = $activeTab.next('.tab-pane').attr('id');
	// 		transfer_tab = tab_sequence[index+1];			
	// 		btn = "next";
	// 	}else{
	// 		// transfer_tab = $activeTab.prev('.tab-pane').attr('id');
	// 		transfer_tab = tab_sequence[index-1];
	// 		btn = "prev";
	// 	}
	// 	console.log("*********************************************************************************")
	// 	console.log({transfer_tab,btn})
	// 	console.log("*********************************************************************************")

	// 	func_transfer_tab(transfer_tab,$('.tab-pane.active'),btn) //parameter transfer_tab,tab id to be validate,(next,skip, or tab pill)

	// });
	$('.next-step').on('click', function (e){
		// e.preventDefault();
		var $activeTab = $('.tab-pane.active');
		var $activePill = $('.nav_sel.active');
		var btn;
		var current_tab = $activeTab.attr('id');
		var index = $.inArray(current_tab,tab_sequence);

		transfer_tab = tab_sequence[index+1];			
		btn = "next";
		console.log({index});
		console.log("*********************************************************************************")
		console.log({transfer_tab,btn})
		console.log("*********************************************************************************")

		func_transfer_tab(transfer_tab,$('.tab-pane.active'),btn) //parameter transfer_tab,tab id to be validate,(next,skip, or tab pill)

	});
	$('.prev-step').on('click', function (e){
		// e.preventDefault();
		var $activeTab = $('.tab-pane.active');
		var $activePill = $('.nav_sel.active');
		var btn;
		var current_tab = $activeTab.attr('id');
		var index = $.inArray(current_tab,tab_sequence);

		transfer_tab = tab_sequence[index-1];
		btn = "prev";
		console.log("*********************************************************************************")
		console.log({transfer_tab,btn})
		console.log("*********************************************************************************")

		func_transfer_tab(transfer_tab,$('.tab-pane.active'),btn) //parameter transfer_tab,tab id to be validate,(next,skip, or tab pill)

	});
	function func_transfer_tab(transfer_tab,form_to_check,btn){
		console.log("________________________________________________________________________________");
		console.log({transfer_tab,btn})
		console.log("________________________________________________________________________________");
		if(btn != "prev" && btn != "tab"){
			var is_form_complete = check_form_completed(form_to_check);
			console.log({is_form_complete})
			if(!is_form_complete){
				Swal.fire({
					title: "Please fill the required field(s)",
					text: '',
					icon: 'warning',
					showConfirmButton : false,
					confirmButtonText: 'OK',
					confirmButtonColor: "#DD6B55",
					timer : 1500
				}); 
				return;   		
			}
		}
		var $activeTab = $('.tab-pane.active');
		var $activePill = $('.nav_sel.active');

		$activePill.removeClass("active");
		var current_tab = $activeTab.attr('id');

		if(current_tab == "personal_information"){
			personal_clear = true;
		}else if(current_tab == "member_information"){
			member_clear = true;
		}else if(current_tab == "financial_information"){
			financial_clear = true;
		}

		console.log({current_tab,transfer_tab})
		if(transfer_tab == undefined){
			alert(123)
		}
		$('.tab-pane').removeClass('active')
		$('[href="#'+ transfer_tab +'"]').addClass('active');
		$('#'+transfer_tab).addClass('active');

		if(transfer_tab == "review_and_save"){
			set_review_and_save();
		}
	}
	$('#txt_no_shares').on('keyup',function(){
		var val = $(this).val();
		var initial_capital_paidup = val *100;
		console.log({initial_capital_paidup})

		$('#txt_in_capital').val(number_format(initial_capital_paidup));
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