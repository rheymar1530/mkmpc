@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
    .main_form {
        font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
    }

    #tbl_patronage_refund tr>td {
        padding: 3px;
        vertical-align: top;
        font-family: Arial !important;
        font-size: 12px !important;
    }

    #tbl_patronage_refund tr::nth-child(0)>th {
        padding: 34px !important;
        vertical-align: top;
        font-family: Arial !important;
        font-size: 12px !important;
    }

    .col_search {
        padding: 1px 1px 1px 1px !important;
    }

    .dataTables_scrollHead table.dataTable th,
    table.dataTable tbody td {
        padding: 9px 10px 1px;
    }

    .head_search {
        height: 24px;
    }


    .form-label {
        margin-bottom: 4px !important;
    }

    .tbl_badge {
        font-size: 13px;
    }

    .class_amount {
        text-align: right;
        padding-right: 10px !important;
    }

    .dt-buttons {
        display: none;
    }

</style>
<div class="container-fluid main_form" style="margin-top:-20px">
    <h3>ISC & PR Allocation&nbsp;&nbsp; </h3>
    <div class="row">
        <div class="col-md-12">
            <table id="tbl_patronage_refund" class="table table-hover table-striped"
                style="white-space: nowrap;margin-top: 10px;">
                <thead>
                    <tr class="table_header_dblue">
                        <th>ID</th>
                        <th>Year</th>
                        <th>Capital Share Payable</th>
                        <th>Patronage Refund Payables</th>
                        <th>Date Released</th>
                        <th>Status</th>
                        <th>Date Created</th>
                    </tr>
                </thead>
                <tbody id="list_body">
                    @foreach($patronage_refunds as $list)
                        <tr class="patronage_refund_row" data-code="{{ $list->id_patronage_capital_allocation }}">
                            <td>{{ $list->id_patronage_capital_allocation }}</td>
                            <td>{{ $list->year }}</td>
                            <td class="text-right">{{ number_format($list->capital_share_p,2) }}</td>
                            <td class="text-right">{{ number_format($list->patronage_refund_p,2) }}</td>
                            <td>{{ $list->date_released ?? '' }}</td>
                            <?php
						if($list->status_code == 0){
							$class="primary";
						}elseif($list->status_code == 1){
							$class="info";
						}elseif($list->status_code == 2){
							$class="success";
						}else{
							$class ="danger";
						}
						?>
                            <td><span class="badge badge-{{ $class }} text-xs">{{ $list->status_description }}</span>
                            </td>

                            <td>{{ $list->date_created }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@if(MySession::isAdmin())
    @include('cbu_withdraw.export_modal')
    @include('global.print_modal')
@endif
@endsection
@push('scripts')
    <script type="text/javascript">
        var dt_table;
        var typingTimer;
        var doneTypingInterval = 400;
        var id_chart_account_holder;
        var opcode = 0; //Add

        $(document).ready(function () {
            //Initialize Datatable
            $('#tbl_patronage_refund thead tr').clone(true).appendTo('#tbl_patronage_refund thead');
            $('#tbl_patronage_refund thead tr:eq(1)').addClass("head_rem")
            $('#tbl_patronage_refund thead tr:eq(1) th').each(function (i) {
                var title = $(this).text();
                $(this).addClass('col_search');
                $(this).html('<input type="text" placeholder="Search ' + title +
                    '" style="width:100%;" class="txt_head_search head_search"/> ');
                $('input', this).on('keyup change', function () {
                    var val = this.value;
                    clearTimeout(typingTimer);
                    typingTimer = setTimeout(function () {
                        if (dt_table.column(i).search() !== val) {
                            dt_table
                                .column(i)
                                .search(val)
                                .draw();
                        }
                    }, doneTypingInterval);
                });
            });
            dt_table = init_table();
            $('#tbl_patronage_refund .head_rem').remove();
            $('.dt-buttons').removeClass('btn-group')
            $('.dt-buttons').find('button').removeClass('btn-secondary');
        });

        function init_table() {
            var config = {
                order: [],
                "lengthChange": true,
                "autoWidth": false,
                scrollCollapse: true,
                scrollY: '70vh',
                scrollX: true,
                orderCellsTop: true,
                "bPaginate": false,
                dom: 'Bfrtip',
                buttons: ['excel']
            }

            var table = $("#tbl_patronage_refund").DataTable(config)
            console.log({
                table
            });
            return table;
        }

        $(function () {
            $.contextMenu({
                selector: '.patronage_refund_row',
                callback: function (key, options) {
                    var m = "clicked: " + key;
                    var id_patronage_capital_allocation = $(this).attr('data-code');
                    var id_cdv = $(this).attr('cdv-id');
                    console.log({
                        id_patronage_capital_allocation
                    })
                    if (key == "view") {
                        window.location = '/patronage-refund/allocate/' +
                            id_patronage_capital_allocation +
                            '?href=' + '{{ urlencode(url()->full()) }}';
                    } else if (key == "print") {
                        print_page('/patronage-refund/entry/' + id_patronage_capital_allocation);
                        // print_page('/cash_disbursement/print/'+id_cdv)
                    }
                },
                items: {
                    "view": {
                        name: "View Allocation",
                        icon: "fas fa-eye"
                    },
                    "sep1": "---------",
                    "quit": {
                        name: "Close",
                        icon: "fas fa-times"
                    }
                }
            });
        });

        function redirect_add() {
            window.location = '/patronage-refund/create' + '?href=' + '{{ urlencode(url()->full()) }}';
        }

    </script>
    @if($credential->is_create)
        <script type="text/javascript">
            $(document).ready(function () {
                $('#tbl_patronage_refund_filter').append(
                    '<a class="btn bg-gradient-info" onclick="redirect_add()" style="float:left"><i class="fa fa-plus"></i>&nbsp;Create ISC & PR Allocation</a>'
                )
            })

        </script>


    @endif
    <script type="text/javascript">
        function show_filter() {
            $('#view_options').modal('show')
        }

        function show_repayment_summary() {
            $('#summary_date_modal').modal('show');
        }

    </script>


@endpush
