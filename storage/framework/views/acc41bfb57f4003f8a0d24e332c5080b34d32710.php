
<div class="col-md-12 p-0">
	<div class="card c-border">
		<div class="card-body">
			<h5 class="lbl_color text-center mb-4">
				Requirements
			</h5>
			<div class="col-md-12">

				<div class="table-responsive" style="max-height: calc(100vh - 200px);overflow-y: auto;margin-top: 5px !important;overflow-x: auto">
					<table class="table table-bordered table-stripped table-head-fixed tbl_requirements" style="white-space: nowrap;">
						<thead>
							<tr>
								<th class="table_header_dblue" style="text-align:center;width: 2%;">No</th>
								<th class="table_header_dblue" style="text-align:center;">Description</th>
								<th class="table_header_dblue" style="min-width: 15px;max-width: 15px;"></th>
							</tr>
						</thead>
						<tbody id="requirements_body">
							<tr class="row_requirements">
								<td style="text-align:center;font-weight: bold;padding-top: 5px;" class="req_no">1</td>
								<td><input type="text" name=""  class="form-control frm-requirements" value="" key="req_description"></td>
								<td ><a onclick="remove_requirements(this)" style="margin-left:5px;margin-top: 10px !important;"><i class="fa fa-times"></i></a></td>
							</tr>
						</tbody>
					</table>	
				</div>
			</div>
		</div>
		<div class="card-footer custom_card_footer">
			<div class="col-md-12">
				<button type="button" class="btn btn-sm bg-gradient-primary2 col-md-12" onclick="append_requirements()"><i class="fa fa-plus"></i>&nbsp;Add More</button>
			</div>
		</div>
	</div>
</div>
<?php $__env->startPush('scripts'); ?>
<script type="text/javascript">
	$requirements_row = '<tr class="row_requirements">'+$('tr.row_requirements').html()+'</tr>';
	function append_requirements(){
		$('#requirements_body').append($requirements_row);

		numbering_requirments()
		animate_element($('.row_requirements').last(),1);
	}
	function remove_requirements(obj){
		var parent_row = $(obj).closest('tr.row_requirements');
		animate_element(parent_row,2);
		
		numbering_requirments()

	}

	function numbering_requirments(){
		$('tr.row_requirements').each(function(i){
			$(this).find('td.req_no').text(i+1);
		})
	}
</script>
<?php $__env->stopPush(); ?><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/loan_service/requirements.blade.php ENDPATH**/ ?>