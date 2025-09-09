<style type="text/css">
  #sel_status{
    padding-bottom: 0px !important;
  }
</style>
<div class="modal fade bd-example-modal-xl" id="modal_status">
  <div class="modal-dialog modal-conf">
    <div class="modal-content">
      <div class="modal-header" style="padding: 5px !important">
        <h5 class="modal-title"> SOA Status</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="frm_submit_status">
        <div class="modal-body">        
          <div class="form-group">
            <div class="form-group col-md-12">
              <label for="sel_status">Status</label>
              <select class="form-control" id="sel_status">
                <!-- <option value="1">Confirm</option> -->
                <option value="1">Sent</option>
                @if($credential->is_delete)
                  <option value="10">Cancel</option>
                @endif
              </select>
            </div>
            <div id="div_frm">
              <div class="form-group col-md-12" id="div_reason">
                <label for="txt_reason">Reason</label>
                <textarea class="form-control" rows="4" id="txt_reason" required=""></textarea>
              </div>
            </div>
        </div>
        </div>
        <div class="modal-footer">
          <div class="float-right">
            <button type="submit" class="btn btn-primary">Save changes</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
@push('scripts')
  <script type="text/javascript">
    var $div_reason = $('#div_reason').detach();
    set_div_frm($('#sel_status').val())
    $('#sel_status').change(function(){
      set_div_frm($(this).val());
    })

    $('#frm_submit_status').submit(function(e){
      e.preventDefault();


      Swal.fire({
        title: 'Do you want to save this status update ?',
        icon: 'warning',
        showDenyButton: false,
        showCancelButton: true,
        confirmButtonColor: '#ff3333',
        confirmButtonText: `Save`,
      }).then((result) => {
          if (result.isConfirmed) {
              post_status();
          }
      })

      
    })
    function post_status(){
      var status = $('#sel_status').val();
      var reason = $('#txt_reason').val();
      var control_number = '{{$details->control_number}}';

      console.log({reason,status,control_number});

      $.ajax({
        type          :         'POST',
        url           :         '/admin/soa/update_status',
        data          :         {'status' : status,
                                 'reason' : reason,
                                 'control_number' : control_number},
        beforeSend    :         function(){
                                  show_loader();
        },
        success       :         function(response){
                                setTimeout(
                                function() {
                                    hide_loader();
                                    if(response.message == "success"){
                                        console.log({response});
                                        $('#modal_status').modal('hide');
                                        Swal.fire({
                                            position: 'center',
                                            icon: 'success',
                                            title: 'Status successfully updated ',
                                            showConfirmButton: false,
                                            timer: 1500
                                        }).then(function() {
                                          if(status == 10){
                                            location.reload();
                                          }else{
                                             generate_soa_attachment();
                                          } 
                                        })                                                                    
                                    }
                                }, 1500);                             
        }   
      })    
    }

    function set_div_frm(val){
      $('#div_frm').html('');
      switch(val){
        case '1':
          
        break;
        case '10': // Cancelled
         $('#div_frm').html($div_reason);
        break;
        default:
      }
    }
  </script>
@endpush