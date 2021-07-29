<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Config;

class UploadedFile extends Model
{
    use HasFactory;

    static function myCreateDirectory($clientId, $projectId, $folderId, $folderName){
        $path = Config::get('app.upload_folder').Config::get('app.client_folder_prefix').$clientId.'/'.Config::get('app.project_folder_prefix').$projectId.'/'.Config::get('app.sub_folder_prefix').$folderId.'/'.$folderName;
        if(!\File::exists($path)) {
            \File::makeDirectory($path, $mode = 0777, true, true);
        }
        return $path;
    }


    static function getDirectory($clientId, $projectId, $folderId, $folderName){
        $path = Config::get('app.upload_folder');
        if($clientId){
            $path = Config::get('app.upload_folder').Config::get('app.client_folder_prefix').$clientId.'/'.Config::get('app.project_folder_prefix').$projectId.'/'.Config::get('app.sub_folder_prefix').$folderId.'/'.$folderName;
        }
        return $path;
    }
}
