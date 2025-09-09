@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
.main_form{
	font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
}
#tbl_employee  tr>td{
    padding:3px ;
    vertical-align:top;
    font-family: Arial !important;
    font-size: 12px !important;
}
#tbl_employee  tr::nth-child(0)>th{
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
	<h3>Employee List&nbsp;&nbsp;	</h3>
	<div class="row">
       <div class="col-md-12">
            <table id="tbl_employee" class="table table-hover table-striped" style="white-space: nowrap;margin-top: 10px;">
                <thead>
                    <tr class="table_header_dblue">
                    	<th>ID Employee</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Branch</th>
                        <th>Department</th>
                        <th>Position</th>
                        <th>Employment Status</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="list_body">
                	@foreach($employee_list as $list)
                		<tr class="employee_row" data-code="{{$list->id_employee}}">
                			<td>{{$list->id_employee}}</td>
                			<td>{{$list->name}}</td>
                			<td>{{$list->e_type}}</td>
                			<td>{{$list->branch_name}}</td>
                			<td>{{$list->department}}</td>
                			<td>{{$list->position}}</td>
                			<td>{{$list->employee_status}}</td>
                			<td><span class="badge badge-{{($list->status=='Inactive')?'danger':'success'}} tbl_badge">{{$list->status}}</span></td>
                		
                			
                				<!-- <span class="badge badge-danger tbl_badge">Cancelled</span> -->
      
              
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
var opcode = 0; //Add
$(document).ready(function(){
	//Initialize Datatable
	$('#tbl_employee thead tr').clone(true).appendTo( '#tbl_employee thead' );
	$('#tbl_employee thead tr:eq(1)').addClass("head_rem")
	$('#tbl_employee thead tr:eq(1) th').each( function (i) {
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
	$('#tbl_employee .head_rem').remove();
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
	
	var table = $("#tbl_employee").DataTable(config)
	console.log({table});
	return table;
}

$(function() {
    $.contextMenu({
        selector: '.employee_row',
        callback: function(key, options) {
            var m = "clicked: " + key;
			var id_employee = $(this).attr('data-code');
			console.log({id_employee})
			if(key == "view"){
				window.location = '/employee/view/'+id_employee +'?href='+'{{urlencode(url()->full())}}';
			}
        },
        items: {
        	"view": {name: "View Employee", icon: "fas fa-eye"},
            "sep1": "---------",
            "quit": {name: "Close", icon: "fas fa-times" }
        }
    });   
});
function redirect_add(){
	window.location = '/employee/create'+'?href='+'{{urlencode(url()->full())}}';
}


</script>
@if($credential->is_create)
<script type="text/javascript">
	$(document).ready(function(){
		$('#tbl_employee_filter').append('<a class="btn bg-gradient-info" onclick="redirect_add()" style="float:left"><i class="fa fa-plus"></i>&nbsp;Add Employee</a>')
	})
</script>
@endif
<script type="text/javascript">
	$(document).ready(function(){
		$('#tbl_employee_filter').append('<a class="btn bg-gradient-primary" onclick="show_filter()" style="float:left;margin-left:10px"><i class="fa fa-eye"></i>&nbsp;Filtering Option</a>')
	})
	function show_filter(){
		$('#view_options').modal('show')
	}
</script>


@endpush
