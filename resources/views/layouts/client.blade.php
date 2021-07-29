<!DOCTYPE html>
<html lang="en" xml:lang="en" xmlns= "http://www.w3.org/1999/xhtml">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="author" content="">
  <meta name="description" content="">
  <meta name="keywords" content="">
  <meta name="google" content="notranslate" />

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.0/min/dropzone.min.css">

</head>


<body>
 
    <div>
        <div class="wrapper">
            <div class="sidebar">
                <div class="logo-header">
                    <a href="https://virtualpages.in/demo/tensixfive/html-login" class="header-logo__box1" target="_blank">
                        <img class="header-logo__img" src="{{asset('assets/backend/images/logo/logo.png')}}" alt="logo">
                    </a>
                </div>
                <h2 class="sidebar-title">Briefcase</h2>
            </div>

            <div class="content-area">
                <!-- Header of content area start-->
                <div class="top-bar">
                    <div class="btn-nav">
                        <button class="btns btns-default"><i class="fas fa-user-alt"></i>
                            {{Auth::guard('user')->user()->email}}</button>
                           <button class="btns btns-default" role="button" data-toggle="modal" data-target="#reset">
                            <i class="fas fa-unlock-alt"></i> Reset Password
                        </button>
                        <a href="{{ route('backend.logout') }}" class="btns btns-default"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>

                @yield('content')                   

            </div>
        </div>
    </div>

    <<div class="modal resetPass fade" id="reset" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <button type="button" class="btns-danger close" data-dismiss="modal" aria-label="Close">X</button>

                <h2>Reset Password</h2>
                <h3>{{Auth::guard('user')->user()->email}}</h3>

                <form class="reset-form" id="reset_password" method="post" action="{{ route('staff.reset.password') }}">
                  {{ csrf_field() }}
                  @include('includes.backend.form-both')
                   <input type="hidden" name="user_id" value="{{Auth::guard('user')->user()->id}}">
                    <div class="form-group">
                        <label for="password">Enter your current password</label>
                         <div class="showPass">
                        <input type="password" class="form-control" name="currentPassword" id="currentPassword">
                        <span toggle="#currentPassword" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                    </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Enter new password</label>
                         <div class="showPass">
                        <input type="password" class="form-control" name="newPassword" id="newPassword">
                        <span toggle="#newPassword" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                    </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Re-enter new password</label>
                         <div class="showPass">
                        <input type="password" class="form-control" name="confirmPassword" id="confirmPassword">
                        <span toggle="#confirmPassword" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                    </div>
                    </div>

                    <div class="foot">
                        <button class="btns w-auto btns-default" type="submit">Reset</button>
                        <button class="btns w-auto btns-danger" data-dismiss="modal" aria-label="Close">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



 <!-- Delete Confirmation Modal -->
  <div class="modal resetPass fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <button type="button" class="btns-danger close" data-dismiss="modal" aria-label="Close">X</button>

        <h2>Confirm Delete File</h2>

        <form class="reset-form" id="delete-file-form" method="post" action="{{route('client.delete.file')}}">
         {{ csrf_field() }}
           <input type="hidden" id="file_id" name="file_id"> 
            Are you sure you want to delete the file?
          <div class="foot">
            <button class="btns w-auto btns-default" type="submit" id="delete-file">Delete</button>
            <button class="btns  w-auto btns-danger" data-dismiss="modal" aria-label="Close">Cancel</button>

          </div>

        </form>
      </div>
    </div>
  </div>


  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
  <script src="{{asset('assets/backend/js/dashboard.js')}}"></script>
  <script src="{{asset('assets/backend/js/dateformat.js')}}"></script>
  <script src="{{asset('assets/backend/js/backend/custom.js')}}"></script>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.0/dropzone.js"></script>

   @yield('scripts')
</body>

</html>