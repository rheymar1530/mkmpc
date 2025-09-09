@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
	.tbl_mem th,.tbl_mem td,.tbl-loans th{
		padding: 2px 5px 2px 8px;
		font-size: 0.9rem;
		cursor: pointer;

	}

	.tbl-loans td{
		padding: 0px;
	}
	.sel-loan{
		background: #66ff99;
	}
	.spn-check{
		color:green;
	}
	tr.delete input, tr.delete select{
		color: #ff6666 !important;
	}
</style>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-5">
			<div class="card">
				<div class="card-body">
					<form id="frm-search-member">
						<div class="row mt-3" id="div-individual">
							
			                    <div class="form-group col-md-12">
			                        <label class="lbl_color mb-0">Select Loaner</label>
			                        <select id="sel-member" class="form-control p-0" name="id_member" required  >

			                        	@if(isset($selected_member))
											<option value="{{$selected_member->id_member}}">{{$selected_member->member_name}}</option>
											@endif
			                        </select>
			                    </div>
			               
		                </div>
		            </form>
	                @if(isset($loans))
	                <div class="row mt-3">
	                <div class="mt-3 col-md-12" style="max-height: calc(100vh - 50px);overflow-y: auto;">
						<table class="table tbl_mem table-bordered table-head-fixed table-hover">
							<thead>
								<tr>
									<th>Loans</th>
									<th>Surcharge</th>
								</tr>
							</thead>
							<tbody>
								@foreach($loans as $loan)
								<tr class="row-loan " data-id="{{$loan->loan_token}}" title="Double click to add loan">
									<td class="td-member">{{$loan->loan_name}} <sup><a href="/loan/application/approval/{{$loan->loan_token}}" target="_blank">[{{$loan->id_loan}}]</a></sup></td>
									<td class="td-member text-right">{{number_format($loan->surcharge,2)}}</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>

	                </div>

	                @endif
	              
				</div>
			</div>
		</div>
		<div class="col-md-7">
			<div class="card">
				<div class="card-header py-3">
					<h6 class="mb-0"><b>Surcharge Maintenance</b></h6>
				</div>
				<div class="card-body">
						                <div class="col-md-12">
	                	<p>Loan Period: <span class="text-period"></span></p>
	                </div>

					<div class="mt-3 col-md-12" style="max-height: calc(100vh - 50px);overflow-y: auto;">
						<table class="table tbl_mem table-bordered table-head-fixed table-hover">
							<thead>
								<tr>
									<th>Date</th>
									<th>Surcharge</th>
									<th>Payment</th>
									<th>Balance</th>
								</tr>
							</thead>
							<tbody id="body-surcharges">
								<tr>
									<td colspan="4" class="text-center">NO DATA</td>
								</tr>
							</tbody>
					
								<tr style="background: gray;color: white;">
									<td class="font-weight-bold">Total</td>
									<td class="font-weight-bold text-right" id="total-surcharge">0.00</td>
									<td class="font-weight-bold text-right" id="total-payment">0.00</td>
									<td class="font-weight-bold text-right" id="total-balance">0.00</td>
								</tr>
						
						</table>
					</div>
				</div>
				<div class="card-footer py-2">
					<button class="btn bg-gradient-primary float-right" id="btn-save-surcharge" disabled>Save</button>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection


