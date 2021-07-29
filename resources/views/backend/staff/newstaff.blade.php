@extends('layouts.staff')
@section('content')

        <div class="bottom-bar">
          <div class="breadcrumb-bar">
            <ul class="breadcrumbs">
              <li>
                <a href="javascript:void(0)" class="btns  btns-default">
                  Staff</a>
              </li>
            </ul>
          </div>
        </div>


        <div class="gray-box">
        <div class="gray-inner">
          <h2 class="section-title">Add New Staff</h2>

          <form class="client-form" id="add-staff" method="post" action="{{ route('staff.verify.email') }}">
           {{ csrf_field() }}
            @include('includes.backend.form-both')
            <div class="row">
              <div class="col-sm-6 col-12">
                <input type="text" class="form-control" name="email" placeholder="Email">
              </div>
              <div class="col-sm-6 col-12">
                <!-- <a href="javascript:void(0)" class="">Activate Account</a> -->
                <button type="submit" class="btns w-auto btns-default">Activate Account</button>
              </div>

            </div>
          </form>
        </div>
      </div>

    <div class="gray-box">
        <div class="gray-inner">
          <div class="table-area">
            <table class="table table-striped table-responsive">

              <tbody>
               @foreach ($getStaffDatas as $staffData)
                <tr>
                  <td>{{$staffData->email}}</td>
                  <td><a href="javascript:void(0)" class="btns btns-default" data-toggle="modal"
                      data-target="#resetUserPass" onclick="resetUserPasswordModal('<?php echo $staffData->id; ?>', '<?php echo $staffData->email; ?>')">Reset Password</a></td>
                  
                  <td>
                  @if($staffData->id != Auth::guard('user')->user()->id)
                   @if ($staffData->is_activated == 0)
                        <button class="btns btns-default" type="button" onclick="activateUser('<?php echo $staffData->id; ?>')">Activate</button>
                    @else
                        <button class="btns btns-danger" type="button" onclick="deactivateUser('<?php echo $staffData->id; ?>')">Deactivate</button>
                    @endif
                 @endif                      
                  </td>
                  
                </tr>
              @endforeach   
              </tbody>
            </table>

          </div>
        </div>
    </div>
@endsection

@section('scripts')

<script>
    var SITE_URL = "{{URL::to('/')}}";
</script>

@endsection
