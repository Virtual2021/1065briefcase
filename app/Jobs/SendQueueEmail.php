<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use Mail;
use App\Models\ProjectSubFolder;
use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectAssignUsers;

class SendQueueEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $details;
    public $timeout = 7200; // 2 hours

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($details)
    {
       $this->details = $details;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Get Mail Data
        $type = $this->details['type'];  // 1=>staff uploaded the file, 2=>client uploaded the file
        $folderName = ProjectSubFolder::find($this->details['folderId']);
        $projectName = Project::find($this->details['projectId']);
        $clientName = Client::find($projectName->client_id);
        
        $fileName = $this->details['fileName'];
        
        if($type == 2){
          $staffUsers = User::where(['role_id' => 1, 'is_activated' => 1])->get();

          foreach($staffUsers as $staffUser){
            $data = array('email' => $staffUser->email, 'folderName' => $folderName->name, 'projectName' => $projectName->name, 'clientName' => $clientName->name, 'fileName' => $fileName);            //Send Mail
                Mail::send('mail.fileupload', $data, function ($message) use ($data) {
                    $message->to($data['email']); $message->subject('Tensixfive Briefcase - New file uploaded');
                    $message->from('support@tensixfivecapital.com', 'TenSixFive Capital');
                });
            }
        }else{
          $users = User::where(['role_id' => 2, 'is_activated' => 1, 'client_id' => $clientName->id])->get();

          foreach($users as $user){
              //Get Project assign
              $projectAssign = ProjectAssignUsers::where(['user_id' => $user->id])->first();
              $data = array('email' => $user->email, 'folderName' => $folderName->name, 'projectName' => $projectName->name, 'clientName' => $clientName->name, 'fileName' => $fileName);
              if($projectAssign->all_projects == 1){
                  Mail::send('mail.fileupload', $data, function ($message) use ($data) {
                     $message->to($data['email']); $message->subject('Tensixfive Briefcase - New file uploaded');
                     $message->from('support@tensixfivecapital.com', 'TenSixFive Capital');
                  });
              }else{
                  //Check if project id exist in array
                  $projectArray = json_decode($projectAssign->projects);
                  if (in_array($this->details['projectId'], $projectArray)) {
                      Mail::send('mail.fileupload', $data, function ($message) use ($data) {
                       $message->to($data['email']); $message->subject('Tensixfive Briefcase - New file uploaded');
                       $message->from('support@tensixfivecapital.com', 'TenSixFive Capital');
                      });  
                  }
              }
          }
        }
    }
}
