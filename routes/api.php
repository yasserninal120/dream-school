<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


//user route
Route::post('user/login','App\Http\Controllers\Controller@login');
Route::post('user/create','App\Http\Controllers\Controller@create_user');
// Route::middleware(['jwt.auth'])->group(function () {
//     Route::get('users/{id}', function ($id) {

//     });
// });
Route::middleware('jwt.auth')->put('user/update/{id}/{imageUpdate}','App\Http\Controllers\Controller@updateUser');
Route::middleware('jwt.auth')->delete('user/delete/{id}','App\Http\Controllers\Controller@destroyUser');
Route::middleware('jwt.auth')->get('users','App\Http\Controllers\Controller@viewUsers');
Route::get('test','App\Http\Controllers\Controller@test');
Route::middleware('jwt.auth')->get('use','App\Http\Controllers\Controller@acountDetiles');
/////semester route
Route::middleware('jwt.auth')->post('create/semester','App\Http\Controllers\Controller@createSemester');
Route::middleware('jwt.auth')->put('update/semester/{id}','App\Http\Controllers\Controller@UpdateSemester');
Route::get('view/semesters','App\Http\Controllers\Controller@viewSemesters');
Route::middleware('jwt.auth')->get('view/semester/{id}','App\Http\Controllers\Controller@viewSemester');
Route::middleware('jwt.auth')->delete('delete/semester/{id}','App\Http\Controllers\Controller@destroySemester');
Route::middleware('jwt.auth')->post('addTeacher/semester/{sId}','App\Http\Controllers\Controller@addTeacherTosemaster');

Route::middleware('jwt.auth')->get('view/student/{id}','App\Http\Controllers\Controller@getStudent');
Route::middleware(['jwt.auth'])->group(function () {
    ////post

    Route::get('/posts','App\Http\Controllers\PostController@index');
    Route::post('/posts','App\Http\Controllers\PostController@store');
    Route::get('/posts/{id}','App\Http\Controllers\PostController@show');
    Route::put('/posts/{id}','App\Http\Controllers\PostController@update');
    Route::delete('/posts/{id}','App\Http\Controllers\PostController@destroy');
    ///comment
    Route::get('/posts/{id}/comments','App\Http\Controllers\CommentController@index');
    Route::post('/posts/{id}/comments','App\Http\Controllers\CommentController@store');
    Route::put('/comments/{id}','App\Http\Controllers\CommentController@update');
    Route::delete('/comments/{id}','App\Http\Controllers\CommentController@destroy');
    //like
    Route::post('/posts/{id}/likes','App\Http\Controllers\LikeController@likeOrUnlike');
    Route::put('/get/st/{id}','App\Http\Controllers\Controller@addStudentTosemaseter');
    //student
    Route::post('creatStudent/{id}','App\Http\Controllers\Controller@createStudent');
    Route::put('update/student/{id}/{imageUpdate}','App\Http\Controllers\Controller@studentUpdate');
    Route::get('getStudent/acount/{id}','App\Http\Controllers\CommentController@getStudentAcounte');
    Route::delete('deleted/student','App\Http\Controllers\CommentController@deletedStudent');
    Route::get('/lastIdAdd','App\Http\Controllers\Controller@maxIdStudentTable');
    /////insat
    Route::post('/insta/create/{id}','App\Http\Controllers\Controller@creatInstaToStudent');
    Route::put('/insta/update/{id}','App\Http\Controllers\Controller@updateInstaToStudent');
    Route::get('/get/insta/{id}','App\Http\Controllers\Controller@getInsta');
    //pay
    Route::get('/pay/get/{id}','App\Http\Controllers\Controller@getPays');
    Route::post('/pay/create/{id}','App\Http\Controllers\Controller@createPay');
    Route::put('/pay/update/{id}','App\Http\Controllers\Controller@editPay');
    Route::delete('/pay/deleted','App\Http\Controllers\Controller@deletedPay');

    Route::get('/totalInstaToAcoint/{id}','App\Http\Controllers\Controller@toTallInstaToAcount');

    Route::get('/oneStudent/{id}','App\Http\Controllers\Controller@oneStudent');
    Route::middleware('jwt.auth')->get('usertpprint','App\Http\Controllers\Controller@acountDetilesToprint');
    Route::get('/getTeacher','App\Http\Controllers\Controller@getTeacher');
    Route::get('/getTeacherClass/{id}','App\Http\Controllers\Controller@getTeacherClass');

    Route::delete('/teachetRemoveFromClass/{id}','App\Http\Controllers\Controller@teachetRemoveFromClass');
    Route::get('/date','App\Http\Controllers\Controller@date');
    Route::post('/takeCheckUp','App\Http\Controllers\Controller@takeCheckUp');
    Route::put('/updateTakeCheckUp','App\Http\Controllers\Controller@updateTakeCheckUp');
    Route::get('/check/{id}','App\Http\Controllers\Controller@getCheck');
    Route::get('/getStudentCount','App\Http\Controllers\Controller@countStudent');
    Route::post('/addHomWork/{smid}','App\Http\Controllers\Controller@addHomWork');
    Route::put('/updateHomWork/{hid}','App\Http\Controllers\Controller@updateHomWork');
    Route::get('/gethomwork/{sid}','App\Http\Controllers\Controller@gethomwork');
    Route::delete('/deleteHom/{hid}','App\Http\Controllers\Controller@deleteHom');
    Route::put('/updatenew','App\Http\Controllers\Controller@updatenew');


});



