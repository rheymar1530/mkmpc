@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
	#tbl_soa_list  tr>td{
		padding:2px ;
		padding-right:10px !important;
		vertical-align:top;
		font-family: Arial !important;
		font-size: 12px !important;
	}  
	.col_search{
		padding: 1px 1px 1px 1px !important;
	}
	.badge_status {
   		font-size: 12px;
	}
table.dataTable,
table.dataTable th,
table.dataTable td {
  -webkit-box-sizing: content-box;
  -moz-box-sizing: content-box;
  box-sizing: content-box;
}
.td_limit_2{
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;  
  overflow: hidden;
  font-size: 12px;
}
</style>

<div class="container-fluid">
	<div class="row">
		<div class="col-sm-12">
			<a class="btn btn-sm bg-gradient-primary" data-toggle="modal" data-target="#modal-filtering"><i class="fas fa-cog"></i>&nbsp;Filter Option</a>
			<a  class="btn btn-sm bg-gradient-success" href="/admin/generate_soa?href={{urlencode(str_replace('http',config('global.http'),url()->full()))}}"><i class="far fa-plus-square"></i>&nbsp;Generate SOA</a>
		</div>
		<div class="col-12" style="margin-top: 10px">
			<div class="card card-info">
				<!-- <div class="card-header">
					<h3 class="card-title">SOA List</h3>
				</div> -->
				<div class="card-body">
					<h3>SOA Control List</h3>
					<table id="tbl_soa_list" class="table table-hover" style="width: 100% !important; table-layout: fixed;word-wrap:break-word;">
						<thead>
							<tr class="table_header_dblue">
								<th>&nbsp;Control #</th>
								<th width="10%">Account No</th>
								<th>Account Name</th>
								<th>Cost Centers</th>
								<th>Billing Period</th>
								<th>Statement Date</th>
								<th>Due Date</th>
								<th>Attachment</th>
								<th>Status</th>
								

							</tr>
						</thead>
						<tbody id="list_body">
							@foreach($soa_list as $list)
								<?php
									if($list->status_code == 0){
										$badge_class="warning";
									}elseif($list->status_code == 1){
										$badge_class = "info";
									}elseif($list->status_code == 2){
										$badge_class = "success";
									}elseif($list->status_code == 10){
										$badge_class = "danger";
									}else{
										$badge_class = "";
									}

									if($list->attachment == "None"){
										$att_class = "danger";
									}elseif($list->attachment == "Processing"){
										$att_class = "info";
									}else{
										$att_class = "success";
									}
								?>
								<tr class="table_row" data-token="{{$list->access_token}}" data-control="{{ $list->control_number }}">
									<td>
									<a href="/admin/view_soa?control_number={{ $list->control_number }}&href={{urlencode(str_replace('http',config('global.http'),url()->full()))}}" class="badge badge-light " style="font-size:12px">{{ $list->control_number }}</a></td>
									<td>{{$list->account_no}}</td>
									<td><span class="td_limit_2">{{$list->account_name}}</span></td>
									<td><span class="td_limit_2">{{$list->cost_centers}}</span></td>
									<td>{{$list->billing_period}}</td>
									<td>{{$list->statement_date}}</td>
									<td>{{$list->due_date }}</td>	
									<td class="col-att">&nbsp;
										<span class="badge badge-{{$att_class}} badge_status">{{$list->attachment}}</span>
									</td>
									<td>&nbsp;<span class="badge badge-{{$badge_class}} badge_status">{{ $list->status }}</span></td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
@include('soa.soa_filter_modal')
@include('soa.attachment_option')
@endsection

