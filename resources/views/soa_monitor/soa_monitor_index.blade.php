@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
	#tbl_transactions  tr>td{
		padding:3px ;
		vertical-align:top;
		font-family: Arial !important;
		font-size: 11px !important;
	}  
	#tbl_transactions  tr::nth-child(0)>th{
		padding:34px !important;
		vertical-align:top;
		font-family: Arial !important;
		font-size: 12px !important;
	}  
	.col_txt_amount{
		text-align: right;
		padding-right: 10px !important;
	}
	.col_search{
		padding: 1px 1px 1px 1px !important;
	}
	.dt-buttons{
		margin-bottom: -50px;
	}
	.dataTables_scrollHead table.dataTable th, table.dataTable tbody td {
		padding: 9px 10px 1px;
	}
	.head_search{
		height: 24px;
	}
	#tbl_transactions thead tr:eq(1) th{
		background: white !important;
	}
	.btn_export_excel{
		background-color: green;
	}
	.dt-buttons{
		margin-bottom: -30px;
	}
	.crd-body{
		padding: 0.5em;
	}
	.crd-body input , .crd-body select{
		font-size: 13px;
		padding: 0;
		height: 27px !important;
	}
	@media (max-width: 800px) {
		.flex-wrap,#tbl_transactions_filter{
			text-align: left !important;
		}
		.lbl_to{
			display: none;
		}
	}
	.first{
		padding-left: 15px !important;
	}
	.container-fluid{
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
	}
	.lbl_filter_frm{
		margin-bottom: 0px !important;
		font-size: 13px;
	}
	.badge{
		margin-left: 10px;
		font-size: 12px;
	}
	.small-box>.inner {
	     padding: 5px; 
	}
/*	.small-box>.small-box-footer {
		margin-top: -15px;
		padding: unset;
	}
	.small-box p, .small-box h3, .small-box a{
		font-family: 'Oswald', sans-serif;
	}*/
	.small-box p{
		margin-bottom: unset;
	}
	.small-box p, .small-box h3, .small-box a{
		font-family: 'Oswald', sans-serif;
	}
</style>
<div class="container-fluid">
	<div class="card" style="margin-top:-25px">
		<div class="card-body crd-body">
			<div class="col-sm-12">
				<h4 class="test_highlight">SOA Monitoring</h4>
				<form type="GET">
					<div class="form-row" style="margin-top:10px" id="filter_div">	
						<div class="form-group col-md-4">
							<label for="sel_branch" class="lbl_filter_frm">Branch</label>
							<select class="form-control" id="sel_branch" name="sel_branch">
								@foreach($branches as $branch)
									<?php $selected = ($branch->id_branch == $sel_branch)?" selected":""; ?>
									<option value="{{$branch->id_branch}}" {{$selected}}>{{$branch->branch_name}}</option>
								@endforeach
							</select>
						</div>
						<div class="form-group col-md-2">
							<label for="txt_telephone" class="lbl_filter_frm">Date</label>
							<input type="date" class="form-control" id="txt_date" value="{{$txt_date}}" name="txt_date"/>
						</div>
						<div class="form-group col-md-2">
							<label for="txt_telephone" class="lbl_filter_frm">&nbsp;</label>
							<input type="date" class="form-control" id="txt_date_to" value="{{$txt_date_to}}" name="txt_date_to"/>
						</div>
						<div class="form-group col-md-1">
							<label class="lbl_filter_frm lbl_to">&nbsp;</label>
							<input type="submit" class="btn btn-sm bg-gradient-primary form-control" value="Search" />
						</div>
					</div>
				</form>
			</div>
			<div class="row">
				<!-- ./col -->
				<div class="col-lg-3">
					<!-- small card -->
					<div class="small-box bg-success">
						<div class="inner">
							<h3><span id="spn_complete"></span> <sup style="font-size: 15px">Client(s)</sup></h3>
							<p>Completed</p>
						</div>
						<div class="icon">
							<i class="fas fa-clipboard-check" style="font-size:60px"></i>
						</div>
						<a onclick="filter(this,'row_complete')" class="small-box-footer btn_a"><span class="spn_click">Click to filter</span> <i class="fas fa-arrow-circle-right"></i></a>	
					</div>
				</div>
				<!-- ./col -->
				<div class="col-lg-3">
					<!-- small card -->
					<div class="small-box bg-danger">
						<div class="inner">
							<h3><span id="spn_incomplete"></span> <sup style="font-size: 15px;font-style: unset !important;">Client(s)</sup></h3>
							<p>Incomplete <span style="font-size:14px;" id="span_no_tran_count"></span></p>
						</div>
						<div class="icon">
							<i class="fa fa-times"></i>
						</div>
						<a onclick="filter(this,'row_incomplete')" class="small-box-footer btn_a"><span class="spn_click">Click to filter</span> <i class="fas fa-arrow-circle-right"></i></a>
					</div>
				</div>
				<!-- ./col -->
				<div class="col-lg-3" style="max-height:120px;min-height: 120px;">
					<!-- small card -->
					<div class="small-box bg-warning">
						<div class="inner">
							<h3><span id="spn_no_trans"></span> <sup style="font-size: 15px">Client(s)</sup></h3>
							<p>No Transaction</p>
						</div>
						<div class="icon">
							<i class="fa fa-list-alt" style="font-size:60px"></i>
						</div>
						<a onclick="filter(this,'row_no_transaction')" class="small-box-footer btn_a"><span class="spn_click">Click to filter</span> <i class="fas fa-arrow-circle-right"></i></a>
					</div>
				</div>
