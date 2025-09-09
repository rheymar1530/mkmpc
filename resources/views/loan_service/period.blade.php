<div class="col-md-12 p-0" style="padding-left:20px !important;margin-top: 40px;" id="loan_period_one_time">
	<div class="card c-border">
		<div class="card-body">
			<h5 class="lbl_color text-center mb-4">Period</h5>
			<div style="max-height: calc(100vh + 500px);overflow-y: auto;overflow-x: auto;margin-top: 15px;padding-right: 10px;" id="period_body">
				<div class="card period_card service_card c-border" data-period="">

					<div class="card-body">
						<h5 class="lbl_color mb-4"><span class="period_count"></span></h5>
						
						
						<input type="text" class="form-control loan_service onetime_period term_period_form hide" key="terms_token">
						<div class="form-row form_check">
							<div class="form-group col-md-5">
								<label for="txt_interest_rate">Interest Rate (%)</label>
								<input type="text" class="form-control loan_service onetime_period term_period_form col_number" key="interest_rate" value="0">
							</div>
							<div class="form-group col-md-5">
								<label>Loan Protection Rate (%)</label>
								<input type="text" class="form-control loan_service onetime_period term_period_form col_number" key="loan_protection_rate" value="0">
							</div>
						</div>
						<!-- CHARGES AND CONDITION HOLDER -->
					</div>
				</div>
			</div>
		</div>
	</div>

</div>

@push('scripts')
<script type="text/javascript">






</script>
@endpush