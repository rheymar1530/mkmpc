<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>"/>
  <title>
    <?php if(!env('TEST')): ?>
    <?php echo e($head_title ?? env('APP_NAME')); ?>

    <?php else: ?>
    MODULE
    <?php endif; ?>
</title>
  <!-- SMESTCC -->
  <!-- Google Font: Source Sans Pro -->

  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="<?php echo e(URL::asset('plugins/fontawesome-free/css/all.min.css')); ?>" />
  <!-- IonIcons -->
  <!-- <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css"> -->
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo e(URL::asset('dist/css/adminlte.min.css')); ?>" />
  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="<?php echo e(URL::asset('plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css')); ?>" />
  <!-- Toastr -->
  <link rel="stylesheet" href="<?php echo e(URL::asset('plugins/toastr/toastr.min.css')); ?>" />

  <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo e(URL::asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(URL::asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(URL::asset('plugins/datatables-buttons/css/buttons.bootstrap4.min.css')); ?>">


  <!-- Select2 -->
  <link rel="stylesheet" href="<?php echo e(URL::asset('plugins/select2/css/select2.min.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(URL::asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')); ?>">


  <!-- daterange picker -->
  <link rel="stylesheet" href="<?php echo e(URL::asset('plugins/daterangepicker/daterangepicker.css')); ?>">

  <!-- Bootstrap4 Duallistbox -->
  <link rel="stylesheet" href="<?php echo e(URL::asset('plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css')); ?>">
  <!-- BS Stepper -->
  <link rel="stylesheet" href="<?php echo e(URL::asset('plugins/bs-stepper/css/bs-stepper.min.css')); ?>">
  <!-- dropzonejs -->
  <link rel="stylesheet" href="<?php echo e(URL::asset('plugins/dropzone/min/dropzone.min.css')); ?>">

  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="<?php echo e(URL::asset('plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')); ?>">

  <!-- Context Menu -->
  <link rel="stylesheet" href="<?php echo e(URL::asset('plugins/context-menu/css/context-menu.css')); ?>">
  <style type="text/css">
    .card{
/*    box-shadow: 0px 1px 13px 0px rgba(0,0,0,0.75);
    -webkit-box-shadow: 0px 1px 13px 0px rgba(0,0,0,0.75);
    -moz-box-shadow: 0px 1px 13px 0px rgba(0,0,0,0.75);*/

    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 10%), 0 2px 4px -1px rgb(0 0 0 / 6%);
    border: 0 solid rgba(0, 0, 0, 0.125);
    border-radius: 0.75rem;
  }
  .card .card-header{
    border-top-left-radius: 0.75rem;
    border-top-right-radius: 0.75rem;
  }
  .card-no-shadow{
    box-shadow: 0 0 1px rgb(0 0 0 / 13%), 0 1px 3px rgb(0 0 0 / 20%) !important;
  }
  .custom_card_header{

    border-top-right-radius: 0.75rem;
    border-top-left-radius: 0.75rem;
    border: 0 solid rgba(0, 0, 0, 0.125);
    padding: 2px 2px 2px 10px !important;
  }
  .custom_card_header > h5{

    margin-bottom: unset;
    font-size:25px;
  }
  .custom_card_footer{
    padding: 5px 5px 5px 5px !important;
  }
  .dark-mode .mandatory{
    border-color: rgba(232, 63, 82, 0.8) !important;
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(232, 63, 82, 0.6) !important;
    outline: 0 none !important;
  }
  .invalid-text, .dark-mode .invalid-text{
    color: #ff6666!important;
    margin-left: 2px;
  }
  .mandatory{
    border-color: rgba(232, 63, 82, 0.8)!important;
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(232, 63, 82, 0.6)!important;
    outline: 0 none;
  }
  .mandatory:focus{
    border-color: rgba(232, 63, 82, 0.8) !important;
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset, 0 0 8px rgba(232, 63, 82, 0.6) !important;
    outline: 0 none !important;
  }
  .card-error{
    box-shadow: 1px 1px 12px 5px rgba(245,45,45,1);
    -webkit-box-shadow: 1px 1px 12px 5px rgba(245,45,45,1);
    -moz-box-shadow: 1px 1px 12px 5px rgba(245,45,45,1);
  }
</style>
<style type="text/css">
  /*RADIO BUTTON*********/
  .hide{
    display: none;
  }
  #content_section{
    padding-bottom: 40px;
    zoom : 90% !important;
  }
  *{
    font-family: Roboto,Helvetica,Arial,sans-serif;
  }
  .control-label{
    font-size: 12px;
    font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif !important;
  }
  .radio {
    margin: 0px 0;
    display: block;
    cursor: pointer;
  }
  .radio input {
    display: none;
  }
  .radio input + span {
    line-height: 22px;
    height: 22px;
    padding-left: 22px;
    display: block;
    position: relative;
  }
  .radio input + span:not(:empty) {
    padding-left: 30px;
  }
  .radio input + span:before, .radio input + span:after {
    content: '';
    width: 22px;
    height: 22px;
    display: block;
    border-radius: 50%;
    left: 0;
    top: 0;
    position: absolute;
  }
  .radio input + span:before {
    background: #D1D7E3;
    transition: background 0.2s ease, -webkit-transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 2);
    transition: background 0.2s ease, transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 2);
    transition: background 0.2s ease, transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 2), -webkit-transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 2);
  }
  .radio input + span:after {
    background: #fff;
    -webkit-transform: scale(0.78);
    transform: scale(0.78);
    transition: -webkit-transform 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.4);
    transition: transform 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.4);
    transition: transform 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.4), -webkit-transform 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.4);
  }
  .radio input:checked + span:before {
    -webkit-transform: scale(1.04);
    transform: scale(1.04);
    background: #5D9BFB;
  }
  .radio input:checked + span:after {
    -webkit-transform: scale(0.4);
    transform: scale(0.4);
    transition: -webkit-transform .3s ease;
    transition: transform .3s ease;
    transition: transform .3s ease, -webkit-transform .3s ease;
  }
  .radio:hover input + span:before {
    -webkit-transform: scale(0.92);
    transform: scale(0.92);
  }
  .radio:hover input + span:after {
    -webkit-transform: scale(0.74);
    transform: scale(0.74);
  }
  .radio:hover input:checked + span:after {
    -webkit-transform: scale(0.4);
    transform: scale(0.4);
  }
