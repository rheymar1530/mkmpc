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
</style>
<?php
$books = [
	1=>"Journal Voucher",
	2=>"Cash Disbursement Voucher",
	3=>"Cash Receipt Voucher"
];

$col = ($filter_type==2)?'9':'12';
?>
<div class="container-fluid section_body">
	<div class="row">
		<div class="col-md-12">
			<div class="card">
				<div class="card-body">
					<div class="row">

						<?php if($filter_type==2): ?>
						<div class="col-md-3">
							<div style="border-right:2px #bfbfbf solid;padding-right: 10px;">
								<!-- <h4>Books</h4> -->
								<div class="form-group" id="div_books" style="margin-top:8px">

								<div class="form-check">
									<input class="form-check-input" id="chk_cancel" type="checkbox" <?php echo ($show_cancel ==1)?"checked":""; ?>>
									<label class="form-check-label" for="chk_cancel">Show Cancel</label>
								</div>						
							</div>
							<input type="text" class="form-control" id="txt_search" placeholder="Search...">
							<div class="table-responsive" id="div_account" style="max-height: calc(100vh - 200px);overflow-y: auto;margin-top: 5px !important;overflow-x: hidden;">
								<table class="table table-bordered table-stripped table-head-fixed table-hover tbl_accounts">
									<thead>
										<tr>
											<th width="10%" style="text-align:center;"><input type="checkbox" id="chk_sel_all"></th>
											<th class="" style="text-align:center;">Account</th>
										</tr>
									</thead>
									<tbody id="account_body">
										<?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<tr class="row_account">
											<td style="text-align:center;"><input type="checkbox" class="chk_account" value="<?php echo e($acc->id_chart_account); ?>" <?php echo(in_array($acc->id_chart_account,$acc_selected)?"checked":""); ?>></td>
											<td class="col_acc"><?php echo e($acc->account_name); ?></td>
										</tr>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
									</tbody>
								</table>    
							</div> 
						</div>
					</div>
					<?php endif; ?>
					<div class="col-md-<?php echo e($col); ?>">
						<h4 class="text-center head_lbl mb-4"><?php echo e($title_head); ?></h4>
						<div class="form-group row" style="margin-top:10px">
							<label class="col-md-1 control-label col-form-label col-md-cus" style="text-align: left">Period&nbsp;</label>
							<?php if($filter_type == 2): ?>
							<div class="col-md-3">
								<input type="date" class="form-control in_payroll" id="sel_period_start" key="period_start" value="<?php echo e($start_date); ?>">
							</div>
							<?php endif; ?>
							<div class="col-md-3">
								<input type="date" class="form-control in_payroll" id="sel_period_end" key="period_end" value="<?php echo e($end_date); ?>">
							</div>

							<button class="btn bg-gradient-primary2" onclick="generate_gl(1)">Generate</button>
							<!-- <button class="btn bg-gradient-danger2 ml-2" onclick="generate_gl(2)">Export to PDF</button> -->
							<div class="btn-group ml-1">
								<button type="button" class="btn bg-gradient-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									Export
								</button>
								<div class="dropdown-menu">
									
									<a class="dropdown-item" onclick="generate_gl(2,1)">PDF</a>
									<a class="dropdown-item" onclick="generate_gl(2,2)">Excel</a>
								</div>
							</div>
<!-- 							<div class="btn-group">
								<button type="button" class="btn bg-gradient-success2 dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									Generate
								</button>
								<div class="dropdown-menu">
									
									<a class="dropdown-item" onclick="generate_gl(1,1)">Summary</a>
									<a class="dropdown-item" onclick="generate_gl(1,2)">Detailed</a>
								</div>
							</div>
 -->
