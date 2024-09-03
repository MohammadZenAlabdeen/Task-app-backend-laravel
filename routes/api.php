<?php

use App\Http\Controllers\FriendController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::post('/register',[UserController::class,'store']);
Route::post('/login',[UserController::class,'login']);
Route::middleware('auth:sanctum')->group(function (){
    Route::post('/logout',[UserController::class,'logout']);

    Route::post('/task/create',[TaskController::class,'store']);
    Route::get('/task/get',[TaskController::class,'index']);
    Route::get('/task/solo',[TaskController::class,'soloTasks']);
    Route::put('/task/update/{id}',[TaskController::class,'update']);
    Route::delete('/task/delete/{id}',[TaskController::class,'destroy']);

    Route::post('/group/create',[GroupController::class,'store']);
    Route::get('/group/getAll',[GroupController::class,'index']);
    Route::get('/group/show/{id}',[GroupController::class,'show']);
    Route::put('/group/update/{id}',[GroupController::class,'update']);
    Route::delete('/group/delete/{id}',[GroupController::class,'destroy']);
    Route::put('/group/addMembers/{id}',[GroupController::class,'addMembersToGroup']);

    Route::post('/friend/send/{id}',[FriendController::class,'sendFriendRequest']);
    Route::get('/friend/received',[FriendController::class,'receivedFriendRequests']);
    Route::post('/friend/accept/{id}',[FriendController::class,'accept']);
    Route::get('/friend/index',[FriendController::class,'friends']);
    Route::delete('/friend/declined/{id}',[FriendController::class,'decline']);

});
