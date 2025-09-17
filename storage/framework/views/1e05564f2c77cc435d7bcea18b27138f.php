<style type="text/css">
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
<div class="modal fade" id="status_modal" tabindex="-1" role="dialog" aria-labelledby="booking" aria-hidden="true">
    <div class="modal-dialog modal-conf" >
        <div class="modal-content">
            <form id="form_cancel_submit">
                <div class="modal-header" style="padding:5px;padding-left: 10px;">
                    <h5 class="modal-title h4">Cancel Loan Payment # <?php echo e($details->id_repayment); ?>

                        

                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <div class="form-row" style="margin-top:10px">
                            <div class="form-group col-md-12">
                                <label for="txt_cancel_reason">Reason</label>
                                <textarea class="form-control" rows="3" style="resize:none;" id="txt_cancel_reason"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="padding:5px;padding-left: 10px;">
                    <button class="btn bg-gradient-success">Save</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>

    </div>
</div>
<?php $__env->startPush('scripts'); ?>
<script type="text/javascript">

    $('#form_cancel_submit').submit(function(e){
        e.preventDefault();
        Swal.fire({
            title: 'Do you want to save this?',
            icon: 'warning',    
            showCancelButton: true,
            confirmButtonText: `Save`
        }).then((result) => {
            if (result.isConfirmed) {
                post_status()
            }
        })  
    })

    function show_status_modal(){
        $('#status_modal').modal('show');
    }
    function post_status(){
        $.ajax({
            type          :          "GET",
            url           :          "/repayment-bulk/updateStatus",
            data          :          {"id_repayment"  :   ID_REPAYMENT,
                                      "cancel_reason"    :   $('#txt_cancel_reason').val()},
            beforeSend    :          function(){
                                     show_loader()
            },
            success       :          function(response){
                                     console.log({response});
                                     hide_loader()
                                     if(response.RESPONSE_CODE == "SUCCESS"){
                                        Swal.fire({
                                            title: "Loan Payment Successfully Cancelled",
                                            text: '',
                                            icon: 'success',
                                            showConfirmButton : false,
                                            timer  : 1500
                                        }).then((result) => {
                                            location.reload();
                                        });                                       
                                     }else if(response.RESPONSE_CODE == "ERROR"){
                                        // $('#status_modal').modal('show');
                                        Swal.fire({
                                            title: response.message,
                                            text: '',
                                            icon: 'warning',
                                            showConfirmButton : false,
                                            timer  : 1500
                                        })
                                     }else if(response.RESPONSE_CODE == "CREDENTIAL_ERROR"){
                                        Swal.fire({
                                            title: response.message,
                                            text: '',
                                            icon: 'warning',
                                            showConfirmButton : false,
                                            timer : 1500
                                        }); 
                                    }
            },error: function(xhr, status, error) {
                hide_loader()
                var errorMessage = xhr.status + ': ' + xhr.statusText
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
<?php $__env->stopPush(); ?><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/repayment-bulk/status_modal.blade.php ENDPATH**/ ?>