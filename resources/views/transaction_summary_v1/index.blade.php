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
	1=>"Per Transaction",
	2=>"Show Entry"
];

$sel_type = $selected_type;
?>

<div class="row">
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
						<h4>Transaction Summary</h4>

						<div class="form-group row" style="margin-top:10px">
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
						<div class="form-group row" >
							<label class="col-md-1 control-label col-form-label col-md-cus" style="text-align: left">Period&nbsp;</label>
							<div class="col-md-3">
								<input type="date" class="form-control in_payroll" id="sel_period_start" key="period_start" value="{{$start_date}}">
							</div>
							<div class="col-md-3">
								<input type="date" class="form-control in_payroll" id="sel_period_end" key="period_end" value="{{$end_date}}">
							</div>
							<button class="btn bg-gradient-success btn-sm" onclick="generate_transaction_summary(1)">Generate</button>
							<button class="btn bg-gradient-danger btn-sm" onclick="generate_transaction_summary(2)" style="margin-left:10px">Export to PDF</button>
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
						@else
						@include('transaction_summary.table_entry')
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
	function generate_transaction_summary(type){
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


		var param = {
			'start_date' : $('#sel_period_start').val(),
			'end_date' : $('#sel_period_end').val(),
			'type' : $('#sel_type').val(),
			'show_cancel' : ($('#chk_cancel').prop("checked"))?1:0
			
		};
		// 'show_cancel' : ($('#chk_cancel').prop("checked"))?1:0
		var queryString = $.param(param)+'&books='+encodeURIComponent(JSON.stringify(books));
		if(type == 1){
			window.location = '/transaction_summary?'+queryString;
		}else{
			window.open('/transaction_summary/export?'+queryString,'_blank');
		}
		
		console.log({queryString});
	}


</script>
@endpush


