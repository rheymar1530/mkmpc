<style type="text/css">
		#nav_search_main{
			display: none;left: 770px;width: 30%;
		}
		@media (max-width: 800px) {
			#nav_search_main{
				display: none;left: unset;width: 100%;
			}			
		}
</style>
<nav class="main-header navbar navbar-expand-lg navbar-light {{$dark_header ?? ''}} nav-custom">
    <a class="navbar-brand">
	    <img src="{{URL::asset('dist/img/LIBCAP_LOGO.jpg')}}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"  style="margin-top: 1px;">
	    <span class="brand-text font-weight-light"></span>
  	</a>
	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>
	<div class="collapse navbar-collapse" id="navbarSupportedContent">
		<ul class="navbar-nav mr-auto">
			<!-- <li><a href="#" class="dropdown-item dp_menu">SOA</a></li> -->
			<!-- <li><a href="#" class="dropdown-item">Some other action</a></li> -->
			<!-- <li class="dropdown-divider"></li> -->
			<!-- Level two dropdown-->
			@foreach(WebHelper::headerMenu() as $key=>$menu)
				@if(gettype($menu) == 'array')
					<li class="dropdown-submenu dropdown-hover">
						<a href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-item dropdown-toggle dp_menu">{{$key}}</a>
						<ul class="dropdown-menu border-0 shadow">
							@foreach($menu as $k=>$link)
								@if(gettype($link) == 'array')
									<li class="dropdown-submenu dropdown-hover">
										<a href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-item dropdown-toggle">{{$k}}</a>
										<ul aria-labelledby="dropdownSubMenu2" class="dropdown-menu border-0 shadow">
											@foreach($link as $j=>$sub)
												<li>
													<a tabindex="-1" href="{{$sub}}" class="dropdown-item dp_main_module">{{$j}}</a>
												</li>
											@endforeach
										</ul>
									</li>
								@else
									@if(str_contains($k, 'divider'))
										<li class="dropdown-divider"></li>
									@else
										<li><a tabindex="-1" href="{{$link}}" class="dropdown-item dp_main_module">{{$k}}</a> </li>
									@endif							
								@endif

							@endforeach
						</ul>
					</li>
				@else
					<li><a href="{{$menu}}" class="dropdown-item dp_menu dp_main_module">{{ $key }}</a></li>
				@endif
			@endforeach
			
		</ul>
<!-- 		<ul class="navbar-nav ml-auto" style="margin-right:20px;">
		    <li class="nav-item dropdown">
		    	<a class="nav-link dropdown-toggle" id="navbarDropdownMenuLink-4" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		    		
		    	{{ MySession::myName() }} </a>
		    	<div class="dropdown-menu dropdown-menu-right dropdown-cyan" aria-labelledby="navbarDropdownMenuLink-4">
		    		<a class="dropdown-item" href="/user/profile">My Account</a>
		    		<a class="dropdown-item" href="/dashboard">My Dashboard</a>
		    		<a class="dropdown-item" onclick=" Swal.fire({
		    			title: 'Do you want to logout ?',
		    			text: '',
		    			icon: 'warning',
		    			showCancelButton: true,
		    			confirmButtonColor: '#3085d6',
		    			cancelButtonColor: '#d33',
		    			confirmButtonText: 'Yes',
		    			cancelButtonText: 'No',
		    		}).then((result) => {
		    			if (result.isConfirmed) {
		    				window.location = '/logout';
		    			}
		    		})">Log out</a>
		    	</div>
		    </li>
		</ul> -->
	</div>
	<ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
<!--         <li class="nav-item"> 
        	<div class="navbar-search-block">
	    		<form class="form-inline">
	    			<div class="input-group input-group-sm">
	    				<input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
	    				<div class="input-group-append">
	    					<button class="btn btn-navbar" type="submit">
	    						<i class="fas fa-search"></i>
	    					</button>
	    					<button class="btn btn-navbar" type="button" data-widget="navbar-search">
	    						<i class="fas fa-times"></i>
	    					</button>
	    				</div>
	    			</div>
	    		</form>
	    	</div>
        </li> -->

