<?php $__env->startSection('content'); ?>
<style type="text/css">
	.main_form{
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
	}
	#tbl-change  tr>td{
		padding:3px ;
		vertical-align:top;
		font-family: Arial !important;
		font-size: 12px !important;
	}
	#tbl-change  tr::nth-child(0)>th{
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
<?php
	function status_class($status){
		switch ($status) {
			case 0:
				return 'success';
				break;
			case 1:
				return 'success';
				break;
			case 10:
				return 'danger';
				break;
			default:
				// code...
				break;
		}
	}
?>
<div class="container-fluid main_form" style="margin-top:-20px">
	<h3>Change Payables&nbsp;&nbsp;	</h3>
	<div class="row">
		<div class="col-md-12">
			<table id="tbl-change" class="table-hover table-bordered table-striped" style="margin-top: 10px;">
				<thead>
					<tr class="table_header_dblue">
						<th>Change ID</th>
						<th>Date</th>
						<th>Change for</th>
						<th>Loan Payment ID</th>
						<th>Amount</th>
				
						<th>Status</th>
						<th>Date Created</th>
						
					</tr>
				</thead>
				<tbody id="list_body">
					<?php $__currentLoopData = $changes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<tr class="change-row" data-code="<?php echo e($list->id_change_payable); ?>">
						<td class="text-center"><?php echo e($list->id_change_payable); ?></td>
						<td><?php echo e($list->date); ?></td>
						<td><?php echo $list->change_for; ?></td>
						<td><?php echo e($list->id_repayment); ?></td>
						<td class="text-right"><?php echo e(number_format($list->total_amount,2)); ?></td>
						

						<td>
							
							<span class="badge bg-gradient-<?php echo e($list->status_badge); ?> text-xs">
							<?php echo e($list->status_description); ?>

							</span>
						</td>
						<td><?php echo e($list->date_created); ?></td>
					</tr>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<?php echo $__env->make('global.print_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
<script type="text/javascript">
	var dt_table;
	var typingTimer;                
	var doneTypingInterval = 400;
	var id_chart_account_holder;
var opcode = 0; //Add

$(document).ready(function(){
	//Initialize Datatable
	$('#tbl-change thead tr').clone(true).appendTo( '#tbl-change thead' );
	$('#tbl-change thead tr:eq(1)').addClass("head_rem")
	$('#tbl-change thead tr:eq(1) th').each( function (i) {
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
	$('#tbl-change .head_rem').remove();
	$('.dt-buttons').removeClass('btn-group')
	$('.dt-buttons').find('button').removeClass('btn-secondary');
});

function init_table(){
	var config = {
		order: [],
		"lengthChange": true, 
		"autoWidth": false,
		scrollY: '70vh',
		// scrollCollapse: true,
		// scrollX : true,
		orderCellsTop: true,
		"bPaginate": false,
		dom: 'Bfrtip',
		buttons: ['excel']

	}
	
	var table = $("#tbl-change").DataTable(config)
	console.log({table});
	return table;
}

$(function() {
	$.contextMenu({
		selector: '.change-row',
		callback: function(key, options) {
			var m = "clicked: " + key;
			var id_change = $(this).attr('data-code');
			console.log({id_change})
			if(key == "view"){
				window.location = '/change-payable/view/'+id_change +'?href='+'<?php echo e(urlencode(url()->full())); ?>';
			}else if(key == "edit"){
				window.location = '/change-payable/edit/'+id_change +'?href='+'<?php echo e(urlencode(url()->full())); ?>';
			}else if(key == "print"){
				print_page(`/repayment-check/print/${id_change}`);
			}
		},
		items: {
			"view": {name: "View", icon: "fas fa-eye"},
			"sep1": "---------",
			"quit": {name: "Close", icon: "fas fa-times" }
		}
	});   
});
function redirect_add(){
	window.location = '/change-payable/create'+'?href='+'<?php echo e(urlencode(url()->full())); ?>';
}


</script>
<!-- if($credential->is_create) -->
<script type="text/javascript"> 
	$(document).ready(function(){
		$('#tbl-change_filter').append('<a class="btn bg-gradient-info" onclick="redirect_add()" style="float:left"><i class="fa fa-plus"></i>&nbsp;Create Change</a>')
	})
</script>


<!-- endif -->
<script type="text/javascript">

	function show_filter(){
		$('#view_options').modal('show')
	}
	function show_repayment_summary(){
		$('#summary_date_modal').modal('show');
	}
</script>


<?php $__env->stopPush(); ?>

<?php echo $__env->make('adminLTE.admin_template', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/change-payable/index.blade.php ENDPATH**/ ?>