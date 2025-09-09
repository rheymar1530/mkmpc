<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href=" {{URL::asset('plugins/fontawesome-free/css/all.min.css')}}">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href=" {{URL::asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href=" {{URL::asset('dist/css/adminlte.min.css')}}">
  <style>


    .login-block {
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



/*        .container {
            background: #fff;
            border-radius: 10px;

        }*/
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
            font-size : 1.65rem;
            letter-spacing : 0.2rem;
        }
        .b-rad-right{
            border-bottom-right-radius: 10px;
            border-top-right-radius: 10px;
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
            .login-block{
                padding-top: 1.4rem !important;
            }
/*            .main-container,.login-block{
                padding: 0 !important;
            }
            .container{
                padding: 0 !important;
            }
            .sec_row{
                margin-bottom: 0 !important;
            }*/
            .b-rad-right{
                border-radius: 0 0 10px 10px;
            }
        }
        #div-right {
            background-image: linear-gradient(to right, #0097b2, #7ed957);

        }


    </style>
</head>
{!! NoCaptcha::renderJs() !!}
<body style="background: #e9ecef;">
    <section class="login-block">
      <div class="container">
          <div class="card sec_row">
              <div class="card-body p-0">
                  <div class="row">
                    <div class="col-lg-5 col-md-5 col-12 order-1 order-lg-1 order-md-1 div-section">
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
                                        <div class="col-12 mt-3">
                                            <button type="submit" class="btn btn-primary btn-block mt-2 float-right" style="border-radius: 1rem;font-size:1.2rem;letter-spacing:0.2rem" id="btn_sign_in">Sign In</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7 col-md-7 col-12 order-1 order-lg-1 order-lg-1 div-section h-100 b-rad-right" id="div-right">
                         <div id="particles-js" class="h-100"></div>
                        <div class="pt-5" id="div-log-logo">
                            <div class="text-center px-3">
                                <h3 class="fnt-weight side-txt mb-0">LEPSTA 1 MULTIPURPOSE COOPERATIVE</h3>
                                <!-- <h3 class="fnt-weight side-txt mb-0">COOPERATIVE</h3> -->

                                <h4 class="mb-0 mt-0">Leon Central Elementary School</h4>
                                <h5 class="mb-0">Leon, Iloilo</h5>
                                <img src="{{URL::asset('dist/img/LEPSTA 1 LOGOx.png')}}" class="img-logo">
                            </div>                        
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</body>

    <script src="{{URL::asset('plugins/jquery/jquery.min.js')}}"></script>
    <script src="{{URL::asset('plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{URL::asset('dist/js/adminlte.min.js')}}"></script>
    <script src="{{URL::asset('plugins/particles/js/particles.js')}}" type='text/javascript'></script>
    <script>
      $(document).ready(function() {
        $('.rounded-input').focus(function() {
          $(this).siblings('.input-group-prepend').find('.input-group-text').addClass('spn-focus');
      });

        $('.rounded-input').blur(function() {
          $(this).siblings('.input-group-prepend').find('.input-group-text').removeClass('spn-focus');
      });

        $('.input-group-prepend').click(function() {
          $(this).addClass('spn-focus');
          $(this).siblings('.rounded-input').focus();
      });
    });
      const togglePassword = document.querySelector('#togglePassword');
      const password = document.querySelector('#txt_password');

      togglePassword.addEventListener('click', function (e) {
                                        // toggle the type attribute
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
                                        // toggle the eye slash icon
        this.classList.toggle('fa-eye-slash');
    });

      const togglePasswordRe = document.querySelector('#togglePasswordRe');
      const password_re = document.querySelector('#txt_re_password');

      togglePasswordRe.addEventListener('click', function (e) {
                                        // toggle the type attribute
        const type2 = password_re.getAttribute('type') === 'password' ? 'text' : 'password';
        password_re.setAttribute('type', type2);
                                        // toggle the eye slash icon
        this.classList.toggle('fa-eye-slash');
    }); 
</script>
<script type="text/javascript">particlesJS("particles-js", {
  "particles": {
    "number": {
      "value": 100,
      "density": {
        "enable": true,
        "value_area": 900
    }
},
"color": {
  "value": "#ffffff"
},
"shape": {
  "type": "circle",
  "stroke": {
    "width": 0,
    "color": "#000000"
},
"polygon": {
    "nb_sides": 5
},
"image": {
    "src": "img/github.svg",
    "width": 100,
    "height": 100
}
},
"opacity": {
  "value": 0.2,
  "random": false,
  "anim": {
    "enable": false,
    "speed": 1,
    "opacity_min": 0.1,
    "sync": false
}
},
"size": {
  "value": 6,
  "random": false,
  "anim": {
    "enable": false,
    "speed": 40,
    "size_min": 0.1,
    "sync": false
}
},
"line_linked": {
  "enable": true,
  "distance": 150,
  "color": "#ffffff",
  "opacity": 0.1,
  "width": 2
},
"move": {
  "enable": true,
  "speed": 1.5,
  "direction": "top",
  "random": false,
  "straight": false,
  "out_mode": "out",
  "bounce": false,
  "attract": {
    "enable": false,
    "rotateX": 600,
    "rotateY": 1200
}
}
},
"interactivity": {
    "detect_on": "canvas",
    "events": {
      "onhover": {
        "enable": false,
        "mode": "repulse"
    },
    "onclick": {
        "enable": false,
        "mode": "push"
    },
    "resize": true
},
"modes": {
  "grab": {
    "distance": 400,
    "line_linked": {
      "opacity": 1
  }
},
"bubble": {
    "distance": 400,
    "size": 40,
    "duration": 2,
    "opacity": 8,
    "speed": 3
},
"repulse": {
    "distance": 200,
    "duration": 0.4
},
"push": {
    "particles_nb": 4
},
"remove": {
    "particles_nb": 2
}
}
},
"retina_detect": true
});</script>
</body>
</html>
