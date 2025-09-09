@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
	@media (min-width: 1200px) {
		.container{
			max-width: 780px;
		}
	}
	.loan_head,.col_amount{
		font-size: 19px;
	}
	.row_loans{
		font-size: 19px;
	}
	.head_payment{
		font-size: 21px;
		/*font-weight: bold;*/
		font-weight: 2000 !important;
	}
	.text-bold{
		/*font-weight: 501 !important;*/
	}

</style>
<div class="container main_form">
		<?php $back_link = (request()->get('href') == '')?'/payments':request()->get('href'); ?>
	<a class="btn bg-gradient-secondary btn-sm" href="{{$back_link}}" style="margin-bottom:10px"><i class="fas fa-chevron-circle-left"></i>&nbsp;&nbsp;Back to Payment List</a>
	<div class="card">
		<div class="card-body">
			<h2 class="text-center lbl_color">Payment</h2>			
			<div class="row">
				<div class="col-12">
					<div class="card c-border">
						<div class="card-body">
							<div class="row lbl_color">
								<div class="col-lg-6 col-12">
									<div class="d-flex flex-column">
									<span class="text-lg"><span class="text-bold">Transaction ID:</span>  {{$repayment_transaction->id_repayment_transaction}}</span>
									<span class="text-lg"><span class="text-bold">Date :</span> {{$repayment_transaction->date}}</span>
									</div>
								</div>
								<div class="col-lg-6 col-12">
									<div class="d-flex flex-column">
									<span class="text-lg"><span class="text-bold">Transaction Type :</span> {{$repayment_transaction->transaction_type}}</span>
									<span class="text-lg"><span class="text-bold">OR No :</span> {{$repayment_transaction->or_no}}</span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-12 lbl_color">
					<table width="100%">
						<thead>
							<tr>
								<th width="3%"></th>
								<th width="8%"></th>
								<th></th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<!-- LOAN PAYMENTS -->
							<tr>
								<td colspan="4" class="head_payment">Loan Payments</td>
							</tr>

							<?php
								$amt_ar =['paid_principal'=>'Principal','paid_interest'=>'Interest','paid_fees'=>'Fees','surcharges'=>'Surcharges'];
								$total_payment = 0;
							?>
							@foreach($loan_payments as $lp)
							<tr class="loan_head" data-widget="expandable-table" aria-expanded="false" data-id ="L{{$lp->id_loan}}">
								<td></td>
							    <td colspan="2" class="row_loans">
							        <i class="expandable-table-caret fas fa-caret-right fa-fw"></i>
							        {{$lp->loan_service_name}}
							    </td>
							    <td class="text-right text-bold">{{number_format(($lp->paid_principal+$lp->paid_interest+$lp->paid_fees+$lp->surcharges),2)}}</td>
							    <td></td>
							</tr>

							@foreach($amt_ar as $key=>$description)
							@if($lp->{$key} > 0)
							<tr class="row_loans" aria-id ="L{{$lp->id_loan}}" style="display:none">
								<td colspan="2"><div class="div_loan" style="display:none"></div></td>
								<td><div class="div_loan" style="display:none">{{$description}}</div></td>
								<td class="text-right"><div class="div_loan" style="display:none">{{number_format($lp->{$key},2) }}</div></td>
								<?php
									$total_payment+=$lp->{$key};
								?>
							</tr>
							@endif
							@endforeach
							@endforeach
							<!-- END LOAN PAYMENTS -->
							@if($repayment_transaction->other_fees_charges > 0)
							<!-- OTHER FEES -->
							<tr>
								<td colspan="4" class="loan_head head_payment">Other Fees and Charges</td>
								<!-- <td class="text-right text-bold col_amount"></td> -->
							</tr>

							@foreach($other_fees_charges as $oth)
							<tr>
								<td></td>
								<td colspan="2" class="col_amount pl-1">{{$oth->description}}</td>
								<td class="text-right text-bold col_amount">{{number_format($oth->amount,2)}}</td>
							</tr>
								<?php
									$total_payment+=$oth->amount;
								?>
							@endforeach

							@endif
							<!-- END OTHER FEES AND CHARGES -->

							@if($repayment_transaction->total_rebates > 0)
							<tr>
								<td colspan="3" class="loan_head head_payment">Rebates</td>
								<td class="text-right text-bold col_amount">({{number_format($repayment_transaction->total_rebates,2)}})</td>
							</tr>
							<?php
								$total_payment-=$repayment_transaction->total_rebates;
							?>
							@endif

						</tbody>
						<tfoot>
							<tr style="border-top : 1px solid">
								<td colspan="3" class="loan_head head_payment">Total</td>
								<td class="text-right text-bold col_amount">{{number_format($total_payment,2)}}</td>
							</tr>

							@if($repayment_transaction->swiping_amount > 0)
							<tr>
								<td colspan="3" class="loan_head head_payment">Swiping Amount</td>
								<td class="text-right text-bold col_amount">{{number_format($repayment_transaction->swiping_amount,2)}}</td>
							</tr>
							<tr>
								<td colspan="3" class="loan_head head_payment">Change</td>
								<td class="text-right text-bold col_amount"><u>{{number_format($repayment_transaction->change,2)}}</u></td>
							</tr>
							@endif
						</tfoot>
					</table>
				</div>
			</div>

		</div>
	</div>
	
</div>
@endsection
@push('scripts')
<script type="text/javascript">
 $(document).on('click','.loan_head',function(){
    var exp = $(this).attr('aria-expanded');
    var parent_row = $(this);

    var id = $(this).attr('data-id');
    var rows = $(".row_loans[aria-id='"+id+"']");
    var divs = $(rows).find('.div_loan');

    if(exp == "true"){
     	$(rows).show();
        $(divs).slideDown("1000",function(){
               
        });
       
    }else{
         
        $(divs).slideUp("1000",function(){

            $(rows).hide();  
           
        });    
        
   }

})
</script>
@endpush