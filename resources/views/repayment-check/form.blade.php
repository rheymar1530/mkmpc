@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
	.tbl_pdc th, .tbl_pdc td{
		padding: 0.2rem;
		font-size: 0.8rem;
	}
	.tbl_pdc th{
		padding: 0.4rem;
		font-size: 0.8rem;
	}
	.selected_rp{
		background: #ccffcc;
	}
</style>
<?php
$check_types = [1=>"On-date",2=>"Post dated"];
?>

<div class="container">
	<?php $back_link = (request()->get('href') == '')?'/repayment-check':request()->get('href'); ?>
	<a class="btn btn-default btn-sm round_button" href="{{$back_link}}" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Loan Payment Check List</a>
	<div class="card">
		<div class="card-body px-5">
			<div class="text-center">
				<h3 class="head_lbl">@if($opcode == 0)Create  @endif Loan Payment Check @if($opcode==1)<small>(ID# {{$details->id_repayment_check}})</small> @endif</h3>
			</div>
			<form>
				<div class="form-row row d-flex align-items-end">
					@if($opcode == 0)
					<div class="form-group-input col-md-3">
						<label class="lbl_color mb-0 text-sm">Baranggay/Branch</label>
						<select class="form-control form-control-border text-sm p-0" id="sel_branch" name="branch">	
							@foreach($branches as $branch)
							<option value="{{$branch->id_branch}}" <?php echo ($selected_branch == $branch->id_branch)?'selected':''; ?> >{{$branch->branch_name}}</option>
							@endforeach
						</select>
					</div>
					<div class="form-group-input col-md-4">
						<button class="btn btn-sm bg-gradient-primary2 round_button" onclick="search_collection()"><i class="fa fa-search"></i>&nbsp;Generate</button>
					</div>
					@else
					<div class="form-group-input col-md-3">
						<label class="lbl_color mb-0 text-md">Branch: {{$details->branch_name}}</label>
					</div>
					@endif
				</div>
			</form>
			<div class="row mt-3">
				<div class="col-lg-7 col-12">
					<input type="text" name="" class="txt_search_transaction form-control form-control-border" placeholder="Search from Loan Payment table..." onkeyup="search_rp(this)">
				</div>
			</div>
			<div class="table-responsive mt-2">

				<table class="table table-bordered table-head-fixed tbl_pdc w-100" id="tbl_pdc">
					<thead>
						<tr class="text-center">
							<th class="text-center"><input type="checkbox" id="sel-all-rp"></th>
							<th>Loan Payment ID</th>
							<th>Date</th>
							<th>Member</th>
							<th>Amount</th>
						</tr>
					</thead>
					@if(count($repayments) > 0)
					<?php
					$rp = array();
					?>
					@foreach($repayments as $list)
					<tr class="rp-row {{($list->checked == 1)?'selected_rp':''}}" data-id="{{$list->id_repayment_transaction}}">
						<td class="text-center"><input type="checkbox" class="chk-rp" <?php echo ($list->checked == 1)?'checked':''; ?>></td>
						<td><a href="/repayment/view/{{$list->repayment_token}}" target="_blank">{{$list->id_repayment_transaction}}</a></td>
						<td>{{$list->date}}</td>
						<td>{{$list->member}}</td>
						<td class="text-right">{{number_format($list->total_payment,2)}}</td>
						<?php $rp[$list->id_repayment_transaction] = floatval($list->total_payment); ?>

					</tr>
					@endforeach
					
					@else
					<tr>
						<td colspan="5" class="text-center">No Loan Payment Found</td>
					</tr>
					@endif
				</table>
			</div>
			<div class="card c-border">
				<div class="card-body px-4">
					<div class="form-row mt-3">
						<div class="form-group col-md-3">
							<label class="lbl_color text-sm mb-0">Transaction Date</label>
							<input type="date" class="form-control form-control-border frm-chk" value="{{$details->transaction_date ?? MySession::current_date()}}" name="transaction_date">
						</div>
						<div class="form-group col-md-6">
							<label class="lbl_color mb-0 text-sm">Remarks</label>
							<input type="text" class="form-control form-control-border frm-chk" value="{{$details->remarks ?? ''}}" name="remarks">
						</div>
						<div class="form-group col-md-3">
							<label class="lbl_color text-sm mb-0">Total Amount</label>
							<input type="text" class="form-control form-control-border text-right" value="{{number_format($details->total ?? 0,2)}}" id="txt-amount" disabled>
						</div>
					</div>

					<h5 class="lbl_color font-weight-normal mb-3">Check Details</h5>
					<div class="row pl-3">
						<div class="form-group col-md-2">
							<label class="lbl_color mb-0 text-sm">Check Type</label>
							<select class="form-control form-control-border p-0 frm-chk" name="check_type">
								@foreach($check_types as $val=>$desc)
								<option value="{{$val}}" <?php echo (($details->id_check_type ?? 1) == $val)?'selected':'';  ?> >{{$desc}}</option>
								@endforeach
							</select>
						</div>
						<div class="form-group col-md-4">
							<label class="lbl_color mb-0 text-sm">Bank</label>
							<select class="form-control form-control-border p-0 frm-chk" name="bank">
								@foreach($banks as $bank)
								<option value="{{$bank->id_bank}}" <?php echo (($details->id_bank ?? 1) == $bank->id_bank)?'selected':'';  ?> >{{$bank->bank_name}}</option>
								@endforeach
							</select>
						</div>
						<div class="form-group col-md-3">
							<label class="lbl_color mb-0 text-sm">Check Date</label>
							<input type="date" class="form-control form-control-border frm-chk" name="check_date" value="{{$details->check_date ?? MySession::current_date()}}">
						</div>
						<div class="form-group col-md-3">
							<label class="lbl_color mb-0 text-sm">Check No.</label>
							<input type="text" class="form-control form-control-border frm-chk" name="check_no" value="{{$details->check_no ?? ''}}">
						</div>
					</div>

				</div>
			</div>
		</div>
		<div class="card-footer">
			<button class="btn btn-md bg-gradient-success2 float-right" onclick="post()"><i class="fa fa-save"></i>&nbsp;Save</button>
		</div>

	</div>
</div>



@endsection



@push('scripts')
<script type="text/javascript">
	const BACK_LINK = `<?php echo $back_link; ?>`;
	const REPAYMENTS = jQuery.parseJSON('<?php echo json_encode($rp ?? []); ?>');
	const CURRENT_BRANCH = '{{$selected_branch}}';
	const ID_REPAYMENT_CHECK = '{{$details->id_repayment_check ?? 0}}';
	const OPCODE = '{{$opcode}}';
	$(document).on('click','input.chk-rp',function(){
		var checked = $(this).prop('checked');
		var parent_row =  $(this).closest("tr.rp-row");

		if(checked){
			parent_row.addClass('selected_rp');
		}else{
			parent_row.removeClass('selected_rp');
		}

		sum_chk();
	});	
	$(document).on('dblclick','tr.rp-row',function(){
		$(this).find('input.chk-rp').trigger('click');
	});	
	function sum_chk(){
		var total = 0;
		var selected_rp = [];
		$('tr.rp-row.selected_rp').each(function(){
			var data_id = $(this).attr('data-id');
			total += parseFloat(REPAYMENTS[data_id]?? 0);
			selected_rp.push(data_id);
		});
		console.log({total});
		$('#txt-amount').val(number_format(total,2));
		// $('#txt_total_remittance').val(number_format(total,2));
		return selected_rp;
	}

	$('#sel-all-rp').on('click',function(){
		var checked = $(this).prop('checked');

		if(checked){
			$('tr.rp-row:visible').addClass('selected_rp');
		}else{
			$('tr.rp-row:visible').removeClass('selected_rp');
		}
		$('input.chk-rp:visible').prop('checked',checked)
		sum_chk();
	});

	function search_rp(obj){
		var value = $(obj).val().toLowerCase();

		$("tr.rp-row").filter(function() {
			$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
		});
	}
	function post(){
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
				post_rc();
			}
		});
	}
	const post_rc = () =>{
		let selected_repayment = [];
		$('tr.rp-row.selected_rp').each(function(){
			var data_id = $(this).attr('data-id');
			selected_repayment.push(data_id);
		});
		let details = {};
		$('.frm-chk').each(function(){
			let key = $(this).attr('name');
			details[key] = $(this).val()
		});

		let ajaxParam = {
			'details' : details,
			'repayments' : selected_repayment,
			'id_branch' : CURRENT_BRANCH,
			'id_repayment_check' : ID_REPAYMENT_CHECK,
			'opcode' : OPCODE
		};

		$.ajax({
			type     	:  'GET',
			url     	:  '/repayment-check/post',
			data     	:  ajaxParam,
			beforeSend  :  function(){
				show_loader();
				$('.mandatory').removeClass('mandatory');
			},
			success  	:  function(response){
				console.log({response});
				hide_loader();
				if(response.RESPONSE_CODE == "SUCCESS"){
					Swal.fire({
						title: 'Loan Payment Check Successfully saved',
						icon: 'success',
						showCancelButton: false,
						showConfirmButton : false,
						cancelButtonText : 'Back to Loan Payment Check list',
						confirmButtonText: `Close`,
						allowOutsideClick: false,
						allowEscapeKey: false,
						timer : 2500
					}).then((result) => {
						
						window.location ='/repayment-check/view/'+response.ID_REPAYMENT_CHECK+"?href="+encodeURIComponent(BACK_LINK);
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

					let invalid_fields = response.invalid_fields ?? [];

					for(var i=0;i<invalid_fields.length;i++){
						$(`.frm-chk[name="${invalid_fields[i]}"]`).addClass('mandatory');
					}					   	
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

		console.log({ajaxParam});
	}
</script>
@endpush