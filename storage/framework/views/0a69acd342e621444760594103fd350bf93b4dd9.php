<?php $__env->startSection('content'); ?>
  <?php if(Session::get('message') != ""): ?>
  <div class="alert alert-warning">
    <strong>Privilege Role Issue</strong> You dont have a permission to access this module.<br>
    	<a href="/admin"><< Back to main menu</a>
    
  </div>
  <?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminLTE.admin_template', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/dashboard/privilege_issue.blade.php ENDPATH**/ ?>