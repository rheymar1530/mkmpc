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
<?php
// $loan_status = [
//     1=>"Processing",
//     2=>"Approved, for Releasing",
//     5=>"Disapproved"
// ];
// if($privilegeID == 8){ // if Credit Committee
//     unset($loan_status[])
// }elseif($privilegeID == 9){

// }else{
//     $loan_status = array();
// }
?>
<div class="modal fade" id="cancel_modal" tabindex="-1" role="dialog" aria-labelledby="booking" aria-hidden="true">
    <div class="modal-dialog" >
        <div class="modal-content">
            <form id="frm_cancel_repayment">
                <div class="modal-header" style="padding:5px;padding-left: 10px;">
                    <h5 class="modal-title h4">Cancel Loan Payment ID# {{$repayment_transaction->id_repayment_transaction ?? ''}} 
                        <span id="spn_id_cash_receipt"></span>

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
                    <button class="btn bg-gradient-success">Save</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>

    </div>
</div>
@push('scripts')
<script type="text/javascript">
    function cancel_repayment(){

        $('#cancel_modal').modal('show')
    }
    $('#frm_cancel_repayment').submit(function(e){
        e.preventDefault();
        Swal.fire({
            title: 'Do you want to save this?',
            icon: 'warning',
            showDenyButton: false,
            showCancelButton: true,
            confirmButtonText: `Save`,
        }).then((result) => {
            if (result.isConfirmed) {
                post_cancel()
            } 
        })  
        

    })

    function post_cancel(){
        $.ajax({
            type       :          'POST',
            url        :          '/repayment/cancel',
            data       :          {'id_repayment_transaction'  : '{{$repayment_transaction->id_repayment_transaction}}',
                                    'cancel_reason' : $('#txt_cancel_reason').val()},
            beforeSend :          function(){
                                  show_loader();
            },
            success    :          function(response){
                                  hide_loader();
                                    if(response.RESPONSE_CODE == "SUCCESS"){
                                        $('#cancel_modal').modal('hide')
                                        var link = "/repayment/view/"+response.REPAYMENT_TOKEN+"?href="+encodeURIComponent('<?php echo $back_link;?>');
                                        Swal.fire({
                                            title: "Loan Payment Successfully Cancelled !",
                                            text: '',
                                            icon: 'success',
                                            showCancelButton : true,
                                            confirmButtonText: 'Loan Payment ID# '+'{{$repayment_transaction->id_repayment_transaction}}',
                                            showDenyButton: false,
                                            
                                            cancelButtonText: 'Close',
                                            showConfirmButton : true,     
                                            allowEscapeKey : false,
                                            allowOutsideClick: false
                                        }).then((result) => {
                                            if(result.isConfirmed) {
                                                window.location = link;
                                            }else{
                                                window.location = '<?php echo $back_link;?>';
                                            }
                                        });
                                    }else if(response.RESPONSE_CODE == "ERROR"){
                                        Swal.fire({
                                            title: response.message,
                                            text: '',
                                            icon: 'warning',
                                            showCancelButton : false,
                                            showConfirmButton : false,
                                            timer : 2500
                                        });
                                        return;
                                    }                                  
                                  console.log({response});
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