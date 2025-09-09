<div class="row">
	<!-- <button type="button" class="btn btn-sm bg-gradient-info" onclick="append_attachment()"><i class="fa fa-plus"></i>&nbsp;Add Attachment</button> -->
	<div class="col-sm-6">
		<div id="att_forms">
			<div class="card card-attachment">
				<div class="card-header p-2 bg-gradient-success2">Government Issued ID</div>
				<div class="card-body">
					<div class="form-row" style="margin-top: 15px;" id="sec_gov_id_att">
						@if($opcode ==0 || $opcode ==1 && !isset($attachments["1"]))		
						<div class="form-group col-md-12">
							<input type="file" class="button form-control p-1 file_attachment"  value="Upload file" style="height:35px !important;" id="file_gov_id" accept="image/jpeg,image/gif,image/png,application/pdf,image/x-eps">
						</div>
						@else
						<?php $file_path = "/storage/uploads/member_files/".$details->member_code."/attachments/".$attachments['1'][0]->file_name;?>
						<div class="form-group col-md-12">
							<span><a href="{{$file_path}}" target="_blank">{{$attachments['1'][0]->file_name}}</a></span> <button type="button" class="btn btn-sm bg-gradient-danger" style="margin-left: 20px" onclick="change_file_input(1)"><i class="fa fa-times"></i>&nbsp;Remove File</button>
						</div>
						@endif
					</div>
					<div class="form-row">
						<div class="form-group col-md-12">
							<label>Remarks/Descripion<i><small> (optional)</small></i></label>
							<textarea class="form-control txt_att_remarks" rows="2" style="resize: none;" id="gov_id_remarks">{{$attachments['1'][0]->description ?? ''}}</textarea>	
						</div>		
					</div>		
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="card card-attachment">
			<div class="card-header p-2 bg-gradient-danger2">BOD Membership Resolution</div>
			<div class="card-body">
				<div class="form-row" style="margin-top: 15px;" id="sec_bod_mem">
					@if($opcode ==0 || $opcode ==1 && !isset($attachments["2"]))
					<div class="form-group col-md-12">
						<input type="file" class="button form-control p-1 file_attachment"  value="Upload file" style="height:35px !important;" id="file_bod_mem" accept="image/jpeg,image/gif,image/png,application/pdf,image/x-eps">
					</div>
					@else
					<?php $file_path = "/storage/uploads/member_files/".$details->member_code."/attachments/".$attachments['2'][0]->file_name;?>
					<div class="form-group col-md-12">
						<span><a href="{{$file_path}}" target="_blank">{{$attachments['2'][0]->file_name}}</a></span> <button type="button" class="btn btn-sm bg-gradient-danger" style="margin-left: 20px" onclick="change_file_input(2)"><i class="fa fa-times"></i>&nbsp;Remove File</button>
					</div>
					@endif
				</div>	
				<div class="form-row">
					<div class="form-group col-md-12">
						<label>Remarks/Descripion<i><small> (optional)</small></i></label>
						<textarea class="form-control txt_att_remarks" rows="2" style="resize: none;"  id="bod_mem_remarks">{{$attachments['2'][0]->description ?? ''}}</textarea>	
					</div>		
				</div>		
			</div>
		</div>
	</div>
<!-- 	<div class="col-sm-12">
		<button type="button" class="btn btn-default prev-step bg-light round_button"><i class="fa fa-chevron-left"></i> Back</button>
		<button class="btn btn-info next-step float-right bg-gradient-primary2 round_button">Next <i class="fa fa-chevron-right"></i></button>
	</div> -->
</div>

@push('scripts')
<script type="text/javascript">
	var attachment_html = `			<div class="card card-attachment">
	<div class="card-body">
	<div class="form-group row" style="margin-top: -15px;">
	<label class="col-sm-12 control-label col-form-label" style="text-align: right;font-size: 15px;"><a onclick="remove_attachment(this)"><i class="fa fa-times"></i></a></label>
	</div>
	<div class="form-row" style="margin-top:-25px">
	<div class="form-group col-md-12">
	<input type="file" class="button form-control p-1 file_attachment"  value="Upload file" style="height:35px !important;">
	</div>
	</div>	
	<div class="form-row">
	<div class="form-group col-md-12">
	<label>Remarks/Descripion<i><small> (optional)</small></i></label>
	<textarea class="form-control txt_att_remarks" rows="2" style="resize: none;"></textarea>	
	</div>		
	</div>		
	</div>
	</div>`;
	const $sec_gov_id_att = $('#sec_gov_id_att').html();
	const $sec_bod_mem = $('#sec_bod_mem').html();

	var remove_gov_id = 0;
	var remove_bod_id = 0;
	
	function append_attachment(){
		$('#att_forms').append(attachment_html);
	}
	function remove_attachment(obj){
		var parent_card = $(obj).closest('div.card');
		parent_card.remove();
	}
	$(document).on("change",".file_attachment",function(){

		var input = this;
		var url = $(this).val();
		
		var ext = url.substring(url.lastIndexOf('.') + 1).toLowerCase();

		if(input.files && input.files[0]){
			$allowed_file_type = ["pdf","jpeg","jpg","png"];

			// if(ext != "pdf" && (ext == "gif" || ext == "png" || ext == "jpeg" || ext == "jpg" || ext=="jfif") ){
			if($.inArray(ext,$allowed_file_type) < 0){
				Swal.fire({
					position: 'center',
					icon: 'warning',
					title: 'Please select valid file (PDF OR IMAGES)',
					showConfirmButton: false,
					cancelButtonText: `Close`,
					showCancelButton: true,
				})		
				$(this).val('');		
			}
		}
	});
	function change_file_input(type){
		if(type == 1){
			var id_input = "file_gov_id";
			var sec_change = "#sec_gov_id_att";
			remove_gov_id = 1;

		}else{
			var id_input = "file_bod_mem";
			var sec_change = "#sec_bod_mem";
			remove_bod_id  = 1;
		}
		var out = `		<div class="form-group col-md-5">
		<input type="file" class="button form-control p-1 file_attachment"  value="Upload files" style="height:35px !important;" id="`+id_input+`" accept="image/jpeg,image/gif,image/png,application/pdf,image/x-eps">
		</div>
		<div class="form-group"><button type="button" class="btn btn-sm bg-gradient-primary" style="margin-left:10px" onclick="undo_file(`+type+`)"><i class="fas fa-undo"></i>&nbsp;&nbsp;Undo</button></div>`;
		$(sec_change).html(out);
		// $('#'+id_input).attr("accept","")
	}
	function undo_file(type){
		if(type == 1){
			$('#sec_gov_id_att').html($sec_gov_id_att);
			remove_gov_id = 0;
		}else{
			$('#sec_bod_mem').html($sec_bod_mem);
			remove_bod_id = 0;
		}
	}

</script>
@endpush