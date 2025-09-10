
<?php $__env->startSection('content'); ?>
<style type="text/css">
	.main_form{
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
	}
	#tbl_statement  tr>td{
		padding:3px ;
		vertical-align:top;
		font-family: Arial !important;
		font-size: 12px !important;
	}
	#tbl_statement  tr::nth-child(0)>th{
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
	<h3>Loan Payment Statement&nbsp;&nbsp;	</h3>
	<div class="row">
		<div class="col-md-12">
			<table id="tbl_statement" class="table table-hover table-striped" style="white-space: nowrap;margin-top: 10px;">
				<thead>
					<tr class="table_header_dblue">
						<th>ID</th>
						<th>Statement No.</th>
						<th>Type</th>
						<th>Barangay/LGU</th>
						<th>Statement Date</th>
						<th>Month Due</th>
						<th>Statement Amount</th>
						<th>Amount Paid</th>
						<th>Status</th>
						<th>Date Created</th>
					</tr>
				</thead>
				<tbody id="list_body">
					<?php $__currentLoopData = $statements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<tr class="statement-row" data-code="<?php echo e($list->id_repayment_statement); ?>">
						<td class="text-center"><?php echo e($list->id_repayment_statement); ?></td>
						<td class="text-center"><?php echo e($list->month_year); ?>-<?php echo e($list->id_repayment_statement); ?></td>
						<td><?php echo e($list->group_); ?></td>
						<td><?php echo e($list->baranggay_lgu); ?></td>
						<td><?php echo e($list->statement_date); ?></td>
						<td><?php echo e($list->month_due); ?></td>

					
						
						<td class="text-right"><?php echo e(number_format($list->total_due,2)); ?></td>
						<td class="text-right"><?php echo e(number_format($list->amount_paid,2)); ?></td>
						<td>
							<?php if($list->status > 0): ?>
							<span class="badge bg-gradient-<?php echo e($list->status_class); ?>2 text-xs"><?php echo e($list->status_description); ?></span>
							<?php endif; ?>
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
	$('#tbl_statement thead tr').clone(true).appendTo( '#tbl_statement thead' );
	$('#tbl_statement thead tr:eq(1)').addClass("head_rem")
	$('#tbl_statement thead tr:eq(1) th').each( function (i) {
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
	$('#tbl_statement .head_rem').remove();
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
	
	var table = $("#tbl_statement").DataTable(config)
	console.log({table});
	return table;
}

$(function() {
	$.contextMenu({
		selector: '.statement-row',
		callback: function(key, options) {
			var m = "clicked: " + key;
			var id_repayment_statement = $(this).attr('data-code');
			console.log({id_repayment_statement})
			if(key == "view"){
				window.location = '/repayment-statement/view/'+id_repayment_statement +'?href='+'<?php echo e(urlencode(url()->full())); ?>';
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
	window.location = '/repayment-statement/create'+'?href='+'<?php echo e(urlencode(url()->full())); ?>';
}


</script>
<?php if($credential->is_create): ?>
<script type="text/javascript"> 
	$(document).ready(function(){
		$('#tbl_statement_filter').append('<a class="btn bg-gradient-info" onclick="redirect_add()" style="float:left"><i class="fa fa-plus"></i>&nbsp;Create Loan Payment Statement</a>')
	})
</script>


<?php endif; ?>
<script type="text/javascript">

	function show_filter(){
		$('#view_options').modal('show')
	}
	function show_repayment_summary(){
		$('#summary_date_modal').modal('show');
	}
</script>


<?php $__env->stopPush(); ?>

<?php echo $__env->make('adminLTE.admin_template', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/repayment-statement/index.blade.php ENDPATH**/ ?>