@push('scripts')
	<script type="text/javascript">

	var dt_table;
	var typingTimer;                
	var doneTypingInterval = 400;
	$(function() {
        $.contextMenu({
            selector: '.table_row',
            callback: function(key, options) {
            	// var soa_status = ['status_generated','status_sent','status_viewed','status_cancelled'];
                var m = "clicked: " + key;
                var access_token = $(this).attr('data-token');
                var control_number = $(this).attr('data-control');
               
                // var account_no = $(this).find('.badge-dark').text();
                // console.log({account_no});
                if(key == 'view'){
                	window.location = '/admin/view_soa?control_number='+control_number+'&href='+'{{urlencode(str_replace('http',config('global.http'),url()->full()))}}';
                	// window.open('/admin/generate_soa?id_account='+$(this).attr('data-token'),"_blank");
                }else if(key == 'export'){
                	window.open('/admin/export_soa?control_number='+control_number,'_blank');
                }else if(key == 'attachment'){
                	validate_view_attachment(access_token);
                	// view_attachment(access_token);
					// $('#iframe_prev_data').attr('src','/admin/view_soa_client/'+account_no+'/'+key);
                }
            },
	        items: {
	        	"view": {name: "View SOA", icon: "fas fa-eye"},
	            "export": {name: "Export SOA", icon: "fas fa-download"},
	            "attachment": {name: "View SOA Attachment", icon: "far fa-file-image"},
	            "sep1": "---------",
	            "quit": {name: "Quit", icon: function($element, key, item){ return 'context-menu-icon context-menu-icon-quit'; }}
	        }
        });   
    });
    function view_attachment(access_token){
		$('#txt_token__').val(access_token);
		$('#modal-attachment-option').modal('show');
		// window.location= '/soa_attachment?access_token='+access_token;
	}
	$(document).ready(function(){
	    $('#tbl_soa_list thead tr').clone(true).appendTo( '#tbl_soa_list thead' );
		$('#tbl_soa_list thead tr:eq(1)').addClass("head_rem")
	    $('#tbl_soa_list thead tr:eq(1) th').each( function (i) {
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

		$('#tbl_soa_list .head_rem').remove();
 	});
 	function init_table(){
 		var table = $("#tbl_soa_list").DataTable({
 		  "columnDefs": [
 		  	{ "width": "5%", "targets": 0 },
		  	{ "width": "6%", "targets": 1 },
		  	{ "width": "15%", "targets": 2 },
		  	{ "width": "6%", "targets": 7 },
		  	{ "width": "5%", "targets": 8 }
		  ],
	      "lengthChange": true, 
	      "autoWidth": false,
	      "pageLength": 20,
	       scrollCollapse: true,
	       scrollY: '70vh',
	       orderCellsTop: true,
		   scrollX : true,

	       "lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]],
	       "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
	       "initComplete": function( settings, json ) {
                            $($.fn.dataTable.tables(true)).DataTable().columns.adjust();  
                    }
	    })
	    return table;
 	}
 	function get_att_indicator($attachment){
 		var $att_class;
		if($attachment == "None"){
			$att_class = "danger";
		}else if($attachment == "Processing"){
			$att_class = "info";
		}else{
			$att_class = "success";
		}

		return $att_class;
 	}
 	function validate_view_attachment(token){
 		$.ajax({
 			type        :     'GET',
 			url         :     '/billing/check_view/attachment',
 			data        :     {'token' : token},
 			beforeSend  :     function(){
 								show_loader();
 			},
 			success     :     function(response){
 								console.log({response});
 								execute_attachment_response(response);
 								hide_loader();
 			},error: function(xhr, status, error){
				hide_loader();
				var errorMessage = xhr.status + ': ' + xhr.statusText;
				Swal.fire({
					position: 'center',
					icon: 'warning',
					title: "Error-"+errorMessage,
					showConfirmButton: false,
					showCancelButton: true,
					cancelButtonText : "Close"
				})
			} 
 		})
 	}
 	function execute_attachment_response(response){
 		if(response.RESPONSE_CODE == "INVALID"){
 				Swal.fire({
					position: 'center',
					icon: response.TYPE,
					title: response.ERROR_MESSAGE,
					showConfirmButton: false,
					showCancelButton: true,
					cancelButtonText : "Close"
				})
 		}else{
 			if(response.COMMAND == "VIEW_GENERATED"){
 				window.open('/billing/attachment/'+response.TOKEN)
 			}else if(response.COMMAND == "PREVIEW"){
 				window.open('/admin/soa/soa_attachment/'+response.TOKEN+'?load=1','_blank');
 			}
 		}
 		// if(RESPONSE_CODE == "PREVIEW_SOA"){

 		// }else if()
 	}




 	    // 		if($details->status == 0){
    		// 	$data['RESPONSE_CODE'] = "PREVIEW_SOA";
    		// }elseif($details->status == 10){
    		// 	$data['RESPONSE_CODE'] = "CANCELLED_SOA";
    		// }else{
    		// 	if($details->attachment_status == 2){
    		// 		$data['RESPONSE_CODE'] = "SHOW_ATTACHMENT";
    		// 	}elseif($details->attachment_status == 1){
    		// 		$data['RESPONSE_CODE'] = "PROCESSING";
    		// 	}else{
    		// 		$data['RESPONSE_CODE'] = "NO_ATTACHMENT";
    		// 	}
    		// }
	</script>
@endpush
