<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * Get Send From Device
     * @param  Request $request
     * @return void
     */
    public function send(Request $request)
    {
        $image = $request->file('image');

        $getToken  = $request->token;
        $user = \App\User::where('token_login', $getToken)->first();
        if($user)
        {
            $name = time().'.'.$image->getClientOriginalExtension();

            $destinationPath = 'images/';

            $image->move($destinationPath, $name);

            return response()->json(['status' => "success", "name" => $name], 201);
        }

        return response()->json(['status' => "error"], 404);
    }
}
