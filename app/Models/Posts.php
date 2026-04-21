<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

#[Fillable(['title', 'post', 'user_id'])]
class Posts extends Model
{
    //table nane
    protected $table = 'posts';

    public function fetchAll(Request $request){
        return Posts::all();
    }

    public function fetch(Request $request){
        return Posts::find($request->id);
    }

    public function store(Request $request){
        try{
            if($response = Posts::create($request->all())){
                return true;
            }
            else{
                return false;
            }
        }catch(\Exception $e){
            Log::info($e);
            throw new \Exception("Internal error");
        }
    }
}
