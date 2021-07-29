@extends('layouts.staff')
@section('content')

                <div class="bottom-bar">
                    <div class="breadcrumb-bar">

                        <ul class="breadcrumbs">
                            <li>
                                <a href="javascript:void(0)" class="btns  btns-default">{{ucfirst($clientData->name)}}</a>
                            </li>



                        </ul>

                        <div class="back-btn">
                            <a href="{{ route('backend.dashboard') }}" class="btns btns-default"><i class="fas fa-chevron-left"></i>
                                Back</a>
                        </div>


                    </div>
                </div>

                
                <div class="row">
                    <div class="col-lg-6 col-sm-12">
                        <div class="gray-box">
                            <div class="gray-inner">
                                <h2 class="section-title">Add New Project</h2>

                                <form class="client-form" id="client-form" method="post" action="{{ route('staff.add.project') }}">
                                   {{ csrf_field() }}
                                    @include('includes.backend.form-both')
                                    <div class="row">
                                        <input type="hidden" name="clientId" value="{{$clientData->id}}">
                                        <div class="col-lg-7 col-sm-6 col-12">
                                            <input type="text" class="form-control" name="projectName" placeholder="Enter Project Name">
                                        </div>

                                        <div class="col-lg-5 col-sm-6  col-12">
                                            <button type="submit" class="btns btns-default">Activate Project</button>
                                        </div>

                                    </div>
                                </form>


                                <h3 class="sub-title">Projects</h3>

                                <form class="projectForm">

                                    <div class="table-area">
                                        <table class="table table-striped projectTable table-responsive">
                                            <tbody>
                                                
                                                @if(!$projectDatas->isEmpty())
                                                  @foreach($projectDatas as $projectData)
                                                    <tr>
                                                        <td>{{ $projectData->name }}</td>
                                                        <td>{{ date("F j, Y", strtotime($projectData->created_at)) }}</td>
                                                        <td>
                                                          @if ($projectData->is_activated == 0)
                                                             <button class="btns btns-default" type="button" onclick="activateProject('<?php echo $projectData->id; ?>')">Activate</button>
                                                          @else
                                                            <button class="btns btns-danger" type="button" onclick="deactivateProject('<?php echo $projectData->id; ?>')">Deactivate</button>
                                                          @endif
                                                          
                                                        </td>
                                                    </tr>
                                                  @endforeach
                                                @else
                                                 <tr>
                                                    <td>No Project Added. </td>
                                                 </tr>
                                                @endif

                                            </tbody>
                                        </table>

                                    </div>
                                </form>



                            </div>
                        </div>
                    </div>



                    <div class="col-lg-6 col-sm-12">
                        <div class="gray-box">
                            <div class="gray-inner">
                                <h2 class="section-title">Add New User</h2>
                                <form class="client-form" id="verify-email-form" method="post" action="{{ route('staff.verify.email') }}">
                                  {{ csrf_field() }}
                                    @include('includes.backend.form-both')
                                    <div class="row">
                                        <div class="col-lg-7 col-sm-6 col-12">
                                            <input type="text" class="form-control" name="email" placeholder="Enter Email">

                                        </div>

                                        <div class="col-lg-5 col-sm-6  col-12">
                                              <button type="submit" class="btns btns-default">Activate User</button>
                                        </div>

                                    </div>
                                </form>


                                <h3 class="sub-title">Users</h3>

                                <form class="projectForm">

                                    <div class="table-area">
                                        <table class="table table-striped userTable table-responsive">
                                            <tbody>
                                                 @if(!$userDatas->isEmpty())
                                                @foreach ($userDatas as $userData)
                                                
                                                <tr>
                                                    <td>
                                                        <span>{{$userData->email}}</span>
                                                        <div class="listed-projects">
                                                            <ul>
                                                                @if($userData->assignedProjects->all_projects == 1)
                                                                  <li>All</li>
                                                                @elseif ($userData->assignedProjects->all_projects == 0 && $userData->assignedProjects->projects != NULL)
                                                                 @php $projectAssigns = json_decode($userData->assignedProjects->projects); @endphp
                                                                 @foreach ($projectAssigns as $assignedProject)
                                                                    <li>
                                                                      @foreach ($projectDatas as $projectData)
                                                                         {{$assignedProject == $projectData->id ? $projectData->name : ''}}
                                                                      @endforeach
                                                                    </li>
                                                                 @endforeach
                                                                @else
                                                                 <li>No Project Assigned</li>
                                                                @endif
                                                            </ul>



                                                            <a class="btns w-auto btns-default" data-toggle="modal" data-target="#editAccess" onclick="editProjectAssign('<?php echo $userData->id; ?>', '<?php echo $userData->email; ?>')">Edit Project
                                                                Access</a>

                                                        </div>
                                                    </td>

                                                    <td>
                                                        @if ($userData->is_activated == 0)
                                                             <button class="btns btns-default" type="button" onclick="activateUser('<?php echo $userData->id; ?>')">Activate</button>
                                                        @else
                                                            <button class="btns btns-danger" type="button" onclick="deactivateUser('<?php echo $userData->id; ?>')">Deactivate</button>
                                                        @endif
                                                
                                                        <a  class="btns btns-default"
                                                            data-toggle="modal" data-target="#resetUserPass" onclick="resetUserPasswordModal('<?php echo $userData->id; ?>', '<?php echo $userData->email; ?>')">Reset
                                                            Password</a>
                                                    </td>
                                                </tr>
                                                @endforeach   
                                                @else
                                                 <tr>
                                                    <td>No User Added. </td>
                                                 </tr>
                                                @endif    


                                                
                                            </tbody>
                                        </table>

                                    </div>
                                </form>









                            </div>


                        </div>
                    </div>


                </div>

@endsection

@section('scripts')

<script>
    var SITE_URL = "{{URL::to('/')}}";
</script>

@endsection