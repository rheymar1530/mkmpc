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
	<h3>Repayment&nbsp;&nbsp;	</h3>
	<div class="row">
       <div class="col-md-12">
            <table id="tbl_repayment_transaction" class="table table-hover table-striped" style="white-space: nowrap;margin-top: 10px;">
                <thead>
                    <tr class="table_header_dblue">
                    	<th>ID</th>
                        <th>Loan Payment Date</th>
                        <th>Loan Due Date</th>
                        <th>Payment Mode</th>
                        <th>Member</th>
                        <th>OR Number</th>
                        <th>Swiping Amount</th>
                        <th>Total Payment</th>
                        <th>Change</th>
                        <th>Status</th>
                        <th>Date Created</th>
                    </tr>
                </thead>
                <tbody id="list_body">
                	@foreach($repayment_list as $list)
                		<tr class="repayment_transaction_row" data-code="{{$list->repayment_token}}">
                			<td>{{$list->id_repayment_transaction}}</td>
                			<td>{{$list->repayment_date}}</td>
                			<td>{{$list->loan_due_date}}</td>
                			<td>{{$list->paymode}}</td>
                			<td>{{$list->member_name}}</td>
                			<td>{{$list->or_no}}</td>
                			<td class="class_amount">{{$list->swiping_amount}}</td>
                			<td class="class_amount">{{$list->total_payment}}</td>
                			<td class="class_amount">{{$list->change}}</td>
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
@include('repayment.filter_option_modal')
@include('repayment.summary_date_modal')
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
			var repayment_token = $(this).attr('data-code');
			console.log({repayment_token})
			if(key == "view"){
				window.location = '/repayment/view/'+repayment_token +'?href='+'{{urlencode(url()->full())}}';
			}
        },
        items: {
        	"view": {name: "View Loan Payment Transaction", icon: "fas fa-eye"},
            "sep1": "---------",
            "quit": {name: "Close", icon: "fas fa-times" }
        }
    });   
});
function redirect_add(){
	window.location = '/repayment/create'+'?href='+'{{urlencode(url()->full())}}';
}


</script>
@if($credential->is_create)
<script type="text/javascript">
	$(document).ready(function(){
		$('#tbl_repayment_transaction_filter').append('<a class="btn bg-gradient-info" onclick="redirect_add()" style="float:left"><i class="fa fa-plus"></i>&nbsp;Create Loan Payment</a>')
	})
</script>
@endif
<script type="text/javascript">
	$(document).ready(function(){
		$('#tbl_repayment_transaction_filter').append('<a class="btn bg-gradient-danger" onclick="show_repayment_summary()" style="float:left;margin-left:10px"><i class="fa fa-print"></i>&nbsp;Loan Payment Summary</a><a class="btn bg-gradient-primary" onclick="show_filter()" style="float:left;margin-left:10px"><i class="fa fa-eye"></i>&nbsp;Filtering Option</a>')
	})
	function show_filter(){
		$('#view_options').modal('show')
	}
	function show_repayment_summary(){
		$('#summary_date_modal').modal('show');
	}
</script>


@endpush
