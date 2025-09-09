@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
.main_form{
	font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
}
#tbl_cdv  tr>td{
    padding:3px ;
    vertical-align:top;
    font-family: Arial !important;
    font-size: 12px !important;
}
#tbl_cdv  tr::nth-child(0)>th{
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
<div class="container-fluid main_form" style="margin-top:-20px">
	<h3>{{$title_mod}}&nbsp;&nbsp;	</h3>
	<div class="row">
       <div class="col-md-12">
            <table id="tbl_cdv" class="table table-hover table-striped" style="white-space: nowrap;margin-top: 10px;">
                <thead>
                    <tr class="table_header_dblue">
                    	<th>ID</th>
                        <th>Date</th>
                        <th>Payee Type</th>
                        <th>Payee</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Reference</th>
                        <th>Status</th>
                       
                    </tr>
                </thead>
                <tbody id="list_body">
                	@foreach($cdv_list as $list)
                		<tr class="jv_row" data-code="{{$list->id_cash_disbursement}}">
                			<td>{{$list->id_cash_disbursement}}</td>
                			<td>{{$list->date}}</td>
                			<td>{{$list->payee_type}}</td>
                			<td>{{$list->payee}}</td>
                			<td>{{$list->description}}</td>
                			<td class="class_amount">{{number_format($list->total,2)}}</td>
                			<td>{{$list->reference}}</td>
                			<td>
								@if($list->status == 10)
								<span class="badge badge-danger tbl_badge">Cancelled</span>
								@endif

							</td>
                		</tr>
                	@endforeach
                </tbody>
            </table>
        </div>
	</div>
</div>
@include('global.print_modal')
@include('cash_disbursement.filter_option_modal')
@endsection
@push('scripts')
<script type="text/javascript">
var dt_table;
var typingTimer;                
var doneTypingInterval = 400;
var id_chart_account_holder;
var opcode = 0; //Add

var PARENT_ROUTE = '/cdv'+'/{{$route}}';
$(document).ready(function(){
	//Initialize Datatable
	$('#tbl_cdv thead tr').clone(true).appendTo( '#tbl_cdv thead' );
	$('#tbl_cdv thead tr:eq(1)').addClass("head_rem")
	$('#tbl_cdv thead tr:eq(1) th').each( function (i) {
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
	$('#tbl_cdv .head_rem').remove();
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
		buttons: ['excel']
	}
	
	var table = $("#tbl_cdv").DataTable(config)
	console.log({table});
	return table;
}

$(function() {
    $.contextMenu({
        selector: '.jv_row',
        callback: function(key, options) {
            var m = "clicked: " + key;
			var id_cash_disbursement = $(this).attr('data-code');
			console.log({id_cash_disbursement})
			if(key == "view"){
				window.location = PARENT_ROUTE+'/view/'+id_cash_disbursement +'?href='+'{{urlencode(url()->full())}}';
			}else if(key == "print"){
				print_page('/cash_disbursement/print/'+id_cash_disbursement)
			}
        },
        items: {
        	"view": {name: "View "+'{{$title_mod}}', icon: "fas fa-eye"},
        	"print": {name: "Print Cash Disbursement Voucher", icon: "fas fa-print"},
            "sep1": "---------",
            "quit": {name: "Close", icon: "fas fa-times" }
        }
    });   
});
function redirect_add(){
	window.location = PARENT_ROUTE+'/create'+'?href='+'{{urlencode(url()->full())}}';
}

</script>
@if($credential->is_create)
<script type="text/javascript">
	$(document).ready(function(){
		$('#tbl_cdv_filter').append('<a class="btn bg-gradient-info" onclick="redirect_add()" style="float:left"><i class="fa fa-plus"></i>&nbsp;Create '+'{{$title_mod}}'+'</a>')
	})
</script>
@endif
<script type="text/javascript">
	$(document).ready(function(){
		$('#tbl_cdv_filter').append('<a class="btn bg-gradient-primary" onclick="show_filter()" style="float:left;margin-left:10px"><i class="fa fa-eye"></i>&nbsp;Filtering Option</a>')
	})
	function show_filter(){
		$('#view_options').modal('show')
	}
	function show_repayment_summary(){
		$('#summary_date_modal').modal('show');
	}
</script>


@endpush