<!-- 				<div class="col-lg-12">
					<button class=" btn btn-sm bg-gradient-danger"><i class="fa fa-times"></i>&nbsp;Remove Filter</button>
				</div> -->
				<!-- ./col -->
        	</div>
			<div class="col-12" style="margin-top: 10px;">
				<table id="tbl_transactions" class="table table-hover table-striped" style="white-space: nowrap;">
					<thead>
						<tr class="table_header_dblue">
							<th>Acct #</th>
							<th>Account Name</th>
							<th>Balance</th>
							<th style="font-size:11px">Transactions W/O SOA</th>		
							<!-- <th>W/O SOA Amt</th> -->
							<th>SOA Status</th>
							<th>Remarks</th>
							<th>Progress</th>
						</tr>
					</thead>
					<tbody id="list_body">
						<?php
							$count = array();
							$count['complete']=0;
							$count['incomplete']=0;
							$count['no_trans']=0;
							$count['incomplete_transaction'] = 0;
						?>
						@foreach($client_soa as $row)
							<?php
								if($row->status == "No Transaction"){
									$count['no_trans']++;
									$spn_class="warning";
									$class="row_no_transaction";
								}else if($row->status == "Incomplete"){
									$count['incomplete']++;
									$count['incomplete_transaction'] += $row->no_soa_count;
									$spn_class="danger";
									$class="row_incomplete";
								}else{
									$count['complete']++;
									$spn_class="success";
									$class="row_complete";
								}
								if($row->percentage != null){
									$percentage_complete = $row->percentage;
									$percentage_incomplete = 100-$row->percentage;
									$percentage_incomplete_dis = ($percentage_complete==0)?"0":$percentage_incomplete;
								}
							?>
							<tr class="{{$class}}">
								<?php 
								$create_href = ($is_soa_create->is_view)?"href='/admin/generate_soa?id_account=".$row->id_client_profile."' target='_blank'":"";
								
								?>
								<td><a <?php echo $create_href;?>
									class="badge badge-dark" title="Click to generate SOA">{{$row->account_no}}</a></td>
								<td>{{$row->account_name}}</td>
								<td class="col_txt_amount">{{number_format($row->balance,2)}}</td>
								<td class="col_txt_amount">{{number_format($row->no_soa_count,0)}}</td>
								<!-- <td class="col_txt_amount">{{number_format($row->wo_soa_amount,2)}}</td> -->
								<td><span class="badge badge-{{$spn_class}}">{{$row->status}}</span></td>
								<td>@if($row->remarks != "")<span class="badge badge-secondary">{{$row->remarks}}</span> @endif</td>
								<td class="project_progress">
									@if($row->percentage != null)
									<div class="progress">
										<div class="progress-bar progress-bar-striped bg-success" role="progressbar" style="width:<?php echo $percentage_complete ?>%" aria-valuenow="{{$percentage_complete}}" aria-valuemin="0" aria-valuemax="{{$percentage_complete}}">{{$percentage_complete}}%
										</div>
										<div class="progress-bar progress-bar-striped bg-danger" role="progressbar" style="width:<?php echo $percentage_incomplete ?>%" aria-valuenow="{{$percentage_incomplete}}" 
											aria-valuemin="{{$percentage_incomplete}}" aria-valuemax="100">{{$percentage_incomplete_dis}}%
										</div>
									</div>
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
@endsection
@push('scripts')
<script type="text/javascript">
	var dt_table;
	var typingTimer;                
	var doneTypingInterval = 400;
	var GroupColumn = -1;
	var columnDefs = (GroupColumn >= 0)?{ "visible": false, "targets": GroupColumn }:{};
	
	$(document).ready(function(){
		$('#tbl_transactions thead tr').clone(true).appendTo( '#tbl_transactions thead' );
		$('#tbl_transactions thead tr:eq(1)').addClass("head_rem")
		$('#tbl_transactions thead tr:eq(1) th').each( function (i) {
			var title = $(this).text();
			$(this).addClass('col_search');
			$(this).html( '<input type="text" placeholder="Search '+title+'" style="width:100%;height: 22px !important" class="txt_head_search head_search"/> ' );
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
		$('#tbl_transactions .head_rem').remove();
		$('#spn_complete').text('<?php echo $count['complete']; ?>');
		$('#spn_incomplete').text('<?php echo $count['incomplete']; ?>');
		$('#spn_no_trans').text('<?php echo $count['no_trans']; ?>');
		$('#span_no_tran_count').text('( '+'<?php echo number_format($count['incomplete_transaction'],0); ?>'+' Transaction(s) )')

		$('.dt-buttons').removeClass('btn-group');
			setTimeout(
			function() {
				$($.fn.dataTable.tables(true)).DataTable().columns.adjust();
				$('.dataTables_scrollBody .head_rem').remove();
			},300)

		});
		$(window).resize(function() { 
			setTimeout(
			function() {
				$($.fn.dataTable.tables(true)).DataTable().columns.adjust();
				$('.dataTables_scrollBody .head_rem').remove();
			},300)
		});
	function init_table(){
		var table = $("#tbl_transactions").DataTable({
			"columnDefs": [
			columnDefs
			],
			"order": [[ 1, "asc" ]],
			"lengthChange": true, 
			"autoWidth": false,
			scrollCollapse: true,
			scrollY: '70vh',
			scrollX : true,
			orderCellsTop: true,
			"bPaginate": false,
			dom: 'Bfrtip',

			buttons: [
			{
				text: 'Export to Excel',
				className: 'btn btn-sm btn-success',
				action: function ( e, dt, node, config ) {
					alert(123);
				}
			},
			{
				text: 'Export to PDF',
				className: 'btn btn-sm btn-danger',
				action: function ( e, dt, node, config ) {

					window.location = '/shipment_history/export_pdf?shipment_dt_from='+$('#txt_date_from').val()+'&shipment_dt_to='+$('#txt_date_to').val();
				}
			},
			// {
			// 	text: 'Customize Fields',
			// 	className: 'btn btn-sm btn-info',
			// 	action: function ( e, dt, node, config ) {
			// 		var current_path = $(location).attr('href');
			// 		window.location = '/custom_report/shipment_history?return_url='+encodeURIComponent(current_path);;
			// 	}
			// }
			],
			"drawCallback": function ( settings ) {
				if(GroupColumn >= 0)
				{

					var api = this.api();
					var rows = api.rows( {page:'current'} ).nodes();
					var last=null;

					api.column(GroupColumn, {page:'current'} ).data().each( function ( group, i ) {
						if ( last !== group ) {
							$(rows).eq( i ).before(
								'<tr class="group"><td colspan="6" style="font-weight:bold">'+group+'</td></tr>'
								);

							last = group;
						}
					} );
				}
			}
		})
		return table;
	}
	function filter(obj,val){
		$.fn.dataTable.ext.search.pop();
	    dt_table.draw();		
		if($(obj).hasClass('active-filter')){
			$(obj).removeClass('active-filter');
			$(obj).find('span.spn_click').text("Click to filter");
		}else{
			$('.btn_a').removeClass('active-filter');
			$('span.spn_click').text("Click to filter");
			$(obj).addClass('active-filter');
			$(obj).find('span.spn_click').text("Click to remove filter");
			console.log("ALL");
		    $.fn.dataTable.ext.search.push(
		        function(settings, data, dataIndex) {
		            return $(dt_table.row(dataIndex).node()).hasClass(val);
		        }
		    );
	        dt_table.draw();
		}
		$('.dataTables_scrollBody .head_rem').remove();
	}
	function highlight(word){
		var class_ = $('.test_highlight');
		var text = class_.text();
		text = text.replace( new RegExp('('+word+')', 'ig') ,'<span style="color:red">$1</span>' )
		class_.html(text);
	}
</script>
@endpush