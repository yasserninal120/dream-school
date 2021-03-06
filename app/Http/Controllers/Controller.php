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
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Exceptions\JWTException;
use JD\Cloudder\Cloudder;
use Illuminate\Support\Facades\URL;
// use App\Http\Controllers\Storage;

class Controller extends BaseController
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
    ////users-functions
    public function create_user(Request $request){
        error_log('started');
        $validated = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:10048',

        ]);

        $creaetUser = new User();
        $creaetUser->name = $request->get('name');
        $creaetUser->email = $request->get('email');
        $creaetUser->password =Hash::make($request->get('password'));
        $creaetUser->role_id = $request->get('role');
        if($request->hasfile('image')){
            $url = $request->file('image')->store('image_profile');
        }else{
            $url = null;
        }
        //http://127.0.0.1:8000/storage/image_profile/YORna4xgc4MwQyr6vLogUWIVSiD4PB7YM70xCulY.png
        $creaetUser->image = $url;
        $creaetUser->save();

        $user = User::first();
        $token =  JWTAuth::fromUser($user);
      //// add student to student table
        $email = $request->get('email');
        $getUser = User::where('email' , '=' , $email )->get();
        foreach($getUser as $user){
            $role_id = $user->role_id;
        }
        if($role_id == 3){
            Student::create([
                'user_id' => $user -> id,
                // 'samester_id' => 1 ,
            ]);
        }
        //// add teacher to teacher table
        if($role_id == 2){
           $teacher =  Teacher::create([
                'user_id' => $user -> id,
            ]);

        }
        error_log('finished');
        return response() -> json(compact('token'));
    }


    public function login(Request $request){
        $validated = Validator::make($request->all(),[
            'email' => 'required',
            'password' => 'required',
        ]);

        if($validated -> fails()){
            return response()->json($validated -> errors());
        }

        $credentials = $request->only('email','password');
        try{
            if(! $token = JWTAuth::attempt($credentials)){
                return response()->json(['error' => '???????? ???? ???????? ???????? ???? ?????? ????????????????'],[503]);

            }
        }catch(JWTException $e){
            return response()->json(['error' => 'could not create token'],[503]);

        }
        // return response() -> json(compact('token'));
        return response() -> json(['token' => $token],200);
    }

    public function updateUser(Request $request , $id){
        $input = $request -> all();
        $validated = Validator::make($input,[
            'email' => 'required',
            'password' => 'required',
            // 'role_id' =>  'required',
            // 'role_id' =>  'required|exists:roles,id',
        ]);
        if($validated -> fails()){
            return $this->sendError('error validation',$validated -> errors());
        }
        if($request->hasfile('image')){
            $url = $request->file('image')->store('image_profile');
        }else{
            $url = null;
        }
        //http://127.0.0.1:8000/storage/image_profile/YORna4xgc4MwQyr6vLogUWIVSiD4PB7YM70xCulY.png
        $user  = User::find($id);
        $user->name = $input['name'];
        $user->email = $input['email'];
        $user->password = $input['password'];
        $user->role_id = $input['role'];
        $user->image = $url;
        $user->save();
        return $this->sendResponse($user->toArray(),'Update succesfully');

    }
    public function destroyUser($id){
        $user = User::find($id);
        $user->delete();
        return $this->sendResponse($user->toArray(),'deleted succesfully');

    }

    public function viewUsers(){
            $users = User::all();
            return $this->sendResponse($users->toArray(),'read succesfully');

    }

/////////////samestares function
    public function createSemester(Request $request){
        $input = $request->all();
        $validator = Validator::make($input,[
            'name' => 'required',
        ]);
        if($validator -> fails()){
            return $this->sendError('error validation',$validator -> errors());
        }
        $semestare = Samester::create($input);
        return $this->sendResponse($semestare->toArray(),'created succesfully');

    }

    public function UpdateSemester(Request $request , $id){
        $input = $request->all();
        // dd($id);
        $validator = Validator::make($input,[
            'name' => 'required',
        ]);
        if($validator -> fails()){
            return $this->sendError('error validation',$validator -> errors());
        }
        $samester = Samester::find($id);
        $samester->name = $input['name'];
        $samester->save();
        return $this->sendResponse($samester->toArray(),'created succesfully');

    }


    public function destroySemester($id){
        $samester = Samester::find($id);
        $samester->delete();
        return $this->sendResponse($samester->toArray(),'deleted succesfully');

    }

    public function viewSemesters(){
        if(auth()->user()->role_id == 1){
            $semestare = Samester::where('id' , '!=',1)->get();
            return $this->sendResponse($semestare->toArray(),'read succesfully');
        }
        if(auth()->user()->role_id == 2){
            $id = auth()->user()->id;
            $t = User::find($id);
            $t -> teacher->semester;
            return $this->sendResponse($t->toArray(),'read succesfully');

        }
    }
    public function viewSemester($id){
      $st =  Student::where('samester_id','=',$id)->with('user')->get();
        // $semestare = Samester::find($id);
        // $st = $semestare->student;

        return $this->sendResponse($st->toArray(),'read succesfully');
    }

    public function addTeacherTosemaster($samester_id , $teacher_id){
        $samester= Samester::find($samester_id);
        $samester->teacher()->attach([$teacher_id]);
        return $this->sendResponse($samester->toArray(),'add succesfully');
    }

     public function addStudentTosemaseter( $id , Request $request){

        $studetId = explode(",",$request->input('ids')) ;
        // foreach($studetId as $key => $value){
        //     $student = Student::find($value['ids']);
        //     $student->samester_id = $id;

        // }
        $t =Student::whereIn('id',$studetId)->update(['samester_id' => $id]);
        // echo "<pre>"; print_r($studetId); die;
       return response()->json(['true' => 'update Sucsse']);

     }
    public function test(){
        $Users = User::with('role')->get();
        return $this->sendResponse($Users->toArray(),'read succesfully');
    }
    public function acountDetiles(){
        $id = auth()->user()->id;
    $user_login = User::where('id', '=', $id)->with('role')->get();
    return $this->sendResponse($user_login->toArray(),'read succesfully');

    }

    public function getStudent($id){
        $studetn = Student::orderBy('created_at','desc')->where('samester_id','!=',$id)->with('samester')->with('user')->get();
        return $this->sendResponse($studetn->toArray(),'read succesfully');

    }



}
