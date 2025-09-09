@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
	.head_crd .fa {
		transition: .3s transform ease-in-out;
	}
	.head_crd .collapsed .fa {
		transform: rotate(90deg);
		padding-right: 3px;
	}
	.btn-circle {
		width: 25px;
		height: 25px;
		text-align: center;
		padding: 6px 0;
		font-size: 13px;
		line-height: 1;
		border-radius: 15px;
		
	}
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
	.tbl_loans tr>th ,.tbl_fees tr>th,.tbl-inputs tr>th{
		padding: 5px;
		padding-left: 5px;
		padding-right: 5px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 14px;
		text-align: center;
	}
	.tbl-inputs-text{
		padding-left: 5px !important;
		padding-right: 5px !important;
		/*padding: px !important;*/
	}
	.tbl_loans tr>td,.tbl_fees tr>td,.tbl_repayment_display tr>td{
		padding: 3px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 15px;
	}
	.tbl-inputs tr>td{
		padding: 0px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 15px;
	}
	.frm_loans,.frm-requirements{
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


	.text_center{
		text-align: center;
	}
	.text_bold{
		font-weight: bold;
	}



	.wrapper2{
		width: 1300px !important;
		margin: 0 auto;
	}

	.swal2-deny{
		padding: 0.375rem 0.75rem !important;
	}

	.highlight_row{
		background: #ff4d4d;
		color: white;
		font-weight: bold;
	}
	.crd_header a{
		font-weight: bold !important;
		color: white !important;
	}
	.txt_change_amount{
		font-weight: bold;
		font-size: 14px;
	}
</style>
<?php
$transaction_type = [
	1=>"Cash",
	2=>"ATM Swipe"
];
?>
<!-- <div class="wrapper2"> -->
	<div class="container-fluid main_form section_body" style="margin-top:-15px">
		<?php $back_link = (request()->get('href') == '')?'/change':request()->get('href'); ?>
		<a class="btn bg-gradient-secondary btn-sm" href="{{$back_link}}" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Loan Payment Change List</a>
		@if(($opcode == 1) && $repayment_change->status < 10)
		<a class="btn bg-gradient-danger btn-sm float-right" style="margin-bottom:10px" onclick="cancel_change()"><i class="fas fa-times"></i>&nbsp;&nbsp;Cancel Released Change</a>
		@endif
		<div class="card">
			<div class="card-body col-md-12">
				<h3 class="head_lbl text-center">Loan Payment Change 
					@if($opcode == 1)
					{{'ID# '.$repayment_change->id_repayment_change}}
					@endif

					@if($opcode == 1 && $repayment_change->status >= 10)
					<span class="badge badge-danger">Cancelled</span>
					@endif

				</h3>
				@if($opcode == 0)
				<div class="row">
					<div class="col-12 col-sm-6 col-md-4">
						<div class="info-box">
							<span class="info-box-icon bg-gradient-success elevation-1"><i class="fas fa-money-bill-wave"></i></span>
							<div class="info-box-content">
								<span class="info-box-text">Remaining Change Total</span>
								<span class="info-box-number" id="spn_remaining_change">


								</span>
							</div>
							<!-- /.info-box-content -->
						</div>
						<!-- /.info-box -->
					</div>
					<!-- /.col -->

					<!-- /.col -->

					<!-- fix for small devices only -->
					<div class="clearfix hidden-md-up"></div>


					<!-- /.col -->
				</div>
				@endif
				<div class="row">
					<div class="col-md-12 row">	
						@if($opcode == 0)
						<div class="col-md-12">
							<div class="card c-border">


								<div class="card-body">
						<h5 class="head_crd text-center lbl_color">
								Change Payable
								<span class="float-right">	
									<a href=".collapse-crd" class="btn btn-success btn-circle" data-toggle="collapse" data-target=".collapse-crd" aria-expanded="true" aria-controls=".collapse-crd" style="margin-right:10px"><i class="fa fa-chevron-down"></i></a>

								</span>
							</h5>
									<div class="row  collapse-crd show">

										<div class="col-md-8" style="margin-top:20px">
											<div class="form-group">
												<input type="text"  id="txt_table_search" class="form-control col-md-12" autofocus="" placeholder="Search ...">
											</div>
										</div>
										<div class="col-md-12" >
											<div class="form-group col-md-12 p-0" style="margin-bottom:unset;">
												<div class="table-responsive" style="max-height: calc(100vh - 200px);overflow-y: auto;margin-top: 5px !important;overflow-x: auto">
													<table class="table table-bordered table-stripped table-head-fixed tbl-inputs" style="white-space: nowrap;" id="tbl_change">
														<thead>
															<tr>
																<th width="30px" class="table_header_dblue"><input type="checkbox" name="" id="select_all_change"></th>
																<th class="table_header_dblue">ID Loan Payment</th>
																<th class="table_header_dblue">Transaction Date</th>
																<th class="table_header_dblue">Payee</th>
																<th class="table_header_dblue">Loan Reference</th>
																<th class="table_header_dblue">Swiping Amount</th>
																<th class="table_header_dblue">Total Amount Paid</th>
																<th class="table_header_dblue">Change</th>
																<th class="table_header_dblue">Change Released</th>
																<th class="table_header_dblue">Remaining Change</th>
																<!-- <th class="table_header_dblue">Change Input</th> -->
															</tr>
														</thead>
														<tbody id="change_body">
															@if(count($change_list) > 0)
															@foreach($change_list as $item)
															<tr class="row_change_records" remaining-change="{{$item->remaining_change}}" reference_repayment="{{$item->id_repayment_transaction}}">
																<td class="text-center col_chk"><input type="checkbox" name="" class="chk_change"></td>
																<td class="tbl-inputs-text text-center">{{$item->id_repayment_transaction}}</td>
																<td class="tbl-inputs-text">{{$item->transaction_date}}</td>
																<td class="tbl-inputs-text">{{$item->borrower}}</td>
																<td class="tbl-inputs-text">{{$item->loan_ids}}</td>
																<td class="tbl-inputs-text class_amount" >{{number_format($item->swiping_amount,2)}}</td>
																<td class="tbl-inputs-text class_amount" >{{number_format($item->total_payment,2)}}</td>
																<td class="tbl-inputs-text class_amount" >{{number_format($item->change,2)}}</td>
																<td class="tbl-inputs-text class_amount" >{{number_format($item->change_released,2)}}</td>
																<td class="tbl-inputs-text class_amount" >{{number_format($item->remaining_change,2)}}</td>
																
															</tr>
															@endforeach
															@else
															<tr>
																<td colspan="10" style="text-align: center;">No Record</td>
															</tr>		
															@endif
														</tbody>

													</table>    
												</div>  
											</div>

										</div>										
									</div>
								</div>
							</div>
						</div>
						@endif
						<div class="col-md-12">

							<div class="card c-border">

								<div class="card-body">
									<h5 class="lbl_color text-center">Change Release</h5>
									<div class="row">
										<div class="col-md-12" style="margin-top:15px">

											<div class="form-row">
												<div class="form-group col-md-3">
													<label for="txt_transaction_date">Date</label>
													<input type="date" name="" class="form-control" value="{{$repayment_change->date ?? $current_date}}" id="txt_transaction_date">
												</div>
											</div>

										</div>
										<div class="col-md-12" >
											<div class="form-group col-md-12 p-0" style="margin-bottom:unset;">
												<div class="table-responsive" style="max-height: calc(100vh - 200px);overflow-y: auto;margin-top: 5px !important;overflow-x: auto">
													<table class="table table-bordered table-stripped table-head-fixed tbl-inputs" style="white-space: nowrap;" id="tbl_change_post">
														<thead>
															<tr>
																<th class="table_header_dblue">ID Loan Payment</th>
																<th class="table_header_dblue">Transaction Date</th>
																<th class="table_header_dblue">Payee</th>
																<th class="table_header_dblue">Loan Reference</th>
																<th class="table_header_dblue">Swiping Amount</th>
																<th class="table_header_dblue">Total Amount Paid</th>
																<th class="table_header_dblue">Change</th>
																<th class="table_header_dblue">Change Released</th>
																@if($opcode == 0 || $type == 2)
																<th class="table_header_dblue">Remaining Change</th>
																@endif
																
																<th class="table_header_dblue"></th>
																
															</tr>
														</thead>
														<tbody id="change_post_body">

															@if($opcode == 1  && isset($repayment_change))
															<tr class="change_row_post" remaining-change="{{$repayment_change->remaining_change}}" reference_repayment="{{$repayment_change->id_repayment_transaction}}">
																<td class="tbl-inputs-text text-center">{{$repayment_change->id_repayment_transaction}}</td>
																<td class="tbl-inputs-text">{{$repayment_change->transaction_date}}</td>
																<td class="tbl-inputs-text">{{$repayment_change->payee}}</td>
																<td class="tbl-inputs-text">{{$repayment_change->loan_ids}}</td>
																<td class="tbl-inputs-text class_amount" >{{number_format($repayment_change->swiping_amount,2)}}</td>
																<td class="tbl-inputs-text class_amount" >{{number_format($repayment_change->total_payment,2)}}</td>
																<td class="tbl-inputs-text class_amount" >{{number_format($repayment_change->change,2)}}</td>
																<td class="tbl-inputs-text class_amount" >{{number_format($repayment_change->change_released,2)}}</td>
																@if($opcode == 0 || $type == 2)
																<td class="tbl-inputs-text class_amount" >{{number_format($repayment_change->remaining_change,2)}}</td>
																@endif
																<td><input type="text" name="" required class="form-control class_amount frm_loans txt_change_amount" balance="{{$repayment_change->remaining_change}}" key="amount" value="{{number_format($repayment_change->amount,2)}}" <?php echo ($type==1)?"disabled":""; ?>></td>

															</tr>
															@else
															<tr class="no_row">
																<td colspan="10" style="text-align: center;">No Selected Transaction</td>
															</tr>
															@endif
														</tbody>

													</table>    
												</div>  
											</div>
											@if($opcode == 1)
											@if($repayment_change->status == 10)
											<p>{{$repayment_change->cancellation_reason}}</p>
											@endif
											@endif
										</div>										
									</div>
								</div>
							</div>
						</div>
					</div>	

				</div>
			</div>

			<div class="card-footer">

				@if($opcode == 1)

				<button type="button" class="btn bg-gradient-danger2 float-right" style="margin-left:10px" onclick="print_page('/cash_disbursement/print/{{$repayment_change->id_cash_disbursement}}')"><i class="fas fa-print" ></i>&nbsp;Print CDV (CDV# {{$repayment_change->id_cash_disbursement}})</button>
				@endif
				@if($allow_post)
				<button class="btn bg-gradient-success2 float-right" onclick="submit_change()">Save Change Release</button>
				@endif

				@if($opcode == 1 && $type ==1 && $repayment_change->status < 10)
				<a class="btn bg-gradient-success2 float-right" href="/change/edit/{{urlencode($repayment_change->id_repayment_change)}}?href={{$back_link}}">Edit Change Release</a>

				@endif
			</div>
		</div>
	</div>
	<!-- </div> -->

	@if($opcode == 1)
	@include('change.status_modal')

	@include('global.print_modal')

	@endif
	@endsection
	@push('scripts')

	<script type="text/javascript">
		var no_trans = `	<tr class="no_row">
		<td colspan="10" style="text-align: center;">No Selected Transaction</td>
		</tr>`;
		const $opcode = '<?php echo $opcode; ?>';
		const $id_repayment_change = '<?php echo $repayment_change->id_repayment_change ?? 0; ?>';
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
		$(document).ready(function(){
			sum_remaining_change()


		})
		$(document).on('click','#select_all_change',function(){
			var checked = $(this).prop('checked');

			$('input.chk_change').each(function(){
				$(this).prop('checked',checked);
				append_post_change($(this));
			})
			console.log({checked})
		})
	</script>
	<script type="text/javascript">
		var opcode = 0;
		const cancel_type = 1;
		function sum_remaining_change(){
			var remaining_change =0;
			$('.row_change_records').each(function(){
				if($(this).is(':visible')){
					remaining_change += parseFloat($(this).attr('remaining-change'))
				}
			})
			remaining_change = number_format(remaining_change,2);

			$('#spn_remaining_change').text(remaining_change);
			console.log({remaining_change});
		}

		$("#txt_table_search").on("keyup", function() {
			var value = $(this).val().toLowerCase();
			console.log({value});
			$("#tbl_change tr.row_change_records").filter(function() {

				$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
			});
			sum_remaining_change()
		});

		$(document).on('click','.chk_change',function(){
			append_post_change($(this));
		})

		function append_post_change(obj){
			var checked = $(obj).prop('checked');
			var parent_row = $(obj).closest('tr.row_change_records');
			var reference = parent_row.attr('reference_repayment');
			var due  = parent_row.attr('remaining-change')

			if(checked){
				$('tr.no_row').remove();
				parent_row.addClass('highlight_row');
				var ll = parent_row.clone();
				$('#change_post_body').append(ll);
				$('tr.row_change_records').last().removeClass('row_change_records highlight_row').addClass('change_row_post');
				var last_added_change = $('tr.change_row_post[reference_repayment="'+reference+'"]');
				last_added_change.find('.col_chk').remove();
				last_added_change.append('<td><input type="text" name="" required class="form-control class_amount frm_loans txt_change_amount" balance="'+due+'" key="amount" value="'+number_format(parseFloat(due),2)+'"></td>')
				animate_element(last_added_change,1);


			}else{
				parent_row.removeClass('highlight_row');
				$('tr.change_row_post[reference_repayment="'+reference+'"]').remove();

				if($('tr.change_row_post').length == 0){
					$('#change_post_body').html(no_trans);
				}
			}
		}
		function submit_change() {
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
		}
		function post(){
			var change_post = [];

			$('tr.change_row_post').each(function(){
				var temp = {};
				var $amount = decode_number_format($(this).find('.txt_change_amount').val());
				var reference = $(this).attr('reference_repayment');
				if($amount > 0){
					temp['id_repayment_transaction'] =reference;
					temp['amount'] = $amount;
					change_post.push(temp);					
				}
			})

			console.log({change_post});

			// return;
			$.ajax({
				type          :          'POST',
				url           :          '/change/post',
				data          :          {'change_post' : change_post,
				'date' : $('#txt_transaction_date').val() ,
				'opcode' : $opcode,
				'id_repayment_change' : $id_repayment_change},
				beforeSend    :          function(){
					$('.mandatory').removeClass('mandatory');
					show_loader();
				},
				success       :          function(response){
					console.log({response});
					hide_loader();

					if(response.RESPONSE_CODE == "success"){

						var html_swal = '';
						var link = "/change/view/"+response.id_repayment_change+"?href="+encodeURIComponent('<?php echo $back_link;?>');

						if($opcode == 1){
							html_swal = "<a href='"+link+"'>Change ID# "+$id_repayment_change+"</a>";
						}

						var show_deny = (response.array_count==1)?true:false;
						Swal.fire({

							title: "Change Successfully Saved",
							html : html_swal,
							text: '',

							icon: 'success',
							showCancelButton : true,
							confirmButtonText: 'Create Another Change Release',
							cancelButtonText: 'Back to List of Released Change',
							showDenyButton: show_deny,
							denyButtonText: `Print CDV`,
							showConfirmButton : true,     
							allowEscapeKey : false,
							allowOutsideClick: false
						}).then((result) => {
							if(result.isConfirmed) {
								window.location = "/change/create?href="+encodeURIComponent('<?php echo $back_link;?>');
							}else if (result.isDenied) {
								var redirect_data = {
									'show_print_cdv' : 1,
									'id_cash_disbursement' : response.id_cdv
								}
								localStorage.setItem("redirect_print_cdv",JSON.stringify(redirect_data));
								window.location = 	link;
							}else{
								window.location = '<?php echo $back_link;?>';
							}
						});	
					}else if(response.RESPONSE_CODE == "ERROR"){
						Swal.fire({
							title: response.message,
							text: '',
							icon: 'warning',
							showConfirmButton : false,
							timer : 2500
						});	
					}else if(response.RESPONSE_CODE == "INVALID_INPUT"){
						Swal.fire({
							title: response.message,
							text: '',
							icon: 'warning',
							showConfirmButton : false,
							timer : 2500
						});
						var inputs = response.inputs;

						for(var $i=0;$i<inputs.length;$i++){
							$('tr.change_row_post[reference_repayment="'+inputs[$i]+'"]').find('.txt_change_amount').addClass('mandatory');
						}							

						console.log({inputs})
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
		$(document).ready(function(){
			sum_remaining_change()

			var redirect_data = jQuery.parseJSON(localStorage.getItem("redirect_print_cdv"));
			console.log({redirect_data});
			if(redirect_data != null){
				if(redirect_data.show_print_cdv == 1){
					if(redirect_data.id_cash_disbursement == '<?php echo $repayment_change->id_cash_disbursement; ?>'){
						print_page("/cash_disbursement/print/"+redirect_data.id_cash_disbursement)
						console.log("SHOW PRINT MODAL")
						localStorage.removeItem("redirect_print_cdv");
					}
				}
			}
		})
	</script>
	@endif
	@endpush
