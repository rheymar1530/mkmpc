@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">

#tbl_chart_account  tr>td{
    padding:3px ;
    vertical-align:top;
    font-family: Arial !important;
    font-size: 12px !important;
}
#tbl_chart_account  tr::nth-child(0)>th{
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


.modal-conf {
    max-width:60% !important;
    min-width:60% !important;
    /*margin: auto;*/
}
.form-label{
	margin-bottom: 4px !important;
}

</style>
<div class="container-fluid" style="margin-top:-15px">
	<h3>Chart of Accounts 
		@if($credential->is_create)
			<a class="btn bg-gradient-info" onclick="show_create_modal()"><i class="fa fa-plus"></i>&nbsp;Add Chart</a>
		@endif
	</h3>
	<div class="row">
       <div class="col-md-12">
            <!-- <button class="btn btn-sm bg-gradient-primary" onclick="open_pickup_modal()">Create Pickup Request</button> -->
            <table id="tbl_chart_account" class="table table-hover table-striped" style="white-space: nowrap;margin-top: 10px;">
                <thead>
                    <tr class="table_header_dblue">
                    	<th>ID Chart Account</th>
                        <th>Code</th>
                        <th>Description</th>
                        <th>Category</th>
                        <th>Line Item</th>
                        <th>Normal</th>
                        <th>Type</th>
                        <th>Sub Type</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="list_body">
                	@foreach($charts as $chart)
                		<tr class="chart_row" data-chart-id="{{$chart->id_chart_account}}">
                			<td>{{$chart->id_chart_account}}</td>
                			<td>{{$chart->account_code}}</td>
                			<td>{{$chart->description}}</td>
                			<td>{{$chart->cat_desc}}</td>
                			<td>{{$chart->line_desc}}</td>
                			<td>{{$chart->cred_deb_description}}</td>
                			<td>{{$chart->type_description}}</td>
                			<td>{{$chart->sub_type}}</td>
                			<td>
                				@if($chart->status != "")
                				<span class="badge badge-danger text-xs">Inactive</span>
                				@endif
                			</td>
                		</tr>
                	@endforeach
                </tbody>
            </table>
        </div>
	</div>
</div>
@if($credential->is_create || $credential->is_edit)
@include('accounting.chart_of_account_modal')
@endif
@endsection
@push('scripts')
<script src="{{URL::asset('dist/js/pickup_request/pickup_request.js')}}" type='text/javascript'></script>
<script type="text/javascript">
var dt_table;
var typingTimer;                
var doneTypingInterval = 400;
var id_chart_account_holder;
var opcode = 0; //Add
var is_edit = '<?php echo $credential->is_edit?>';
var is_create = '<?php echo $credential->is_create?>';
$(document).ready(function(){

		//Initialize Datatable
	$('#tbl_chart_account thead tr').clone(true).appendTo( '#tbl_chart_account thead' );
	$('#tbl_chart_account thead tr:eq(1)').addClass("head_rem")
	$('#tbl_chart_account thead tr:eq(1) th').each( function (i) {
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
	$('#tbl_chart_account .head_rem').remove();
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
	var table = $("#tbl_chart_account").DataTable(config)
	console.log({table});
	return table;
}

$(function() {
    $.contextMenu({
        selector: '.chart_row',
        callback: function(key, options) {
            var m = "clicked: " + key;
			var id_chart_account= $(this).attr('data-chart-id');

			if(key == "view"){
				load_chart(id_chart_account);
			}
			console.log({id_chart_account})

        },
        items: {
        	"view": {name: "View Chart", icon: "fas fa-eye"},
            "sep1": "---------",
            "quit": {name: "Close", icon: "fas fa-times" }
        }
    });   
});

function show_create_modal(){
	var $length = $('.crd_chart').length;
	$('#div_btn_add').html($btn_add_chart);
	opcode = 0;
	if($length == 0){
		append_chart();
	}
	$('#spn_btn_save').html('');
	if(is_create == 1){
		$('#spn_btn_save').html($btn_save);
	}
	$('#chart_modal').modal('show');
}
function load_chart(id_chart_account){
	$.ajax({
		type         :        'GET',
		url          :        '/charts_of_account/load_chart',
		data         :        {'id_chart_account' : id_chart_account},
		beforeSend   :        function(){
							  show_loader()
		},
		success      :        function(response){
							  hide_loader()
							  console.log({response});
							  $('#chart_modal').modal('show')
							  set_chart_details(response.details)
							  
		},
		error: function(xhr, status, error) {
			hide_loader()
			var errorMessage = xhr.status + ': ' + xhr.statusText
			isClick = false;
			Swal.fire({
				title: "Error-" + errorMessage,
				text: '',
				icon: 'warning',
				confirmButtonText: 'OK',
				confirmButtonColor: "#DD6B55"
			});
		}
	})
}
function set_chart_details(details){
	$('#div_chart_form').html(''); // Clear the chart cards
	append_chart();
	var new_card = $('.crd_chart').last();
	var inputs = ['txt_code','txt_description','sel_cat','sel_line_item','sel_normal','sel_type','sel_sub_type'];
	new_card.find('.txt_code').val(details.account_code);
	new_card.find('.txt_description').val(details.description);
	new_card.find('.sel_cat').val(details.id_chart_account_category);
	new_card.find('.sel_line_item').val(details.id_chart_account_line_item);
	new_card.find('.sel_normal').val(details.normal);
	new_card.find('.sel_type').val(details.id_chart_account_type);
	new_card.find('.sel_sub_type').val(details.id_chart_account_subtype);
	new_card.find('.sel_status').val(details.ac_active);
	new_card.find('.sel_dep_account').val(details.depreciation_account);
	new_card.find('.sel_ac_dep_account').val(details.ac_depreciation_account);
	new_card.find('.sel_cash_flow').val(details.id_cash_flow);
	id_chart_account_holder = details.id_chart_account;
	opcode = 1;
	$('#btn_add_chart').remove();
	$('#spn_btn_save').html('');
	if(is_edit == 1){
		$('#spn_btn_save').html($btn_save);
	}
}
</script>
@endpush
