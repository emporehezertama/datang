<?php
namespace App\Http\Controllers;
use App\User;
use App\Models\UsersMhr;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
//Required to hash the password
use Illuminate\Support\Facades\Hash;
use App\Models\AbsensiItemMobile;
use App\Models\AbsensiItemMhr;
use App\Models\UsersDemoEmp;

class AuthController extends Controller {
    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    public function validateRequest(Request $request) {
      $rules = [
          'email' => 'required|email|unique:users',
          'password' => 'required|min:6'
      ];
      $this->validate($request, $rules);
    }


    //Get the input and create a user
    public function store(Request $request) {
        $this->validateRequest($request);
        $user = User::create([
            'email' => $request->get('email'),
            'password'=> Hash::make($request->get('password'))
        ]);
        return response()->json(['status' => "success", "user_id" => $user->id], 201);
    }


   //delete the user
   public function destroy($id) {
          $user = User::find($id);
          if(!$user){
              return response()->json(['message' => "The user with {$id} doesn't exist"], 404);
          }
          $user->delete();
          return response()->json(['data' => "The user with with id {$id} has been deleted"], 200);
        }


    //Authenticate the user
    public function verify(Request $request) 
    {
      header('Access-Control-Allow-Origin: *');
      
      $nik = $request->get('nik');
      $password = $request->get('password');
      
      $user = User::where('nik', $nik)->first();  
      
      if($user && Hash::check($password, $user->password)) 
      {
        $apikey = base64_encode(str_random(40));

        $user->apikey = $apikey;
        $user->save();

        if(!empty($user->foto))
        {
          $foto = env('PATH_EM_HR') .'/storage/foto/'. $user->foto;          
        } else 
          $foto = '';

        $params = ['status' => 200, 'data' => $user, 'apikey' => $apikey, 'foto' => $foto];

        if(!empty(get_setting('attendance_logo', $user)))
        {
          $params['logo']     = env('PATH_EM_HR').get_setting('attendance_logo', $user);
        }
        else $params['logo'] = '';
        
        $params['company']  = get_setting('attendance_company', $user);
        $params['news']     = get_setting('attendance_news', $user);
        $params['job']      = '';
        $params['date']      = date('d F Y');

        if(isset($user->structure->position->name))
        {
          $params['job'] .= $user->structure->position->name;
        }

        // if(isset($user->structure->division))
        // {
        //   $params['job'] .= ' '. $user->structure->division;
        // }

        $check = AbsensiItem::where('user_id', $user->id)->whereDate('date', date('Y-m-d'))->first();
        if($check)
        {
          $params['clock_in'] = $check->clock_in;
          $params['clock_out'] = $check->clock_out;
        }
        else
        {
          $params['clock_in'] = 0;
          $params['clock_out'] = 0;
        }

        return response()->json($params, 200);
      }
      else
      {
        return response()->json(['status' => 404, 'message' => "User details incorrect"], 200);        
      }
    }

    /**
     * verifyAttendance
     * @param  Request $request
     */
    public function verifyAttendance(Request $request)
    {
      header('Access-Control-Allow-Origin: *');

      $nik = $request->get('nik');
      $password = $request->get('password');
      
      $user = UsersDemoEmp::where('nik', $nik)->first();  
     
      if($user && Hash::check($password, $user->password)) 
      {
        $apikey = base64_encode(str_random(40));
        
        $user->apikey = $apikey;
        $user->save();

        if(!empty($user->foto))
        {
          $foto = env('PATH_EM_HR') .'/storage/foto/'. $user->foto;          
        } else $foto = '';


        $params = ['status' => 200, 'data' => $user, 'apikey' => $apikey, 'foto' => $foto];

        if(!empty(get_setting('attendance_logo', $user)))
        {
          $params['logo']     = env('PATH_EM_HR').get_setting('attendance_logo', $user);
        }
        else $params['logo'] = '';

        $params['company']  = get_setting('attendance_company', $user);
        $params['news']     = get_setting('attendance_news', $user);
        $params['job']      = '';
        $params['date']      = date('d F Y');

        if(isset($user->structure->position->name))
        {
          $params['job'] .= $user->structure->position->name;
        }

        // if(isset($user->structure->division))
        // {
        //   $params['job'] .= ' '. $user->structure->division;
        // }

        $check = AbsensiItemMobile::where('user_id', $user->id)->whereDate('date', date('Y-m-d'))->first();
        if($check)
        {
          $params['clock_in'] = $check->clock_in;
          $params['clock_out'] = $check->clock_out;
        }
        else
        {
          $params['clock_in'] = 0;
          $params['clock_out'] = 0;
        }

        return response()->json($params, 200);
      }
      else
      {
        return response()->json(['status' => 404, 'message' => "User details incorrect"], 200);        
      }
    }

    /**
     * verifyAttendance
     * @param  Request $request
     */
    public function verifyAttendanceMhr(Request $request)
    {
      header('Access-Control-Allow-Origin: *');

      $nik = $request->get('nik');
      $password = $request->get('password');
      
      $user = UsersMhr::where('nik', $nik)->first();  
     
      if($user && Hash::check($password, $user->password)) 
      {
        $apikey = base64_encode(str_random(40));
        
        $user->apikey = $apikey;
        $user->save();

        if(!empty($user->foto))
        {
          $foto = env('PATH_EM_HR_MHR') .'/storage/foto/'. $user->foto;          
        } else $foto = '';

        $params = ['status' => 200, 'data' => $user, 'apikey' => $apikey, 'foto' => $foto];

        if(!empty(get_setting('attendance_logo', $user)))
        {
          $params['logo']     = env('PATH_EM_HR').get_setting('attendance_logo', $user);
        }
        else $params['logo'] = '';

        $params['company']  = get_setting('attendance_company', $user);
        $params['news']     = get_setting('attendance_news', $user);
        $params['job']      = '';
        $params['date']      = date('d F Y');

        if(isset($user->structure->position->name))
        {
          $params['job'] .= $user->structure->position->name;
        }

        // if(isset($user->structure->division->name))
        // {
        //   $params['job'] .= ' '. $user->structure->division->name;
        // }

        $check = AbsensiItemMhr::where('user_id', $user->id)->whereDate('date', date('Y-m-d'))->first();
        if($check)
        {
          $params['clock_in'] = $check->clock_in;
          $params['clock_out'] = $check->clock_out;
        }
        else
        {
          $params['clock_in'] = 0;
          $params['clock_out'] = 0;
        }

        return response()->json($params, 200);
      }
      else
      {
        return response()->json(['status' => 404, 'message' => "User details incorrect"], 200);        
      }
    }

    //Return the user
    public function show($id) {
      $user = User::find($id);
      if(!$user) {
        return response()->json(['status' => "invalid", "message" => "The userid {$id} does not exist"], 404);
      }
        return response()->json(['status' => "success", 'data' => $user], 200);
    }

    //Update the password
    public function update(Request $request, $id) {
      $user = User::find($id);
      if(!$user){
          return response()->json(['message' => "The user with {$id} doesn't exist"], 404);
      }
      $this->validateRequest($request);
      $user->email        = $request->get('email');
      $user->password     = Hash::make($request->get('password'));
      $user->save();
      return response()->json(['data' => "The user with with id {$user->id} has been updated"], 200);
    }

}