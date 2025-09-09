
@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
	.card-header .fa {
		transition: .3s transform ease-in-out;
	}
	.card-header .collapsed .fa {
		transform: rotate(90deg);
		padding-right: 3px;
	}
	.class_amount{
		text-align: right;

	}
	.custom_card_header{
		padding: 2px 2px 2px 10px !important;
	}
	.custom_card_header > h5{
		margin-bottom: unset;
		font-size:25px;
	}
	.form-row  label{
		margin-bottom: unset !important;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 15px;
	}
	.btn-circle {
		width: 25px;
		height: 25px;
		text-align: center;
		padding: 6px 0;
		font-size: 13px;
		line-height: 1;
		border-radius: 15px;
		
	}

	.tbl-inputs tr>th{
		padding: 5px;
		padding-left: 5px;
		padding-right: 5px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 14px;
		text-align: center;
	}
	.tbl-inputs tr>td{
		padding: 0px 3px 0px 3px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 14px;
	}
	.frm_loans,.frm-requirements{
		height: 27px !important;
		width: 100%;    
		font-size: 13px;
	}
	.text_center{
		text-align: center;
		font-weight: bold;
	}
	.select2-container--default .select2-selection--single {
		padding: unset;
		padding-top: 3px;
	}
	.select2-selection__rendered {
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 14px;
	}
	span.select2-container {
		transition: none;
		-moz-transition: none;
		-webkit-transition: none;
		-o-transition: none;
	}
	@media (min-width: 768px) {

		.col-md-cus{
			flex: 12.666667% !important;
			max-width: 12.666667% !important;
		}
		.col-md-cus2{
			flex: 5.666667% !important;
			max-width: 5.666667% !important;
		}	
		.col-md-cus3{
			flex: 8.666667% !important;
			max-width: 8.666667% !important;
		}
	}
	.control-label{
		font-size: 14px !important;
	}
	tr.asset_row[aria-deleted="true"]{
		display: none;
	}

	.row_account{
		font-weight: bold;
		background: #ff9999;
	}
	.class_num{
		text-align: center;
	}
	.asset_det{
		font-size: 16px !important;
	}
	.border-top{
		border-top:3px solid !important;
	}
</style>

