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
use App\Models\comment;

class CommentController extends Controller
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
    //get all comments of a post
   public function index($id){
    $post = post::find($id);

    if(!$post){
     return response()->json(['error' => 'post not found'],[503]);
    }
     $comment = $post->comments()->with('user')->get();
    return $this->sendResponse($comment->toArray(),'comment succesfully');
   }

   // create comment
   public function store(Request $request , $id){
    $post = post::find($id);

    if(!$post){
     return response()->json(['error' => 'post not found'],[503]);
    }

    $attrs = $request->validate([
        'comment' => 'required|string'
        ]);


        $comment =  comment::create([
       'comment' =>$attrs['comment'],
       'post_id' => $id,
       'user_id' => auth()->user()->id,
     ]);

   return $this->sendResponse($comment->toArray(),'comment succesfully');

   }
   //comment update
   public function update(Request $request , $id){
       $comment = comment::find($id);
       if(!$comment){
        return response()->json(['error' => 'comment not found'],[503]);
       }
       if($comment->user_id != auth()->user()->id){
        return response()->json(['error' => 'Permission denied'],[503]);
       }
       $attrs = $request->validate([
        'comment' => 'required|string'
        ]);

        $comment->update([
            'comment' => $attrs['comment']
        ]);
        return $this->sendResponse($comment->toArray(),'update succesfully');

   }
   ///destroy comment
   public function destroy($id){
    $comment = comment::find($id);
    if(!$comment){
     return response()->json(['error' => 'comment not found'],[503]);
    }
    if($comment->user_id != auth()->user()->id){
     return response()->json(['error' => 'Permission denied'],[503]);
    }

    $comment->delete();
    return $this->sendResponse($comment->toArray(),'deleted succesfully');


   }


}
