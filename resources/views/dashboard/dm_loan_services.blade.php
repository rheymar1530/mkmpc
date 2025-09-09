
<div class="pr-2 col-lg-12 p-4" style="max-height: calc(137vh - 0px);overflow-y: auto;overflow-x: auto">
    @foreach($loan_services as $loan_service)
    <div class="card c-border gray-color crd-soa">
        <div class="card-body pb-0">
            <div class="col-lg-12 col-12 p-0">
                <div class="row p-0 pb-3">

                    <div class="col-lg-12 col-12 p-0">
                        <div class="col p-0">
                            <h4 class="mb-3 text-m"><span class="badge bg-gradient-dark">{{$loan_service[0]->name}}</span></h4>
                        </div>
                    </div>
                    <div class="col-lg-7 col-12 p-0">
                        <div class="d-flex flex-column">
                            <span class="txt-desc text-dark font-weight-bold lbl_color">Principal Amount: <span class="ms-sm-2 font-weight-normal ml-2">{{$loan_service[0]->amount}}</span></span>
            
                        </div>
                    </div>
                    <div class="col-lg-5 col-12 p-0">
                        <div class="d-flex flex-column">
                           
                            <span class="txt-desc text-dark font-weight-bold lbl_color">Payment Type: <span class="ms-sm-2 font-weight-normal ml-2">{{$loan_service[0]->payment_type}}</span></span>
                        </div>
                    </div>
                    <div class="col-lg-12 col-12 p-0">
                        <span class="txt-desc text-dark font-weight-bold lbl_color">{{($loan_service[0]->id_loan_payment_type==1)?'Terms':'Period'}}:  </span>
                           <!--  @if(count($loan_service) == 1)
                            <span class="txt-sm text-dark badge-dash badge bg-gradient-success2 mt-1">
                            {{$loan_service[0]->term_period}} - {{(floor($loan_service[0]->interest_rate) == $loan_service[0]->interest_rate)?number_format($loan_service[0]->interest_rate,0):$loan_service[0]->interest_rate}}%
                            </span> 
                            @endif -->
                       

                        @if(count($loan_service) > 0)

                        <?php
                        $terms_count = count($loan_service);

                        $t = round($terms_count/2);

                        if($t > 4){
                            $chunked_ls = array_chunk($loan_service,$t);
                        }else{
                            $chunked_ls = array_chunk($loan_service,4);
                        }
                        ?>


                        <div class="row p-0">
                            @foreach($chunked_ls as $cl)
                            <div class="col-lg-6 col-12 pl-4 pt-0">
                               <ul class="mb-0">
                                @foreach($cl as $c)
                                <li>
                                    <!-- /loan/application/create -->
                                    <a class="txt-sm  badge-dash badge bg-gradient-success2 mt-1" href="javascript:void(0)" target="_blank">{{$c->term_period}} - {{interest_rate_format($c->interest_rate)}}</a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endforeach

                        </div>
                        @endif


                </div>
            </div>
        </div>

    </div>
</div>
@endforeach
</div>