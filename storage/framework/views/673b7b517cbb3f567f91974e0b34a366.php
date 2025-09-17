<?php $__env->startSection('content'); ?>
<style type="text/css">
	.tbl_accounts tr>th{
		padding: 5px;
		padding-left: 5px;
		padding-right: 5px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 15px;
	}
	.tbl_accounts tr>td{
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
	.col_tot{
		font-weight: bold;
	}
	.row_total{
		background: #e6e6e6;
		/*color: black;*/
	}
	.acc_head{
		background: #b3ffff;
	}
	.fas {
		transition: .3s transform ease-in-out;
	}
	.collapsed .fas {
		transform: rotate(90deg);
		padding-right: 3px;
	}
	.row_total{
		background: #e6e6e6;
		/*color: black;*/
	}
	.table_header_dblue_cust{
		background: #203764 !important;
		color: white;
	}
</style>
<?php
$books_op = [
	1=>"Journal Voucher",
	2=>"Cash Disbursement Voucher",
	3=>"Cash Receipt Voucher"
];


$type = [
	2=>"Show Entry",
	1=>"Per Transaction",
	
];


$cash_bank = [
	1=>"Cash on Hand",
	2=>"Cash in Bank",
];

$sel_type = $selected_type;
?>

<div class="row section_body">
	<div class="col-md-12">
		<div class="card">
			<div class="card-body">
				<div class="row">
<!-- 					<div class="col-md-3">
						<div class="form-group" id="div_books" style="margin-top:8px">
							<div class="form-check">
								<input class="form-check-input" id="chk_cancel" type="checkbox" >
								<label class="form-check-label" for="chk_cancel">Show Cancel</label>
							</div>						
						</div>
					</div> -->
					<div class="col-md-12">
						<h4 style="margin-bottom: 10px;" class="head_lbl text-center"><?php echo e($description); ?></h4>
						<br>
<!-- 
						<div class="form-group row" style="margin-top:10px">
							<label class="col-md-1 control-label col-form-label col-md-cus" style="text-align: left">Books&nbsp;</label>
							<div class="col-md-9" >
								<?php $__currentLoopData = $books_op; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val=>$b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<div class="form-check form-check-inline">
									<input class="form-check-input chk_books" type="checkbox" id="chk_<?php echo e($val); ?>" value="<?php echo e($val); ?>" <?php echo((in_array($val,$books) || count($books) == 0)?"checked":""); ?>>
									<label class="form-check-label" for="chk_<?php echo e($val); ?>"><?php echo e($b); ?></label>
								</div>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</div>
						</div> -->

						<?php if($fil_type != "journal_entries"): ?>
						<div class="form-group row" >
							<label class="col-md-1 control-label col-form-label col-md-cus" style="text-align: left">Cash/Bank&nbsp;</label>
							<div class="col-md-3">
								<select class="form-control p-0" id="sel_cash_bank">
									<?php $__currentLoopData = $cash_bank; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val=>$tl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<option value="<?php echo e($val); ?>" <?php echo($val == $sel_cash_bank)?"selected":""; ?> ><?php echo e($tl); ?></option>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</select>
							</div>
						</div>
						<?php endif; ?>
						<div class="form-group row" >
							<label class="col-md-1 control-label col-form-label col-md-cus" style="text-align: left">Period&nbsp;</label>
							<div class="col-md-3">
								<input type="date" class="form-control in_payroll" id="sel_period_start" key="period_start" value="<?php echo e($start_date); ?>">
							</div>
							<div class="col-md-3">
								<input type="date" class="form-control in_payroll" id="sel_period_end" key="period_end" value="<?php echo e($end_date); ?>">
							</div>
							<button class="btn bg-gradient-success2 btn-sm" onclick="generate_transaction_summary(1)">Generate</button>
							<div class="btn-group ml-1">
								<button type="button" class="btn bg-gradient-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									Export
								</button>
								<div class="dropdown-menu">
									
									<a class="dropdown-item" onclick="generate_transaction_summary(2,1)">PDF</a>
									<a class="dropdown-item" onclick="generate_transaction_summary(2,2)">Excel</a>
								</div>
							</div>
							<!-- <button class="btn bg-gradient-danger2 btn-sm" onclick="generate_transaction_summary(2)" style="margin-left:10px">Export to PDF</button> -->
						</div>

						<div class="form-group row">
							<label class="col-md-1 control-label col-form-label col-md-cus" style="text-align: left">&nbsp;</label>
							<div class="col-md-3">
								<div class="form-check">
									<input class="form-check-input" id="chk_cancel" type="checkbox" <?php echo ($show_cancel ==1)?"checked":""; ?>>
									<label class="form-check-label" for="chk_cancel">Show Cancelled Transactions</label>
								</div>	
							</div>					
						</div>


						<?php if($selected_type == 1): ?>
						<?php echo $__env->make('journal_report.table', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
						<?php else: ?>
						<?php echo $__env->make('journal_report.table_entry', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
						<?php endif; ?>
						
					</div>
				</div>
			</div>
		</div>
		
	</div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script type="text/javascript">
	function generate_transaction_summary(type,export_type=1){
		// var books = [];
		// $('.chk_books:checked').each(function() {
		// 	books.push($(this).val());
		// });

		// if(books.length == 0){
		// 	Swal.fire({
		// 		title: "Please select atleast 1 Book",
		// 		text: '',
		// 		icon: 'warning',
		// 		showConfirmButton : false,
		// 		timer : 2500
		// 	}).then(function(){
		// 		$('#div_books').addClass('mandatory');
		// 		setTimeout(
		// 			function() 
		// 			{
		// 				$('#div_books').removeClass('mandatory');
		// 			}, 3000);
		// 	});	

		// 	return;
		// }


		var param = {
			'start_date' : $('#sel_period_start').val(),
			'end_date' : $('#sel_period_end').val(),
			'type' : $('#sel_type').val(),
			'show_cancel' : ($('#chk_cancel').prop("checked"))?1:0,
			'cash_bank' : $('#sel_cash_bank').val(),
			'export_type' : export_type
			
		};
		// 'show_cancel' : ($('#chk_cancel').prop("checked"))?1:0
		// var queryString = $.param(param)+'&books='+encodeURIComponent(JSON.stringify(books));
		var queryString = $.param(param);
		if(type == 1){
			window.location = '/journal/report/<?php echo e($fil_type); ?>?'+queryString;
		}else{
			window.open('/journal/report/<?php echo e($fil_type); ?>/export?'+queryString,'_blank');
		}
		
		console.log({queryString});
	}


</script>
<?php $__env->stopPush(); ?>



<?php echo $__env->make('adminLTE.admin_template', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/journal_report/index.blade.php ENDPATH**/ ?>