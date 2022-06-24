<?php

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
Route::middleware('jwt.auth')->put('user/update/{id}','App\Http\Controllers\Controller@updateUser');
Route::middleware('jwt.auth')->delete('user/delete/{id}','App\Http\Controllers\Controller@destroyUser');
Route::middleware('jwt.auth')->get('/user', function (Request $request) {
    return auth()->user()->role_id->role()->name;
});
/////semester route
Route::middleware('jwt.auth')->post('create/semester','App\Http\Controllers\Controller@createSemester');
Route::middleware('jwt.auth')->put('update/semester/{id}','App\Http\Controllers\Controller@UpdateSemester');
Route::get('view/semesters','App\Http\Controllers\Controller@viewSemesters');
Route::middleware('jwt.auth')->get('view/semester/{id}','App\Http\Controllers\Controller@viewSemester');
Route::middleware('jwt.auth')->delete('delete/semester/{id}','App\Http\Controllers\Controller@destroySemester');
Route::middleware('jwt.auth')->post('addTeacher/semester/{sId}/{tId}','App\Http\Controllers\Controller@addTeacherTosemaster');



