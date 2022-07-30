<?php
namespace App\Http\Controllers;

use App\Models\instalment;
use App\Models\pay;
use App\Models\post;
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
        if($request->input('image')){
            $image = base64_decode($request->input('image'));
            $png_url = time().".png";
             file_put_contents(public_path('/storage/image_profile/').$png_url, $image);
            $url = 'image_profile/'.$png_url;

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
        // if($role_id == 3){
        //     Student::create([
        //         'user_id' => $user -> id,
        //         // 'samester_id' => 1 ,
        //     ]);
        // }
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
                return response()->json(['error' => 'خطاء في كلمة السر او اسم المستخدم'],503);

            }
        }catch(JWTException $e){
            return response()->json(['error' => 'could not create token'],503);

        }
        // return response() -> json(compact('token'));
        return response() -> json(['token' => $token],200);
    }

    public function updateUser(Request $request , $id ,$imageUpdate){
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

        if($imageUpdate == 1){
            if($request->input('image')){
                $image = base64_decode($request->input('image'));
                $png_url = time().".png";
                 file_put_contents(public_path('/storage/image_profile/').$png_url, $image);
                $url = 'image_profile/'.$png_url;
            }
        }else{
            $url = $request->input('image');
        }


        //http://127.0.0.1:8000/storage/image_profile/YORna4xgc4MwQyr6vLogUWIVSiD4PB7YM70xCulY.png
        $user  = User::find($id);
        $user->name = $input['name'];
        $user->email = $input['email'];
        $user->password = Hash::make($request->get('password'));
        $user->role_id = $input['role'];
        $user->image = $url;
        $user->save();
        return $this->sendResponse($user->toArray(),'Update succesfully');

    }
    public function destroyUser($id){
        $user = User::find($id);
        if($user->role_id == 1){
            $post = post::where('user_id' , '=', $id)->delete();
            $user->delete();
           return $this->sendResponse($user->toArray(),'deleted succesfully');

        }
        // if($user->role_id == 2){
        //     return 'teacher';

        // }
        // if($user->role_id == 3){
        //     return 'student';
        // }
        // $user->delete();
        // return $this->sendResponse($user->toArray(),'deleted succesfully');

    }

    public function viewUsers(){
            $users = User::orderBy('created_at','desc')->get();
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
        $Users = User::orderBy('created_at','desc')->with('role')->get();
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
/////////student
public function createStudent($userId  ,Request $request){
    $name = $request->input('name');
    $sId = $request->input('sId');
    if($request->input('image')){
        $image = base64_decode($request->input('image'));
        $png_url = time().".png";
         file_put_contents(public_path('/storage/image_profile/').$png_url, $image);
        $url = 'image_profile/'.$png_url;

    }else{
        $url = null;
    }
    $student = new Student();
    $student->user_id = $userId;
    $student->name = $name;
    $student->image = $url;
    $student->samester_id = $sId;
    $student->save();
    return $this->sendResponse($student->toArray(),'create succesfully');
}
public function getStudentAcounte($userId){
    $student = Student::where('user_id', '=' , $userId)->with('samester')->get();
    return $this->sendResponse($student->toArray(),'get succesfully');
}
public function studentUpdate($id , Request $request){
    $student = Student::find($id);
  $student ->name = $request->input('name');
  $student->image = $request->input('image');
  $student->save();
  return $this->sendResponse($student->toArray(),'Update succesfully');

}
// public function deletedStudent($id ){
// $student = Student::find($id);
// $student->delete();
// return $this->sendResponse($student->toArray(),'deleted succesfully');

// }
public function deletedStudent(  Request $request){

    $studetId = explode(",",$request->input('ids')) ;
    Student::whereIn('id',$studetId)->delete();
   return response()->json(['true' => 'deleted Sucsse']);

 }

//////instalements
public function creatInstaToStudent($id , Request $request){
//     $validator = Validator::make($request->all(), [
//         'student_id' => 'unique:instalments',
//    ]);

//    if ($validator->fails()) {
//        $errors = $validator->errors();
//            return response()->json(['status' => false, 'errors' => $errors]);
//    }
    error_log('started');

        $is =  new instalment();
        $is->student_id = $id;
        $is->discointUsdOrPersent = $request->input('discount');
        $is->instalment = $request->input('insta');
        $is->transport = $request->input('trans');
        $is->persent	= $request->input('pearsnt');
        $instaAll =  ($request->input('insta')+$request->input('trans'));
        $instaAfter = $instaAll;
        $pearsnt = $request->input('discount');
        if($request->input('pearsnt') == 1){
            $instaAfter = $instaAll -  (($instaAll*$pearsnt) / 100);
        }
        if($request->input('pearsnt') == 0){
         $instaAfter = $instaAll - $pearsnt;
        }
        if($request->input('pearsnt') == 2){
         $instaAfter = $instaAll ;
        }
        $is->instaAfterPearsent	 = $instaAfter;

        $is->save();
         return $this->sendResponse($is->toArray(),'create succesfully');
}

public function updateInstaToStudent($id , Request $request){
   $is = instalment::find($id);
   $is->discointUsdOrPersent = $request->input('discount');
   $is->instalment = $request->input('insta');
   $is->transport = $request->input('trans');
   $is->persent	= $request->input('pearsnt');
   $instaAll =  ($request->input('insta')+$request->input('trans'));
   $instaAfter = $instaAll;
   $pearsnt = $request->input('discount');
   if($request->input('pearsnt') == 1){
       $instaAfter = $instaAll -  (($instaAll*$pearsnt) / 100);
   }
   if($request->input('pearsnt') == 0){
    $instaAfter = $instaAll - $pearsnt;

   }
   if($request->input('pearsnt') == 2){
    $instaAfter = $instaAll ;
   }
   $is->instaAfterPearsent	 = $instaAfter;

   $is->save();
    return $this->sendResponse($is->toArray(),'create succesfully');

}

public function getInsta($id){
 $insta = instalment::where('student_id', '=' , $id)->get();
 return $this->sendResponse($insta->toArray(),'create succesfully');

}
///pays

public function createPay ($id , Request $request){

$pay = pay::create([
     'pay' => $request->input('pay'),
     'instalment_id' => $id,
     'created_at' => Carbon::now()->format('D, M d, Y h:i A'),
]);
return $this->sendResponse($pay->toArray(),'create succesfully');

}

public function editPay ($id , Request $request){
    $pay = pay::find($id);
     $pay->pay = $request->input('pay');
     $pay->save();

     return $this->sendResponse($pay->toArray(),'update succesfully');


}
public function getPays($id){
$pays = pay::where('instalment_id' , '=' , $id)->get();
return $this->sendResponse($pays->toArray(),'update succesfully');

}
public function deletedPay ($id ){

    $pay = pay::find($id);
     $pay->delete();
     return $this->sendResponse($pay->toArray(),'update succesfully');


}


public function maxIdStudentTable(){
    $lastIdAdd = Student::find(Student::max('id'));
    return $this->sendResponse($lastIdAdd->toArray(),'update succesfully');

}




}
