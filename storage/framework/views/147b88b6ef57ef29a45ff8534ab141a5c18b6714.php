<style type="text/css">
	.f-total{
		font-size: 18px !important;
	}
</style>
<?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type=>$transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="box">
<table class="table table-bordered table-stripped table-head-fixed table-hover tbl_accounts tbl_gl" style="margin-top:20px">

	<thead>

		<tr style="text-align:center;" class="table_header_dblue_cust tbl_head">
			<th class="table_header_dblue_cust">Date</th>
			<!-- <th class="table_header_dblue_cust">Type</th> -->
			<th class="table_header_dblue_cust"><?php echo e(($fil_type=='cash_receipt')?'Payor':'Payee'); ?></th>
			<th class="table_header_dblue_cust" style="width:12cm">Reference</th>
			<th class="table_header_dblue_cust" style="width: 4cm;">Voucher Reference</th>
			<?php if($type == "Cash-in Summary"): ?>
			<th class="table_header_dblue_cust">OR #</th>

			<?php endif; ?>
			<th class="table_header_dblue_cust">Amount</th>
			<th class="table_header_dblue_cust" style="width:6.5cm">Remarks</th>

		</tr>
	</thead>
	<tbody>
		<?php if(count($transaction) > 0): ?>
		<?php
			$total = 0;
		?>
		<?php
			$GLOBALS['export_type'] = $export_type ?? 1;
			function check_amt($amt){
				$formated = ($GLOBALS['export_type']==1)?number_format($amt,2):$amt;
				return ($amt == 0)?'':$formated;
			}
		?>
		<?php $__currentLoopData = $transaction; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
		<tr class="row_trans" title="<?php echo e(strtoupper($tr->type)); ?>">
			<td><?php echo e($tr->date); ?></td>
			<!-- <td><?php echo e($tr->type); ?></td> -->
			<td><?php echo e($tr->payee); ?></td>
			<td><?php echo e($tr->description); ?></td>

			<td style="text-align:center"><?php echo e($tr->reference); ?></td>
			<?php if($type == "Cash-in Summary"): ?>
			<td style="text-align:center"><?php echo e($tr->or_no); ?></td>

			<?php endif; ?>


			<td class="class_amount"><?php echo e(check_amt($tr->amount)); ?></td>
			<td><?php echo e($tr->remarks); ?></td>
		</tr>
		<?php $total+=$tr->amount; ?>
		<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
		<tr class="row_total" style="border-top:1px solid">
			<th class="f-total" colspan="<?php echo e(($type=='Cash-in Summary')?5:4); ?>" style="text-align:left;font-weight: bold;font-size: 12px;">GRAND TOTAL</th>
			<th class="class_amount f-total" style="font-weight: bold;font-size: 12px;"><?php echo e(check_amt($total)); ?></th>
			<th></th>
		</tr>
		<?php else: ?>
			<tr>
				<th colspan="<?php echo e(($type=='Cash-in Summary')?7:6); ?>" style="text-align:center;">No data</th>
			</tr>
		<?php endif; ?>
		
	</tbody>

</table>  
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/journal_report/table.blade.php ENDPATH**/ ?>