
<!DOCTYPE html>
<html lang="en">
<?php echo $__env->make('adminLTE.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
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
<body class="hold-transition sidebar-mini <?php echo e($dark_body); ?> layout-fixed <?php echo e($sidebar ?? ''); ?> bg-gray-200">

<div class="wrapper" id="main_div">
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
    <!-- <img class="animation__shake" src="<?php echo e(URL::asset('dist/img/img_trans.png')); ?>" alt="LIBCAP_LOGO" height="120px" width="400px"> -->
  </div>
  <?php echo $__env->make('adminLTE.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
  <!-- Main Sidebar Container -->
  <?php echo $__env->make('adminLTE.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper bg-gray-200">
    <?php echo $__env->make('adminLTE.content_header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
      <section id='content_section' class="content">

          <!-- Main content -->
          <!-- <div class="content">          -->
              <?php echo $__env->yieldContent('content'); ?>
          <!-- </div>  -->
          <!-- /.container-fluid -->
      </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <?php echo $__env->make('adminLTE.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark" id="side_settings">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->

  <!-- Main Footer -->

</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->

<?php echo $__env->make('adminLTE.plugins', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</body>
</html>
<?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/adminLTE/admin_template.blade.php ENDPATH**/ ?>