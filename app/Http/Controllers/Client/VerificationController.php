<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    // 1. Show the "Verify Your Email" Notice
    public function notice()
    {
        // If user is already verified, redirect home
        if (request()->user()->hasVerifiedEmail()) {
            return redirect()->route('home');
        }

        return view('client.auth.verify-email');
    }

    // 2. Handle the Verification Link Click
    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return redirect()->route('home')->with('success', 'Your email has been verified!');
    }

    // 3. Resend Verification Email
    public function send(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', 'Verification link sent!');
    }
}
