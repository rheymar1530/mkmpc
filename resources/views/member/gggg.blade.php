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
	.nav-pills .active{
		background: #dc3545 linear-gradient(180deg,#e15361,#dc3545) repeat-x !important;

	}
	.nav-pills .nav-link{
		margin-right: 15px !important;
	}
	a.nav_sel:hover{
		color: white !important;
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
</style>


<div class="container main_form" style="margin-top: -20px;">
	<?php $back_link = (request()->get('href') == '')?'/member/index':request()->get('href'); ?>
	<a class="btn btn-default btn-sm" href="{{$back_link}}" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Member List</a>
	<div class="card">
		<!-- <form id="frm_post_member" enctype="multipart/form-data" method="post"> -->
			<div class="card-body col-md-12">
				<h3>Membership Form</h3>
				<div class="row">
					<div class="col-sm-12" style="margin-bottom:10px">
						<div class="form-row">
							<div class="form-group" style="margin-top:5px !important">
								<ul class="nav nav-pills">
									<li class="nav-item"><a class="nav-link active nav_sel" href="#personal_information">Personal Information</a></li>
									<li class="nav-item"><a class="nav-link nav_sel" href="#member_information">Member Information</a></li>
									<li class="nav-item"><a class="nav-link nav_sel" href="#financial_information">Financial Information</a></li>
									<li class="nav-item"><a class="nav-link nav_sel" href="#attachment">Attachment</a></li>
									<li class="nav-item"><a class="nav-link nav_sel" href="#review_and_save" onclick="set_review_and_save()">Review & Save</a></li>
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
						<div class="tab-pane" id="member_information">
							<form id="next_member_info">
								@include('member.member_information')
							</form>

						</div>
						<div class="tab-pane" id="financial_information">
							<form id="next_financial_info">
								@include('member.financial_information')
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
	@endsection
	@push('scripts')
	<script type="text/javascript">
	// let personal_info_fields = ["txt_mem_date","txt_first_name","txt_middle_name","txt_last_name","txt_birthdate","sel_gender","sel_civil_status","txt_email","txt_address","txt_mobile","sel_position","sel_coop_position","sel_pos_stat","txt_school"];

	let personal_info_fields = ["txt_mem_date","txt_first_name","txt_middle_name","txt_last_name","txt_suffix","txt_birthdate","txt_place_birth","sel_gender","sel_civil_status","txt_address","txt_mobile","txt_tin","txt_email","txt_religion","sel_educ_att","sel_branch","sel_mem_type","txt_bod_reso","txt_no_shares","txt_amt_shares","txt_in_capital","sel_income_source","txt_annual_income","txt_spouse","txt_spouse_occupation","txt_no_dependent"];
	var opcode = {{$opcode}}; //0 - add; 1 - update

	var details = jQuery.parseJSON('<?php echo json_encode($details ?? []); ?>');


	if(opcode == 1){
		for(var $i=0;$i<=personal_info_fields.length;$i++){
			var element = $('#'+personal_info_fields[$i]);
			var input_value = (element.hasClass("class_amount"))?number_format(parseFloat(details[element.attr("input-key")])):details[element.attr("input-key")]
			element.val(input_value)
		}
	}
	
	$('#frm_post_member').submit(function(e){
		e.preventDefault();
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
		
	})
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

	function post(){
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
				if(response.RESPONSE_CODE == "success"){
					
					Swal.fire({
						title: "Member successfully saved",
						text: '',
						icon: 'success',
						showConfirmButton : false,
						timer  : 1300
					}).then((result) => {
                        // location.reload()
                        if(opcode == 1){
                        	location.reload();
                        }
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
	
	$(document).on('click','.next-step',function(){
		submit_using = "next_button";
	})
	$(document).on('click','.nav_sel',function(){
		// var form_id = $('.tab-pane.active').find('form').attr('id');
		// submit_using = "tab_button";

		// $('.tab-pane.active').find('.next-step').click();
		$('.tab-pane.active').find('.next-step').click();
		// target_tab = $(this).attr('href');
		
		// $('#'+form_id).submit();
		// console.log({form_id});

	})
	$('#next_personal_info,#next_member_info,#next_financial_info,#next_attachment').submit(function(e){
		e.preventDefault();
		var form_id = $(e.target).attr("id");
		// console.log("----SUBMITTED"+form_id);

		// if(submit_using == "tab_button"){
		// 	console.log("PTANG INAA")
		// 	$('.tab-pane').removeClass('active')
		// 	$('.nav_sel').removeClass('active');
		// 	$('[href="'+ target_tab +'"]').addClass('active');
		// 	$(target_tab).addClass('active');

		// 	if(target_tab =="#review_and_save"){
		// 		set_review_and_save();
		// 	}
		// 	return;
		// }
		console.log("MOOOO")
		var $activeTab = $('.tab-pane.active');
		var $activePill = $('.nav_sel.active');
		$activePill.removeClass("active");

		var transfer_tab = $activeTab.next('.tab-pane').attr('id');

		console.log({transfer_tab});
		
		$('.tab-pane').removeClass('active')
		$('[href="#'+ transfer_tab +'"]').addClass('active');
		$('#'+transfer_tab).addClass('active');

		if(transfer_tab == "review_and_save"){
			set_review_and_save();
		}
	})
	$('.prev-step').on("click",function(){
		var $activeTab = $('.tab-pane.active');
		var transfer_tab = $activeTab.prev('.tab-pane').attr('id');
		// $('[href="#'+ transfer_tab +'"]').click();
	})


    // $('.next-step, .prev-step').on('click', function (e){
    //     var $activeTab = $('.tab-pane.active');
    //     var transfer_tab;
    //     $('.btn-circle.btn-info').removeClass('btn-info').addClass('btn-sel-tab');
    //     if ( $(e.target).hasClass('next-step') ){
    //         transfer_tab = $activeTab.next('.tab-pane').attr('id');
    //     }else{
    //         transfer_tab = $activeTab.prev('.tab-pane').attr('id');
    //     }

    //     console.log({transfer_tab});
    //     $('.tab-pane').removeClass('fade').removeClass('active');
    //     $('#'+transfer_tab).addClass('active');
    //     $('[href="#'+ transfer_tab +'"]').addClass('btn-info').removeClass('btn-sel-tab');
    //     set_review(transfer_tab);
    // });
</script>
@endpush