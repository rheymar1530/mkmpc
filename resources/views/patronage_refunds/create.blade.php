@extends('adminLTE.admin_template')
@section('content')


<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="text-center">
                        <h5 class="head_lbl">Patronage Refund and Capital Share Allocation</h5>
                    </div>
                </div>
            </div>
            <form>
                <div class="row mt-5 d-flex align-items-end">
                    <div class="form-group col-md-2">
                        <label class="lbl_color">Year</label>
                        <select class="form-control form-control-border" name="year">
                            @php
                                $currentYear = MySession::current_year();
                            @endphp

                            @for($i=2025;$i<=$currentYear;$i++)
                                <option value="{{ $i }}" <?php echo ($i == $sel_year) ? 'selected' : ''; ?>>{{ $i }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label class="lbl_color">Interest Capital Share Payable</label>
                        <input class="form-control form-control-border class_amount text-right" type="text"
                            value="{{ number_format($icsp,2) }}" name="icsp">
                    </div>
                    <div class="form-group col-md-3">
                        <label class="lbl_color">Patronage Refund Payables</label>
                        <input class="form-control form-control-border class_amount text-right" type="text"
                            value="{{ number_format($prp,2) }}" name="prp">
                    </div>
                    <div class="form-group col-md-3">
                        <button class="btn btn-md round_button bg-gradient-primary">Compute Allocation</button>
                    </div>
                </div>
            </form>
            <div class="row">
                <div class="col-md-6">
                    <p class="my-0"><b>Average Capital Share: </b>
                        {{ number_format(round($ave_CBU ?? 0),2) }}
                    </p>
                    <p class="my-0"><b>Interest on Capital Share Rate: </b>
                        {{ round($ISCRate * 100,2) }}%</p>

                </div>
                <div class="col-md-6">

                    <p class="my-0"><b>Total Interest: </b>
                        {{ number_format(round($total_Interest ?? 0),2) }}</p>
                    <p class="my-0"><b>Patronage Refund Rate: </b>
                        {{ round($PRRate * 100,2) }}%</p>
                </div>
                <div class="col-md-12">
                    @include('patronage_refunds.allocation-table')
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button class="btn btn-md bg-gradient-success float-right" id="btn-post-allocation">Post</button>
        </div>
    </div>
</div>

@include('patronage_refunds.post-modal')
@endsection

@push('scripts')
<script type="text/javascript">
    $(document).on("focus",".class_amount",function(){
    	var val = $(this).val();
    	if(val == '' || val == 'NaN'){
    		val = '0.00';

    		$(this).val('');

    		return;
    	}
    	$(this).val(decode_number_format(val));
    })
    $(document).on("blur",".class_amount",function(){
    	var val = $(this).val();
    	if(!$.isNumeric(val)){
    		val = 0;
    		$(this).val('');

    		return;
    	}
    	$(this).val(number_format(parseFloat(val)));
    })
</script>
@endpush
