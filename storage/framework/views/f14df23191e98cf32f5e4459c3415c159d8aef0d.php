<?php $__env->startSection('content'); ?>

<style type="text/css">
	#tbl_overdues th,#tbl_overdues td{
		padding: 3px;
		font-size: 0.8rem;
	}	
	.b-top{
		border-top: 0.2rem solid;
	}
	.b-bottom{
		border-bottom: 0.2rem solid;
	}
	.class_amount{
		text-align: right;
	}
.table.table-head-fixed thead tr:nth-child(1) th {
    top: 0 !important;
}
.table.table-head-fixed thead tr:nth-child(2) th {
    top: 30px !important; /* Adjust depending on the height of the first row */
}

.table.table-head-fixed thead tr th {

    border-bottom: 0 !important;;
    box-shadow: inset 0 1px 0 #dee2e6, inset 0 -1px 0 #dee2e6 !important;;
    position: -webkit-sticky !important;;
    position: sticky !important;
/*    top: 0;*/
    z-index: 10 !important;
}
</style>
<?php
	$month_list = [
		1=>"January",
		2=>"February",
		3=>"March",
		4=>"April",
		5=>"May",
		6=>"June",
		7=>"July",
		8=>"August",
		9=>"September",
		10=>"October",
		11=>"November",
		12=>"December"
	];
?>
<div class="container-fluid">
	<div class="card">
		<div class="card-body">
			<div class="text-center mb-5">
				<h5 class="head_lbl">Loan Payment Report</h5>
			</div>
			<form>
				<div class="row d-flex align-items-end">
					<div class="form-group col-md-3">
						<label class="lbl_color">Month</label>
						<select class="form-control form-control-border p-0" name="month" id="sel-month">
							<?php $__currentLoopData = $month_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<option value="<?php echo e($key); ?>" <?php echo($key==$selected_month)?'selected':''; ?> ><?php echo e($month); ?></option>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</select>
					</div>
					<div class="form-group col-md-2">
						<label class="lbl_color">Year</label>
						<select class="form-control form-control-border p-0" name="year" id="sel-year">
							<?php for($i=2024;$i<=$currentYear;$i++): ?>
							<option value="<?php echo e($i); ?>" <?php echo($i==$selected_year)?'selected':''; ?>><?php echo e($i); ?></option>
							<?php endfor; ?>
						</select>
					</div>
					<div class="form-group col-md-2">
						<button type="button" class="btn btn-sm bg-gradient-success2 w-100" onclick="Generate()">Generate</button>
					</div>
					<div class="form-group col-md-2">
						<div class="dropdown show">
							<button type="button" class="btn btn-sm bg-gradient-primary2 col-md-12 dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
							Export
							</button>
							<div class="dropdown-menu">
								<button type="button" class="dropdown-item" onclick="ExportData(1)">PDF</button>
								<button type="button" class="dropdown-item" onclick="ExportData(2)">Excel</button>
							</div>
						</div>
					</div>
					
				</div>
			</form>

			<div class="row">
				<?php echo $__env->make('repayment-report.table', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
			</div>
		</div>
	</div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script type="text/javascript">
	const ExportData = (type) =>{
		$t = (type==1)?'pdf':'excel';
		
		$query = {
			'month' : <?php echo e($selected_month); ?>,
			'year' : <?php echo e($selected_year); ?>

		};
		
		window.open(`/repayment-report/export/${$t}?`+$.param($query),'_blank');
	}
	
	const Generate = ()=>{
		$query = {
			'month' : $('#sel-month').val(),
			'year' : $('#sel-year').val()
		};
		window.location = `/repayment-report?`+$.param($query);

	}
	const view = (obj)=>{
		var token = $(obj).closest('tr').attr('tk');
		window.open(`/loan/application/approval/${token}`,'_blank');
	}
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('adminLTE.admin_template', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/repayment-report/index.blade.php ENDPATH**/ ?>