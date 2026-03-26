<div class="modal fade" id="post-allocate-modal" tabindex="-1" role="dialog" aria-labelledby="booking" aria-hidden="true">
    <div class="modal-dialog modal-conf" >
        <div class="modal-content">
            <form id="frm-allocation-released">
                <div class="modal-header" style="padding:5px;padding-left: 10px;">
                    <h5 class="modal-title h4">Release Allocation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <div class="form-row" style="margin-top:10px">
                            <div class="form-group col-md-12">
                                <label for="txt-date-released">Date Released</label>
                                <input type="date" class="form-control form-control-border" id="txt-date-released" value="{{MySession::current_date()}}">
                            </div>
                            <div class="form-group col-md-12">
                                <label for="txt_cancel_reason">Remarks</label>
                                <textarea class="form-control" rows="3" style="resize:none;" id="txt_remarks_allocate"></textarea>
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

    function showPostAllocateModal(){
        $('#post-allocate-modal').modal('show');
    }

    $('#btn-post-allocation').click(function(){
        showPostModal();
    });

    $('#frm-allocation-released').submit(function(e){
        e.preventDefault();
        Swal.fire({
            title: 'Do you want to save this?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: `Save`
        }).then((result) => {
            if (result.isConfirmed) {
                postAllocation();
            }
        })
    });

    const postAllocation = () =>{
        const allocation = $('.row-member').map(function () {
            const temp = {};
            temp['id_member'] = $(this).attr('data-id-member');
            $(this).find('.ff').each(function () {
                temp[this.name] = decode_number_format(this.value);
            });
            return temp;
        }).get();

        const ajaxParam = {
            'allocations': allocation,
            'id_patronage_capital_allocation': ID_PATRONAGE_CAPITAL_ALLOCATION,
            'id_baranggay_lgu' : $('#sel-groupings').val(),
            'release_remarks' : $('#txt_remarks_allocate').val(),
            'date_released' : $('#txt-date-released').val()
        };

        $.ajax({
            type: 'POST',
            url: '/patronage-refund/post-allocation',
            data: ajaxParam,
            beforeSend: function () {
                $('.table-danger').removeClass('table-danger');
                show_loader();
            },
            success: function (response) {
                console.log({
                    response
                });
                hide_loader();
                if (response.RESPONSE_CODE == "SUCCESS") {
                    Swal.fire({
                        title: response.message,
                        text: '',
                        icon: 'success',
                        showCancelButton: false,
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {

                    });
                    $('#sel-groupings').val($('#sel-groupings').val()).trigger('change');
                    $('#post-allocate-modal').modal('hide');
                } else if (response.RESPONSE_CODE == "ERROR") {
                    const InvalidRows = response.invalidRows;
                    Swal.fire({
                        title: response.message,
                        text: '',
                        icon: 'warning',
                        showCancelButton: false,
                        showConfirmButton: false,
                        timer: 2500
                    }).then(() => {
                        $.each(InvalidRows, function (i, id) {
                            console.log({
                                id
                            })
                            $(`tr.row-member[data-id-member="${id}"]`).addClass(
                                'table-danger');
                        })
                    });
                }
            },
            error: function (xhr, status, error) {
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

        console.log({
            ajaxParam
        });
    }

</script>
@endpush