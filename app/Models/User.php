<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Tymon\JWTAuth\Facades\JWTAuth;
use Throwable;

#[Fillable(['name', 'email', 'password', 'token'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    //Define interface member functions
    public function getJWTIdentifier(){
        return $this->getKey();
    }
    //Define interface member functions
    public function getJWTCustomClaims(){
        return [];
    }

    //create user record in the DB and generate JWT token
    public function registerUser(Request $request){
        try{
            $user = static::create([
                'name'=>$request->name,
                'email'=>$request->email,
                'password'=>$request->password]);

            $accessToken = JWTAuth::fromUser($user);
            $this->saveToken($user, $accessToken);
            return $accessToken;
        }catch(Throwable $e){
            Log::error("Error while registering the user at DB and saving the token".$e);
            throw new Exception("Facing internal issue");
            
        }
    }

    // verify login and return JWT token
    public function verifyUserLogin(Request $request){
        try{
            if($loginSuccess = auth()->attempt(['email'=> $request->email, 'password' => $request->password])){
                $user = auth()->user();
                $accessToken = JWTAuth::fromUser($user);
                $this->saveToken($user, $accessToken);
                return $accessToken;
            }
            else{
                return false;
            }
        }catch(Throwable $e){
            Log::error("Error while verifying the user".$e);
            throw new Exception("Facing internal issue");
            
        }
    }

    //Save the generated token against user entry in DB
    public function saveToken($user, $accessToken){
        $user->update(["token" => $accessToken]);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
