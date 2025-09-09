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
$STATUS_LISTS = [
    1=>'Released',
    10=>'Cancel'
];
?>
<div class="modal fade" id="approval_modal" tabindex="-1" role="dialog" aria-labelledby="booking" aria-hidden="true">
    <div class="modal-dialog" >
        <div class="modal-content">
            <form id="frm_status_update_batch">
                <div class="modal-header" style="padding:5px;padding-left: 10px;">
                    <h5 class="modal-title h4">Update Status
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
                                    @foreach($STATUS_LISTS as $code=>$st)
                                    <option value="{{$code}}">{{$st}}</option>
                                    @endforeach
                                </select>        
                            </div>
                        </div>
                        
                        <div class="form-row" id="frm_inv_date">
                            <div class="form-group col-md-12">
                                <label for="sel_status">Date</label>
                                <input type="date" class="form-control" id="txt_date_released" value="{{MySession::current_date()}}">
                            </div>
                        </div> 
                        <div id="div_reason_cancel">

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
    let ID_INVESTMENT_WITHDRAWAL_IN = 0;

    var div_reason = `  <div class="form-row" style="margin-top:10px">
    <div class="form-group col-md-12">
    <label for="txt_cancel_reason2">Reason</label>
    <textarea class="form-control" rows="3" style="resize:none;" id="txt_cancel_reason2" required></textarea>            
    </div>
    </div> `;
    const div_date_released = $('#frm_inv_date').detach();


    init_status_in();
    $(document).on('change','#sel_status',function(){
        init_status_in();
    })

    function show_status_modal(id_inv){
        ID_INVESTMENT_WITHDRAWAL_IN = id_inv;
        $('.spn_id_inv_with').text(ID_INVESTMENT_WITHDRAWAL_IN);
        $('#approval_modal').modal('show');
    }

    function init_status_in(){
      var val = $('#sel_status').val();
      if(val == 1){
        $('#div_reason_cancel').html(div_date_released);
    }else{
        $('#div_reason_cancel').html(div_reason);
    }
}

$('#frm_status_update_batch').submit(function(e){
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
    });
})

function post_status(){

   $data = {
    'id_investment_withdrawal_batch' : ID_INVESTMENT_WITHDRAWAL_IN,
    'status' : $('#sel_status').val(),
    'reason' :  $('#txt_cancel_reason2').val(),
    'date_released' : $('#txt_date_released').val()
};
$.ajax({
    type      :      'POST',
    url       :      '/investment-withdrawal/update_status',
    data      :      $data,
    beforeSend :     function(){
       show_loader();
   },
   success   :      function(response){
       hide_loader();
       console.log({response});
       if(response.RESPONSE_CODE == "SUCCESS"){
        Swal.fire({
            title: "Investment Withdrawal Status Successfully Updated",
            text: '',
            icon: 'success',
            showConfirmButton : false,
            timer : 2000
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