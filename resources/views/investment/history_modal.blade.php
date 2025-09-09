<style type="text/css">
	.modal-p-60 {
		max-width: 60% !important;
		min-width: 60% !important;
		margin: auto;
	}
</style>
<div class="modal fade" id="history_modal" tabindex="-1" role="dialog" aria-labelledby="PrintModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-body p-0">
				
				<iframe id="frame_history" class="embed-responsive-item" frameborder="0" style="border:0;height:700px;width: 100%" src=""></iframe>
			</div>

		</div>
	</div>
</div>

@push('scripts')
<script type="text/javascript">
	function show_withdrawals(){
		$('#frame_history').attr('src',`/investment/show-withdrawal/${ID_INVESTMENT}`);
		$('#history_modal').modal('show');
	}
</script>
@endpush