<!--  --><!--         <li class="nav-item">
	        <a class="nav-link" data-widget="navbar-search" href="#" role="button">
	          <i class="fas fa-search"></i>
	        </a>
	        <div class="navbar-search-block" style="display: none;left: unset;width: 30%;">
					<div class="input-group input-group-sm" >
						<input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search" list="items-4412">
				
						<div class="input-group-append">
							<button class="btn btn-navbar" type="button" data-widget="navbar-search">
							<i class="fas fa-times"></i>
							</button>
						</div>
					</div>
	        </div>
        </li> -->
        <li class="nav-item">
        	<a class="nav-link" data-widget="navbar-search" href="#" role="button">
	          <i class="fas fa-search"></i>
	        </a>
					<div class="form-inline sidebar-search-open navbar-search-block" id="nav_search_main"   >
		        <div class="input-group">
		          <input class="form-control form-control-sidebar txt_search_module" type="search" placeholder="Search" aria-label="Search">
		          <div class="input-group-append">
		            <div class="input-group-append">
									<button class="btn btn-navbar" type="button" data-widget="navbar-search">
									<i class="fas fa-times"></i>
									</button>
								</div>
		          </div>
		        </div>
		        <div class="sidebar-search-results">
		        	<div class="list-group">
		        		<a href="/soa_monitoring" class="list-group-item">
		        			<div class="search-title"><strong class="text-light">SOA</strong> Monitoring</div><div class="search-path"></div>
		        		</a>
		        		<a href="/admin/soa/index" class="list-group-item">
		        			<div class="search-title"><strong class="text-light">SOA</strong> Control list</div><div class="search-path">Statement of Account</div>
		        		</a>
		        		<a href="/admin/generate_soa" class="list-group-item">
		        			<div class="search-title">Generate <strong class="text-light">SOA</strong></div><div class="search-path">Statement of Account</div></a>
		        		<a href="/admin/soa_template/create" class="list-group-item">
		        			<div class="search-title">Create <strong class="text-light">SOA</strong> Template</div><div class="search-path">SOA Template</div>
		        		</a>
		        	</div>
		        </div>
	     	  </div>
        </li>
        <li class="nav-item dropdown">
	    	<a class="nav-link dropdown-toggle" id="navbarDropdownMenuLink-4" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	    	{{ MySession::myName() }} </a>
	    	<div class="dropdown-menu dropdown-menu-right dropdown-cyan" aria-labelledby="navbarDropdownMenuLink-4">

	    		<a class="dropdown-item" href="/user/profile">My Account</a>
	    		<a class="dropdown-item" href="/dashboard">My Dashboard</a>
	    		<a class="dropdown-item" data-widget="control-sidebar" data-slide="true" href="#" role="button">Settings</a>
	    		<a class="dropdown-item" data-widget="fullscreen" href="#" role="button">Full Screen</a>

	    		<a class="dropdown-item" onclick=" Swal.fire({
	    			title: 'Do you want to logout ?',
	    			text: '',
	    			icon: 'warning',
	    			showCancelButton: true,
	    			confirmButtonColor: '#3085d6',
	    			cancelButtonColor: '#d33',
	    			confirmButtonText: 'Yes',
	    			cancelButtonText: 'No',
	    		}).then((result) => {
	    			if (result.isConfirmed) {
	    				window.location = '/logout';
	    			}
	    		})">Log out</a>
	    	</div>
		</li>

    </ul>
<!-- 	<form class="form-inline ml-0 ml-md-4" style="margin-right:15px">
		<div class="form-group">
			<input class="form-control search_nav col-md-12" type="search" placeholder="Search" aria-label="Search" style="margin-top:15px">
		</div>
	</form> -->
<!-- 	<ul class="navbar-nav">
		<li class="nav-item">
	    	<a class="nav-link" data-widget="navbar-search" href="#" role="button">
	    		<i class="fas fa-search"></i>
	    	</a>
	    	<div class="navbar-search-block">
	    		<form class="form-inline">
	    			<div class="input-group input-group-sm">
	    				<input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
	    				<div class="input-group-append">
	    					<button class="btn btn-navbar" type="submit">
	    						<i class="fas fa-search"></i>
	    					</button>
	    					<button class="btn btn-navbar" type="button" data-widget="navbar-search">
	    						<i class="fas fa-times"></i>
	    					</button>
	    				</div>
	    			</div>
	    		</form>
	    	</div>
	    </li>
	</ul> -->
</nav>

@push('scripts')
	<script type="text/javascript">
		$(".txt_search_module").keyup(function(){
			var val = $(this).val();
			if(val.length >= 3){
				console.log({val});
			}
			
		})
		function parseMenus(){
			var final = {};
		}
	</script>
@endpush
<!--    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <a class="navbar-brand font-bold" href="#">Navbar</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent-4" aria-controls="navbarSupportedContent-4" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent-4">
        <ul class="navbar-nav ">
          <li class="nav-item active">
            <a class="nav-link" href="#"><i class="fa fa-envelope"></i> Contact <span class="sr-only">(current)</span></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#"><i class="fa fa-gear"></i> Settings</a>
          </li>

        </ul>
        <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="navbarDropdownMenuLink-4" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-user"></i> Profile </a>
            <div class="dropdown-menu dropdown-menu-right dropdown-cyan" aria-labelledby="navbarDropdownMenuLink-4">
              <a class="dropdown-item" href="#">My account</a>
              <a class="dropdown-item" href="#">Log out</a>
            </div>
          </li>
        </ul>
      </div>
    </nav> -->