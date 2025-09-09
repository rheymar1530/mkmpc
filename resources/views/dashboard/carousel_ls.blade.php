@push('head')
<style type="text/css">
	.MultiCarousel { float: left; overflow: hidden; padding-top: 25px; width: 100%;}
	.MultiCarousel .MultiCarousel-inner { transition: 1s ease all; float: left; }
	.MultiCarousel .MultiCarousel-inner .item_c { float: left;}

	.MultiCarousel .leftLst, .MultiCarousel .rightLst { position:absolute; border-radius:50%;top:calc(50% - 20px); }
	.MultiCarousel .leftLst { left:0; }
	.MultiCarousel .rightLst { right:0; }
	.lead{
		font-size: 23px;
	}
	.loan_amt{
		font-size: 18px;
		font-weight: 500;
	}

	.ls_head{
		margin-top: -1.3rem;
	}
	.text-terms li{
		font-size: 14px;
	}
	.pill-shadow{
		box-shadow: 0 4px 20px 0 rgb(0 0 0 / 14%), 0 7px 10px -5px rgb(64 64 64 / 40%) !important
	}
	.head_round{
		/*border-top-left-radius: 25% !important;*/
		border-bottom-left-radius: 15%;
		border-bottom-right-radius: 15%;
	}
	.txt_desc{
		font-size: 18px;
	}
	.text-list{
		font-size: 14px;
	}

	/*.MultiCarousel .leftLst.over, .MultiCarousel .rightLst.over { pointer-events: none; background:#ccc; }*/
