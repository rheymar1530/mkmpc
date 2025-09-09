<table  class="table tbl_loan repayment-pad" style="white-space: nowrap;margin-top: 5px;border: 1px solid black !important;display: none;">
	<tr class="table_header_dblue">
		<th colspan="4" class="center">ACTIVE {{$service_details->name}} BALANCE</th>
	</tr>
	@foreach($active_multiple as $am)
	<tr>
		<td class="no_border pad-left" style="width: 10%;"><a href="/loan/application/approval/{{$am->loan_token}}" target="_blank">ID# {{$am->id_loan}}</a></td>
		<td class="col_amount no_border" style="width: 15%;">{{number_format($am->balance,2)}}</td>
		<td class="no_border" colspan="2"></td>
	</tr>
	@endforeach	
</table>

<div class="card c-border">
	<div class="card-header bg-gradient-primary2 py-1">
		<h5 class="text-center mb-0">Active {{$service_details->name}} Balance</h5>
	</div>
	<div class="card-body">
		<!-- <div class="text-center">
			<h5 class="lbl_color badge bg-light text-lg text-center">Active {{$service_details->name}} Balance</h5>
		</div> -->
		<table  class="table tbl_loan repayment-pad" style="white-space: nowrap;margin-top: 5px;">
			@foreach($active_multiple as $am)
			<tr>
				<td class="no_border pad-left" style="width: 10%;"><a href="/loan/application/approval/{{$am->loan_token}}" target="_blank">Loan ID# {{$am->id_loan}}</a></td>
				<td class="col_amount no_border" style="width: 15%;">{{number_format($am->balance,2)}}</td>
				<td class="no_border" colspan="2"></td>
			</tr>
			@endforeach	
		</table>
	</div>
</div>