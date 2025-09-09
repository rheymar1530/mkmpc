@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
	.main_form{
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
	}
	#tbl_deposit  tr>td{
		padding:3px ;
		vertical-align:top;
		font-family: Arial !important;
		font-size: 12px !important;
	}
	#tbl_deposit  tr::nth-child(0)>th{
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
	.tbl_badge{
		font-size: 13px;
	}
	.class_amount{
		text-align: right;
		padding-right: 10px !important;
	}
	.dt-buttons{
		display: none;
	}
</style>
<?php
	function status_class($status){
		switch ($status) {
			case 0:
				return 'success';
				break;
			case 1:
				return 'success';
				break;
			case 10:
				return 'danger';
				break;
			default:
				// code...
				break;
		}
	}
?>
<div class="container-fluid main_form" style="margin-top:-20px">
	<h3>Check Deposit&nbsp;&nbsp;	</h3>
	<div class="row">
		<div class="col-md-12">
			<table id="tbl_deposit" class="table-hover table-striped" style="margin-top: 10px;">
				<thead>
					<tr class="table_header_dblue">
						<th>Deposit ID</th>
						<th>Date</th>
						<th>Bank</th>
						<th>Check Nos</th>
						<th>Remarks</th>
						<th>Amount</th>
						
						<th>Status</th>
						<th>Date Created</th>
						
					</tr>
				</thead>
				<tbody id="list_body">
					@foreach($deposits as $list)
					<tr class="repayment-row" data-code="{{$list->id_check_deposit}}">
						<td class="text-center">{{$list->id_check_deposit}}</td>
						<td>{{$list->date_deposited}}</td>
						<td>{{$list->bank_name}}</td>
						<td>{{$list->check_nos}}</td>
						<td>{{$list->remarks}}</td>
						<td class="text-right">{{number_format($list->amount,2)}}</td>

						<td>
							
							<span class="badge badge-{{status_class($list->status)}} text-xs">
							{{$list->status_description}}
							</span>
							
						</td>
						<td>{{$list->date_created}}</td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>


@include('global.print_modal')

@endsection
@push('scripts')
<script type="text/javascript">
	var dt_table;
	var typingTimer;                
	var doneTypingInterval = 400;
	var id_chart_account_holder;
var opcode = 0; //Add

$(document).ready(function(){
	//Initialize Datatable
	$('#tbl_deposit thead tr').clone(true).appendTo( '#tbl_deposit thead' );
	$('#tbl_deposit thead tr:eq(1)').addClass("head_rem")
	$('#tbl_deposit thead tr:eq(1) th').each( function (i) {
		var title = $(this).text();
		$(this).addClass('col_search');
		$(this).html( '<input type="text" placeholder="Search '+title+'" style="width:100%;" class="txt_head_search head_search"/> ' );
		$( 'input', this ).on( 'keyup change', function (){
			var val = this.value;
			clearTimeout(typingTimer);
			typingTimer = setTimeout(function(){
				if(dt_table.column(i).search() !== val){
					dt_table
					.column(i)
					.search( val )
					.draw();
				}
			}, doneTypingInterval);
		});
	});
	dt_table    = init_table();
	$('#tbl_deposit .head_rem').remove();
	$('.dt-buttons').removeClass('btn-group')
	$('.dt-buttons').find('button').removeClass('btn-secondary');
});

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
		buttons: ['excel']
	}
	
	var table = $("#tbl_deposit").DataTable(config)
	console.log({table});
	return table;
}

$(function() {
	$.contextMenu({
		selector: '.repayment-row',
		callback: function(key, options) {
			var m = "clicked: " + key;
			var id_check_deposit = $(this).attr('data-code');
			console.log({id_check_deposit})
			if(key == "view"){
				window.location = '/check-deposit/view/'+id_check_deposit +'?href='+'{{urlencode(url()->full())}}';
			}else if(key == "edit"){
				window.location = '/check-deposit/edit/'+id_check_deposit +'?href='+'{{urlencode(url()->full())}}';
			}else if(key == "print"){
				print_page(`/repayment-check/print/${id_check_deposit}`);
			}
		},
		items: {
			"view": {name: "View", icon: "fas fa-eye"},
			"sep1": "---------",
			"quit": {name: "Close", icon: "fas fa-times" }
		}
	});   
});
function redirect_add(){
	window.location = '/check-deposit/create'+'?href='+'{{urlencode(url()->full())}}';
}


</script>
<!-- if($credential->is_create) -->
<script type="text/javascript"> 
	$(document).ready(function(){
		$('#tbl_deposit_filter').append('<a class="btn bg-gradient-info" onclick="redirect_add()" style="float:left"><i class="fa fa-plus"></i>&nbsp;Create Check Deposit</a>')
	})
</script>


<!-- endif -->
<script type="text/javascript">

	function show_filter(){
		$('#view_options').modal('show')
	}
	function show_repayment_summary(){
		$('#summary_date_modal').modal('show');
	}
</script>


@endpush
