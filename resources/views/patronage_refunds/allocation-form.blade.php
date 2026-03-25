@extends('adminLTE.admin_template')
@section('content')
<style>
    .table-allocation th,
    td {
        font-size: 0.9rem;

    }

    .table-allocation th {
        vertical-align: middle !important;
        padding: 3px;
    }

    .table-allocation td {
        padding: 3px;
    }

    .footer td {
        font-weight: bold;
        text-align: right;
    }

    .b {
        font-weight: bold;
    }

</style>
<div class="row">
    <!-- <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <input type="text" class="form-control" id="txt_search" placeholder="Search...">
                <div class="mt-2" style="max-height: calc(100vh - 50px);overflow-y: auto;">
                    <table class="table table-bordered">
@foreach($Groups as $g)
                        <tr>
                            <td>{{ $g->groupings }}</td>
                        </tr>
@endforeach
                    </table>
                </div>
            </div>
        </div>
    </div> -->
    <div class="col-md-12">
        <?php $back_link = (request()->get('href') == '')?'/patronage-refund':request()->get('href'); ?>
        <a class="btn bg-gradient-secondary btn-sm" href="{{ $back_link }}" style="margin-bottom:10px"><i
                class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Allocation List</a>
        <div class="card">
            <div class="card-body mx-4">
                <div class="text-center">
                    <h5 class="head_lbl">Patronage Refund and Capital Share Allocation</h5>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <div class="card c-border">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-6 col-12">
                                        <div class="d-flex flex-column">

                                            <span class="text-md  font-weight-bold lbl_color">Year: <span
                                                    class="ms-sm-2 font-weight-normal ml-2">{{ $details->year }}</span></span>
                                            <span class="text-md  font-weight-bold lbl_color">Interest on Capital Share
                                                Payable: <span class="ms-sm-2 font-weight-normal ml-2">
                                                    {{ number_format($details->capital_share_p,2) }} <i>(Interest
                                                        Rate @ {{ round($details->capital_share_rate*100,2) }}
                                                        %)</i></span></span>
                                            <span class="text-md  font-weight-bold lbl_color">Patronage Refund Payables:
                                                <span class="ms-sm-2 font-weight-normal ml-2">
                                                    {{ number_format($details->patronage_refund_p,2) }} <i>(Interest
                                                        Rate @ {{ round($details->patronage_refund_rate*100,2) }}
                                                        %)</i></span></span>

                                            <span class="text-md  font-weight-bold lbl_color">Remarks: <span
                                                    class="ms-sm-2 font-weight-normal ml-2">
                                                    {{ $details->remarks }}</span></span>


                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button class="btn btn-sm bg-gradient-success float-right"
                                    onclick="print_page('/patronage-refund/print/{{ $details->id_patronage_capital_allocation }}')">Print
                                    Allocation</button>
                                 <button class="btn btn-sm bg-gradient-danger float-right mr-3" id="btnAllocationStatus">Allocation Status</button>


                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="form-group col-md-3">
                        <label class="lbl_color mb-2">Brgy/LGU/Regular</label>
                        <select class="form-control form-control-border" id="sel-groupings">
                            @foreach($Groups as $g)
                                <option value="{{ $g->group_ref }}">{{ $g->groupings }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>


                <table class="table table-bordered table-allocation table-head-fixed mt-3">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Member</th>
                            <th>TSM</th>
                            <th>ASM</th>
                            <th>ISC</th>
                            <th>INTEREST ON LOAN</th>
                            <th>PR</th>
                            <th>TOTAL</th>
                            <th>NET</th>
                            <th>CBU</th>
                            <th></th>


                        </tr>
                    </thead>
                    <tbody id="body-allocation">

                    </tbody>

                    <tfoot id="footer-allocation">

                    </tfoot>
                </table>
            </div>
            <div class="card-footer">
                <!-- <div class="dropdown float-right ">
                    <a class="btn btn-primary dropdown-toggle btn-md" href="#" role="button" data-toggle="dropdown"
                        aria-expanded="false">
                        Update Status
                    </a>

                    <div class="dropdown-menu">
                        <a class="dropdown-item" onclick="ConfirmAllocation()">Confirm</a>
                        <a class="dropdown-item" onclick="CancelAllocation()">Cancel</a>

                    </div>
                </div> -->
                <button class="btn btn-md float-right bg-gradient-success" id="btn-post">Allocation Released</button>
            </div>
        </div>
    </div>
</div>
@include('global.print_modal')
@include('patronage_refunds.confirm-allocation')
@include('patronage_refunds.group-modal')
@endsection

@push('scripts')
    <script type="text/javascript">
        const ID_PATRONAGE_CAPITAL_ALLOCATION = '{{ $details->id_patronage_capital_allocation ?? 0 }}';
        const AllocationTable = @json($AllocationTable);
        $(document).ready(function () {
            DrawTable(AllocationTable);
        })
        $(document).on('change', '#sel-groupings', function () {
            let type = $(this).val();
            fetchAllocations(type);
        });

        const fetchAllocations = (type) => {
            $.ajax({
                type: 'GET',
                url: '/patronage-refund/fetch-member-allocations',
                beforeSend: function () {
                    show_loader();
                },
                data: {
                    'id_patronage_capital_allocation': ID_PATRONAGE_CAPITAL_ALLOCATION,
                    'type': type
                },
                success: function (response) {
                    hide_loader();
                    DrawTable(response.allocations);
                    console.log({
                        response
                    });
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
            })
        }

        const DrawTable = (allocations) => {
            $('#body-allocation').html('');

            // Initialize totals
            let total_capital_share = 0;
            let total_ave_monthly_cbu = 0;
            let total_interest_capital_share = 0;
            let total_loan_interest = 0;
            let total_patronage_refund = 0;
            let total_total = 0;
            let total_w_cash = 0;
            let total_w_cbu = 0;

            let out = '';


            $.each(allocations, function (i, item) {
                total_capital_share += parseFloat(item.capital_share) || 0;
                total_ave_monthly_cbu += parseFloat(item.ave_monthly_cbu) || 0;
                total_interest_capital_share += parseFloat(item.interest_capital_share) || 0;
                total_loan_interest += parseFloat(item.loan_interest) || 0;
                total_patronage_refund += parseFloat(item.patronage_refund) || 0;
                total_total += parseFloat(item.total) || 0;
                total_w_cash += parseFloat(item.w_cash) || 0;
                total_w_cbu += parseFloat(item.w_cbu) || 0;

                out += `<tr class="row-member" data-id-member="${item.id_member}" data-jv="${item.id_journal_voucher}">`;
                out += `<td class="text-center">${i+1}</td>`;
                out += `<td>${item.Name}</td>`;
                out += `<td class="text-right">${number_format(item.capital_share,2)}</td>`;
                out += `<td class="text-right">${number_format(item.ave_monthly_cbu,2)}</td>`;
                out += `<td class="text-right b">${number_format(item.interest_capital_share,2)}</td>`;
                out += `<td class="text-right">${number_format(item.loan_interest,2)}</td>`;
                out += `<td class="text-right b">${number_format(item.patronage_refund,2)}</td>`;
                out += `<td class="text-right b">${number_format(item.total,2)}</td>`;
                out +=
                    `<td class="text-right p-0"><input type="text" class="form-control w-100 class_amount text-right text-cash ff" name="cash" value="${number_format(item.w_cash,2)}"></td>`;
                out +=
                    `<td class="text-right p-0"><input type="text" class="form-control w-100 class_amount text-right text-cbu ff" name="cbu" value="${number_format(item.w_cbu,2)}"></td>`;
                if(item.id_journal_voucher > 0){
                    out += `<td><a class="btn btn-xs bg-gradient-primary btn-print-jv">JV# ${item.id_journal_voucher}</a></td>`;
                }

                out += `</tr>`;
            });
            $('#body-allocation').html(out);
            let grand = '';
            grand += `<tr class="font-weight-bold bg-light">`;
            grand += `<td></td>`;
            grand += `<td class="text-right">GRAND TOTAL</td>`;
            grand += `<td class="text-right">${number_format(total_capital_share,2)}</td>`;
            grand += `<td class="text-right">${number_format(total_ave_monthly_cbu,2)}</td>`;
            grand += `<td class="text-right">${number_format(total_interest_capital_share,2)}</td>`;
            grand += `<td class="text-right">${number_format(total_loan_interest,2)}</td>`;
            grand += `<td class="text-right">${number_format(total_patronage_refund,2)}</td>`;
            grand += `<td class="text-right">${number_format(total_total,2)}</td>`;
            grand += `<td class="text-right pr-3" id="total-cash">${number_format(total_w_cash,2)}</td>`;
            grand += `<td class="text-right pr-3" id="total-cbu">${number_format(total_w_cbu,2)}</td>`;
            grand += `<td></td>`;

            grand += `</tr>`;

            $('#footer-allocation').html(grand);
        }

        function recomputeTotals() {

            let totalCash = 0;
            let totalCbu = 0;


            $('.text-cash').each(function () {
                let val = $(this).val().replace(/,/g, '');
                totalCash += parseFloat(val) || 0;
            });


            $('.text-cbu').each(function () {
                let val = $(this).val().replace(/,/g, '');
                totalCbu += parseFloat(val) || 0;
            });

            console.log({
                totalCash,
                totalCbu
            })

            $('#total-cash').text(number_format(totalCash, 2));
            $('#total-cbu').text(number_format(totalCbu, 2));
        }

        $(document).on('blur', '.ff', function () {
            recomputeTotals();
        });

        $('#btn-post').on('click', () => {
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
                'id_baranggay_lgu' : $('#sel-groupings').val()
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

        $(document).on('click','.btn-print-jv',function(){
            let id_journal_voucher = $(this).closest('tr.row-member').attr('data-jv');
            print_page(`/journal_voucher/print/${id_journal_voucher}`);
        })
    </script>
@endpush
