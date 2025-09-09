@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
.main_form{
	font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
}
#tbl_charges_group  tr>td{
    padding:3px ;
    vertical-align:top;
    font-family: Arial !important;
    font-size: 12px !important;
}
#tbl_charges_group  tr::nth-child(0)>th{
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
	<h3>Charges Group&nbsp;&nbsp; 
		@if($credential->is_create)
			<a class="btn bg-gradient-info" onclick="redirect_add()"><i class="fa fa-plus"></i>&nbsp;Add Charges Group</a>
		@endif
	</h3>
	<div class="row">
       <div class="col-md-12">
            <table id="tbl_charges_group" class="table table-hover table-striped" style="white-space: nowrap;margin-top: 10px;">
                <thead>
                    <tr class="table_header_dblue">
                    	<th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Date Created</th>

                    </tr>
                </thead>
                <tbody id="list_body">
                	@foreach($charges_list as $list)
                		<tr class="charges_group_row" data-code="{{$list->id_charges_group}}">
                			<td>{{$list->id_charges_group}}</td>
                			<td>{{$list->name}}</td>
                			<td>{{$list->description}}</td>
                			<td>
                				<span class="badge badge-{{ ($list->active == 'Inactive'?'danger':'success') }} tbl_badge">{{$list->active}}</span>
                			</td>
                			<td>{{$list->date_created}}</td>
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
	$('#tbl_charges_group thead tr').clone(true).appendTo( '#tbl_charges_group thead' );
	$('#tbl_charges_group thead tr:eq(1)').addClass("head_rem")
	$('#tbl_charges_group thead tr:eq(1) th').each( function (i) {
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
	$('#tbl_charges_group .head_rem').remove();
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
	var table = $("#tbl_charges_group").DataTable(config)
	console.log({table});
	return table;
}

$(function() {
    $.contextMenu({
        selector: '.charges_group_row',
        callback: function(key, options) {
            var m = "clicked: " + key;
			var id_charges_group= $(this).attr('data-code');
			if(key == "view"){
				window.location = '/charges/view/'+id_charges_group+'?href='+'{{urlencode(url()->full())}}';
			}
			console.log({id_chart_account})
        },
        items: {
        	"view": {name: "View Charges Group", icon: "fas fa-eye"},
            "sep1": "---------",
            "quit": {name: "Close", icon: "fas fa-times" }
        }
    });   
});
function redirect_add(){
	window.location = '/charges/create'+'?href='+'{{urlencode(url()->full())}}';
}


</script>
@endpush
