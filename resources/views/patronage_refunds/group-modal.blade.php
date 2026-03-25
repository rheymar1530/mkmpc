<div class="modal fade" id="groupModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Group Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
            </div>

            <div class="modal-body">
                <table class="table table-bordered table-head-fixed">
                    <thead>
                        <tr>
                            <th>Group</th>
                            <th>Status</th>
                            <th></th>
                            <!-- <th>CBU</th>
                            <th>Net</th> -->
                        </tr>
                    </thead>
                    <tbody id="groupTableBody">
                        <!-- AJAX content goes here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script type="text/javascript">



    $('#btnAllocationStatus').click(function () {

        $.ajax({
            url: '/patronage-refund/group-status',
            type: 'GET',
            data: {'id_patronage_capital_allocation' : ID_PATRONAGE_CAPITAL_ALLOCATION},
            success: function (response) {

                let rows = '';

                response.forEach(item => {

                    let badgeClass = item.status_code == 1 ? 'bg-success' : 'bg-primary';

                    // let cbu = item.status_code == 1 ? item.cbu : '-';
                    // let net = item.status_code == 1 ? item.net : '-';

                    rows += `
                        <tr class="row-group" data-id="${item.id_baranggay_lgu}">
                            <td>${item.groupings}</td>
                            <td>
                                <span class="badge ${badgeClass}">
                                    ${item.status_description}
                                </span>
                            </td>
                             <td><a class="btn btn-xs bg-gradient-info" onclick="setFilter(${item.id_baranggay_lgu})"><i class="fa fa-eye"></i>&nbsp;View</a></td>
                        </tr>
                    `;
                });
                // <td>${cbu}</td>
                // <td>${net}</td>

                $('#groupTableBody').html(rows);

                // OPEN MODAL AFTER DATA LOAD

                $('#groupModal').modal('show');

            },
            error: function () {
                alert('Failed to load data.');
            }
        });

    });


const setFilter = (id) =>{
    $('#sel-groupings').val(id).trigger('change');
    $('#groupModal').modal('hide');
}
</script>
@endpush