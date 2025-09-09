@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
	@media (min-width: 1200px) {
		.container{
			max-width: 780px;
		}
	}
	.swal2-deny{
		padding: 0.375rem 0.75rem !important;
	}
</style>
<?php


$sel_reason = $details->reason_code ?? 1;

dd($sel_reason);
?>
<div class="container section_body">
	<?php $back_link = (request()->get('href') == '')?'/prime_withdraw':request()->get('href'); ?>
	<a class="btn bg-gradient-secondary btn-sm" href="{{$back_link}}" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Prime Withdrawal List</a>
	<form id="frm_post_prime">
		<div class="card">
			<div class="card-body">
				<h3 class="text-center head_lbl">Prime Withdrawal
					@if($opcode == 1)
					<small>(ID# {{$details->id_prime_withdrawal}})</small>
					@if($details->status > 0)
					<span class="badge badge-{{($details->status ==1)?'primary':(($details->status==2)?'success':'danger')}}">{{$details->status_desc}}</span>
					@endif
					@endif
				</h3>

				@if(MySession::isAdmin())
				<div class="row">
					<div class="col-md-8 col-12">
						<label class="mb-0">Member</label>
						<select class="form-control select2 in-request" id="sel_member" required>
							@if(isset($selected_member))
							<option value="{{$selected_member->tag_id}}">{{$selected_member->tag_value}}</option>
							@endif
						</select>
					</div>
				</div>
				@endif

				<div class="row mt-2 d-flex align-items-end">
					<div class="col-md-5 col-12">
						<label class="mb-0">Reason</label>
						<select class="form-control select2 p-0 in-request" id="sel_reason">
							@foreach($reasons as $r)
							<option value="{{$r->id_prime_withdrawal_reason}}" <?php echo($r->id_prime_withdrawal_reason==$sel_reason)?'selected':''; ?> >{{$r->description}}</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-7 col-12" id="others_holder">
						<input type="text" class="form-control in-request" id="text_others" value="{{$details->other_reason ?? ''}}">
					</div>
				</div>

				<div class="row mt-2 d-flex align-items-end">
					<div class="col-md-4 col-12">
						<label class="mb-0">Amount</label>

						<input type="text" class="form-control class_amount text-right in-request" id="txt_amount" value="{{number_format(($details->amount ?? 0),2)}}">

					</div>
					@if($allow_post)
					<div class="col-md-4 col-12">
						<p class="mb-0" id="prime_amt_holder">
							@if(isset($current_prime))
							({{number_format($current_prime,2)}})
							@endif
						</p>
					</div>
					@endif
				</div>
			</div>
			<div class="card-footer  py-1">
				@if($allow_post)
				<button type="submit" class="btn bg-gradient-success2 float-right">Save</button>
				@endif
				@if($opcode == 1 && MySession::isAdmin())
				@if($details->status <= 1)
				<button type="button" class="btn bg-gradient-primary2 float-right mr-1" onclick="show_status_modal()">Update Status</button>
				@endif

				@if($opcode == 1 && MySession::isadmin() && $details->id_cash_disbursement > 0)

				<button type="button" class="btn bg-gradient-danger2 float-right" style="margin-right:10px" onclick="print_page('/cash_disbursement/print/{{$details->id_cash_disbursement}}')"><i class="fas fa-print" ></i>&nbsp;Print CDV (#{{$details->id_cash_disbursement}})</button>
				@endif
				@endif
			</div>
		</div>
	</form>
</div>

@if($opcode == 1)
@include('prime_withdraw.withdraw_status')
@include('global.print_modal')
@endif
@endsection

@push('scripts')
@if($opcode == 1)
<script>
	$(document).ready(function(){
		var redirect_data = jQuery.parseJSON(localStorage.getItem("redirect_print_entry"));

		if(redirect_data != null){
			if(redirect_data.show_print_entry == 1){
				print_page('/cash_disbursement/print/{{$details->id_cash_disbursement}}');
				localStorage.removeItem("redirect_print_entry");
			}
		}
	})
</script>
@endif
<script type="text/javascript">
	let others_reason_html = $('#text_others').detach();

	const REASONS = jQuery.parseJSON('<?php echo json_encode($reasons ?? []);?>');
	const REASON_DEF_AMOUNT = format_reason();


	let CURRENT_PRIME = parseFloat('{{$current_prime ?? 0}}');


	var init_state = ('{{$opcode}}' == '1')?false:true;


	init_reason(init_state);

	$(document).on('change','#sel_reason',function(){
		init_reason(true);
	});

	function format_reason(){
		var out = [];
		$.each(REASONS,function(i,item){
			out[item.id_prime_withdrawal_reason] = item.default_amount;
		});

		return out;
	}

	function init_reason(from_change){
		var val = $('#sel_reason').val();
		if(val == 5){
			$('#others_holder').html(others_reason_html);
		}else{
			$('#others_holder').html('');
		}

		if(from_change){
			$('#txt_amount').val(number_format(REASON_DEF_AMOUNT[val]));
		}

	}


</script>

@if($allow_post)
<script type="text/javascript">

	$(document).on('ready',function(){
		
	});
	intialize_select2();
	function intialize_select2(){		
		$link = '/search_member'
		$("#sel_member").select2({
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

	$(document).on('change','#sel_member',function(){
		$.ajax({
			type       :     'GET',
			url        :     '/prime_withdraw/get_member_prime',
			data       :     {'id_member' : $(this).val()},
			success    :     function(response){
				console.log({response});
				$('#prime_amt_holder').text(`(${number_format(response.prime_amount,2)})`);
				CURRENT_PRIME = response.prime_amount;
				$('#txt_amount').val(number_format(0,2));	

				init_reason(true);
				// $('#txt_amount').val(number_format(CURRENT_PRIME,2));	
			}
		})
	})
	$(document).on('select2:open', (e) => {
		const selectId = e.target.id
		$(".select2-search__field[aria-controls='select2-" + selectId + "-results']").each(function(key,value,){
			value.focus()
		})
	}) 
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
	$('#frm_post_prime').on('submit',function(e){
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
		var post_data = {
			'id_member' :  $('#sel_member').val(),
			'reason' : $('#sel_reason').val(),
			'others' : $('#text_others').val(),
			'amount' : decode_number_format($('#txt_amount').val()),
			'opcode' : '{{$opcode}}',
			'id_prime_withdrawal' : '{{$details->id_prime_withdrawal ?? 0}}'
		}

		$.ajax({
			type          :       'POST',
			url           :       '/prime_withdraw/post',
			data          :       post_data,
			beforeSend :   function(){
				show_loader();
				$('.mandatory').removeClass('mandatory');
			},
			success       :       function(response){
				console.log({response});
				hide_loader();
				if(response.RESPONSE_CODE == "SUCCESS"){
					var html_swal = '';
					var link = "/prime_withdraw/view/"+response.id_prime_withdrawal+"?href="+encodeURIComponent('<?php echo $back_link;?>');


					html_swal = "<a href='"+link+"'>Withdrawal ID# "+response.id_prime_withdrawal+"</a>";

					Swal.fire({
						title: "Withdrawal Successfully Saved",
						html : html_swal,
						text: '',
						icon: 'success',
						showCancelButton : true,
						confirmButtonText: 'Create Another Withdrawal',
						cancelButtonText: 'Back to List of Withdrawal',
						showDenyButton: false,

						showConfirmButton : true,     
						allowEscapeKey : false,
						allowOutsideClick: false
					}).then((result) => {
						if(result.isConfirmed) {
							window.location = "/prime_withdraw/create?href="+encodeURIComponent('<?php echo $back_link;?>');
						}else{
							window.location = '<?php echo $back_link;?>';
						}
					});	
				}else if(response.RESPONSE_CODE == "ERROR"){
					Swal.fire({
						title: response.message,
						text: '',
						icon: 'warning',
						showCancelButton : false,
						showConfirmButton : false,
						timer : 2500
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

		console.log({post_data});
	}
</script>
@else
<script type="text/javascript">
	$('#frm_post_prime').on('submit',function(e){
		e.preventDefault();
	})

	$(function(){
		$('.in-request').prop('disabled',true);
	})
</script>
@endif

@endpush