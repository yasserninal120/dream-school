<?php

use App\Http\Controllers\NotificationController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('web', function () {
    return view('search');
});
Route::get('notify', function () {
    $user = User::find(104);
    foreach ($user->app_tokens as $app_token) {
        # code...
        $result = NotificationController::sendNotification($app_token->token,"Test Title","يوال");
        return $result;
    }
});
Route::get('test', function () {
    $users = User::query()->orderBy('id', 'DESC')->with('role');
    $rows = 2;
    return $users->paginate($rows);
    // $users = User::orderBy('created_at','desc')->query();
    // return $this->sendResponse($users->toArray(),'read succesfully');
});
Route::get('cardprint', 'App\Http\Controllers\Controller@cardprint');
Route::get('searchId', 'App\Http\Controllers\Controller@search')->name('searchId');
