@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
	#tbl-cdv tr.cdv-row td, #tbl-cdv th{
		padding: 3px;
	}
	#tbl-cdv tr.cdv-row td{
		font-size: 0.85rem;
	}
	.footer_fix {
		padding: 3px !important;
		background-color: #fff;
		border-bottom: 0;
		box-shadow: inset 0 1px 0 #dee2e6, inset 0 -1px 0 #dee2e6;
		position: -webkit-sticky;
		position: sticky;
		bottom: 0;
		z-index: 10;
	}
	.selected{
		background-color: #b3ffb3;
	}
</style>
<?php $back_link = (request()->get('href') == '')?'/manager-certification':request()->get('href'); ?>
<div class="container">
	<a class="btn btn-default btn-sm round_button" href="{{$back_link}}" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Manager Certiciation List</a>
	@if(!$allow_post)
	<a class="btn bg-gradient-primary2 btn-sm round_button float-right" href="/manager-certification/edit/{{$details->id_manager_certification}}" style="margin-bottom:10px"><i class="fas fa-edit"></i>&nbsp;&nbsp;Edit Manager Certification</a>
	@endif
	<div class="card">
		<div class="card-body">
			<div class="text-center mb-3">
				<h4 class="head_lbl">@if($allow_post)Generate @endif Manager Certification  @if($allow_post && $opcode == 1) <small>(Reference no. {{$details->id_manager_certification}})</small>  @endif</h4>
			</div>
			@if($allow_post)
			<form id="frm-generate-cdv">
				<div class="row d-flex align-items-end">
					<div class="form-group col-md-3">
						<label class="lbl_color">Date Released</label>
						<input type="date" class="form-control form-control-border" name="start_date" value="{{$start_date}}">
					</div>
					<div class="form-group col-md-3">
						<input type="date" class="form-control form-control-border" name="end_date" value="{{$end_date}}">
					</div>
					<div class="form-group col-md-2">
						<button class="btn bg-gradient-primary2" type="submit"><i class="fa fa-search"></i>&nbsp;Generate</button>
					</div>
				</div>
			</form>
			@else
			<div class="card c-border">
				<div class="card-body">
					<div class="row">
						<div class="col-lg-6 col-12">
							<div class="d-flex flex-column">
								<span class="text-sm  font-weight-bold lbl_color">MC-{{$details->mc_year}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$details->formatted_reference}}</span>
								<span class="text-sm  font-weight-bold lbl_color">Manager Certification No: <span class="ms-sm-2 font-weight-normal ml-2">{{$details->id_manager_certification}}</span></span>
								<span class="text-sm  font-weight-bold lbl_color">Date: <span class="ms-sm-2 font-weight-normal ml-2">{{$details->date_form}}</span></span>
							</div>		
						</div>
					</div>
				</div>
			</div>
			@endif
			<?php
			if($allow_post){
				$colspan1 = 6;
				$colspan2 = 4;
			}else{
				$colspan1 = 5;
				$colspan2 = 3;
			}
			?>

			<div class="row mb-3">
				<div class="col-md-12 col-12">
					<table class="table table-head-fixed tbl_in_prod table-bordered table-hover" id="tbl-cdv">
						<thead>
							<tr class="table_header_dblue">
								@if($allow_post)
								<th class="table_header_dblue"><input type="checkbox" id="chk-sel-all"></th>
								@endif
								<th class="table_header_dblue">DATE</th>
								<th class="table_header_dblue">CASH VOUCHER</th>
								<th class="table_header_dblue">PAYEE</th>
								<th class="table_header_dblue">AMOUNT</th>
								<th class="table_header_dblue">PURPOSE</th>

							</tr>
						</thead>
						<tbody id="cdv-body">
							<?php
							$total = 0;

							?>
							@if(count($cdvs) > 0)
							@foreach($cdvs as $cdv)
							<tr class="cdv-row {{($cdv->checked == 1)?'selected':''}}" data-id="{{$cdv->id_cash_disbursement}}" <?php
							if($allow_post){
								echo "amount='".$cdv->amount."'";
							}
						?> >
						@if($allow_post)
						<td class="text-center"><input type="checkbox" class="chk-cdv" <?php echo ($cdv->checked==1)?'checked':''; ?> ></td>
						@endif
						<td>{{$cdv->date}}</td>
						<td>CV - {{$cdv->id_cash_disbursement}}</td>
						<td>{{$cdv->payee}}</td>
						<td class="text-right">{{number_format($cdv->amount,2)}}</td>
						<td>{{$cdv->purpose}}</td>
					</tr>
					<?php
					$total += $cdv->amount;

					?>
					@endforeach
					@else
					<tr>
						<th colspan="{{$colspan1}}" class="text-center">No Data</th>
					</tr>
					@endif
				</tbody>
				<footer>
					<tr>
						<th colspan="{{$colspan2}}" class="footer_fix text-left font-weight-normal" style="text-align:center;background: #808080 !important;color: white;">
							@if($allow_post)
							Total Selected:
							<span id="spn-total" class="ml-2"></span>
							@endif
						</th>
						<th class="footer_fix text-right"  style="background: #808080 !important;color: white;">{{number_format($total,2)}}</th>
						<th class="footer_fix"  style="background: #808080 !important;color: white;"></th>
					</tr>
				</footer>
			</table>
		</div>					
	</div>
