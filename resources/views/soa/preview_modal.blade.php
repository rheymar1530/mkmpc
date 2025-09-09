<div class="modal fade bd-example-modal-xl" id="modal-preview-data">
	<div class="modal-dialog modal-conf">
		<div class="modal-content">
			<div class="modal-header" style="padding: 5px !important">
				<h5 class="modal-title"> Statement of Account</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="frm_confirm_request">
				<div class="modal-body">				
					<iframe id="iframe_prev_data" style="border:0;height:700px;width: 100%" ></iframe>
				</div>
				<div class="modal-footer">
					<div class="float-right">
						<!-- <button type="submit" class="btn btn-primary">Save changes</button> -->
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

@push('scripts')
	<script type="text/javascript">

	</script>
@endpush