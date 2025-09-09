

<div class="col-md-12 p-0">
	<div class="card c-border">
		<div class="card-body">
			<h5 class="lbl_color text-center mb-4">
				Loan Condition
			</h5>
			<div class="col-md-12">

				<div class="form-row form_check">
					<div class="form-group col-md-6">
						<label>CBU Requirement Amount</label>
						<input type="text" class="form-control parent_loan_service loan_service col_amount class_amount"  key="cbu_amount" value="0.00">
					</div>
				</div>
				<div class="form-row form_check">
					<div class="form-group col-md-12">
						<div class="custom-control custom-checkbox">
							<input type="checkbox" class="custom-control-input chk_deduct_cbu parent_loan_service loan_service" id="CHK_7762322" key="is_deduct_cbu">
							<label class="custom-control-label chk_deduct_cbu" for="CHK_7762322">Deduct the deficient amount from the total loan proceeds?</label>
						</div>									
					</div>
				</div>
				<hr class="separator">
				<div class="form-row form_check">
					@include('loan_service.netpay')
				</div>
				<hr class="separator">
				<div class="form-row form_check">
					<div class="form-group col-md-6">
						<label>Age (0 if no condition on Age)</label>
						<input type="number" class="form-control parent_loan_service loan_service" key="avail_age" value="0">
					</div>
					<div class="form-group col-md-6">
						<label>No of Comakers</label>
						<input type="number" class="form-control parent_loan_service loan_service" key="no_comakers">
					</div>
				</div>
				<div class="form-row form_check">
					<div class="form-group col-md-12">
						<div class="custom-control custom-checkbox">
							<input type="checkbox" class="custom-control-input chk_new_loaner parent_loan_service loan_service" id="CHK_5132" key="is_open_new_loaner">
							<label class="custom-control-label chk_new_loaner" for="CHK_5132">Open for new loaner?</label>
						</div>									
					</div>
				</div>
				<hr class="separator">
				<div class="form-row form_check" id="div_deduct_interest_holder">
					<div class="form-group col-md-12" id="div_deduct_interest">
						<?php
							if(!isset($details->deduct_interest)){
								$checked = "";
							}else{
								$checked = ($details->deduct_interest ==0)?"":"checked";
							}
						?>
						<div class="custom-control custom-checkbox">
							<input type="checkbox" class="custom-control-input chk_deduct_interest parent_loan_service loan_service" id="chk_deduct_interest" key="deduct_interest" <?php echo $checked;   ?> >
							<label class="custom-control-label" for="chk_deduct_interest">Deduct Interest from loan proceed ?</label>
						</div>									
					</div>
				</div>
				<div class="form-row form_check">
					<div class="form-group col-md-12">
						<?php
							if(!isset($details->is_multiple)){
								$checked = "";
							}else{
								$checked = ($details->is_multiple ==0)?"":"checked";
							}
						?>
						<div class="custom-control custom-checkbox">
							<input type="checkbox" class="custom-control-input chk_is_multiple parent_loan_service loan_service" id="chk_is_multiple" key="is_multiple" <?php echo $checked;   ?> >
							<label class="custom-control-label" for="chk_is_multiple">Open for Multiple Application ?</label>
						</div>									
					</div>
				</div>
				<div class="form-row form_check">
					<div class="form-group col-md-12">
						<div class="custom-control custom-checkbox">
							<input type="checkbox" class="custom-control-input chk_is_renew_no_pay parent_loan_service loan_service" id="CHK_7762322qwe" key="is_renew_no_pay" onclick="can_be_renew_after_no_pay(this)">
							<label class="custom-control-label chk_is_renew_no_pay" for="CHK_7762322qwe">Can be renew after how many payments ?</label>
						</div>									
					</div>
				</div>

				<div class="form-row form_check">
					<div class="form-group col-md-5">
						<label>Number of payments to renew</label>
						<input type="number" class="form-control parent_loan_service loan_service txt_renew_payments" key="renew_payments" id="txt_renew_payments"disabled>
					</div>
				</div>
				<div class="form-row form_check">
					<div class="form-group col-md-12">
						<div class="custom-control custom-checkbox">
							<input type="checkbox" class="custom-control-input chk_is_renew_no_pay parent_loan_service loan_service" id="CHK_7762322qwexx" key="with_maker_cbu" onclick="maker_cbu(this)">
							<label class="custom-control-label chk_maker_cbu" for="CHK_7762322qwexx">Has Minimum CBU for Co-maker</label>
						</div>									
					</div>
				</div>
				<div class="form-row form_check">
					<div class="form-group col-md-5">
						<label>Co-Maker Minimum CBU</label>
						<input type="text" class="form-control parent_loan_service loan_service class_amount txt_comaker_cbu" key="maker_min_cbu" id="txt_comaker_cbu" value="0.00" disabled>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

