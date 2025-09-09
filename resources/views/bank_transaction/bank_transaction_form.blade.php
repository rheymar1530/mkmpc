@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
	.modal-conf {
		max-width:60% !important;
		min-width:60% !important;
		/*margin: auto;*/
	}
	.form-label{
		margin-bottom: 4px !important;
	}
	.class_amount{
		text-align: right;
	}
</style>
<div class="container main_form section_body" style="margin-top:-15px">
	<?php
	$tran_type = [
		1=>"Deposit",
		2=>"Withdraw",
		3=>"Transfer"
	];


	?>

	<?php $back_link = (request()->get('href') == '')?'/bank_transaction':request()->get('href'); ?>
	<a class="btn bg-gradient-secondary btn-sm" href="{{$back_link}}" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Bank Transaction list</a>
	<div class="row">
		<div class="col-md-12">
			<div class="card">			
				<form id="frm_submit_bank_transaction">	
					<div class="card-body">
						<h3 class="text-center head_lbl">Bank Transactions 
							@if($opcode == 1) <small>(ID# {{$transactions->id_bank_transaction}})</small>  
									@if($transactions->status == 10)
                            			<span class="badge badge-danger bgd_cancel">Cancelled</span>
                       				@endif
							@endif</h3>
						@if($opcode == 0 && $credential->is_create)
							<button class="btn btn-sm bg-gradient-info2" type="button" onclick="append_form()"><i class="fa fa-plus"></i>&nbsp;&nbsp;&nbsp;Add Bank Transactions</button>
						@endif
						<div style="max-height: calc(100vh - 177px);overflow-y: auto;overflow-x: auto;margin-top: 15px;padding-right: 10px;" id="form_body">

							<div class="card card-form crd_bank_transaction c-border">
								<div class="card-body">
									@if($opcode == 0)
									<div class="row p-0 mt-n3 mb-2">
										<label class="col-sm-12 control-label col-form-label" style="text-align: right;font-size: 15px;"><a onclick="remove_card(this)"><i class="fa fa-times"></i></a></label>
										<h4 style="margin-top:-20px" class="head_lbl">Transaction #<span class="transaction_count">1</span></h4>
									</div>
									@endif
									<div class="row p-0" style="margin-top: 10px;">

										<div class="col-sm-12 p-1">
											<div class="form-group row p-0" >
												<label class="col-sm-1  text-sm control-label col-form-label" style="text-align: left">Date&nbsp;</label>
												<div class="col-sm-3">
													<input type="date" name="" class="form-control form_input date" key="date" value="{{$transactions->date ?? $current_date	}}" required>
												</div>
											</div>
											<div class="form-group row p-0" >
												<label class="col-sm-1  text-sm control-label col-form-label" style="text-align: left">Type&nbsp;</label>
												<div class="col-sm-4">
													<select class="form-control form_input type p-0 sel_type" key="type" <?php echo ($opcode == 1)?"disabled":""; ?>   required>
														@foreach($tran_type as $val=>$text)
														<?php 

														$def_trans = request()->get('def_trans') ?? 1;
														$def_type = $transactions->type ?? $def_trans;
														$selected = ($def_type == $val)?"selected":"";  
														?>
														<option value="{{$val}}" <?php echo $selected; ?> >{{$text}}</option>
														@endforeach	
													</select>
												</div>
											</div>
											<div class="form-group row p-0" >
												<label class="col-sm-1  text-sm control-label col-form-label" style="text-align: left">Bank <span class="spn_add_lbl">(From)</span>&nbsp;</label>
												<div class="col-sm-4">
													<select class="form-control form_input id_bank p-0" key="id_bank" required>
														@foreach($banks as $bank)
														<?php 
														$def_bank = $transactions->id_bank ?? config('variables.default_bank');
														$selected = ($def_bank == $bank->id_bank)?"selected":"";  
														?>
														<option value="{{$bank->id_bank}}" <?php echo $selected; ?> >{{$bank->bank_name}}</option>
														@endforeach	
													</select>
												</div>
											</div>
											<div class="div_bank_to_holder"></div>
											<div class="form-group row p-0 div_bank_to">
												<label class="col-sm-1  text-sm control-label col-form-label" style="text-align: left">Bank (To)&nbsp;</label>
												<div class="col-sm-4">
													<select class="form-control form_input id_bank p-0" key="id_bank_transfer_to" required>
														@foreach($banks as $bank)
														<?php 
														$def_bank = $transactions->id_bank_transfer_to ?? config('variables.default_bank');;
														$selected = ($def_bank == $bank->id_bank)?"selected":"";  
														?>
														<option value="{{$bank->id_bank}}" <?php echo $selected; ?> >{{$bank->bank_name}}</option>
														@endforeach	
													</select>
												</div>
											</div>
											<div class="form-group row p-0" >
												<label class="col-sm-1  text-sm control-label col-form-label" style="text-align: left">Amount&nbsp;</label>
												<div class="col-sm-4">
													<input type="" name="" class="form-control form_input amount class_amount" key="amount" value="{{number_format(($transactions->amount ?? 0.00),2)}}" required>
												</div>
											</div>
											<div class="name_holder"></div>
											<div class="form-group row p-0 div_name" >
												<label class="col-sm-1  text-sm control-label col-form-label name" style="text-align: left">Name&nbsp;</label>
												<div class="col-sm-6">
													<input type="text" name="" class="form-control form_input" key="name" value="{{$transactions->name ?? ''}}" required>
												</div>
											</div>
											<div class="form-group row p-0" >
												<label class="col-sm-1  text-sm control-label col-form-label" style="text-align: left">Reference&nbsp;</label>
												<div class="col-sm-6">
													<input type="" name="" class="form-control form_input reference" key="reference" value="{{$transactions->reference ?? ''}}" required>
												</div>
											</div>
											<div class="form-group row p-0" >
												<label class="col-sm-1  text-sm control-label col-form-label" style="text-align: left">Remarks&nbsp;</label>
												<div class="col-sm-6">
													<textarea class="form-control form_input reference form-control-border" key="remarks" rows="3" style="resize:none">{{$transactions->remarks ?? ''}}</textarea>
													
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						@if($opcode == 1)
							<div style="padding-top: 10px !important;">
								<span class="text-muted spn_details"><b>Date Created:</b> {{$transactions->date_created}}</span><br>
								@if($transactions->status == 10)
								<span class="text-muted spn_transactions"><b>Date Cancelled:</b> {{$transactions->date_cancelled}}</span><br>
								<span class="text-muted spn_transactions"><b>Cancellation Reason:</b> {{$transactions->cancellation_reason}}</span>
								@endif
							</div>
						@endif
					</div>
					<div class="card-footer">
						@if(($opcode == 0 && $credential->is_create) || ($opcode == 1 && $credential->is_edit && $transactions->status < 10))
							<button class="btn bg-gradient-success float-right" style="margin-left:10px">Save</button>
						@endif
						@if($opcode == 1)


						@if($transactions->id_cash_disbursement > 0)
						<button type="button" class="btn bg-gradient-danger float-right" style="margin-left:10px" onclick="print_page('/cash_disbursement/print/{{$transactions->id_cash_disbursement}}')"><i class="fas fa-print" ></i>&nbsp;Print Cash Disbursement (CDV# {{$transactions->id_cash_disbursement}})</button>

						@elseif($transactions->id_cash_receipt_voucher > 0)
						<button type="button" class="btn bg-gradient-danger float-right" style="margin-left:10px" onclick="print_page('/cash_receipt_voucher/print/{{$transactions->id_cash_receipt_voucher}}')"><i class="fas fa-print" ></i>&nbsp;Print CRV (CRV# {{$transactions->id_cash_receipt_voucher}})</button>
						@else
						<button type="button" class="btn bg-gradient-danger float-right" style="margin-left:10px" onclick="print_page('/journal_voucher/print/{{$transactions->id_journal_voucher}}')"><i class="fas fa-print" ></i>&nbsp;Print Journal Voucher (JV# {{$transactions->id_journal_voucher}})</button>

						@endif
						


						@endif

		
						@if($opcode == 1 && $transactions->status < 10 && $credential->is_cancel)
							<button type="button" class="btn bg-gradient-warning float-right" style="color:white" onclick="show_cancel_modal()"><i class="fas fa-times" ></i>&nbsp;Cancel Bank Transaction</button>
						@endif
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

@if($opcode == 1)
@include('bank_transaction.cancel_modal')
@include('global.print_modal')
@endif
@endsection

@push('scripts')
<script type="text/javascript">
	const $bank_to = '<div class="form-group row p-0 div_bank_to">'+$('.div_bank_to').detach().html()+'</div>';
	const $name = '<div class="form-group row p-0 div_name" >'+$('.div_name').detach().html()+'</div>';
	const form_html = $('#form_body').html();
</script>
@if($opcode == 0)
<script type="text/javascript">
	function remove_card(obj){
		if(opcode == 1){
			return;
		}
		var parent_card = $(obj).closest('.card');
		animate_element(parent_card,2)
		// parent_card.remove()
		print_transaction_count();
	}
	function append_form(){
		$('#form_body').append(form_html);
		// console.log({form_html})
		// $('.sel_type').last().val(3)
		initialize_types($('.sel_type').last())
		// intialize_select2();
		print_transaction_count();
		animate_element($('.crd_bank_transaction').last(),1)

	}
</script>
@endif
<script type="text/javascript">

	var opcode = '{{$opcode}}';
	initialize_types($('.sel_type').last());
	
	$(document).on('change','.sel_type',function(){
		initialize_types($(this));
	})
	function makeid(length){
		var result           = '';
		var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		var charactersLength = characters.length;
		for(var i = 0; i < length; i++ ) {
			result += characters.charAt(Math.floor(Math.random() * charactersLength));
		}
		return result;
	}
	function initialize_types(obj){
		var val = parseInt($(obj).val());
		
		var parent_card = $(obj).closest(".crd_bank_transaction");
		var index = parent_card.index();



		if(val == 3){
			parent_card.find("div.div_bank_to_holder").html($bank_to);
			parent_card.find("span.spn_add_lbl").text("(From)");
			parent_card.find("div.name_holder").html('')	
		}else{
			parent_card.find("div.div_bank_to_holder").html('');
			parent_card.find("span.spn_add_lbl").text('');
			parent_card.find("div.name_holder").html($name)
		}
		
	}

	$(document).on('select2:open', () => {
		document.querySelector('.select2-search__field').focus();
	});	

	function intialize_select2(){		
		$(".id_member").last().select2({
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
	$(function() {
		$.contextMenu({
			selector: '.list_row',
			callback: function(key, options) {
				var m = "clicked: " + key;
				var id_reference= $(this).attr('data-id');

				if(key == "edit"){
					view_details(id_reference)
				}
			},
			items: {
				"edit": {name: "Edit", icon: "fas fa-eye"},
				"sep1": "---------",
				"quit": {name: "Close", icon: "fas fa-times"}
			}
		});   
	});





	function print_transaction_count(){
		$('.transaction_count').each(function(i){
			$(this).text(i+1);
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

	function number_format(number){
		var result =  number.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
		return result;
	}
	function decode_number_format(number){
		var result=number.replace(/\,/g,''); // 1125, but a string, so convert it to number
		result=parseFloat(result,10);
		return result;
	}

	$('#frm_submit_bank_transaction').submit(function(e){
		e.preventDefault();

		var transaction_count  = $('.crd_bank_transaction').length;
		if(transaction_count == 0){
			Swal.fire({
				title: "Please add atleast 1 transaction",
				text: '',
				icon: 'warning',
				showConfirmButton : false,
				confirmButtonColor: "#DD6B55",
				timer : 1500
			});
			return;			
		}
		Swal.fire({
			title: 'Do you want to save this?',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonText: `Save`
		}).then((result) => {
			if (result.isConfirmed) {
				post();
			}
		})	
		
	})

	function post(){
		var transactions = [];
		var $valid_post = true;
		$('.mandatory').removeClass('.mandatory');
		$('.crd_bank_transaction').each(function(){
			var this_form = $(this).find('.form_input');
			var temp_tran = {};
			this_form.each(function(){
				var key = $(this).attr('key');
				if(key=='amount'){

					var val = decode_number_format($(this).val());
					if(val == 0){
						$(this).addClass('mandatory');
						$valid_post = false;
					}
					temp_tran[key] = val;
				}else{
					temp_tran[key] =$(this).val()
				}
				
			})
			transactions.push(temp_tran);

			
		})

		if(!$valid_post){
			Swal.fire({
				title: "Invalid Amount",
				text: '',
				icon: 'warning',
				showConfirmButton : false,
				confirmButtonColor: "#DD6B55",
				timer : 1500
			});
			return;
		}
		$.ajax({
			type            :         'POST',
			url             :         '/bank_transaction/post',
			data            :         {'transactions' : transactions,
			'opcode' : opcode,
			'id_bank_transaction' : '{{ $transactions->id_bank_transaction ?? 0}}'},
			beforeSend      :         function(){
				show_loader()
			},
			success         :         function(response){
				hide_loader();
				console.log({response})
				if(response.RESPONSE_CODE == "SUCCESS"){
    				var link = '/bank_transaction/view/'+response.redirect_id+"?href="+encodeURIComponent('<?php echo $back_link;?>');
    				Swal.fire({
    					title: "Bank Transaction successfully saved",
    					html : "<a href='"+link+"'>Bank Transaction ID# "+response.redirect_id+"</a>",
    					text: '',
    					icon: 'success',
    					showCancelButton : true,
    					confirmButtonText: 'Create More ',
    					showDenyButton: false,
    					denyButtonText: `Print Cash Receipt`,
    					cancelButtonText: 'Close',
    					showConfirmButton : true,     
    					allowEscapeKey : false,
    					allowOutsideClick: false
    				}).then((result) => {
    					if(result.isConfirmed) {
    						window.location =  "/bank_transaction/create?href="+encodeURIComponent('<?php echo $back_link;?>');
    					}else if (result.isDenied) {
                        	var print_obj = {};
                        	print_obj['show_print'] = true;
                        	print_obj['id_cash_receipt'] = response.id_cash_receipt;
                        	window.localStorage.setItem("print_data",JSON.stringify(print_obj));

    						window.location = 	link;
    					}else{
    						window.location = '<?php echo $back_link;?>';
    					}
    				});
					// Swal.fire({
					// 	title: "Bank Transaction successfully saved",
					// 	text: '',
					// 	icon: 'success',
					// 	showConfirmButton : false,
					// 	timer  : 1300
					// }).then((result) => {
					// 	if(response.COMMAND == "RELOAD"){
					// 		window.location = '/bank_transaction/view/'+response.redirect_id;
					// 	}else{
					// 		window.location = "{{$back_link}}";
					// 	}

					// 	                        // if(opcode == 0){ // if add
					// 	                        // 	window.location = '/cash_receipt/view/'+response.id_cash_receipt;
					// 	                        // }else{
					// 	                        // 	location.reload();
					// 	                        // }
					// 	                    });	
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
@endpush
