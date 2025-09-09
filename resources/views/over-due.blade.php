@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
	#tbl_overdues th,#tbl_overdues td{
		padding: 3px;
		font-size: 0.9rem;
	}	
	.b-top{
		border-top: 0.2rem solid;
	}
</style>

<div class="container-fluid">
	<div class="card">
		<div class="card-body">
			<div class="text-center">
				<h3 class="head_lbl">Loan Deficits <small><i>(as of {{date('m/d/Y', strtotime($dt))}})</i></small></h3>
			</div>
			<p class="mb-1 text-muted"></p>
			<table class="table table-bordered table-head-fixed" id="tbl_overdues" width="100%">
				<thead>
					<tr>
						<th class="table_header_dblue"><input type="checkbox" class="float-left mt-1" id="sel_all_member">Member</th>
						<th class="table_header_dblue" width="3%"></th>
						<th class="table_header_dblue">Reference</th>
						<th class="table_header_dblue">Loan</th>
						<th class="table_header_dblue">Outstanding Balance</th>
						<th class="table_header_dblue">Amount Paid</th>
						<th class="table_header_dblue">Deficit Amount</th>
						<th class="table_header_dblue">Notif Status</th>
						<th class="table_header_dblue" width="3%"></th>
					</tr>
				</thead>
				<tbody>
					@foreach($overdues as $overdue)
					@foreach($overdue as $c=>$od)
					<?php
						$class = ($c==0)?'b-top':'';
						switch($od->notif_status){
							case 0:
								$bdge = "info";
								$text = "Processing";
								break;
							case 1:
								$bdge = "info";
								$text = "Processing";
								break;
							case 2:
								$bdge = "primary";
								$text = "Sending";
								break;
							case 3:
								$bdge = "success";
								$text = "Sent";
								break;
							default:
								break;
						}
					?>
					<tr class="{{$class}} row-deficit" data-token="{{$od->id_loan}}" data-member="{{$od->id_member}}">
						@if($c==0)
						<td rowspan="{{count($overdue)}}" class="memb_span" member-data="{{$od->id_member}}"><input type="checkbox" class="mr-2 chk_sel_member" value="{{$od->id_member}}">{{$od->member_name}}<br><span class="text-muted text-sm ml-4"><i>{{$od->email}}</i></span></td>
						@endif
						<td class="text-center"><input type="checkbox" class="chk_def_row" member-data="{{$od->id_member}}"></td>
						<td class="text-center"><a href="/loan/application/approval/{{$od->loan_token}}" target="_blank">{{$od->id_loan}}</a></td>
						<td>{{$od->loan_name}}</td>
						<td>{{number_format($od->month_total_due,2)}}</td>
						<td>{{number_format($od->current_payment,2)}}</td>
						<td>{{number_format($od->total_due,2)}}</td>
						<td>
							@if($od->notif_status < 10)
							<span class="badge text-sm badge-{{$bdge}}">{{$text}}</span>
							@endif
						</td>
						<td class="text-center">
							@if($od->notif_status <= 1)
							<a class="btn btn-danger btn-xs" onclick="cancel_sending({{$od->id_loan}})"><i class="fa fa-ban"></i></a>
							@endif
						</td>
					</tr>

					@endforeach
					<tr>
						<td colspan="9"></td>
					</tr>
					@endforeach
				</tbody>
			</table>
			
		</div>
		<div class="card-footer p-2">
			<button class="btn bg-gradient-success2 float-right" onclick="send_notif()">Send Notification</button>
		</div>
	</div>

</div>
@endsection

@push('scripts')
<script type="text/javascript">
	$(document).on('click','input.chk_def_row',function(){
		var parent_row = $(this).closest('.row-deficit');
		var token = parent_row.attr('data-token');

		var member_id = $(this).attr('member-data');

		var checked = $(this).prop('checked');

		if(checked){
			parent_row.addClass('table-success');
		}else{
			parent_row.removeClass('table-success');
		}

		d = $(`.row-deficit.table-success[data-member="${member_id}"]`).length
		if(d > 0){
			$(`.memb_span[member-data="${member_id}"]`).addClass('table-success');
		}else{
			$(`.memb_span[member-data="${member_id}"]`).removeClass('table-success');
		}
		
	});

	$(document).on('click','input.chk_sel_member',function(){
		var checked = $(this).prop('checked');
		var val = $(this).val();
		$(`.chk_def_row[member-data="${val}"]`).prop('checked',!checked).trigger('click');

		// .closest('tr.row-deficit').addClass('table-success')

	
		console.log({checked,val});
	})
	$(document).on('click','#sel_all_member',function(){
		var c = $(this).prop('checked');
		$('.chk_sel_member').prop('checked',!c).trigger('click');

	})

	function send_notif(){
		Swal.fire({
			title: 'Do you want to send notification on this loans?',
			icon: 'warning',
			showDenyButton: false,
			showCancelButton: true,
			confirmButtonText: `Save`,
		}).then((result) => {
			if (result.isConfirmed) {
				post();
			} 
		})	
	}
	function post(){
		var token_array = [];
		$('tr.row-deficit.table-success').each(function(){
			var token = $(this).attr('data-token');
			token_array.push(token);
		})
		$.ajax({

			type     :       'GET',
			url      :       '/overdue/post/push-notif',
			data     :       {'id_loans' : token_array},
			beforeSend :     function(){
							 show_loader();
			},
			success  :        function(response){
							  console.log({response});
							  hide_loader();
							if(response.RESPONSE_CODE == "ERROR"){
								var emails = [];
								var out = '<ul>';
								$.each(response.emails_invalid,function(i,item){
									out += `<li class="text-left">${item.member_name}</li>`
								});
								out += '</ul>';

								var timer = out.length > 0 ?0:3000;

								Swal.fire({
									title: response.message,
									html : out,
									icon: 'warning',
									showCancelButton : false,
									showConfirmButton : false,
									timer : timer
								}).then((result)=>{

								});
							}else if(response.RESPONSE_CODE == "SUCCESS"){
								
								Swal.fire({
									title: "Notification successfully processed",
									text : '',
									text: '',
									icon: 'success',
									showCancelButton : true,
									cancelButtonText: 'Close',
									
									showDenyButton: false,
						
									showConfirmButton : false,   
									timer : 2500  

								}).then((result) => {
									location.reload();
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

	function cancel_sending(id_loan){
		Swal.fire({
			title: 'Do you want to cancel this notification?',
			icon: 'warning',
			showDenyButton: false,
			showCancelButton: true,
			confirmButtonText: `Save`,
		}).then((result) => {
			if (result.isConfirmed) {
				post_cancel(id_loan);
			} 
		})	
	}

	function post_cancel(id_loan){
		$.ajax({
			type        :        'POST',
			url         :        '/overdue/post-cancel',
			data        :        {'id_loan' : id_loan},
			beforeSend  :        function(){
								 show_loader();
			},
			success     :       function(response){
								console.log({response});
								hide_loader();
								if(response.RESPONSE_CODE == "ERROR"){
									
									Swal.fire({
										title: response.message,
										text : '',
										icon: 'warning',
										showCancelButton : false,
										showConfirmButton : false,
										timer : 3000
									});
								}else if(response.RESPONSE_CODE == "SUCCESS"){
									
									Swal.fire({
										title: "Notification successfully cancelled",
										text : '',
										text: '',
										icon: 'success',
										showCancelButton : true,
										cancelButtonText: 'Close',
										
										showDenyButton: false,
							
										showConfirmButton : false,   
										timer : 2500  

									}).then((result) => {
										location.reload();
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
@endpush