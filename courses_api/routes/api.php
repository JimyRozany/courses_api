<?php

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\User\UserController;
use App\Http\Controllers\Api\Admin\AdminController;
use App\Http\Controllers\Api\Course\CourseController;
use App\Http\Controllers\Api\Favorites\FavoritesController;

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
    
    /* ============ Course Routes ============== */
    Route::middleware('checkAuthAndToken:admin-api')->group(function(){

        Route::post('create' ,[CourseController::class ,'createCourse']); // create new course
        Route::post('edit-course' ,[CourseController::class ,'editCourse']); // edit course info
        Route::post('remove-course' ,[CourseController::class ,'removeCourse']); // remove course
        Route::post('add-videos' ,[CourseController::class ,'addVideos']); // add videos to course
        Route::post('edit-video' ,[CourseController::class ,'editVideo']); // edit videos in course
        Route::post('remove-video' ,[CourseController::class ,'removeVideo']); // remove videos in course
    });

    
    });

    /* ------------- Auth Routes --------------- */
    Route::middleware('checkToken')->group(function(){

        Route::post('all-courses' ,[CourseController::class ,'allCourses']); // get all courses
        Route::post('show' ,[CourseController::class ,'showCourse']); // get one course
        Route::post('show-all-videos' ,[CourseController::class ,'allVideos']); // get all videos in course
        Route::post('show-video' ,[CourseController::class ,'showVideo']); // get one video in course
    
        /* ------------- Favorites Routes --------------- */
        Route::post('favorites' ,[FavoritesController::class ,'allCourses']); // get all courses in favorites
        Route::post('add-favorite' ,[FavoritesController::class ,'add']); // add course to favorites
        Route::post('remove-favorite' ,[FavoritesController::class ,'remove']); // remove course from favorites


    });


});