<!-- 							<div class="btn-group ml-1">
								<button type="button" class="btn bg-gradient-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									Export
								</button>
								<div class="dropdown-menu">
									
									<a class="dropdown-item" onclick="generate_gl(2,1)">PDF</a>
									<a class="dropdown-item" onclick="generate_gl(2,2)">Excel</a>
								</div>
							</div> -->


							<!-- <button class="btn bg-gradient-success btn-sm" onclick="generate_gl(1)">Generate</button> -->
							<!-- <button class="btn bg-gradient-danger btn-sm" onclick="generate_gl(2)" style="margin-left:10px">Export to PDF</button> -->
						</div>
						<!-- <button type="button" class="btn btn-sm  bg-gradient-info" id="btn-exp-collapse"><i class="expandable-table-caret fas fa-caret-right fa-fw"></i>&nbsp;<span id="spn_exp">Expand</span></button> -->
						<table class="table table-bordered table-stripped table-head-fixed table-hover tbl_accounts">
							<thead>

								<?php if($filter_type == 1): ?>

								<tr style="text-align:center;" class="table_header_dblue">
									<th class="table_header_dblue">Account</th>
									<th class="table_header_dblue">Debit</th>
									<th class="table_header_dblue">Credit</th>
								</tr>
								<?php else: ?>
								<tr style="text-align:center;" class="table_header_dblue">
									<th class="table_header_dblue" width="3%"></th>
									<th class="table_header_dblue">Date</th>
									<th class="table_header_dblue">Description</th>
									<th class="table_header_dblue">Post Reference</th>
									<th class="table_header_dblue">Debit</th>
									<th class="table_header_dblue">Credit</th>
									<th class="table_header_dblue">Remarks</th>
								</tr>
								<?php endif; ?>
							</thead>
							<tbody>
								<?php if(count($general_ledger) > 0): ?>

								<?php if($filter_type == 1): ?>
								<?php echo $__env->make('general_ledger.table_summary', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
								<?php else: ?>
								<?php echo $__env->make('general_ledger.table', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
								<?php endif; ?>
								

								<?php else: ?>
								<tr>
									<th colspan="<?php echo e(($filter_type==2)?7:3); ?>" style="text-align:center;">No Record Found</th>
								</tr>
								<?php endif; ?>
							</tbody>
						</table>  
					</div>
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
		$("#txt_search").on("keyup", function() {
			var value = $(this).val().toLowerCase();
			$("tr.row_account").filter(function() {
				$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
			});
		});
	});

	$(document).on('click','#chk_sel_all',function(){
		// $('input.chk_account').is(':visible').each(function(){
		// 	$(this).prop('checked',ck);
		// })
		$('input.chk_account:visible').prop('checked',$(this).prop('checked'))
	})

	$(document).on('click','.col_acc',function(){
		var parent_row = $(this).closest('.row_account');
		var $checkbox = parent_row.find('input.chk_account');
		$checkbox.prop('checked',!$checkbox.prop('checked'));
	})
	function generate_gl(type,export_type){
		var books = [];
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
		// 		  function() 
		// 		  {
		// 		    $('#div_books').removeClass('mandatory');
		// 		  }, 3000);
		// 	});	

		// 	return;
		// }
		var accounts = [];
		$('.chk_account:checked').each(function() {
			accounts.push($(this).val());
		});
		if(accounts.length == 0 && '<?php echo e($filter_type); ?>' == 2){
			Swal.fire({
				title: "Please select atleast 1 Account",
				text: '',
				icon: 'warning',
				showConfirmButton : false,
				timer : 2500
			}).then(function(){
				$('#div_account').addClass('mandatory');
				setTimeout(
					function() 
					{
						$('#div_account').removeClass('mandatory');
					}, 3000);
			});	
			return;
		}

		var param = {
			'start_date' : $('#sel_period_start').val(),
			'end_date' : $('#sel_period_end').val(),
			'show_cancel' : ($('#chk_cancel').prop("checked"))?1:0
		};

		param['export_type'] = export_type ?? 0;

		// var queryString = $.param(param)+'&books='+encodeURIComponent(JSON.stringify(books))+'&accounts='+encodeURIComponent(JSON.stringify(accounts))
		var queryString = $.param(param)+'&accounts='+encodeURIComponent(JSON.stringify(accounts))
		if(type == 1){
			window.location = window.location.pathname+'?'+queryString;
		}else{
			
			window.open(window.location.pathname+'/export?'+queryString,'_blank');
		}
		
		console.log({queryString});
	}
	$('#btn-exp-collapse').on('click',function(){
		if(!$(this).hasClass('collapsed')){
			$(this).addClass('collapsed');
			$('.acc_head').attr('aria-expanded',"false");
			text = "Collapse";
		}else{
			$(this).removeClass('collapsed');
			$('.acc_head').attr('aria-expanded',"true");
			text = "Expand";
		}
		$(this).find('#spn_exp').text(text);
		$('.acc_head').trigger('click');
	})

</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('adminLTE.admin_template', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/general_ledger/index.blade.php ENDPATH**/ ?>