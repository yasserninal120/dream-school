<?php
namespace App\Http\Controllers;

use App\Models\Image_Post;
use App\Models\Role;
use App\Models\Samester;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\teacherSamester;
use App\Models\User;
use Carbon\Carbon;
use Intervention\Image\ImageManagerStatic as Image;

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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
class PostController extends Controller
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
    //get all posts
    public function index(){
        $posts = post::orderBy('created_at','desc')->with('user')->withCount('comments','likes')
        ->with('likes',function($like){
            return $like->where('user_id',auth()->user()->id)
            ->select('id','user_id','post_id')->get();
        })->get();
        return $this->sendResponse($posts->toArray(),'Update succesfully');
    }
    //get singel post
    public function show($id){
        $post = post::where('id','=',$id)->withCount('comments','likes')->get();
        return $this->sendResponse($post->toArray(),'Update succesfully');
    }
    /// creat post
    public function store (Request $request){
        $attrs = $request->validate([
            'body' => 'required|string'
        ]);
        if($request->input('image')){
            $image = base64_decode($request->input('image'));
            $png_url = time().".png";
             file_put_contents(public_path('/storage/image_post/').$png_url, $image);
            $url = 'image_post/'.$png_url;

        }else{
            $url = null;
        }
        $post = post::create([
            'body' => $attrs['body'],
             'user_id' => auth()->user()->id,
             'image' => $url,
             'date' => Carbon::now()->locale("ar_SA")->translatedFormat("'l Y m d"),
        ]);
        $imagUrl =[];
        if($request->hasFile('images')){
            foreach( $request->file('images') as $image){
                $name = $image->getClientOriginalName();
                $extention = $image->getClientOriginalExtension();
                $filename = time().$name.'.'.$extention;
                $imagOptimazation = Image::make($image);
                $imagOptimazation->save(public_path('/storage/image_post/'.$filename),40);
                $url2 = 'image_post/'.$filename;
                 $imaseTabel = new Image_Post();
                 $imaseTabel->urlImage = $url2;
                 $imaseTabel->post_id = $post->id;
                 $imaseTabel->save();

            }
                return $imagUrl;
        }

        return $this->sendResponse($post->toArray(),'Create succesfully');
    }

      /// update post
      public function update (Request $request ,$id){
       $post = post::find($id);

       if(!$post){
        return response()->json(['error' => 'post not found'],[503]);
       }
    //    if($post->user_id != auth()->user()->id){
    //     return response()->json(['error' => 'Permission denied'],[503]);
    //    }
       $attrs = $request->validate([
        'body' => 'required|string'
        ]);
        if($request->hasfile('image')){
            $url = $request->file('image')->store('image_post');
        }else{
            $url = null;
        }

        $post->update([
            'body' => $attrs['body'],
            'image' => $url,
        ]);
        return $this->sendResponse($post->toArray(),'update succesfully');
    }

    ///destroy
    public function destroy($id){
        $post = post::find($id);

       if(!$post){
        return response()->json(['error' => 'post not found'],[503]);
       }
    //    if($post->user_id != auth()->user()->id){
    //     return response()->json(['error' => 'Permission denied'],[503]);
    //    }

       $post->comments()->delete();
       $post->likes()->delete();
       $post->delete();
       return $this->sendResponse($post->toArray(),'delete succesfully');

    }

    public function indexText(){
        $posts = post::query()->orderBy('created_at','desc')->with('user','postImage')->withCount('comments','likes')
        ->with('likes',function($like){
            return $like->where('user_id',auth()->user()->id)
            ->select('id','user_id','post_id')->get();
        });
        $rows = 5;
        return $posts->paginate($rows);
        return $this->sendResponse($posts->toArray(),'Update succesfully');
    }

}

