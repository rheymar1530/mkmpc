
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

$transaction_type = [
	2=>"ATM Swipe",
	1=>"Cash"
	
];
?>
<div id="summary_date_modal" class="modal fade"  role="dialog" aria-hidden="false">
	<div class="modal-dialog  modal-p-50" role="document" style="width: 50%">
		<form id="generate_summary">
			<div class="modal-content " style="">
				<div class="modal-header panel_header">
					<h4 class="modal-title"><i class="fa fa-eye"></i> ATM Swipe Summary</h4>

					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">×</span>
					</button>
				</div>
				<div class="modal-body" style="max-height: calc(100vh - 210px);
				overflow-y: auto;overflow-x: auto">    
				<div class="form-horizontal" >   
					<div class="form-group row" style="margin-top:10px">
						<div class="col-md-2">
							<div class="form-check">
							  <label class="form-check-label font-bold" style="margin-left:-18px">
							   	 Date
							  </label>
							</div>
						</div>						
						<div class="col-md-4">
							<input type="date" title="Date" required="" class="form-control opt_sel form-in-text fil_date" id="txt_date_summary_start"  value="{{$current_date}}" name="date_start">
						</div>
						<div class="col-md-4">
							<input type="date" title="Date" required="" class="form-control opt_sel form-in-text fil_date" id="txt_date_summary_end"  value="{{$current_date}}" name="date_end">
						</div>
					</div>
						
				</div>
			</div>
			<div class="modal-footer modal_body">
				<button type="submit" class="btn btn-md  bg-gradient-primary but"><i class="fa fa-search"></i>&nbsp;&nbsp;Generate</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div><!-- /.modal-dialog -->
	</form>
</div>
</div>


@push('scripts')
<script type="text/javascript">
	$('#generate_summary').submit(function(e){
		e.preventDefault();
		$('#summary_date_modal').modal('hide');
		print_page('/atm_swipe/swipe_summary/'+$('#txt_date_summary_start').val()+'/'+$('#txt_date_summary_end').val());

	})
</script>
@endpush

