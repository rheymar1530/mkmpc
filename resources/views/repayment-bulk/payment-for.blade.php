		<div class="col-md-4">
			<div class="card h-100" id="card-payment-for">
				<div class="card-body py-4">
					<div class="row mt-3">
						<div class="form-group col-md-8">
							<label class="lbl_color">Payment For</label>
							<select class="form-control" id="sel-payment-for">
								@foreach($repayment_types as $val=>$type)
								<option value="{{$val}}">{{$type}}</option>
								@endforeach
							</select>
						</div>
					</div>

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
						<h6>Select Statements</h6>
						<div class="col-md-12">
							<label class="lbl_color mb-0">Select Barangay/LGU</label>
							<select id="sel-brgy-lgu" class="form-control p-0" name="brg-lgu" required  >
								
							</select>
						</div>

						<div class="col-md-12">
							
							<table class="table table-bordered tbl_pdc">
								<tbody id="body-loan-select"></tbody>

							</table>
						</div>
					</div>	

					<div id="div-type-field">
					</div>

				</div>

				<div class="card-footer py-2" id="div-foot">
					<button class="float-right btn round_button bg-gradient-primary2" onclick="SelectTransaction()" id="btn-select-transaction">Select Statement</button>
				</div>
			</div>
		</div>