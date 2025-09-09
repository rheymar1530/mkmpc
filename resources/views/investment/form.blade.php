@extends('adminLTE.admin_template')
@section('content')
<style>
	@media (min-width: 1200px) {
		.container{
			max-width: 900px;
		}
	}
	.tbl_in_prod tr>th ,.tbl_fees tr>th,.tbl-inputs tr>th{
		padding: 3px !important;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 14px;
		text-align: center;
	}
	.tbl_in_prod tr>td{
		padding: 0px;
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
		font-size: 15px;
	}
	.frm_inv_prod{
		height: 27px !important;
		width: 100%;    
		font-size: 13px;
	}
</style>
<script type="text/javascript">
	function resizeIframe(obj) {
		// alert(123);
		obj.style.height = 0;
		$height = (obj.contentWindow.document.documentElement.scrollHeight+50) + 'px';
		console.log({$height})
		obj.style.height = $height;
	}
</script>
<?php
	$back_link = '';
?>
<div class="container section_body">
	<?php $back_link = (request()->get('href') == '')?'/investment':request()->get('href'); ?>
	<a class="btn bg-gradient-secondary btn-sm" href="{{$back_link}}" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Investment List</a>
	<div class="card">
		<form id="frm_investment">
			<div class="card-body">
				<div class="text-center mb-5">
					<h5 class="head_lbl">Investment Application</h5>
				</div>

				@if(MySession::isAdmin())
				<div class="form-row">
					<div class="form-group col-md-10">
						<label class="mb-0">Investor</label>
						<select class="form-control in-inv-details" id="sel_investor" <?php echo (isset($investment_app_details)?'disabled':''); ?> >
							@if(isset($investment_app_details))
							<option value="{{$investment_app_details->id_member}}">{{$investment_app_details->member_name}}</option>
							@endif	
						</select>
					</div>
				</div>
				@endif
				<div class="form-row">
					<div class="form-group col-md-8">
						<label class="mb-0">Investment Product</label>
						<select class="form-control in-inv-details" id="sel_product">
							@foreach($products as $pr)
							<option value="{{$pr->id_investment_product}}">{{$pr->product_name}}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="form-row d-flex align-items-end">
					<div class="form-group col-md-4">
						<label class="mb-0">Interest Rate</label>
						<select class="form-control in-inv-details" id="sel_interest">
							@if(isset($terms))
							@foreach($terms as $t)
							<option value="{{$t->id_investment_product_terms}}" <?php echo (($investment_app_details->id_investment_product_terms ?? 0)==$t->id_investment_product_terms)?'selected':''; ?> >{{$t->terms}} {{$t->unit}}(s) - {{$t->interest_rate}}%</option>
							@endforeach 
							@endif
						</select>
					</div>

				</div>
				<div class="form-row d-flex align-items-end">
					<div class="form-group col-md-4">
						<label class="mb-0">Amount <small id="amt_range"></small></label>
						<input type="text" class="form-control in-inv-details class_amount" id="txt_investment_amount" value="{{number_format($investment_app_details->amount ?? 0,2)}}">
					</div>
					<div class="form-group col-md-2">
						<button type="button" class="btn btn-sm bg-gradient-primary2 round_button" onclick="compute_investment(1)">Compute</button>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12 col-12">
						<iframe id="iframe_investment_table" style="border:0;width: 100%" onload="resizeIframe(this)" src=""></iframe>
					</div>
				</div>
				<div class="row mb-3">
					<div class="col-md-12 col-12">
						<h5 class="lbl_color">Benefactors</h5>
						<table class="table tbl_in_prod table-bordered">
							<thead>
								<tr class="table_header_dblue">
									<th style="width: 5%;"></th>
									<th>Name</th>
									<th style="width: 30%;">Relationship</th>
									<th style="width: 5%;"></th>
								</tr>
							</thead>
							<tbody id="benefactor_body">
								<tr class="benefactor_row">
									<td class="text-center ben_counter">1</td>
									<td><input type="text" class="form-control  frm_inv_prod ben_in" name="name"></td>
									<td><input type="text" class="form-control  frm_inv_prod ben_in" name="relationship"></td>
									<td><a class="btn btn-xs bg-gradient-danger2 w-100" onclick="delete_benefactor(this)"><i class="fa fa-trash"></i></a></td>
								</tr>
							</tbody>
							
						</table>
						<a class="btn btn-sm bg-gradient-primary2 float-right" onclick="append_benefactor()"><i class="fa fa-plus pr-2"></i>Add Benefactors</a>
					</div>					
				</div>
			</div>
			<div class="card-footer">
				<button class="float-right btn btn-md bg-gradient-success2 round_button">Submit</button>
				@if($opcode == 1)
				<button type="button" class="btn btn-md bg-gradient-primary2 round_button float-right mr-2" onclick="show_status_modal()">Update Status</button>
				@endif
			</div>
		</form>
	</div>
</div>


@if($opcode == 1)
@include('investment.status_modal')
@endif

@endsection
@push('scripts')

@if(isset($investment_app_details))
<script type="text/javascript">
	$(document).ready(function(){
		compute_investment(1);

		// set benefactor
		const $benefactor = jQuery.parseJSON('<?php echo json_encode($benefactors ?? []); ?>');
		if($benefactor.length > 0){
			$('tbody#benefactor_body').html('');
			$.each($benefactor,function(i,item){
				append_benefactor();
				$('tr.benefactor_row').last().find('input.ben_in[name="name"]').val(item.name);
				$('tr.benefactor_row').last().find('input.ben_in[name="relationship"]').val(item.relationship);
			})
		}

		console.log({$benefactor});
	})
