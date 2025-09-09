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
</style>
<?php
$books = [
	1=>"Journal Voucher",
	2=>"Cash Disbursement Voucher",
	3=>"Cash Receipt Voucher"
];
?>

<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="card-body">
				<div class="row">
					<div class="col-md-3">
						<div style="border-right:2px #bfbfbf solid;padding-right: 10px;">
<!-- 							<h4>Books</h4>
							<div class="form-group" id="div_books" style="margin-top:8px">
								@foreach($books as $val=>$description)
								<div class="form-check">
									<input class="form-check-input chk_books" id="chk_{{$val}}" type="checkbox" value="{{$val}}" <?php echo((in_array($val,$books_selected) || count($books_selected) == 0)?"checked":""); ?>>
									<label class="form-check-label chk_books" for="chk_{{$val}}">{{$description}}</label>
								</div>
								@endforeach
								<hr>
								<div class="form-check">
									<input class="form-check-input" id="chk_cancel" type="checkbox" <?php echo ($show_cancel ==1)?"checked":""; ?>>
									<label class="form-check-label" for="chk_cancel">Show Cancel</label>
								</div>						
							</div> -->
							<input type="text" class="form-control" id="txt_search" placeholder="Search...">
							<div class="table-responsive" id="div_account" style="max-height: calc(100vh - 200px);overflow-y: auto;margin-top: 5px !important;overflow-x: hidden;">
								<table class="table table-bordered table-stripped table-head-fixed table-hover tbl_accounts">
									<thead>
										<tr>
											<th width="10%" style="text-align:center;"><input type="checkbox" id="chk_sel_all"></th>
											<th class="" style="text-align:center;">Account</th>
										</tr>
									</thead>
									<tbody id="account_body">
										@foreach($accounts as $acc)
										<tr class="row_account">
											<td style="text-align:center;"><input type="checkbox" class="chk_account" value="{{$acc->id_chart_account}}" <?php echo(in_array($acc->id_chart_account,$acc_selected)?"checked":""); ?>></td>
											<td class="col_acc">{{$acc->account_name}}</td>
										</tr>
										@endforeach
									</tbody>
								</table>    
							</div> 
						</div>
					</div>
					<div class="col-md-9">
						<h4>General Ledger</h4>
						<div class="form-group row" style="margin-top:10px">
							<label class="col-md-1 control-label col-form-label col-md-cus" style="text-align: left">Period&nbsp;</label>
							<div class="col-md-3">
								<input type="date" class="form-control in_payroll" id="sel_period_start" key="period_start" value="{{$start_date}}">
							</div>
							<div class="col-md-3">
								<input type="date" class="form-control in_payroll" id="sel_period_end" key="period_end" value="{{$end_date}}">
							</div>
							<button class="btn bg-gradient-success btn-sm" onclick="generate_gl(1)">Generate</button>
							<button class="btn bg-gradient-danger btn-sm" onclick="generate_gl(2)" style="margin-left:10px">Export to PDF</button>
						</div>
						<button type="button" class="btn btn-sm  bg-gradient-info" id="btn-exp-collapse"><i class="expandable-table-caret fas fa-caret-right fa-fw"></i>&nbsp;<span id="spn_exp">Expand</span></button>
						<table class="table table-bordered table-stripped table-head-fixed table-hover tbl_accounts">
							<thead>
								<tr style="text-align:center;" class="table_header_dblue">
									<th class="table_header_dblue">Date</th>
									<th class="table_header_dblue">Description</th>
									<th class="table_header_dblue">Post Reference</th>
									<th class="table_header_dblue">Debit</th>
									<th class="table_header_dblue">Credit</th>
									<th class="table_header_dblue">Remarks</th>
								</tr>
							</thead>
							<tbody>
								@if(count($general_ledger) > 0)
								@include('general_ledger.table2')

								@else
								<tr>
									<th colspan="6" style="text-align:center;">No Record Found</th>
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
@endsection

@push('scripts')
<script type="text/javascript">
	$(document).ready(function(){
		$("#txt_search").on("keyup", function() {
			var value = $(this).val().toLowerCase();
			$("tr.row_account").filter(function() {
				$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
			});
		});
	});

	$(document).on('click','#chk_sel_all',function(){
		$('input.chk_account').prop('checked',$(this).prop('checked'))
	})

	$(document).on('click','.col_acc',function(){
		var parent_row = $(this).closest('.row_account');
		var $checkbox = parent_row.find('input.chk_account');
		$checkbox.prop('checked',!$checkbox.prop('checked'));
	})
	function generate_gl(type){
		var books = [];
		// $('.chk_books:checked').each(function() {
		// 	books.push($(this).val());
		// });

		// if(books.length == 0){
		// 	Swal.fire({
		// 		title: "Please select atleast 1 Book",
		// 		text: '',
		// 		icon: 'warning',
		// 		showConfirmButton : false,
		// 		timer : 2500
		// 	}).then(function(){
		// 		$('#div_books').addClass('mandatory');
		// 		setTimeout(
		// 		  function() 
		// 		  {
		// 		    $('#div_books').removeClass('mandatory');
		// 		  }, 3000);
		// 	});	

		// 	return;
		// }
		var accounts = [];
		$('.chk_account:checked').each(function() {
			accounts.push($(this).val());
		});
		if(accounts.length == 0){
			Swal.fire({
				title: "Please select atleast 1 Account",
				text: '',
				icon: 'warning',
				showConfirmButton : false,
				timer : 2500
			}).then(function(){
				$('#div_account').addClass('mandatory');
				setTimeout(
				  function() 
				  {
				    $('#div_account').removeClass('mandatory');
				  }, 3000);
			});	


			return;
		}

		var param = {
			'start_date' : $('#sel_period_start').val(),
			'end_date' : $('#sel_period_end').val(),
			'show_cancel' : ($('#chk_cancel').prop("checked"))?1:0
		};

		// var queryString = $.param(param)+'&books='+encodeURIComponent(JSON.stringify(books))+'&accounts='+encodeURIComponent(JSON.stringify(accounts))
		var queryString = $.param(param)+'&accounts='+encodeURIComponent(JSON.stringify(accounts))
		if(type == 1){
			window.location = '/general_ledger?'+queryString;
		}else{
			window.open('/general_ledger/export?'+queryString,'_blank');
		}
		
		console.log({queryString});
	}
	$('#btn-exp-collapse').on('click',function(){
		if(!$(this).hasClass('collapsed')){
			$(this).addClass('collapsed');
			$('.acc_head').attr('aria-expanded',"false");
			text = "Collapse";
		}else{
			$(this).removeClass('collapsed');
			$('.acc_head').attr('aria-expanded',"true");
			text = "Expand";
		}
		$(this).find('#spn_exp').text(text);
		$('.acc_head').trigger('click');
	})

</script>
@endpush