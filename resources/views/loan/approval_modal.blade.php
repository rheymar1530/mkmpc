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
<div class="modal fade" id="approval_modal" tabindex="-1" role="dialog" aria-labelledby="booking" aria-hidden="true">
    <div class="modal-dialog" >
        <div class="modal-content">
            <form id="frm_loan_approval">
                <div class="modal-header" style="padding:5px;padding-left: 10px;">
                    <h5 class="modal-title h4">Update Loan Application ID# {{$service_details->id_loan ?? $loan_service->id_loan}} 
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
                                    @foreach($loan_status as $val=>$status)
                                    <option value="{{$val}}">{{$status}}</option>
                                    @endforeach
                                </select>        
                            </div>
                        </div>

                        @if(isset($for_releasing) && $for_releasing)
                        <div class="form-row" id="sel-release-form">
                            <div class="form-group col-md-12">
                                <label for="sel_status">Date</label>
                                <input type="date" class="form-control" id="txt_date_released" value="{{$current_date}}">
                            </div>
                            <div class="form-group col-md-12">
                                <label for="sel_bank">Bank</label>
                                <select class="form-control" id="sel_bank">
                                    <option value="0">**CASH ON HAND**</option>
                                    @foreach($banks as $bank)
                                    <option value="{{$bank->id_bank}}" <?php echo ($bank->id_bank == config('variables.default_bank'))?"selected":""; ?>>{{$bank->bank_name}}</option>
                                    @endforeach
                                </select>
                                
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
var div_reason = `  <div class="form-row" style="margin-top:10px">
                        <div class="form-group col-md-12">
                            <label for="txt_cancel_reason">Reason</label>
                            <textarea class="form-control" rows="3" style="resize:none;" id="txt_cancel_reason" required></textarea>            
                        </div>
                    </div> `;
const status_d = $('#sel-release-form').html();
init_status_in();

$(document).on('change','#sel_status',function(){
    init_status_in();
})

function init_status_in(){
      var val = $('#sel_status').val();
    if(val == 5){
        $('#div_reason_cancel').html(div_reason);
        $('#sel-release-form').html('');
    }else{
        $('#div_reason_cancel').html('');
        $('#sel-release-form').html(status_d)
    }  
}
</script>
@endpush