<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\AbsensiItem;

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
     * Finger Store
     * @param  Request $request
     * @return void
     */
    public function fingerStore(Request $request)
    {
        $user = \App\User::where('absensi_number', $request->absensi_number)->first();

        if($user)
        {
            // inject attendance
            $item               = new AbsensiItem();
            $item->user_id      = $user->id;
            $item->date         = date('Y-m-d', strtotime($request->checktime));
            $item->clock_in     = date('H:i:s', strtotime($request->checktime));
            $item->absensi_device_id = 10; 
            $item->save();

            return response()->json(['status' => "success", "name" => $name], 201);
        }

        return response()->json(['status' => "success", "name" => $name], 201);
    }

    /**
     * Get Send From Device
     * @param  Request $request
     * @return void
     */
    public function send(Request $request)
    {
        $data  = $request->getContent(); // Get its content
        #$log   = 'DATA : '. json_encode($request->file('image'));

        #file_put_contents('./log_attendance_'.date("j.n.Y").'.log', $log, FILE_APPEND);

        $image = $request->file('image');

        $user = \App\User::where('email', $request->email)->first();

        if($user)
        {
            $name = time().'.'.$image->getClientOriginalExtension();
            $destinationPath = 'images/'. $user->id .'/';
            $image->move($destinationPath, $name);

            // inject attendance
            $item               = new AbsensiItem();
            $item->user_id      = $user->id;
            $item->date         = date('Y-m-d');
            $item->clock_in     = date('H:i:s');
            $item->absensi_device_id = 10; 
            $item->save();

            return response()->json(['status' => "success", "name" => $name], 201);
        }

        return response()->json(['status' => "error"], 404);
    }
}