</style>

<style>


  .hide{
    display: none;
  }
  .delete_row:hover{
    color:red;
  }
  .dark-mode .col_table_input{
    background-color: #343a40 !important;
    color : #fff !important;
  }
  .dark-mode .head_search{
    background-color: #343a40 !important;
    color : #fff !important;
  }
  .drp-calendar td{
    color:black;
  }
  .dark-mode .select2-selection{
    background-color: #343a40 !important;
    color : #fff !important;
  }
  .dark-mode .select2-selection__rendered{
    color: #fff !important;
  }
  .select2-container .select2-selection--single{
   height:0%;
 }
 .form-control:not(textarea),.btn-sidebar,.input-group-append{
  height: 31px ;
}

.form-group{
  margin-top: -10px;
}
.select2-selection {
  height: 31px !important;
}
.select2-container .select2-selection--multiple {
  height: auto!important;
  margin: 0;
  padding: 0;
  line-height:inherit;
  border-radius:0;
}
.select2-container .select2-search--inline .select2-search__field {
  margin:0;
  padding:0;
  min-height:0;
}
.select2-container .select2-search--inline {
  line-height:inherit;
}

/* width */
::-webkit-scrollbar {
  height: 10px;
  width: 10px;
}

/* Track */
  /* ::-webkit-scrollbar-track {
    box-shadow: inset 0 0 5px grey; 
    border-radius: 10px;
  } */

  /* Handle */
  ::-webkit-scrollbar-thumb {
    background: #d6dee1;;
    border-radius: 20px;
    border: 6px solid transparent;
  }


  /* Handle on hover */
  ::-webkit-scrollbar-thumb:hover {
    background: #6666662b; 
  }
  /*DARK MODE*/
  /* Handle */
  .dark-mode ::-webkit-scrollbar-thumb {
    background: #f2f2f2; 
    border-radius: 10px;
  }
  .dark-mode ::-webkit-scrollbar-thumb {
    background: #f2f2f2; 
    border-radius: 10px;
  }

  /* Handle on hover */
  .dark-mode ::-webkit-scrollbar-thumb:hover {
    background: #f2f2f22b; 
  }

  .txt_head_search{
    height: 24px;
  }

</style>
<!-- LOADER -->
<style type="text/css">
  .shake{
    animation: shake 1s;
    animation-iteration-count: infinite;
  }
  .rotate{
    margin: auto;
    background-color: coral;
    color: white;
    animation: rotate 3s infinite;
  }
  @keyframes  shake {
    0% { transform: translate(1px, 1px) rotate(0deg); }
    10% { transform: translate(-1px, -2px) rotate(-1deg); }
    20% { transform: translate(-3px, 0px) rotate(1deg); }
    30% { transform: translate(3px, 2px) rotate(0deg); }
    40% { transform: translate(1px, -1px) rotate(1deg); }
    50% { transform: translate(-1px, 2px) rotate(-1deg); }
    60% { transform: translate(-3px, 1px) rotate(0deg); }
    70% { transform: translate(3px, 1px) rotate(-1deg); }
    80% { transform: translate(-1px, -1px) rotate(1deg); }
    90% { transform: translate(1px, 2px) rotate(0deg); }
    100% { transform: translate(1px, -2px) rotate(-1deg); }
  }
  @keyframes  rotate {
    50% {transform: rotate(360deg);}
  }
  .loader{
    display: flex;
    justify-content: center;
    opacity:0.8;
    background-color:#ccc;
    position: absolute;
    width:100%;
    height:100%;
    top:0px;
    left:0px;
    z-index: 9999999999999999999999999999999999999;
    display: none;
  }
  .child {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
  }
  .loader_img {
    display: block;
    margin-left: auto;
    margin-right: auto;
  }
