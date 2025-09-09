    <!-- Content Header (Page header) -->
    <div class="content-header" id="div_content_head">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            
            <h4><?php echo e($title ?? ''); ?></h4>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <?php if(isset($breadcrumbs)): ?>
                <?php 
                  $c = 0;
                  $max_count = count($breadcrumbs)-1;
                ?>
                <?php $__currentLoopData = $breadcrumbs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                  <li class="breadcrumb-item"><?php echo ($c == $max_count)?"$name":"<a href='$link'>$name</a>"; $c++ ?></li>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              <?php endif; ?>  
              <!-- <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Dashboard v3</li> -->
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
  
      <style type="text/css">
        #div_content_head{
          padding: 10px 5px 5px 10px !important;
        }
      </style>
<?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/adminLTE/content_header.blade.php ENDPATH**/ ?>