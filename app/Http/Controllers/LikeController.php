<?php

namespace App\Http\Controllers;
use App\Models\Role;
use App\Models\Samester;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\teacherSamester;
use App\Models\User;
use Carbon\Carbon;
// use GuzzleHttp\Psr7\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use JWTFactory;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth as FacadesJWTAuth;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use JD\Cloudder\Cloudder;
use App\Models\post;
use App\Models\like;
class LikeController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function sendResponse($result , $message){
        $response = [
            'success' => true,
            'data' => $result,
            'message' => $message,
        ];
        return response()->json($response , 200);
    }
    public function sendError($error , $errorMessage = [] , $code = 404){
        $response = [
            'success' => false,
            'data' => $errorMessage,
            'message' => $error ,
        ];
        if(!empty($errorMessage)){
            $response['data'] = $errorMessage;

        }
        return response()->json($response , $code );
    }

   public function likeOrUnlike( $id){
    $post = post::find($id);

    if(!$post){
     return response()->json(['error' => 'post not found'],[503]);
    }

    $like = $post->likes()->where('user_id', auth()->user()->id)->first();
    //if not like then like
     if(!$like){
     $like = like::create([
        'user_id' => auth()->user()->id,
        'post_id' => $id,
      ]);
      return $this->sendResponse($like->toArray(),'create like succesfully');
     }

     $like->delete();
     return response([
         'messeg' => 'DisLike'
     ],200);
   }
   public function userIsLiked($ipBost){
    $userId = auth()->user()->id;
    $isLiked = like::where('post_id','=',$ipBost)->where('user_id','=',$userId)->count();
    return response()->json(['isLiked'=>$isLiked]);
   }

}
