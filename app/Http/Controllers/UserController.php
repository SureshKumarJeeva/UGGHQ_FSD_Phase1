<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class UserController extends Controller
{
    public function __construct(protected User $user){}

    public function register(Request $request){
        try{
            //Validate user details
            $validator = Validator::make($request->all(), [
                    'name' => 'required|string|min:2|max:255',
                    'email' => 'required|unique:users|email|max:255',
                    'password' => 'required|string|min:8|max:255'
                ]);

            //Respond to user with appropriate validation errors.
            if($validator->fails()){
                return response()->json([
                    'status'=>'error',
                    'errors'=>$validator->errors()
                    ], 200);
            }
            $token = $this->user->registerUser($request);
            $cookie = cookie('access_token', $token, config('app.cookie-ttl'), '/', null, true, true, false, 'Lax');
            $loggedUser = $this->user->fetchUserName(auth()->id());
            $loggedUserName = $loggedUser[0]->name;
            return response()->json([
                    'status'=>'success',
                    'data'=>$loggedUserName,
                    ], 200)->withCookie($cookie);
        }catch(\Exception $e){
            Log::info($e);
        }
    }
}
