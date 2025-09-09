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
	</style>

	<div class="container-fluid main_form" style="margin-top: -20px;">
		<div class="card">
			<div class="card-body col-md-12">
				<h3>Repayment</h3>
				<div class="row">
					<div class="col-md-12">	
						<button class="btn btn-sm bg-gradient-primary" onclick="add_repayment()"><i class="fas fa-plus"></i>&nbsp;Add Loan Payment</button>
					</div>	

					<div class="col-md-12" style="margin-top:10px" id="repayment_main_display">

					</div>
				</div>
			</div>
		</div>
	</div>
	@include('repayment.repayment_modal')
	@endsection
	@push('scripts')

	<script type="text/javascript">
		var repayment_data = {};
		let current_repayment_token = '';
		let temp_repayment_token = '';
		$(document).ready(function(){
			$("#repayment_modal").on("hidden.bs.modal", function () {
				if(is_edit == 1){
					clear_repayment_modal();
				}
			});
		})
		function generate_object(key,data){
			var repayment_obj = {};
			// repayment_obj['id_member'] = 20
			// repayment_obj['member_name'] = data['member_name'];
			repayment_obj['loan_dues'] = data['loan_dues'];
			repayment_obj['swiping_amount'] = data['swiping_amount'];
			repayment_obj['repayment_fees'] = data['repayment_fees'];
			repayment_obj['id_member'] = data['id_member'];
			repayment_obj['member_name'] = data['member_name'];
			// repayment_obj['fees'] = parseLoanFees();

			repayment_data[key] = repayment_obj;
			console.log({repayment_data})
		}
		function makeid(length){
			var result           = '';
			var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
			var charactersLength = characters.length;
			for(var i = 0; i < length; i++ ) {
				result += characters.charAt(Math.floor(Math.random() * charactersLength));
			}
			return result;
		}
		function add_repayment(){
			is_edit = 0;
			$('#repayment_modal').modal('show');
		}
		function display_loan_dues_main(dues){
			var out = "";
			$.each(dues,function(i,item){
				console.log({item})
				out += '<tr class="row_loan_dues_display" loan-token="'+item.loan_token+'">';
				out += '<td class="text_center text_bold">'+(i+1)+'</td>';
				out += '<td class="text_bold" key="loan_service_name">'+item.loan_service_name+'</td>';
				out += '<td class="class_amount" key="principal_amount">'+number_format(parseFloat(item.principal_amount))+'</td>';
				out += '<td class="text_center" key="interest_rate">'+item.interest_rate+'%</td>';
				out += '<td key="terms">'+item.terms+'</td>';
				out += '<td class="class_amount" key="loan_amount">'+number_format(parseFloat(item.loan_amount))+'</td>';
				out += '<td class="text_center" key="repayment_made">'+item.repayment_made+'</td>';
				out += '<td class="class_amount" key="total_amount_paid">'+number_format(parseFloat(item.total_amount_paid))+'</td>';

				out += '<td class="class_amount" key="loan_balance">'+number_format(parseFloat(item.loan_balance))+'</td>';
				out += '<td class="class_amount" key="amount_due">'+number_format(parseFloat(item.amount_due))+'</td>';
				out += '<td class="class_amount" key="amount_paid">'+number_format(parseFloat(item.payable_amount))+'</td>';
				out += '</tr>';


	            // payable_amount
	        })
			$('#loan_dues_display_'+current_repayment_token).html(out);
		}
		function display_fees_main(fees){
			var out = "";
			$.each(fees,function(i,item){
				out += '<tr>';
				out += '<td class="text_center text_bold">'+(i+1)+'</td>';
				out += '<td class="text_bold">'+item.fee_description+'</td>';
				out += '<td class="class_amount">'+number_format(item.amount)+'</td>';
				out += '</tr>';
			})
			$('#loan_fee_display_'+current_repayment_token).html(out);
		}
		function append_repayment(token){
			var out = `	<div class="card card-outline card-primary" id="crd_`+token+`">
							<div class="card-body">
								<div class="row">
									<div class="col-md-12 p-0">
										<h4><span id="spn_memname_`+token+`"></span>
											<a class="btn btn-sm bg-gradient-danger float-right" style="margin-left: 10px;" onclick="remove_repayment(`+token+`)"><i class="fas fa-times"></i>&nbsp;Remove</a>
											<a class="btn btn-sm bg-gradient-primary float-right" onclick="edit_repayment(`+token+`)"><i class="fas fa-edit"></i>&nbsp;Edit</a>
										</h4>
										<hr>
										<div class="table-responsive" style="max-height: calc(100vh - 200px);overflow-y: auto;margin-top: 5px !important;overflow-x: auto">
											<table class="table table-bordered table-stripped table-head-fixed tbl_repayment_display" style="white-space: nowrap;">
												<thead>
													<tr>
														<th class="table_header_dblue" width="2%"></th>
														<th class="table_header_dblue">Loan Service</th>
														<th class="table_header_dblue">Principal</th>
														<th class="table_header_dblue">Interest Rate</th>
														<th class="table_header_dblue">Terms</th>
														
														<th class="table_header_dblue">Loan Amount</th>
														<th class="table_header_dblue">No of Loan Payment made</th>
														<th class="table_header_dblue">Total Amount Paid</th>
														<th class="table_header_dblue">Loan Balance </th>
														<th class="table_header_dblue">Amount Due</th>
														<th class="table_header_dblue">Payable Amount</th>
													</tr>
												</thead>
												<tbody id="loan_dues_display_`+token+`">

												</tbody>
											</table>    
										</div> 
										<div class="col-md-5 p-0">
											<div class="table-responsive" style="max-height: calc(100vh - 200px);overflow-y: auto;margin-top: 5px !important;overflow-x: auto">
												<table class="table table-bordered table-stripped table-head-fixed tbl_repayment_display" style="white-space: nowrap;">
													<thead>
														<tr>
															<th class="table_header_dblue" width="8%"></th>
															<th class="table_header_dblue">Fee</th>
															<th class="table_header_dblue">Amount</th>
														</tr>
													</thead>
													<tbody id="loan_fee_display_`+token+`">
													</tbody>
												</table>
											</div>
										</div>
										<div class="col-md-6 p-0">

										<table style="width:100%">
			                                <tr>
			                                    <td width="34%" class="spn_t">Total Loan Amount Due:</td>
			                                    <td class="class_amount spn_total_loan_amount_due" width="30%" id=""></td>
			                                    <td></td>
			                                </tr>
			                                <tr>
			                                    <td width="34%" class="spn_t">Total Loan Fees:</td>
			                                    <td class="class_amount spn_total_loan_fees" width="30%">0.00</td>
			                                    <td></td>
			                                </tr>
			                                <tr>
			                                    <td width="34%" class="spn_t">Total Amount Due:</td>
			                                    <td class="class_amount spn_total_amount_due" width="30%">0.00</td>
			                                    <td></td>
			                                </tr>
			                                <tr>
			                                    <td width="34%" class="spn_t">Swiping Amount:</td>
			                                    <td class="class_amount spn_swiping_amount text_undeline text_bold" width="30%">0.00</td>
			                                    <td></td>
			                                </tr>
			                                <tr>
			                                    <td width="34%" class="spn_t">Total Paid Amount:</td>
			                                    <td class="class_amount spn_total_paid_amount text_undeline text_bold" width="30%">0.00</td>
			                                    <td></td>
			                                </tr>
			                                <tr>
			                                    <td width="34%" class="spn_t">Change:</td>
			                                    <td class="class_amount spn_change" width="30%">0.00</td>
			                                    <td></td>
			                                </tr>
			                            </table>

										</div> 
									</div>
								</div>
							</div>
						</div>`;
			$('#repayment_main_display').append(out)
			console.log({out});
		}
		function remove_repayment(token){
		Swal.fire({
			title: 'Are you sure you want to remove this repayment transaction?',
			icon: 'warning',
			showDenyButton: false,
			showCancelButton: true,
			confirmButtonText: `Yes`,
		}).then((result) => {
			if (result.isConfirmed) {
				console.log("REMOVING "+token);
				delete repayment_data[token];
				$('#crd_'+token).remove();
			} 
		})	

		}
		function edit_repayment(token){
			console.log("EDITING "+token);
			load_repayment_details(token);
			$('#repayment_modal').modal('show');
			is_edit = 1;
		}
	</script>
	<script type="text/javascript" id="test">
		var temp_data = '{"2020211209231714":{"loan_dues":[{"loan_token":"01242022213152744cc","id_loan":17,"loan_service_name":"INSTALLMENT LOAN 1","principal_amount":"50000.00","interest_rate":"3.00","terms":"24 Months","loan_amount":"87720.00","term_code":"P1","repayment_made":0,"total_amount_paid":0,"loan_balance":"87720.00","amount_due":"3655.00","payable_amount":3655},{"loan_token":"0130202217122216385","id_loan":18,"loan_service_name":"Installment Loan 2","principal_amount":"100000.00","interest_rate":"5.00","terms":"12 Months","loan_amount":"167050.00","term_code":"P1","repayment_made":0,"total_amount_paid":0,"loan_balance":"167050.00","amount_due":"13920.83","payable_amount":13920.83}],"swiping_amount":20000,"repayment_fees":[{"id_repayment_fee_type":"1","amount":25,"fee_description":"Swiping Fee"},{"id_repayment_fee_type":"3","amount":399.17,"fee_description":"Fee 2"}],"id_member":"20","member_name":"2020211209231714 || Jhon Rheymar Caluza"},"3620211212163558":{"loan_dues":[{"loan_token":"02012022172932612de","id_loan":20,"loan_service_name":"Installment Loan 2","principal_amount":"150000.00","interest_rate":"5.00","terms":"12 Months","loan_amount":"247575.00","term_code":"P1","repayment_made":0,"total_amount_paid":0,"loan_balance":"247575.00","amount_due":"20631.25","payable_amount":20631.25}],"swiping_amount":25000,"repayment_fees":[{"id_repayment_fee_type":"1","amount":25,"fee_description":"Swiping Fee"}],"id_member":"36","member_name":"3620211212163558 || Kirk Hammett"}}';
		// repayment_data = jQuery.parseJSON(temp_data);
		function loan_repayments(){
			var data = jQuery.parseJSON(temp_data);
			$.each(data,function(i,item){
				display_repayment_to_main(i);
			}) 
		}
	</script>
	<script type="text/javascript">
		function map_post_data(){
			var repayment_post_data = {};
			$.each(repayment_data,function(i,item){
				var temp_rep_object = {};

				var totals = parseTotals(i);
				console.log({totals});
				temp_rep_object['repayment_transaction'] = {};
				temp_rep_object['repayment_transaction']['swiping_amount'] = item.swiping_amount;
				$.each(totals,function(j,values){
					temp_rep_object['repayment_transaction'][j] = values;
				});
				temp_rep_object['repayment_transaction']['id_member'] = item.id_member;
				
				//loan dues payment
				temp_rep_object['repayment_loans'] = [];
				$.each(item.loan_dues,function(key,dues){
					var temp_dues = {};
					temp_dues['loan_token'] = dues.loan_token;
					temp_dues['amount_paid'] = dues.payable_amount;

					temp_rep_object['repayment_loans'].push(temp_dues);
				});

				//repayment_fees
				temp_rep_object['repayment_fees'] = [];
				$.each(item.repayment_fees,function(f,fees){
					var temp_fees = {};
					temp_fees['id_repayment_fee_type'] = fees.id_repayment_fee_type;
					temp_fees['amount'] = fees.amount;

					temp_rep_object['repayment_fees'].push(temp_fees);
				})
				repayment_post_data[i] = temp_rep_object;
			})
			console.log({repayment_post_data});

			return repayment_post_data;
		}

		function test_post(){
			$.ajax({
				type         :           "GET",
				url          :           "/repayment/post",
				data         :           {'repayments'  :  map_post_data()},
				success      :           function(response){
										 console.log({response});
				}
			})
		}
	</script>
	@endpush



