<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use Input;
use Session;
use Validator;
use Illuminate\Validation\Rule;
use App\Models\User;
use Mail;
use Hash;

class LoginController extends Controller
{

    public function showLoginForm()
    {
        return view('backend.login');
    }

    //LOGIN Function 
    public function login(Request $request)
    {
        //--- Validation Section
        $rules = [
            'email' => 'required',
            'password' => 'required',
        ];

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }
        //--- Validation Section Ends

        // Attempt to log the user in
        /*Backend login check*/
        if (Auth::guard('user')->attempt(['email' => $request->email, 'password' => $request->password])) {
            if (Auth::guard('user')->user()->is_activated) {
                // if successful, then redirect to their intended location
                /*Tenant Id Session Create*/
                $loginid = Auth::guard('user')->user()->id;
                $sessionUserArray = array('id' => $loginid);
                Session::put('data', $sessionUserArray);

                return response()->json(route('backend.dashboard'));
            } else {
                Auth::guard('user')->logout();
                return response()->json(array('errors' => [0 => 'Your account is not activate! Please contact to administrator']));
            }
        } else {
            return response()->json(array('errors' => [0 => 'Invalid Credentials.']));
        }
    }

    //Logout From backend
    public function logout()
    {
        Auth::guard('web')->logout();
        Session::forget('data');
        Auth::guard('user')->logout();
        return redirect('/login');
    }


    public function showForgotForm()
    {
      return view('backend.forgot');
    }
    
    public function backendforgotpass(Request $request)
    {

        $rules = [
            'email' => [
                'required',
                Rule::exists('users')
            ],
        ];

        $custom = ['exists' => 'Invalid Email entered!'];

        $custom = [
            'email.required'=>"Please enter email",
            'email.exists'=>'Invalid email!'];

        $validator = Validator::make(Input::all(), $rules,$custom);
        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }

        $email = $request->email;
        $get_email_info  = User::where('email',$email)->where('is_activated',1)->first();
        if(!$get_email_info->is_activated){
            return response()->json(array('errors' => array('This user account has been disabled. Please contact support.')));
        }
        $success_msg = "";
        $success_status = "";

        $token = md5(time().$email);

        $get_email_info->remember_token = $token;
        if($get_email_info->save()){

            $to = $get_email_info->email;
            /*Send Mail*/
            $mailDataArray = [
                'to' => $to,
                'type' => "staff_forgot_password",
                'fname' => $get_email_info->name,
                'verify_url' => route('backend.reset.pass.token',compact('token')),
            ];

            $success_status = "success";
            $success_msg = "We need to verify your email address. We have sent an email to ".$to." to verify your email address. Please click link in that email to continue.";

            //Email Information to New User
            Mail::send('mail.forgotpassword', $mailDataArray, function ($message) use ($mailDataArray) {
                $message->to($mailDataArray['to'])->subject
                    ('TenSixFive Briefcase – Forgot Password');
                $message->from('support@tensixfivecapital.com', 'TenSixFive Capital');
            });


        }else{
            $success_status = "token";
            $success_msg = "Error! Please try again.";
        }
        return response()->json(array('success'=>$success_status,'msg'=>$success_msg,'redirect_url'=>route('backend.login')));
    }


     public function showresetpassword($token, Request $request) 
    {
      return view('backend.resetpass',['token' => $token]);
    }


     public function passwordResetStore(Request $request)
    {
      $rules = [
          'password' => 'required|string|min:6|confirmed',
          'password_confirmation' => 'required',
          'token' => 'required',

      ];
      $validator = Validator::make(Input::all(), $rules);
      if ($validator->fails()) {
          return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
      }

      $search_token_user = User::where('remember_token',$request->token)
                            ->where('is_activated',1)
                            ->first();

      if($search_token_user){
          $encrypt_password = Hash::make($request->password);
          $search_token_user->password = $encrypt_password;
          $search_token_user->remember_token = NULL;
          if($search_token_user->save()){
                  $check_web_user_record = User::where('email',$search_token_user->email)
                      ->where('is_activated',1)
                      ->first();
                  if($check_web_user_record){
                      /*Update web user password*/
                      $check_web_user_record->password = $encrypt_password;
                      $check_web_user_record->save();
                  }

              return response()->json(array('success'=>"success",'redirect_url'=>route('backend.login'),'msg'=>'Password Reset successfully. Please Login.'));
          }else{
              $errMsg = [
                  'Error! Password is not reset. Please try again.',
              ];
              return response()->json(array('errors' => $errMsg));
          }

      }else{
          $errMsg = [
              'Token is expired or invalid please try again.',
          ];
          return response()->json(array('errors' => $errMsg));
      }

    }

    public function loginPasswordReset(){
        //Get User Data
        $userId = session('data.id');
        $userData = User::find($userId);

       return view('backend.loginpass', compact('userData'));
    }

    public function loginPasswordStore(Request $request){
        $rules = [
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required'
        ];
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }

        $encryptPass = Hash::make($request->password);

        $userData = User::find($request->user_id);
        $userData->password = $encryptPass;
        $userData->is_password_change = 0;


        if ($userData->save()) {
        $success_status = "success";
        $success_msg = "Password Reset Successfully";
        } else {
            $success_status = "token";
            $success_msg = "Error! Please try again.";
        }

        return response()->json(array('success' => $success_status, 'msg' => $success_msg, 'redirect_url' => route('backend.dashboard')));


    }
    
}
