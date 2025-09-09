<div class="modal fade bd-example-modal-xl" id="modal-request-status">
	<div class="modal-dialog modal-conf">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Confirmation</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="frm_confirm_request">
				<div class="modal-body">				
					<div class="form-row">

						<div class="form-group col-md-3">
							<label for="sel_status">Status</label>
							<select class="form-control" id="sel_status">
								<option value="1">Confirm</option>
								<option value="2">Cancel</option>
							</select>
						</div>
						<div class="form-group col-md-3">
							<label for="txt_date_status"  id="lbl_status_date">Confirmation Date</label>
							<input type="date" class="form-control" id="txt_date_status" value="{{$current_date}}"/>
						</div>
					</div>
					<div class="form-group">
						<div class="table-responsive" style="max-height: calc(100vh - 250px);overflow-y: auto;margin-top: 10px;">
							<table class="table table-hover table-striped" id="tbl_items_conf" style="white-space: nowrap;width: 100%;overflow-x: auto;">
								<thead>
									<tr>
										<th>Item Name</th>
										<th>Quantity</th>
										<th>UOM</th>
										@if($details->id_type == 2)
											<th>Brand Selected</th>
										@endif

										<th>Qty Approved</th>
										<th>UOM</th>
										<th>Remarks</th>
										<th width="1%"></th>
									</tr>
								</thead>
								<tbody>
									@foreach($items as $item)
										<tr class="row_item_conf">
											<td>
												<input type="text" name="item_name" class="col_table_input" value="{{ $item->item_name }}" readonly="">
											</td>
											<td class="td_qty"><input type="number" name="Quantity" class="col_table_input" value="{{ $item->quantity }}" readonly=""></td>
											<td class="col_sel_uom">
												<select class="col_uom col_table_input" disabled="">
													<?php
														$uom_list = explode(",",$item->uom_list);
													?>
													@foreach($uom_list as $uom)
														<?php
															$uom_parse = explode('-', $uom);
															$selected = ($item->id_uom == $uom_parse[0]) ?'selected':'';
														?>
														<option value="{{ $uom_parse[0] }}" {{$selected}}>{{ $uom_parse[1] }}</option>
													@endforeach
				
												</select>
											</td>
											@if($details->id_type == 2)
											<td>
												<input type="text" name="brand_name" class="col_table_input"  readonly="">
											</td>
											@endif
											<td class="td_qty"><input type="number" name="Quantity" class="col_table_input col_table_input_fil col_quantity_approved" value="{{ $item->quantity }}"  min="0" max="{{ $item->quantity }}"></td>
											<td class="col_fil_uom"></td>
											<td><input type="text" name="Remarks" class="col_remarks_approved col_table_input col_table_input_fil" value="{{ $item->remarks }}"></td>
											<td class="hide col_id_request_details">{{ $item->id_request_details }}</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<div class="float-right">
						<button type="submit" class="btn btn-primary">Save changes</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</div>
			</form>

		</div>
	</div>
</div>

@push('scripts')
	<script type="text/javascript">
		$(document).ready(function(){
			$('tr.row_item_conf').each(function(){
				var uom_html = $(this).find('td.col_sel_uom').html();
				$(this).find('td.col_fil_uom').html(uom_html);
				var cur_uom = $(this).find('td.col_fil_uom').find('select.col_uom')
				cur_uom.addClass('col_table_input_fil col_uom_confirm');
				cur_uom.prop('disabled',false);
			})
		})
		var type = "Confirmed";
		var confirm_text = "Confirm";
		$('#sel_status').change(function(){
			var val = $(this).val();
			var text = "";
			switch(val){
				case '1':
					text = "Confirmation Date";
					type = "Confirmed";
					confirm_text = "Confirm";
					break;
				case '2':
					text = "Cancellation Date";
					type = "Cancelled";
					confirm_text = "Cancell";
					break;
				default:
					break;
			}
			$('#lbl_status_date').text(text);
		})
		$('#frm_confirm_request').submit(function(e){
			e.preventDefault();
			Swal.fire({
				title: 'Do you want to '+confirm_text+' this request ?',
				icon: 'warning',
				showDenyButton: false,
				showCancelButton: true,
				confirmButtonText: `Save`,
			}).then((result) => {
				if (result.isConfirmed) {
					post_status();
				} 
			// else if (result.isDenied) {
			//   Swal.fire('Changes are not saved', '', 'info')
			// }
			})	
		})
		function post_status(){
			var status = $('#sel_status').val();
			var date_confirm = $('#txt_date_status').val();
			var id_request = "{{ $details->id_request }}";
			var id_request_details = [],uom_confirm=[],quantity_approved=[],remarks_confirm=[];
			$('tr.row_item_conf').each(function(){
				id_request_details.push($(this).find('td.col_id_request_details').text());
				quantity_approved.push($(this).find('input.col_quantity_approved').val());
				uom_confirm.push($(this).find('select.col_uom_confirm').val());
				remarks_confirm.push($(this).find('input.col_remarks_approved').val());
			})
			console.log({id_request_details,uom_confirm,quantity_approved,remarks_confirm});
			console.log({status,date_confirm,id_request});
			$.ajax({
				type           :      'POST',
				url            :      '/admin/request/post_status',
				data           :      {
										'status' : status,
										'date_confirm' : date_confirm,
										'id_request' : id_request,

										'id_request_details' : id_request_details,
										'uom_confirm' : uom_confirm,
										'quantity_approved' : quantity_approved,
										'remarks_confirm' : remarks_confirm
									  },
				beforeSend     :      function(){
										show_loader();
				},
				success        :      function(response){
					console.log({response});
					setTimeout(
						function() {
							hide_loader();
							if(response.message == "success"){
								Swal.fire({
									position: 'center',
									icon: 'success',
									title: 'Request Successfully '+type,
									showConfirmButton: false,
									timer: 1500
								}).then(function() {
									location.reload();
									
								})
							}
					}, 1500);
				},error: function(xhr, status, error){
				hide_loader();
				var errorMessage = xhr.status + ': ' + xhr.statusText;
				Swal.fire({
					position: 'center',
					icon: 'warning',
					title: "Error-"+errorMessage,
					showConfirmButton: false,
					showCancelButton: true,
					cancelButtonText : "Close"
				})
	        }  
			})
		}
	</script>
@endpush