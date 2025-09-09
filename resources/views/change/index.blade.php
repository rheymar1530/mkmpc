@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
.main_form{
	font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
}
#tbl_change  tr>td{
    padding:3px ;
    vertical-align:top;
    font-family: Arial !important;
    font-size: 12px !important;
}
#tbl_change  tr::nth-child(0)>th{
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
	<h3>Change Release&nbsp;&nbsp;	</h3>
		

	<div class="row">
       <div class="col-md-12">
            <table id="tbl_change" class="table table-hover table-striped" style="white-space: nowrap;margin-top: 10px;">
                <thead>
                    <tr class="table_header_dblue">
                    	<th>ID</th>
                        <th>Date</th>
                        <th>Payee</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date Created</th>
                    </tr>
                </thead>
                <tbody id="list_body">
                	@foreach($change_list as $list)
                		<tr class="repayment_change_row" data-code="{{$list->id_repayment_change}}" data-cdv="{{$list->id_cash_disbursement}}">
                			<td>{{$list->id_repayment_change}}</td>
                			<td>{{$list->date}}</td>
                			<td>{{$list->payee}}</td>
                			<td>{{$list->description}}</td>
                			<td class="class_amount">{{$list->amount}}</td>
                			<td>@if($list->status != "")
                				<span class="badge badge-danger tbl_badge">{{$list->status}}</span>
                			    @endif</td>
                			<td>{{$list->date_created}}</td>
                		</tr>
                	@endforeach
                </tbody>
            </table>
        </div>
	</div>
</div>
@include('change.filter_option_modal')
@include('global.print_modal')
@include('change.summary_modal')
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
	$('#tbl_change thead tr').clone(true).appendTo( '#tbl_change thead' );
	$('#tbl_change thead tr:eq(1)').addClass("head_rem")
	$('#tbl_change thead tr:eq(1) th').each( function (i) {
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
	$('#tbl_change .head_rem').remove();
	$('.dt-buttons').removeClass('btn-group')
	$('.dt-buttons').find('button').removeClass('btn-secondary');



	<?php
	if(request()->get('show_print') == 1){
		echo "show_print_summary()";
	}
	
	?>


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
	
	var table = $("#tbl_change").DataTable(config)
	console.log({table});
	return table;
}

$(function() {
    $.contextMenu({
        selector: '.repayment_change_row',
        callback: function(key, options) {
            var m = "clicked: " + key;
			var repayment_token = $(this).attr('data-code');
			var cdv = $(this).attr('data-cdv');
			console.log({repayment_token})
			if(key == "view"){
				window.location = '/change/view/'+repayment_token +'?href='+'{{ urlencode(str_replace("show_print=1","",url()->full())) }}';
			}else if(key == "edit"){
				window.location = '/change/edit/'+repayment_token +'?href='+'{{ urlencode(str_replace("show_print=1","",url()->full())) }}';
			}else if(key == "print"){
				print_page("/cash_disbursement/print/"+cdv)
			}
        },
        items: {
        	"view": {name: "View Released Change", icon: "fas fa-eye"},
        	"edit": {name: "Edit Released Change", icon: "fas fa-edit"},
        	"print": {name: "Print Cash Disbursement", icon: "fas fa-print"},
            "sep1": "---------",
            "quit": {name: "Close", icon: "fas fa-times" }
        }
    });   
});
function redirect_add(){
	window.location = '/change/create'+'?href='+'{{ urlencode(str_replace("show_print=1","",url()->full())) }}';
}


</script>
@if($credential->is_create)
<script type="text/javascript">
	$(document).ready(function(){
		$('#tbl_change_filter').append('<a class="btn bg-gradient-info" onclick="redirect_add()" style="float:left"><i class="fa fa-plus"></i>&nbsp;Create Change Release</a>')
	})
</script>
@endif

<script type="text/javascript">
	$(document).ready(function(){
		$('#tbl_change_filter').append('<a class="btn bg-gradient-danger" onclick="show_print_summary()" style="float:left;margin-left:10px;display:none"><i class="fa fa-print"></i>&nbsp;Loan Payment Summary</a><a class="btn bg-gradient-primary" onclick="show_filter()" style="float:left;margin-left:10px"><i class="fa fa-eye"></i>&nbsp;Filtering Option</a>')
	})
	function show_filter(){
		$('#view_options').modal('show')
	}
	function show_print_summary(){
		$('#summary_date_modal').modal('show');
	}
</script>

<script type="text/javascript">
	$(document).ready(function(){
		// $('#tbl_change_filter').append('<a class="btn bg-gradient-danger" onclick="print_page(`/change/summary`)" style="float:left;margin-left:10px"><i class="fa fa-print"></i>&nbsp;Generate Change Payable</a>')
		$('#tbl_change_filter').append(`<div class="btn-group" style="float:left;margin-left:10px">
										<button type="button" class="btn btn-danger  dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										Generate Summary
										</button>
										<div class="dropdown-menu" style="">
										<a class="dropdown-item" href="javascript:void(0)" onclick="print_page('/change/summary')">Change Payable</a>
										<a class="dropdown-item" href="javascript:void(0)" onclick="$('#summary_date_modal').modal('show')">Released Change</a>




</div>
</div>`)
	})
</script>


@endpush
