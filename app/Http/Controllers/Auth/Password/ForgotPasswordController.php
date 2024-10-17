<?php

namespace App\Http\Controllers\Auth\Password;

use App\Http\Controllers\Controller;
use Helpers\EmailHelper;
//use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Models\User;
use Lang;

class ForgotPasswordController extends Controller
{
    // use Carbon;
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
     */
  //  use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    private $emailHelper;

    public function __construct(EmailHelper $emailHelper)
    {
        $this->middleware('guest');
        $this->emailHelper = $emailHelper;
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getResetToken(Request $request)
    {
        $this->validate($request, ['username' => 'required']);
        $user = User::where('username', $request->input('username'))->first();

        if (!$user) {
            return response()->json(['error' => Lang::get('validation.custom.user.username-not-exist',[],'en')], 400);
        }

        $payload = $this->broker()->createToken($user);

        if (filter_var($request->input('username'), FILTER_VALIDATE_EMAIL)) {      
            // username is email
            $this->emailHelper->sendResetPasswordLinkMail($request->input('username')["username"],$user->name,$payload["token"]);
            return response()->json("true", 200);
        } else {
            // username is phone
            $verificationCode = $payload["reset_code"];
            //todo send sms with code
            return response()->json(['token' => $payload["token"]], 200);
        }

       
    }

}
