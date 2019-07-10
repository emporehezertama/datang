<?php
namespace App\Http\Controllers;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
//Required to hash the password
use Illuminate\Support\Facades\Hash;

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
        return response()->json(['status' => 200, 'data' => $user], 200);
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