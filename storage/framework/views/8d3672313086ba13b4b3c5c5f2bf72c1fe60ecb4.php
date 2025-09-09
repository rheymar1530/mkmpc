<?php $__env->startSection('content'); ?>
<style type="text/css">
	.main_form{
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
	}
	#tbl_cash_receipt  tr>td{
		padding:3px ;
		vertical-align:top;
		font-family: Arial !important;
		font-size: 12px !important;
	}
	#tbl_cash_receipt  tr::nth-child(0)>th{
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
	.col_amount{
		text-align: right;
		padding-right: 10px !important;
	}
	.tbl_badge{
		font-size: 13px;
		margin-left: 10px !important;
	}

</style>
<div class="container-fluid main_form" style="margin-top:-20px">
	<h3>Cash Receipt&nbsp;&nbsp;
		<!-- <a class="btn bg-gradient-danger" onclick="show_filter()" style="margin-right: 5px"><i class="fa fa-eye"></i>&nbsp;Filter Option</a> <a class="btn bg-gradient-info" onclick="redirect_add()"><i class="fa fa-plus"></i>&nbsp;Create Cash Receipt</a> -->
	</h3>
	<div class="row">
		<div class="col-12 col-sm-6 col-md-4">
			<div class="info-box">
				<span class="info-box-icon bg-gradient-success elevation-1"><i class="fas fa-money-bill-wave"></i></span>
				<div class="info-box-content">
					<span class="info-box-text">Total Payments</span>
					<span class="info-box-number" id="spn_total_payment">
						
						
					</span>
				</div>
				<!-- /.info-box-content -->
			</div>
			<!-- /.info-box -->
		</div>
		<!-- /.col -->
		<div class="col-12 col-sm-6 col-md-4">
			<div class="info-box mb-3">
				<span class="info-box-icon bg-danger elevation-1"><i class="fa fa-times"></i></span>

				<div class="info-box-content">
					<span class="info-box-text">Cancelled Payments</span>
					<span class="info-box-number" id="spn_cancelled_payment">41,410</span>
				</div>
				<!-- /.info-box-content -->
			</div>
			<!-- /.info-box -->
		</div>
		<!-- /.col -->

		<!-- fix for small devices only -->
		<div class="clearfix hidden-md-up"></div>


		<!-- /.col -->
	</div>
	<div class="row">
		<div class="col-md-12">
			<!-- <button class="btn btn-sm bg-gradient-primary" onclick="open_pickup_modal()">Create Pickup Request</button> -->
			
			<table id="tbl_cash_receipt" class="table table-hover table-striped" style="white-space: nowrap;margin-top: 10px;">
				<thead>
					<tr class="table_header_dblue">
						<th>ID</th>
						<th>Date Received</th>
						<th>Received from</th>
						<th>Member Code</th>
						<th>OR No.</th>
						<th>Paymode</th>
						<th>Total Payment</th>
						<th>Status</th>
						<th>Date Created</th>
					</tr>
				</thead>
				<tbody id="list_body">
					<?php
						$cancelled_amount = 0;
						$total_amount = 0;
					?>
					<?php $__currentLoopData = $lists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<tr class="row_cash_receipt" data-key="<?php echo e($list->id_cash_receipt); ?>">
						<?php
							if($list->status == 10){
								$cancelled_amount += floatval(preg_replace('/[^\d\.\-]/', '', $list->total_payment));
							}else{
								$total_amount += floatval(preg_replace('/[^\d\.\-]/', '', $list->total_payment));
							}
						?>
						<td><?php echo e($list->id_cash_receipt); ?></td>
						<td><?php echo e($list->date_received); ?></td>
						<td><?php echo e($list->payee); ?></td>
						<td><?php echo e($list->member_code); ?></td>
						<td><?php echo e($list->or_no); ?></td>
						<td><?php echo e($list->paymode); ?></td>
						<td class="col_amount"><?php echo e($list->total_payment); ?></td>
						<td>
							<?php if($list->status): ?>
							<span class="badge badge-danger tbl_badge">Cancelled</span>
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
<?php
	$total_amount = number_format($total_amount,2);
	$cancelled_amount = number_format($cancelled_amount,2);
?>
<?php echo $__env->make('cash_receipt.or_print_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('cash_receipt.filter_option_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('cash_receipt.cancel_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>


<script type="text/javascript">
	var dt_table;
	var typingTimer;                
	var doneTypingInterval = 400;
	var id_chart_account_holder;
var opcode = 0; //Add
$(document).ready(function(){
		$('#spn_total_payment').text("₱"+'<?php echo e($total_amount); ?>');
		$('#spn_cancelled_payment').text("₱"+'<?php echo e($cancelled_amount); ?>')
		//Initialize Datatable
		$('#tbl_cash_receipt thead tr').clone(true).appendTo( '#tbl_cash_receipt thead' );
		$('#tbl_cash_receipt thead tr:eq(1)').addClass("head_rem")
		$('#tbl_cash_receipt thead tr:eq(1) th').each( function (i) {
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
		$('#tbl_cash_receipt .head_rem').remove();
		$('.dt-buttons').removeClass('btn-group')
		$('.dt-buttons').find('button').removeClass('btn-secondary');


		// $('#print_modal').on('hidden.bs.modal', function (e) {
		// 	$('#print_frame').attr('src', link);	
		// })
		$('#cancel_cr_modal').on('hidden.bs.modal', function (e) {
			$('#txt_cancel_reason').val('')
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
	var table = $("#tbl_cash_receipt").DataTable(config)
	console.log({table});
	return table;
}
function print_frame(route){
	$('#print_frame').attr('src',route);
}
$(function() {
	$.contextMenu({
		selector: '.row_cash_receipt',
		callback: function(key, options) {
			var m = "clicked: " + key;
			var id_cash_receipt= $(this).attr('data-key');
			if(key == "view"){
				window.location = '/cash_receipt/view/'+id_cash_receipt+'?href='+'<?php echo e(urlencode(url()->full())); ?>';
			}else if(key == "print"){
				print_frame("/cash_receipt/print?reference="+id_cash_receipt);
			}else if(key == "cancel"){
				show_cancel_modal(id_cash_receipt);
			}
		},
		items: {
			"view": {name: "View Cash Receipt", icon: "fas fa-eye"},
			"print" : {name: "Print Cash Receipt", icon: "fas fa-print"},
			"cancel" : {name: "Cancel Cash Receipt", icon: "fas fa-times"},
			"sep1": "---------",
			"quit": {name: "Close", icon: "fas fa-times" }
		}
	});   
});
function redirect_add(){
	window.location = '/cash_receipt/add'+'?href='+'<?php echo e(urlencode(url()->full())); ?>';
}
function show_filter(){
	$('#view_options').modal('show')
}
function number_format(number){
	var result =  number.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
	return result;
}
</script>
<script type="text/javascript">
	$(document).ready(function(){
		$('#tbl_cash_receipt_filter').prepend('<a class="btn bg-gradient-danger" onclick="show_filter()" style="float:left"><i class="fa fa-eye"></i>&nbsp;Filter Option</a>')
	})
</script>

<?php if($credential->is_create): ?>
<script type="text/javascript">
	$(document).ready(function(){
		$('#tbl_cash_receipt_filter').append('<a class="btn bg-gradient-info" onclick="redirect_add()" style="float:left;margin-left:10px"><i class="fa fa-plus"></i>&nbsp;Create Cash Receipt</a>')
	})
</script>
<?php endif; ?>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('adminLTE.admin_template', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/cash_receipt/index.blade.php ENDPATH**/ ?>