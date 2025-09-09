<style type="text/css">
/*    .crd_chart .card-body{
        padding-bottom: -10px;
    }*/
    .crd_pad{
        padding-right: 15px !important;
        padding-left: 15px !important;
    }
    .mandatory{
        border-color: rgba(232, 63, 82, 0.8) !important;
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(232, 63, 82, 0.6) !important;
        outline: 0 none;
    }
    .form-row  label{
        margin-bottom: unset !important;
        font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
        font-size: 13px;
    }
    .form-row{
        margin-top: -7px;
    }
</style>
<?php
    $status_list = array(
        1 => 'Active',
        0 => 'Inactive'
    );
?>
<div class="modal fade" id="chart_modal" tabindex="-1" role="dialog" aria-labelledby="booking" aria-hidden="true">
    <div class="modal-dialog modal-conf" >
        <div class="modal-content">
            <form id="frm_chart">
                <div class="modal-header" style="padding:5px;padding-left: 10px;">
                    <h5 class="modal-title h4 head_lbl">Chart of Accounts <span class="spn_id_pickup_request"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <div id="div_btn_add"><button type="button" class="btn btn-sm bg-gradient-primary2" id="btn_add_chart"><i class="fa fa-plus"></i>&nbsp;Add Chart</button></div>
                        <div style="max-height: calc(100vh - 177px);overflow-y: auto;overflow-x: auto;margin-top: 15px;padding-right: 10px;" id="div_chart_form">
                            <div class="crd_chart_app">
                                <div class="card c-border crd_chart">
                                    <div class="card-body p-2 crd_pad">
                                        <div class="form-group row" style="margin-top:1px !important">
                                            <label class="col-sm-12 control-label col-form-label" style="text-align: right;font-size: 15px;"><a onclick="remove_chart(this)"><i class="fa fa-times"></i></a></label>
                                        </div>
                                        <div class="row p-0" style="margin-top:-35px">
                                            <div class="col-sm-12 p-1">
                                                <div class="form-row">
                                                    <div class="form-group col-md-12">
                                                        <label for="txt_description">Description</label>
                                                        <input type="text" class="form-control txt_description" placeholder="Description" required>
                                                    </div>

                                                </div>
                                                <div class="form-row">
                                                    <div class="form-group col-md-6">
                                                        <label for="txt_code">Code</label>
                                                        <input type="text" class="form-control txt_code"  placeholder="Code" required>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="sel_cat">Category</label>
                                                        <select class="form-control form-in-text in p-0 sel_cat">
                                                            <?php $__currentLoopData = $category; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($cat->id_chart_account_category); ?>"><?php echo e($cat->name); ?></option>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-row">
                                                    <div class="form-group col-md-6">
                                                        <label for="sel_line_item">Line Item</label>
                                                        <select class="form-control form-in-text in p-0 sel_line_item">
                                                            <?php $__currentLoopData = $line_item; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <option value="<?php echo e($line->id_chart_account_line_item); ?>"><?php echo e($line->description); ?></option>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="sel_normal">Normal</label>
                                                        <select class="form-control form-in-text in p-0 sel_normal">
                                                            <?php $__currentLoopData = $normal; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $norm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($norm->id_chart_account_normal); ?>"><?php echo e($norm->description); ?></option>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-row">
                                                    <div class="form-group col-md-6">
                                                        <label for="sel_type">Type</label>
                                                        <select class="form-control form-in-text in p-0 sel_type">
                                                            <?php $__currentLoopData = $type; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($t->id_chart_account_type); ?>"><?php echo e($t->description); ?></option>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label for="sel_sub_type">Sub-Type</label>
                                                        <select class="form-control form-in-text in p-0 sel_sub_type">
                                                            <?php $__currentLoopData = $sub_type; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($sub->id_chart_account_subtype); ?>"><?php echo e($sub->description); ?></option>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-row">
                                                    <div class="form-group col-md-6">
                                                        <label>Depreciation Account</label>
                                                        <select class="form-control form-in-text in p-0 sel_dep_account">
                                                         <option value="0">-</option>
                                                         <?php $__currentLoopData = $charts_sel; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                         <option value="<?php echo e($ch->id_chart_account); ?>"><?php echo e($ch->account); ?></option>
                                                         <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label>Acc Depreciation Account</label>
                                                        <select class="form-control form-in-text in p-0 sel_ac_dep_account">
                                                         <option value="0">-</option>
                                                         <?php $__currentLoopData = $ac_charts_sel; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                         <option value="<?php echo e($ch->id_chart_account); ?>"><?php echo e($ch->account); ?></option>
                                                         <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-row">
                                                <div class="form-group col-md-8">
                                                        <label>Cash Flow</label>
                                                        <select class="form-control form-in-text in p-0 sel_cash_flow">
                                                            <option value="0">None</option>
                                                            <?php $__currentLoopData = $cfs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v=>$cf): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($cf->id_cash_flow); ?>"><?php echo e($cf->type); ?> <?php echo e($cf->sub_type); ?> > <?php echo e($cf->description); ?></option>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </select>
                                                    </div>
                                                   <div class="form-group col-md-4">
                                                        <label>Status</label>
                                                        <select class="form-control form-in-text in p-0 sel_status">
                                                            <?php $__currentLoopData = $status_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v=>$st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($v); ?>"><?php echo e($st); ?></option>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        </select>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>             
                                    </div>
                                </div>
                            </div>

                        </div>


                    </div>
                </div>
                <div class="modal-footer" style="padding:5px;padding-left: 10px;">
                    <?php if($credential->is_edit || $credential->is_create): ?>
                    <span id="spn_btn_save"> <button class="btn bg-gradient-success2" id="btn_save">Save</button></span>
                       
                    <?php endif; ?>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                </div>
            </form>
        </div>

    </div>