</script>
@endif
<script type="text/javascript">
	const OPCODE = '<?php echo $opcode;?>';
	const ID_INVESTMENT = '<?php echo $investment_app_details->id_investment ?? 0;?>';
	const BEN_ROW =`<tr class="benefactor_row">${$('.benefactor_row').last().html()}</tr>`;

	intialize_select2();
	$('#sel_product').val('<?php echo $investment_app_details->id_investment_product ?? 0 ?>');

	$(document).on('change','#sel_product',function(){
		// var val = $(this).val();
		// compute_investment(0);
		parseTerms();

	});

	function parseTerms(){
		$.ajax({
			type       :      'GET',
			url        :      '/invest/parseTerms',
			data       :      {'id_investment_product' : $('#sel_product').val()},
			success    :      function(response){
							  console.log({response});
							  var $out = ``;
							  $.each(response.terms,function(i,item){
							  	$out+= `<option value="${item.id_investment_product_terms}">${item.terms} ${item.unit}(s) - ${item.interest_rate}%</option>`;
							  });

							  $('#amt_range').text(`(${response.limit})`);
							  $('#sel_interest').html($out);
			}
		})
	}

	function set_amount_range(value){
		$('#amt_range').text(`(${value})`);
	}
	function intialize_select2(){		
		$("#sel_investor").select2({
			minimumInputLength: 2,
			width: '100%',
			createTag: function (params) {
				return null;
			},
			ajax: {
				tags: true,
				url: '/search_member',
				dataType: 'json',
				type: "GET",
				quietMillis: 1000,
				data: function (params) {
					var queryParameters = {
						term: params.term
					}
					return queryParameters;
				},
				processResults: function (data) {
					console.log({data});
					return {
						results: $.map(data.accounts, function (item) {
							return {
								text: item.tag_value,
								id: item.tag_id
							}
						})
					};
				}
			}
		});
	}
	$(document).on("focus",".class_amount",function(){
		var val = $(this).val();
		if(val == '' || val == 'NaN'){
			val = '0.00';
		}
		$(this).val(decode_number_format(val)); 
	})
	$(document).on("blur",".class_amount",function(){
		var val = $(this).val();
		if(!$.isNumeric(val)){
			val = 0;
		}
		$(this).val(number_format(parseFloat(val)));
	});

	function compute_investment(with_table){
		$param = {
			'investor' : $('#sel_investor').val(),
			'investment_product': $('#sel_product').val(),
			'amount' : decode_number_format($('#txt_investment_amount').val()),
			'id_terms' : $('#sel_interest').val(),
			'with_table' : with_table
		};
		$link = `/investment/compute_frame?${$.param($param)}`;
		$('#iframe_investment_table').prop('src',$link);


	}

	function post(){
		var $benefactor = [];
		$('tr.benefactor_row').each(function(){
			$temp = {};
			$(this).find('input.ben_in').each(function(){
				$temp[$(this).attr('name')] = $(this).val();
			})
			$benefactor.push($temp);
		});

	

		$post_data = {
			'investor' : $('#sel_investor').val(),
			'investment_product': $('#sel_product').val(),
			'id_investment_product_terms' : $('#sel_interest').val(),
			'amount' : decode_number_format($('#txt_investment_amount').val()),
			'opcode' : OPCODE,
			'benefactors' : $benefactor,
			'id_investment' : ID_INVESTMENT
		};

		$.ajax({
			type       :    'GET',
			url        :    '/investment/post',
			data       :    $post_data,
			beforeSend :    function(){
							show_loader();
			},
			success    :    function(response){
				console.log({response});
				hide_loader();
				if(response.RESPONSE_CODE == "SUCCESS"){
					var redirect_link = '/investment/view/'+response.INVESTMENT_ID;
					Swal.fire({
						title: "Investment  successfully Saved ",
						html : "<a href='"+redirect_link+"'>Investment ID#"+response.INVESTMENT_ID+"</a>",
						text: '',
						icon: 'success',
						showCancelButton : true,
						confirmButtonText: 'Create Another Investment',
						cancelButtonText: 'Back to Investment List',
						showConfirmButton : true,     
						allowEscapeKey : false,
						allowOutsideClick: false
					}).then((result) => {
						if(result.isConfirmed) {
							window.location = "/investment/create?href="+'<?php echo $back_link;?>';
						}else{
							window.location = '<?php echo $back_link;?>';
						}
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
				hide_loader();
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


		// 
	}


	$('#frm_investment').submit(function(e){
		e.preventDefault();
		Swal.fire({
			title: 'Do you want to save this?',
			icon: 'warning',
			showDenyButton: false,
			showCancelButton: true,
			confirmButtonText: `Save`,
		}).then((result) => {
			if (result.isConfirmed) {
				post();
			} 
		})	
	})

	function append_benefactor(){

		$('tbody#benefactor_body').append(BEN_ROW);
		benefactor_counter();
	}
	function delete_benefactor(elem){
		var parent_row = $(elem).closest('tr.benefactor_row');

		parent_row.hide(300,function(){
			$(this).remove();
			benefactor_counter();
		})
	}
	function benefactor_counter(){
		$('tr.benefactor_row').each(function(i){
			$(this).find('td.ben_counter').text((i+1));
		})
	}
</script>
@endpush