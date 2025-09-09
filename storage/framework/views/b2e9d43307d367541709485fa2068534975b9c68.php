<div class="col-md-12" style="margin-top:20px">
	<div class="card c-border">
		
		<div class="card-body">
			<h5 class="lbl_color text-center mb-4"><i class="fas fa-upload"></i>&nbsp;Upload Invoice/Receipts</h5>
			<div class="col-md-12">
				<button type="button" class="btn btn-sm bg-gradient-info2" style="display:none;" id="btn_undo" onclick="undo_attachment()"><i class="fas fa fa-undo"></i>&nbsp;Undo</button>
				<div style="max-height: calc(100vh - 200px);overflow-y: auto;margin-top: 5px !important;overflow-x: auto" id="attachment_body">
					<?php if(isset($attachments) && count($attachments) > 0): ?>
					<?php $__currentLoopData = $attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $count=>$att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<div class="card card-no-shadow div_attachment c-border" data-id="<?php echo e($att->id_cdv_attachments); ?>">
						<div class="card-body" style="padding-bottom:0px !important">
							<div class="row p-0" style="margin-top:-20px">
								<label class="col-sm-12 control-label col-form-label" style="text-align: right;font-size: 15px;"><a onclick="remove_attachment_card(this)"><i class="fa fa-times"></i></a></label>
								<h5 style="margin-top:-20px">Attachment <span class="attachment_count"><?php echo e($count+1); ?></span></h5>
							</div>
							<div class="form-group col-md-12 file_path_show" style="margin-top: 10px;">
								<span><a href="/storage/uploads/cdv_attachments/<?php echo e($att->path_file); ?>" target="_blank"><?php echo e($att->file_name); ?></a></span> 
								<!-- <button type="button" class="btn btn-sm bg-gradient-danger" style="margin-left: 20px" onclick="change_file_input(this)"><i class="fa fa-times"></i>&nbsp;Replace File</button> -->
							</div>
						</div>
					</div>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					<?php else: ?>
					<div class="lbl_color text-center card c-border" id="no_attachment"><div class="card-body"><h5>No Attachment</h5></div></div>
					<?php endif; ?>
				</div> 
				
				
				
			</div>
		</div>
		<?php if($allow_post): ?>
		<div class="card-footer">
			<a class="btn btn-xs bg-gradient-success2 col-md-12" onclick="append_attachment()"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add Attachment</a>
		</div>
		<?php endif; ?>
	</div>
</div>
<?php $__env->startPush('scripts'); ?>
<script type="text/javascript" id="attachments">
	const file_input = `	<div class="form-row div_file_input" style="margin-top: 15px;">
	<div class="form-group col-md-10">
	<input type="file" class="button form-control p-1 file_attachment"  value="Upload file" style="height:35px !important;" id="file_gov_id" accept="image/jpeg,image/gif,image/png,application/pdf,image/x-eps" multiple>
	</div>
	</div>`;
	const file_div = `<div class="card card-no-shadow div_attachment c-border">
	<div class="card-body" style="padding-bottom:0px !important">
	<div class="row p-0" style="margin-top:-20px">
	<label class="col-sm-12 control-label col-form-label" style="text-align: right;font-size: 15px;"><a onclick="remove_attachment_card(this)"><i class="fa fa-times"></i></a></label>
	<h5 style="margin-top:-20px">Attachment <span class="attachment_count"></span></h5>

	</div>
	`+file_input+`
	</div>
	</div>`;
	const no_att = '<div class="lbl_color text-center card c-border" id="no_attachment"><div class="card-body"><h5>No Attachment</h5></div></div>';
	
	function append_attachment(){
		$('#no_attachment').hide(300).remove();
		$('#attachment_body').append(file_div);
		set_attachment_count();
		animate_element($('.div_attachment').last(),1)
	}
	function remove_attachment_card(obj){
		var parent_div = obj.closest('.div_attachment');

		var data_id = $(parent_div).attr('data-id');

		console.log(data_id);
		if(data_id == undefined){
			parent_div.closest('.div_attachment').remove(500);

		}else{
			$('#btn_undo').show();
			removed_attachments.push(data_id);
			$(parent_div).addClass('removed_attachment');
		}
		

		var cc = $('.div_attachment').not(".removed_attachment").length;
		if(cc == 0){
			$('#attachment_body').append(no_att)
		}
		set_attachment_count();
	}
	function set_attachment_count(){
		var counter = 1;
		$('.attachment_count').each(function(i){
			var parent_div = $(this).closest('.div_attachment');
			if(!$(parent_div).hasClass('removed_attachment')){
				$(this).text(counter);
				counter++;
			}
			
		})		
	}
	$(document).on("change",".file_attachment",function(){
		var input = this;
		var url = $(this).val();

		var ext = url.substring(url.lastIndexOf('.') + 1).toLowerCase();
		if(input.files && input.files[0]){
			$allowed_file_type = ["pdf","jpeg","jpg","png"];
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
	function undo_attachment(){
		var id = removed_attachments.pop();
		console.log({id});
		$('.div_attachment[data-id="'+id+'"]').removeClass("removed_attachment");
		set_attachment_count();
		$('#no_attachment').hide(300).remove();
		if(removed_attachments.length == 0){
			$('#btn_undo').hide();
		}
	}
	// function change_file_input(obj){
	// 	var parent_div = $(obj).closest(".div_attachment");
	// 	parent_div.find('.file_path_show').hide();
	// 	parent_div.find('.card-body').append(file_input)
	// 	parent_div.find('.div_file_input').append('<div class="form-group col-md-2"><a class="btn btn-sm bg-gradient-primary" onclick="undo_file(this)"><i class="fas fa-undo"></i>Undo</a></div>'); //add undo button
	// }
	// function undo_file(obj){
	// 	var parent_div = $(obj).closest(".div_attachment");
	// 	parent_div.find('.div_file_input').remove();
	// 	parent_div.find('.file_path_show').show();
	// }
</script>
<?php $__env->stopPush(); ?><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/cash_disbursement/attachment.blade.php ENDPATH**/ ?>