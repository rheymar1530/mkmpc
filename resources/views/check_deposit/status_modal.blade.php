
<?php
      $status_list = array(
        // 1=>'Confirmed',
        10=>'Cancel'
    );  
?>

<div class="modal fade" id="status-modal" tabindex="-1" role="dialog" aria-labelledby="booking" aria-hidden="true">
    <div class="modal-dialog" >
        <div class="modal-content">
            <form id="frm-post-status">
                <div class="modal-header" style="padding:5px;padding-left: 10px;">
                    <h5 class="modal-title h4 lbl_color">Update Status 
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
                                    @foreach($status_list as $val=>$status)
                                    <option value="{{$val}}">{{$status}}</option>
                                    @endforeach
                                </select>        
                            </div>
                        </div>
                        <div id="div_reason_cancel"></div>
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

</script>

<script type="text/javascript">
    var div_reason = `  <div class="form-row" style="margin-top:10px">
    <div class="form-group col-md-12">
    <label for="txt_cancel_reason">Reason</label>
    <textarea class="form-control" rows="3" style="resize:none;" id="txt_cancel_reason" required></textarea>            
    </div>
    </div> `;

    let date_div = '';

    // php
    // if($details->status == 1){
    //     echo " date_div = $('#div_date_holder').detach(); ";
    // }

    

    init_status_in();
    $(document).on('change','#sel_status',function(){
        init_status_in();
    })

    function init_status_in(){
      var val = $('#sel_status').val();
      if(val == 10 || val == 5){
        $('#div_reason_cancel').html(div_reason);
    }else if(val == 2){
        $('#div_reason_cancel').html(date_div);
    }else{
        $('#div_reason_cancel').html('');
    } 
}

function show_status_modal(){
    $('#status-modal').modal('show')
}

$('#frm-post-status').submit(function(e){
    e.preventDefault();


    Swal.fire({
        title: 'Do you want to save this?',
        icon: 'warning',
        showDenyButton: false,
        showCancelButton: true,
        confirmButtonText: `Save`,
    }).then((result) => {
        if (result.isConfirmed) {
            post_status();
        } 
    })  
});

function post_status(){
    $.ajax({
        type       :      'GET',
        url        :       '/check-deposit/postStatus',
        data       :       {'id_check_deposit' : ID_CHECK_DEPOSIT,
                            'status' : $('#sel_status').val(),
                            'reason' : $('#txt_cancel_reason').val(),
                            'date' : $('#txt_date_released').val()},
        beforeSend :       function(){
                            show_loader();
        },
        success     :      function(response){
                            hide_loader();
                            console.log({response});
                            if(response.RESPONSE_CODE == "SUCCESS"){
                                Swal.fire({
                                    title: "Status successfully updated ",
                                    text: '',
                                    icon: 'success',
                                    showCancelButton : false,
                                    showConfirmButton : false,
                                    timer : 2000
                                }).then((result) => {   
                                    location.reload();
                                });;                               
                            }else if(response.RESPONSE_CODE == "ERROR"){
                                Swal.fire({
                                    title: response.message,
                                    text: '',
                                    icon: 'warning',
                                    showCancelButton : false,
                                    showConfirmButton : false,
                                    timer : 2000
                                })
                            }
        },error: function(xhr, status, error){
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

    });
}
</script>
@endpush