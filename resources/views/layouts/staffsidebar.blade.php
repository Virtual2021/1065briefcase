<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US" > 

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
                <div class="side-nav">
                    <ul class="navbar">
                        <li><a href="{{ route('backend.dashboard') }}" id="client_menu"><i class="fas fa-user-tie"></i> Clients</a>
                        </li>
                        <li><a href="{{ route('get.staff') }}" id="staff_menu"><i class="far fa-id-badge"></i> Staff</a></li>
                    </ul>
                </div>
               
                @if(!$projectDatas->isEmpty())
                <div class="folderDiv">
                    <a href="javascript:void(0)" class="btns btns-default" role="button" data-toggle="modal"
                        data-target="#createFolder">
                        Create Folder</a>
                </div>
                @endif
                
                @if(sizeof($subFolders) != 0)    
                @if(!$subFolders->isEmpty())
                <div class="folder-nav">
                    <ul class="folder-nav-inner">
                        @foreach ($subFolders as $subFolder)
                        <li>
                            @if (isset($_GET['folderId']) && $_GET['folderId'] !='')
                            <a href="{{ request()->fullUrlWithQuery(['folderId' => $subFolder->id]) }}" class="{{ isset($_GET['folderId']) && $_GET['folderId'] !='' && $_GET['folderId'] == $subFolder->id ? 'active' : ''}}">
                                <i class="fa fa-folder" aria-hidden="true"></i>
                                {{$subFolder->name}}
                            </a>
                            @else
                            <a href="{{ request()->fullUrlWithQuery(['folderId' => $subFolder->id]) }}" class="{{ $subFolders[0]->id == $subFolder->id ? 'active' : ''}}">
                                <i class="fa fa-folder" aria-hidden="true"></i>
                                {{$subFolder->name}}
                            </a>
                            @endif
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
                @endif

            </div>



            <div class="content-area">
                <!-- Header of content area start-->
                <div class="top-bar">
                    <div class="btn-nav">
                        <button class="btns btns-default"><i class="fas fa-user-alt"></i> {{Auth::guard('user')->user()->email}}</button>
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

    <div class="modal resetPass fade" id="reset" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
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
                        <input type="password" class="form-control" name="newPassword">
                        <span toggle="#newPassword" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                    </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Re-enter new password</label>
                         <div class="showPass">
                        <input type="password" class="form-control" name="confirmPassword">
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


    <div class="modal resetPass fade" id="activateUser" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <button type="button" class="btns-danger close" data-dismiss="modal" aria-label="Close">X</button>

                <h2>Add New User</h2>
                <h3 id="activate_user_email"></h3>
                

                <form class="reset-form" id="user-form" method="post" action="{{ route('staff.add.user') }}">
                  {{ csrf_field() }}
                 @include('includes.backend.form-both')
                <input type="hidden" name="email" id="activate_user_email_input">
                <input type="hidden" name="clientId" value="{{isset($clientData->id) ? $clientData->id : null}}">
                    <div class="check-flex">
                        <input type="checkbox" id="passwordReset1" name="passwordReset1">
                        <label for="passwordReset1">Automatically create a password</label>
                    </div>
                    <div class="input-flex">
                        <label for="password">Password </label>
                         <div class="showPass">
                        <input type="password" class="form-control" id="activateuserPassword" name="password">
                        <span toggle="#activateuserPassword" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                    </div>
                    </div>

                    <div class="check-flex">
                        <input type="checkbox" id="passwordReset2" name="passwordReset2"
                            value="Require this user to change their password when they first sign in">
                        <label for="passwordReset2"> Require this user to change their password when they first sign
                            in</label>
                    </div>
                    <div class="check-flex">

                        <input type="checkbox" id="passwordReset3" name="passwordReset3"
                            value="Email the sign-in info to me">
                        <label for="passwordReset3"> Email the sign-in info to me</label>
                    </div>

                    <div class="foot">
                        <button class="btns w-auto btns-default" id="create_user" type="submit">Create User</button>
                        <button class="btns  w-auto btns-danger" data-dismiss="modal" aria-label="Close">Cancel</button>
                    </div>

                </form>




            </div>
        </div>
    </div>


    <!--Edit Project Access-->
    <div class="modal resetPass fade" id="editAccess" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <button type="button" class="btns-danger close" data-dismiss="modal" aria-label="Close">X</button>
                <h2>Choose Access For This Person</h2>
                <h3 id="edit_access_email"></h3>

                <form class="reset-form" id="edit-access-form" method="post" action="{{ route('staff.edit.project.assigned') }}">
                  {{ csrf_field() }}
                   @include('includes.backend.form-both')
                   <input type="hidden" id="editAccessuserId" name="editAccessuserId">

                    <div class="check-flex">
                        <input type="checkbox" id="all" name="all_projects">
                        <label for="all">All</label>
                    </div>
                    
                    @if(isset($activatedProjects))
                    @foreach ($activatedProjects as $projectData)
                    <div class="check-flex">
                        <input type="checkbox" id="project_assign_{{$projectData->id}}" name="project_assign[]" value="{{$projectData->id}}">
                        <label for="{{$projectData->name}}">{{$projectData->name}}</label>
                    </div>
                    @endforeach
                    @endif

                    <div class="foot">


                        <button class="btns w-auto btns-default">Update</button>

                    </div>

                </form>




            </div>
        </div>
    </div>

    <!-- Staff Reset User password -->
    <div class="modal resetPass fade" id="resetUserPass" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <button type="button" class="btns-danger close" data-dismiss="modal" aria-label="Close">X</button>

                <h2>Reset User Password</h2>
                <h3 id="staff_reset_email"></h3>

                <form class="reset-form" id="staff_user_reset_password" method="post" action="{{ route('staff.reset.user.password') }}">

                {{ csrf_field() }}
                 @include('includes.backend.form-both')
                  
                    <input type="hidden" name="resetuserId" id="resetuserId">
                    <div class="check-flex">
                        <input type="checkbox" class="form-group" id="passwordResetcheck" name="passwordReset1">
                        <label for="passwordReset1">Automatically create a password</label>
                    </div>
                    <div class="input-flex">
                        <label for="password">Password </label>
                         <div class="showPass">
                        <input type="password" class="form-control" id="staffresetuserPassword" name="password" >
                        <span toggle="#staffresetuserPassword" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                    </div>
                    </div>

                    <div class="check-flex">
                        <input type="checkbox" id="passwordReset2" name="passwordReset2">
                        <label for="passwordReset2"> Require this user to change their password when they first sign
                            in</label>
                    </div>
                    <div class="check-flex">

                        <input type="checkbox" id="passwordReset3" name="passwordReset3">
                        <label for="passwordReset3"> Email the sign-in info to me</label>
                    </div>

                    <div class="foot">
                        <button class="btns w-auto btns-default">Reset</button>
                        <button class="btns  w-auto btns-danger" data-dismiss="modal" aria-label="Close">Cancel</button>
                    </div>

                </form>




            </div>
        </div>
    </div>



    <!--Add Staff-->

