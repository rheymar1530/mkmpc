<style type="text/css">

	.modal-header{
		padding: 10px;
	}

	#camera{
		width: 500;
		height: 380px;
		border: 1px solid black;
	}
</style>
<div class="modal fade bd-example-modal-xl" id="modal_camera">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Take a photo</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			
			<div class="modal-body">				
				<div id="camera"></div>

			</div>
			<div class="modal-footer">
				<div class="float-right">
					<button type="button" class="btn bg-gradient-primary2" onclick="take_snapshot()">Capture</button>
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>


		</div>
	</div>
</div>


@push('scripts')

<script type="text/javascript">
	$(document).ready(function(){
		$('#modal_camera').on('hidden.bs.modal', function (e) {
			Webcam.reset();
			console.log("CAMERA HIDE");
		});
	})
	var current_op = 1;
	function show_modal_camera(opcode){
		current_op = opcode;
		$('#modal_camera').modal('show')           
		Webcam.set({
			width : 500,
			height:380,
			crop_width:0,
			crop_height:0,
			image_format : 'jpeg',
			jpeg_quality : 100,
			flip_horiz: true
		});
		Webcam.attach('#camera')
		$('#camera').css({'width':'500px','height':'380px'})
	}
	function take_snapshot(){

		image_via = 1;
		Webcam.snap(function(data_uri){
			console.log({data_uri});
			$('#preview_image').attr('src',data_uri)
			$('#modal_camera').modal('hide');    
		})
	}

</script>
@endpush