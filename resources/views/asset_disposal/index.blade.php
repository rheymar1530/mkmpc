@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
.main_form{
	font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
}
#tbl_asset_disposal  tr>td{
    padding:3px ;
    vertical-align:top;
    font-family: Arial !important;
    font-size: 12px !important;
}
#tbl_asset_disposal  tr::nth-child(0)>th{
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
	<h3>Asset Disposal List&nbsp;&nbsp;	</h3>
	<div class="row">
       <div class="col-md-12">
            <table id="tbl_asset_disposal" class="table table-hover table-striped" style="white-space: nowrap;margin-top: 10px;">
                <thead>
                    <tr class="table_header_dblue">
                    	<th>ID Asset Disposal</th>
                        <th>Date</th>
                        <th>Disposed Amount</th>
                        <th>OR No</th>
                        <th>Loss/Gain Amount</th>
                        <th></th>
                        <th>Status</th>

                    </tr>
                </thead>
                <tbody id="list_body">
                	@foreach($disposal_list as $list)
                		<tr class="asset_disposal_row" data-code="{{$list->id_asset_disposal}}">
                			<td>{{$list->id_asset_disposal}}</td>
                			<td>{{$list->date}}</td>
                			<td class="class_amount">{{number_format($list->disposed_amount,2)}}</td>
                			<td>{{$list->or_no}}</td>
                			<td class="class_amount">{{number_format($list->loss_gain_amount,2)}}</td>
                			<td><span class="badge badge-{{($list->loss_gain_status=='Loss')?'danger':'success'}} tbl_badge">{{$list->loss_gain_status}}</span></td>
                			
                			<td>
                				@if($list->status == "Cancelled")
                				<span class="badge badge-danger tbl_badge">{{$list->status}}</span>
                				@endif
                			</td>
                		
                			
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
	$('#tbl_asset_disposal thead tr').clone(true).appendTo( '#tbl_asset_disposal thead' );
	$('#tbl_asset_disposal thead tr:eq(1)').addClass("head_rem")
	$('#tbl_asset_disposal thead tr:eq(1) th').each( function (i) {
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
	$('#tbl_asset_disposal .head_rem').remove();
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
	
	var table = $("#tbl_asset_disposal").DataTable(config)
	console.log({table});
	return table;
}

$(function() {
    $.contextMenu({
        selector: '.asset_disposal_row',
        callback: function(key, options) {
            var m = "clicked: " + key;
			var id_asset_disposal = $(this).attr('data-code');
			console.log({id_asset_disposal})
			if(key == "view"){
				window.location = '/asset_disposal/view/'+id_asset_disposal +'?href='+'{{urlencode(url()->full())}}';
			}
        },
        items: {
        	"view": {name: "View Asset Disposal", icon: "fas fa-eye"},
            "sep1": "---------",
            "quit": {name: "Close", icon: "fas fa-times" }
        }
    });   
});
function redirect_add(){
	window.location = '/asset_disposal/create'+'?href='+'{{urlencode(url()->full())}}';
}


</script>
@if($credential->is_create)
<script type="text/javascript">
	$(document).ready(function(){
		$('#tbl_asset_disposal_filter').append('<a class="btn bg-gradient-info" onclick="redirect_add()" style="float:left"><i class="fa fa-plus"></i>&nbsp;Add Asset Disposal</a>')
	})
</script>
@endif
<script type="text/javascript">
	$(document).ready(function(){
		$('#tbl_asset_disposal_filter').append('<a class="btn bg-gradient-primary" onclick="show_filter()" style="float:left;margin-left:10px"><i class="fa fa-eye"></i>&nbsp;Filtering Option</a>')
	})
	function show_filter(){
		$('#view_options').modal('show')
	}
</script>


@endpush