@push('scripts')
<script type="text/javascript">
	let CURDATA = {};
	$(document).on('select2:open', () => {
		document.querySelector('.select2-search__field').focus();
	});	


	$(document).on('change','#sel-member',function(){
		$('#frm-search-member').trigger('submit');
	});

	initMemberSelect();
	function initMemberSelect(){		
		$("#sel-member").select2({
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

	$(document).on('dblclick','tr.row-loan',function(){
		var token = $(this).attr('data-id');

		let CurRow = $(this);

		$.ajax({
			type        :       'GET',
			url         :        'parse-surcharge',
			data        :        {'loan_token' : token},
			beforeSend  :        function(){
								 $('.sel-loan').removeClass('sel-loan');
								 show_loader();
			},
			success     :        function(response){
								 console.log({response});
								 hide_loader();
								 $(CurRow).addClass('sel-loan');
								 let out = ``;
								 let $disabled = true;
								 let TotalPayment = 0;

								 CURDATA = response.details;
								 if(response.surcharges.length > 0){
									 $.each(response.surcharges,function(i,item){
									 	var Disabled = (item.surcharge > 0 && item.surcharge-item.payment == 0)?"disabled":"";
									 	out += `<tr class="row-surcharge" data-payment="${item.payment}" data-id="${item.id_loan_table}">
									 				<td>${item.date}</td>
									 				<td class="p-0"><input class="form-control txt-surcharge class_amount text-right" value="${number_format(item.surcharge,2)}" ${Disabled}></td>
									 				<td class="text-right">${number_format(item.payment,2)}</td>
									 				<td class="text-right balance">${number_format(item.surcharge-item.payment,2)}</td>
									 			</tr>`;
									 	TotalPayment += item.payment;
									 });
									 $disabled = false;
								 }else{
								 	out += `<tr>
												<td colspan="4" class="text-center">NO DATA</td>
											</tr>`;
								 }
								
								 $('#total-payment').text(number_format(TotalPayment,2));
								 $('#btn-save-surcharge').prop('disabled',$disabled);
								 $('#body-surcharges').html(out);
								 $('.text-period').text(`${CURDATA['date_from']} - ${CURDATA['date_to']}`);
								  ComputeAll();
			},error: function(xhr, status, error) {
				hide_loader();
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
	});


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

		ComputeRow($(this));
	})
	const ComputeAll=()=>{
		let totalSurcharge = 0;
		let totalBalance = 0;
		$('.txt-surcharge:visible').each(function(){
			var p = $(this).val();
			let sur = ($.isNumeric(p))?roundoff(p):decode_number_format(p);
			totalSurcharge += sur;

			console.log({totalSurcharge});

			let payment = $(this).closest('tr').attr('data-payment');
			totalBalance += (sur-payment);
			
		});
		$('#total-surcharge').text(number_format(totalSurcharge));
		$('#total-balance').text(number_format(totalBalance));
	}


	const ComputeRow = (obj)=>{
		let parentRow = $(obj).closest('tr.row-surcharge');
		let payment = $(parentRow).attr('data-payment');
		var p = $(obj).val();
		let sur = ($.isNumeric(p))?roundoff(p):decode_number_format(p);
		let balance = sur - payment;
		$(parentRow).find('td.balance').text(number_format(balance,2));

		ComputeAll();
	}
	$('#btn-save-surcharge').click(function(){
		Swal.fire({
			title: 'Are you sure you want to save this ?',
			icon: 'warning',
			showDenyButton: false,
			showCancelButton: true,
			confirmButtonText: `Yes`,
			allowOutsideClick: false,
			allowEscapeKey: false,
		}).then((result) => {
			if (result.isConfirmed) {
				post();
			}
		});
	});

	const post = ()=>{
		let postData = [];
		$('tr.row-surcharge').each(function(){
			temp = {
				'id_loan_table' : $(this).attr('data-id'),
				'amount' : decode_number_format($(this).find('.txt-surcharge').val())
			}
			postData.push(temp);
		});

		$.ajax({

			type         :      'GET',
			url          :      'surcharge-maintenance/post',
			data         :      {'surcharges' : postData, 
								'idLoan' : CURDATA['id_loan'],
								'loanToken' : CURDATA['loan_token']},
			beforeSend   :      function(){
								$(`tr.row-surcharge`).removeClass('table-danger');
								show_loader();
			},
			success      :      function(response){
								console.log({response});
								hide_loader();
								if(response.RESPONSE_CODE == "SUCCESS"){
								Swal.fire({
										title: response.message,
										icon: 'success',
										showCancelButton: false,
										showConfirmButton : false,
										confirmButtonText: `Close`,
										timer : 2500
									})
								}else if(response.RESPONSE_CODE == "ERROR"){
									Swal.fire({
										title: response.message,
										text: '',
										icon: 'warning',
										showCancelButton : false,
										showConfirmButton : false,
										timer : 2500
									});	

									let invalidSurcharge = response.id_loan_table ?? [];
									$.each(invalidSurcharge,function(i,val){
										$(`tr.row-surcharge[data-id="${val}"]`).addClass('table-danger');
									})
								}
			},error: function(xhr, status, error) {
				hide_loader();
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