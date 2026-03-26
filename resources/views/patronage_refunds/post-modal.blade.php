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

<div class="modal fade" id="post-modal" tabindex="-1" role="dialog" aria-labelledby="booking" aria-hidden="true">
    <div class="modal-dialog modal-conf" >
        <div class="modal-content">
            <form id="frm-post-allocation">
                <div class="modal-header" style="padding:5px;padding-left: 10px;">
                    <h5 class="modal-title h4">Save


                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <div class="form-row" style="margin-top:10px">
                            <div class="form-group col-md-12">
                                <label for="txt_cancel_reason">Remarks</label>
                                <textarea class="form-control" rows="3" style="resize:none;" id="txt_remarks"></textarea>

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
    const YEAR = '{{$sel_year}}';
    const ICSP = '{{$icsp}}';
    const PRP = '{{$prp}}';

    $('#frm-post-allocation').submit(function(e){
        e.preventDefault();

        Swal.fire({
            title: 'Do you want to save this?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: `Save`
        }).then((result) => {
            if (result.isConfirmed) {
                postAllocation()
            }
        })
    })

    function showPostModal(){

        $('#post-modal').modal('show');
    }



    $('#btn-post-allocation').click(function(){
        showPostModal();
    });


    const postAllocation = ()=>{
        const ajaxParam = {
            'year' : YEAR,
            'icsp' : ICSP,
            'prp'  : PRP,
            'remarks' : $('#txt_remarks').val(),
            'default_allocation' : $('#sel-def-allocation').val()
        };


        $.ajax({
            type       :    'POST',
            url        :    '/patronage-refund/post',
            data       :    ajaxParam,
            beforeSend  :   function(){
                            show_loader();
            },
            success    :    function(response){
                            console.log({response});
                            hide_loader();
                            if(response.RESPONSE_CODE == "SUCCESS"){
                                Swal.fire({
                                    title: response.message,
                                    text: '',
                                    icon: 'success',
                                    showCancelButton : false,
                                    showConfirmButton : false,
                                    timer : 2000
                                }).then(()=>{
                                    window.location = `/patronage-refund/allocate/${response.id_patronage_capital_allocation}`;
                                });
                            }else if(response.RESPONSE_CODE == "ERROR"){
                                const InvalidRows = response.invalidRows;
                                Swal.fire({
                                    title: response.message,
                                    text: '',
                                    icon: 'warning',
                                    showCancelButton : false,
                                    showConfirmButton : false,
                                    timer : 2500
                                }).then(()=>{

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
        });
    }
</script>
@endpush