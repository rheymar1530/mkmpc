<style type="text/css">
	.tbl_net_pay tr>th ,.tbl_requirements tr>th{
		padding: 5px;
		padding-left: 5px;
		padding-right: 5px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 12px;
	}
	.tbl_net_pay tr>td,.tbl_requirements tr>td{
		padding: 0px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 12px;
	}
	.frm-net,.frm-requirements{
		height: 24px !important;
		width: 100%;    
		font-size: 13px;
	}
	.class_amount{
		text-align: right;
	}
</style>
<button type="button" class="btn btn-sm bg-gradient-primary2" onclick="append_net()"><i class="fa fa-plus"></i>&nbsp;Add More</button>

<div class="table-responsive" style="max-height: calc(100vh - 200px);overflow-y: auto;margin-top: 5px !important;overflow-x: auto">
	<table class="table table-bordered table-stripped table-head-fixed tbl_net_pay" style="white-space: nowrap;">
		<thead>
			<tr>
				<!-- <th class="table_header_dblue" colspan="2" style="text-align:center;">Principal Amount</th>
				<th class="table_header_dblue" colspan="2" style="text-align:center;">Net Pay</th> -->
				<th class="table_header_dblue" colspan="3" style="text-align:center">Salary Requirements</th>
			</tr>
			<tr>
				<th class="table_header_dblue" style="text-align:center">Principal Maximum</th>
				<th class="table_header_dblue" style="text-align:center">Net Pay Minimum</th>
				<th class="table_header_dblue" style="min-width: 20px;max-width: 20px;"></th>
			</tr>
		</thead>
		<tbody id="net_body">
			<tr class="row_net">
				<!-- <td><input type="text" name="" required class="form-control frm-net class_amount" value="0.00" key="prin_min"></td> -->
				<td><input type="text" name="" required class="form-control frm-net class_amount" value="0.00" key="prin_max"></td>
				<td><input type="text" name="" required class="form-control frm-net class_amount" value="0.00" key="net_min"></td>
				<!-- <td><input type="text" name="" required class="form-control frm-net class_amount" value="0.00" key="net_max"></td> -->
				<td><a onclick="remove_net(this)" style="margin-left:5px;margin-top: 10px !important;"><i class="fa fa-times"></i></a>
			</tr>
		</tbody>
	</table>	
</div>	

@push('scripts')
<script type="text/javascript">
	$net_row = '<tr class="row_net">'+$('tr.row_net').html()+'</tr>';
	function append_net(){
		$('#net_body').append($net_row);
		animate_element($('.row_net').last(),1)
	}
	function remove_net(obj){
		var parent_row = $(obj).closest('tr.row_net');
		animate_element(parent_row,2)
		// parent_row.remove()
	}
</script>
@endpush

