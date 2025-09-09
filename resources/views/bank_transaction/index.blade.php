@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
	.main_form{
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
	}
	#tbl_bank_transaction  tr>td{
		padding:3px ;
		vertical-align:top;
		font-family: Arial !important;
		font-size: 12px !important;
	}
	#tbl_bank_transaction  tr::nth-child(0)>th{
		padding:34px !important;
		vertical-align:top;
		font-family: Arial !important;
		font-size: 12px !important;
	}  
	.col_search{
		padding: 1px 1px 1px 1px !important;
	}

	.dataTables_scrollHead table.dataTable th, table.dataTable tbody td {
		padding: 9px 10px 1px;
	}

	.head_search{
		height: 24px;
	}


	.form-label{
		margin-bottom: 4px !important;
	}
	.col_amount{
		text-align: right;
		padding-right: 10px !important;
		font-weight: bold;
	}
	.tbl_badge{
		font-size: 13px;
		margin-left: 10px !important;
	}

</style>
<div class="container-fluid main_form" style="margin-top:-20px">
	<h3>Bank Transaction&nbsp;&nbsp;
	</h3>
	<div class="row">
		<div class="col-12 col-sm-6 col-md-4">
			<div class="info-box">
				<span class="info-box-icon bg-gradient-success elevation-1"><i class="fas fa-sign-in-alt"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">Total Deposit</span>
					<span class="info-box-number" id="spn_total_deposit"></span>
						
						
					
				</div>
				<!-- /.info-box-content -->
			</div>
			<!-- /.info-box -->
		</div>
		<!-- /.col -->
		<div class="col-12 col-sm-6 col-md-4">
			<div class="info-box mb-3">
				<span class="info-box-icon bg-danger elevation-1"><i class="fas fa-sign-out-alt"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">Total Withdrawal</span>
					<span class="info-box-number" id="spn_total_widthdrawal">41,410</span>
				</div>
				<!-- /.info-box-content -->
			</div>
			<!-- /.info-box -->
		</div>
		<!-- /.col -->

		<!-- fix for small devices only -->
		<div class="clearfix hidden-md-up"></div>


		<!-- /.col -->
	</div>
	<div class="row">
		<div class="col-md-12">
			<!-- <button class="btn btn-sm bg-gradient-primary" onclick="open_pickup_modal()">Create Pickup Request</button> -->
			
			<table id="tbl_bank_transaction" class="table table-hover table-striped" style="white-space: nowrap;margin-top: 10px;">
				<thead>
					<tr class="table_header_dblue">
						<th>Transaction ID</th>
						<th>Transaction Date</th>
						<th>Reference</th>
						<!-- <th>Member Code</th> -->
						<th>Name</th>
						<th>Bank</th>
						<th>Type</th>
						<th>Amount</th>
						<th>Status</th>
						<th>Date Created</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$with_amount = 0;
						$dep_amount = 0;
					?>
					@foreach($lists as $list)	
						<tr class="row_bank_transaction" data-key="{{$list->id_bank_transaction}}">
							<?php
								if($list->status < 10){
									if($list->id_type == 1){
										$dep_amount += floatval(preg_replace('/[^\d\.\-]/', '', $list->amount));
									}else{
										$with_amount += floatval(preg_replace('/[^\d\.\-]/', '', $list->amount));
									}									
								}
							?>
							<td>{{$list->id_bank_transaction}}</td>
							<td>{{$list->date}}</td>
							<td>{{$list->reference}}</td>
							<!-- <td>{{$list->member_code}}</td> -->
							<td>{{$list->name}}</td>
							<td>{{$list->bank_name}}</td>
							<td>{{$list->type}}</td>
							<td class="col_amount">{{$list->amount}}</td>
							<td>
								@if($list->status == 10)
								<span class="badge badge-danger tbl_badge">Cancelled</span>
								@endif

							</td>
							<td>{{$list->date_created}}</td>
						</tr>
					@endforeach

				</tbody>

			</table>
		</div>
	</div>
</div>

<?php
	$dep_amount = number_format($dep_amount,2);
	$with_amount = number_format($with_amount,2);
?>
@endsection
@push('scripts')


<script type="text/javascript">
	var dt_table;
	var typingTimer;                
	var doneTypingInterval = 400;
	var id_chart_account_holder;
var opcode = 0; //Add
$(document).ready(function(){
		$('#spn_total_deposit').text("₱"+'{{$dep_amount}}');
		$('#spn_total_widthdrawal').text("₱"+'{{$with_amount}}')
		//Initialize Datatable
		$('#tbl_bank_transaction thead tr').clone(true).appendTo( '#tbl_bank_transaction thead' );
		$('#tbl_bank_transaction thead tr:eq(1)').addClass("head_rem")
		$('#tbl_bank_transaction thead tr:eq(1) th').each( function (i) {
			var title = $(this).text();
			$(this).addClass('col_search');
			$(this).html( '<input type="text" placeholder="Search '+title+'" style="width:100%;" class="txt_head_search head_search"/> ' );
			$( 'input', this ).on( 'keyup change', function (){
				var val = this.value;
				clearTimeout(typingTimer);
				typingTimer = setTimeout(function(){
					if(dt_table.column(i).search() !== val ) {
						dt_table
						.column(i)
						.search( val )
						.draw();
					}
				}, doneTypingInterval);
			});
		});
		dt_table    = init_table();
		$('#tbl_bank_transaction .head_rem').remove();
		$('.dt-buttons').removeClass('btn-group')
		$('.dt-buttons').find('button').removeClass('btn-secondary');
	})

function init_table(){
	var config = {
		order: [],
		"lengthChange": true, 
		"autoWidth": false,
		scrollCollapse: true,
		scrollY: '70vh',
		scrollX : true,
		orderCellsTop: true,
		"bPaginate": false,
		dom: 'Bfrtip',
		buttons: [

		]
	}
	var table = $("#tbl_bank_transaction").DataTable(config)
	console.log({table});
	return table;
}

$(function() {
	$.contextMenu({
		selector: '.row_bank_transaction',
		callback: function(key, options) {
			var m = "clicked: " + key;
			var id_bank_transaction= $(this).attr('data-key');
			if(key == "view"){
				window.location = '/bank_transaction/view/'+id_bank_transaction+'?href='+'{{urlencode(url()->full())}}';
			}else if(key == "print"){
				print("/cash_receipt/print?reference="+id_bank_transaction);
			}else if(key == "cancel"){
				show_cancel_modal(id_bank_transaction);
			}
		},
		items: {
			"view": {name: "View Bank Transaction", icon: "fas fa-eye"},
			// "print" : {name: "Print Cash Receipt", icon: "fas fa-print"},
			// "cancel" : {name: "Cancel Cash Receipt", icon: "fas fa-times"},
			"sep1": "---------",
			"quit": {name: "Close", icon: "fas fa-times" }
		}
	});   
});
function redirect_add(){
	window.location = '/bank_transaction/create'+'?href='+'{{urlencode(url()->full())}}';
}
</script>
@if($credential->is_create)
<script type="text/javascript">
	$(document).ready(function(){
		$('#tbl_bank_transaction_filter').prepend('<a class="btn bg-gradient-info" onclick="redirect_add()" style="float:left"><i class="fa fa-plus"></i>&nbsp;Create Bank Transaction</a>')
	})
</script>
@endif
@endpush
