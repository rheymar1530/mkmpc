
<!DOCTYPE html>
<html lang="en">
@include('adminLTE.master')
<!--
`body` tag options:
  Apply one or more of the following classes to to the body tag
  to get the desired effect
  * sidebar-collapse
  * sidebar-mini
-->
  <?php 
    $dark_header = (MySession::WebSettings()['dark_mode'] ?? '')?'navbar-dark':'' ?? ''; 
    $dark_body = (MySession::WebSettings()['dark_mode'] ?? '')?'dark-mode':'' ?? ''; 
  ?>
<body class="hold-transition sidebar-mini {{$dark_body}} layout-fixed">



<div class="wrapper">
<div class="loader">
  <div class="child" style="width: 200px;height: 200px;">
    <div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>
    
  </div>
</div>
    <!-- Preloader -->
  <div class="preloader flex-column justify-content-center align-items-center">
    <div style="margin-left : -200px !important">
       <div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>
    </div>
    <!-- <img class="animation__shake" src="{{URL::asset('dist/img/img_trans.png')}}" alt="LIBCAP_LOGO" height="120px" width="400px"> -->
  </div>
  
  <!-- Content Wrapper. Contains page content -->
  <div class="">
      <section id='content_section' class="content">
          <!-- Main content -->
          <!-- <div class="content">          -->
              @yield('content')
          <!-- </div>  -->
          <!-- /.container-fluid -->
      </section>
    <!-- /.content -->
  </div>

  <!-- Main Footer -->

</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->

@include('adminLTE.plugins')
</body>
</html>
