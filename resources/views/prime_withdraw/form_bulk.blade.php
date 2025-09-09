@extends('adminLTE.admin_template')
@section('content')

<style type="text/css">
	.primt-tbl td, .primt-tbl th{
		padding: 2px;
		font-size: 0.85rem;
	}

	.selected{
		background: #b3ffcc !important;
	}

	.in-amt{
		height: 27px !important;
		width: 100%;
		font-size: 13px;
	}
</style>
<?php

$back_link = (request()->get('href') == '')?'/prime_withdraw':request()->get('href');
$sel_reason = $details->reason_code ?? 1;
?>
<div class="container-fluid section_body">
	<a class="btn bg-gradient-secondary btn-sm" href="{{$back_link}}" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Prime Withdrawal List</a>
	<div class="row">

		@if($admin_view)
		<div class="col-md-4 col-12">
			<div class="card c-border">
				<div class="card-body pt-4">
					<h5 class="lbl_color">Member's Prime</h5>
					<input type="text" class="form-control form-control-border" placeholder="Search from member's prime ...">
					<div class="mt-3" style="max-height:calc(100vh - 100px);overflow-y:auto;">
						<table class="table table-head-fixed table-hover table-bordered mt-3 primt-tbl">
							<thead>
								<tr>
									<th class="table_header_dblue" width="5%"><input type="checkbox" id="sel_all"></th>
									<th class="table_header_dblue">Member</th>
									<th class="table_header_dblue">Balance</th>
								</tr>
							</thead>
							<tbody>
								@foreach($prime_list as $pl)
								<tr class="prime_row" member-id="{{$pl->id_member}}">
									<td><input type="checkbox" class="chk_prime"></td>
									<td class="col-nm">{{$pl->member}}</td>
									<td class="text-right pr-2 col-bal">{{number_format($pl->amount,2)}}</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		@endif

		<div class="col-md-8 col-12">
			<div class="card c-border">
				<div class="card-body">
					<h5 class="lbl_color">Prime Withdrawal @if($opcode == 1)<small>(ID# {{$details->id_prime_withdrawal_batch}})</small>@endif</h5>

					@if(!$admin_view && $opcode == 1)
					<p class="lbl_color mb-0"><b>Member: </b>{{$withdrawals[0]->name}}</p>
					@endif
					<div class="form-row mt-3">
						<div class="form-group col-md-4">
							<label class="mb-0">Date</label>
							<input type="date" class="form-control form-control-border" name="" value="{{$details->transaction_date ?? $current_date}}" id="txt_date">
						</div>
						<div class="form-group col-md-5">
							<label class="mb-0">Reason</label>
							<select class="form-control select2 p-0 in-request" id="sel_reason">
								@foreach($reasons as $r)
								<option value="{{$r->id_prime_withdrawal_reason}}" <?php echo($r->id_prime_withdrawal_reason==$sel_reason)?'selected':''; ?> >{{$r->description}}</option>
								@endforeach
							</select>
						</div>
						<div class="col-md-9 col-12" id="others_holder">
							<input type="text" class="form-control in-request" id="txt_others" value="{{$details->other_reason ?? ''}}" placeholder="Others Reason">
						</div>
					</div>

					
					<div class="row">
						@if($admin_view)
						<div class="col-md-12 col-12 mt-3" style="max-height:calc(100vh - 100px);overflow-y:auto;">
							<table class="table table-head-fixed table-hover table-bordered mt-3 primt-tbl">
								<thead>
									<tr>
										<th class="table_header_dblue">Member</th>
										<th class="table_header_dblue">Balance</th>
										<th class="table_header_dblue" style="width:200px">Withdrawal Amount</th>
										<th class="table_header_dblue" width="5%"></th>
									</tr>
								</thead>
								<tbody id="withdrawal_body">

								</tbody>
							</table>
						</div>
						@else
						<div class="form-group col-md-4 with_row mt-2">
							<label class="mb-0">Amount</label>
							<input type="text" class="form-control form-control-border class_amount in-amt text-right" value="{{number_format($withdrawals[0]->amount ?? '0.00',2)}}">
							<p class="text-muted">Balance: {{number_format($balance ?? 0,2)}}</p>
						</div>						
						@endif

					</div>
					
				</div>
				<div class="card-footer py-2">
					@if($allow_post)
					<button class="btn bg-gradient-success2 btn-md float-right" id="btn_post">Save</button>
					@endif

					@if(MySession::isAdmin() && $opcode == 1)
					@if($details->status <= 1)
					<button type="button" class="btn bg-gradient-primary2 float-right mr-1" onclick="show_status_modal()">Update Status</button>
					@endif
					@endif
				</div>
			</div>
		</div>
	</div>

	@if($opcode == 1)
	@include('prime_withdraw.withdraw_status')

	@endif
	@endsection

	@push('scripts')
	<script type="text/javascript">
		let others_reason_html = $('#txt_others').detach();

		const REASONS = jQuery.parseJSON('<?php echo json_encode($reasons ?? []);?>');
		const REASON_DEF_AMOUNT = format_reason();

		const WITHDRAWALS = jQuery.parseJSON('<?php echo json_encode($withdrawals ?? []);?>');


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
				$('.with_row').find('.in-amt').val(number_format(REASON_DEF_AMOUNT[val]));
			}

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
	</script>

	<script>
		$(document).ready(function(){
			// initialize withdrawables
			$.each(WITHDRAWALS,function(i,item){
				$(`tr.prime_row[member-id="${item.id_member}"]`).find('.chk_prime').trigger('click');
				$(`tr.with_row[member-id="${item.id_member}"]`).find('input.in-amt').val(number_format(item.amount,2));
			})
		})
	</script>

	@if($admin_view)

	<script type="text/javascript">
	// javascript for member prime table
		$('#sel_all').on('click',function(){
			var checked = $(this).prop('checked');
			$('#withdrawal_body').html('');
			$('.chk_prime').prop('checked',false);
			$('tr.prime_row').removeClass('selected');
			if(checked){
				$('.chk_prime').each(function(){
					$(this).trigger('click');
				})
			}


		})
		$(document).on('click','.chk_prime',function(){
			var parent_row = $(this).closest('tr.prime_row');
			var member_id = $(parent_row).attr('member-id');

			if($(this).prop('checked')){
				parent_row.addClass('selected');

				transfer_row({'id_member':member_id,'name' : parent_row.find('.col-nm').text(),'balance' : parent_row.find('.col-bal').text()});

			}else{
				parent_row.removeClass('selected');
				$(`tr.with_row[member-id="${member_id}"]`).remove();
			}
		})

		$(document).on('dblclick','tr.prime_row',function(){
			$(this).find('.chk_prime').trigger('click');
		});

		function transfer_row(d){

			if($(`tr.with_row[member-id="${d.id_member}"]`).length > 0){
				console.log("exists");

				return;
			}
			var sugg_amt = REASON_DEF_AMOUNT[$('#sel_reason').val()];
			let out_row = `<tr class="with_row" member-id=${d.id_member}>
			<td>${d.name}</td>
			<td class="text-right">${d.balance}</td>
			<td class="p-0"><input type="text" class="form-control form-control-border class_amount in-amt text-right" value="${number_format(sugg_amt,2)}"></td>
			<td class="p-0"><button class="btn btn-xs bg-gradient-danger2 w-100 btn-remove-withdrawal"><i class="fas fa-times"></i></button></td>
			</tr>`;

			$('#withdrawal_body').append(out_row);
		}

		$(document).on('click','.btn-remove-withdrawal',function(){
			var parent_row = $(this).closest('.with_row');
			var id = $(parent_row).attr('member-id');

			$(`tr.prime_row[member-id="${id}"]`).find('.chk_prime').trigger('click');
		});

	</script>

	@endif

	<script type="text/javascript">
		$(document).on('click','#btn_post',function(){

		    Swal.fire({
		        title: 'Do you want to save this?',
		        icon: 'warning',
		        showDenyButton: false,
		        showCancelButton: true,
		        confirmButtonText: `Yes`,
		    }).then((result) => {
		        if (result.isConfirmed) {
		            post_withdrawal();
		        } 
		    })  

		});
		function post_withdrawal(){
			let withdrawal_obj = [];
			$('.with_row').each(function(){
				var temp = {};
				temp['id_member'] = $(this).attr('member-id');
				temp['amount'] = decode_number_format($(this).find('.in-amt').val());

				withdrawal_obj.push(temp);
			});

			console.log({withdrawal_obj});

			let ajaxParam = {
				'date' : $('#txt_date').val(),
				'id_reason' : $('#sel_reason').val(),
				'others' : $('#txt_others').val(),
				'withdrawals' : withdrawal_obj,
				'opcode'  : {{$opcode}},
				'id_prime_withdrawal_batch' : {{$details->id_prime_withdrawal_batch ?? 0}}
			};

			$.ajax({
				type          :       'GET',
				url           :       '/prime_withdraw/post',
				data          :       ajaxParam,
				beforeSend :   function(){
					show_loader();
					$('.mandatory').removeClass('mandatory');
				},
				success       :       function(response){
					console.log({response});
					hide_loader();
					if(response.RESPONSE_CODE == "SUCCESS"){
						var html_swal = '';
						var link = "/prime_withdraw/view/"+response.id_prime_withdrawal_batch+"?href="+encodeURIComponent('<?php echo $back_link;?>');


						html_swal = "<a href='"+link+"'>Withdrawal ID# "+response.id_prime_withdrawal_batch+"</a>";

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
					}else if(response.RESPONSE_CODE == "ERROR" || response.RESPONSE_CODE == "INVALID_WITHDRAWAL"){
						Swal.fire({
							title: response.message,
							text: '',
							icon: 'warning',
							showCancelButton : false,
							showConfirmButton : false,
							timer : 2500
						});

						if(response.RESPONSE_CODE == "INVALID_WITHDRAWAL"){
							$invalid = response.invalids;
							for(var $j=0;$j<$invalid.length;$j++){
								$row =$(`tr.with_row[member-id="${$invalid[$j]}"]`)
								$row.addClass('mandatory');
								$row.find('.in-amt').addClass('mandatory');
							}
						}
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
			console.log({ajaxParam});
		}

	</script>
	@endpush