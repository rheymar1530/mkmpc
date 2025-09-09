@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
	.main_form{
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
	}
	#tbl_rc  tr>td{
		padding:3px ;
		vertical-align:top;
		font-family: Arial !important;
		font-size: 12px !important;
	}
	#tbl_rc  tr::nth-child(0)>th{
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
				return 'primary';
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
	<h3>Loan Payment (Bulk)&nbsp;&nbsp;	</h3>
	<div class="row">
		<div class="col-md-12">
			<table id="tbl_rc" class="table table-hover table-striped" style="white-space: nowrap;margin-top: 10px;">
				<thead>
					<tr class="table_header_dblue">
						<th>Loan Payment ID</th>
						<th>Group</th>
						<th>Brgy/LGU</th>
						<th>Transaction Date</th>
						<th>Total Amount</th>
						<th>Status</th>
						<th>Date Created</th>
						
					</tr>
				</thead>
				<tbody id="list_body">
					@foreach($repayments as $list)
					<tr class="repayment-row" data-code="{{$list->id_repayment}}">
						<td class="text-center">{{$list->id_repayment}}</td>
						<td>{{$list->group_}}</td>
						<td>{{$list->baranggay_lgu}}</td>
						<td>{{$list->date}}</td>
						<td class="text-right">{{number_format($list->total_amount,2)}}</td>
						<td>
							@if($list->status == 10)
							<span class="badge badge-{{status_class($list->status)}} text-xs">
							{{$list->status_description}}
							</span>
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
	$('#tbl_rc thead tr').clone(true).appendTo( '#tbl_rc thead' );
	$('#tbl_rc thead tr:eq(1)').addClass("head_rem")
	$('#tbl_rc thead tr:eq(1) th').each( function (i) {
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
	$('#tbl_rc .head_rem').remove();
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
	
	var table = $("#tbl_rc").DataTable(config)
	console.log({table});
	return table;
}

$(function() {
	$.contextMenu({
		selector: '.repayment-row',
		callback: function(key, options) {
			var m = "clicked: " + key;
			var id_repayment = $(this).attr('data-code');
			console.log({id_repayment})
			if(key == "view"){
				window.location = '/bulk-repayment/view/'+id_repayment +'?href='+'{{urlencode(url()->full())}}';
			}else if(key == "edit"){
				window.location = '/bulk-repayment/edit/'+id_repayment +'?href='+'{{urlencode(url()->full())}}';
			}else if(key == "print"){
				print_page(`/repayment-check/print/${id_repayment}`);
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
	window.location = '/bulk-repayment/create'+'?href='+'{{urlencode(url()->full())}}';
}


</script>
<!-- if($credential->is_create) -->
<script type="text/javascript"> 
	$(document).ready(function(){
		$('#tbl_rc_filter').append('<a class="btn bg-gradient-info" onclick="redirect_add()" style="float:left"><i class="fa fa-plus"></i>&nbsp;Create Loan Payment (Bulk)</a>')
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
