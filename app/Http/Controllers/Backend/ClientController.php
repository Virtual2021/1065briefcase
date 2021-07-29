<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use App\Models\ProjectAssignUsers;
use App\Models\UploadedFile;
use App\Models\ProjectSubFolder;
use App\Rules\ConfirmPassword;
use Auth;
use Illuminate\Http\Request;
use Input;
use Validator;
use Hash;
use Mail;

class ClientController extends Controller
{

    public function __construct()
    {
    $this->middleware("guest");
    }

    public function projectDetail($id, $searchid=null){
        
        //Get Project and Client Data
        $projectData = Project::find($id);
        $clientData = Client::find($projectData->client_id);

        $userId = session('data.id');
        $userData = User::find($userId);
        //Get Project Assign to user
        $userProject = ProjectAssignUsers::where(['user_id' => $userId])->first();
        $projectDatas = [];
        if ($userProject->all_projects != null || $userProject->projects != null) {
            if ($userProject->all_projects == 1) {
                $projectDatas = Project::where(['is_activated' => 1, 'client_id' => $userData->client_id])->get();
            } else if ($userProject->all_projects == 0 && $userProject->projects != null) {
                $projectIds = json_decode($userProject->projects);
                $projectDatas = Project::where(['is_activated' => 1])->whereIn('id', $projectIds)->get();
            }
        }


        $subFolders = [];
        $projectFiles = [];

        $search = "";

        if (isset($_GET)) {

            //If Get All Three Parameters are Available
            if (isset($_GET['projectId']) && $_GET['projectId'] != "" && isset($_GET['folderId']) && $_GET['folderId'] != "" && isset($_GET['keyword']) && $_GET['keyword'] != "") {
                $subFolders = ProjectSubFolder::where(['project_id' => $_GET['projectId'], 'isDeleted' => 0])->get();
                $projectFiles = UploadedFile::where(['folder_id' => $_GET['folderId'], 'isdeleted' => 0])->where('name', 'LIKE', "%{$_GET['keyword']}%")->get();
            }

            //If Project and Folder are available
            if (isset($_GET['projectId']) && $_GET['projectId'] != "" && isset($_GET['folderId']) && $_GET['folderId'] != "" && (!isset($_GET['keyword']))) {
                $subFolders = ProjectSubFolder::where(['project_id' => $_GET['projectId'], 'isDeleted' => 0])->get();
                $projectFiles = UploadedFile::where(['folder_id' => $_GET['folderId'], 'isdeleted' => 0])->get();
            }

            //If Project and Keyword are available
            if (isset($_GET['projectId']) && $_GET['projectId'] != "" && isset($_GET['keyword']) && $_GET['keyword'] != "" && (!isset($_GET['folderId']))) {
                $subFolders = ProjectSubFolder::where(['project_id' => $_GET['projectId'], 'isDeleted' => 0])->get();
                if (!$subFolders->isEmpty()) {
                    $projectFiles = UploadedFile::where(['folder_id' => $subFolders[0]->id, 'isdeleted' => 0])->where('name', 'LIKE', "%{$_GET['keyword']}%")->get();
                }
            }

            //If Folder and Keyword are available
            if (isset($_GET['keyword']) && $_GET['keyword'] != "" && isset($_GET['folderId']) && $_GET['folderId'] != "" && (!isset($_GET['projectId']))) {
                $subFolders = ProjectSubFolder::where(['project_id' => $projectDatas[0]->id, 'isDeleted' => 0])->get();
                $projectFiles = UploadedFile::where(['folder_id' => $_GET['folderId'], 'isdeleted' => 0])->get();
            }

            //If Only Project are Available
            if (isset($_GET['projectId']) && $_GET['projectId'] != "" && !isset($_GET['folderId']) && (!isset($_GET['keyword']))) {
                $subFolders = ProjectSubFolder::where(['project_id' => $_GET['projectId'], 'isDeleted' => 0])->get();
                if (!$subFolders->isEmpty()) {
                    $projectFiles = UploadedFile::where(['folder_id' => $subFolders[0]->id, 'isdeleted' => 0])->get();
                }
            }

            //If Only Folder are available
            if (!isset($_GET['projectId']) && isset($_GET['folderId']) && $_GET['folderId'] != "" && (!isset($_GET['keyword']))) {
                $subFolders = ProjectSubFolder::where(['project_id' => $projectDatas[0]->id, 'isDeleted' => 0])->get();
                $projectFiles = UploadedFile::where(['folder_id' => $_GET['folderId'], 'isdeleted' => 0])->get();
            }

            //If Only Keyword is aavilabe
            if (!isset($_GET['projectId']) && !isset($_GET['folderId']) && isset($_GET['keyword']) && $_GET['keyword'] != "") {
                $subFolders = ProjectSubFolder::where(['project_id' => $projectDatas[0]->id, 'isDeleted' => 0])->get();
                $projectFiles = UploadedFile::where(['folder_id' => $subFolders[0]->id, 'isdeleted' => 0])->where('name', 'LIKE', "%{$_GET['keyword']}%")->get();
            }

            //No Parameters are Available except project
            if ((!isset($_GET['projectId'])) && (!isset($_GET['folderId'])) && (!isset($_GET['keyword']))) {
                if (!$projectDatas->isEmpty()) {
                    $subFolders = ProjectSubFolder::where(['project_id' => $id, 'isDeleted' => 0])->get();
                    if (!$subFolders->isEmpty()) {
                        $projectFiles = UploadedFile::where(['folder_id' => $subFolders[0]->id, 'isdeleted' => 0])->get();
                    }
                }
            }
        }
        return view('backend.client.projectdetail', compact('id', 'projectData' ,'projectDatas' , 'clientData', 'subFolders', 'projectFiles', 'search'));
    }

