
<?php
$scheduler_types = DB::table('scheduler_type')->get();
$status_o = ['Inactive','Active'];
?>
<div class="modal fade" id="scheduler_view" tabindex="-1" role="dialog" aria-labelledby="SchedulerModal" aria-hidden="true">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<form id="frm_post_schedule">
				<div class="modal-header" style="padding:5px;padding-left: 10px;">
					<h5 class="modal-title h4"><i class="fa fa-calendar"></i>&nbsp;Scheduler </h5>

					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">×</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="card c-border">
						<div class="card-body">
							<label class="lbl_color mb-0">ID: <span id="spn_id_scheduler"></span></label><br>
							<label class="lbl_color mb-0">Date: <span id="spn_date_scheduler"></span></label><br>
							<label class="lbl_color">Reference: <span id="spn_reference"></span></label>
						</div>
					</div>

					<div class="form-row">
						<div class="form-group col-md-6">
							<label class="lbl_color mb-0">Date</label>
							<input type="date" class="form-control" name="" value="" id="txt_sched_date">
						</div>
						<div class="form-group col-md-6">
							<label class="lbl_color mb-0">Schedule</label>
							<select class="form-control p-0" id="sel_sched_type">
								@foreach($scheduler_types as $st)
								<option value="{{$st->id_scheduler_type}}" <?php echo ($st->id_scheduler_type == ($scheduler->id_scheduler_type ?? 2))?"selected":""; ?> >{{$st->description}}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="form-row">
						<div class="form-group col-md-6">
							<label class="lbl_color mb-0">Stop Date</label>
							<input type="date" class="form-control" name="" value="{{$scheduler->stop_date ?? ''}}" id="txt_stop_date">
						</div>
						<div class="form-group col-md-6">
							<label class="lbl_color mb-0">Status</label>
							<select class="form-control p-0" id="sel_status">
								@foreach($status_o as $val=>$s)
								<option value="{{$val}}" <?php echo (($scheduler->status ?? 0))==$val?"selected":""; ?> >{{$s}}</option>
								@endforeach
							</select>
						</div>
					</div>
					<table class="tbl_scheduler table-head-fixed table-bordered" width="100%">
						<thead>
							<tr class="text-center">
								<th class="table_header_dblue">Date</th>
								<th class="table_header_dblue">New Reference</th>
								<th class="table_header_dblue">Date Executed</th>
								<th class="table_header_dblue">Status</th>
							</tr>
						</thead>
						<tbody id="tbody_history"></tbody>
					</table>
				</div>
				<div class="modal-footer">
					<button class="btn btn-sm bg-gradient-success">&nbsp;Set Scheduler</button>
					<button type="button" class="btn btn-sm btn-default but" data-dismiss="modal">Close</button>
				</div>
			</form>
		</div>
	</div>
</div>
@include('scheduler.scheduler_post_script')
@push('scripts')
<script>
	function show_scheduler_details(id_scheduler,link){
		link_o = link;
		link = removeLastSlash(link);
	
		$.ajax({
			type        :        'GET',
			url         :        '/scheduler/view-details',
			data        :         {'id_scheduler' : id_scheduler},
			success     :         function(response){
				console.log({response});
				var out = ``;
				$('#spn_id_scheduler').text(id_scheduler);
				$('#spn_date_scheduler').text(response.details.date);
				$('#spn_reference').html(`<a href="${link_o}" target="_blank">${response.details.reference}</a>`);

				$('#sel_sched_type').val(response.details.id_scheduler_type);
				$('#txt_stop_date').val(response.details.rstop_date);
				$('#sel_status').val(response.details.rstatus);
				$('#txt_sched_date').val(response.sdate);


				
				$.each(response.history,function(i,item){
					out += `<tr>`
					out += `<td>${item.date}</td>
							<td class="text-center"><a target="_blank" href="${link}${item.new_reference}">${item.new_reference}</a></td>
							<td>${item.date_executed ?? ''}</td>
							<td><span class="badge text-sm badge-${item.status_c}">${item.status}</span></td>`;
					out += `</tr>`;
				});
				// tbody_history
				$('#tbody_history').html(out);
				$('#scheduler_view').modal('show');


			}
		})
	}
	function removeLastSlash(inputString) {
		return inputString.split('/').slice(0,-1).join('/')+'/';
	}
</script>
@endpush