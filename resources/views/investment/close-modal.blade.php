<div class="modal fade" id="close-modal" tabindex="-1" role="dialog" aria-labelledby="booking" aria-hidden="true">
    <div class="modal-dialog" >
        <div class="modal-content">
            <form id="frm_close_investment">
                <div class="modal-header" style="padding:5px;padding-left: 10px;">
                    <h5 class="modal-title h4">Withdrawal Request (Full) <small>Investment ID# {{$investment_app_details->id_investment}}</small>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <h6 class="lbl_color mb-4">Withdrawable: {{number_format($w_full,2)}}</h6>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="txt_withdrawal_reason">Reason</label>
                                <textarea class="form-control" rows="3" style="resize:none;" id="txt_withdrawal_reason" required></textarea>            
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
@push('scripts')
<script type="text/javascript">

    function close_request(){
        $('#close-modal').modal('show');
    }
    $('#frm_close_investment').submit(function(e){
        e.preventDefault();

        Swal.fire({
            title: 'Do you want to save this?',
            icon: 'warning',
            showDenyButton: false,
            showCancelButton: true,
            confirmButtonText: `Save`,
        }).then((result) => {
            if (result.isConfirmed) {
                post_close_request();
            } 
        });
    })

    function post_close_request(){
        $data = {
            'id_investment' : ID_INVESTMENT,
            'reason' :  $('#txt_withdrawal_reason').val(),
        };
        $.ajax({
            type      :      'POST',
            url       :      '/investment/post-close-request',
            data      :      $data,
            beforeSend :     function(){
               show_loader();
           },
           success   :      function(response){
               hide_loader();
               console.log({response});


               if(response.RESPONSE_CODE == "SUCCESS"){
                Swal.fire({
                    title: response.message,
                    text: '',
                    icon: 'success',
                    showConfirmButton : false,
                    timer : 3000
                }).then((result) => {
                    location.reload();

                })
            }else if(response.RESPONSE_CODE == "ERROR"){
                Swal.fire({
                    title: response.message,
                    text: '',
                    icon: 'warning',
                    showCancelButton : false,
                    showConfirmButton : false,
                    timer : 2500
                });
            }

        },error: function(xhr, status, error) {
            hide_loader();
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

