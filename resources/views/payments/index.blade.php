@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
	.main_form{
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
	}
	#tbl_repayment_transaction  tr>td{
		padding:3px ;
		vertical-align:top;
		font-family: Arial !important;
		font-size: 12px !important;
	}
	#tbl_repayment_transaction  tr::nth-child(0)>th{
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

</style>
<div class="container-fluid main_form" style="margin-top:-20px">
	<h3>Payments&nbsp;&nbsp;	</h3>
	<div class="row">
		<div class="col-md-12">
			<table id="tbl_repayment_transaction" class="table table-hover table-striped" style="white-space: nowrap;margin-top: 10px;">
				<thead>
					<tr class="table_header_dblue">
						<th>Transaction ID</th>
						<th>Transaction Date</th>
						
						<th>Payment Mode</th>
						
						<th>OR Number</th>
						<th>Check Amount</th>
						<th>Total Payment</th>
						<th>Change</th>
						
						
					</tr>
				</thead>
				<tbody id="list_body">
					@foreach($repayment_list as $list)
					<tr class="repayment_transaction_row" data-id="{{$list->id_repayment_transaction}}">
						<td>{{$list->id_repayment_transaction}}</td>
						<td>{{$list->repayment_date}}</td>
						
						<td>{{$list->paymode}}</td>
						
						<td>{{$list->or_no}}</td>
						<td class="class_amount">{{$list->swiping_amount}}</td>
						<td class="class_amount">{{$list->total_payment}}</td>
						<td class="class_amount">{{$list->change}}</td>
						
						
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>
@include('payments.filter_option_modal')


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
	$('#tbl_repayment_transaction thead tr').clone(true).appendTo( '#tbl_repayment_transaction thead' );
	$('#tbl_repayment_transaction thead tr:eq(1)').addClass("head_rem")
	$('#tbl_repayment_transaction thead tr:eq(1) th').each( function (i) {
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
	$('#tbl_repayment_transaction .head_rem').remove();
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
		buttons: []
	}
	
	var table = $("#tbl_repayment_transaction").DataTable(config)
	console.log({table});
	return table;
}

$(function() {
	$.contextMenu({
		selector: '.repayment_transaction_row',
		callback: function(key, options) {
			var m = "clicked: " + key;
			var id_repayment_transaction = $(this).attr('data-id');
			console.log({id_repayment_transaction})
			if(key == "view"){
				window.location = '/payments/'+id_repayment_transaction +'?href='+'{{ urlencode(str_replace("show_print=1","",url()->full())) }}';
			}
		},
		items: {
			"view": {name: "View Payment", icon: "fas fa-eye"},
			"sep1": "---------",
			"quit": {name: "Close", icon: "fas fa-times" }
		}
	});   
});



</script>

<script type="text/javascript">
	$(document).ready(function(){
		$('#tbl_repayment_transaction_filter').append('<a class="btn bg-gradient-primary" onclick="show_filter()" style="float:left"><i class="fa fa-eye"></i>&nbsp;Filtering Option</a>')
	})
	function show_filter(){
		$('#view_options').modal('show')
	}

</script>

@endpush