</style>
@endpush
<div class="container-fluid content-row">

	<div class="row">
		<div class="col-12 px-3">
			<div class="MultiCarousel" data-items="1,2,2,3"  id="MultiCarousel"  data-interval="1000">
				<div class="MultiCarousel-inner position-relative">
					@foreach($loan_services as $name=>$loan_service)

					<div class="item_c px-2">

						<div class="card c-border  h-100 crd-gradient">
							<div class="card-header text-center card-head-nb py-2 bg-ls-card head_round" style="">
								<h4 class="ls_head"><span class="badge rounded-pill bg-{{($loan_service[0]->id_loan_payment_type==1)?'light':'light'}} text-lg pill-shadow lbl_color">{{$loan_service[0]->payment_type}}</span></h4>
								<div class="text-center mt-2">

									<p class="lead text-bold text-light" style="text-shadow: 3px 3px #262626;">{{$name}}</p>
									<p class="mt-n2 loan_amt text-light">{{$loan_service[0]->amount}}</p>
								</div>
							</div>
							@if($loan_service[0]->new)
							<div class="ribbon-wrapper ribbon-lg">
								<div class="ribbon bg-gradient-success2">
									New	
								</div>
							</div>
							@endif
							<div class="card-body pt-2 pb-0">
								<div class="row p-0 mt-2" style="">
									@if($loan_service[0]->is_multiple == 1)
									<div class="col-12 pl-2">
										<i class="fas fa-star" style="color:#FFD700"></i>
										<span class="text-terms lbl_color ml-2 text-list">Open for new loaner</span>
									</div>
									@endif
									@if($loan_service[0]->is_multiple == 1)
									<div class="col-12 pl-2">
										<i class="fas fa-star" style="color:#FFD700"></i>
										<span class="text-terms lbl_color ml-2 text-list">Open for multiple application</span>
									</div>
									@endif
									@if($loan_service[0]->deduct_interest == 1)
									<div class="col-12 pl-2">
										<i class="fas fa-star" style="color:#FFD700"></i>
										<span class="text-terms lbl_color ml-2 text-list">Interest deducted on loan proceed</span>
									</div>
									@endif
								</div>
								<hr>

								<!-- <p class="lbl_color" style="font-size:18px">Interest Rate</p> -->
								<div class="text-center">
									<span class="text-dark w-30 mt-n5 mx-auto txt_desc lbl_color text-bold">Interest Rate</span>
								</div>
								@if(count($loan_service) > 0)

								<?php
								$terms_count = count($loan_service);

								$t = round($terms_count/2);

								if($t > 3){
									$chunked_ls = array_chunk($loan_service,$t);
								}else{
									$chunked_ls = array_chunk($loan_service,3);
								}
								?>
								<div class="row p-0 sect_terms">
									
									@foreach($chunked_ls as $cl)
									<div class="col-lg-6 col-12 pt-0 ">
										<ul class="mb-0 text-terms lbl_color text-left pl-4">
											@foreach($cl as $c)
											<li>{{$c->term_period}} - {{interest_rate_format($c->interest_rate)}}</li>
											@endforeach
										</ul>
									</div>
									@endforeach
									
								</div>
								@endif
								<hr>
								
								<div class="row mt-3">
									<!-- <span class="badge rounded-pill bg-light text-dark w-30 mt-n2 mx-auto text-lg lbl_color">Requirements</span> -->
									<span class="text-dark w-30 mt-n2 mx-auto txt_desc lbl_color text-bold">Requirements</span>
								</div>
								<div class="row p-0 sect_req">
									<div class="row p-0">
										<!-- CBU -->
										@if($loan_service[0]->cbu_amount > 0)
										<div class="col-12 pl-3">
											<i class="fas fa-check-circle" style="color:green"></i><span class="ml-3 text-list lbl_color">₱{{number_format($loan_service[0]->cbu_amount,2)}} required CBU
												@if($loan_service[0]->is_deduct_cbu ==1)
												<i>(Deficient Deducted)</i>
												@endif
											</span>
										</div>
										@endif

										<!-- AGE LIMIT -->
										@if($loan_service[0]->age > 0)
										<div class="col-12 pl-3">
											<i class="fas fa-check-circle" style="color:green"></i><span class="ml-3 text-list lbl_color">Age requirement up to {{$loan_service[0]->age}} y/o
											</span>
										</div>
										@endif

										<!-- COMAKERS -->
										@if($loan_service[0]->no_comakers > 0)
										<div class="col-12 pl-3">
											<i class="fas fa-check-circle" style="color:green"></i><span class="ml-3 text-list lbl_color">{{$loan_service[0]->no_comakers}} Comaker<?php echo($loan_service[0]->no_comakers>1)?"s":""?>
										</span>
									</div>
									@endif
									@if(isset($requirements[$loan_service[0]->id_loan_service]))
									@foreach($requirements[$loan_service[0]->id_loan_service] as $k)
									<div class="col-12 pl-3">
										<i class="fas fa-check-circle" style="color:green"></i><span class="ml-3 text-list lbl_color">{{ucfirst(strtolower($k->req_description))}}</span>
									</div>
									@endforeach
									@endif




								</div>

							</div>



						</div>
						<div class="card-footer text-center border-0" style="background-color: transparent;">
							<button class="btn btn-md bg-gradient-primary2 round_button col-6" onclick="window.open('/loan/application/create?loan_reference={{$loan_service[0]->id_loan_service}}','_blank')">Apply</button>
						</div>
					</div>
				</div>
				@endforeach
			</div>

			<button class="btn btn-primary leftLst"><i class="fa fa-chevron-left"></i></button>
			<button class="btn btn-primary rightLst"><i class="fa fa-chevron-right"></i></button>
		</div>
	</div>

</div>
</div>


