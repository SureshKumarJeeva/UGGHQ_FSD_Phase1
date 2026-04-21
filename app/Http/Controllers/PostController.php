<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Posts;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function __construct(protected Posts $post){}

    public function posts(Request $request){
        try{
            if($responsedata = $this->post->fetchAll($request)){
                $responsedata->put('u', auth()->id());
                return response()->json([
                                        'status'=>'success',
                                        'data' => $responsedata
                                    ], 200);
            }
            else{
                return response()->json([
                                        'status'=>'error'
                                    ], 500);
            }
        }catch(\Exception $e){
            Log::error($e);
        }
    }

    public function create(Request $request){
        try{
            //assign current loggedin user to request object
            $request->merge(['user_id'=>auth()->id()]);

            //Validate post details
            $validator = Validator::make($request->all(), [
                    'title' => 'required|string|min:10',
                    'post' => 'required|string|min:10',
                ]);

            //Respond to user with appropriate validation errors.
            if($validator->fails()){
                return response()->json([
                    'status'=>'error',
                    'errors'=>$validator->errors()
                    ], 200);
            }
            
            if($this->post->store($request))
                return response()->json([
                                        'status'=>'success'
                                    ], 200);
            else
                return response()->json([
                                        'status'=>'error'
                                    ], 500);
        }catch(\Exception $e){
            Log::error($e);
        }
    }

    public function fetch(Request $request){
        return $this->post->fetch($request);
    }
}
