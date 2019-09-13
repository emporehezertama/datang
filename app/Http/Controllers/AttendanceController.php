<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\AbsensiItem;
use App\Models\UsersMhr;
use App\Models\AbsensiItemMobile;
use App\Models\AbsensiItemDemo;
use App\Models\UsersDemoEmp;
use App\Models\AbsensiItemMhr;

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
     * @return void
     */
    public function attendanceCheckAuth(Request $request)
    {
        $user = UsersDemoEmp::where('nik', $request->nik)->where('apikey', $request->apikey)->first();

        if($user)
        {
            $attendance = AbsensiItemMobile::where('user_id', $user->id)->whereDate('date', $request->date)->first();
            if($attendance)
            {
                if($attendance->clock_in != "" and $attendance->clock_out == "")
                {
                    return response()->json(['status' => 2], 201);
                }

                if($attendance->clock_in != "" and $attendance->clock_out != "")
                {
                    return response()->json(['status' => 3], 201);
                }
            }

            return response()->json(['status' => 1], 201);
        }
    }

    /**
     * Finger Store
     * @return void
     */
    public function attendanceCheckAuthMhr(Request $request)
    {
        $user = UsersMhr::where('nik', $request->nik)->where('apikey', $request->apikey)->first();

        if($user)
        {
            $attendance = AbsensiItemMhr::where('user_id', $user->id)->whereDate('date', $request->date)->first();
            if($attendance)
            {
                if($attendance->clock_in != "" and $attendance->clock_out == "")
                {
                    return response()->json(['status' => 2], 201);
                }

                if($attendance->clock_in != "" and $attendance->clock_out != "")
                {
                    return response()->json(['status' => 3], 201);
                }
            }

            return response()->json(['status' => 1], 201);
        }
    }

    /**
     * Finger Store
     * @return void
     */
    public function fingerStore(Request $request)
    {
        /**
         * Insert to em-apps.com
         */
        if($request->sn == 'A3AG184660639') // Punya Empore
        {
            $user = \App\User::where('absensi_number', $request->absensi_number)->first();
        }
        else
        {
            $user = \App\UserMhr::where('absensi_number', $request->absensi_number)->first();
        }
        
        if($user)
        {
            if($request->sn == 'A3AG184660639') // Punya Empore
            {
                $item               = AbsensiItem::where('user_id', $user->id)->whereDate('date', date('Y-m-d', strtotime($request->checktime)))->first();
            }
            else
            {
                $item               = AbsensiItemMhr::where('user_id', $user->id)->whereDate('date', date('Y-m-d', strtotime($request->checktime)))->first();
            }

            if(!$item)
            {
                if($request->sn == 'A3AG184660639') // Punya Empore
                {
                    $item = new AbsensiItem();                
                }
                else
                {
                    $item = new AbsensiItemMhr();                
                }

                // inject attendance
                $item->user_id      = $user->id;
                $item->date         = date('Y-m-d', strtotime($request->checktime));
                $item->absensi_device_id = 11; 
                $item->timetable    = date('l', strtotime($request->checktime));   
                $item->ac_no        = $request->sn;
            }
            
            if($request->checktype == 1)
            {
                if($item->clock_out =="") 
                {
                    $item->clock_out = date('H:i:s', strtotime($request->checktime));

                    if(isset($user->absensiSetting->clock_out))
                    {
                        $akhir  = strtotime($item->date .' '. $user->absensiSetting->clock_out .':00');
                        $awal = strtotime($item->date .' '. $request->checktime);
                        $diff  = $akhir - $awal;
                        $jam   = floor($diff / (60 * 60));
                        $menit = ($diff - $jam * (60 * 60)) / 60;
                        
                        if($diff > 0)
                        {
                            $awal  = date_create($item->date .' '. $user->absensiSetting->clock_out .':00');
                            $akhir = date_create($item->date .' '. $request->time .':00'); // waktu sekarang, pukul 06:13
                            $diff  = date_diff( $akhir, $awal );
                            
                            $item->early = $diff->h .':'. $diff->i; 
                        }
                    }
                }
            }
            else
            {
                if($item->clock_in == "") 
                {
                    $item->clock_in = date('H:i:s', strtotime($request->checktime));

                    if(isset($user->absensiSetting->clock_in))
                    {
                        $awal  = strtotime($item->date .' '. $user->absensiSetting->clock_in .':00');
                        $akhir = strtotime($item->date .' '. $request->checktime);
                        $diff  = $akhir - $awal;
                        $jam   = floor($diff / (60 * 60));
                        $menit = ($diff - $jam * (60 * 60)) / 60;
                        
                        if($jam > 0 || $menit > 0)
                        {
                            $jam = abs($jam);
                            $menit = abs($menit);
                            $jam = $jam <= 9 ? "0".$jam : $jam;
                            $menit = $menit <= 9 ? "0".$menit : $menit;

                            $item->late = $jam .':'. $menit; 
                        }
                    }
                }
            }

            $item->save();
        }
        /**
         * END
         */
        return response()->json(['status' => "success"], 201);
    }

    /**
     * Get Send From Device
     * @param  Request $request
     * @return void
     */
    public function send(Request $request)
    {
        header('Access-Control-Allow-Origin: *');

        $user = UsersDemoEmp::where('nik', $request->nik)->first();
        if($user)
        {
            if($request->type == 1)
                $imageName = 'in.jpg';   
            else
                $imageName = 'out.jpg';   
            
            $image_parts = explode(";base64,", $request->file);
            $image_type_aux = explode("image/", @$image_parts[0]);
            $image_type = @$image_type_aux[1];
            $image_base64 = base64_decode(@$image_parts[1]);

            $path = env('PATH_ATTENDANCE_UPLOAD'). '/'.$user->id.'/'.date('Y-m-d');
            
            //Check if the directory already exists.
            if(!is_dir($path)){
                //Directory does not exist, so lets create it.
                mkdir($path, 0755, true);
            }

            // Upload PIC
            file_put_contents($path.'/'. $imageName, $image_base64);
            // resize image
            $img = \Image::make($path.'/'. $imageName);

            $img->resize( ceil( ($img->width() / 2 ) / 2), ceil(($img->height() /2) / 2));

            // save image
            $img->save($path.'/'. $imageName);
            
            // inject attendance
            // replace time server
            $request->time = date('H:i');
            $request->date = date('Y-m-d');

            $item               = AbsensiItemMobile::whereDate('date', '=',$request->date)->where('user_id', $user->id)->first();
            if(!$item)
            {
                $item               = new AbsensiItemMobile();   
                $item->user_id      = $user->id;
                $item->date         = $request->date;
                $item->timetable    = date('l', strtotime($request->date));   
                $item->absensi_device_id = 10;           
            }   

            if($request->type == 1)
            {
                if(empty($item->clock_in)) 
                {
                    $item->clock_in = $request->time;
                    $item->pic          = '/'.$user->id.'/'.date('Y-m-d').'/'.$imageName;
                    $item->long         = $request->long;
                    $item->lat          = $request->lat;
                }
                
                if(isset($user->absensiSetting->clock_in))
                {
                    $awal  = strtotime($item->date .' '. $user->absensiSetting->clock_in .':00');
                    $akhir = strtotime($item->date .' '. $request->time .':00');
                    $diff  = $akhir - $awal;
                    $jam   = floor($diff / (60 * 60));
                    $menit = ($diff - $jam * (60 * 60)) / 60;
                    
                    if($diff > 0)
                    {
                        $jam = abs($jam);
                        $menit = abs($menit);
                        $jam = $jam <= 9 ? "0".$jam : $jam;
                        $menit = $menit <= 9 ? "0".$menit : $menit;

                        $item->late = $jam .':'. $menit;
                    }
                }
            }
            else
            {
                if(empty($item->clock_out))
                {
                    $item->clock_out        = $request->time;
                    $item->pic_out          = '/'.$user->id.'/'.date('Y-m-d').'/'.$imageName;
                    $item->lat_out          = $request->lat;
                    $item->long_out         = $request->long;

                    $awal  = strtotime($item->date .' '. $item->clock_in .':00');
                    $akhir = strtotime($item->date .' '. $item->clock_out .':00');
                    $diff  = $akhir - $awal;
                    $jam   = floor($diff / (60 * 60));
                    $menit = ($diff - $jam * (60 * 60) ) / 60;

                    $jam = $jam <= 9 ? "0".$jam : $jam;

                    $item->work_time        = $jam .':'. $menit;  
                }

                if(isset($user->absensiSetting->clock_out))
                {
                    $akhir  = strtotime($item->date .' '. $user->absensiSetting->clock_out .':00');
                    $awal = strtotime($item->date .' '. $request->time .':00');
                    $diff  = $akhir - $awal;
                    $jam   = floor($diff / (60 * 60));
                    $menit = ($diff - $jam * (60 * 60)) / 60;
                    
                    if($diff > 0)
                    {
                        $awal  = date_create($item->date .' '. $user->absensiSetting->clock_out .':00');
                        $akhir = date_create($item->date .' '. $request->time .':00'); // waktu sekarang, pukul 06:13
                        $diff  = date_diff( $akhir, $awal );
                        
                        $item->early = $diff->h .':'. $diff->i; 
                    }
                }
            }

            $item->save();

            return response()->json(['status' => "success"], 200);
        }
        else
        {
            return response()->json(['status' => "error"], 200);
        }
    }

    /**
     * Get Send From Device
     * @param  Request $request
     * @return void
     */
    public function sendMhr(Request $request)
    {
        header('Access-Control-Allow-Origin: *');

        $user = UsersMhr::where('nik', $request->nik)->first();
        if($user)
        {
            if($request->type == 1)
                $imageName = 'in.jpg';   
            else
                $imageName = 'out.jpg';   
            
            $image_parts = explode(";base64,", $request->file);
            $image_type_aux = explode("image/", @$image_parts[0]);
            $image_type = @$image_type_aux[1];
            $image_base64 = base64_decode(@$image_parts[1]);

            $path = env('PATH_ATTENDANCE_UPLOAD_MHR'). '/'.$user->id.'/'.date('Y-m-d');
            
            //Check if the directory already exists.
            if(!is_dir($path)){
                //Directory does not exist, so lets create it.
                mkdir($path, 0755, true);
            }

            // Upload PIC
            file_put_contents($path.'/'. $imageName, $image_base64);
            // resize image
            $img = \Image::make($path.'/'. $imageName);

            $img->resize( ceil( ($img->width() / 2 ) / 2), ceil(($img->height() /2) / 2));

            $img->save($path.'/'. $imageName);
            
            // inject attendance
            // replace time server
            $request->time = date('H:i');
            $request->date = date('Y-m-d');

            $item               = AbsensiItemMhr::whereDate('date', '=',$request->date)->where('user_id', $user->id)->first();
            if(!$item)
            {
                $item               = new AbsensiItemMhr();   
                $item->user_id      = $user->id;
                $item->date         = $request->date;
                $item->timetable    = date('l', strtotime($request->date));   
                $item->absensi_device_id = 10;           
            }   

            if($request->type == 1)
            {
                if(empty($item->clock_in)) 
                {
                    $item->clock_in = $request->time;
                    $item->pic          = '/'.$user->id.'/'.date('Y-m-d').'/'.$imageName;
                    $item->long         = $request->long;
                    $item->lat          = $request->lat;
                }
                
                if(isset($user->absensiSetting->clock_in))
                {
                    $awal  = strtotime($item->date .' '. $user->absensiSetting->clock_in .':00');
                    $akhir = strtotime($item->date .' '. $request->time .':00');
                    $diff  = $akhir - $awal;
                    $jam   = floor($diff / (60 * 60));
                    $menit = ($diff - $jam * (60 * 60)) / 60;
                    
                    if($diff > 0)
                    {
                        $jam = abs($jam);
                        $menit = abs($menit);
                        $jam = $jam <= 9 ? "0".$jam : $jam;
                        $menit = $menit <= 9 ? "0".$menit : $menit;

                        $item->late = $jam .':'. $menit;
                    }
                }
            }
            else
            {
                if(empty($item->clock_out))
                {
                    $item->clock_out        = $request->time;
                    $item->pic_out          = '/'.$user->id.'/'.date('Y-m-d').'/'.$imageName;
                    $item->lat_out          = $request->lat;
                    $item->long_out         = $request->long;

                    $awal  = strtotime($item->date .' '. $item->clock_in .':00');
                    $akhir = strtotime($item->date .' '. $item->clock_out .':00');
                    $diff  = $akhir - $awal;
                    $jam   = floor($diff / (60 * 60));
                    $menit = ($diff - $jam * (60 * 60) ) / 60;

                    $jam = $jam <= 9 ? "0".$jam : $jam;
                    $menit = $menit <= 9 ? "0".$menit : $menit;

                    $item->work_time        = $jam .':'. $menit;  
                }

                if(isset($user->absensiSetting->clock_out))
                {
                    $akhir  = strtotime($item->date .' '. $user->absensiSetting->clock_out .':00');
                    $awal = strtotime($item->date .' '. $request->time .':00');
                    $diff  = $akhir - $awal;
                    $jam   = floor($diff / (60 * 60));
                    $menit = ($diff - $jam * (60 * 60)) / 60;

                    if($diff > 0)
                    {
                        $awal  = date_create($item->date .' '. $user->absensiSetting->clock_out .':00');
                        $akhir = date_create($item->date .' '. $request->time .':00'); // waktu sekarang, pukul 06:13
                        $diff  = date_diff( $akhir, $awal );
                        
                        $i = $diff->i <= 9 ? "0".$diff->i : $diff->i;
                        $h = $diff->h <=9 ? "0". $diff->h : $diff->h;

                        $item->early = $h .':'. $i; 
                    }
                }
            }

            $item->save();

            return response()->json(['status' => "success"], 200);
        }
        else
        {
            return response()->json(['status' => "error"], 200);
        }
    }
}
