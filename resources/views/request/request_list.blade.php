@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
	#tbl_request_list  tr>td{
		padding:3px !important;
		vertical-align:top;
		font-family: Arial !important;
		font-size: 14px !important;
	}  
	.col_search{
		padding: 1px 1px 1px 1px !important;
	}
</style>
<div class="container-fluid">
	<div class="row">
		<div class="col-sm-12">
			<button type="button" class="btn btn-primary" onclick="$('#modal-filtering').modal('show')">Filter Option</button>
		</div>
		<div class="col-12" style="margin-top: 10px">
			<div class="card card-info">
				<div class="card-header">
					<h3 class="card-title">Request List</h3>
				</div>
				<div class="card-body">

					<table id="tbl_request_list" class="table table-hover" style="white-space: nowrap;" width="100%">
						<thead>
							<tr class="table_header">
								<th>Request ID</th>
								<th>Date</th>
								<th>Requested by</th>
								<th>Type</th>
								<th>Remarks</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody id="list_body">
							@foreach($request_list as $request)
								<tr class="request_row">
									<td class="col_id_request"><a href="/admin/request/view?id_request={{$request->id_request}}&return_url={{urlencode(url()->full())}}">{{$request->id_request}}</a></td>
									<td>{{$request->date}}</td>
									<td>{{$request->requested_by}}</td>
									<td>{{$request->request_type}}</td>
									<td>{{$request->remarks}}</td>
									<td>{{$request->status}}</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

@include('request.filtering_modal')
@endsection

@push('scripts')
	<script type="text/javascript">

	// $(function () {

	var dt_table;
	var typingTimer;                
	var doneTypingInterval = 400;
	$(document).ready(function(){
    $('#tbl_request_list thead tr').clone(true).appendTo( '#tbl_request_list thead' );
    $('#tbl_request_list thead tr:eq(1) th').each( function (i) {
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
 	});
 	function init_table(){
 		var table = $("#tbl_request_list").DataTable({
	      "responsive": true, 
	      "lengthChange": true, 
	      "autoWidth": false,
	      "pageLength": 20,
	       scrollCollapse: true,
	       scrollY: '70vh',
	       orderCellsTop: true,

	       "lengthMenu": [[20, 50, 100, -1], [20, 50, 100, "All"]],
	       "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
	    })

	    return table;
 	}
	</script>
@endpush

<!-- 		$(document).on('dblclick','tr.request_row',function(){
			var id_request = $(this).find("td.col_id_request").text();
			window.location ='/admin/request/view?id_request='+id_request ;
		}) -->