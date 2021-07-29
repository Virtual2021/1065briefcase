<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use App\Models\User;
use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectAssignUsers;

class DashboardController extends Controller
{
    public function __construct()
    {
    $this->middleware("guest");
    }
    
    public function index(){
        $userId = session('data.id');
        $userData = User::find($userId);

        //Get Client Data for Staff Listing
        $clientDatas = Client::where(['status' => 1])->orderBy('name', 'asc')->get();
        
        //Check role of user and redirect to specific page i.e Staff or Client
        if($userData->is_password_change == 0){
            if ($userData->role_id == 1) {
                return view('backend.staff.dashboard', compact('userData', 'clientDatas'));
            } else {

                $userClientData = Client::find($userData->client_id);

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

                return view('backend.client.dashboard', compact('userData', 'userClientData', 'projectDatas'));
            }
        }else{
            return view('backend.loginpass', compact('userData'));
        }
       

    }
}
