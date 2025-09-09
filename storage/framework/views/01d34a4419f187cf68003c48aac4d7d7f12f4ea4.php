<style type="text/css">
	.descripition_col{
		width: 4cm !important;
	}
	.name_col{
		width: 4cm !important;
	}
	.row_total td{
		font-size: 18px !important;
	}
</style>

<?php
	$GLOBALS['export_type'] = $export_type ?? 1;
	function check_amt($amt){
		$formated = ($GLOBALS['export_type']==1)?number_format($amt,2):$amt;
		return ($amt == 0)?'':$formated;
	}
?>
<?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type=>$transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="box">
<table class="table table-bordered table-stripped table-head-fixed table-hover tbl_accounts tbl_gl" style="margin-top:20px">

	<thead>
		<tr style="text-align:center;" class="table_header_dblue_cust">
			<th class="table_header_dblue_cust" colspan="<?php echo e(($type=='Cash Receipt Voucher')?9:8); ?>" style="text-align:center;font-size: 17px"><?php echo e(strtoupper($type)); ?></th>
		</tr>
		<tr style="text-align:center;border-bottom:1px solid;border-top:1px solid" class="table_header_dblue_cust">
			<th class="table_header_dblue_cust">Date</th>
			<th class="table_header_dblue_cust">Reference</th>
			<?php if($type == "Cash Receipt Voucher"): ?>
			<th class="table_header_dblue_cust">OR #</th>

			<?php endif; ?>
			<th class="table_header_dblue_cust name_col">Name</th>
			<th class="table_header_dblue_cust descripition_col" >Description</th>

			<!-- <th class="table_header_dblue_cust">Acc Code</th> -->
			<th class="table_header_dblue_cust">Account</th>
			<th class="table_header_dblue_cust">Debit</th>
			<th class="table_header_dblue_cust">Credit</th>
			<th class="table_header_dblue_cust">Remarks</th>
		</tr>
	</thead>
	<tbody>
		<?php
			$debit_g_total = 0;
			$credit_g_total = 0;
		?>
		<?php if(count($transaction) > 0): ?>
			<?php $__currentLoopData = $transaction; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ref=>$trans_ref): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<?php
					$r_span = count($trans_ref);
				?>
				<?php
					$ac_debit=0;
					$ac_credit=0;
				?>
				<?php $__currentLoopData = $trans_ref; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c=>$items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<tr class="row_trans">
					<?php if($c==0): ?>
					<td rowspan="<?php echo e($r_span); ?>"><?php echo e($items->date); ?></td>
					<td rowspan="<?php echo e($r_span); ?>" style="text-align:center;"><?php echo e($items->reference); ?></td>
					<?php if($type == "Cash Receipt Voucher"): ?>
					<td rowspan="<?php echo e($r_span); ?>" style="text-align:center;"><?php echo e($items->or_no); ?></td>
					<?php endif; ?>
					<td rowspan="<?php echo e($r_span); ?>"><?php echo e($items->payee); ?></td>
					<td rowspan="<?php echo e($r_span); ?>"><?php echo e($items->entry_description); ?></td>
					<?php endif; ?>
					<!-- <td style="text-align:center;"><?php echo e($items->account_code); ?></td> -->
					<td><?php echo e($items->account_code); ?> || <?php echo e($items->account_name); ?></td>
					<?php
						$cred = ($items->credit >0)?(check_amt($items->credit).($items->status==10?'*':'')):'';
						$deb = ($items->debit >0)?(check_amt($items->debit).($items->status==10?'*':'')):'';
					?>
					<td class="class_amount"><?php echo e($deb); ?></td>
					<td class="class_amount"><?php echo e($cred); ?></td>
					<td style="padding-left: 10px"><?php echo e($items->details); ?></td>

					<?php
						$ac_debit+=$items->debit;
						$ac_credit+=$items->credit;

						if($items->status != 10){
							$debit_g_total += $items->debit;
							$credit_g_total += $items->credit;
						}
					?>
				</tr>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				<tr class="row_trans row_total" style="border-bottom:1px solid;">
					<td style="font-weight:bold;text-align:right;" colspan="<?php echo e(($type=='Cash Receipt Voucher')?5:4); ?>"></td>
					<td style="font-weight:bold;text-align:left;">TOTAL</td>
					<td style="font-weight:bold" class="class_amount"><?php echo e(check_amt($ac_debit)); ?><?php echo e(($items->status==10?'*':'')); ?></td>
					<td style="font-weight:bold" class="class_amount"><?php echo e(check_amt($ac_credit)); ?><?php echo e(($items->status==10?'*':'')); ?></td>
					<td></td>
				</tr>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				<tr class="row_total" style="border-bottom:1px solid;">
					<td style="font-weight:bold;font-size: 12px;" colspan="<?php echo e(($type=='Cash Receipt Voucher')?6:5); ?>"><?php echo e($type); ?> Grand Total</td>
					<td style="font-weight:bold;font-size: 12px" class="class_amount"><?php echo e(check_amt($debit_g_total)); ?></td>
					<td style="font-weight:bold;font-size: 12px" class="class_amount"><?php echo e(check_amt($credit_g_total)); ?></td>
					<td></td>
				</tr>
		<?php endif; ?>
		
		
	</tbody>

</table>  
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/transaction_summary/table_entry.blade.php ENDPATH**/ ?>