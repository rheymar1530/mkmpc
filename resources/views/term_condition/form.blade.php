@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
	.tbl_mem th,.tbl_mem td,.tbl-terms th{
		padding: 2px 5px 2px 8px;
		font-size: 0.9rem;
		cursor: pointer;

	}

	.tbl-terms td{
		padding: 0px;
	}
	.sel-member{
		background: #66ff99;
	}
	.spn-check{
		color:green;
	}
</style>

<div class="container-fluid">
	<?php $back_link = (request()->get('href') == '')?'/term-condition':request()->get('href'); ?>
	<a class="btn btn-default btn-sm round_button" href="{{$back_link}}" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Terms Condition List</a>
	<div class="card">
		<div class="card-body">
			<div class="text-center">
				<h5 class="head_lbl">Terms Condition @if($opcode == 1)<small>(ID# {{$details->id_terms_condition}})</small>  @endif</h5>
			</div>

			<div class="row mt-5">
				<div class="form-group col-md-8">
					<label class="lbl_color mb-0">Description</label>
					<input type="text" class="form-control form-control-border" name="" id="txt-description" value="{{$details->description ?? ''}}">
				</div>
			</div>
			
			<table class="table tbl-terms table-bordered table-head-fixed">
				<thead>
					<tr>

						<th>Minimum CBU</th>
						<th>Maximum CBU</th>
						<th>Minimum Principal</th>
						<th>Maximum Principal</th>
						<th>Max Terms</th>
						<th width="3%"></th>
					</tr>
				</thead>
				<tbody id="body-term">
					<tr class="term-row">
						<td>
							<input type="text" class="form-control in-terms text-right" value="0.00" key="min_cbu">
						</td>
						<td>
							<input type="text" class="form-control in-terms text-right" value="0.00" key="max_cbu">
						</td>
						<td>
							<input type="text" class="form-control in-terms text-right" value="0.00" key="min_principal">
						</td>
						<td>
							<input type="text" class="form-control in-terms text-right" value="0.00" key="max_principal">
						</td>
						<td>
					
							<input type="text" class="form-control in-terms" value="1" key="up_to_terms">
						</td>
			
						<td class="text-center">
							<a class="btn btn-xs btn-danger" onclick="remove_term_row(this)"><i class="fa fa-trash"></i></a>
						</td>
					</tr>
				</tbody>
			</table>
			<button class="btn bg-gradient-primary mt-3" onclick="append_term_condition();">
				<i class="fa fa-plus"></i>&nbsp;Add Term Condition
			</button>
		</div>
		<div class="card-footer p-2">
			<button class="btn bg-gradient-success float-right" onclick="post()">Save</button>
		</div>
	</div>

</div>


@endsection

@push('scripts')
<script type="text/javascript">

	const loan_row = `<tr class="term-row">${$('tr.term-row').html()}</tr>`;
	const no_loan = '<tr class="text-center" id="row-no-loan"><td colspan="6" class="p-3">No Record Found</td></tr>';
	const OPCODE = {{$opcode}};
	const ID_TERMS_CONDITION = {{$details->id_terms_condition ?? 0}};
	const BACK_LINK = `<?php echo $back_link; ?>`;

	let CURRENT_MEMBER = 0;
	$('tr.term-row').remove();
	$('#body-term').html(no_loan);


	const remove_term_row=(obj)=>{
		parent_row = $(obj).closest('tr.term-row');
		parent_row.remove();
		if($('tr.term-row').length == 0){
			$('#body-term').html(no_loan);
		}
	}

	const append_term_condition = ()=>{
		$('#row-no-loan').remove();
		$('#body-term').append(loan_row);
	}

	$(document).on('select2:open', (e) => {
		const selectId = e.target.id

		$(".select2-search__field[aria-controls='select2-" + selectId + "-results']").each(function (
			key,
			value,
			) {
			value.focus()
		})
	})	

	$(document).on("focus",".in-terms.text-right",function(){
		var val = $(this).val();
		if(val == '' || val == 'NaN'){
			val = '0.00';
		}
		$(this).val(decode_number_format(val));	
	})

	$(document).on("blur",".in-terms.text-right",function(){
		var val = $(this).val();
		if(!$.isNumeric(val)){
			val = 0;
		}
		$(this).val(number_format(parseFloat(val)));
	});

	const post = ()=>{
		Swal.fire({
			title: 'Do you save this?',
			icon: 'warning',
			showDenyButton: false,
			showCancelButton: true,
			confirmButtonText: `Save`,
		}).then((result) => {
			if (result.isConfirmed) {
				// alert("POSTED");
				post_request();
			} 
		})	
		
	}

	const post_request = () =>{
		var terms = [];

		$('tr.term-row').each(function(){
			var t= {};
			$(this).find('.in-terms').each(function(){
				var val = ($(this).hasClass('text-right'))?decode_number_format($(this).val()):$(this).val();
				t[$(this).attr('key')] = val;
			})
			terms.push(t);

		})
		$.ajax({
			type          :       'GET',
			url           :       '/term-condition/post',
			data          :        {'opcode' : OPCODE,'description' : $('#txt-description').val() , 'terms' : terms,'id_terms_condition' : ID_TERMS_CONDITION},
			beforeSend    :        function(){
				$('.mandatory').removeClass('mandatory');
				show_loader();
			},
			success       :        function(response){
				console.log({response});
				hide_loader();

	
				if(response.RESPONSE_CODE == "SUCCESS"){
					Swal.fire({
						title: response.message,
						text: '',
						icon: 'success',
						showConfirmButton : false,
						timer : 2500
					}).then((result) => {
					
						window.location ='/term-condition/view/'+response.id_terms_condition+"?href="+encodeURIComponent(BACK_LINK);
					});	
				
				}else if(response.RESPONSE_CODE == "ERROR"){
					Swal.fire({
						title: response.message,
						text: '',
						icon: 'warning',
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

@if($opcode == 1)
<script type="text/javascript">
	const CURRENT_TERMS = jQuery.parseJSON('<?php echo json_encode($terms ?? []); ?>');
	$.each(CURRENT_TERMS,function(i,item){
		append_term_condition();

	   	$last = $('tr.term-row').last();
	   	$last.find('.in-terms[key="min_cbu"]').val(number_format(item.min_cbu,2));
	   	$last.find('.in-terms[key="max_cbu"]').val(number_format(item.max_cbu,2));
	   	$last.find('.in-terms[key="min_principal"]').val(number_format(item.min_principal,2));
	   	$last.find('.in-terms[key="max_principal"]').val(number_format(item.max_principal,2));
	   	$last.find('.in-terms[key="up_to_terms"]').val(item.up_to_terms);
	   	
	});
</script>
@endif
@endpush