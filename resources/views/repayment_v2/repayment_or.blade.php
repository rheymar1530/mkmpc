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

<div class="modal fade" id="or_modal" tabindex="-1" role="dialog" aria-labelledby="booking" aria-hidden="true">
    <div class="modal-dialog" >
        <div class="modal-content">
            <form id="frm_submit_or">
                <div class="modal-header" style="padding:5px;padding-left: 10px;">
                    <h5 class="modal-title h4">Print OR Loan Payment ID# {{$repayment_transaction->id_repayment_transaction}} </h5>    
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="sel_status">OR No.</label>
                                <input type="text" class="form-control" id="txt_or_no" value="{{$repayment_transaction->or_no ?? ''}}" required>
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
@push('scripts')
<script type="text/javascript">
    let $or_opcode = 0; 
    function print_or(){
        $.ajax({
            type       :       'GET',
            url        :       '/repayment/check_or',
            data       :       {'repayment_token'  : '<?php echo $repayment_transaction->repayment_token?>'},
            beforeSend :       function(){
                                show_loader();
            },
            success    :       function(response){
                hide_loader();
                console.log({response});
                if(response.RESPONSE_CODE == "SHOW_OR_ENTRY"){
                    $or_opcode = 0;
                    $('#or_modal').modal('show')
                }else if(response.RESPONSE_CODE == "SHOW_PRINT"){


                    setTimeout(function(){
                        $('#print_frame').attr('src','/repayment/print_or/'+'<?php echo $repayment_transaction->id_repayment_transaction?>')
                                        // print_page();
                    }, 1000); 
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
    $('#frm_submit_or').submit(function(e){
        e.preventDefault();
        Swal.fire({
            title: 'Do you want to update the OR of this repayment transaction?',
            icon: 'warning',
            showDenyButton: false,
            showCancelButton: true,
            confirmButtonText: `Save`,
        }).then((result) => {
            if (result.isConfirmed) {
                post_or();
            } 
        })  
        
    })
    function post_or(){
        $.ajax({
            type          :         'POST',
            url           :         '/repayment/post_or',
            data          :         {'repayment_token'  : '<?php echo $repayment_transaction->repayment_token?>',
                                     'or_no' : $('#txt_or_no').val(),
                                     'or_opcode' : $or_opcode},
            beforeSend :       function(){
                                show_loader();
            },
            success    :       function(response){
                hide_loader();
                console.log({response})
                if(response.RESPONSE_CODE == "ERROR_POST"){
                    Swal.fire({
                        title: response.message,
                        text: '',
                        icon: 'warning',
                        showCancelButton : false,
                        showConfirmButton : false,
                        timer : 2500
                    })
                }else if(response.RESPONSE_CODE == "SUCCESS"){
                    $('#or_modal').modal('hide')
                    Swal.fire({
                        title: "OR Successfully Posted",
                        text: '',
                        icon: 'success',
                        showCancelButton : false,
                        showConfirmButton : false,
                        timer : 2500
                    }).then(function(){
                        $('#or_text_holder').html('<span>OR No. '+$('#txt_or_no').val()+'</span>');
                        $('#print_frame').attr('src','/repayment/print_or/'+'<?php echo $repayment_transaction->id_repayment_transaction?>');
                        $('#btn_save_repayment').remove();
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
@endpush