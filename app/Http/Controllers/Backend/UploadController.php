<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UploadController extends Controller
{
 public  function dropzoneUi()  
    {  
        return view('backend.staff.upload-view');  
    }  

    /** 
     * File Upload Method 
     * 
     * @return void 
     */  

    public  function dropzoneFileUpload(Request $request)  
    {  
       $image = $request->file('file');
        $avatarName = $image->getClientOriginalName();
        $image->move(public_path('images'), $avatarName);
       return response()->json(['success' => $avatarName]);

    }

}
