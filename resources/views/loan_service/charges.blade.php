<div class="col-md-12 p-0">
	<div class="card c-border">
		<div class="card-body">
			<h5 class="lbl_color text-center mb-4">Fees & Charges</h5>
			<div class="row">
				<div class="col-md-12">

					<div class="form-row">
						<div class="form-group col-md-8">
							<select class="form-control parent_loan_service loan_service sel_charges p-0 select2" key="id_charges_group" id="sel_charges_group" onchange="parseCharges(this)">
								@foreach($charges_group as $cg)
								<option value="{{$cg->id_charges_group}}">{{$cg->name}}</option>
								@endforeach
								@if($opcode == 1)
								<option value="{{$details->id_charges_group}}">{{$details->charge_name}}</option>
								@endif
							</select>
						</div>
						<div class="form-group col-md-4">
							<button class="btn btn-sm bg-gradient-danger2" onclick="remove_charges()" type="button"><i class="fas fa-times"></i>&nbsp;Remove</button>
						</div>
						<div class="form-group col-md-12 " style="margin-top:5px">
							<ul class="charges_text" id="charges_text">

							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@if(env('PRIME_ENABLE'))
<div class="col-md-12 p-0">
	<div class="card c-border">
		<div class="card-body">
			<h5 class="lbl_color text-center mb-4">Prime Condition</h5>
			<div class="row">
					<?php
						$prime_inp = ($details->with_prime ?? 0) == 1?'':'disabled';
						$prime_calc = array(1=>'Percentage',2=>'Fixed Amount');

					
					?>
				<div class="col-md-12">
					<div class="form-row">
						<div class="form-group col-md-12">
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input parent_loan_service loan_service" id="chk_with_prime" key="with_prime">
								<label class="custom-control-label chk_deduct_cbu" for="chk_with_prime">Deduct the Prime from the total loan proceeds?</label>
							</div>									
						</div>
						<div class="form-group col-md-6">
							<label>Minimum CBU</label>
							<input type="text" name="" class="parent_loan_service loan_service form-control in-prime class_amount" value="0" key="prime_min_cbu" <?php echo $prime_inp; ?> >
						</div>
						<div class="form-group col-md-6">
							<label>Minimum Loan Amount</label>
							<input type="text" name="" class="parent_loan_service loan_service form-control in-prime class_amount" key="prime_min_loan" value="0" <?php echo $prime_inp; ?> >
						</div>
					</div>

					<div class="form-row">
						<div class="form-group col-md-6">
							<label>Prime Calculation</label>
							<select class="parent_loan_service loan_service form-control in-prime" key="prime_calc" <?php echo $prime_inp; ?> >
								@foreach($prime_calc as $val=>$description)
								<option value="{{$val}}">{{$description}}</option>
								@endforeach
							</select>
						</div>
						<div class="form-group col-md-6">
							<label>Value</label>
							<input type="text" name="" class="form-control parent_loan_service loan_service in-prime class_amount" value="0" key="prime_val" <?php echo $prime_inp; ?> >
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endif
@push('scripts')
<script type="text/javascript">
	function intialize_select_charge(){
		$('#sel_charges_group').select2({dropdownAutoWidth :true,dropdownPosition: 'below'});
	}
	function parseCharges(obj){
		var val = $(obj).val();
		console.log(val);
		$.ajax({
			type        :        'GET',
			url         :        '/parseChargesDetails',
			data        :        {'id_charges_group' : val},
			success     :        function(response){
				var $charges_array = response.charges.charges.split("!!");
				var charges_html = "";
				for(var j=0;j<$charges_array.length;j++){
					charges_html+= '<li class="text-muted">'+$charges_array[j]+'</li>'
					console.log({j})
				}
				$('#charges_text').html(charges_html);
				
			}
		});
	}
	function remove_charges(){
		$('#sel_charges_group').val(0).trigger('change');
		$('#charges_text').html('');

	}

	$(document).on('click','#chk_with_prime',function(){
		var checked = $(this).prop('checked');
		
		$('.in-prime').prop('disabled',!checked);
	})
</script>
@endpush
