<!-- <div class="col-md-12 p-0" style="padding-left:20px !important;margin-top: 40px;" id="loan_installment_terms">
	<fieldset class="border" style="padding-left: 10px;padding-right: 10px;">
		<legend class="w-auto p-1">Terms <span id="spn_term_period"></span><a class="btn btn-sm bg-gradient-primary" style="margin-left:20px" onclick="add_terms()"><i class="fa fa-plus"></i> &nbsp;Add More Terms</a></legend>

		<div style="max-height: calc(100vh + 500px);overflow-y: auto;overflow-x: auto;margin-top: 15px;padding-right: 10px;" id="terms_body">

		</div>
	</fieldset>
</div> -->
<div class="col-md-12 p-0" style="padding-left:20px !important;" id="loan_installment_terms">
	<div class="card c-border">
		<div class="card-body">
			<h5 class="lbl_color mb-4">Terms <span id="spn_term_period"></span>
				
			</h5>
			<div class="col-md-12">
				<div style="max-height: calc(100vh + 500px);overflow-y: auto;overflow-x: auto;margin-top: 15px;padding-right: 10px;" id="terms_body">
					<div class="card term_card service_card c-border">

						<div class="card-body">
							<div class="row">
								<div class="col-md-6 col-6">
									<span class="badge_term_period_label lbl_color">Term #<span class="term_count">1</span></span>
								</div>
								<div class="col-md-6 col-6">
									<button type="button" class="btn btn-sm bg-gradient-danger2 float-right btn-circle" onclick="remove_term(this)"><i class="fa fa-times float-right" style=""></i></button>
									<!-- <span class="badge_term_period_label lbl_color">Term #<span class="term_count">1</span></span> -->
								</div>
							</div>
							<!-- <a ><i class="fa fa-times float-right" style=""></i></a> -->

							<input type="text" class="form-control installment_terms term_period_form loan_service hide" key="terms_token">
							<div class="form-row form_check mt-4">
								<div class="form-group col-md-3">
									<label for="txt_terms">Loan Term</label>
									<input type="text" class="form-control installment_terms term_period_form loan_service col_number" value="0" key="terms">
								</div>
								<div class="form-group col-md-4">
									<label for="txt_interest_rate">Interest Rate (%)</label>
									<input type="text" class="form-control installment_terms term_period_form loan_service col_number" key="interest_rate" value="0">
								</div>
								<div class="form-group col-md-5">
									<label>Loan Protection Rate (%)</label>
									<input type="text" class="form-control loan_service installment_terms term_period_form col_number" key="loan_protection_rate" value="0">
								</div>
							</div>


							<!-- CHARGES AND CONDITION HOLDER -->

						</div>

					</div>
				</div>
			</div>
		</div>
		<div class="card-footer custom_card_footer">
			<div class="col-md-12">
				<a class="btn btn-sm bg-gradient-primary2 col-md-12" onclick="add_terms()"><i class="fa fa-plus"></i> &nbsp;Add More Terms</a>
			</div>
		</div>
	</div>
</div>
<?php $__env->startPush('scripts'); ?>
<script type="text/javascript">
	var $term_card = $('#terms_body').html();


	function add_terms(){
		$('#terms_body').append($term_card);
		animate_element($('.term_card').last(),1);
		set_term_counter();
	}

	function set_term_counter(){
		$('.term_card').each(function(i){
			$(this).find('.term_count').text(i+1)
		})
	}
	function remove_term(obj){
		$parent_card = $(obj).closest('.term_card');
		// $parent_card.remove();
		animate_element($parent_card,2);
		set_term_counter();
	}


	$(document).on('keyup','.col_number',function(){
		var val = $(this).val();
		if(!$.isNumeric(val)){
			val = 0;
		}
		$(this).val(val);
	})





</script>
<?php $__env->stopPush(); ?><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/loan_service/terms.blade.php ENDPATH**/ ?>