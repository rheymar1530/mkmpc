@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
	#tbl_template  tr>td{
		padding:3px !important;
		vertical-align:top;
		font-family: Arial !important;
		font-size: 14px !important;
	}  
	.col_search{
		padding: 1px 1px 1px 1px !important;
	}
	.card-label{
		padding: 8px;
	}
</style>

<div class="container-fluid">
	<div class="row">
		<div class="col-sm-12">
			<!-- <button type="button" class="btn btn-primary" onclick="$('#modal-filtering').modal('show')">Filter Option</button> -->
		</div>
		<div class="col-12">
			@if($credential->is_create)
			<a  class="btn btn-sm bg-gradient-success" href="/admin/soa_template/create" style="margin-bottom:10px"><i class="far fa-plus-square"></i>&nbsp;Create SOA Template</a>
			@endif
			<div class="card card-info card-outline">
				<div class="card-label">
					<h4>SOA Template List</h4>
				</div>
				<div class="card-body">
					<table id="tbl_template" class="table table-hover" style="white-space: nowrap;" width="100%">
						<thead>
							<tr class="table_header_dblue">
								<th>&nbsp;ID Report</th>
								<th>Name</th>
								<th>Description</th>
								<th>Remarks</th>
								<th>Orientation</th>
								<th>Date Created</th>
								<th></th>
							</tr>
						</thead>
						<tbody id="list_body">
							@foreach($template_list as $list)
								<tr>
									<td>{{ $list->id_report }}</td>
									<td>{{ $list->name }}</td>
									<td>{{ $list->description }}</td>
									<td>{{ $list->remarks }}</td>
									<td>{{ $list->orientation }}</td>
									<td>{{ $list->created_at }}</td>	
									<td>
										@if($credential->is_confirm)
											<a class="btn bg-gradient-primary btn-xs" href="/admin/soa_template/assign/{{$list->enc_id}}">&nbsp;Assign</a>
										@endif
										@if($credential->is_edit || $credential->is_read)
											<a class="btn bg-gradient-success btn-xs" href="/admin/soa_template/edit?id_report={{ $list->id_report }}">&nbsp;Edit/View</a>
										@endif
										@if($credential->is_delete)
											@if($default_template != $list->id_report)
												<a class="btn bg-gradient-danger btn-xs">&nbsp;Delete</a>
											@endif
										@endif
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
@push('scripts')
	<script type="text/javascript">
	var dt_table;
	var typingTimer;                
	var doneTypingInterval = 400;
	$(document).ready(function(){
    $('#tbl_template thead tr').clone(true).appendTo( '#tbl_template thead' );
    $('#tbl_template thead tr:eq(1) th').each( function (i) {
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
 		var table = $("#tbl_template").DataTable({
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
