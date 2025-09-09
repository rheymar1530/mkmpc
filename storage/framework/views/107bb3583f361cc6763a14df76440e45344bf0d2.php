  <!-- Navbar -->
  <!-- <nav class="main-header navbar navbar-expand navbar-white navbar-light"> -->
    <style type="text/css">
      .nav-custom-color{
        /*background:  linear-gradient(to right, #0f2027, #203a43, #2c5364);*/
        /*color : white !important;*/
      }
      .nav-custom-color a{
        /*color: white !important;*/
      }
    </style>

  <nav class="main-header navbar navbar-expand <?php echo e($dark_header ?? ''); ?> navbar-light nav-custom-color border-radius-xl px-2">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button" onclick="setTimeout(function(){
          $($.fn.dataTable.tables(true)).DataTable().columns.adjust();      
          }, 350);"><i class="fas fa-bars"></i></a>
      </li>

    </ul>
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Navbar Search -->
      <li class="nav-item">



      <?php
        $switch_priv = WebHelper::myPrivilegeSwitchList();
      ?>
      <?php if(count($switch_priv) > 0): ?>
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="fas fa-user-circle"></i>
          
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-item dropdown-header">Switch Privilege</span>
          <?php $__currentLoopData = $switch_priv; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sw): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="dropdown-divider"></div>
            <a data-id="<?php echo e($sw->id_cms_privilege_s); ?>" class="dropdown-item toggle-privilege">
              <i class="fas fa-users mr-2"></i> <?php echo e($sw->name); ?>

              
              <?php if(MySession::myPrivilegeId() == $sw->id_cms_privilege_s): ?>
              <span class="badge badge-success  float-right">Active</span>
              <?php endif; ?>
             
            </a>

          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

          
        </div>
      </li>
      <?php endif; ?>
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
          <i class="fas fa-th-large"></i>
        </a>
      </li>
  <li class="nav-item dropdown user user-menu">
    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
      <img src="<?php echo e(MySession::myPhoto()); ?>" class="user-image img-circle elevation-2" alt="User Image">
      <span class="hidden-xs"><?php echo e(MySession::myName()); ?></span>
    </a>
    <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
      <!-- User image -->
      <li class="user-header bg-primary">
        <img src="<?php echo e(MySession::myPhoto()); ?>" class="img-circle elevation-2" alt="User Image">
        <p>
          <?php echo e(MySession::myName()); ?> - <?php echo e(MySession::myPrivilegeName()); ?>

          <!-- <small>Member since Nov. 2012</small> -->
        </p>
      </li>
<!--       <li class="user-body">
        <div class="row">
          <div class="col-4 text-center">
            <a href="#">Followers</a>
          </div>
          <div class="col-4 text-center">
            <a href="#">Sales</a>
          </div>
          <div class="col-4 text-center">
            <a href="#">Friends</a>
          </div>
        </div>
      </li> -->
      <!-- Menu Footer-->
      <li class="user-footer">
        <div class="btn-block">
          <a href="/profile" class="btn btn-default btn-flat pull-left">Profile</a>
          <a class="btn btn-default btn-flat pull-right" onclick="      Swal.fire({
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
        })">Sign out</a>
        </div>
      </li>
    </ul>
  </li>
    </ul>
  </nav>
  <?php $__env->startPush('scripts'); ?>
  <script type="text/javascript">
    $('.toggle-privilege').on('click',function(){
      var data_id = $(this).attr('data-id');
      $.ajax({
        type          :     'POST',
        url           :     '/switch_privileges',
        data          :     {'switch_id'  : data_id},
        beforeSend    :     function(){
                            show_loader();
        },
        success       :     function(response){
          hide_loader();
          if(response.RESPONSE_CODE == "ERROR"){
            Swal.fire({
              title: response.message,
              text: '',
              icon: 'warning',
              showConfirmButton : false,
              timer : 2500
            }); 
          }else if(response.RESPONSE_CODE == "SUCCESS"){
            location.reload();
          }
          console.log({response});
        },error: function(xhr, status, error) {
        hide_loader()
        var errorMessage = xhr.status + ': ' + xhr.statusText;
        Swal.fire({
          title: "Error-" + errorMessage,
          text: '',
          icon: 'warning',
          confirmButtonText: 'OK',
          confirmButtonColor: "#DD6B55"
        });
      }
      })
      
    })
  </script>
  <?php $__env->stopPush(); ?>

  <!-- /.navbar --><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/adminLTE/header.blade.php ENDPATH**/ ?>