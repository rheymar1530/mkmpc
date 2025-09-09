<?php if($export_type == 1): ?>
<style type="text/css">
.dbl_undline {
	text-decoration-line: underline !important;
	text-decoration-style: double !important;
}

.tbl_gl  u{
	text-decoration: none !important;

}
.pad_head td, .pad_head th{

}
.text-center{
	text-align: center !important;
}
.class_amount{
	padding-left: 12px !important;
}
.borderless td,.borderless th{
    border-top: none;
    border-left: none;
    border-right: none;
    border-bottom: none;
}
.font-emp{
	font-size: 15px !important;
}
</style>
<?php endif; ?>
<?php
	$date_type = $type;
	$field_not_amount = ['Account','line','type','id_chart_account','category'];
	$field_exclude = ['line','type','id_chart_account','category'];

	$headers = $financial_statement['headers'];
	$fin_data_type = $financial_statement['data'];

	$comp_header_col = 0;

	if($comparative_type == 1){
		$comp_header_col = ($date_type==1)?2:1;
	}

	$key_total_excluded = ['LAST_MONTH','LAST_YEAR'];

	//total per type

	$total_type = array();
	$allocation = array();
	$GLOBALS['export_type'] = $export_type ?? 1;

	$GLOBALS['total_type'] = array();

	function check_negative($val){
		if($GLOBALS['export_type'] == 1){
			$col_val = number_format(abs($val),2);			
		}else{
			$col_val = $val;
		}

		if($val == 0){
			return '-';
		}

		if($val < 0 && $GLOBALS['export_type'] == 1){
			$col_val = "($col_val)";
		}

		return $col_val;
	}

	function td_class($val){
		if($val == 0){
			return "text-center";
		}
	}
