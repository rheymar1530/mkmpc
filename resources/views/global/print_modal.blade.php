<style type="text/css">
	.modal-p-70 {
		max-width: 70% !important;
		min-width: 70% !important;
		margin: auto;
	}
</style>
<div class="modal fade" id="print_modal" tabindex="-1" role="dialog" aria-labelledby="PrintModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-body p-0">
				
				<iframe id="print_frame" class="embed-responsive-item" frameborder="0" style="border:0;height:700px;width: 100%" src=""></iframe>
			</div>
			<div class="modal-footer">
				<a class="btn btn-sm bg-gradient-danger" id="btn_tab">&nbsp;Open in new tab</a>
				<button type="button" class="btn btn-sm btn-default but" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

@push('scripts')
<script type="text/javascript">
	var current_print_holder;
	function print_page(print_route){

		var link = print_route;
		console.log({print_route});
		current_print_holder = print_route;
		$('#print_modal').modal('show');
		$('#print_frame').attr('src', link);	
	}
	$('#btn_tab').click(function() {
		var link = current_print_holder;
		window.open(link, '_blank');
	});	
</script>
@endpush