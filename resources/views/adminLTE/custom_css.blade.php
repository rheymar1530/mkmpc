<style type="text/css">
	#content_section input:focus,#content_section select:focus,#content_section textarea:focus{
		border-bottom: 0.2rem solid #e91e63 !important;
		border-color:#e91e63 !important;
		outline: 0 none !important;	
	}
	#content_section input, #content_section textarea {
		-webkit-transition: all 0.40s ease-in-out;
		-moz-transition: all 0.40s ease-in-out;
		-ms-transition: all 0.40s ease-in-out;
		-o-transition: all 0.40s ease-in-out;
		outline: none;
	}
	*{
		font-family: Roboto,Helvetica,Arial,sans-serif;
	}
	.bg-gradient-dark{
		background-image: linear-gradient(195deg, #42424a 0%, #191919 100%) !important;
	}
	.bg-gray-200,.content-wrapper,.main-footer{
		background-color: #f0f2f5!important;
	}
	.main-footer{
		background-color: #fff;
		border-top: unset !important; 
		color: #869099;
		padding: 1rem;
	}
	.border-radius-xl {
		border-radius: 0.75rem;
		font-family: Roboto,Helvetica,Arial,sans-serif;
	}
	.layout-fixed .main-sidebar{
		left: 1rem;
		top: 1rem;
		margin-bottom: 15px;
	}
	.main-header{
		margin-top: 1rem !important;
	}
	.sidebar-collapse .main-sidebar, .sidebar-collapse .main-sidebar::before {
		margin-left: -265px;
	}
	@media (min-width: 1200px) {
		#content_section{

		}
	}
	@media (min-width: 768px) {
		body:not(.sidebar-mini-md):not(.sidebar-mini-xs):not(.layout-top-nav) .content-wrapper, body:not(.sidebar-mini-md):not(.sidebar-mini-xs):not(.layout-top-nav) .main-footer, body:not(.sidebar-mini-md):not(.sidebar-mini-xs):not(.layout-top-nav) .main-header {
			transition: margin-left .3s ease-in-out;
			margin-left: 280px;
		}
		.nav-sidebar{
			font-family: Roboto,Helvetica,Arial,sans-serif;
			font-size: 14px;
		}
	}

	@media (min-width: 992px) {
		.sidebar-mini.sidebar-collapse .content-wrapper, .sidebar-mini.sidebar-collapse .main-footer, .sidebar-mini.sidebar-collapse .main-header {
			margin-left: 7rem!important;

		}
		.main-sidebar {
			height: 94vh;
			overflow-y: hidden;
		}
	}

	.nav-item .active{

		background-image: linear-gradient(195deg,#e91e63,#e91e63);
		color: #fff;
	}
	.nav-link > p{
		color: #fff !important;
	}


	.card-header{
		border-top-left-radius: 0.75rem;
		border-top-right-radius: 0.75rem;
	}



	.c-rounded-border{
		border-radius: 0.75rem;
		box-shadow: 0 4px 6px -1px rgb(0 0 0 / 10%), 0 2px 4px -1px rgb(0 0 0 / 6%) !important;		
	}

	.main-sidebar{
		transition: 0.5s !important;
	}

	@media (max-width: 768px) {
		
	}
	



	.head_lbl2{
		font-family: Roboto,Helvetica,Arial,sans-serif;
		/*font-weight: 550;*/
		color: #344767;
		font-size: 18px;
	}

	.lbl_gen{
		color: #404040;
	}
	.table-head-fixed th {
	    background: #333333 !important;
	    /* background-image: linear-gradient(195deg, #42424a, #191919); */
	    color: #fff !important;
	}
</style>
