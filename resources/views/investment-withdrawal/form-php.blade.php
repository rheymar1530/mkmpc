<?php
	function withdrawal_form_card($param){


		$output = '		<div class="card c-border c-withdrawal-form">
							<div class="card-body p-3">
								<div class="text-right">
									<a class="btn btn-xs bg-gradient-danger2" onclick="remove_inv(this)"><i class="fa fa-trash"></i></a>
								</div>
								<p class="font-weight-bold mb-0 text-sm lbl_color spn-investor"></p>
								<p class="mb-0 text-sm lbl_color mb-0 spn-inv-prod"></p>
								<div class="form-row mt-3">
									<div class="form-group col-md-12 col-12">
										<label class="lbl_color mb-0">Amount</label>
										<input class="form-control form-control-border text-right class_amount text-with-amt" value="0.00">
									</div>
								</div>
							</div>
						</div>';

		return $output;
	}

	function withdrawal_form_row(){
		$output = '	<tr class="c-withdrawal-form">
						<td class="col-investor"></td>
						<td class="col-inv-prod"></td>
						<td class="text-right col-principal"></td>
						<td class="text-right col-interest"></td>
						<td class="text-right col-total"></td>
						<td class="td-in"><input class="form-control form-control-border text-right class_amount text-with-amt"></td>
						<td><a class="btn btn-xs bg-gradient-danger2" onclick="remove_inv(this)"><i class="fa fa-trash"></i></a></td>
					</tr>';
		return $output;
	}
?>