<div class="modal fade" id="confirm-modal" tabindex="-1" role="dialog" aria-labelledby="booking" aria-hidden="true">
    <div class="modal-dialog modal-conf">
        <div class="modal-content">
            <form id="frm-post-allocation">
                <div class="modal-header" style="padding:5px;padding-left: 10px;">
                    <h5 class="modal-title h4">Confirm Allocation


                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
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
        const ConfirmAllocation = () => {

            $.ajax({
                type: 'GET',
                url: '/patronage-refund/get-allocation-summary',
                data: {
                    'ID_PATRONAGE_CAPITAL_ALLOCATION': ID_PATRONAGE_CAPITAL_ALLOCATION
                },
                success: function (response) {
                    console.log({
                        response
                    });

                }
            })
            $('#confirm-modal').modal('show');
        }

    </script>
@endpush