</div>
<?php $__env->startPush('scripts'); ?>
<script type="text/javascript">
    var $card_chart = $('.crd_chart_app').detach().html();
    var $btn_add_chart= $('#btn_add_chart').detach();
    var $btn_save = $('#btn_save').detach();

    $(document).on('click','#btn_add_chart',function(){
        append_chart();
    })
    function append_chart(){
        $('#div_chart_form').append($card_chart);
        var new_card = $('.crd_chart').last();
        new_card.find('select:not(.sel_status)').val('');
        new_card.find('select.sel_dep_account').val(0);
        new_card.find('select.sel_cash_flow').val(0);
    }
    function remove_chart(obj){
        var parent_crd = obj.closest('div.crd_chart');
        parent_crd.remove();
    }
    $('#frm_chart').submit(function(e){
        e.preventDefault();

        Swal.fire({
            title: 'Are you sure you want to save this ?',
            text: '',
            icon: 'warning',
            confirmButtonText: 'Yes',
            showCancelButton: true,
            confirmButtonColor: "#DD6B55"
        }).then((result) => {
            if (result.isConfirmed) {
               post();
           }
       })
        

    });
    function post(){
        // txt_code,txt_description,sel_cat,sel_line_item,sel_normal,sel_type,sel_sub_type
        var txt_code = [],txt_description=[],sel_cat = [],sel_line_item = [],sel_normal = [],sel_type = [],sel_sub_type = [],sel_status=[],sel_dep_acc=[],sel_ac_dep_account=[],sel_cash_flow=[];;
        $('div.crd_chart').each(function(){
            txt_code.push($(this).find('input.txt_code').val());
            txt_description.push($(this).find('input.txt_description').val());
            sel_cat.push($(this).find('select.sel_cat').val());
            sel_line_item.push($(this).find('select.sel_line_item').val());
            sel_normal.push($(this).find('select.sel_normal').val());
            sel_type.push($(this).find('select.sel_type').val());
            sel_sub_type.push($(this).find('select.sel_sub_type').val());
            sel_status.push($(this).find('select.sel_status').val());
            sel_dep_acc.push($(this).find('select.sel_dep_account').val());
            sel_ac_dep_account.push($(this).find('select.sel_ac_dep_account').val());
            sel_cash_flow.push($(this).find('select.sel_cash_flow').val());
        });
        var inputs = ['txt_code','txt_description','sel_cat','sel_line_item','sel_normal','sel_type','sel_sub_type','sel_status'];
        var postField = {};
        postField['txt_code'] = txt_code;
        postField['txt_description'] = txt_description;
        postField['sel_cat'] = sel_cat;
        postField['sel_line_item'] = sel_line_item;
        postField['sel_normal'] = sel_normal;
        postField['sel_type'] = sel_type;
        postField['sel_sub_type'] = sel_sub_type;
        postField['id_chart_account'] = id_chart_account_holder;
        postField['sel_status'] = sel_status;
        postField['sel_dep_acc'] = sel_dep_acc;
        postField['sel_ac_dep_account'] = sel_ac_dep_account;
        postField['sel_cash_flow'] = sel_cash_flow;
        postField['opcode'] = opcode;


        $.ajax({

            type           :        'POST',
            url            :        '/charts_of_account/post',
            data           :        postField,
            beforeSend     :        function(){
                show_loader();
                $('.mandatory').removeClass('mandatory');
            },
            success        :        function(response){
                hide_loader();
                console.log({response})
                if(response.RESPONSE_CODE == "success"){
                    Swal.fire({
                        title: "Chart of account successfully saved",
                        text: '',
                        icon: 'success',
                        showConfirmButton : false,
                        timer  : 1300
                    }).then((result) => {
                        location.reload()
                    });
                }else if(response.RESPONSE_CODE == "DUPLICATE_CODE"){
                 Swal.fire({
                    title: "Account Code already Exists",
                    text: '',
                    icon: 'warning',
                    showConfirmButton : false,
                    timer  : 1300
                }).then((result) => {
                    var codes = response.array_codes;
                    $('input.txt_code').each(function(){
                        if($.inArray($(this).val(),codes) >= 0){

                            $(this).addClass('mandatory');
                        }
                    })
                });                                       
            }else if(response.RESPONSE_CODE == "CREDENTIAL_ERROR"){
                Swal.fire({
                    title: response.message,
                    text: '',
                    icon: 'warning',
                    showConfirmButton : false,
                    timer : 1500
                }); 
            }
        },
        error: function(xhr, status, error) {
            hide_loader()
            var errorMessage = xhr.status + ': ' + xhr.statusText
            isClick = false;
            Swal.fire({
                title: "Error-" + errorMessage,
                text: '',
                icon: 'warning',
                confirmButtonText: 'OK',
                confirmButtonColor: "#DD6B55"
            });
        }
    })

    }
</script>
<?php $__env->stopPush(); ?><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/accounting/chart_of_account_modal.blade.php ENDPATH**/ ?>