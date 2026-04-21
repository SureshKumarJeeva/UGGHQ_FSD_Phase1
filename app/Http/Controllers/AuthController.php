<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Throwable;

class AuthController extends Controller
{

    public function __construct(protected User $user){}

    //Login handler - validate and generate token on successful login
    public function login(Request $request){
        try{
            //Validate login credentials
            $validator = Validator::make($request->all(), [
                    'email' => 'required|email|max:255',
                    'password' => 'required'
                ]);

            //Respond to user with appropriate validation errors.
            if($validator->fails()){
                return response()->json([
                    'status'=>'error',
                    'errors'=>$validator->errors()
                    ], 200);
            }
            //Verify login credentials and generate token on successful login
            if(!$accessToken = $this->user->verifyUserLogin($request))
                return response()->json(['status'=>'error', 'message'=> "Invalid Login credentials"], 200);
            else{
                $loggedUser = $this->user->fetchUserName(auth()->id());
                $loggedUserName = $loggedUser[0]->name;
                $cookie = cookie('access_token', $accessToken, config('app.cookie-ttl'), '/', null, true, true, false, 'Lax');
                return response()->json([
                        'status'=>'success',
                        'data'=>$loggedUserName,
                        ], 200)->withCookie($cookie);
            }
        }catch(\Exception $e){
            Log::info($e);
            throw new \Exception("Internal issue");
        }
    }

    //If this function is reached passing through the middleware then the token is valid
    public function user(Request $request){
        return response()->json([
            'status'=>'success'
        ], 200);
    }

    //Logout user session
    public function logout(Request $request){
        try{
            auth()->logout();
            return response()->json([
                'status'=>'success'
            ], 200);
        }catch(\Exception $e){
            Log::info($e);
        }
    }
}
