<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <style>


        .login-block {
/*            background: #DE6262;*/
/*            background: -webkit-linear-gradient(to bottom, #FFB88C, #DE6262);*/
/*            background: linear-gradient(to bottom, #FFB88C, #DE6262);*/
            padding: 50px 0;
            min-height: 100%;
            display: flex;
            align-items: center;

        }
        .sec_row{
            box-shadow: -6px 8px 15px 0px rgba(0,0,0,0.47) !important;
            -webkit-box-shadow: -6px 8px 15px 0px rgba(0,0,0,0.47);
            -moz-box-shadow: -6px 8px 15px 0px rgba(0,0,0,0.47);
             border-radius: 10px !important;
        }



        .container {
            background: #fff;
            border-radius: 10px;
/*            z-index: 1;*/
/*            box-shadow: 15px 20px 0px rgba(0, 0, 0, 0.1);*/
        }
    </style>
  <style>
    html, body, .container-fluid, .row, .col,.div-section {
        height: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .input-group-prepend .input-group-text{
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
        background-color: transparent;
        border-top-left-radius: 1rem !important;
        border-bottom-left-radius: 1rem !important;
        border-right: none;
        background-color: #f2f2f2;
        border-color: #f2f2f2 !important;
    }
    .input-bg{
        background-color: #f2f2f2;
        border-color: #f2f2f2 !important;
    }
    .rounded-input {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        border-top-right-radius: 1rem !important;
        border-bottom-right-radius: 1rem !important;
        border-left: none;
        background-color: #f2f2f2;
        border-color: #f2f2f2 !important;
    }
    .input-group-append .input-group-text {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        border-top-right-radius: 1rem !important;
        border-bottom-right-radius: 1rem !important;
        background-color: transparent;

        border-left: none;
        background-color: #f2f2f2;
        border-color: #f2f2f2 !important;
    }
    .rounded-input-pass {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        border-top-right-radius: 1rem !important;
        border-bottom-right-radius: 1rem !important;
        border-left: none;
        background-color: #f2f2f2;
        border-color: #f2f2f2 !important;
    }
    .rounded-input:focus,.input-bg:focus{
        outline: none !important;
        box-shadow: none !important;
        border-color:#f2f2f2 !important;
        background-color: #f2f2f2;
    }
    #particles-js {
        position: absolute;
        width: 100%;
        height: 100%;
        overflow: hidden;
    }
    .div-section {
        height: auto !important;
        display: flex;
        align-items: center;
        /* justify-content: center; */
        flex-direction: column;
    }
    .fnt-weight{
        font-weight: 600;
    }
    #div-log-logo{
        color: white;
    }
    .img-logo{
        height: 60vh;
    }
    .side-txt{
        font-size : 2rem;
        letter-spacing : 0.2rem;
    }
    @media (max-width: 767px) {
        .img-logo{
            height: 100%;width: 100%;
        }
        .div-section {
            height: auto !important;
        }
        #div-login-form{
            padding-top: 3rem;
            padding-bottom: 3rem;
        }
        .side-txt{
            font-size:1.7rem;
            letter-spacing : unset;
        }
        .main-container,.login-block{
            padding: 0 !important;
        }
    }
    #div-right {
        background-image: linear-gradient(to right, #0097b2, #7ed957);
/*        box-sizing :s*/
        position: relative;
        z-index: 1;
        overflow: hidden;
    }

    #div-right::after {
/*        content: "";
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background-color: #fff;
        border-radius: 10px;
        opacity: 0;
        z-index: -1;*/
    }
</style>
</head>
{!! NoCaptcha::renderJs() !!}
<body>
    <section class="login-block">
        <div class="container main-container">
            <div class="row sec_row">
            <div class="col-lg-6 col-md-6 col-12 order-1 order-lg-1 order-md-1 div-section">
                <div class="d-flex align-items-center justify-content-center h-100" id="div-login-form">
                    <div class="mx-auto">
                        <div class="text-center mb-4">
                            <h2 style="color: #1a8cff;font-weight: 600;">Welcome Back</h2>
                        </div>
                        @if ( Session::get('with_error') )
                        <div class='alert alert-danger'>
                            <ul class="py-0 my-0">
                                @foreach(Session::get('message') as $m)
                                <li>{{$m}}</li>
                                @endforeach
                            </ul>

                        </div>
                        @endif
                        <form autocomplete='off' method="post" action="{{ route('postLogin') }}" id="frm_login">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="redirect_url" value="{{ request('redirect_url') }}">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-user-circle"></i></span>
                                </div>
                                <input type="text" class="form-control rounded-input log-inputs" placeholder="Username" required name="email">
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                    </div>
                                    <input type="password" name="password" autocomplete="current-password" required="" id="txt_password" class="form-control class_pass input-bg" placeholder="Password">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="far fa-eye" id="togglePassword" style="cursor: pointer;"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                {!! NoCaptcha::display() !!}
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-block mt-2 float-right" style="border-radius: 1rem;font-size:1.2rem;letter-spacing:0.2rem" id="btn_sign_in">Sign In</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
                <div class="col-lg-6 col-md-6 col-12 order-1 order-lg-1 order-lg-1 div-section h-100" id="div-right">
                <div class="pt-5" id="div-log-logo">
                    <div class="text-center px-3">
                        <h3 class="fnt-weight side-txt mb-0">LEPSTA 1 MULTIPURPOSE</h3>
                        <h3 class="fnt-weight side-txt mb-0">COOPERATIVE</h3>
                    
                        <h4 class="mb-0 mt-2">Leon Central Elementary School</h4>
                        <h5 class="mb-0">Leon, Iloilo</h5>
                        <img src="{{URL::asset('dist/img/LEPSTA 1 LOGO.png')}}" class="img-logo">
                    </div>                        
                </div>
                </div>
            </div>
        </div>

    </section>
        <div class="card mx-3" style="border-radius: 10px;background: red;">
            <div class="card-body">fasdas</div>
        </div>
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>
<script src="{{URL::asset('plugins/particles/js/particles.js')}}" type='text/javascript'></script>
</body>
</html>
