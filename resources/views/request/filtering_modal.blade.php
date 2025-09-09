<div class="modal fade bd-example-modal-xl" id="modal-filtering">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Filtering Option</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="frm_submit_filter">
				<div class="modal-body">				
					<div class="col-sm-8">
						<div class="form-group row" style="margin-top: 5px;">
						    <label for="sel_request_type" class="col-sm-4 col-form-label">Request Type</label>
						    <div class="col-sm-6">
						      <select class="form-control" id="sel_request_type">
						      	<option value="0"> **ALL**</option>
						      	@foreach($request_types as $type)
						      		<option value="{{$type->id_type}}">{{$type->description}}</option>
						      	@endforeach
						      </select>
						    </div>
						</div>
						<div class="form-group row">
							<label class="radio col-sm-4">
								<input type="radio" name="optradio" value="1" checked="">
								<span>Status</span>
							</label>
							<div class="col-sm-6">
								<select class="form-control filter_input option_1" id="sel_status" data-name="status">
							      	<option value="-">**ALL**</option>
							      	<option value="0" selected="">Draft</option>
							      	<option value="1">Confirmed</option>
							      	<option value="2">Cancelled</option>
							    </select>
							</div>
						</div>
						<div class="form-group row">
							<label class="radio col-sm-4">
								<input type="radio" name="optradio" value="2">
								<span>Date Request</span>
							</label>
						    <div class="col-sm-4">
						    	<input type="date" class="form-control filter_input option_2" value="{{$current_date}}" data-name="start_date"/>
						    </div>
						    <div class="col-sm-4">
						    	<input type="date" class="form-control filter_input option_2" value="{{$current_date}}" data-name="end_date"/>
						    </div>
						</div>
						<div class="form-group row">
							<label class="radio col-sm-4">
								<input type="radio" name="optradio" value="3">
								<span>Date Confirmed/Cancelled</span>
							</label>
						    <div class="col-sm-4">
						    	<input type="date" class="form-control filter_input option_3" value="{{$current_date}}" data-name="start_date"/>
						    </div>
						    <div class="col-sm-4">
						    	<input type="date" class="form-control filter_input option_3" value="{{$current_date}}" data-name="end_date"/>
						    </div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<div class="float-right">
						<button type="submit" class="btn btn-primary">Search</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</div>
			</form>

		</div>
	</div>
</div>

@push('scripts')
	<script type="text/javascript">
		var fil = jQuery.parseJSON('<?php echo $filter_data;?>');
		$("input[name=optradio][value=" + fil.opcode + "]").attr('checked', 'checked');
		$('#sel_request_type').val(fil.request_type);
		console.log({fil});
		$.each(fil,function(i,item){
			console.log({i,item});
			$('.option_'+fil.opcode).each(function(){
				if($(this).attr('data-name') == i){
					$(this).val(item);
				}
			})
		})
		// console.log();
		set_disabled(1);
		$('input[name=optradio]').change(function(){
			var val = $(this).val();
			set_disabled(val);

		})
		function set_disabled(val){
			$('.filter_input').attr('disabled',true);
			$('.option_'+val).attr('disabled',false);	
		}
		$('#frm_submit_filter').submit(function(e){
			e.preventDefault();
			var filter_data = {};
			var val = $('input[name=optradio]:checked').val();
			filter_data['request_type'] = $('#sel_request_type').val();
			filter_data['opcode'] = val;
			$('.option_'+val).each(function(){
				var key = $(this).attr('data-name');
				var value = $(this).val();
				filter_data[key] = value;
			})

			window.location = '/admin/request/index?filter_data='+encodeURIComponent(JSON.stringify(filter_data));;

			// console.log($.param(filter_data));
			return;
			console.log({filter_data});
			$.ajax({
				type           :           'GET',
				url            :           '/admin/request/filter',
				data           :           {'filter_data' : filter_data,
										    'opcode' : val},
				beforeSend     :           function(){
											// alert(123);
											show_loader();
				},
				success        :           function(response){
											console.log({response});
											$('#tbl_request_list').DataTable().destroy();
											draw_table(response.data);
											init_table().draw();
											// dt_table.draw();
											setTimeout(
											function() {

												hide_loader();
												

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
		})
		function draw_table(data){
			var out = "";
			$.each(data,function(i,item){
				out += '<tr class="request_row">';
				out +=	'<td class="col_id_request"><a href="/admin/request/view?id_request='+item.id_request+'">'+item.id_request+'</a></td>'
				out +=	'<td>'+item.date+'</td>'
				out +=	'<td>'+item.requested_by+'</td>'
				out +=	'<td>'+item.request_type+'</td>'
				out +=	'<td>'+item.remarks+'</td>'
				out +=	'<td>'+item.status+'</td>'
				out+= '</tr>';
	
			})
			$('#list_body').html(out);
			console.log({out});
		}


	</script>
@endpush