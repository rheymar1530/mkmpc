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
if($confirm_close_request ?? false){
    $STATUS_LISTS = array(
        1 => "Confirm",
        10 => "Cancel"
    );
}


?>
<div class="modal fade" id="approval_modal" tabindex="-1" role="dialog" aria-labelledby="booking" aria-hidden="true">
    <div class="modal-dialog" >
        <div class="modal-content">
            <form id="frm_status_update">
                <div class="modal-header" style="padding:5px;padding-left: 10px;">
                    <h5 class="modal-title h4">Update Investment Status <small>({{$investment_app_details->id_investment}})</small>
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
                        @if(isset($SHOW_INVESTMENT_DATE) && $SHOW_INVESTMENT_DATE)
                        <div class="form-row" id="frm_inv_date">
                            <div class="form-group col-md-12">
                                <label for="sel_status">Date</label>
                                <input type="date" class="form-control" id="txt_investment_date" value="{{MySession::current_date()}}">
                            </div>
                        </div> 
                        @endif
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


    const STATUS_MODE = '<?php echo $STATUS_MODE ?>';
    var div_reason = `  <div class="form-row" style="margin-top:10px">
    <div class="form-group col-md-12">
    <label for="txt_cancel_reason">Reason</label>
    <textarea class="form-control" rows="3" style="resize:none;" id="txt_cancel_reason" required></textarea>            
    </div>
    </div> `;
    const div_investment_date = $('#frm_inv_date').detach();

    init_status_in();
    $(document).on('change','#sel_status',function(){
        init_status_in();
    })

    function show_status_modal(){
        $('#approval_modal').modal('show');
    }

    function init_status_in(){
      var val = $('#sel_status').val();
      if(val >= 4){
        $('#div_reason_cancel').html(div_reason);
    }else if(val == 2){
        $('#div_reason_cancel').html(div_investment_date);
    }else{
        $('#div_reason_cancel').html('');
    }  
}

$('#frm_status_update').submit(function(e){
    e.preventDefault();

    Swal.fire({
        title: 'Do you want to save this?',
        icon: 'warning',
        showDenyButton: false,
        showCancelButton: true,
        confirmButtonText: `Save`,
    }).then((result) => {
        if (result.isConfirmed) {
            post();
        } 
    });
})

function post(){
 $data = {
    'id_investment' : ID_INVESTMENT,
    'status' : $('#sel_status').val(),
    'status_mode' : STATUS_MODE,
    'reason' :  $('#txt_cancel_reason').val(),
    'investment_date' : $('#txt_investment_date').val()
};
$.ajax({
    type      :      'GET',
    url       :      '/investment/update_status',
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
            timer : 2000
        }).then((result) => {
            

            if(response.is_released){
                localStorage.setItem("show_print_ask",1);
            }
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

