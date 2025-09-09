@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
	.tbl_accounts tr>th{
		padding: 5px;
		padding-left: 5px;
		padding-right: 5px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 15px;
	}
	.tbl_accounts tr>td{
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
	.col_tot{
		font-weight: bold;
	}
	.row_total{
		background: #e6e6e6;
		/*color: black;*/
	}
	.acc_head{
		background: #b3ffff;
	}
	.fas {
		transition: .3s transform ease-in-out;
	}
	.collapsed .fas {
		transform: rotate(90deg);
		padding-right: 3px;
	}
	.row_total{
		background: #e6e6e6;
		/*color: black;*/
	}
	.table_header_dblue_cust{
		background: #203764 !important;
		color: white;
	}
</style>
<?php
$books_op = [
	1=>"Journal Voucher",
	2=>"Cash Disbursement Voucher",
	3=>"Cash Receipt Voucher"
];


$type = [
	3=>"Account Summary",
	// 1=>"Per Transaction",
	2=>"Show Entry",
];

$sel_type = $selected_type;


$pay_to_from_op = [
	1=>"Supplier",
	2=>"Members",
	3=>"Employee",
	4=>"Others"
]
?>

<div class="row section_body">
	<div class="col-md-12">
		<div class="card">
			<div class="card-body">
				<div class="row">
<!-- 					<div class="col-md-3">
						<div class="form-group" id="div_books" style="margin-top:8px">
							<div class="form-check">
								<input class="form-check-input" id="chk_cancel" type="checkbox" >
								<label class="form-check-label" for="chk_cancel">Show Cancel</label>
							</div>						
						</div>
					</div> -->
					<div class="col-md-12">
						<h4 class="head_lbl text-center">Voucher Summary</h4>

						<div class="form-group row mt-4">
							<label class="col-md-1 control-label col-form-label col-md-cus" style="text-align: left">Books&nbsp;</label>
							<div class="col-md-9" >
								@foreach($books_op as $val=>$b)
								<div class="form-check form-check-inline">
									<input class="form-check-input chk_books" type="checkbox" id="chk_{{$val}}" value="{{$val}}" <?php echo((in_array($val,$books) || count($books) == 0)?"checked":""); ?>>
									<label class="form-check-label" for="chk_{{$val}}">{{$b}}</label>
								</div>
								@endforeach
							</div>
						</div>

						<div class="form-group row">
							<label class="col-md-1 control-label col-form-label col-md-cus" style="text-align: left">Type&nbsp;</label>
							<div class="col-md-3">
								<select class="form-control p-0" id="sel_type">
									@foreach($type as $val=>$tl)
									<option value="{{$val}}" <?php echo($sel_type == $val)?"selected":""; ?> >{{$tl}}</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-md-1 control-label col-form-label col-md-cus" style="text-align: left">From/To&nbsp;</label>
							<div class="col-md-2">
								<select class="form-control p-0" id="sel_pay_to_from">
									<option value="0">**ALL**</option>
									@foreach($pay_to_from_op as $val=>$p)
									<option value="{{$val}}" <?php echo($val==$pay_to_from)?'selected':''; ?>>{{$p}}</option>
									@endforeach
								</select>
							</div>
							<div class="col-md-4">
								<div id="id_payee_holder"></div>
								<div id="div_sel_reference">
									<select class="form-control form_input p-0 entry_parent" id="sel_reference" key="payee_reference">
										@if(isset($selected_reference_payee))
										<option value="{{$selected_reference_payee->id}}">{{$selected_reference_payee->name}}</option>
										@endif
									</select>
								</div>
							</div>
							
						</div>
						<div class="form-group row" >
							<label class="col-md-1 control-label col-form-label col-md-cus" style="text-align: left">Period&nbsp;</label>
							<div class="col-md-3">
								<input type="date" class="form-control in_payroll" id="sel_period_start" key="period_start" value="{{$start_date}}">
							</div>
							<div class="col-md-3">
								<input type="date" class="form-control in_payroll" id="sel_period_end" key="period_end" value="{{$end_date}}">
							</div>
							<button class="btn bg-gradient-success2 btn-sm" onclick="generate_transaction_summary(1)">Generate</button>
							<div class="btn-group ml-1">
								<button type="button" class="btn bg-gradient-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									Export
								</button>
								<div class="dropdown-menu">
									
									<a class="dropdown-item" onclick="generate_transaction_summary(2,1)">PDF</a>
									<a class="dropdown-item" onclick="generate_transaction_summary(2,2)">Excel</a>
								</div>
							</div>
							<!-- <button class="btn bg-gradient-danger2 btn-sm" onclick="generate_transaction_summary(2)" style="margin-left:10px">Export to PDF</button> -->
						</div>

						<div class="form-group row">
							<label class="col-md-1 control-label col-form-label col-md-cus" style="text-align: left">&nbsp;</label>
							<div class="col-md-3">
								<div class="form-check">
									<input class="form-check-input" id="chk_cancel" type="checkbox" <?php echo ($show_cancel ==1)?"checked":""; ?>>
									<label class="form-check-label" for="chk_cancel">Show Cancelled Transactions</label>
								</div>	
							</div>					
						</div>


						@if($selected_type == 1)
						@include('transaction_summary.table')
						@elseif($selected_type == 2)
						@include('transaction_summary.table_entry')
						@else
						@include('transaction_summary.table_account')
						@endif
						
					</div>
				</div>
			</div>
		</div>
		
	</div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
	var $div_sel_reference = $('#div_sel_reference').detach();
	initialize_payee_type();

	$(document).on('change','#sel_pay_to_from',function(){
		initialize_payee_type(true);

		// alert(1);
	})

	$(document).on('select2:open', (e) => {
		const selectId = e.target.id
		$(".select2-search__field[aria-controls='select2-" + selectId + "-results']").each(function(key,value,){
			value.focus()
		})
	}) 
	function initialize_payee_type(reset_reference){
		var payee_type = $('#sel_pay_to_from').val();
		$('#id_payee_holder').html('')
		if(payee_type >0 && payee_type <= 3){
			$('#id_payee_holder').html($div_sel_reference);
			if(reset_reference){
				$('#sel_reference').val(0).trigger("change");

			}
			intialize_select2(payee_type)
		}

		// animate_element($('#id_payee_holder'),1)
	}
	function intialize_select2(type){		
		var $link = '';
		if(type == 1){
			$link = '/search_supplier';
		}else if(type == 2){
			$link = '/search_member'
		}else if(type == 3){
			$link = '/search_employee';
		}

		$("#sel_reference").select2({
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
	function generate_transaction_summary(type,export_type=1){
		var books = [];
		$('.chk_books:checked').each(function() {
			books.push($(this).val());
		});

		if(books.length == 0){
			Swal.fire({
				title: "Please select atleast 1 Book",
				text: '',
				icon: 'warning',
				showConfirmButton : false,
				timer : 2500
			}).then(function(){
				$('#div_books').addClass('mandatory');
				setTimeout(
					function() 
					{
						$('#div_books').removeClass('mandatory');
					}, 3000);
			});	

			return;
		}
		var payee_type = $('#sel_pay_to_from').val();
		
		if(payee_type >0 && payee_type <= 3 && $('#sel_reference').val() == null){
			Swal.fire({
				title: "Please select reference",
				text: '',
				icon: 'warning',
				showConfirmButton : false,
				timer : 1000
			}).then(function(){

				$('#sel_reference').select2('open');


			});	
			return;
		
		}

		var param = {
			'start_date' : $('#sel_period_start').val(),
			'end_date' : $('#sel_period_end').val(),
			'type' : $('#sel_type').val(),
			'show_cancel' : ($('#chk_cancel').prop("checked"))?1:0,
			'pay_to_from' : $('#sel_pay_to_from').val(),
			'reference' : $('#sel_reference').val(),
			'export_type' : export_type
			
		};
		// 'show_cancel' : ($('#chk_cancel').prop("checked"))?1:0
		var queryString = $.param(param)+'&books='+encodeURIComponent(JSON.stringify(books));
		if(type == 1){
			window.location = '/voucher_summary?'+queryString;
		}else{
			window.open('/voucher_summary/export?'+queryString,'_blank');
		}
		
		console.log({queryString});
	}


</script>
@endpush


