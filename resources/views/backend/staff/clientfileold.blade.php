@extends('layouts.staff')
@section('content')

                <div class="bottom-bar">
                    <div class="breadcrumb-bar">

                        <ul class="breadcrumbs">
                            <li>
                                <a href="javascript:void(0)" class="btns btns-default">
                                    {{ucfirst($clientData->name)}}</a>
                            </li>

                            <li>
                         <div class="project-listed-form">
                    <form>
                        <select class="select-dropdown">
                             @if(!$projectDatas->isEmpty())
                                          @foreach ($projectDatas as $projectData)
                                            <option value="{{$projectData->id}}">{{$projectData->name}}</option>
                                          @endforeach
                                          @else
                                            <option value="">------ No Project Created -----</option>
                                          @endif
                        </select>
                    </form>

                </div>
                </li>

                    <li>
                    <a href="javascript:void(0)" class="btns btns-default">
                    <i class="fa fa-folder" aria-hidden="true"></i> Folder 1</a>
                </li>
                </ul>

                        <div class="back-btn">
                            
                            <a href="{{ route('backend.dashboard') }}" class="btns btns-default"><i class="fas fa-chevron-left"></i>
                                Back</a>
                        </div>
                    </div>
                </div>


                <div id="exTab1">
                    <ul class="nav nav-pills">
                        <li class="active">
                            <a href="#tab1" data-toggle="tab">{{$clientData->name}} Has Sent You Files</a>
                        </li>
                        <li><a href="#tab2" data-toggle="tab">Send Files to {{$clientData->name}}</a>
                        </li>

                    </ul>

                    <div class="tab-content clearfix">
                        <div class="tab-pane active" id="tab1">

                             <div class="gray-box">
                                <div class="gray-inner">
                                <div class="table-area">
                                    <table class="table table-striped table-responsive">

                                        <tbody id="clientFiles">
                                            

                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>

                        <div class="tab-pane" id="tab2">



                             <div class="gray-box ">
                                <div class="gray-inner">

                                <div class="table-area scroller">
                                    <table class="table table-striped table-responsive">

                                        <tbody id="staffFiles">
                                          

                                        </tbody>


                                    </table>


                                </div>
                            </div>


                        </div>

                        <div class="gray-box">
                          <div class="gray-inner">

                            <div class="dragger">

                                <form class="drag-file dropzone" action="{{route('staff.upload.files')}}" enctype="multipart/form-data" id="dropzone">
                                     @csrf
                                     <input type="hidden" name="project_id" id="project_id">
                                     <input type="hidden" id="client_id" name="client_id" value="{{$clientData->id}}">
                                    <!-- <div class="file-drop-area">
                                        <span class="file-msg">Send Files to TenSixFiveCapital
                                            Drag & Drop Your Files Here<br>
                                            OR</span><br>
                                        <span class="fake-btn"><i class="fas fa-upload"></i> Click to select from you
                                            computer</span>
                                        <input class="file-input" type="file">
                                    </div> -->
                                </form>

                                </div>
                            </div>
                        </div>


                    
                    </div>
                </div>
            </div>




@endsection

@section('scripts')

<script>
    var SITE_URL = "{{URL::to('/')}}";

    //Get Uploaded File Data for Staff in View Files 

     //Get Drop down Value of Project Id
     
    var projectValue = $('#project_dropdown').find(":selected").val();
    
    if (projectValue != null || projectValue != undefined) {
        $('#project_id').val(projectValue);
        fileData(projectValue);
    }

    //Onchange get Dropdown value
    $(function () {
        $("#project_dropdown").change(function () {
            var selectedValue = $(this).find("option:selected").val();
            fileData(selectedValue);
             $('#project_id').val(selectedValue);
             clearDropzone();
        });
    });
   

    //Clear Drop Zone
    function clearDropzone(){
        var myDropzone = Dropzone.forElement("#dropzone");
        myDropzone.removeAllFiles(true);
    }

    //Get Files Data
    function fileData(id) {
       var url = SITE_URL + "/get/project/uploaded/files"+'/'+id;
       var client_id = $('#cient_id').val();

       let download_url = SITE_URL+"/staff/download/file/";
          $.ajax({
           headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: 'GET',
            url:  url,
            dataType: 'json',      //return data will be json
           success:function(response){
               if ((response.errors && response.errors != "")) {
               } else {
                   $("#staffFiles").empty();
                   $("#clientFiles").empty();
                  var staff_file_length = (response.staffFiles).length;
                  var client_file_length = (response.clientFiles).length;

                  //Append Data according to projects in table  //Staff
                  if(staff_file_length > 0){
                      var staffData = response.staffFiles;
                      $("#staffFiles").empty();
                       for(var i= 0; i< staff_file_length; i++){
                             //Date Format
                             var now = new Date(staffData[i].created_at);
                             now = dateFormat(now, "mmm d, yyyy");

                             $('#staffFiles').append('<tr><td>' + staffData[i].name +'</td><td>'+now+'</td><td><button class="btns btns-danger" onclick="deleteFile('+ staffData[i].id +')">Delete</button></td></tr>');                                            
                       }
                  }else{
                     $('#staffFiles').append('<tr><td>No File Uploaded.</td></tr>');
                  }

                  //Append Data according to projects in table //Client
                    if(client_file_length > 0){
                        var clientData = response.clientFiles; 
                       for(var i= 0; i< client_file_length; i++){
                            //Date Format
                            var now = new Date(clientData[i].created_at);
                            now = dateFormat(now, "mmm d, yyyy");

                            $('#clientFiles').append('<tr><td>' + clientData[i].name +'</td><td>'+now+'</td> <td><button class="btns btns-danger" onclick="deleteFile('+ clientData[i].id +')">Delete</button></td><td><a class="btns btns-default" href="'+download_url+''+clientData[i].id+'" >Download</a></td></tr>');
                       }
                    }else{
                        $('#clientFiles').append('<tr><td>No File Uploaded.</td></tr>');
                    }
                
                }
           }
        });
    }



    //Drag and Drop Files
      Dropzone.options.dropzone =
        {
            maxFilesize: 20,
            renameFile: function (file) {
                var dt = new Date();
                var time = dt.getTime();
                return file.name;
            },
            // acceptedFiles: ".jpeg,.jpg,.png,.gif,.pdf",
             dictDefaultMessage: '<div class="file-drop-area"><span class="file-msg"><img src="{{asset("assets/backend/images/upload.png")}}" alt="img"/>Send Files to TenSixFiveCapital Drag & Drop Your Files Here<br>OR</span><br><span class="fake-btn"><i class="fas fa-upload"></i> Click to select from you computer</span></div>',
            addRemoveLinks: false,
            timeout: 600000,
            success: function (file, response) {
                swal({
                    title: "",
                    text: response.data,
                    type: "success"
                }).then(function() {
                    fileData(response.project_id);
                    clearDropzone();
                });
            },
            error: function (file, response) {
                return false;
            }
        };

        //Delete Files 
        function deleteFile(id){
            $('#file_id').val(id);
             $('#confirm-delete').modal('show');
        }

</script>

@endsection
