<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use App\Models\ProjectAssignUsers;
use App\Models\UploadedFile;
use App\Rules\ConfirmPassword;
use App\Models\ProjectSubFolder;
use Auth;
use Illuminate\Http\Request;
use Input;
use Validator;
use Hash;
use Mail;

class StaffController extends Controller
{

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function addClient(Request $request)
    {

        //--- Validation Section
        $rules = [
            'clientName' => 'required',
        ];

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }
        //--- Validation Section Ends

        //Add New Client
        $newClient = new Client();
        $newClient->name = $request->clientName;
        $newClient->created_by = Auth::guard('user')->user()->id;

        $msg = "";
        $error = "";
        $resetform = "";
        if ($newClient->save()) {
            $msg = 'Client Data Added Successfully.';
        } else {
            $error = 'Client Data is not added. Please try again.';
        }

        //--- Validation Section
        $response = array('data' => $msg, 'error' => $error, 'resetform' => $resetform);
        return response()->json($response);
        //--- Redirect Section Ends

    }

    //Client Profile Page
    public function viewClientProfile($id)
    {

        //Get Client Data
        $clientData = Client::find($id);

        //Project Data of Client
        $projectDatas = Project::where(['client_id' => $id])->get();

        $activatedProjects = Project::where(['client_id' => $id, 'is_activated' => 1])->get();

        $userDatas = User::where(['client_id' => $id])->with('assignedProjects')->get();

        return view('backend.staff.clientprofile', compact('clientData', 'projectDatas', 'userDatas', 'activatedProjects'));
    }

    //Staff Add Project
    public function addProject(Request $request)
    {
        //--- Validation Section
        $rules = [
            'projectName' => 'required',
        ];

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }
        //--- Validation Section Ends

        //Add New Project
        $newProject = new Project();
        $newProject->name = $request->projectName;
        $newProject->client_id = $request->clientId;
        $newProject->created_by = Auth::guard('user')->user()->id;

        $msg = "";
        $error = "";
        $resetform = "";
        if ($newProject->save()) {
            $msg = 'Project Added Successfully.';
        } else {
            $error = 'Project is not added. Please try again.';
        }

        //--- Validation Section
        $response = array('data' => $msg, 'error' => $error, 'resetform' => $resetform);
        return response()->json($response);
        //--- Redirect Section Ends

    }

    //Deactivate Project
    public function deactivateProject($id)
    {
        $projectData = Project::find($id);
        $projectData->is_activated = 0;
        $msg = "";
        $error = "";
        $resetform = "";
        if ($projectData->save()) {
            $msg = 'Project Deactivated Successfully.';
        } else {
            $error = 'Project is not updated. Please try again.';
        }
        //--- Validation Section
        $response = array('data' => $msg, 'error' => $error, 'resetform' => $resetform);
        return response()->json($response);
        //--- Redirect Section Ends

    }

     //Activate Project
    public function activateProject($id)
    {
        $projectData = Project::find($id);
        $projectData->is_activated = 1;
        $msg = "";
        $error = "";
        $resetform = "";
        if ($projectData->save()) {
            $msg = 'Project Activated Successfully.';
        } else {
            $error = 'Project is not updated. Please try again.';
        }
        //--- Validation Section
        $response = array('data' => $msg, 'error' => $error, 'resetform' => $resetform);
        return response()->json($response);
        //--- Redirect Section Ends

    }

    //Verify Email before Activate User Pop Up
    public function verifyEmail(Request $request){
        //--- Validation Section
            $rules = [
                'email' => 'required|email',
            ];
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {
                return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
            }

            $userData = User::where(['email' => $request->email])->first();

            if($userData != null){
                if($userData->role_id == 2){
                  $clientData = Client::find($userData->client_id);  
                  $msg = "This user already exists as client user for " . $clientData->name;      
                }else{
                  $msg = "This user already exists";  
                }
                return response()->json(array('errors' => [$msg]));
            }
                //--- Validation Section Ends
            $msg = $request->email;
            $error = "";
            $resetform = "";

            //--- Validation Section
            $response = array('data' => $msg, 'error' => $error, 'resetform' => $resetform);
            return response()->json($response);
            //--- Redirect Section Ends
    }


    //Staff Add User under Client
    public function addUser(Request $request){
         //--- Validation Section
        $rules = [
            'password' => 'required',
        ];
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }
        //--- Validation Section Ends
        
        //Generate Password
        $password = Hash::make($request->password);
        
        //Add New User
        $newUser = new User();
        $newUser->email = $request->email;
        $newUser->password = $password;
        $newUser->role_id = 2;
        $newUser->client_id = $request->clientId;
        $newUser->created_by = Auth::guard('user')->user()->id;
        $newUser->is_password_change = isset($request->passwordReset2) ? 1 : 0;
        $newUser->save();

        //Add Data to Project Assign User
        $projectsAssign = new ProjectAssignUsers();
        $projectsAssign->user_id = $newUser->id; 

        //Client Email 
        $clientEmail = Auth::guard('user')->user()->email;

        $data = array('password' => $request->password, 'email' => $request->email, 'clientemail' => $clientEmail);
    
        
        //Email Information to Particular Staff if Checkbox is checked
        if(isset($request->passwordReset3)){
            Mail::send('mail.newuser', $data, function ($message) use ($data) {
                $message->to($data['email']);$message->bcc($data['clientemail']);$message->subject('TenSixFive Briefcase – New User Registration');
                $message->from('support@tensixfivecapital.com', 'TenSixFive Capital');
            });
        }else{
            //Email Information to New User
            Mail::send('mail.newuser', $data, function ($message) use ($data) {
                $message->to($data['email'])->subject
                    ('TenSixFive Briefcase – New User Registration');
                $message->from('support@tensixfivecapital.com', 'TenSixFive Capital');
            });
        }

        $msg = "";
        $error = "";
        $resetform = "";
        if ($projectsAssign->save()) {
            $msg = 'User Added Successfully.';
        } else {
            $error = 'User is not added. Please try again.';
        }

        //--- Validation Section
        $response = array('data' => $msg, 'error' => $error, 'resetform' => $resetform);
        return response()->json($response);
        //--- Redirect Section Ends
      }


      //Deactivate Project
    public function deactivateUser($id)
    {
        $userData = User::find($id);
        $userData->is_activated = 0;
        $msg = "";
        $error = "";
        $resetform = "";
       if ($userData->save()) {
            if($userData->role_id == 1){
                $msg = 'Staff Deactivated Successfully.';
            }else{
                $msg = 'User Deactivated Successfully.';
            }
        } else {
            if($userData->role_id == 1){
                $error = 'Staff is not updated. Please try again.';
            }else{
                $error = 'User is not updated. Please try again.';
            }
        }
        //--- Validation Section
        $response = array('data' => $msg, 'error' => $error, 'resetform' => $resetform);
        return response()->json($response);
        //--- Redirect Section Ends

    }

     //Activate User
    public function activateUser($id)
    {
        $userData = User::find($id);
        $userData->is_activated = 1;
        $msg = "";
        $error = "";
        $resetform = "";
        if ($userData->save()) {
            if($userData->role_id == 1){
                $msg = 'Staff Activated Successfully.';
            }else{
                $msg = 'User Activated Successfully.';
            }
        } else {
            if($userData->role_id == 1){
                $error = 'Staff is not updated. Please try again.';
            }else{
                $error = 'User is not updated. Please try again.';
            }
        }
        //--- Validation Section
        $response = array('data' => $msg, 'error' => $error, 'resetform' => $resetform);
        return response()->json($response);
        //--- Redirect Section Ends
    }

   //Reset User/Staff Password
    public function resetUserPassword(Request $request){
         //--- Validation Section
        $rules = [
            'password' => 'required',
        ];
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }
        //--- Validation Section Ends
        
        //Generate Password
        $password = Hash::make($request->password);
        
        //Update Password for User
        $newUser = User::find($request->resetuserId);
        $newUser->password = $password;
        $newUser->is_password_change = isset($request->passwordReset2) ? 1 : 0;
        
        //Client Email 
        $clientEmail = Auth::guard('user')->user()->email;

        $data = array('password' => $request->password, 'email' => $newUser->email, 'clientemail' => $clientEmail);
        
        
        //Email Information to Particular Staff if Checkbox is checked
        if(isset($request->passwordReset3)){
            Mail::send('mail.password_reset', $data, function ($message) use ($data) {
                $message->to($data['email']);$message->bcc($data['clientemail']);$message->subject('TenSixFive Briefcase – Password Reset');
                $message->from('support@tensixfivecapital.com', 'TenSixFive Capital');
            });
        }else{
            //Email Information to New User
            Mail::send('mail.password_reset', $data, function ($message) use ($data) {
                $message->to($data['email'])->subject
                    ('TenSixFive Briefcase – Password Reset');
                $message->from('support@tensixfivecapital.com', 'TenSixFive Capital');
            });
        }


        $msg = "";
        $error = "";
        $resetform = "";
        if ($newUser->save()) {
            $msg = 'User password successfully reset and email sent..';
        } else {
            $error = 'Password is not reset. Please try again.';
        }

        //--- Validation Section
        $response = array('data' => $msg, 'error' => $error, 'resetform' => $resetform);
        return response()->json($response);
        //--- Redirect Section Ends
    }

    //Staff Edit User Assigned Project 
    public function editProjectAssigned(Request $request){
        $project_assigned = isset($request->project_assign) ? json_encode($request->project_assign) : NULL;
        //Add Data to Project Assign User
        $projectsAssign = ProjectAssignUsers::where(['user_id' => $request->editAccessuserId])->first();
        $projectsAssign->all_projects = isset($request->all_projects) ? 1 : NULL;
        $projectsAssign->projects = $project_assigned;
       

        $msg = "";
        $error = "";
        $resetform = "";
        if ( $projectsAssign->save()) {
            $msg = 'Projects Assigned Successfully Successfully.';
        } else {
            $error = 'Project does not not assigned. Please try again.';
        }

        //--- Validation Section
        $response = array('data' => $msg, 'error' => $error, 'resetform' => $resetform);
        return response()->json($response);
        //--- Redirect Section Ends
    }

    //Get User Assigned Projects
    public function getUserAssignedProjects($id){

        $data = ProjectAssignUsers::where(['user_id' => $id])->first();

        $userClientData = User::find($id);
        
        $allProjects = $data->all_projects;
        $assigned_projects = json_decode($data->projects);

        $projectLength = Project::where(['client_id' => $userClientData->client_id])->get('id')->toArray();

        $response = array('all' => $allProjects, 'other' => $assigned_projects, 'total' => $projectLength);
        return response()->json($response);
    }


    //New Staff
    public function getStaff(){
        $getStaffDatas = User::where('role_id' , 1)->orderBy('email', 'asc')->get();
        return view('backend.staff.newstaff', compact('getStaffDatas'));
    }

    
    //Add Staff
    public function addStaff(Request $request){
     //--- Validation Section
        $rules = [
            'password' => 'required',
        ];
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }
        //--- Validation Section Ends
        
        //Generate Password
        $password = Hash::make($request->password);
        
        //Add New User
        $newUser = new User();
        $newUser->email = $request->email;
        $newUser->password = $password;
        $newUser->role_id = 1;
        $newUser->client_id = 0;
        $newUser->created_by = Auth::guard('user')->user()->id;
        $newUser->is_password_change = isset($request->passwordReset2) ? 1 : 0;
        $newUser->save();

        //Add Data to Project Assign User
        $projectsAssign = new ProjectAssignUsers();
        $projectsAssign->user_id = $newUser->id; 

        //Client Email 
        $clientEmail = Auth::guard('user')->user()->email;

        $data = array('password' => $request->password, 'email' => $request->email, 'clientemail' => $clientEmail);
        
        
        // Email Information to Particular Staff if Checkbox is checked
        if(isset($request->passwordReset3)){
            Mail::send('mail.newuser', $data, function ($message) use ($data) {
                $message->to($data['email']);$message->bcc($data['clientemail']);$message->subject('TenSixFive Briefcase – New User Registration');
                $message->from('support@tensixfivecapital.com', 'TenSixFive Capital');
            });
        }else{
            //Email Information to New User
            Mail::send('mail.newuser', $data, function ($message) use ($data) {
                $message->to($data['email'])->subject
                    ('TenSixFive Briefcase – New User Registration');
                $message->from('support@tensixfivecapital.com', 'TenSixFive Capital');
            });
        }


        $msg = "";
        $error = "";
        $resetform = "";
        if ($projectsAssign->save()) {
            $msg = 'Staff Added Successfully.';
        } else {
            $error = 'Staff is not added. Please try again.';
        }

        //--- Validation Section
        $response = array('data' => $msg, 'error' => $error, 'resetform' => $resetform);
        return response()->json($response);
        //--- Redirect Section Ends
    }


    //Get Client Files
    public function viewFiles($id, $searchid=null){
        $clientData = Client::find($id);
        $projectDatas = Project::where(['is_activated' => 1, 'client_id' => $id])->get();
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
                if(!$subFolders->isEmpty()){
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
                if(!$subFolders->isEmpty()){
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
            if( (!isset($_GET['projectId'])) && (!isset($_GET['folderId'])) && (!isset($_GET['keyword']))){
                if (!$projectDatas->isEmpty()) {
                    $subFolders = ProjectSubFolder::where(['project_id' => $projectDatas[0]->id, 'isDeleted' => 0])->get();
                    if (!$subFolders->isEmpty()) {
                            $projectFiles = UploadedFile::where(['folder_id' => $subFolders[0]->id, 'isdeleted' => 0])->get();
                    }
                }
            }

        }
        return view('backend.staff.clientfiles', compact('clientData', 'projectDatas', 'subFolders', 'projectFiles', 'search'));
    } 

    //Get Project Files
    public function getProjectFiles($id){
       
        $staffFiles = UploadedFile::where(['project_id' => $id, 'isdeleted' => 0, 'type' => 1])->get(['id', 'name', 'created_at'])->toArray();

        $clientFiles = UploadedFile::where(['project_id' => $id, 'isdeleted' => 0, 'type' => 2])->get(['id', 'name', 'created_at'])->toArray();
        
        $response = array('staffFiles' => $staffFiles, 'clientFiles' => $clientFiles);
        return response()->json($response);

    }

    //Staff Upload Files
    public function uploadFiles(Request $request){
       $clientId = $request->client_id;
       $projectId = $request->project_id;
       $folderId = $request->folder_id;
       $folderName = 'staff';

      $path = UploadedFile::myCreateDirectory($clientId, $projectId, $folderId, $folderName);

      $image = $request->file('file');
      $fileName = $image->getClientOriginalName();
     

      //Store Name in Database
      $newFile = new UploadedFile;
      $newFile->project_id = $projectId;
      $newFile->folder_id = $folderId;
      $newFile->name = $fileName;
      $newFile->type = 1;
      $newFile->uploaded_by = Auth::guard('user')->user()->id;
      $newFile->save();
      
      if(file_exists($path.'/'.$fileName)) unlink($path.'/'.$fileName);
      $msg = '';

      if($image->move($path, $fileName)){
        //Send Mail Through Queue
        $details = [
                'subject' => 'Tensixfive Briefcase - New file uploaded',
                'folderId' => $folderId,
                'projectId' => $projectId,
                'fileName'  => $fileName,
                'type' => 1
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
        $path = UploadedFile::getDirectory($clientId, $projectId, $folderId ,$folderName);

        return response()->download($path.'/'.$fileName);
    }


    //Reset Password
    public function resetPassword(Request $request){
         //--- Validation Section
        $rules = [
            'currentPassword' => 'required',
            'newPassword' => 'required',
            'confirmPassword' => 'required',
        ];
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }
        
        if($request->newPassword != $request->confirmPassword){
            return response()->json(array('errors' => ["Password doesn't match."]));
        }

        //End Validation Section

        //Get User Current Password
        $userData = User::find($request->user_id);
        $oldPassword = $userData->password;
        //--- Validation Section Ends

        if(Hash::check($request->currentPassword, $userData->password)){
           $newPassword = Hash::make($request->newPassword);
           $userData->password = $newPassword;
           $msg = "";
            $error = "";
            $resetform = "";
            if ($userData->save()) {
                $msg = 'Password Reset Successfully.';
            } else {
                $error = 'Password is not reset. Please try again.';
            }

            //--- Validation Section
            $response = array('data' => $msg, 'error' => $error, 'resetform' => $resetform);
            return response()->json($response);

        }else{
            return response()->json(array('errors' => ['Please provide correct current password.']));
        }
    }

    //Staff Add Folder
    public function addFolder(Request $request){
        //--- Validation Section
        $rules = [
            'folderName' => 'required'
        ];
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }
        
        //End Validation Section
         $projectId = $request->projectId;

        //Get File Data
        $subFolder = new ProjectSubFolder();
        $subFolder->project_id = $projectId;
        $subFolder->name = $request->folderName;
        $subFolder->created_by = Auth::guard('user')->user()->id;
    
        $msg = "";
        $error = "";
        $resetform = "";
        if ($subFolder->save()) {
            $msg = 'Folder Created Successfully.';
        } else {
            $error = 'Folder is not created. Please try again.';
        }

        //--- Validation Section
        $response = array('data' => $msg, 'error' => $error, 'resetform' => $resetform);
        return response()->json($response);
        //--- Redirect Section Ends
    }


     //Staff Add/Modify Description
    public function addDescription(Request $request){
        //--- Validation Section
        $rules = [
            'description' => 'required'
        ];
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }
        
        //End Validation Section
        $fileId = $request->fileId;

        //Get File Data
        $fileDescription = UploadedFile::find($fileId);
        $fileDescription->description = $request->description;
    
        $msg = "";
        $error = "";
        $resetform = "";
        if ($fileDescription->save()) {
            $msg = 'Description Added Successfully.';
        } else {
            $error = 'Description is not added. Please try again.';
        }

        //--- Validation Section
        $response = array('data' => $msg, 'error' => $error, 'resetform' => $resetform);
        return response()->json($response);
        //--- Redirect Section Ends
    }


}