</style>
<!-- TABLE CSS -->
<style type="text/css">
  .table_header{
    background-color: #4d4d4d;
    color : white;
  }
  .dark-mode .table_header{
    background-color: #8c8c8c;
    color : white;
  }
  .table_header_dblue{
    /* background: #007bff linear-gradient(180deg,#268fff,#007bff) repeat-x!important;
     color: #fff;*/
     /*background: #00264d linear-gradient(180deg,#003870,#00264d) repeat-x!important;*/
     /*background-color: #00264d;*/

     background-image: linear-gradient(195deg, #49a3f1 0%, #1A73E8 100%);
     color: #fff;
     /*box-shadow: 0 4px 20px 0 rgb(0 0 0 / 14%), 0 7px 10px -5px rgb(0 188 212 / 40%) !important;*/
     color : white;
   }
   .dark-mode .table_header_dblue{
    background: #3f6791 linear-gradient(180deg,#5c7ea2,#3f6791) repeat-x!important;
    color: #fff;
    
/*    background-color: #3498db;
    color : white;*/
  } 
  .dataTables_scrollHead table.dataTable th, table.dataTable tbody td {
    font-size: 13px;
    padding: 9px 1px 0px;
  }
  .head_search{
    height: 23px;
  }
  .head_rem{
    background-color: transparent;
  }
</style>
<!-- LOADER -->
<style type="text/css">
  .lds-ellipsis {
    display: inline-block;
    position: relative;
    width: 80px;
    height: 80px;
  }
  .lds-ellipsis div {
    /*left: 1px ;*/
    position: absolute;
    top: 33px;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    /*background: red;*/
    background: #ED213A;  /* fallback for old browsers */
    background: -webkit-linear-gradient(to right, #93291E, #ED213A);  /* Chrome 10-25, Safari 5.1-6 */
    background: linear-gradient(to right, #93291E, #ED213A); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */

    animation-timing-function: cubic-bezier(0, 1, 1, 0);
    box-shadow: 0 0 1px rgb(0 0 0 / 13%), 0 1px 3px rgb(0 0 0 / 20%) !important;
    border-color: white!important;
    border : solid 4px;
  }
  .lds-ellipsis div:nth-child(1) {
    left: 32px;
    /*left : 8px;*/
    animation: lds-ellipsis1 0.6s infinite;
  }
  .lds-ellipsis div:nth-child(2) {
    left: 32px;
    /*background: green !important;*/
    /*left : 8px;*/
    animation: lds-ellipsis2 0.6s infinite;
  }
  .lds-ellipsis div:nth-child(3) {
    left: 128px;
    /*left: 32px;*/
    animation: lds-ellipsis2 0.6s infinite;
  }
  .lds-ellipsis div:nth-child(4) {
    left: 224px;
    /*left: 56px;*/
    animation: lds-ellipsis3 0.6s infinite;
  }
  @keyframes  lds-ellipsis1 {
    0% {
      transform: scale(0);
    }
    100% {
      transform: scale(1);
    }
  }
  @keyframes  lds-ellipsis3 {
    0% {
      transform: scale(1);
    }
    100% {
      transform: scale(0);
    }
  }
  @keyframes  lds-ellipsis2 {
    0% {
      transform: translate(0, 0);
    }
    100% {
      transform: translate(96px, 0);
    }
  }
</style>
<style type="text/css">
  .navbar-nav a{
   font-family: 'Oswald', sans-serif;
   transition: all 0.2s ease-in-out;
 }
 .navbar-nav a:hover,.navbar-nav a:focus{
  background-color:#C8C8C86E;
  transform: scale(1.1);
}
.form-control-navbar,.form-control-navbar:focus{
 background-color: white !important;
 color: black !important;
}
.mt-n4-5{
  margin-top: -2.5rem!important;
}
.mt-n3-5{
  margin-top: -2rem!important;
}

</style>

<style type="text/css">
  .bg-gradient-success2 {
    background-image: linear-gradient(195deg, #66BB6A 0%, #43A047 100%);
    color : #fff !important;
    box-shadow: 0 4px 20px 0 rgb(0 0 0 / 14%), 0 7px 10px -5px rgb(76 175 80 / 40%)
  }
  .bg-gradient-danger2{
    background-image: linear-gradient(195deg, #EC407A 0%, #D81B60 100%);
    color : #fff !important;
    box-shadow: 0 4px 20px 0 rgb(0 0 0 / 14%), 0 7px 10px -5px rgb(233 30 99 / 40%)
  }
  .bg-gradient-dark {
    background-image: linear-gradient(195deg, #42424a 0%, #191919 100%);
    color : #fff !important;
    box-shadow: 0 4px 20px 0 rgb(0 0 0 / 14%), 0 7px 10px -5px rgb(64 64 64 / 40%) !important;
  }

  .bg-gradient-warning2{
    background-image: linear-gradient(195deg,#ffa726,#fb8c00);
    color : #fff !important;
    box-shadow: 0 4px 20px 0 rgba(0,0,0,.14),0 7px 10px -5px rgba(255,152,0,.4) !important;
  }
  .bg-gradient-primary2 {
    background-image: linear-gradient(195deg, #49a3f1 0%, #1A73E8 100%);
    color: #fff;
    box-shadow: 0 4px 20px 0 rgb(0 0 0 / 14%), 0 7px 10px -5px rgb(0 188 212 / 40%) !important;
  }
  .bg-gradient-info2 {
    background-image: linear-gradient(195deg,#49a3f1,#1a73e8);
    color: #fff;
    box-shadow: 0 4px 20px 0 rgba(0,0,0,.14),0 7px 10px -5px rgba(0,188,212,.4) !important;
  }
  .bg-gradient-danger3 {
    background-image: linear-gradient(195deg,#ef5350,#e53935);
    color: #fff !important;
    box-shadow: 0 4px 20px 0 rgba(0,0,0,.14),0 7px 10px -5px rgba(244,67,54,.4)!important;
  }
  .bg-light {
    background-color: #f0f2f5!important;
  }
  .card-cust {
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 10%), 0 2px 4px -1px rgb(0 0 0 / 6%);
    border: 0 solid rgba(0, 0, 0, 0.125);
    border-radius: 0.75rem;

  }
  .bg-gray-200 {
    background-color: #f0f2f5!important;
  }
</style>
<style type="text/css">
  .gray-color{
    background-color: #f8f9fa;
  }
  .head_lbl{
    font-family: Roboto,Helvetica,Arial,sans-serif;
    font-weight: 600;
    color: #344767;
    font-size: 22px;
  }
  .lbl_color{
    color: #344767 !important;
  }
  .lbl_gen{
    color: #404040;
  }
  .round_button{
    /*border-radius: 0.5rem*/
    border-radius: 0.5rem!important;
  }
  .round_button{
    border-radius: 1rem
/*    border-radius: 50rem!important;*/
}
.round_button_3{
  border-radius: 0.5rem
}
.c-border{
  border:1px solid rgba(0, 0, 0, 0.125)
}
.card-head-nb{
  border-bottom: unset !important;
}
.badge.bg-light {
  background: #f0f2f5;
}
.bg-light {
  background-color: #f0f2f5!important;
}
.btn:not(.btn-sidebar){
  border-radius: 0.5rem !important;
}
.section_body input:not(.w_border), .section_body select:not(.w_border),
.modal input, .modal select
{
  border-top: 0;
  border-left: 0;
  border-right: 0;
  border-radius: 0;
  box-shadow: inherit;
}
.section_body label,.modal label{
  color: #344767 !important;
  font-weight: 600 !important;
   font-size: 0.9rem !important;
}
.select2-selection:not(table .select2-selection){
  border: 0 !important;
  border-bottom: 1px solid !important;
  border-color: #d9d9d9 !important;
  border-radius: 0px !important;
  box-shadow: inherit !important;
}
.table thead th{
  text-align: center !important;
}
.table_header_dblue,.table-head-fixed thead th.table_header_dblue{
  background: #333333 !important;
  color : #fff !important;
}
    .nav-sidebar{
      font-family: Roboto,Helvetica,Arial,sans-serif;
      font-size: 0.9rem;
    }
  .btn-circle{
    border-radius: 1rem !important;
  }
  .swal2-deny{
    padding: 0.375rem 0.75rem !important;
  }
</style>
<?php echo $__env->make('adminLTE.custom_css', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->yieldPushContent('head'); ?>
</head><?php /**PATH C:\wamp64\www\maasin_live_act\resources\views/adminLTE/master.blade.php ENDPATH**/ ?>