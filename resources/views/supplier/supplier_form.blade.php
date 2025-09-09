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

	.tbl-inputs-text{
		padding-left: 5px !important;
		padding-right: 5px !important;
		/*padding: px !important;*/
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
/*	.wrapper2{
		width: 1300px !important;
		margin: 0 auto;
	}*/
	.text-red{
		color: red;
	}
	.swal2-deny{
		padding: 0.375rem 0.75rem !important;
	}
</style>
<?php

?>
<div class="wrapper2">
	<div class="container" style="margin-top: -20px;" >
		<?php $back_link = (request()->get('href') == '')?'/supplier':request()->get('href'); ?>
		<a class="btn bg-gradient-secondary btn-sm" href="{{$back_link}}" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Supplier List</a>

		<div class="card" id="repayment_main_div">
			<form id="frm_submit_supplier">
				<div class="card-body col-md-12">
					<h3>Supplier 
						@if($opcode == 1)
						<small>(ID# {{$supplier_details->id_supplier}})</small>
						@endif
					</h3>
					<div class="row">
						<div class="col-md-12 p-0" style="margin-top:15px">
							<div class="card">
								<div class="card-header bg-gradient-primary custom_card_header">
									<h5>Supplier Information</h5>
								</div>



								<div class="card-body">
									<div class="form-row">
										<div class="form-group col-md-8" style="">
											<label for="txt_supplier_name">Supplier Name</label>
											<input type="text" name="" class="form-control in_supplier" value="" id="txt_supplier_name" key="name">
										</div>
									</div>	
									<div class="form-row">
										<div class="form-group col-md-8" style="">
											<label for="txt_address">Address</label>
											<input type="text" name="" class="form-control in_supplier" value="" id="txt_address" key="address">
										</div>
									</div>	
									<div class="form-row">
										<div class="form-group col-md-4" style="">
											<label for="txt_contact_no">Mobile/Tel No.</label>
											<input type="text" name="" class="form-control in_supplier" value="" id="txt_contact_no" key="contact_no">
										</div>
									</div>
									<div class="form-row">
										<div class="form-group col-md-4" style="">
											<label for="txt_email">Email</label>
											<input type="text" name="" class="form-control in_supplier" value="" id="txt_email" key="email">
										</div>



									</div>	
									<div class="form-row">

										<div class="form-group col-md-4" style="">
											<label for="txt_tin_no">TIN No.</label>
											<input type="text" name="" class="form-control in_supplier" value="" id="txt_tin_no" key="tin_no">
										</div>
									</div>
									<div class="form-row">
										<div class="form-group col-md-4" style="">
											<label for="sel_type">Type</label>
											<select class="form-control in_supplier p-0" id="sel_type" key="id_supplier_type">
												@foreach($type as $t)
												<option value="{{$t->id_supplier_type}}">{{$t->description}}</option>
												@endforeach
											</select>
										</div>
									</div>
									<div class="form-row">
										<div class="form-group col-md-3" style="">
											<label for="txt_supplier_code">Supplier code</label>
											<input type="text" name="" class="form-control in_supplier" value="" id="txt_supplier_code" key="supplier_code">
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="card-footer">
					<button class="btn  bg-gradient-success float-right">Save</button>
				</div>
			</form>

		</div>
	</div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">

	const $opcode = '<?php echo $opcode; ?>';
	$('#frm_submit_supplier').submit(function(e){
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
		var supplier_data = {};
		$('.in_supplier').each(function(){
			var key = $(this).attr('key');
			var val = $(this).val();
			if($(this).hasClass('class_amount')){
				val = decode_number_format(val);
			}
			supplier_data[key] = val;
		})



		console.log({supplier_data});

		// return;

		$('.mandatory').removeClass('mandatory')

		$.ajax({
			type        :          'POST',
			url         :          '/supplier/post',
			data        :          {'supplier_data' : supplier_data,
			'opcode' : $opcode,
			'id_supplier' : '<?php echo $supplier_details->id_supplier ?? 0 ?>'},
			beforeSend  :          function(){
				show_loader();
			},
			success     :          function(response){
				console.log({response})
				hide_loader()
				if(response.RESPONSE_CODE == "INVALID_INPUT"){
					var invalid_fields = response.invalid_details.invalid_fields;
					$.each(invalid_fields,function(i,item){
						$('.in_supplier[key="'+item+'"]').addClass('mandatory');
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
					var link = "/supplier/view/"+response.id_supplier+"?href="+encodeURIComponent('<?php echo $back_link;?>');
					Swal.fire({
						title: "Supplier Successfully Saved !",
						html : "<a href='"+link+"'>Supplier ID# "+response.id_supplier+"</a>",
						text: '',
						icon: 'success',
						showCancelButton : true,
						confirmButtonText: 'Add More Supplier',
						showDenyButton: false,
						cancelButtonText: 'Close',
						showConfirmButton : true,     
						allowEscapeKey : false,
						allowOutsideClick: false
					}).then((result) => {
						if(result.isConfirmed) {
							window.location = "/supplier/create?href="+encodeURIComponent('<?php echo $back_link;?>');
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

</script>


@if($opcode == 1)
<script type="text/javascript">
	var supplier_details = jQuery.parseJSON('<?php echo json_encode($supplier_details ?? []); ?>');

	$(document).ready(function(){
		$.each(supplier_details,function(key,value){
			var element = $('.in_supplier[key="'+key+'"]');
			var val = value;
			if($(element).hasClass('class_amount')){
				val = number_format(parseFloat(value),2);
			}
			$(element).val(val);
		})


	})
</script>
@endif
@endpush

