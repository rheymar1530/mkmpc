<aside class="main-sidebar sidebar-dark-primary elevation-4 cust-sidebar-color border-radius-xl" style="background:linear-gradient(to right, #232526, #414345) !important;color: white;">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link text-center">
      <!-- <img src="<?php echo e(URL::asset('dist/img/LIBCAP_LOGO.jpg')); ?>" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8"> -->
      <span class="brand-text font-weight-light"><?php echo e(env('APP_NAME')); ?></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar l-navbar" style="display: block; overflow-y: auto; max-height: calc(100vh - 120px);overflow-x:hidden;">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="<?php echo e(MySession::myPhoto()); ?>" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block"><?php echo e(MySession::myName()); ?></a>
        </div>
      </div>

      <!-- SidebarSearch Form -->
      <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column nav-compact" data-widget="treeview" role="menu" data-accordion="false">

          <?php $__currentLoopData = WebHelper::sidebarMenu(0); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $href = (empty($menu->children))?" href= '$menu->path'":'';    ?>
            <li data-id='<?php echo e($menu->id); ?>' class='nav-item <?php echo e((!empty($menu->children))?"treeview":""); ?>'>
              <a <?php echo $href; ?> class="nav-link ">
                <i class="nav-icon <?php echo e($menu->icon); ?>"></i>
                <p>
                  <?php echo e($menu->name); ?>

                  <i class='right <?php echo e((!empty($menu->children))?"fas fa-angle-left":""); ?>'></i>
                </p>
              </a>
              <?php if(!empty($menu->children)): ?>
                <ul class="nav nav-treeview">
                  <?php $__currentLoopData = $menu->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li data-id='<?php echo e($child->id); ?>' class="nav-item">
                      <a  href="<?php echo e($child->path); ?>" class="nav-link">
                        <i class="<?php echo e($child->icon); ?> child_nav"></i>
                        <p><?php echo e($child->name); ?></p>
                      </a>
                    </li>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> 
                </ul>
              <?php endif; ?>
            </li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <?php
          $report = WebHelper::sidebarMenu(0,1);
        ?>

        <?php if(count($report) > 0): ?>
         <li class="nav-header">Reports</li>
          <?php $__currentLoopData = $report; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $href = (empty($menu->children))?" href= '$menu->path'":'';    ?>
            <li data-id='<?php echo e($menu->id); ?>' class='nav-item <?php echo e((!empty($menu->children))?"treeview":""); ?>'>
              <a <?php echo $href; ?> class="nav-link ">
                <i class="nav-icon <?php echo e($menu->icon); ?>"></i>
                <p>
                  <?php echo e($menu->name); ?>

                  <i class='right <?php echo e((!empty($menu->children))?"fas fa-angle-left":""); ?>'></i>
                </p>
              </a>
              <?php if(!empty($menu->children)): ?>
                <ul class="nav nav-treeview">
                  <?php $__currentLoopData = $menu->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li data-id='<?php echo e($child->id); ?>' class="nav-item">
                      <a  href="<?php echo e($child->path); ?>" class="nav-link">
                        <i class="<?php echo e($child->icon); ?> child_nav"></i>
                        <p><?php echo e($child->name); ?></p>
                      </a>
                    </li>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> 
                </ul>
              <?php endif; ?>
            </li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>

          <?php
            $maintenance = WebHelper::sidebarMenu(1);
          ?>

        <?php if(count($maintenance) > 0): ?>
         <li class="nav-header">Maintenance</li>
          <?php $__currentLoopData = $maintenance; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $href = (empty($menu->children))?" href= '$menu->path'":'';    ?>
            <li data-id='<?php echo e($menu->id); ?>' class='nav-item <?php echo e((!empty($menu->children))?"treeview":""); ?>'>
              <a <?php echo $href; ?> class="nav-link ">
                <i class="nav-icon <?php echo e($menu->icon); ?>"></i>
                <p>
                  <?php echo e($menu->name); ?>

                  <i class='right <?php echo e((!empty($menu->children))?"fas fa-angle-left":""); ?>'></i>
                </p>
              </a>
              <?php if(!empty($menu->children)): ?>
                <ul class="nav nav-treeview">
                  <?php $__currentLoopData = $menu->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li data-id='<?php echo e($child->id); ?>' class="nav-item">
                      <a  href="<?php echo e($child->path); ?>" class="nav-link">
                        <i class="<?php echo e($child->icon); ?> child_nav"></i>
                        <p><?php echo e($child->name); ?></p>
                      </a>
                    </li>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> 
                </ul>
              <?php endif; ?>
            </li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
        <?php if(MySession::myPrivilegeID() == 1): ?>
          <li class="nav-header">Admin Access</li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-user-circle"></i>
              <p>
                Account User
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a class="nav-link" href="/user/index">
                  <i class="fas fa-bars nav-icon child_nav"></i>&nbsp;
                  <p>View Users</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/user/add" class="nav-link">
                  <i class="far fa-plus-square nav-icon child_nav"></i>&nbsp;
                  <p>Add User Account</p>
                </a>
              </li>
            </ul>
          </li>
          <!--/ Menu Management-->
          <li class="nav-item">
            <a href="/admin/menu_management" class="nav-link">
              <i class="nav-icon fas fa-bars"></i>
              <p>
                Menu Management
              </p>
            </a>
          </li>

<!--           <li class="nav-item">
            <a href="/admin/privilege/index" class="nav-link">
              <i class="nav-icon far fa-list-alt"></i>
              <p>Privilege Role</p>
            </a>
          </li> -->
          <li class="nav-item">
            <a class="nav-link">
              <i class="nav-icon far fa-list-alt"></i>
              <p>
                Privilege Role
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a class="nav-link" href="/admin/privilege/index"  >
                  <i class="fas fa-bars nav-icon child_nav"></i>&nbsp;
                  <p >Privilege List</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/admin/privilege/add" class="nav-link">
                  <i class="far fa-plus-square nav-icon child_nav"></i>&nbsp;
                  <p>Add User Privilege</p>
                </a>
              </li>
            </ul>
          </li>
        <?php endif; ?>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->

    <style type="text/css">
      .child_nav{
          padding-left: 15px;
      }
    </style>

  </aside>


  <!--  -->
<?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/adminLTE/sidebar.blade.php ENDPATH**/ ?>