@push('scripts')
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
                    <h5 class="modal-title h4">Print OR Investment ID# {{$investment_app_details->id_investment}} </h5>    
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="sel_status">OR No.</label>
                                <input type="text" class="form-control" id="txt_or_no" value="{{$investment_app_details->or_no ?? ''}}" required>
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
<script type="text/javascript">
	let $or_opcode = 0; 

	$(document).ready(function(){
		if(localStorage.getItem("auto_trigger_or") == 1){
			print_or();
			localStorage.removeItem("auto_trigger_or");
		}
	})

	function print_or(){
        $.ajax({
            type       :       'GET',
            url        :       '/investment/check_or',
            data       :       {'id_investment'  : ID_INVESTMENT},
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
                        $('#print_frame_or').attr('src','/cash_receipt/print?reference='+response.id_cash_receipt)
                    }, 1000); 
                }
            },error: function(xhr, status, error){
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
            title: 'Do you want to save this?',
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
            url           :         '/investment/post_or',
            data       :       		{'id_investment'  : ID_INVESTMENT,
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

    					localStorage.setItem("auto_trigger_or",1);
                    	location.reload();
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