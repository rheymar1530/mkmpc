@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
.main_form{
	font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
}
#tbl_investment_product  tr>td{
    padding:3px ;
    vertical-align:top;
    font-family: Arial !important;
    font-size: 12px !important;
}
#tbl_investment_product  tr::nth-child(0)>th{
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

</style>
<div class="container-fluid main_form" style="margin-top:-20px">
	<h3>Investment Product&nbsp;&nbsp; 
		<!-- if($credential->is_create) -->
		<a class="btn bg-gradient-info" onclick="redirect_add()"><i class="fa fa-plus"></i>&nbsp;Create Investment Product</a>
		<!-- endif -->
	</h3>
	<div class="row">
       <div class="col-md-12">
            <table id="tbl_investment_product" class="table table-hover table-striped" style="white-space: nowrap;margin-top: 10px;">
                <thead>
                    <tr class="table_header_dblue">
                    	<th>ID</th>
                        <th>Name</th>
                        <th>Amount Range</th>
                        <th>Interest Type</th>
                        <th>Interest Period</th>
                        <th>Withdrawable Part</th>
                        <th>Members Only</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="list_body">
                	@foreach($investment_products as $list)
                		<tr class="investment_product_row" data-code="{{$list->id_investment_product}}">
                			<td class="text-center">{{$list->id_investment_product}}</td>
                			<td>{{$list->product_name}}</td>
                			<td>{{$list->amount}}</td>
                			<td>{{$list->interest_type}}</td>
                			<td>{{$list->interest_period}}</td>
                			<td>{{$list->withdrawable_part}}</td>
                			<td>{{$list->member_only}}</td>

                			<td>
                				<span class="badge badge-{{ ($list->status == 'Inactive'?'danger':'success') }} tbl_badge">{{$list->status}}</span>
                			</td>
                		</tr>
                	@endforeach
                </tbody>
            </table>
        </div>
	</div>
</div>
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
	$('#tbl_investment_product thead tr').clone(true).appendTo( '#tbl_investment_product thead' );
	$('#tbl_investment_product thead tr:eq(1)').addClass("head_rem")
	$('#tbl_investment_product thead tr:eq(1) th').each( function (i) {
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
	$('#tbl_investment_product .head_rem').remove();
	$('.dt-buttons').removeClass('btn-group')
	$('.dt-buttons').find('button').removeClass('btn-secondary');

	$('#chart_modal').on('hidden.bs.modal', function (e) {
		if(opcode == 1){ //if edit
			$('#div_chart_form').html(''); // Clear the chart cards
		}
	})
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
	var table = $("#tbl_investment_product").DataTable(config)
	console.log({table});
	return table;
}

$(function() {
    $.contextMenu({
        selector: '.investment_product_row',
        callback: function(key, options) {
            var m = "clicked: " + key;
			var id_investment_product = $(this).attr('data-code');
			if(key == "view"){
				window.location = '/investment_product/view/'+id_investment_product +'?href='+'{{urlencode(url()->full())}}';
			}
			console.log({id_chart_account})
        },
        items: {
        	"view": {name: "View Investment Product", icon: "fas fa-eye"},
            "sep1": "---------",
            "quit": {name: "Close", icon: "fas fa-times" }
        }
    });   
});
function redirect_add(){
	window.location = '/investment_product/create'+'?href='+'{{urlencode(url()->full())}}';
}


</script>
@endpush