<div class="modal resetPass fade" id="addStaff" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <button type="button" class="btns-danger close" data-dismiss="modal" aria-label="Close">X</button>

        <h2>Add New Staff</h2>
        <h3 id="add-staff-email"></h3>

        <form class="reset-form" id="staff-form" method="post" action="{{ route('add.staff') }}">
            {{ csrf_field() }}
            @include('includes.backend.form-both')
        <input type="hidden" name="email" id="activate_staff_email_input">
          <div class="check-flex">
            <input type="checkbox" id="staffpasswordResetcheck" name="passwordReset1">
            <label for="passwordReset1">Automatically create a password</label>
          </div>
          <div class="input-flex">
            <label for="password">Password </label>
             <div class="showPass">
            <input type="password" class="form-control" id="staffaddPassword" name="password">
            <span toggle="#staffaddPassword" class="fa fa-fw fa-eye field-icon toggle-password"></span>
        </div>
          </div>

          <div class="check-flex">
            <input type="checkbox" id="passwordReset2" name="passwordReset2">
            <label for="passwordReset2"> Require this user to change their password when they first sign in</label>
          </div>
          <div class="check-flex">

            <input type="checkbox" id="passwordReset3" name="passwordReset3">
            <label for="passwordReset3"> Email the sign-in info to me</label>
          </div>

          <div class="foot">


            <button class="btns w-auto btns-default" type="submit" id="create-staff">Create Staff</button>
            <button class="btns  w-auto btns-danger" data-dismiss="modal" aria-label="Close">Cancel</button>

          </div>

        </form>
      </div>
    </div>
  </div>
  <!-- Ehd of Add Staff- -->

  <!-- Delete Confirmation Modal -->
  <div class="modal resetPass fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <button type="button" class="btns-danger close" data-dismiss="modal" aria-label="Close">X</button>

        <h2>Confirm Delete File</h2>

        <form class="reset-form" id="delete-file-form" method="post" action="{{route('staff.delete.file')}}">
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

    <!-- create folder popup -->

    <div class="modal resetPass fade" id="createFolder" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <button type="button" class="btns-danger close" data-dismiss="modal" aria-label="Close">X</button>

                <h2>Create Folder</h2>

                <form class="reset-form" id="add_folder" method="post" action="{{route('staff.add.folder')}}">
                  {{ csrf_field() }}
                   @include('includes.backend.form-both')
                   <input type="hidden" name="projectId" value="{{ isset($_GET['projectId']) && $_GET['projectId'] !='' ? $_GET['projectId'] : isset($projectDatas[0]->id) }}">
                    <div class="form-group">
                        <label for="password">Enter Folder Name</label>
                        <input type="text" class="form-control" name="folderName">
                    </div>
                    <div class="foot">
                        <button class="btns w-auto btns-default" type="submit">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- create folder end -->

    <!-- Create Update Description -->
    <div class="modal resetPass fade" id="createDescription" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <button type="button" class="btns-danger close" data-dismiss="modal" aria-label="Close">X</button>

                <h2>Add/Edit Description</h2>

                <form class="reset-form" id="add_description" method="post" action="{{route('staff.add.description')}}">
                      {{ csrf_field() }}
                    @include('includes.backend.form-both')
                    <input type="hidden" id="fileId" name="fileId">
                    <div class="form-group">
                        <label for="password">Description</label>
                        <input type="text" class="form-control" name="description" id="fileDescription">
                    </div>
                    <div class="foot">
                        <button class="btns w-auto btns-default" type="submit">Save</button>
                        <button class="btns w-auto btns-danger" data-dismiss="modal" aria-label="Close">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Create Update Description -->



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