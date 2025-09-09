@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
.main_form{
	font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
}
#tbl_investment  tr>td{
    padding:3px ;
    vertical-align:top;
    font-family: Arial !important;
    font-size: 12px !important;
}
#tbl_investment  tr::nth-child(0)>th{
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

</style>
<?php
	function status_class($status){
		$class = "";
		switch($status){
			case 0:
				$class="primary";
				break;
			case 1:
				$class ="success";
				break;
			default:
				$class="danger";
				break;
		}
		return $class;
	}
?>
<div class="container-fluid main_form" style="margin-top:-20px">
	<h3>Investment Withdrawals&nbsp;&nbsp; </h3>
	
	<div class="row">
       <div class="col-md-12">
            <table id="tbl_investment" class="table table-hover table-striped" style="white-space: nowrap;margin-top: 10px;">
                <thead>
                    <tr class="table_header_dblue">
                    	<th>ID</th>
                        <th>Investor</th>
                        <th>Total Amount</th>
                        <th>Date released</th>
                        <th>Status</th>
                        <th>Note</th>
                        <th>Date Created</th>
                    </tr>
                </thead>
                <tbody id="list_body">
                	@foreach($investment_withdrawal as $inv)
                	<tr class="row_investment" data-id="{{$inv->id_investment_withdrawal_batch}}">
                		<td>{{$inv->id_investment_withdrawal_batch}}</td>
                		<td>
                			@if(isset($inv->investor))
                				{{$inv->investor}}
                			@else
                				<i>[Batch Withdrawal]</i>
                			@endif
                		</td>
                		<td class="text-right pr-2">{{number_format($inv->total_amount,2)}}</td>
                		<td>{{$inv->date_released}}</td>
                		<td><span class="badge text-xs badge-{{status_class($inv->status)}}">{{$inv->status_description}}</span></td>
                		<td>
                			@if($inv->note != "")
                			<i>{{$inv->note}}</i>
                			@endif

                		</td>
                		<td>{{$inv->date_created}}</td>
                	</tr>
                	@endforeach
                </tbody>
            </table>
        </div>
	</div>
</div>

@include('investment-withdrawal.status_modal')

@if(MySession::isAdmin())
@include('global.print_modal')
@endif
@endsection

@push('scripts')
<script type="text/javascript">
const STATUS_UPDATE_MODE = 1;
var dt_table;
var typingTimer;                
var doneTypingInterval = 400;
var id_chart_account_holder;
var opcode = 0; //Add
$(document).ready(function(){
	//Initialize Datatable
	$('#tbl_investment thead tr').clone(true).appendTo( '#tbl_investment thead' );
	$('#tbl_investment thead tr:eq(1)').addClass("head_rem")
	$('#tbl_investment thead tr:eq(1) th').each( function (i) {
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
	$('#tbl_investment .head_rem').remove();
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
	var table = $("#tbl_investment").DataTable(config)
	console.log({table});
	return table;
}

$(function() {
    $.contextMenu({
        selector: '.row_investment',
        callback: function(key, options) {
            var m = "clicked: " + key;
			var id_investment = $(this).attr('data-id');
			if(key == "view"){
				window.location = '/investment-withdrawal/view/'+id_investment +'?href='+'{{urlencode(url()->full())}}';
			}else if(key == "confirm"){
				show_status_modal(id_investment);
			}
        },
        items: {
        	"view": {name: "View Investment Withdrawal", icon: "fas fa-eye"},
        	"confirm" : {name: "Update Status", icon: "fas fa-check"},
            "sep1": "---------",
            "quit": {name: "Close", icon: "fas fa-times" }
        }
    });   
});

function redirect_add(){
	window.location = '/investment-withdrawal/create'+'?href='+'{{urlencode(url()->full())}}';
}


</script>



<!-- if($credential->is_create) -->
<script type="text/javascript">
	$(document).ready(function(){
		$('#tbl_investment_filter').append('<a class="btn bg-gradient-info" onclick="redirect_add()" style="float:left;"><i class="fa fa-plus"></i>&nbsp;Create Investment Withdrawal</a>')
	})
</script>
<!-- endif -->
<!-- if($credential->is_read) -->
<script type="text/javascript">
	$(document).ready(function(){
		$('#tbl_investment_filter').append('<a class="btn bg-gradient-primary" onclick="show_filter()" style="float:left;margin-left:10px"><i class="fa fa-eye"></i>&nbsp;Filtering Option</a>')
	})
	function show_filter(){
		$('#view_options').modal('show');
	}
</script>
<!-- endif -->



@if(MySession::isAdmin())
<script type="text/javascript">
	$(document).ready(function(){
		if(localStorage.getItem("cdv-print-trigger-index") == 1){
			print_page(`/cash_disbursement/print/${localStorage.getItem('id_cash_disbursement')}`);
	        localStorage.removeItem('cdv-print-trigger-index');
            localStorage.removeItem('id_cash_disbursement');
		}
	})
</script>
@endif
@endpush
