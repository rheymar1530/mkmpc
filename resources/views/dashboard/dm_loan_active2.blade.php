<div class="pr-2 col-lg-12 p-4" style="max-height: calc(100vh - 75px);overflow-y: auto;overflow-x: auto">
	@foreach($loans as $loan)
	<div class="card c-border gray-color crd-soa">
		<div class="card-body pb-0">
			<div class="col-lg-12 col-12 p-0">
				<div class="row p-0 pb-3">

					<div class="col-lg-12 col-12 p-0">
						
						<div class="col p-0">
							<h4 class="mb-3 text-m"><span class="badge bg-gradient-dark" onclick="window.open('/loan/application/view/{{$loan->loan_token}}','_blank')">{{$loan->loan_service_name}}</span></h4>
						</div>
					</div>
                    <?php
                    	$total_paid = ROUND($loan->principal_amount - $loan->loan_balance,2);
                    	$percentage_complete = ROUND(($total_paid/$loan->principal_amount)*100,2);

                    	$percentage_incomplete = 100-$percentage_complete;

                    ?>
					<div class="col-lg-6 col-12 p-0">
                        <div class="d-flex flex-column">
  
                            <span class="txt-desc text-dark font-weight-bold lbl_color">Date Granted: <span class="ms-sm-2 font-weight-normal ml-2">{{$loan->loan_date}}</span></span>

                            <span class="txt-desc text-dark font-weight-bold lbl_color">Interest Rate: <span class="ms-sm-2 font-weight-normal ml-2">{{interest_rate_format($loan->interest_rate)}}</span></span>
                            <span class="txt-desc text-dark font-weight-bold lbl_color">Principal Amount: <span class="ms-sm-2 font-weight-normal ml-2">₱{{number_format($loan->principal_amount,2)}}</span></span>
                           
                        </div>
                    </div>
					<div class="col-lg-6 col-12 p-0">
                        <div class="d-flex flex-column">
                            
                            <span class="txt-desc text-dark font-weight-bold lbl_color">Total Paid: <span class="ms-sm-2 font-weight-normal ml-2">₱{{number_format($total_paid,2)}}</span></span>
                            <span class="txt-desc text-dark font-weight-bold lbl_color">Loan Balance: <span class="ms-sm-2 font-weight-normal ml-2">₱{{number_format($loan->loan_balance,2)}}</span></span>
                            <span class="txt-desc text-dark font-weight-bold lbl_color">Current Due: <span class="ms-sm-2 font-weight-normal ml-2">₱{{number_format($loan->current_due,2)}}</span></span>
                             
                            
                           
                        </div>
                    </div>

					<div class="col-lg-12 col-12 p-0">
						<span class="txt-desc text-dark font-weight-bold lbl_color">Loan Progress:</span><br>
						<div class="progress mt-1" style="height:22px">
							<div class="progress-bar progress-bar-striped bg-success" role="progressbar" style="width:<?php echo $percentage_complete ?>%" aria-valuenow="{{$percentage_complete}}" aria-valuemin="0" aria-valuemax="{{$percentage_complete}}">{{$percentage_complete}}%
							</div>
							<div class="progress-bar progress-bar-striped bg-danger" role="progressbar" style="width:<?php echo $percentage_incomplete ?>%" aria-valuenow="{{$percentage_incomplete}}" 
								aria-valuemin="{{$percentage_incomplete}}" aria-valuemax="100">{{($percentage_incomplete==100)?0:$percentage_incomplete}}%
							</div>
						</div>	
					</div>
				</div>
			</div>

		</div>
	</div>
	@endforeach
</div>