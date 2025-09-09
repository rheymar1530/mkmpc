@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
.main_form{
	font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
}
#tbl_asset  tr>td{
    padding:3px ;
    vertical-align:top;
    font-family: Arial !important;
    font-size: 12px !important;
}
#tbl_asset  tr::nth-child(0)>th{
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
	<h3>{{$module_title}} List&nbsp;&nbsp;	</h3>
	<div class="row">
       <div class="col-md-12">
            <table id="tbl_asset" class="table table-hover table-striped" style="white-space: nowrap;margin-top: 10px;">
                <thead>
                    <tr class="table_header_dblue">
                    	<th>Asset ID</th>
                        <th>Purchase Date</th>
                        <th>Type</th>
                        <th>Branch</th>
                        <th>Valuation Date</th>
                        
                        <th>ID Cash Disbursement</th>
             
                        <th>ID Journal Voucher</th>
                        <th>Status</th>
                        <th>Date Created</th>

                    </tr>
                </thead>
                <tbody id="list_body">
                	@foreach($assets as $list)
                		<tr class="asset_row" data-code="{{$list->id_asset}}">
                			<td>{{$list->id_asset}}</td>
                			<td>{{$list->purchase_date}}</td>
                			<td>{{$list->type}}</td>

                			
                			<td>{{$list->branch_name}}</td>
                			<td>{{$list->valuation_date}}</td>
                			<td>{{$list->id_cash_disbursement}}</td>
                			<td>{{$list->id_journal_voucher}}</td>
                			
                			<td><span class="badge badge-{{($list->status=='Cancelled')?'danger':'success'}} tbl_badge">{{$list->status}}</span></td>
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
var opcode = 0; //Add
$(document).ready(function(){
	//Initialize Datatable
	$('#tbl_asset thead tr').clone(true).appendTo( '#tbl_asset thead' );
	$('#tbl_asset thead tr:eq(1)').addClass("head_rem")
	$('#tbl_asset thead tr:eq(1) th').each( function (i) {
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
	$('#tbl_asset .head_rem').remove();
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
	
	var table = $("#tbl_asset").DataTable(config)
	console.log({table});
	return table;
}

$(function() {
    $.contextMenu({
        selector: '.asset_row',
        callback: function(key, options) {
            var m = "clicked: " + key;
			var id_asset = $(this).attr('data-code');
			console.log({id_asset})
			if(key == "view"){
				window.location = '/asset/view/'+id_asset +'?href='+'{{urlencode(url()->full())}}';
			}else if(key == "edit"){
				window.location = '/asset/edit/'+id_asset +'?href='+'{{urlencode(url()->full())}}';
			}
        },
        items: {
        	"view": {name: "View Asset", icon: "fas fa-eye"},
        	"edit": {name: "Edit Asset", icon: "fas fa-edit"},
            "sep1": "---------",
            "quit": {name: "Close", icon: "fas fa-times" }
        }
    });   
});
function redirect_add(){
	window.location = '/asset/'+'{{$route}}'+'/add?href='+'{{urlencode(url()->full())}}';
}


</script>
@if($credential->is_create)
<script type="text/javascript">
	$(document).ready(function(){
		$('#tbl_asset_filter').append('<a class="btn bg-gradient-info" onclick="redirect_add()" style="float:left"><i class="fa fa-plus"></i>&nbsp;Add '+'{{$module_title}}'+'</a>')
	})
</script>
@endif
<script type="text/javascript">
	$(document).ready(function(){
		$('#tbl_asset_filter').append('<a class="btn bg-gradient-primary" onclick="show_filter()" style="float:left;margin-left:10px"><i class="fa fa-eye"></i>&nbsp;Filtering Option</a>')
	})
	function show_filter(){
		$('#view_options').modal('show')
	}
</script>


@endpush
