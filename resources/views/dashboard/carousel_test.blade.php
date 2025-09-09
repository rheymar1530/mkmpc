@extends('adminLTE.admin_template')
@section('content')
<div class="container my-5">


	<!--Carousel Wrapper-->

	<!-- data-ride="carousel" -->
	<div id="multi-item-example" class="carousel slide carousel-multi-item" data-interval="false">

		<!--Controls-->
		<div class="controls-top">
			<a class="btn-floating float-left btn bg-gradient-primary2 btn-lg mb-4" href="#multi-item-example" data-slide="prev" style="border-radius:50%"><i class="fa fa-chevron-left"></i></a>
			<a class="btn-floating float-right btn bg-gradient-primary2 btn-lg mb-4" href="#multi-item-example" data-slide="next" style="border-radius:50%"><i class="fa fa-chevron-right"></i></a>
		</div>
		<!--/.Controls-->

		<!--Indicators-->
		<!-- <ol class="carousel-indicators">
			<li data-target="#multi-item-example" data-slide-to="0" class="active"></li>
			<li data-target="#multi-item-example" data-slide-to="1"></li>
			<li data-target="#multi-item-example" data-slide-to="2"></li>
		</ol> -->
		<!--/.Indicators-->

		<!--Slides-->
		<div class="carousel-inner  mt-3" role="listbox">
			<!--First slide-->
			<div class="carousel-item active">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-4">
							<div class="card mb-2 h-100">
							
								<div class="card-body">
									<h4 class="card-title">Card title 1</h4>
									<p class="card-text">Some quick example text to build on the card title and make up the bulk of the
									card's content.</p>
									<a class="btn btn-primary">Button</a>
								</div>
							</div>
						</div>

						<div class="col-md-4 clearfix d-none d-md-block">
							<div class="card mb-2 h-100">
								
								<div class="card-body">
									<h4 class="card-title">Card title 2</h4>
									<p class="card-text">Some quick example text to build on the card title and make up the bulk of the
									card's content.</p>
									<!-- <a class="btn btn-primary">Button</a> -->
								</div>
							</div>
						</div>

						<div class="col-md-4 clearfix d-none d-md-block">
							<div class="card mb-2 h-100">
								
								<div class="card-body">
									<h4 class="card-title">Card title 3</h4>
									<p class="card-text">Some quick example text to build on the card title and make up the bulk of the
									card's content.</p>
									<a class="btn btn-primary">Button</a>
								</div>
							</div>
						</div>
					</div>

				</div>
			</div>

			<!--/.Third slide-->
			<div class="carousel-item">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-4">
							<div class="card mb-2 h-100">
							
								<div class="card-body">
									<h4 class="card-title">Card title 4</h4>
									<p class="card-text">Some quick example text to build on the card title and make up the bulk of the
									card's content.</p>
									<a class="btn btn-primary">Button</a>
								</div>
							</div>
						</div>

						<div class="col-md-4 clearfix d-none d-md-block">
							<div class="card mb-2 h-100">
								
								<div class="card-body">
									<h4 class="card-title">Card title 5</h4>
									<p class="card-text">Some quick example text to build on the card title and make up the bulk of the
									card's content.</p>
									<!-- <a class="btn btn-primary">Button</a> -->
								</div>
							</div>
						</div>

						<div class="col-md-4 clearfix d-none d-md-block">
							<div class="card mb-2 h-100">
								
								<div class="card-body">
									<h4 class="card-title">Card title 6</h4>
									<p class="card-text">Some quick example text to build on the card title and make up the bulk of the
									card's content.</p>
									<a class="btn btn-primary">Button</a>
								</div>
							</div>
						</div>
					</div>

				</div>
			</div>

		</div>
		<!--/.Slides-->

	</div>
	<!--/.Carousel Wrapper-->


</div>
@endsection