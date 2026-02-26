<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\LoginNeedsVerification;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function submit(Request $request)
    {
        //validate the phone number
        $request->validate([
            'phone' => 'required|numeric|min:10'
        ]);

        //find or create the user
        $user = User::firstOrCreate([
            'phone' => $request->phone
        ]);

        if(!$user) {
            return response()->json([
                'message' => 'Could not process a user with that phone number'
            ], 401);
        }

        //send the user a one-time use code
        $user->notify(new LoginNeedsVerification());

        //return back a response
        return response()->json([
            'message' => 'text message notification sent.'
        ], 200);
    }

    public function verify(Request $request)
    {
        //validate the phone number and code
        $request->validate([
            'phone' => 'required|numeric|min:9',
            'login_code' => 'required|numeric|between:100000,999999'
        ]);

        //find the user
        $user = User::where('phone', $request->phone)->where('login_code', $request->login_code)->first();

        //is the code provider the same one saved?
        //if so, return back on auth token
        if($user) {
            $user->update(['login_code' => null]);

            return $user->createToken($request->login_code)->plainTextToken;
        }

        //if not, return back a message
        return response()->json(['message' => 'invalid verification code.'], 401);

    }

}
