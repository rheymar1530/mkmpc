<?php $__env->startSection('content'); ?>
<style type="text/css">
	.tbl_cbu tr>th{
		padding: 5px;
		padding-left: 5px;
		padding-right: 5px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 15px;
	}
	.tbl_cbu tr>td{
		padding: 3px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 13px;
	}
	.class_amount{
		text-align: right;
		padding-right: 10px !important;
	}
	.form-row  label{
		margin-bottom: unset !important;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 15px;
		margin-top: 5px;
	}
</style>
<div class="container main_form section_body" style="margin-top: -20px;">
	<div class="card">
		<div class="card-body">
			<h2 class="head_lbl text-center">Capital Build-Up</h2>
			
			<div class="row mt-3">
				
				<!-- <form> -->
					<div class="col-md-12">
						<form>
							<div class="form-row align-items-end">
								<div class="form-group col-md-3">
									<label for="sel_loan_service">Date</label>

									<div class="col-md-12 p-0">
										<input type="date" class="form-control in_payroll" id="sel_period_start" value="<?php echo e($date); ?>" name="date">
									</div>
									
								</div>
								
								<div class="form-group col-md-1">
									<label>&nbsp;</label>
									<button class="btn btn-sm bg-gradient-primary2">Generate</button>
								</div>
								<div class="form-group col-md-2">
									<label>&nbsp;</label>
									<button type="button" class="btn btn-sm bg-gradient-success2 form-control" onclick="export_excel()">Export to Excel</button>
								</div>

							</div>
						</form>
					</div>
	
					<?php $total = 0;?>
					<div class="col-md-12">
						<h5 class="lbl_color">Total CBU:&nbsp;&nbsp;&nbsp;<span id="spn_cbu_total"></span></h5>
						<!-- <div class="table-responsive" style="max-height: calc(100vh - 200px);overflow-y: auto;margin-top: 5px !important;overflow-x: auto"> -->
							<div class="table-responsive" style="margin-top: 5px !important;overflow-x: auto">
							<table class="table table-bordered table-stripped table-head-fixed tbl_cbu" style="white-space: nowrap;" id="tbl_cbu">
								<thead>
									<tr>
										<th class="table_header_dblue">ID Member</th>
										<th class="table_header_dblue">Member</th>
										<th class="table_header_dblue">Amount</th>
									</tr>
								</thead>
								<tbody id="net_body">
									<?php $__currentLoopData = $cbu; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<tr>
										<td><?php echo e($c->id_member); ?></td>
										<td><?php echo e($c->member); ?></td>
										<td class="class_amount"><?php echo e(number_format($c->amount,2)); ?></td>
									</tr>
									<?php
										$total += $c->amount;
									?>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									
								</tbody>
								<tfoot>
									<tr>
										<th colspan="2">Total</th>
										<th class="class_amount">₱<?php echo e(number_format($total,2)); ?></th>
									</tr>
								</tfoot>
							</table>    
						</div>  
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php $__env->stopSection(); ?>
	<?php $__env->startPush('scripts'); ?>
	<script type="text/javascript">
		$(document).ready(function(){
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
			buttons: []
		}
			$('#spn_cbu_total').text('₱'+'<?php echo number_format($total,2); ?>');
			$('#tbl_cbu').dataTable(config)
		})
		function export_excel(){
			let queryParam={
				'date' : $('#sel_period_start').val()
			};
			window.open('/cbu/report-export-excel?'+$.param(queryParam),'_blank');
		}
	</script>
	<?php $__env->stopPush(); ?>
<?php echo $__env->make('adminLTE.admin_template', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/cbu/report.blade.php ENDPATH**/ ?>