<p class="nb"><b>Loan ID: </b>{{$details->id_loan}}</p>
<p class="nb"><b>Loan Service: </b>{{$details->service_name}}</p>
@if($details->terms != "")
<p class="nb"><b>Terms: </b>{{$details->terms}}</p>
@endif
<p class="nb"><b>Principal Amount: </b>{{number_format($details->principal_amount,2)}}</p>
<p class="nb"><b>Interest Rate: </b>{{$details->interest_rate}}%</p>
<p class="nb"><b>Date Submitted: </b>{{$details->date_submitted}}</p>

@if($show_complete)
<br>
<a class="btn-link button" href="{{$currentDomain}}/loan/application/view/{{$details->loan_token}}">Click for the complete details</a>
@endif