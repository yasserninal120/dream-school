<?php

namespace App\Http\Controllers;

use App\Models\FCMToken;
use Illuminate\Http\Request;

class TokenController extends Controller
{
    public function store(Request $request){
        $app_token = FCMToken::updateOrCreate(
            ['token' => $request->token],
            ['user_id' => $request->user_id]
        );
        if($app_token != null){
            return response([
                'message' => 'success'
            ]);
        }
        return response([
            'message' => 'fail'
        ],401);
    }
    public function revoke(Request $request){
        $tokens = FCMToken::where('token',$request->token)->get();
        foreach ($tokens as $token) {
            $token->delete();
        }
        return response([
            'message' => 'success'
        ]);
    }
}