?>
<table class="table borderless table-head-fixed table-hover tbl_accounts tbl_gl mt-1">
	<thead>
		<?php if($comparative_type == 1): ?>
		<tr style="text-align:center;font-weight: normal;" class="year_head head_tbl">
			<th colspan="3" style="width: 1cm;"></th>
			<?php $__currentLoopData = $comp_header; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hh): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			<th style="text-align:center;" ><?php if($date_type==1): ?><?php echo e($hh); ?><?php endif; ?></th>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

			<?php if($date_type == 1): ?>
			<th style="text-align:center;" >Last Month</th>
			<?php endif; ?>
			<th style="text-align:center;" >Last Year</th>
		</tr>
		<?php endif; ?>
		<tr class="head_tbl" style="text-align:center;border-top: 2px solid !important">
			
				<th colspan="3" style="width: 1cm;"></th>

				<?php $__currentLoopData = $headers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $head): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<?php
					$cl = (!in_array($head,$field_not_amount))?"col_border":"";
				?>
				<th style="text-align:center;" class="<?php echo e($cl); ?>"><?php echo e($header_keys[$head] ?? $head); ?></th>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				<?php if($comparative_type ==1): ?>
				<?php if($date_type == 1): ?>
				<th>Increased<br>(Decreased)</th>
				<?php endif; ?>
				<th>Increased<br>(Decreased)</th>

				<?php endif; ?>
			
		</tr>
	</thead>
	<tbody>
		<?php $__currentLoopData = $fin_data_type; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type=>$fin_data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
		<tr class="pad_head" style="border-top:2px solid;">
			<td colspan="<?php echo e(count($headers)+3+$comp_header_col); ?>" class="row_total font-emp" style="font-weight:bold;font-size:12px"><?php echo e(strtoupper($type)); ?></td>
		</tr>
		<?php
			$GLOBALS['totalType'][$type] = array();
			foreach($headers as $head){
				if(!in_array($head,$field_not_amount)){
					$GLOBALS['totalType'][$type][$head] =0;
				}
			}
			if($comparative_type == 1){
				if($date_type == 1){
					$GLOBALS['totalType'][$type]['LAST_MONTH'] = 0;
				}
				

				$GLOBALS['totalType'][$type]['LAST_YEAR'] = 0;
			}

		?>






		

		<?php $__currentLoopData = $fin_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line=>$lists): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
		<tr style="">
			<td style="width: 0.5cm;" class="row_total"></td>
			<td colspan="<?php echo e(count($headers)+2+$comp_header_col); ?>" style="font-weight:bold" class="row_total"><?php echo e($line); ?></td>
		</tr>

			<?php
				$total_line =array();
				foreach($headers as $head){
					if(!in_array($head,$field_not_amount)){
						$total_line[$head] =0;
					}
				}

				if($comparative_type == 1){
					if($date_type == 1){
						$total_line['LAST_MONTH'] = 0;
					}
					$total_line['LAST_YEAR'] = 0;
				}
			?>
			
			<?php if($line != "Non-current Assets"): ?>
				<?php $__currentLoopData = $lists; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vals): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<tr>
						<td colspan="2"></td>
						<?php $__currentLoopData = $vals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$col): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<?php if(!in_array($key,$field_exclude)): ?>
							<?php if(in_array($key,$field_not_amount)): ?>
							<td style="padding-left: 0.7cm !important"><?php echo e($col); ?></td>
							<?php else: ?>					
							<?php
								$total_line[$key] += $col;
								$GLOBALS['totalType'][$type][$key] += $col;

							?>
							<td class="class_amount col_border <?php echo e(td_class($col)); ?>"><?php echo e(check_negative($col)); ?></td>

							<?php endif; ?>
							<?php endif; ?>

						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

						<?php if($comparative_type == 1): ?>
							<?php if($date_type == 1): ?>
							<td class="class_amount col_border <?php echo e(td_class($vals->A-$vals->B)); ?>"><?php echo e(check_negative($vals->A-$vals->B)); ?></td>
							<?php
								$total_line['LAST_MONTH'] +=$vals->A-$vals->B;
								$GLOBALS['totalType'][$type]['LAST_MONTH']+=$vals->A-$vals->B;
							?>
							<?php endif; ?>
							<?php
								$total_line['LAST_YEAR'] +=$vals->A-$vals->C;
								$GLOBALS['totalType'][$type]['LAST_YEAR']+=$vals->A-$vals->C;
							?>
							<td class="class_amount col_border <?php echo e(td_class($vals->A-$vals->C)); ?>"><?php echo e(check_negative($vals->A-$vals->C)); ?></td>	
						<?php endif; ?>
					</tr>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

			<tr style="border-top: 1px solid;border-bottom: 1px solid;" class="row_total">
				<td></td>
				<td style="font-weight:bold;padding-left:0.7cm" colspan="2">Total <?php echo e($line); ?></td>
					<?php $__currentLoopData = $total_line; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$tot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<td class="class_amount col_border <?php echo e(td_class($tot)); ?>" style="font-weight: bold;"><?php echo e(check_negative($tot)); ?></td>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</tr>
		<?php else: ?>
			<?php echo $__env->make('financial_statement.non-current', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
			
		<?php endif; ?>
		<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>



		<tr style="border-top: 1px solid;border-bottom: 2px solid;" class="row_total pad_head">
			
			<td class="font-emp" style="font-weight:bold;font-size: 12px" colspan="3">TOTAL <?php echo e(strtoupper($type)); ?> </td>
				<?php $__currentLoopData = $GLOBALS['totalType'][$type]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$tot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<td class="class_amount col_border <?php echo e(td_class($tot)); ?>" style="font-weight: bold;"><u><?php echo e(check_negative($tot)); ?></u></td>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
		</tr>
		<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

		<?php if($financial_report_type == 1): ?>
		<tr style="border-bottom: 2px solid;" class="pad_head">
			<td colspan="3" class="font-emp" style="font-weight:bold;font-size: 12px;">TOTAL LIABILITIES AND EQUITY</td>
			<?php $__currentLoopData = $headers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			<?php if(!in_array($h,$field_not_amount)): ?>
			<td class="class_amount col_border <?php echo e(td_class($GLOBALS['totalType']['Liabilities'][$h] + $GLOBALS['totalType']['Equity'][$h])); ?>" style="font-weight: bold;"><u><?php echo e(check_negative($GLOBALS['totalType']['Liabilities'][$h] + $GLOBALS['totalType']['Equity'][$h])); ?></u></td>
			<?php endif; ?>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			<?php if($comparative_type ==1): ?>
				<?php if($date_type == 1): ?>
				<td class="class_amount col_border <?php echo e(td_class($GLOBALS['totalType']['Liabilities']['LAST_MONTH'] + $GLOBALS['totalType']['Equity']['LAST_MONTH'])); ?>" style="font-weight: bold;"><u><?php echo e(check_negative($GLOBALS['totalType']['Liabilities']['LAST_MONTH'] + $GLOBALS['totalType']['Equity']['LAST_MONTH'])); ?></u></td>
				<?php endif; ?>
				<td class="class_amount col_border <?php echo e(td_class($GLOBALS['totalType']['Liabilities']['LAST_YEAR'] + $GLOBALS['totalType']['Equity']['LAST_YEAR'])); ?>" style="font-weight: bold;"><u><?php echo e(check_negative($GLOBALS['totalType']['Liabilities']['LAST_YEAR'] + $GLOBALS['totalType']['Equity']['LAST_YEAR'])); ?></u></td>
			<?php endif; ?>
		</tr>
		<?php else: ?>


		<tr style="border-bottom: 2px solid;" class="row_total pad_head">
			<td colspan="3" style="font-weight:bold">NET SURPLUS</td>
			<?php $__currentLoopData = $headers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			<?php if(!in_array($h,$field_not_amount)): ?>
			<?php
				$alloc = $GLOBALS['totalType']['Revenues'][$h] - $GLOBALS['totalType']['Expenses'][$h];
				$allocation[$h] = $alloc;
			?>
			<td class="class_amount col_border <?php echo e(td_class($alloc)); ?>" style="font-weight: bold;"><u><?php echo e(check_negative($alloc)); ?></u></td>
			<?php endif; ?>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

			<?php if($comparative_type == 1): ?>
				<?php if($date_type == 1): ?>
					<?php
						$alloc = $allocation['A'] - $allocation['B'];
					?>
					<td class="class_amount col_border <?php echo e(td_class($alloc)); ?>" style="font-weight: bold;"><u><?php echo e(check_negative($alloc)); ?></u></td>
				<?php endif; ?>
				<?php
					$alloc = $allocation['A'] - $allocation['C'];
				?>
				<td class="class_amount col_border <?php echo e(td_class($alloc)); ?>" style="font-weight: bold;"><u><?php echo e(check_negative($alloc)); ?></u></td>
			<?php endif; ?>
		</tr>
		<!-- SPACE -->
		
		<tr style="border-top: 2px solid;border-bottom: 2px solid;">
			<td colspan="3" style="font-weight:bold">NET SURPLUS ALLOCATION</td>
			<?php $__currentLoopData = $headers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			<?php if(!in_array($h,$field_not_amount)): ?>
			<td class="col_border">&nbsp;</td>
			<?php endif; ?>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			<?php if($comparative_type == 1): ?>
				<?php if($date_type == 1): ?>
					<td class="col_border">&nbsp;</td>
				<?php endif; ?>
				<td class="col_border">&nbsp;</td>
			<?php endif; ?>
		</tr>
		<!-- ALLOCATIONS -->
		<?php
			$allocation_totals = array();
		?>
		<?php $__currentLoopData = $allocations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $al): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
		<tr>
			<td colspan="2"></td>
			<td style="padding-left: 0.7cm"><?php echo e($al->description); ?></td>
			<?php $__currentLoopData = $headers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			<?php if(!in_array($h,$field_not_amount)): ?>
			<?php
				$al_val = $allocation[$h]*($al->percentage/100);
				$allocation_totals[$h] = $allocation_totals[$h]  ?? 0;
				$allocation_totals[$h]+= $al_val;
			?>

			<td class="col_border class_amount <?php echo e(td_class($al_val)); ?>" style="font-weight: normal;"><?php echo e(check_negative($al_val)); ?></td>
			<?php endif; ?>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			
			<?php if($comparative_type == 1): ?>
				<?php if($date_type == 1): ?>
				 <?php
				 	$alloc_last_month = ($allocation['A'] - $allocation['B'])*($al->percentage/100);
				 	$allocation_totals['LAST_MONTH'] = $allocation_totals['LAST_MONTH'] ?? 0;
				 	$allocation_totals['LAST_MONTH'] +=$alloc_last_month;
				 ?>
				 <td class="col_border class_amount <?php echo e(td_class($alloc_last_month)); ?>" style="font-weight: normal;"><?php echo e(check_negative($alloc_last_month)); ?></td>
				<?php endif; ?>
				<?php
				 	$alloc_last_year = ($allocation['A'] - $allocation['C'])*($al->percentage/100);
				 	$allocation_totals['LAST_YEAR'] = $allocation_totals['LAST_YEAR'] ?? 0;
				 	$allocation_totals['LAST_YEAR'] +=$alloc_last_year;
				 ?>
				 <td class="col_border class_amount <?php echo e(td_class($alloc_last_year)); ?>" style="font-weight: normal;"><?php echo e(check_negative($alloc_last_year)); ?></td>
			<?php endif; ?>



		</tr>
		<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
		<tr class="pad_head" style="border-top:2px solid;border-bottom:2px solid">
			<td colspan="3" style="font-weight:bold;" class="">TOTAL</td>
			<?php $__currentLoopData = $headers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
			<?php if(!in_array($h,$field_not_amount)): ?>

			<td class="class_amount col_border <?php echo e(td_class($allocation_totals[$h])); ?>" style="font-weight: bold;"><u><?php echo e(check_negative($allocation_totals[$h])); ?></u></td>
			<?php endif; ?>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

			<?php if($comparative_type == 1): ?>
				<?php if($date_type == 1): ?>
					<td class="class_amount col_border <?php echo e(td_class($allocation_totals['LAST_MONTH'])); ?>" style="font-weight: bold;"><u><?php echo e(check_negative($allocation_totals['LAST_MONTH'])); ?></u></td>
				<?php endif; ?>
				<td class="class_amount col_border <?php echo e(td_class($allocation_totals['LAST_YEAR'])); ?>" style="font-weight: bold;"><u><?php echo e(check_negative($allocation_totals['LAST_YEAR'])); ?></u></td>
			<?php endif; ?>
			
		</tr>


		<?php endif; ?>
		<!-- TOTAL LIABILITIES AND EQUITY -->

	</tbody>


</table>  


<?php
	// echo json_encode($allocation);	
?> <?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/financial_statement/table.blade.php ENDPATH**/ ?>