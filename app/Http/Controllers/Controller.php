<?php
namespace App\Http\Controllers;

use App\Models\Homwork;
use App\Models\instalment;
use App\Models\MorningCheckUp;
use App\Models\Note;
use App\Models\pay;
use App\Models\post;
use App\Models\Role;
use App\Models\Samester;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\teacherSamester;
use App\Models\User;
use Illuminate\Support\Facades\DB;

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
use Illuminate\Support\Facades\Crypt;
use Mockery\Matcher\Not;

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
            'password' => 'required',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:10048',

        ]);

        $creaetUser = new User();
        $creaetUser->name = $request->get('name');
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
        $creaetUser->text_plane = $request->get('password');
        $creaetUser->note = $request->input('note');
        if($request->input('phone')){
           $creaetUser->phoneNumber = $request->input('phone');
        }else{
            $creaetUser->phoneNumber ="000";

        }
        $creaetUser->save();

        $user = User::first();
        $token =  JWTAuth::fromUser($user);
      //// add student to student table
        $email = $request->get('name');
        $getUser = User::where('name' , '=' , $email )->get();
        foreach($getUser as $user){
            $role_id = $user->role_id;
        }
        if($role_id == 2){
           $teacher =  Teacher::create([
                'user_id' => $user -> id,
            ]);

        }

         if($role_id == 3){
            $is =  new instalment();
            $is->user_id = $creaetUser->id;
            $is->discointUsdOrPersent = $request->input('discount');
            $is->instalment = $request->input('insta');
            $is->transport = $request->input('trans');
            $is->persent	= $request->input('pearsnt');
            $instaAll =  ($request->input('insta'));
            $instaAfter = $instaAll +$request->input('trans');
            $pearsnt = $request->input('discount');
            if($request->input('pearsnt') == 1){
                $instaALlP = $instaAll -  (($instaAll*$pearsnt) / 100);
                  $instaAfter  = $instaALlP + $request->input('trans');
            }
            if($request->input('pearsnt') == 0){
             $instaALlP = $instaAll - $pearsnt;
             $instaAfter  = $instaALlP + $request->input('trans');
            }
            if($request->input('pearsnt') == 2){
             $instaAfter = $instaAll +$request->input('trans');
            }
            if($request->input('pearsnt') == 3){
                $instaAfter = $request->input('munInstan') +$request->input('trans');
           }
            $is->instaAfterPearsent	 = $instaAfter;

            $is->save();
            return response()->json(['tre' => 'ee'],200);

         }




        error_log('finished');
        return response() -> json(['user' => $creaetUser],200);
    }


    public function login(Request $request){
        $validated = Validator::make($request->all(),[
            'name' => 'required',
            'password' => 'required',
        ]);

        if($validated -> fails()){
            return response()->json($validated -> errors());
        }

        $credentials = $request->only('name','password');
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
            'name' => 'required',
            'password' => 'required',
            // 'role_id' =>  'required',
            // 'role_id' =>  'required|exists:roles,id',
        ]);
        $role_id = $request->input('role');
        if($validated -> fails()){
            return $this->sendError('error validation',$validated -> errors());
        }
        $user  = User::find($id);

            $t= Teacher::where('user_id','=',$id)->count();

                   if($request->input('role') == 2){
                       if($t == 0){
                       $teacher = new Teacher();
                       $teacher->user_id = $user->id;
                       $teacher->save();
                       }

                   }else{
                       if($request->input('role') != 2){
                        Teacher::where('user_id','=',$id)->delete();
                       }
                   }



        // if($t){
        //     return "[d]";
        //     if($request->input('role') == 2){
        //         $teacher =  Teacher::create([
        //             'user_id' => $user -> id,
        //         ]);
        //         return $t;
        //     }else{
        //         if($request->input('role') != 2){
        //             Teacher::where('user_id','=',$user->id)->delete();
        //         }
        //     }

        //     }

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
        $user->name = $input['name'];
        $user->password = Hash::make($request->get('password'));
        $user->role_id = $input['role'];
        $user->image = $url;
        $user->text_plane = $request->get('password');
        $user->note = $request->input('note');
        if($request->input('phone')){
            $user->phoneNumber = $request->input('phone');
         }else{
             $user->phoneNumber ="000";

         }
        $user->save();
        return $this->sendResponse($user->toArray(),'Update succesfully');

    }
    public function destroyUser($id){
        $user = User::find($id);
        if($user->role_id == 1 || $user->role_id == 2){
            $post = post::where('user_id' , '=', $id)->delete();
            $user->delete();
           return $this->sendResponse($user->toArray(),'deleted succesfully');

        }else{
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
        $semestare = new Samester();
        $semestare->id = (Samester::max('id')+1);
        $semestare->name = $request->input('name');
        $semestare->save();
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
        $st= Student::where('samester_id', '=',$id)->get();
        if($st){
            foreach($st as $s){
                Student::where('samester_id','=', $id)->update([
                    'samester_id' => 1,
                ]);
             }
        }

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

    public function addTeacherTosemaster($sId ,Request $request){
        $ifound =  DB::table('teacher_samesters')->where('teacher_id', '=', $request->input('ids'))->where('semester_id','=',$sId)->count();

      if(str_contains($request->input('ids'),",")){
        $techerId = explode(",",$request->input('ids'));
        foreach($techerId as $t){
            $ifound =  DB::table('teacher_samesters')->where('teacher_id', '=', $t)->where('semester_id','=',$sId)->count();
            if($ifound == 0){
               $ts = new  teacherSamester();
               $ts->semester_id = $sId;
               $ts->teacher_id = $t;
               $ts->save();
            }
        }

      }else{
        $ifound =  DB::table('teacher_samesters')->where('teacher_id', '=', $request->input('ids'))->where('semester_id','=',$sId)->count();
            if($ifound == 0){
               $ts = new  teacherSamester();
               $ts->semester_id = $sId;
               $ts->teacher_id = $request->input('ids');
               $ts->save();
            }
      }
       return response()->json(['tru' => 'succ']);

        //   return $samester= Samester::where('id','=',$sId)->with('teacher')->get();
        // if(str_contains($request->input('ids'),",")){
        //     $techerId = explode(",",$request->input('ids'));
        //     foreach ($techerId as $t_id) {
        //         $s = Samester::where('id','=',$sId)->whereHas('teacher',function($q) use($t_id){
        //             return $q->where('teacher_id','=',$t_id);
        //         })->count();
        //       if($s == 0){
        //           $samester->teacher()->attach([$t_id]);
        //       }
        //     }
        // }else{
        //     $id = $request->input('ids');
        //     $s = Samester::where('id','=',$sId)->whereHas('teacher',function($q) use($id){
        //         return $q->where('teacher_id','=',$id);
        //     })->count();
        //         if($s == 0){
        //             $samester->teacher()->attach([$id]);
        //         }
        // }
        // return response()->json(['true' => 'update Sucsse']);
    }

     public function addStudentTosemaseter( $id , Request $request){
        if(str_contains($request->input('ids'),",")){
            $studetId = explode(",",$request->input('ids')) ;
            // foreach($studetId as $key => $value){
            //     $student = Student::find($value['ids']);
            //     $student->samester_id = $id;

            // }
            // $t =Student::whereIn('id',$studetId)->update(['samester_id' => $id]);
            foreach ($studetId as $s_id) {
                Student::find($s_id)->update(['samester_id' => $id]);
            }
        }else{
            Student::find($request->input('ids'))->update(['samester_id' => $id]);

        }

        // echo "<pre>"; print_r($studetId); die;
       return response()->json(['true' => 'update Sucsse']);

     }
    public function test(){
        $Users = User::orderBy('created_at','desc')->where("role_id",'!=',3)->with('role')->get();
        return $this->sendResponse($Users->toArray(),'read succesfully');
    }
    public function acountDetiles(){
        $id = auth()->user()->id;
    $user_login = User::where('id', '=', $id)->with('role')->get();
    foreach($user_login as $u ){
           $name = $u->name;
           $role= $u->role->name;
           $role_id= $u->role->id;
           $id = $u->id;

    }

    return response()->json(['name' =>$name, 'role' => $role ,'role_id' => $role_id ,'id' => $id ],200);
    }

    public function acountDetilesToprint(){
        $id1 = auth()->user()->id;
        $user_login = User::where('id', '=', $id1)->with('role')->get();
        foreach($user_login as $u ){
               $name = $u->name;
               $role= $u->role->name;
               $role_id= $u->role->id;
               $id = $u->id;
               $pass = $u->text_plane;
               $imag = $u->image;

        }

        return response()->json(['name' =>$name, 'role' => $role ,'role_id' => $role_id ,'id' => $id , 'pass' => $pass ,'imag' =>$imag],200);
    }

    public function getStudent($id){
        $studetn = Student::orderBy('created_at','desc')->where('samester_id','!=',$id)->with('samester')->with('user')->get();
        return $this->sendResponse($studetn->toArray(),'read succesfully');

    }
/////////student
public function createStudent($userId  ,Request $request){
    // $max = DB::table('students')->max('id') + 1;
    // DB::students("ALTER TABLE users AUTO_INCREMENT =  $max");
    $name = $request->input('name');
    $sId = $request->input('sId');
    $nameClass = $request->input('className');
    if($request->input('image')){
        $image = base64_decode($request->input('image'));
        $png_url = time().".png";
         file_put_contents(public_path('/storage/image_profile/').$png_url, $image);
        $url = 'image_profile/'.$png_url;

    }else{
        $url = null;
    }
    $student = new Student();
    $student->id = (Student::max('id')+1);
    $student->user_id = $userId;
    $student->name = $name;
    $student->image = $url;
    $student->samester_id = $sId;
    $student->className = $nameClass;
    $student->save();
////////////////////

         return $this->sendResponse($student->toArray(),'create succesfully');












    return $this->sendResponse($student->toArray(),'create succesfully');
}
public function getStudentAcounte($userId){
    $student = Student::orderBy('created_at','desc')->where('user_id', '=' , $userId)->with('samester')->get();
    return $this->sendResponse($student->toArray(),'get succesfully');
}
public function studentUpdate($id , Request $request ,$imageUpdate){
    $student = Student::find($id);
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
  $student ->name = $request->input('name');
  $nameClass = $request->input('className');
  $student->image = $url;
  $student->className = $nameClass;
  $student->samester_id = $request->input('sId');

  $student->save();
  return $this->sendResponse($student->toArray(),'Update succesfully');

}
// public function deletedStudent($id ){
// $student = Student::find($id);
// $student->delete();
// return $this->sendResponse($student->toArray(),'deleted succesfully');

// }
public function deletedStudent(  Request $request){
    if(str_contains($request->input('ids'),",")){
        $studetId = explode(",",$request->input('ids')) ;
        foreach ($studetId as $s_id) {
            Student::find($s_id)->delete();
        }
    }else{
        Student::find($request->input('ids'))->delete();

    }

   return response()->json(['true' => 'deleted Sucsse']);

 }

//////instalements
public function creatInstaToStudent($id , Request $request){

    error_log('started');

        $is =  new instalment();
        $is->student_id = $id;
        $is->discointUsdOrPersent = $request->input('discount');
        $is->instalment = $request->input('insta');
        $is->transport = $request->input('trans');
        $is->persent	= $request->input('pearsnt');
        $instaAll =  ($request->input('insta'));
        $instaAfter = $instaAll +$request->input('trans');
        $pearsnt = $request->input('discount');
        if($request->input('pearsnt') == 1){
            $instaALlP = $instaAll -  (($instaAll*$pearsnt) / 100);
              $instaAfter  = $instaALlP + $request->input('trans');
        }
        if($request->input('pearsnt') == 0){
         $instaALlP = $instaAll - $pearsnt;
         $instaAfter  = $instaALlP + $request->input('trans');
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
   $instaAll =  ($request->input('insta'));
   $instaAfter = $instaAll +$request->input('trans');
   $pearsnt = $request->input('discount');
   if($request->input('pearsnt') == 1){
       $instaALlP = $instaAll -  (($instaAll*$pearsnt) / 100);
         $instaAfter  = $instaALlP + $request->input('trans');
   }
   if($request->input('pearsnt') == 0){
    $instaALlP = $instaAll - $pearsnt;
    $instaAfter  = $instaALlP + $request->input('trans');
   }
   if($request->input('pearsnt') == 2){
    $instaAfter = $instaAll +$request->input('trans');
    $is->discointUsdOrPersent = 0;
   }
   if($request->input('pearsnt') == 3){
        $instaAfter = $request->input('munInstan')+$request->input('trans');
        $is->discointUsdOrPersent = 0;
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
     'user_id' => $id,
     'created_at' => Carbon::now()->format('d/m/Y'),
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
$pays = pay::orderBy('created_at','desc')->where('user_id' , '=' , $id)->get();
return $this->sendResponse($pays->toArray(),'update succesfully');

}
public function deletedPay (Request $request ){
    if(str_contains($request->input('ids'),",")){
        $payID = explode(",",$request->input('ids')) ;
        foreach ($payID as $s_id) {
            pay::find($s_id)->delete();
        }
    }else{
        pay::find($request->input('ids'))->delete();

    }
    return response()->json(['true' => 'deleted Sucsse']);

}

public function maxIdStudentTable(){
    $lastIdAdd = Student::find(Student::max('id'));
    return $this->sendResponse($lastIdAdd->toArray(),'update succesfully');

}


public function toTallInstaToAcount($userId){
    $st = instalment::where('user_id','=',$userId)->get();
    $instan =[];
    $pay = [];
    foreach ($st as $order =>$v)
     $instan[$order] = $v->instaAfterPearsent;
     $totalInsta = array_sum($instan);

    $p1 = User::where("id", '=',$userId)->with('pays')->get();
    foreach($p1 as $t )
    $e = $t->pays->sum('pay');

     $alb = $totalInsta - $e;
    return response()->json(['totalInsta' =>$totalInsta, 'alb' => $alb],200);

}
public function oneStudent($id){
    $oneStudent = Student::where('id','=',$id)->with('samester')->with('user')->get();
    return $this->sendResponse($oneStudent->toArray(),'update succesfully');

}
public function getTeacher(){
    $teacher= Teacher::orderBy('created_at','desc')->with('user')->get();
    return $this->sendResponse($teacher->toArray(),'update succesfully');

}

public function getTeacherClass($id){
    $t = Teacher::whereHas('semester' , function($q) use($id){
         return $q->where('semester_id' , '=' ,$id);
    })->with('user')->get();

    return $this->sendResponse($t->toArray(),'update succesfully');

}


public function teachetRemoveFromClass($sId , Request $request){
    if(str_contains($request->input('ids'),",")){
        $tId = explode(",",$request->input('ids')) ;
        foreach ($tId as $t_id) {
            $r = DB::table('teacher_samesters')->where('teacher_id', $t_id)->where('semester_id' , '=' ,$sId)->count();
            if($r == 1){
                DB::table('teacher_samesters')->where('teacher_id', $t_id)->where('semester_id' , '=' ,$sId)->delete();
            }
        }
    }else{
        $r = DB::table('teacher_samesters')->where('teacher_id', $request->input('ids'))->where('semester_id' , '=' ,$sId)->count();
        if($r == 1){
            DB::table('teacher_samesters')->where('teacher_id', $request->input('ids'))->where('semester_id' , '=' ,$sId)->delete();
        }

    }
    return response()->json(['true' => 'deleted Sucsse']);


}
public function date(){
    $row = User::first();
    // return  Carbon::now()->format('l Y m d');
 Carbon::now()->locale("ar_SA")->translatedFormat("'l Y m d");
   $pays = pay::orderBy('created_at','desc')->where('user_id' , '=' , 79)->get();
foreach($pays as $p){
    $t = $p->created_at;

}
   return $this->sendResponse($pays->toArray(),'update succesfully');

}

public function takeCheckUp(Request $request){
     $date  = Carbon::now()->locale("ar_SA")->translatedFormat("'l Y m d");
   if($request->input('ids') != null){
    if(str_contains($request->input('ids'),",")){
        $tId = explode(",",$request->input('ids'));
        foreach ($tId as $s_id) {
            $ifFindCheck = MorningCheckUp::where('student_id', '=', $s_id)->where('date' , '=' , $date)->count();
            if($ifFindCheck == 1){

            }else{
                $check =   new MorningCheckUp();
                $check->student_id = $s_id;
                $check->checkUp	 = $request->input('check');
                $check->date = $date;
                $check->new = 1;
                $check->save();
            }

        }

    }else{
        $ifFindCheck = MorningCheckUp::where('student_id', '=', $request->input('ids'))->where('date' , '=' , $date)->count();
            if($ifFindCheck == 1){
            }else{
                $check =   new MorningCheckUp();
                $check->student_id = $request->input('ids');
                $check->checkUp	 = $request->input('check');
                $check->date = $date;
                $check->new = 1;
                $check->save();
            }

    }
    return response()->json(['true' => 'checked Sucsse']);
   }else{
    return response()->json(['true' => 'checked Sucsse']);

   }

}
public function updateTakeCheckUp(Request $request){
    if($request->input('ids') != null){
        if(str_contains($request->input('ids'),",")){
            $tId = explode(",",$request->input('ids'));
            foreach ($tId as $s_id) {
            $max = MorningCheckUp::where('student_id','=',$s_id)->max('id');
                $check = MorningCheckUp::find($max);
                $check->checkUp	 = $request->input('check');
                $check->save();
            }

        }else{
            $max = MorningCheckUp::where('student_id','=',$request->input('ids'))->max('id');
            $check = MorningCheckUp::find($max);
            $check->checkUp	 = $request->input('check');
            $check->save();
        }
        return response()->json(['true' => 'checked Update']);
    }else{
        return response()->json(['true' => 'checked Update']);

    }


}

public function getCheck($id){
    $daate = Carbon::now()->locale("ar_SA")->translatedFormat("'l Y m d");
    MorningCheckUp::where('date', '!=' ,$daate)->update([
          'new' => 0,
      ]);
    $check  = MorningCheckUp::orderBy('created_at','desc')->where('student_id' , '=' , $id)->with('student')->get();
    return $this->sendResponse($check->toArray(),'update succesfully');


}
public function countStudent(){
    $countStudent = Student::all()->count();
    return response()->json(['StudentCount' => $countStudent]);

}


public function addHomWork($smid  , Request $request){
      $id = Auth::user()->id;


      if($request->input('image')){
        $image = base64_decode($request->input('image'));
        $png_url = time().".png";
         file_put_contents(public_path('/storage/image_profile/').$png_url, $image);
        $url = 'image_profile/'.$png_url;
    }else{
        $url = "";
    }

    if($request->hasfile('aduio')){
            $file = $request->file('aduio');
            $extention = $file->getClientOriginalExtension();
            $filename = time().'.'.$extention;
            $file->move(public_path('/storage/audio/'),$filename);
            $audoiUrl = 'audio/'.$filename;
        }else{
            $audoiUrl = "";
        }
    $containHomwork = $request->input('contianHomwork');
    $object = $request->input('ob');
    $date = $request->input('date');
    $homwork = new Homwork();
    $homwork->user_id = $id;
    $homwork->semester_id = $smid;
    $homwork->contain_homwork = $containHomwork;
    $homwork->name_object	= $object;
    $homwork->date = Carbon::now()->locale("ar_SA")->translatedFormat("'l Y m d");
    $homwork->new = 1;
    $homwork->image = $url;
    $homwork->audio = $audoiUrl;
    $homwork->save();
    return $this->sendResponse($homwork->toArray(),'update succesfully');


}
public function updateHomWork($hId  , $imageUpdate, Request $request){
       $homEdit = Homwork::find($hId);
       if($homEdit){
        if($request->hasfile('aduio')){
            $file = $request->file('aduio');
            $extention = $file->getClientOriginalExtension();
            $filename = time().'.'.$extention;
            $file->move(public_path('/storage/audio/'),$filename);
            $audoiUrl = 'audio/'.$filename;
        }else{
            $audoiUrl = "";
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
        $containHomwork = $request->input('contianHomwork');
        $object = $request->input('ob');
        $homEdit->contain_homwork = $containHomwork;
        $homEdit->name_object	= $object;
        $homEdit->audio = $audoiUrl;
        $homEdit->image = $url;
        $homEdit->save();
        return $this->sendResponse($homEdit->toArray(),'update succesfully');
       }else{
           return response()->join(['fale' => 'note found homwork']);
       }
}

public function gethomwork($sid){
    $daate = Carbon::now()->locale("ar_SA")->translatedFormat("'l Y m d");
      Homwork::where('date', '!=' ,$daate)->update([
          'new' => 0,
      ]);
     $homSe = Homwork::orderBy('created_at','desc')->with('semster')->with('user')->whereHas('semster',function($q) use($sid){
         return $q->where('id', '=', $sid);
     })->get();
     return $this->sendResponse($homSe->toArray(),'update succesfully');
    }

    public function deleteHom(Request $request){

        if(str_contains($request->input('ids'),",")){
            $tId = explode(",",$request->input('ids'));
            foreach ($tId as $s_id) {
                $check = Homwork::find($s_id);
                $check->delete();
            }
            return response()->json(['deleted' => 'succses']);

        }else{
            $h = Homwork::find($request->input('ids'));
            $h->delete();
            return response()->json(['deleted' => 'succses']);

        }

    }

public function updatenew(){
    $daate = Carbon::now()->locale("ar_SA")->translatedFormat("'l Y m d");
      Homwork::where('date', '!=' ,$daate)->update([
          'new' => 0,
      ]);
      return response()->json(['true' => 'update suc']);

}


public function addNote($stid, Request $request){
  $id = Auth::user()->id;
  $d = Carbon::now()->locale("ar_SA")->translatedFormat("l Y m d");
  $note = new Note();
             $note->student_id = $stid;
             $note->note = $request->input('note');
             $note->user_id= $id;
             $note->date = $d;
             $note->new = 1;
             $note->save();

  return $this->sendResponse($note->toArray(),'update succesfully');
}
public function addNoteAll(Request $request){
$id = Auth::user()->id;
 $d = Carbon::now()->locale("ar_SA")->translatedFormat("l Y m d");
if($request->input('ids') != null){
    if(str_contains($request->input('ids'),",")){
        $tId = explode(",",$request->input('ids'));
        foreach ($tId as $s_id) {
             $note = new Note();
             $note->student_id = $s_id;
             $note->note = $request->input('note');
             $note->user_id= $id;
             $note->date = $d;
             $note->new = 1;
             $note->save();

        }
    }
    return response()->json(['true' => "create succ"]);
}
}

public function updateNote($id ,Request $request){
  $note = Note::find($id);
  if($note){
      $note->note = $request->input('note');
      $note->save();
      return response()->json(['true' => "update succ"]);
  }else{
    return response()->json(['true' => "note founds succ"]);
  }
}

public function deletednote(Request $request){
    if($request->input('ids') != null){
        if(str_contains($request->input('ids'),",")){
            $tId = explode(",",$request->input('ids'));
            foreach ($tId as $s_id) {
              $n =  Note::find($s_id);
              $n->delete();
            }
        }else{
             $n =  Note::find($request->input('ids'));
             $n->delete();
        }
        return response()->json(['true' => "deleted succ"]);
    }
}
public function getNotes($id){
    $daate = Carbon::now()->locale("ar_SA")->translatedFormat("'l Y m d");
      Note::where('date', '!=' ,$daate)->update([
          'new' => 0,
      ]);

      $nots = Note::orderBy('created_at','desc')->where('student_id', '=',$id )->with('user')->with('student')->get();
      return $this->sendResponse($nots->toArray(),'update succesfully');

}


public function PageStudentAcount(){
    $Users = User::orderBy('created_at','desc')->where("role_id",'=',3)->with('instalment')->with('role')->get();
    return $this->sendResponse($Users->toArray(),'read succesfully');

}
public function detilesK(){
    $user = Auth::user()->id;
    $u = User::where('id', '=' , $user)->with('instalment')->get();
    return $this->sendResponse($u->toArray(),'update succesfully');

}

}

