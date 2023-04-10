<?php

use App\Http\Controllers\Api\User\UserController;
use App\Http\Controllers\Api\Admin\AdminController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::middleware('checkApiPassword')->group(function(){
/* -------------user routes ----------------- */
    Route::prefix('user')->group(function(){
        /* ---------- User Register ------------ */
        Route::post('register' ,[UserController::class ,'userRegister']);
        
        /* ---------- User Login ------------ */
        Route::post('login' ,[UserController::class ,'userLogin']);
        
        /* ---------- User Logout ------------ */
        Route::post('logout' ,[UserController::class ,'userLogout'])->middleware('checkAuthAndToken:user-api');
        
        /* ---------- User Update ------------ */
        Route::post('update' ,[UserController::class ,'userUpdate'])->middleware('checkAuthAndToken:user-api');
        
        /* ---------- User Delete Account ------------ */
        Route::post('delete-account' ,[UserController::class ,'deleteAccount'])->middleware('checkAuthAndToken:user-api');
    });

/* -------------admin routes ----------------- */
    Route::prefix('admin')->group(function(){
    /* ---------- Admin Register ------------ */
    Route::post('register' ,[AdminController::class ,'adminRegister']);
            
    /* ---------- Admin Login ------------ */
    Route::post('login' ,[AdminController::class ,'adminLogin']);
    
    /* ---------- Admin Logout ------------ */
    Route::post('logout' ,[AdminController::class ,'AdminLogout'])->middleware('checkAuthAndToken:admin-api');
    

    
    });
});









