@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
	.main_form{
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
	}
	#tbl_loan  tr>td{
		padding:3px ;
		vertical-align:top;
		font-family: Arial !important;
		font-size: 12px !important;
	}
	#tbl_loan  tr::nth-child(0)>th{
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
	.col_amount{
		text-align: right;
		padding-right: 10px !important;
	}
	.txt_danger{
		color : red;
	}
	tr.v-middle th{
		vertical-align: middle !important;
	}
</style>
<?php

$status_badge = [
	0=>"info",
	1=>"primary",
	2=>"success",
	3=>"success",
	4=>"danger",
	5=>"danger",
	6=>"primary",
];
?>
<div class="container-fluid main_form" style="margin-top:-20px">
	<h3>Loan&nbsp;&nbsp; 
		@if($credential->is_create)
		
		@endif
	</h3>
	<div class="row">
		<div class="col-md-12">
			<table id="tbl_loan" class="table table-hover table-striped" style="margin-top: 10px;">
				<thead>
					<tr class="table_header_dblue v-middle">
						<th>ID</th>
						<th>Application Date</th>
						<th>Date Released</th>
						<th>Name</th>
						<th>Loan Service</th>
						<th>Interest Rate</th>
						<th>Principal Amount</th>
						<th>Paid Principal</th>
						<th>Principal Balance</th>

						<th>Status</th>
					</tr>
				</thead>
				<tbody id="list_body">
					@foreach($loan_lists as $list)
					<tr class="loan_row" data-code="{{$list->loan_token}}">
						<td>{{$list->id_loan}}</td>
						<td>{{$list->application_date}}</td>
						<td>{{$list->date_released}}</td>
						<td>{{$list->member_name}}</td>
						<td>{{$list->loan_service_name}}</td>
						<td class="text-right">{{$list->interest_rate}}%</td>
						<td class="col_amount">{{number_format($list->principal_amount,2)}}</td>
						<td class="col_amount">{{number_format($list->paid_principal,2)}}</td>


						<td class="col_amount {{($list->principal_balance==$list->principal_amount)?'txt_danger':''}}">{{number_format($list->principal_balance,2)}}</td>

						<td><span class="badge badge-{{$status_badge[$list->status_code]}} tbl_badge">{{$list->loan_status}}</span></td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>
@include('loan.filter_option_modal')
@include('global.print_modal')
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
	$('#tbl_loan thead tr').clone(true).appendTo( '#tbl_loan thead' );
	$('#tbl_loan thead tr:eq(1)').addClass("head_rem")
	$('#tbl_loan thead tr:eq(1) th').each( function (i) {
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
	$('#tbl_loan .head_rem').remove();
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
	var table = $("#tbl_loan").DataTable(config)
	console.log({table});
	return table;
}

$(function() {
	$.contextMenu({
		selector: '.loan_row',
		callback: function(key, options) {
			var m = "clicked: " + key;
			var loan_token = $(this).attr('data-code');
			var viewing_route = '<?php echo $viewing_route;?>';

			console.log({loan_token});
			if(key == "view"){
				window.location = viewing_route+loan_token +'?href='+'{{urlencode(url()->full())}}';
			}

			// else if(key == "view_approval"){
			// 	window.location = '/loan/application/approval/'+loan_token +'?href='+'{{urlencode(url()->full())}}';
			// }

		},
		items: {
			"view": {name: "View Loan", icon: "fas fa-eye"},
        	// "view_approval": {name: "View Loan (Approval)", icon: "fas fa-eye"},
			"sep1": "---------",
			"quit": {name: "Close", icon: "fas fa-times" }
		}
	});   
});
function redirect_add(){
	window.location = '/loan/application/create'+'?href='+'{{urlencode(url()->full())}}';
}

</script>


@if($credential->is_create)
<script type="text/javascript">
	$(document).ready(function(){
		$('#tbl_loan_filter').append('<a class="btn bg-gradient-info" onclick="redirect_add()" style="float:left;"><i class="fa fa-plus"></i>&nbsp;Create Loan Application</a>')
	})
</script>
@endif
@if($credential->is_read)
<script type="text/javascript">
	$(document).ready(function(){
		// $('#tbl_loan_filter').append('<a class="btn bg-gradient-danger" onclick="show_active_loan_summary()" style="float:left;margin-left:10px"><i class="fa fa-print"></i>&nbsp;Generate Active Loan Summary</a>')
		

		$('#tbl_loan_filter').append(`<div class="btn-group" style="float:left;margin-left:10px">
			<button type="button" class="btn btn-danger  dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			Generate Active Loan Summary
			</button>
			<div class="dropdown-menu" style="">
			<a class="dropdown-item" href="javascript:void(0)" onclick="show_active_loan_summary(1)">Per Borrower</a>
			<a class="dropdown-item" href="javascript:void(0)" onclick="show_active_loan_summary(2)">Per Loan Service</a>




			</div>
			</div>`)

	})
</script>
@endif


<script type="text/javascript">
	$(document).ready(function(){
		$('#tbl_loan_filter').append('<a class="btn bg-gradient-primary" onclick="show_filter()" style="float:left;margin-left:10px"><i class="fa fa-eye"></i>&nbsp;Filtering Option</a>')
	})
	function show_filter(){
		$('#view_options').modal('show')
	}

	function show_active_loan_summary(type){
		$.ajax({
			type           :         'GET',
			url              :         '/loan/validate/active',
			data             :       {},
			beforeSend       :       function(){
				show_loader();
			},
			success         :        function(response){
				console.log({response});
				hide_loader();
				if(response.RESPONSE_CODE == "ERROR"){
					Swal.fire({
						title: response.message,
						text: '',
						icon: 'warning',
						showConfirmButton : false,
						timer  : 2500
					})
				}else if(response.RESPONSE_CODE == "SUCCESS"){
					print_page('/loan/active/'+type)
				}
			},error: function(xhr, status, error) {
				hide_loader()
				var errorMessage = xhr.status + ': ' + xhr.statusText;
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

</script>
@endpush




