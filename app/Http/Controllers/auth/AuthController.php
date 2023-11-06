<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordReset;
use App\Models\User;
use App\Models\UserOtp;
use App\Notifications\AccountVerifyMail;
use App\Notifications\PasswordResetMail;
use App\Notifications\WelcomeMail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class AuthController extends Controller
{
    /**
     * User Registration 
     * store user data in database
     * @param \Illuminate\Http\Request  $request
     * @return json response 
     */
    public function register(Request $request)
    {
        $request->validate([
            'first_name'            =>  'required|string|min:3|max:30',
            'last_name'             =>  'required|string|min:3|max:30',
            'email'                 =>  'required|email|unique:users,email',
            'password'              =>  'required|confirmed',
            'phone'                 =>  'required|numeric|unique:users,phone',
        ]);

        $user = User::create($request->only(['first_name', 'last_name', 'email', 'phone'])
            + [
                'password'              =>  Hash::make($request->password),
                'email_verify_token'    =>  Str::random(64),
                'role_id'               =>  1,
            ]);
        //$user->notify(new WelcomeMail());
        $user->notify(new AccountVerifyMail($user));

        return ok("Account Created Successfully");
    }

    /**
     * verify user account 
     * @param string token
     * @return json response 
     */
    public function verifyAccount($token)
    {

        $user = User::where('email_verify_token', $token)->first();
        if ($user) {
            $user->update([
                'email_verify_token'    =>  null,
                'email_verified_at'     => Carbon::now(),
                'is_active'             =>  true,
            ]);
            return ok('Account Verify Successfuly');
        } else {
            return ok('Account Already Verified');
        }
    }

    /**
     * user login 
     * @param \Illuminate\Http\Request  $request
     * @return json response 
     */
    public function login(Request $request)
    {

        $request->validate([
            'email'    =>  'required|email|exists:users,email',
            'password' =>  'required',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'is_active' => true])) {
            $user   =   auth()->user();
            $user->role;
            $token  =  $user->createToken('API TOKEN')->plainTextToken;
            return ok('User Login Successfully', 
            [
                'user'  => $user,
                'token' =>$token
            ]);
        }
        return error('Invalid Email & Password');
    }


    /**
     * @func forgotPassword
     * @param Request $request 
     * @return json response
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' =>  'required|email|exists:users,email'
        ]);

        $user = User::where('email',$request->email)->first();
        $data  = PasswordReset::updateOrCreate(
            ['email'    =>  $request->email],
            [
                'email'         =>  $request->email,
                'token'         =>  Str::random(64),
                'expired_at'    =>  now()->addDays(2),
            ]);

        $user['token'] = $data->token;

        $user->notify(new PasswordResetMail($user));
        return ok('Password Reset Link Send Successfully',[
            'token' => $data->token,
        ]);
    }

    /**
     * @func resetPassword
     * @description 'chnage password via password reset token'
     * @param Request $request
     * @return json response
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'                 =>  'required|exists:password_resets,token',
            'password'              =>  'required|min:8|max:12',
            'password_confirmation' =>  'required|same:password', 
        ]);
        $passwordReset = PasswordReset::where('token',$request->token)->first();
        //return ok($passwordReset->email);
        if($passwordReset->expired_at >= now())
        {
            User::updateOrCreate(
                ['email' => $passwordReset->email],
                [
                    'password' => Hash::make($request->password),
                ]
            );

            $passwordReset->where('token',$request->token)->delete();

            return ok('Password Reset Successfully');
        }
        return error('Password Reset Token Expired!!!', type: 'unauthenticated');
    }

    /**
     * @func sentOtp
     * @description 'send otp of given number'
     * @param Request $request
     * @return json response
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' =>  'required|numeric|digits:10|exists:users,phone'
        ]);

        return $this->genrateOtp($request->phone);
    }

    /**
     * @func sentOtp
     * @description 'send otp of given number'
     * @param phoneNumber 
     * @return json response
     */
    public function genrateOtp($phone)
    {
        $otp = rand(0000, 9999);

        $userOtp = UserOtp::updateOrCreate(
            ['phone'    =>  $phone],
            [
                'otp'           =>  $otp,
                'expired_at'    =>  now()->addMinutes(3),
            ]
        );

        $account_sid = getenv("TWILIO_SID");
        $auth_token = getenv("TWILIO_TOKEN");
        $twilio_number = getenv("TWILIO_FROM");

        // $client = new Client($account_sid, $auth_token);
        // $client->messages->create("+91 " . $phone, [
        //     'from'  =>  $twilio_number,
        //     'body'  =>  $otp,
        // ]);
        Log::info('Otp :- ' . $otp);
        return ok("otp send Successfully:- $otp");
    }

    /**
     * @func verifyOtp
     * @description 'verify otp then user login'
     * @param Request $request
     * @return json response
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp'   =>  'required|numeric|exists:user_otps,otp'
        ]);

        $userOtp = UserOtp::where('otp', $request->otp)->first();
        $user  = User::where('phone', $userOtp->phone)->first();
        if ($userOtp->expired_at >= now()) {
            $token = $user->createToken('API TOKEN')->plainTextToken;
            Auth::login($user, true);
            $userOtp->delete();
            $user->role;
            return ok("Login successfully",
            [
                'token' => $token,
                'user'  => $user
            ]);
        }
        return $this->genrateOtp($user->phone);
    }
}
