<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="author" content="">
  <meta name="description" content="">
  <meta name="keywords" content="">

  <link rel="shortcut icon" href="{{asset('assets/backend/images/favicon.png')}}">
  <link rel="apple-touch-icon"  href="{{asset('assets/backend/images/apple-touch-icon-57x57.png')}}">
  <link rel="apple-touch-icon" sizes="72x72" href="{{asset('assets/backend/images/apple-touch-icon-72x72.png')}}">
  <link rel="apple-touch-icon" sizes="114x114" href="{{asset('assets/backend/images/apple-touch-icon-114x114.png')}}">

  <title>TenSixFive Capital</title>


  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="{{asset('assets/backend/css/dashboard.css')}}" rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
    integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>

<body>


  <div class="js-animsition-overlay" data-animsition-overlay="true">
    <div class="wrapper" data-scroll-section>

      <div class="sidebar">
        <div class="logo-header">
           <a href="https://virtualpages.in/demo/tensixfive/html-login" class="header-logo__box1" target="_blank">
            <img class="header-logo__img" src="{{asset('assets/backend/images/logo/logo.png')}}" alt="logo">
          </a>
        </div>


      </div>

 <div class="content-area">
        <div class="login-box">
          <div class="login-inner">
            <h2 class="login-title">Briefcase</h2>
            <p class="py-30 text-danger"><b>{{ __('Please reset your password to login') }}</b></p>
             @include('includes.backend.form-both')
            <form class="login" id="forgotform" method="post" action ="{{route('backend.resetpass.store')}}" enctype="multipart/form-data">
               {{csrf_field()}}
              <div class="flex-container">
                <input type="hidden" name="user_id" value="{{$userData->id}}">

                <div class="twelve-columns">
                  <div class="form-input">
                    <input type="password" name="password" id="password" class="form-input" placeholder="{{ __('Enter New Password') }}" required>
                    <span toggle="#password" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                  </div>

                </div>

                <div class="twelve-columns">
                  <div class="form-input">
                    <input type="password" name="password_confirmation" id="password-confirm" class="form-input" placeholder="{{ __('Enter Confirm Password') }}" required>
                    <span toggle="#password-confirm" class="fa fa-fw fa-eye field-icon toggle-password"></span>

                  </div>

                </div>

                 <input id="authdata" type="hidden" value="{{ __('Authenticating...') }}">

                <div class="twelve-columns">

                  <button class="btns submit-btn" type="submit">
                    Submit
                  </button>

                </div>

              </div>
            </form>

          </div>

        </div>

      </div>

    </div>

  </div>


  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
  <script src="{{asset('assets/backend/js/dashboard.js')}}"></script>
  <script src="{{asset('assets/backend/js/backend/custom.js')}}"></script>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
</body>

</html>