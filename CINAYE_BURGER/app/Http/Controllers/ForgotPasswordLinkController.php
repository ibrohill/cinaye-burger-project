<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ForgotPasswordLinkController extends Controller
{
    public function index (){
        return view('auth.email');
    }

    public function sendEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
    );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message'=> __($status)])
            : response()->json(['email' => __($status)],422);
    }


    public function getToken(){
        return view('auth.reset');
    }

//    public function reset(Request $request)
//    {
//        $request->validate([
//            'token' => 'required',
//            'email' => 'required|email',
//            'password' => 'required|string|confirmed|min:8',
//        ]);
//
//        $status = Password::reset(
//            $request->only('email', 'password', 'password_confirmation', 'token'),
//            function ($user) use ($request) {
//                $user->forceFill([
//                    'password' => Hash::make($request->password),
//                    'remember_token' => Str::random(60)
//                ])->save();
//            }
//        );
//
//        return $status == Password::PASSWORD_RESET
//            ? redirect()->route('login')->with('status', __($status))
//            : back()->withInput($request->only('email'))->withErrors(['email' => __($status)]);
//    }
}
