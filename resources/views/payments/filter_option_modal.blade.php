
<style type="text/css">
	.modal-p-50 {
		max-width: 50% !important;
		min-width: 50% !important;
		margin: auto;
	}
	.filter_label{
		margin-top: 8px;
	}
	.font-bold{
		font-weight: bold;
	}
</style>
<?php

?>
<div id="view_options" class="modal fade"  role="dialog" aria-hidden="false">
	<div class="modal-dialog  modal-p-50" role="document" style="width: 50%">
		<form id="filter_target">
			<div class="modal-content " style="">
				<div class="modal-header panel_header">
					<h4 class="modal-title"><i class="fa fa-eye"></i> View Options</h4>

					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">×</span>
					</button>
				</div>
				<div class="modal-body" style="max-height: calc(100vh - 210px);
				overflow-y: auto;overflow-x: auto">    
				<div class="form-horizontal" id="submit_filter">   
					<div class="form-group row" style="margin-top:10px">
						<div class="col-md-3">
							<div class="form-check">
							  <label class="form-check-label font-bold" for="chk_date_type" style="margin-left:-18px">
							   	Transaction Date
							  </label>
							</div>
						</div>						
						<div class="col-md-4">
							<input type="date" title="Date" required="" class="form-control opt_sel form-in-text fil_date" id="txt_fil_date_from"  value="{{$start_date}}" name="date_start">
						</div>
						<div class="col-md-4">
							<input type="date" title="Date" required="" class="form-control opt_sel form-in-text fil_date" id="txt_fil_date_to"  value="{{$end_date}}" name="date_end">
						</div>
					</div>


				</div>
			</div>
			<div class="modal-footer modal_body">
				<button type="submit" class="btn btn-md  bg-gradient-primary but"><i class="fa fa-search"></i>&nbsp;&nbsp;Search</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div><!-- /.modal-dialog -->
	</form>
</div>
</div>
@push('scripts')
<script type="text/javascript">



	$('#filter_target').submit(function(e){
		e.preventDefault();
		var filter_data = {};
		filter_data['start_date'] = $('#txt_fil_date_from').val();
		filter_data['end_date'] = $('#txt_fil_date_to').val();

		var location_reload = '/payments?'+$.param(filter_data);
		window.location = location_reload;
		console.log({location_reload});
	})



</script>
@endpush

