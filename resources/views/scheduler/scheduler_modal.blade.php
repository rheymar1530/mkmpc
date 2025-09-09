
<?php
$scheduler_types = DB::table('scheduler_type')->get();

$status_o = ['Inactive','Active'];

?>
<div class="modal fade" id="scheduler_modal" tabindex="-1" role="dialog" aria-labelledby="SchedulerModal" aria-hidden="true">
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
					<div class="form-row">
						<div class="form-group col-md-5 mt-2">

							<?php
								if(($scheduler->status ?? 0) == 0){
									$dt = MySession::current_date();
								}else{
									$dt = $scheduler->date;
								}
							?>
							<label class="lbl_color">Date</label>
							<input type="date" class="form-control" name="" value="{{$dt}}" id="txt_sched_date">
						</div>
						<div class="form-group col-md-7 mt-2">
							<label class="lbl_color">Schedule</label>
							<select class="form-control p-0" id="sel_sched_type">
								@foreach($scheduler_types as $st)
								<option value="{{$st->id_scheduler_type}}" <?php echo ($st->id_scheduler_type == ($scheduler->id_scheduler_type ?? 2))?"selected":""; ?> >{{$st->description}}</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="form-row">
						<div class="form-group col-md-6 mt-1">
							<label class="lbl_color">Stop Date</label>
							<input type="date" class="form-control" name="" value="{{$scheduler->stop_date ?? ''}}" id="txt_stop_date">
						</div>
						<div class="form-group col-md-6 mt-1">
							<label class="lbl_color">Status</label>
							<select class="form-control p-0" id="sel_status">
								@foreach($status_o as $val=>$s)
								<option value="{{$val}}" <?php echo (($scheduler->status ?? 0))==$val?"selected":""; ?> >{{$s}}</option>
								@endforeach
							</select>
						</div>
					</div>
					@if($SCHEDULER_PARAM['id_scheduler'] > 0)
					<input type="checkbox" name="" id="chk_active" <?php echo (($scheduler->status ?? 0))==1?'checked':'';  ?> ><span class="lbl_color ml-2">Active</span>
					<p class="text-danger mb-0 mt-2"><i>Scheduler already exists (ID#{{$SCHEDULER_PARAM['id_scheduler']}})</i></p>
					<p class="text-danger"><i>Bla Bla Bla Bla</i></p>

					@endif

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