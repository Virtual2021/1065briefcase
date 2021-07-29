@extends('layouts.staffsidebar')
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
                        <select class="select-dropdown" id="project-dropdown" onchange="cityChangedTrigger()">
                            @if(!$projectDatas->isEmpty())
                            @foreach ($projectDatas as $projectData)
                            <option value="{{$projectData->id}}" {{ isset($_GET['projectId']) && $_GET['projectId'] !='' && $_GET['projectId'] == $projectData->id ? 'selected' : ''}}>{{$projectData->name}}</option>
                             @endforeach
                            @else
                            <option value="">------ No Project Created -----</option>
                            @endif
                        </select>
                    </form>

                </div>
            </li>

            
                
                    @if(sizeof($subFolders) != 0) 
                    @if (!$subFolders->isEmpty())
                     @if (isset($_GET['folderId']) && $_GET['folderId'] !='')
                       @foreach ($subFolders as $subFolder)
                       <li>  
                         <a href="javascript:void(0)" class="btns btns-default">
                            <i class="fa fa-folder" aria-hidden="true"></i>
                            {{$subFolder->id == $_GET['folderId'] ? $subFolder->name : ''}}
                         </a>
                       </li>
                       @endforeach
                     @else
                     <li>
                     <a href="javascript:void(0)" class="btns btns-default">
                        <i class="fa fa-folder" aria-hidden="true"></i>
                         {{$subFolders[0]->name}}
                     </a>
                     </li>
                      @endif
                    @else
                    {{ 'No Folder' }}                    
                    @endif
                    @else
                            
                    @endif
        </ul>

        <div class="back-btn">

            <a href="{{ route('backend.dashboard') }}" class="btns btns-default"><i class="fas fa-chevron-left"></i>
                Back</a>
        </div>
    </div>
</div>
  @if(sizeof($subFolders) != 0) 
   @if(!$subFolders->isEmpty())
<div class="gray-box">
                    <div class="gray-inner">
                        <div class="dragger">

                            <form class="drag-file dropzone" action="{{route('staff.upload.files')}}" enctype="multipart/form-data" id="dropzone">
                            @csrf
                                <input type="hidden" name="project_id" id="project_id" value="{{ isset($_GET['projectId']) && $_GET['projectId'] !='' ? $_GET['projectId'] : $projectDatas[0]->id }}">
                                <input type="hidden" name="folder_id" id="folder_id" value="{{ isset($_GET['folderId']) && $_GET['folderId'] !='' ? $_GET['folderId'] : $subFolders[0]->id }}">
                                <input type="hidden" id="client_id" name="client_id" value="{{$clientData->id}}">
                                
                            </form>



                        </div>
                    </div>
                </div>


<div class="gray-box">
    <div class="gray-inner">

        <div class="search-band">
            <div class="total-documents">
                <p>No. of Documents: <span>{{count($projectFiles)}}</span></p>

            </div>

            <div class="search-form">
                <!-- <form class="search-form" action=""> -->
                    <div class="form-group">
                        <input type="text" class="form-control" id="searchvalue" placeholder="Search" value="{{ isset($_GET['keyword']) ? $_GET['keyword'] : '' }}">
                        <button class="search-btn" onclick="searchFile()">Search</button>
                    </div>
                <!-- </form> -->
            </div>

        </div>


        <div class="table-area">
            <table class="table table-striped table-responsive">

                <tbody>
                    @foreach ($projectFiles as $projectFile)
                    <tr>
                        <td>
                            <p><b>{{$projectFile->name}}</b></p>

                            <div class="doc-description">
                                <p>{{$projectFile->description}} 
                                   @if($projectFile->description != NULL)    
                                         <a href="javascript:void(0)" role="button" data-toggle="modal" data-target="#createDescription" class="create-update" onclick="modifyDescription('<?php echo $projectFile->id ?>', '<?php echo $projectFile->description ?>')">Modify Description</a>
                                    @else
                                        <a href="javascript:void(0)" role="button" data-toggle="modal" data-target="#createDescription" class="create-update">Add Description</a>
                                    @endif     
                                    </p>
                            </div>
                        </td>

                        <td>{{ date("F j, Y", strtotime($projectFile->created_at)) }}</td>
                        <td><button class="btns btns-danger"  onclick="deleteFile('<?php echo $projectFile->id; ?>')">Delete</button></td>
                        <td><a class="btns btns-default" href="{{ route('staff.download.file', $projectFile->id) }}" >Download</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>
</div>

@endif
@endif



@endsection

@section('scripts')

<script>
    var SITE_URL = "{{URL::to('/')}}";

    //Get Uploaded File Data for Staff in View Files

    //Get Drop down Value of Project Id
    //Clear Drop Zone
    function clearDropzone() {
        var myDropzone = Dropzone.forElement("#dropzone");
        myDropzone.removeAllFiles(true);
    }


    //Drag and Drop Files
    Dropzone.options.dropzone = {
        maxFilesize: 20,
        renameFile: function(file) {
            var dt = new Date();
            var time = dt.getTime();
            return file.name;
        },
        // acceptedFiles: ".jpeg,.jpg,.png,.gif,.pdf",
        dictDefaultMessage: '<div class="file-drop-area"><span class="file-msg"><img src="{{asset("assets/backend/images/upload.png")}}" alt="img" />Drag & Drop Your Files Here<br>OR</span><br><span class="fake-btn"><i class="fas fa-upload"></i> Click to select from you computer</span></div>',
        addRemoveLinks: false,
        timeout: 600000,
        success: function(file, response) {
            swal({
                title: "",
                text: response.data,
                type: "success"
            }).then(function() {
                clearDropzone();
                 window.location.reload();
            });
        },
        error: function(file, response) {
            return false;
        }
    };

    //Delete Files
    function deleteFile(id) {
        $('#file_id').val(id);
        $('#confirm-delete').modal('show');
    }


     function cityChangedTrigger () {
        let queryString = window.location.search;  // get url parameters
        let params = new URLSearchParams(queryString);  // create url search params object
        params.delete('projectId');  
        params.delete('folderId');
        params.append('projectId', document.getElementById("project-dropdown").value); 
        document.location.href = "?" + params.toString(); // refresh the page with new url

    }

     function searchFile () {
        let queryString = window.location.search;  // get url parameters
        let params = new URLSearchParams(queryString);  // create url search params object
        params.delete('keyword');
        params.append('keyword', document.getElementById("searchvalue").value); 
        document.location.href = "?" + params.toString(); // refresh the page with new url
    }

    function modifyDescription(file_id, description){
        $('#fileId').val(file_id);
        $('#fileDescription').val(description);
    }
</script>

@endsection
