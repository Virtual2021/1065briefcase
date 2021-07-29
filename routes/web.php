<?php

use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/login', 'Backend\LoginController@showLoginForm')->name('backend.login');
Route::post('/login', 'Backend\LoginController@login')->name('backend.login.submit');

Route::get('/portal/forgot-password', 'Backend\LoginController@showForgotForm')->name('backend.forgot');
Route::post('/portal/forgotpass/store', 'Backend\LoginController@backendforgotpass')->name('backend.forgot.submit');
Route::get('/resetpass/{token}', 'Backend\LoginController@showresetpassword')->name('backend.reset.pass.token');
Route::post('/forgot/reset/password', 'Backend\LoginController@passwordResetStore')->name('backend.resetpass.submit');

Route::get('/login/reset/password', 'Backend\LoginController@loginPasswordReset')->name('backend.resetpass.login');
Route::post('/login/password/store', 'Backend\LoginController@loginPasswordStore')->name('backend.resetpass.store');
Route::get('/logout', 'Backend\LoginController@logout')->name('backend.logout');


Route::get('/dashboard', 'Backend\DashboardController@index')->name('backend.dashboard');

//Staff Module Routes

//Client Operation routes
Route::post('/add/client', 'Backend\StaffController@addClient')->name('backend.add.client');
Route::get('/client/profile/{id}', 'Backend\StaffController@viewClientProfile')->name('backend.client.profile');
Route::get('/client/view/files/{id}', 'Backend\StaffController@viewFiles')->name('backend.client.files');


//Client View Files Routes
Route::get('/get/project/uploaded/files/{id}', 'Backend\StaffController@getProjectFiles')->name('get.project.files');
Route::post('/staff/upload/files', 'Backend\StaffController@uploadFiles')->name('staff.upload.files');
Route::post('/staff/delete/file', 'Backend\StaffController@deleteFile')->name('staff.delete.file');
Route::get('/staff/download/file/{id}', 'Backend\StaffController@downloadFile')->name('staff.download.file');
Route::post('/staff/add/folder', 'Backend\StaffController@addFolder')->name('staff.add.folder');
Route::post('/staff/add/description', 'Backend\StaffController@addDescription')->name('staff.add.description');


//Client Profile Routes
Route::post('/add/project' , 'Backend\StaffController@addProject')->name('staff.add.project');
Route::get('/activate/project/{id}', 'Backend\StaffController@activateProject')->name('staff.activate.project');
Route::get('/deactivate/project/{id}', 'Backend\StaffController@deactivateProject')->name('staff.deactivate.project');

Route::post('/user/verify/email', 'Backend\StaffController@verifyEmail')->name('staff.verify.email');
Route::post('/add/user', 'Backend\StaffController@addUser')->name('staff.add.user');

Route::get('/activate/user/{id}', 'Backend\StaffController@activateUser')->name('staff.activate.user');
Route::get('/deactivate/user/{id}', 'Backend\StaffController@deactivateUser')->name('staff.deactivate.user');
Route::post('/user/reset/password', 'Backend\StaffController@resetuserPassword')->name('staff.reset.user.password');
Route::post('/user/edit/project/assigned', 'Backend\StaffController@editProjectAssigned')->name('staff.edit.project.assigned');
Route::get('/get/user/assigned/projects/{id}', 'Backend\StaffController@getUserAssignedProjects')->name('staff.get.assigned.projects');


//Staff operation routes
Route::get('/get/staff', 'Backend\StaffController@getStaff')->name('get.staff');
Route::post('/add/staff', 'Backend\StaffController@addStaff')->name('add.staff');
Route::post('/reset/password', 'Backend\StaffController@resetPassword')->name('staff.reset.password');


//Client Routes

Route::get('/project/{id}', 'Backend\ClientController@projectDetail')->name('client.project.detail');

Route::get('/client/project/uploaded/files/{id}', 'Backend\ClientController@getClientFiles')->name('get.client.files');
Route::post('/client/upload/files', 'Backend\ClientController@uploadFiles')->name('client.upload.files');
Route::post('/client/delete/file', 'Backend\ClientController@deleteFile')->name('client.delete.file');
Route::get('/client/download/file/{id}', 'Backend\ClientController@downloadFile')->name('client.download.file');

Route::get('/send/mail', 'Backend\SendMailController@send_mail')->name('send_mail');