</div>

<div class="card-footer">
	@if($allow_post)
	<button class="btn bg-gradient-success2 round_button float-right" onclick="post_mgr()">Save</button>
	@else
	<button class="btn bg-gradient-danger2 round_button float-right" onclick="print_page('/manager-certification/print/{{$details->id_manager_certification}}')" id="btn-print-cert"><i class="fa fa-print"></i>&nbsp;Print</button>
	@endif
</div>

</div>
</div>

@if(!$allow_post)
@include('global.print_modal')
@endif

@endsection


@push('scripts')
<script type="text/javascript">
	const BACK_LINK = `<?php echo $back_link; ?>`;
	const ID_MANAGER_CERTIFICATION = {{$details->id_manager_certification ?? 0}};
</script>

@if($allow_post)
<script type="text/javascript">
	const OPCODE = {{$opcode}};
	
	$(document).ready(function(){
		compute_selected();
	})
	$(document).on('click','.chk-cdv',function(){
		
		var prow = $(this).closest('tr.cdv-row');
		if($(this).prop('checked')){
			prow.addClass("selected");

		}else{
			prow.removeClass("selected");
		}
		compute_selected();
		
	});

	$(document).on('click','#chk-sel-all',function(){
		var checked = $(this).prop('checked');
		if(checked){
			$('tr.cdv-row').addClass('selected');
		}else{
			$('tr.cdv-row').removeClass('selected');
		}		
		$('.chk-cdv').prop('checked',checked);

		compute_selected();
	});

	const compute_selected = ()=>{
		var $total = 0;
		$('.chk-cdv').each(function(){
			if($(this).prop('checked')){
				var amount = parseFloat($(this).closest('tr.cdv-row').attr('amount'));
				$total += amount;
			}
		});
		$('#spn-total').text(number_format($total,2));		
	}
	
	function post_mgr(){
		Swal.fire({
			title: 'Are you sure you want to save this ?',
			icon: 'warning',
			showDenyButton: false,
			showCancelButton: true,
			confirmButtonText: `Yes`,
			allowOutsideClick: false,
			allowEscapeKey: false,
		}).then((result) => {
			if (result.isConfirmed) {
				post();
			}
		});
	}

	function post(){
		let selected_cdv = [];
		$('.chk-cdv').each(function(){
			if($(this).prop('checked')){
				var id = $(this).closest('tr.cdv-row').attr('data-id');
				selected_cdv.push(id);
			}
		})
		$.ajax({
			type          :             'POST',
			url           :             '/manager-certification/post',
			beforeSend    :             function(){
				show_loader();
			},
			data          :             {
				'cdv' : selected_cdv,
				'opcode' : OPCODE,
				'id_manager_certification' : ID_MANAGER_CERTIFICATION
			},
			success       :             function(response){
				console.log({response});
				hide_loader();
				if(response.RESPONSE_CODE == "SUCCESS"){
					Swal.fire({
						title: 'Manager Certification Successfully posted',
						icon: 'success',
						showCancelButton: false,
						showConfirmButton : false,
						cancelButtonText : 'Back to Manager certification list',
						confirmButtonText: `Close`,
						allowOutsideClick: false,
						allowEscapeKey: false,
						timer : 3000
					}).then((result) => {
						window.localStorage.setItem('for_print',response.ID_MANAGER_CERTIFICATION);
						window.location ='/manager-certification/view/'+response.ID_MANAGER_CERTIFICATION+"?href="+encodeURIComponent(BACK_LINK);
					})
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
	}
</script>

@else
<script type="text/javascript">
	$(document).ready(function(){
		if(window.localStorage.getItem("for_print") == ID_MANAGER_CERTIFICATION){
			$('#btn-print-cert').trigger('click');
			window.localStorage.removeItem("for_print");
		}
	})
</script>
@endif
@endpush