@push('scripts')
<script type="text/javascript">
	

	$(document).ready(function () {
		var itemsMainDiv = ('.MultiCarousel');
		var itemsDiv = ('.MultiCarousel-inner');
		var itemWidth = "";

		$('.leftLst, .rightLst').click(function () {
			var condition = $(this).hasClass("leftLst");
			if (condition)
				click(0, this);
			else
				click(1, this)
		});

		ResCarouselSize();



		$(window).resize(function () {
			ResCarouselSize();
		});

    //this function define the size of the items
    function ResCarouselSize() {
    	var incno = 0;
    	var card_count = 10;
    	var dataItems = ("data-items");
    	var itemClass = ('.item_c');
    	var id = 0;
    	var btnParentSb = '';
    	var itemsSplit = '';
    	// var sampwidth = $(itemsMainDiv).width();
    	var sampwidth = $(itemsMainDiv).width();
    	var mobile = false;

    	console.log({sampwidth})
    	var bodyWidth = $('body').width();
    	$(itemsDiv).each(function (kk) {
    		console.log({kk})
    		id = id + 1;
    		var itemNumbers = $(this).find(itemClass).length;
    		btnParentSb = $(this).parent().attr(dataItems);
    		itemsSplit = btnParentSb.split(',');
    		$(this).parent().attr("id", "MultiCarousel" + id);


    		if (bodyWidth >= 1200) {
    			incno = itemsSplit[3];
    			itemWidth = sampwidth / incno;
    			$('.MultiCarousel').attr('data-slide',3)

    		}
    		else if (bodyWidth >= 992) {
    			incno = itemsSplit[2];
    			itemWidth = sampwidth / incno;
    			console.log(992);
    			$('.MultiCarousel').attr('data-slide',2)
    		}
    		else if (bodyWidth >= 768) {
    			incno = itemsSplit[1];
    			itemWidth = sampwidth / incno;
    			console.log(768);
    			$('.MultiCarousel').attr('data-slide',1)
    			mobile = true;
    		}
    		else {
    			incno = itemsSplit[0];
    			itemWidth = sampwidth / incno;
    			$('.MultiCarousel').attr('data-slide',1)
    			mobile = true;

    		}

    		console.log({incno})
    		$(this).css({ 'transform': 'translateX(0px)', 'width': itemWidth * itemNumbers });
    		$(this).find(itemClass).each(function () {
    			$(this).outerWidth(itemWidth);
    		});

    		$(".leftLst").addClass("over");
    		$(".rightLst").removeClass("over");

    	});
    	var max_h = 0;
    	var max_terms = 0;
    	$('.item_c').each(function(kk){
    		var h = $(this).height();
    		if(h > max_h){
    			max_h = h;
    		}
    		var terms_h = $(this).find('.sect_terms').height();
    		if(terms_h > max_terms){
    			max_terms = terms_h;
    		}
    	})

    	$('.item_c').css({'height':max_h})
    	// if(!mobile){
    	// 	$('.sect_terms').css({'height' : max_terms})
    	// }else{
    	// 	$('.sect_terms').css({'height' : 'auto'})
    	// }
    	
    	console.log({max_h,mobile})
    }


    //this function used to move the items
    function ResCarousel(e, el, s) {
    	var leftBtn = ('.leftLst');
    	var rightBtn = ('.rightLst');
    	var translateXval = '';
    	var divStyle = $(el + ' ' + itemsDiv).css('transform');
    	var values = divStyle.match(/-?[\d\.]+/g);
    	var xds = Math.abs(values[4]);
    	if (e == 0) {
    		translateXval = parseInt(xds) - parseInt(itemWidth * s);
    		$(el + ' ' + rightBtn).removeClass("over");

    		if (translateXval <= itemWidth / 2) {
    			translateXval = 0;
    			$(el + ' ' + leftBtn).addClass("over");
    		}
    	}
    	else if (e == 1) {
    		var itemsCondition = $(el).find(itemsDiv).width() - $(el).width();
    		translateXval = parseInt(xds) + parseInt(itemWidth * s);
    		$(el + ' ' + leftBtn).removeClass("over");

    		if (translateXval >= itemsCondition - itemWidth / 2) {
    			translateXval = itemsCondition;
    			$(el + ' ' + rightBtn).addClass("over");
    		}
    	}
    	$(el + ' ' + itemsDiv).css('transform', 'translateX(' + -translateXval + 'px)');
    }

    //It is used to get some elements from btn
    function click(ell, ee) {
    	var Parent = "#" + $(ee).parent().attr("id");
    	var slide = $(Parent).attr("data-slide");
    	ResCarousel(ell, Parent, slide);
    }

});

</script>
@endpush