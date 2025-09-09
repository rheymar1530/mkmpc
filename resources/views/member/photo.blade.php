<style type="text/css">
	.spn_t{
		font-weight: bold;
		font-size: 14px;
	}
	.spn_txt{
		word-wrap:break-word;
		overflow: hidden;
	}
	.row_review{
		margin-top: -13px !important;
	}
	div#file {
		position: relative;
		overflow: hidden;
	}
	input#txt_photo {
		position: absolute;
		font-size: 50px;
		opacity: 0;
		right: 0;
		top: 0;
	}
</style>
<div class="row">
	<div class="col-sm-5">
		<div class="form-row lbl_color">
			<div class="form-group col-md-12">
				<?php
				$prev_image = ($image_path ?? "/storage/uploads/account_image/no_img.png");
				?>
				<div style="display: flex;justify-content: center;">
					<a href="{{$prev_image}}" data-toggle="lightbox" data-type="image" id="lightbox_preview">
						<img class="profile-user-img img-fluid center" src="{{$prev_image}}" alt="User profile picture" style="height:250px !important;width: 250px !important;" id="preview_image">
					</a>
				</div>
			</div>
			<div class="form-group col-md-12">
				<div style="display: flex;justify-content: center;">
					<div class="btn-group" role="group" aria-label="Button group with nested dropdown">
						<button type="button" class="btn btn-sm bg-gradient-info" onclick="$('#txt_photo').click()"><i class="fas fa-upload"></i>&nbsp;Upload Photo</button>
						<input type="file" name="file" id="txt_photo" style="display:none" />
						<button type="button" class="btn btn-sm bg-gradient-primary" onclick="show_modal_camera()"><i class="fas fa-camera"></i>&nbsp;Open Camera</button>

						<div class="btn-group" role="group">
							<button id="btnGroupDrop1" type="button" class="btn bg-gradient-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								Options
							</button>
							<div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
								<a class="dropdown-item" onclick="remove_image()"><i class="fa fa-times"></i>&nbsp;&nbsp;Remove Image</a>
								@if($opcode == 1)
								<a class="dropdown-item" onclick="undo_image()"><i class="fas fa-undo"></i>&nbsp;&nbsp;Undo</a>
								@endif
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>


@push('scripts')
<script type="text/javascript">
	
	var attachment_icon_parent_path = "/storage/uploads/icons/";
	var image_via = 0; // 0 - upload ; 1 - webcam


	$(document).on('click', '[data-toggle="lightbox"]', function(event){
		event.preventDefault();

		$('#lightbox_preview').attr('href',$('#preview_image').attr('src'))

		$(this).ekkoLightbox();
	});


	var no_image = '/storage/uploads/account_image/no_img.png';
	var last_photo = '<?php echo $prev_image; ?>';
	var is_remove_image = 0;
	$('#txt_photo').change(function(){
		var input = this;
		var url = $(this).val();
		console.log({url});
		var ext = url.substring(url.lastIndexOf('.') + 1).toLowerCase();
		if (input.files && input.files[0]&& (ext == "gif" || ext == "png" || ext == "jpeg" || ext == "jpg" || ext=="jfif")) 
		{
			var reader = new FileReader();
			reader.onload = function (e) {
				$('#preview_image').attr('src', e.target.result);
				$('#preview_image:not([src=""])').show();
			}
			reader.readAsDataURL(input.files[0]);
			is_remove_image  = 0;

			image_via = 0;

		}else if(url == ''){ //cancelled
			$(this).val('')
			return;
		}
		else
		{
			$(this).val('')
			Swal.fire({
				position: 'center',
				icon: 'warning',
				title: 'Please select valid image',
				showConfirmButton: false,
				cancelButtonText: `Close`,
				showCancelButton: true,
			})
		}
	});
	function remove_image(){
		$('#preview_image').attr('src', no_image);
		$('#txt_photo').val('');
		is_remove_image = 1;
		image_via = 0;
	}

	function append_beneficiary(){
		var out = `<tr class="row_beneficiaries">
		<td><input class="form-control frm-ben txt_ben_name" required></td>
		<td><input class="form-control frm-ben txt_ben_relationship" required></td>
		<td><a class="btn btn-xs btn-edit delete_row" title="Remove" onclick="delete_ben_row(this)"><i class="fa fa-times"></i>&nbsp;&nbsp;</a></td>
		</tr>`;

		$('#beneficiaries_body').append(out);
	}
	function delete_ben_row(obj){
		var parent_row = $(obj).closest("tr.row_beneficiaries")
		parent_row.remove();
	}
	function undo_image(){
		$('#preview_image').attr('src', last_photo);
		$('#txt_photo').val('');	
		is_remove_image = 0;
		image_via = 0;
	}

	$(document).on('click','#btn_submit_member,#btn_submit_member_add',function(e){
		// return;
		Swal.fire({
			title: 'Are you sure you want to save this ?',
			text: '',
			icon: 'warning',
			confirmButtonText: 'Yes',
			showCancelButton: true,
			confirmButtonColor: "#DD6B55"
		}).then((result) => {
			if (result.isConfirmed) {
				post($(e.target).attr('id'));
			}
		})
	})

</script>
@endpush