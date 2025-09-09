<form class='form-horizontal' method='post' id="form_menu_submit" enctype="multipart/form-data" >
    <div class="form-group header-group-0 " id="form-group-cms_menus_privileges" style="">
        <label class="control-label col-sm-2">Privileges
            <span class="text-danger" title="This field is required">*</span>
        </label>
        <div class="col-sm-9">
            <select class="select2" id="sel_privileges" multiple="multiple" data-placeholder="Select a Privileges" style="width: 100%;">
             <?php $__currentLoopData = $privilege_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $priv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
             <option value="<?php echo e($priv->id); ?>"><?php echo e($priv->name); ?></option>
             <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
         </select>
         <div class="text-danger"> </div><!--end-text-danger-->

         <p class="help-block"></p>

     </div>
 </div>
<!--       <div class="ribbon-wrapper">
    <div class="ribbon bg-primary">
      Ribbon
    </div>
</div> -->
<div class="form-group header-group-0 " id="form-group-cms_menus_name" style="">
    <label class="control-label col-sm-2">Name
        <span class="text-danger" title="This field is required">*</span>
    </label>
    <div class="col-sm-9">
     <input type="text" title="Name" required="" placeholder="You can only enter the letter only" maxlength="255" class="form-control" name="name" id="txt_name" value="<?php echo e($menu_details->name ?? ''); ?>">
     <div class="text-danger"> </div><!--end-text-danger-->
     <p class="help-block"></p>
 </div>
</div>
<div class="form-group header-group-0 " id="form-group-cms_menus_link" style="">
    <label class="control-label col-sm-2">Link
        <span class="text-danger" title="This field is required">*</span>
    </label>
    <div class="col-sm-9">
     <input type="text" title="Name" placeholder="Link" maxlength="255" class="form-control" name="name" id="txt_link" value="<?php echo e($menu_details->path ?? ''); ?>">
     <div class="text-danger"> </div><!--end-text-danger-->
     <p class="help-block"></p>
 </div>
</div>
<div class="form-group" id="form-group-cms_menus_icon" style="">
    <label class="control-label col-sm-2">Icon
        <span class="text-danger" title="This field is required">*</span>
    </label>
    <div class="col-sm-9">
        <select class="select2 form-control" id="sel_icon" style="width: 100%;" required="">
         <?php $__currentLoopData = $font_awesome_icon; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name=>$icon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
         <option value="<?php echo e($icon); ?>" date-icon="<?php echo e($icon); ?>"><?php echo e($name); ?></option>
         <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
     </select>
     <div class="text-danger"> </div><!--end-text-danger-->

     <p class="help-block"></p>
 </div>
</div>
<div class="form-group row">
    <?php
        $checked = (!isset($menu_details->is_maintenance) || $menu_details->is_maintenance == 0)?"":"checked";
    ?>
    <div class="col-sm-10 p-2">
      <div class="form-check" style="margin-left:8px">
        <input class="form-check-input" type="checkbox" id="chk_maintenance" <?php echo e($checked); ?>>
        <label class="form-check-label" for="chk_maintenance">
          Maintenance Module
      </label>
  </div>
</div>
</div>


<div class="form-group row">
    <?php
        $checked = (!isset($menu_details->is_report) || $menu_details->is_report == 0)?"":"checked";
    ?>
    <div class="col-sm-10 p-2">
      <div class="form-check" style="margin-left:8px">
        <input class="form-check-input" type="checkbox" id="chk_report" <?php echo e($checked); ?>>
        <label class="form-check-label" for="chk_report">
          Report Module
      </label>
  </div>
</div>
</div>
<!--     <div class="form-group header-group-0 " id="form-group-cms_menus_link" style="">
        <label class="control-label col-sm-2">Status
            <span class="text-danger" title="This field is required">*</span>
        </label>
        <div class="col-sm-9">
           <select id="sel_status" class="form-control">
               <option value="1"> Active</option>
           </select>
            <div class="text-danger"> </div>
            <p class="help-block"></p>
        </div>
    </div> -->
    <!-- <p align="right"></p> -->
    <p align="right">
        <?php if($opcode == 1): ?>
        <input type='button' class='btn btn-default' value='Back to Add' style="margin-right: 10px;" onclick="window.location = '/admin/menu_management'" />
        <?php endif; ?>
        <input type='submit' class='btn btn-primary' value='Save'/>
    </p>
</form>

<?php $__env->startPush('scripts'); ?>
<script type="text/javascript">
    var opcode = '<?php echo $opcode?>';
        if(opcode == 1){ // Edit
            var priv_list = $.parseJSON('<?php echo $priv_list ?? json_encode([]) ?>');
            console.log({priv_list});
            $('#sel_privileges').val(priv_list);
            $('#sel_icon').val('<?php echo $menu_details->icon ?? '' ?>');
        }
        $('#sel_privileges').select2();
        function formatText (icon) {
            return $('<span><i class="'+icon.id+'"></i>&nbsp;' + icon.text +'</span>');
        };
        $('#sel_icon').select2({
            templateSelection: formatText,
            templateResult: formatText
        });
        $('#form_menu_submit').submit(function(e){
            e.preventDefault();
            if($('#sel_privileges').val().length == 0){
                Swal.fire({
                  position: 'center',
                  icon: 'warning',
                  title: 'Please Select Privilege(s)',
                  showConfirmButton: false,
                  cancelButtonText: `Close`,
                  showCancelButton: true,
              })
                $('#sel_privileges').focus();
                return;
            }
            Swal.fire({
              title: 'Do you want to save the changes?',
              showDenyButton: false,
              showCancelButton: true,
              icon: 'warning',
              confirmButtonText: `Save`,
          }).then((result) => {
            if (result.isConfirmed) {
                post();
            } 
        });
      })
        function post(){
            var data = {
                'name'  : $('#txt_name').val(),
                'link' : $('#txt_link').val(),
                'icon' : $('#sel_icon').val(),
                'privileges' : $('#sel_privileges').val(),
                'id_menu' : '<?php echo $menu_details->id ?? 0 ?>',
                'opcode' : opcode,
                'is_maintenance' : ($('#chk_maintenance').prop('checked'))?1:0,
                'is_report' : ($('#chk_report').prop('checked'))?1:0
            };
            $.ajax({
                type     :    'POST',
                url      :    '/admin/post_menu',
                data     :     data,
                success  :     function(response){
                    console.log({response});
                    if(response.message == 'success'){
                        location.reload();
                    }
                }
            });
            console.log({data});
        }
    </script>
    <?php $__env->stopPush(); ?><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/menu_management/menu_form.blade.php ENDPATH**/ ?>