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

<div class="modal fade" id="payment-for-modal" role="dialog" aria-labelledby="booking" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md" >
        <div class="modal-content">
           
            <div class="modal-header" style="padding:5px;padding-left: 10px;">
                <h5 class="modal-title h4">Payment For
                    <span id="spn_id_cash_receipt"></span>

                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">


                <div class="row mt-3" id="div-individual">
                    <div class="form-group col-md-12">
                        <label class="lbl_color mb-0">Select Loaner</label>
                        <select id="sel-member" class="form-control p-0" name="id_member" required  >
                        </select>
                    </div>
                    <div class="col-md-12">
                        <table class="table table-bordered tbl_pdc">
                            <tbody id="body-loan-select"></tbody>

                        </table>
                    </div>
                </div>          

                <div class="row mt-3" id="div-statement">
                    <div class="col-md-12">
                        <h6>Select Statement</h6>
                    </div>
                    <div class="col-md-12">
                        <label class="lbl_color mb-0">Barangay/LGU</label>
                        <select id="sel-brgy-lgu" class="form-control p-0" name="brg-lgu" required>
                            @foreach($brgy_lgu as $type=>$row)
                            <optgroup label="{{$type}}">
                                @foreach($row as $r)
                                <option value="{{$r->id_baranggay_lgu}}" <?php echo ($r->id_baranggay_lgu == ($StatementData['selected_brgy_lgu'] ?? 0))?'selected':''; ?> >{{$r->brgy_lgu}}</option>
                                @endforeach
                            </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12 mt-3">
                       
                        <table class="table table-bordered tbl_pdc">
                            <tbody id="body-loan-select"></tbody>

                        </table>
                    </div>
                </div>  

                <div id="div-type-field">
                </div>
            </div>
            <div class="modal-footer" style="padding:5px;padding-left: 10px;">
                <button class="float-right btn round_button bg-gradient-primary2" onclick="SelectTransaction()" id="btn-select-transaction">Select Statement</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
            
        </div>

    </div>
</div>
@push('scripts')
<script type="text/javascript">
    $(document).on('change','#sel-brgy-lgu',function(){
        var val = $(this).val();
        parseStatements(false);
    })
</script>
@endpush