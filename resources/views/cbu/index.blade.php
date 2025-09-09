@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
	.tbl_cbu tr>th{
		padding: 5px;
		padding-left: 5px;
		padding-right: 5px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 15px;
	}
	.tbl_cbu tr>td{
		padding: 3px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 13px;
	}
	.class_amount{
		text-align: right;
		padding-right: 10px !important;
	}
	.form-row  label{
		margin-bottom: unset !important;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 15px;
		margin-top: 5px;
	}
</style>
<div class="container main_form section_body" style="margin-top: -20px;">
	<div class="card">
		<div class="card-body">
			<h2 class="text-center head_lbl">Capital Build-Up</h2>
			
			<div class="row mt-4">
				
				<!-- <form> -->
					<div class="col-md-12">
						<form>
							@if($isAdmin)
							<div class="form-row">

								<div class="form-group col-md-7">
									<label for="sel_loan_service">Select Member</label>

									<select class="form-control frm-inputs frm-parent-loan p-0" id="sel_id_member" key='id_member' name="id_member" required>
										@if(isset($selected_member))
										<option value="{{$selected_member->id_member}}">{{$selected_member->member_name}}</option>
										@endif
									</select>
								</div>


							</div>
							@endif	
							<div class="form-row">
								<div class="form-group col-md-3">
									<label for="sel_loan_service">Date</label>

									<div class="col-md-12 p-0">
										<input type="date" class="form-control in_payroll" id="sel_period_start" key="period_start" value="{{$start_date}}" name="start_date">
									</div>
									
								</div>
								<div class="form-group col-md-3">
									<label for="sel_loan_service">&nbsp;</label>
									<div class="col-md-12 p-0">
										<input type="date" class="form-control in_payroll" id="sel_period_end" key="period_end" value="{{$end_date}}" name="end_date">
									</div>
								</div>
								<div class="form-group col-md-2 col-12">
									<label>&nbsp;</label>
									<button class="btn btn-sm bg-gradient-primary2 form-control">Search</button>
								</div>
								<div class="form-group col-md-3 col-12">
									<label>&nbsp;</label>
									<button type="button" class="btn btn-sm bg-gradient-success2 form-control" onclick="export_excel()">Export to Excel</button>
								</div>
							</div>
						</form>
					</div>

					<?php 
						$total = 0;$sum_credit=0;$sum_debit=0;
						function amount($amt){
							return ($amt == 0)?'-':number_format($amt,2);
						}
					?>
					
					<div class="col-md-12">
						<h5>Total CBU:&nbsp;&nbsp;&nbsp;<span id="spn_cbu_total"></span></h5>
						<div class="table-responsive" style="max-height: calc(100vh - 200px);overflow-y: auto;margin-top: 5px !important;overflow-x: auto">
							<table class="table table-bordered table-stripped table-head-fixed tbl_cbu" style="white-space: nowrap;">
								<thead>
									<tr>
										<th class="table_header_dblue">Transaction Date</th>
										<th class="table_header_dblue">Description</th>
										<th class="table_header_dblue">Reference</th>
										<th class="table_header_dblue">Debit</th>
										<th class="table_header_dblue">Credit</th>
										
										<th class="table_header_dblue">Ending Balance</th>
										<!-- <th class="table_header_dblue">Amount</th> -->
									</tr>
								</thead>
								<tbody id="net_body">
									@foreach($cbu_ledger as $cbu)
									<tr>
										<td>{{$cbu->transaction_date}}</td>
										<td>{{$cbu->description}}</td>
										<td>{{$cbu->reference}}</td>

										<!-- <td class="class_amount">{{number_format($cbu->amount,2)}}</td> -->
										<td class="class_amount">{{amount($cbu->debit)}}</td>
										<td class="class_amount">{{amount($cbu->credit)}}</td>
										
										<?php 
											$sum_credit+= $cbu->credit;
											$sum_debit+= $cbu->debit;
											$total+= $cbu->amount;
										?>
										<td class="class_amount">{{number_format($total,2)}}</td>
									</tr>
									@endforeach
								</tbody>
								<tfoot>
									<tr>
										<th colspan="3">Total</th>
										<th class="class_amount">₱{{number_format($sum_debit,2)}}</th>
										<th class="class_amount">₱{{number_format($sum_credit,2)}}</th>
										
										<th></th>
										<!-- <th class="class_amount">₱{{number_format($total,2)}}</th> -->
									</tr>
								</tfoot>
							</table>    
						</div>  
					</div>
				</div>
			</div>
		</div>
	</div>
	@endsection
	@push('scripts')
	<script type="text/javascript">
		$(document).ready(function(){
			$('#spn_cbu_total').text('₱'+'<?php echo number_format($total,2); ?>')
		})
		$(document).on('select2:open', () => {
			document.querySelector('.select2-search__field').focus();
		});	

		intialize_select2()

		function intialize_select2(){		
			$("#sel_id_member").select2({
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

		function export_excel(){
			let queryParam={
				'id_member' : $('#sel_id_member').val(),
				'start_date' : $('#sel_period_start').val(),
				'end_date' : $('#sel_period_end').val()
			};
			window.open('/cbu/account-export?'+$.param(queryParam),'_blank');
		}
	</script>
	@endpush