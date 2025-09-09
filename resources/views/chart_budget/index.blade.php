@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
	#table_budget th,#table_budget td{
		padding: 3px;
		font-weight: bold;
	}
	#table_budget{
		font-size: 0.8rem;
	}
	#table_budget tfoot th{
		background: #808080;
		color: white;
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
</style>
<?php
$months = array();

for($i=1;$i<=12;$i++){
	$dt = date("M",strtotime("$selected_year-$i-01"));
	$months[$i] = $dt;
}
?>
<div class="container-fluid">
	<div class="card">
		<div class="card-body">
			<h3 class="lbl_color">Chart of Account Budget</h3>
			<form>
				<div class="row mt-3 d-flex align-items-end">
					<div class="col-md-3">
						<label class="lbl_color mb-0">Type</label>
						<select class="form-control form-control-border p-0" name="type">
							@foreach($types as $type)
							<option value="{{$type->id_chart_account_type}}" <?php echo($type->id_chart_account_type == $selected_type)?'selected':''; ?>>{{$type->description}}</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-2">
						<label class="lbl_color mb-0">Year</label>
						<select class="form-control form-control-border p-0" name="year">
							@for($i=2022;$i<=2050;$i++)
							<option value="{{$i}}" <?php echo($i == $selected_year)?'selected':''; ?>  >{{$i}}</option>
							@endfor
						</select>
					</div>
					<div class="col-md-2">
						<button class="btn bg-gradient-success2 round_button" type="submit">Filter</button>
					</div>
				</div>
			</form>

			<div class="row mt-3">
				<div class="col-md-12 col-12" style="max-height:calc(100vh - 150px);overflow-y: auto;">
					<table class="table table-bordered table-head-fixed" id="table_budget">
						<thead>
							<tr class="text-center lbl_color table_header_dblue">
								<th class="table_header_dblue">Chart Account</th>
								@foreach($months as $k=>$description)
								<th class="table_header_dblue">{{$description}}</th>
								@endforeach
								<th class="table_header_dblue">Total</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$foot_total = array_fill(1, 12, 0);
								$g_total = 0;

							?>
							@foreach($chart_accounts as $ca)
							<?php
							$total_row = 0;
							?>
							<tr class="chart_row" data-id="{{$ca->id_chart_account}}">
								<td style="white-space:nowrap;" class="text-xs col_chart">{{$ca->description}}</td>
								@foreach($months as $k=>$description)
								<?php
									if(isset($chart_budget[$ca->id_chart_account])){
										$amt =$chart_budget[$ca->id_chart_account][$k];
									}else{
										$amt = 0;
									}
									$total_row+=$amt;


									$foot_total[$k]+=$amt;
								?>
								<td class="p-0"><input class="form-control form-control-border text-sm p-0 text-right class_amount col_chart col_acc_amount" type="text" value="{{number_format($amt,2)}}" attr-month-key="{{$k}}"></td>
								@endforeach
								<?php
									$g_total += $total_row;
								?>
								<td class="p-0"><input class="form-control form-control-border text-sm p-0 text-right class_amount col_chart col_total" type="text" value="{{number_format($total_row,2)}}"></td>
							</tr>
							@endforeach
						</tbody>
						<tfoot>
							<tr>
								<th class="footer_fix font-total text-center">Total</th>
								@foreach($months as $k=>$description)
								<th class="footer_fix class_amount font-total total-foot-{{$k}} text-right" style="">{{number_format($foot_total[$k],2)}}</th>
								@endforeach
								<th class="footer_fix class_amount font-total total-foot text-right" style="">{{number_format($g_total,2)}}</th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
		<div class="card-footer">
			<button class="btn btn-md bg-gradient-success2 round_button float-right" onclick="save_budget()"><i class="fa fa-check"></i>&nbsp;Save</button>
		</div>
	</div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">

	$(function() {
		$.contextMenu({
			selector: '.chart_row',
			callback: function(key, options) {
				var m = "clicked: " + key;
				// var id_chart_account= $(this).attr('data-chart-id');
				if(key == "clear"){
					clear_amount($(this));
				}

			},
			items: {
				"clear": {name: "Clear Monthly Amount", icon: "fas fa-trash"},
				"sep1": "---------",
				"quit": {name: "Close", icon: "fas fa-times" }
			}
		});   
	});

	$("tr.chart_row").hover(
		function () {
			$(this).find('.col_chart').css("background","yellow");
		}, 
		function () {
			$(this).find('.col_chart').css("background","");
		}
		);
	$(document).on("focus",".class_amount",function(){
		var val = $(this).val();
		if(val == '' || val == 'NaN'){
			val = '0.00';
		}
		$(this).val(decode_number_format(val));	
	});
	$(document).on("blur",".class_amount",function(){
		var val = $(this).val();
		if(!$.isNumeric(val)){
			var decoded_val = decode_number_format(val);
			val = (!isNaN(decoded_val))?decoded_val:0;
		}
		$(this).val(number_format(parseFloat(val)));
	});

	$(document).on('keyup','.col_acc_amount',function(){
		var parent_row = $(this).closest('tr.chart_row');
		compute_budget_row(parent_row,false);

		sum_month($(this).attr('attr-month-key'));
	})

	function sum_month(month){
		total_month = 0;
		$(`input[attr-month-key="${month}"]`).each(function(){
			var val = decode_number_format($(this).val());
			total_month += val;
		});

		$('th.total-foot-'+month).text(number_format(total_month));
		sum_g_total();
	}

	function sum_g_total(){
		total = 0;
		$('.col_total').each(function(){
			var val = decode_number_format($(this).val());
			total += val;
		});

		$('.total-foot').text(number_format(total));
	}

	function compute_budget_row(parent_row,return_total){
		var total = 0;
		$(parent_row).find('input.col_acc_amount').each(function(){
			var val = $(this).val();
			val = ($.isNumeric(val))?val:decode_number_format(val);
			total += parseFloat(val);
		});
		console.log({total})
		total = isNaN(total)?0:total;

		if(!return_total){
			$(parent_row).find('.col_total').val(number_format(total,2));
		}else{
			return total;
		}	
	}
 
	$(document).on('blur','.col_total',function(){
		var parent_row = $(this).closest('tr.chart_row');
		var month_row = compute_budget_row(parent_row,true);
		var val = $(this).val();
		val = ($.isNumeric(val))?val:decode_number_format(val);
		var temp_amt = 0;
		if(month_row == 0){
			var x = roundoff(val/12);
			for(var i=1;i<=12;i++){
				if(i < 12){
					temp_amt +=x;
				}else{
					x = roundoff(val-temp_amt);
				}
				$(parent_row).find(`input[attr-month-key='${i}']`).val(number_format(x));
				sum_month(i);
			}
		}else{
			$(parent_row).find('.col_total').val(number_format(month_row,2));
		}
		
	});

	function clear_amount(obj){
		$(obj).find('input.class_amount').val('0.00');

		for(let i=1;i<=12;i++){
			sum_month(i);
		}
	}

	function save_budget(){
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
	}

	function post(){
		var chart_budget = [];
		$('tr.chart_row').each(function(){
			var data_id = $(this).attr('data-id');
			var temp = {};
			temp['id_chart_account'] = data_id;
			temp['monthly_amount'] = {};
			$(this).find('input.col_acc_amount').each(function(){
				temp['monthly_amount'][$(this).attr('attr-month-key')] = decode_number_format($(this).val());
			});
			chart_budget.push(temp);
			
		});

		console.log({chart_budget});
		$.ajax({
			type       :         'POST',
			url        :         '/chart/budget/post',
			data       :         {'year' : '{{$selected_year}}',
			'chart_budget' : chart_budget},
			beforeSend :         function(){
				show_loader();
			},
			success    :         function(response){
				console.log({response});
				hide_loader();
				Swal.fire({
					title: "Chart Account Budget Successfully Saved",
					text: '',
					icon: 'success',
					confirmButtonText: 'Close',
					confirmButtonColor: "#DD6B55",
					timer : 2000
				}).then(() => {
					location.reload();
				});
			},
			error: function(xhr, status, error) {
				hide_loader()
				var errorMessage = xhr.status + ': ' + xhr.statusText
				isClick = false;
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
@endpush