<div class="container-fluid section_body">
	<div class="row">


		<div class="col-md-12">
			<?php 
			$back = ($type ==1)?'/asset/asset_purchase':'/asset/asset_adjustment';
			$back_link = (request()->get('href') == '')?$back:request()->get('href'); 
			?>


			<a class="btn bg-gradient-secondary btn-sm" href="{{$back_link}}" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to {{$module_title}} List</a>

			@if($allow_post)
			<a class="btn bg-gradient-primary btn-sm" href="/asset/edit/{{$asset->id_asset}}?href={{$back_link}}" style="margin-bottom:10px"><i class="fas fa-edit"></i>&nbsp;&nbsp;Edit {{$module_title}}</a>




			<a class="btn bg-gradient-warning btn-sm" onclick="show_status_modal()" style="margin-bottom:10px"><i class="fas fa-times"></i>&nbsp;&nbsp;Cancel {{$module_title}}</a>
			@endif




			<div class="btn-group" style="margin-bottom:10px">
				<button type="button" class="btn btn-danger btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Print
				</button>
				<div class="dropdown-menu">
					@if($asset->status != 10)
					<a class="dropdown-item" href="javascript:void(0)" onclick="print_page('/asset/print_sticker/{{$asset->id_asset}}')"  >Print Asset Sticker</a>
					@endif
					@if($type == 2)
					<a class="dropdown-item" href="javascript:void(0)" onclick="print_page('/journal_voucher/print/{{$asset->id_journal_voucher}}')" >Print Journal Voucher (JV# {{$asset->id_journal_voucher}})</a>
					@endif




				</div>
			</div>





			<div class="card c-border">
				<div class="card-body">
					<h4 class="head_lbl text-center">{{$module_title}} <small>

						(Asset ID# {{$asset->id_asset}})</small>
						@if($asset->status == 10)
						<span class="badge badge-danger">Cancelled</span>
						@endif
					</h4>
					<div class="form-group row p-0" style="margin-top:10px">
						<label class="col-md-12 control-label col-form-label asset_det" style="text-align: left">
							@if($type == 1)
							Cash Disbursement ID:
							@else
							ID Journal Voucher:
							@endif
							&nbsp; <span style="font-weight:normal">{{($type==1)?$asset->id_cash_disbursement:$asset->id_journal_voucher}}</span></label>
						</div>
						<div class="form-group row p-0" style="margin-top:-30px">
							<label class="col-md-12 control-label col-form-label asset_det" style="text-align: left">Branch: &nbsp; <span style="font-weight:normal">{{$asset->branch_name}}</span></label>
						</div>
						<div class="form-group row p-0" style="margin-top:-30px">
							<label class="col-md-12 control-label col-form-label asset_det" style="text-align: left">Purchase Date: &nbsp; <span style="font-weight:normal">{{date("m/d/Y", strtotime($asset->purchase_date))}}</span></label>
						</div>
						<div class="form-group row p-0" style="margin-top:-30px">
							<label class="col-md-12 control-label col-form-label asset_det" style="text-align: left">Valuation Date: &nbsp; <span style="font-weight:normal">{{$asset->valuation_date}}</span></label>
						</div>
						<div class="col-md-12 p-0" style="margin-top:20px">
							@if($view == 0)
							<div class="table-responsive" style="max-height: calc(100vh - 200px);overflow-y: auto;margin-top: 5px !important;overflow-x: auto">

								<table class="table table-bordered table-stripped table-head-fixed tbl-inputs table-hover" style="white-space: nowrap;">
									<thead>
										<?php
										$header_count = 7+count($depreciation_header);
										?>
										<tr>

											<th>Asset Code</th>

											<th class="" width="30%">Description</th>
											<th class="" width="20%">Serial No.</th>
											<th class="" width="5%">Qty</th>
											<th class="">Total Cost</th>
											<th class="" width="8%">Life (yrs)</th>
											<th class="">Salvage Value</th>
											@foreach($depreciation_header as $d)
											<th style="border:2px solid;">{{$d}}</th>
											@endforeach
											<!-- <th class="">Salvage Value</th> -->

										</tr>
									</thead>
									<tbody class="asset_body">

										@foreach($asset_items as $account_code=>$assets)
										<tr>
											<td class="row_account" colspan="{{$header_count}}">{{$account_code}}</td>
										</tr>
										@foreach($assets as $as)
										<tr>
											<td>{{$as->asset_code}}</td>
											<td>{{$as->description}}</td>
											<td>{{$as->serial_no}}</td>
											<td class="class_num">{{$as->quantity}}</td>
											<td class="class_amount">{{number_format($as->cost,2)}}</td>
											<td class="class_num">{{$as->life}}</td>
											<td class="class_amount" style="border-right: 2px solid">{{number_format($as->salvage_value,2)}}</td>
											@foreach($depreciation_header as $year)
											<?php
											$dep_amount = $depreciation_table[$as->asset_code][$year][0]->end_book_value ?? '';
											$dep_amount = ($dep_amount != '')?number_format($dep_amount,2):'';
											?>
											<td style="border:2px solid !important;" class="class_amount">{{$dep_amount}}</td>
											@endforeach
										</tr>
										@endforeach
										@endforeach
									</tbody>
								</table>

							</div>
							@else
							@foreach($asset_items as $account_code=>$assets)
							@foreach($assets as $as)
							<div class="card c-border">
								<div class="card-body">
									<h6 class="lbl_color"><b>{{$as->account}}</b></h6>
									<h6 class="lbl_color"><b>{{$as->description}} ({{$as->asset_code}})</b></h6>
									<div class="row">
										<div class="col-md-4">
											<table class="lbl_color">
												<tr>
													<td>Serial No. :</td>
													<td>&nbsp;&nbsp;{{$as->serial_no}}</td>
												</tr>
												<tr>
													<td>Quantity:</td>
													<td>&nbsp;&nbsp;{{$as->quantity}}</td>
												</tr>
												<tr>
													<td>Cost:</td>
													<td>&nbsp;&nbsp;{{number_format($as->cost,2)}}</td>
												</tr>
											</table>
										</div>
										<div class="col-md-4">
											<table class="lbl_color">
												<tr>
													<td>Life Span (Years):</td>
													<td>&nbsp;&nbsp;{{$as->life}}</td>
												</tr>
												<tr>
													<td>Salvage Value:</td>
													<td>&nbsp;&nbsp;{{number_format($as->salvage_value,2)}}</td>
												</tr>

												<tr>
													<td>Depreciation Method:</td>
													<td>&nbsp;&nbsp;{{$as->depreciatiton_method}}</td>
												</tr>
											</table>
										</div>
										<div class="col-md-8">
											<table class="table table-bordered table-stripped table-head-fixed tbl-inputs table-hover" style="white-space: nowrap;">
												<thead>
													<tr>
														<th width="2%"></th>
														<th style="width: 10%;">Year</th>
														<th>Start Value</th>
														<th>Depreciation Expense</th>
														<!-- <th>Depreciation Percentage</th> -->
														<th>Accumulated Depreciation</th>
														<th>End Value</th>
														<th></th>
														<!-- <th class="">Salvage Value</th> -->

													</tr>
												</thead>
												<?php
												$accumulated_depreciation = 0;
												$count = 1;

												?>

												<tbody class="asset_body">
													@foreach($depreciation_table[$as->asset_code] as $year=>$details)
													<?php
													$year_index_c = count($depreciation_table[$as->asset_code]);
													?>
													@foreach($details as $cc=> $row)
													<?php
													$accumulated_depreciation += $row->depreciation_amount;
													?>
													<tr class="<?php echo ($count == $year_index_c)?'border-top':'';  ?>">
														<td class="class_num">{{$count}}</td>
														<td class="class_num">{{$row->year}}</td>
														<td class="class_amount">{{number_format($row->start_book_value,2)}}</td>
														<td class="class_amount">{{number_format($row->depreciation_amount,2)}}</td>
														<!-- <td class="class_num">{{$row->depreciation_percentage}} %</td> -->

														<td class="class_amount">{{number_format($accumulated_depreciation,2)}}</td>
														<td class="class_amount">{{number_format($row->end_book_value,2)}}</td>
														<td><a class="btn btn-xs btn-primary" onclick="parseMonthlyDep('{{$row->id_asset_item}}','{{$row->year}}')">Show Monthly Depreciation</a></td>
													</tr>
													@endforeach
													<?php $count++;?>
													@endforeach

												</tbody>
											</table>

										</div>
									</div>
								</div>
							</div>
							@endforeach
							@endforeach
							@endif
						</div>
						@if($opcode == 1 && $asset->status == 10)
						<b>Cancellation Reason:</b> {{$asset->cancellation_reason}}
						@endif
					</div>
				</div>	

			</div>



		</div>
	</div>
	@if($allow_post)
	@include('asset.status_modal')

	@endif
	@include('global.print_modal')

	@include('asset.month_dep_modal')


	@endsection
	@push('scripts')	

	<script type="text/javascript">

	</script>
	@endpush

