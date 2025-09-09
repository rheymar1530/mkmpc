@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">

	.main_form{
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif !important;

	}

	.tbl_asset_maintenance tr>th{
		padding: 3px !important;
	/*	padding-left: 5px;
		padding-right: 5px;*/
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 14px;
		text-align: center;
	}

	.tbl_asset_maintenance tr>td,.tbl_fees tr>td,.tbl_repayment_display tr>td{
		padding: 0px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 15px;
	}

	.frm_asset_in{
		height: 27px !important;
		width: 100%;    
		font-size: 13px;
	}

	.class_amount{
		text-align: right;

	}

</style>
<?php
$depreciation_method = [
	1=>"Straight Line",
	2=>"Sum of Years",
	3=>"Double Declining Balance"
];

?>
<div class="wrapper2">
	<div class="container main_form section_body" style="margin-top: -20px;" >
		
		<div class="card" id="repayment_main_div">
			<form id="frm_submit">
				<div class="card-body col-md-12">
					<h3 class="head_lbl text-center mb-4">Asset Maintenance </h3>

					<div class="row">
						<div class="col-md-12">
							
							<table class="table tbl_asset_maintenance table-bordered">
								<thead>
									<tr class="table_header_dblue">
										<th width="12%">Account Code</th>
										<th>Description</th>
										<th width="9%">Life Span</th>
										<th width="12%">Salvage Value %</th>
										<th width="20%">Depreciation Method</th>
										
									</tr>
								</thead>
								<tbody id="allowance_body">
									<?php
									$asset = array();
									?>
									@foreach($asset_accounts as $ac)
									<tr class="row_asset" data-id="{{$ac->id_chart_account}}">
										<td><input type="text" class="form-control frm_asset_in" value="{{$ac->account_code}}" disabled></td>
										<td><input type="text" class="form-control frm_asset_in" value="{{$ac->description}}" disabled></td>
										<td><input type="text" class="form-control frm_asset_in tbl-input" key="life_span" value="{{$ac->life_span}}"></td>
										<td><input type="text" class="form-control frm_asset_in tbl-input" key="salvage_percentage" value="{{$ac->salvage_percentage}}"></td>
										<td>
											<select class="form-control frm_asset_in p-0 tbl-input"  key="depreciation_method">
												@foreach($depreciation_method as $val=>$desc)
												<option value="{{$val}}" <?php echo ($val == $ac->depreciation_method)?"selected":""; ?>>{{$desc}}</option>
												@endforeach
											</select>
										</td>
										<?php
										$asset[$ac->id_chart_account]['id_chart_account'] = $ac->id_chart_account;
										$asset[$ac->id_chart_account]['life_span'] = $ac->life_span;
										$asset[$ac->id_chart_account]['salvage_percentage'] = (float)$ac->salvage_percentage;
										$asset[$ac->id_chart_account]['depreciation_method'] = (float)$ac->depreciation_method;
										?>
									</tr>
									@endforeach
								</tbody>
							</table>
						</div>


					</div>
				</div>
				@if($allow_post)
				<div class="card-footer">
					<button class="btn  bg-gradient-success2 float-right">Save</button>
				</div>
				@endif
			</form>

		</div>
	</div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
	const assets = jQuery.parseJSON('<?php echo json_encode($asset); ?>');
	console.log({assets});
	$("tr").not(':first').hover(
		function () {
			$(this).find('.frm_asset_in').css("background","yellow");
		}, 
		function () {
			$(this).find('.frm_asset_in').css("background","");
		}
		);


	</script>
	@if($allow_post)
	<script type="text/javascript">

		function check_changes(){
			var $changes = [];
			$('.row_asset').each(function(){
				var id_chart_account = $(this).attr('data-id');
				var salvage_percentage = parseFloat($(this).find('.tbl-input[key="salvage_percentage"]').val());
				var life_span= parseFloat($(this).find('.tbl-input[key="life_span"]').val());
				var depreciation_method= $(this).find('.tbl-input[key="depreciation_method"]').val();

				if(salvage_percentage != assets[id_chart_account]['salvage_percentage'] || life_span != assets[id_chart_account]['life_span'] || depreciation_method != assets[id_chart_account]['depreciation_method']){
					var $temp = {};
					$temp['id_chart_account'] = id_chart_account;
					$temp['salvage_percentage'] = salvage_percentage;
					$temp['life_span'] = life_span;
					$temp['depreciation_method'] = depreciation_method;
					$changes.push($temp);
				}

			})


			return $changes;
			console.log({$changes})
		}


		$('#frm_submit').on('submit',function(e){
			e.preventDefault();
			var changes = check_changes();
			if(changes.length > 0){
				Swal.fire({
					title: 'Do you want to save this?',
					icon: 'warning',
					showDenyButton: false,
					showCancelButton: true,
					confirmButtonText: `Save`,
				}).then((result) => {
					if (result.isConfirmed) {
						post(changes);
					} 
				})	

			}else{
				Swal.fire({
					title: "No Changes",
					text: '',
					icon: 'warning',
					showCancelButton : false,
					showConfirmButton : false,
					timer : 2500
				});
				return;
			}
		})

		function post($changes){
			$.ajax({
				type      :      'POST',
				url       :      '/asset_maintenance/post',
				data      :      {'changes' : $changes},
				beforeSend:      function(){
					show_loader();
				},
				success   :      function(response){
					hide_loader();
					console.log({response});

					if(response.RESPONSE_CODE == "SUCCESS"){
						Swal.fire({
							title: "Data successfully saved !",
							text: '',
							icon: 'success',
							showCancelButton : false,
							showConfirmButton : false,
							timer : 2500
						}).then(function(){
							location.reload();
						});
					}else if(response.RESPONSE_CODE == "ERROR"){
						Swal.fire({
							title: response.message,
							text: '',
							icon: 'warning',
							showCancelButton : false,
							showConfirmButton : false,
							timer : 2500
						});
					}
				},error: function(xhr, status, error) {
					hide_loader()
					var errorMessage = xhr.status + ': ' + xhr.statusText;
					Swal.fire({
						title: "Error-" + errorMessage,
						text: '',
						icon: 'warning',
						confirmButtonText: 'OK',
						confirmButtonColor: "#DD6B55"
					});
				}

			})
		}

	</script>
	@endif


	@endpush

