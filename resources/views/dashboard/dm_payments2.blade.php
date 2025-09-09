<div class="pr-2 col-lg-12 p-4" style="max-height: calc(100vh - 75px);overflow-y: auto;overflow-x: auto">
	@foreach($payments as $payment)
	<div class="card c-border gray-color crd-soa">
		<div class="card-body pb-0">
			<div class="col-lg-12 col-12 p-0">
				<div class="row p-0 pb-3">

					<div class="col-lg-12 col-12 p-0">
						<div class="col p-0">
							<h4 class="mb-3 text-m"><span class="badge bg-gradient-dark" onclick="window.open('/payments/{{$payment->reference}}','_blank')">#{{$payment->reference}}</span></h4>
						</div>
					</div>

					<div class="col-lg-6 col-12 p-0">
						<div class="d-flex flex-column">

							<span class="txt-desc text-dark font-weight-bold lbl_color">Transaction Type: <span class="ms-sm-2 font-weight-normal ml-2">{{$payment->transaction_type}}</span></span>

							<span class="txt-desc text-dark font-weight-bold lbl_color">Date: <span class="ms-sm-2 font-weight-normal ml-2">{{$payment->transaction_date}}</span></span>
							<span class="txt-desc text-dark font-weight-bold lbl_color">OR No: <span class="ms-sm-2 font-weight-normal ml-2">{{$payment->or_no}}</span></span>

						</div>
					</div>
					<div class="col-lg-6 col-12 p-0">
						<div class="d-flex flex-column">

							<span class="txt-desc text-dark font-weight-bold lbl_color">Total Payment: <span class="ms-sm-2 font-weight-normal ml-2">₱{{number_format($payment->total_payment,2)}}</span></span>
							<span class="txt-desc text-dark font-weight-bold lbl_color">Swiping Amount: <span class="ms-sm-2 font-weight-normal ml-2">₱{{number_format($payment->swiping_amount,2)}}</span></span>
							<span class="txt-desc text-dark font-weight-bold lbl_color">Change: <span class="ms-sm-2 font-weight-normal ml-2">₱{{number_format($payment->change,2)}}</span></span>

						</div>
					</div>


				</div>
			</div>

		</div>
	</div>
	@endforeach
</div>