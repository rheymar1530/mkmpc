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

<div class="modal fade" id="cancel_admin_modal" tabindex="-1" role="dialog" aria-labelledby="booking" aria-hidden="true">
    <div class="modal-dialog" >
        <div class="modal-content">
            <form id="frm_cancel_loan">
                <div class="modal-header" style="padding:5px;padding-left: 10px;">
                    <h5 class="modal-title h4">Cancel Loan Application ID# <?php echo e($service_details->id_loan ?? $loan_service->id_loan); ?> 


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
                                <textarea class="form-control" rows="3" style="resize:none;" id="txt_cancel_reason" required></textarea>            
                            </div>
                        </div>
                        

                    </div>
                </div>
                <div class="modal-footer" style="padding:5px;padding-left: 10px;">
                    <button class="btn bg-gradient-success2">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>

    </div>
</div>


<?php $__env->startPush('scripts'); ?>
<script type="text/javascript">
    function show_cancel_modal(){
        $('#cancel_admin_modal').modal('show');

    }
    function cancel_loan(){
        $('#approval_modal').modal('show');
        $('#div_reason_cancel').html(div_reason);
    }
    $('#frm_cancel_loan').submit(function(e){
        e.preventDefault();
        Swal.fire({
            title: 'Do you cancel this loan?',
            icon: 'warning',
            showDenyButton: false,
            showCancelButton: true,
            confirmButtonText: `Save`,
        }).then((result) => {
            if (result.isConfirmed) {
                // alert("POSTED");
                post_cancellation();
            } 
        })  
    })
    function post_cancellation(){
        $.ajax({
            type     :     'POST',
            url      :      '/loan/application/cancel',
            data      :     {'id_loan' : '<?php echo $service_details->id_loan ?? 0 ?>',
            'cancellation_reason'   : $('#txt_cancel_reason').val()},
            beforeSend :    function(){
                show_loader();
            },
            success   :     function(response){
                console.log({response});
                hide_loader();
                if(response.RESPONSE_CODE == "SUCCESS"){
                    Swal.fire({
                        title: "Loan Application Successfully Cancelled",
                        text: '',
                        icon: 'success',
                        showConfirmButton : false,
                        timer  : 2500
                    }).then((result) => {
                        window.location = '/loan/application/view/'+response.LOAN_TOKEN+"?href="+'<?php echo $back_link;?>';
                    });                     
                }else if(response.RESPONSE_CODE == "ERROR"){
                    Swal.fire({
                        title: response.message,
                        text: '',
                        icon: 'warning',
                        showConfirmButton : false,
                        timer  : 2500
                    })                    
                }
            },error: function(xhr, status, error) {
                hide_loader()
                var errorMessage = xhr.status + ': ' + xhr.statusText;
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
<?php $__env->stopPush(); ?><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/loan/status_admin_modal.blade.php ENDPATH**/ ?>