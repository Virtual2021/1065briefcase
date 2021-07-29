<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SendMailController extends Controller
{
     public function send_mail(Request $request)
    {
        $details = [
            'subject' => 'Tensixfive Briefcase - New file uploaded',
            'folderId' => 1,
            'projectId' => 1,
            'fileName' => 'fileName',
            'type' => 2
        ];

    	
        $job = (new \App\Jobs\SendQueueEmail($details))
            	->delay(now()->addSeconds(2)); 

        dispatch($job);
        echo "Mail send successfully !!";
    }
}
