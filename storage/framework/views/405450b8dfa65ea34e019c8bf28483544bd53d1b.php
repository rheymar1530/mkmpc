<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo e(env('APP_NAME')); ?></title>

  <!-- Google Font: Source Sans Pro -->
  <!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback"> -->
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">

  <!-- !! NoCaptcha::renderJs() !! -->
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <?php if( Session::get('with_error') ): ?>
    <div class='alert alert-danger'>
        <ul class="py-0 my-0">
            <?php $__currentLoopData = Session::get('message'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($m); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>

    </div>
   <?php endif; ?>
  <!-- /.login-logo -->
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <!-- <a href="index2.html" class="h1"><b>SMES</b>TCCO</a> -->
       <a href="index2.html" class="h1"><b><?php echo e(config('variables.coop_abbr')); ?></b></a>
    </div>
    <div class="card-body">
      <p class="login-box-msg">Sign in to start your session</p>

      <form autocomplete='off'  method="post" action="<?php echo e(route('postLogin')); ?>">
         <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>"/>
         <input type="hidden" name="redirect_url" value="<?php echo e(request('redirect_url')); ?>">
        <div class="input-group mb-3">
          <input type="text" class="form-control" placeholder="Username" name="email">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" placeholder="Password" name="password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
          <!-- !! NoCaptcha::display() !! -->
          </div>
          <!-- /.col -->
          <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block mt-2 float-right" id="btn_sign_in">Sign In</button>
          </div>
        </div>
        </div>
      </form>

<!--       <div class="social-auth-links text-center mt-2 mb-3">
        <a href="#" class="btn btn-block btn-primary">
          <i class="fab fa-facebook mr-2"></i> Sign in using Facebook
        </a>
        <a href="#" class="btn btn-block btn-danger">
          <i class="fab fa-google-plus mr-2"></i> Sign in using Google+
        </a>
      </div> -->
      <!-- /.social-auth-links -->

<!--       <p class="mb-1">
        <a href="forgot-password.html">I forgot my password</a>
      </p> -->
<!--       <p class="mb-0">
        <a href="register.html" class="text-center">Register a new membership</a>
      </p> -->
    </div>
    <!-- /.card-body -->
  </div>
  <!-- /.card -->
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
</body>
</html>
<?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/adminLTE/login.blade.php ENDPATH**/ ?>