     //Get Project Files
    public function getClientFiles($id){
       
        $staffFiles = UploadedFile::where(['project_id' => $id, 'isdeleted' => 0, 'type' => 1])->get(['id', 'name', 'created_at'])->toArray();

        $clientFiles = UploadedFile::where(['project_id' => $id, 'isdeleted' => 0, 'type' => 2])->get(['id', 'name', 'created_at'])->toArray();
        
        $response = array('staffFiles' => $staffFiles, 'clientFiles' => $clientFiles);
        return response()->json($response);

    }


    //Client Upload Files
    public function uploadFiles(Request $request){
       $clientId = $request->client_id;
       $projectId = $request->project_id;
       $folderId = $request->folder_id;
       $folderName = 'client';

      $path = UploadedFile::myCreateDirectory($clientId, $projectId, $folderId, $folderName);

      $image = $request->file('file');
      $fileName = $image->getClientOriginalName();
     

      //Store Name in Database
     $newFile = new UploadedFile;
      $newFile->project_id = $projectId;
      $newFile->folder_id = $folderId;
      $newFile->name = $fileName;
      $newFile->type = 2;
      $newFile->uploaded_by = Auth::guard('user')->user()->id;
      $newFile->save();
      
      if(file_exists($path.'/'.$fileName)) unlink($path.'/'.$fileName);
      $msg = '';

      if($image->move($path, $fileName)){

        $details = [
                'subject' => 'Tensixfive Briefcase - New file uploaded',
                'folderId' => $folderId,
                'projectId' => $projectId,
                'fileName' => $fileName,
                'type' => 2,
            ];

            $job = (new \App\Jobs\SendQueueEmail($details))
                ->delay(now()->addSeconds(2));

            dispatch($job);

      $msg = 'File Uploaded Successfully';
      }else{
          $msg = "File not Uploaded Successfully.";
      }
      return response()->json(['success' => $fileName, 'project_id' => $projectId, 'data' => $msg]);
    }


    //Delete File 
    public function deleteFile(Request $request){
        $fileId = $request->file_id;

        //Get File Data
        $uploadedFile = UploadedFile::find($fileId);
        $uploadedFile->isdeleted = 1;
    
        $msg = "";
        $error = "";
        $resetform = "";
        $projectId = $uploadedFile->project_id; 
        if ($uploadedFile->save()) {
            $msg = 'File Deleted Successfully.';
        } else {
            $error = 'File is not deleted. Please try again.';
        }

        //--- Validation Section
        $response = array('data' => $msg, 'error' => $error, 'resetform' => $resetform, 'project_id' => $projectId);
        return response()->json($response);
        //--- Redirect Section Ends
    }

    //Download File
    public function downloadFile($id){
        //Get File Data
        $uploadedFile = UploadedFile::find($id);
        
        $projectId = $uploadedFile->project_id;
        $folderId = $uploadedFile->folder_id;
        $fileName = $uploadedFile->name;

        //Get Client Data 
        $projectData = Project::find($projectId);

        $clientId = $projectData->client_id;

        //Get Directory Path
        if($uploadedFile->type == 2){
           $folderName = 'client';
        }else{
           $folderName = 'staff';
        }

        $path = UploadedFile::getDirectory($clientId, $projectId, $folderId, $folderName);

        return response()->download($path.'/'.$fileName);
    }

}
