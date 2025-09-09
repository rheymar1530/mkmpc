@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
.main_form{
	font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
}
.tbl_scheduler  tr>td{
    padding:3px ;
    vertical-align:top;
    font-family: Arial !important;
    font-size: 12px !important;
}
.tbl_scheduler  tr::nth-child(0)>th{
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
	<h3>Schedulers&nbsp;&nbsp;	</h3>
	<div class="row">
       <div class="col-md-12">
            <table id="tbl_scheduler" class="table table-hover table-striped tbl_scheduler" style="white-space: nowrap;margin-top: 10px;">
                <thead>
                    <tr class="table_header_dblue">
                    	<th>ID</th>
                    	<th>Date</th>
                    	<th>Type</th>
                    	<th>Schedule Type</th>
                        <th>Stop Date</th>
                        <th>Reference</th>
                        <th>Last Run</th>
                        <th>Next Run</th>
                        <th>Status</th>
                        <th>Date Created</th>
                    </tr>
                </thead>
                <tbody id="list_body">
                	@foreach($schedules as $list)
                	<?php
                		$link_route = '#';

                		switch($list->books){
                			case '1':
                				$link_route = "/journal_voucher/view/".$list->reference_no;
                				break;
                			case '2':
                				if($list->cdv_type == 2){
                					$ct = 'expenses';
                				}elseif($list->cdv_type == 3){
                					$ct= 'asset_purchase';
                				}elseif($list->cdv_type == 4){
                					$ct = 'others';
                				}
                				$link_route = "/cdv/$ct/view/".$list->reference_no;
                				break;
                		}
                	?>
                		<tr class="scheduler-row" data-code="{{$list->id_scheduler}}">
                			<td>{{$list->id_scheduler}}</td>
                			<td>{{$list->date}}</td>
                			<td>{{$list->type}}</td>
                			
                			<td>{{$list->schedule_type}}</td>
                			<td>{{$list->stop_date}}</td>
                			<td class="col-ref">
                				@if($list->reference_no != '')
                				<a href="{{$link_route}}" target="_blank">
                				{{$list->book_type}}# {{$list->reference_no}}
                				</a>
                				@endif
                			</td>
                			<!-- <td>{{$list->type}}# {{$list->reference_no}}</td> -->
                			<td>{{$list->last_run}}</td>
                			<td>{{$list->next_run}}</td>
                			<td>							
   
								<span class="badge badge-{{($list->status=='Active')?'success':'danger'}} tbl_badge">{{$list->status}}</span>
						
							</td>
                			<td>{{$list->date_created}}</td>
                		</tr>
                	@endforeach
                </tbody>
            </table>
        </div>
	</div>
</div>

@include('scheduler.scheduler_view')
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
	$('#tbl_scheduler thead tr').clone(true).appendTo( '#tbl_scheduler thead' );
	$('#tbl_scheduler thead tr:eq(1)').addClass("head_rem")
	$('#tbl_scheduler thead tr:eq(1) th').each( function (i) {
		var title = $(this).text();
		$(this).addClass('col_search');
		$(this).html( '<input type="text" placeholder="Search '+title+'" style="width:100%;" class="txt_head_search head_search"/> ' );
		$( 'input', this ).on( 'keyup change', function (){
			var val = this.value;
			clearTimeout(typingTimer);
			typingTimer = setTimeout(function(){
				if(dt_table.column(i).search() !== val){
					dt_table
					.column(i)
					.search( val )
					.draw();
				}
			}, doneTypingInterval);
		});
	});
	dt_table    = init_table();
	$('#tbl_scheduler .head_rem').remove();
	$('.dt-buttons').removeClass('btn-group')
	$('.dt-buttons').find('button').removeClass('btn-secondary');
});

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
	
	var table = $("#tbl_scheduler").DataTable(config)
	console.log({table});
	return table;
}

$(function() {
    $.contextMenu({
        selector: '.scheduler-row',
        callback: function(key, options) {
            var m = "clicked: " + key;
            var id_scheduler = $(this).attr('data-code');
			if(key == "view"){
				window.open('/scheduler/view/'+id_scheduler,'_self');
			}
        },
        items: {
        	"view": {name: "View Details", icon: "fas fa-eye"},

            "sep1": "---------",
            "quit": {name: "Close", icon: "fas fa-times" }
        }
    });   
});


function redirect_add(){
	window.location = '/atm_swipe/create'+'?href='+'{{urlencode(url()->full())}}';
}


</script>

@endpush
