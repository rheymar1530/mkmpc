@extends('adminLTE.admin_template')
@section('content')

<style type="text/css">
	@media (min-width: 1200px) {
		.container{
			max-width: 1000px;
		}
	}
	.primt-tbl td, .primt-tbl th{
		padding: 2px;
		font-size: 0.85rem;
	}
	.swal2-deny{
		padding: 0.375rem 0.75rem !important;
	}
</style>

<div class="container section_body">
	<?php $back_link = (request()->get('href') == '')?'/prime_withdraw':request()->get('href'); ?>
	<a class="btn bg-gradient-secondary btn-sm" href="{{$back_link}}" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Prime Withdrawal List</a>
	<form id="frm_post_prime">
		<div class="card">
			<div class="card-body">
				<h3 class="text-center head_lbl">Prime Withdrawal <small>(ID# {{$details->id_prime_withdrawal_batch}})</small><span class="badge badge-{{($details->status ==1)?'primary':(($details->status==2)?'success':'danger')}} ml-2">{{$details->status_desc}}</span></h3>
				<p class="mb-0"><b>Transaction Date: </b>{{$details->trans_date}}</p>
				<p class="mb-0"><b>Reason: </b>{{$details->reason}}</p>



				<div class="mt-3" style="max-height:calc(100vh - 100px);overflow-y:auto;">
					<table class="table table-head-fixed table-hover table-bordered mt-3 primt-tbl">
						<thead>
							<tr>
								<th class="table_header_dblue">Reference</th>
								<th class="table_header_dblue">Member</th>
								<th class="table_header_dblue">Amount</th>

								@if(MySession::isAdmin())

								<th class="table_header_dblue" width="25%"></th>
								@endif
							</tr>
						</thead>
						<tbody>
							@foreach($withdrawals as $w)
							<tr>
								<td class="text-center">{{$w->id_prime_withdrawal}} @if($w->status == 10)<i class="text-danger">(CANCELLED)</i>@endif</td>
								<td>{{$w->name}}</td>
								<td class="text-right pr-2">{{number_format($w->amount,2)}}</td>
								@if(MySession::isAdmin())
								<td style="white-space:normal !important;">

									@if($w->id_cash_disbursement > 0)
									<a class="btn btn-xs bg-gradient-success2" onclick="print_page('/cash_disbursement/print/{{$w->id_cash_disbursement}}')">Print CDV [#{{$w->id_cash_disbursement}}]</a>
									@endif

									@if($details->status != 5 && MySession::isSuperAdmin() && $w->status != 10)
									<a class="btn btn-xs bg-gradient-danger2" onclick="cancel_withdrawal('{{$w->id_prime_withdrawal}}')">Cancel Request</a>
									@endif
								</td>
								@endif
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
			@if($details->status == 1 && MySession::isAdmin())
			<div class="card-footer  py-1">
				<button type="button" class="btn bg-gradient-primary2 float-right mr-1" onclick="show_status_modal()">Update Status</button>
			</div>
			@elseif($details->status == 2 && MySession::isAdmin())
			<div class="card-footer  py-1">
				<button type="button" class="btn bg-gradient-danger2 float-right mr-1" onclick="print_page('/prime_withdraw/export-batch/{{$details->id_prime_withdrawal_batch}}')"><i class="fa fa-print"></i>&nbsp;Print Summary</button>
			</div>

			@endif
		</div>
	</form>
</div>

@if($details->status == 1)
@include('prime_withdraw.withdraw_status')

@elseif($details->status == 2)

@endif
@include('global.print_modal')

@endsection

@push('scripts')
@if(MySession::isSuperAdmin())
<script type="text/javascript">
	function cancel_withdrawal(id_prime_withdrawal){
		Swal.fire({
			title: 'Do you want to cancel this?',
			icon: 'warning',
			showDenyButton: false,
			showCancelButton: true,
			confirmButtonText: `Yes`,
		}).then((result) => {
			if (result.isConfirmed) {
				post_cancel(id_prime_withdrawal);
			} 
		})  
	}

	function post_cancel(id_prime_withdrawal){
		$.ajax({
			type         :       'POST',
			url          :       '/prime_withdraw/individual/cancel',
			data         :       {'id_prime_withdrawal' : id_prime_withdrawal},
			beforeSend   :       function(){
				show_loader();
			},
			success      :       function(response){
				hide_loader();
				if(response.RESPONSE_CODE == "SUCCESS"){
					Swal.fire({
						title: "Withdrawal Successfully Cancelled",
						text: '',
						icon: 'success',
						showCancelButton : false,
						showConfirmButton : false,
						timer : 2000
					}).then((result) => {   

						location.reload();
					});                              
				}
			},
			error: function(xhr, status, error) {
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

	}
</script>
@endif
@endpush