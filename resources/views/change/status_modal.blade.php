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
    $change_status = [
        10=>"Cancel"
    ];
?>
<div class="modal fade" id="status_modal" tabindex="-1" role="dialog" aria-labelledby="booking" aria-hidden="true">
    <div class="modal-dialog" >
        <div class="modal-content">
            <form id="frm_change_status">
                <div class="modal-header" style="padding:5px;padding-left: 10px;">
                    <h5 class="modal-title h4">Update Released Change ID# {{$repayment_change->id_repayment_change}} 
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
                                <label for="sel_status">Status</label>
                                <select class="form-control p-0" id="sel_status">
                                    @foreach($change_status as $val=>$status)
                                    <option value="{{$val}}">{{$status}}</option>
                                    @endforeach
                                </select>        
                            </div>
                        </div>


                        <div id="div_reason_cancel">
                           <div class="form-row" style="margin-top:10px">
                        <div class="form-group col-md-12">
                            <label for="txt_cancel_reason">Reason</label>
                            <textarea class="form-control" rows="3" style="resize:none;" id="txt_cancel_reason" required></textarea>            
                        </div>
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
var div_reason = `  <div class="form-row" style="margin-top:10px">
                        <div class="form-group col-md-12">
                            <label for="txt_cancel_reason">Reason</label>
                            <textarea class="form-control" rows="3" style="resize:none;" id="txt_cancel_reason" required></textarea>            
                        </div>
                    </div> `;
// $(document).on('change','#sel_status',function(){
//     var val = $(this).val();
//     if(val == 5){
//         $('#div_reason_cancel').html(div_reason);
//     }else{
//         $('#div_reason_cancel').html('');
//     }
// })
function cancel_change(){
    $('#status_modal').modal('show')
}

$('#frm_change_status').submit(function(e){
    e.preventDefault()
    Swal.fire({
        title: 'Do you want to save this?',
        icon: 'warning',
        showDenyButton: false,
        showCancelButton: true,
        confirmButtonText: `Save`,
    }).then((result) => {
        if (result.isConfirmed) {
            post_status($id_repayment_change);
        } 
    })  
    
})

function post_status(id_reference){

    $.ajax({
        type       :       'POST',
        url        :       '/change/post/status',
        data       :       {'id_repayment_change' : id_reference,
                            'status' : $('#sel_status').val(),
                           'cancellation_reason' : $('#txt_cancel_reason').val()},
        beforeSend    :          function(){
                                $('.mandatory').removeClass('mandatory');
                                 show_loader();
        },
        success    :       function(response){
                console.log({response});
                hide_loader()

                if(response.RESPONSE_CODE == "success"){
                    $('#status_modal').modal('hide')
                       var html_swal = '';
                        var link = "/change/view/"+$id_repayment_change+"?href="+'<?php echo $back_link;?>';

                        if($opcode == 1){
                            html_swal = "<a href='"+link+"'>Change ID# "+$id_repayment_change+"</a>";
                        }
                        Swal.fire({
                            
                            title: "Change Successfully Cancelled",
                            html : html_swal,
                            text: '',

                            icon: 'success',
                            showCancelButton : true,
                           
                            cancelButtonText: 'Back to List of Released Change',
                            showConfirmButton : false,     
                            allowEscapeKey : false,
                            allowOutsideClick: false
                        }).then((result) => {
                                if(!result.isConfirmed) {
                                    window.location = '<?php echo $back_link;?>';
                                   
                                }
                        }); 
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