<?php

namespace App\Http\Controllers\Api\Admin;

use \getID3;
use App\Models\Admin;
use App\Models\VideoInfo;
use App\Models\CourseInfo;
use Illuminate\Http\Request;
use Dflydev\DotAccessData\Data;
use App\Http\Traits\ResponseTrait;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Constraint\Count;
use Symfony\Component\Console\Input\Input;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;



class AdminController extends Controller
{
    use ResponseTrait;
    /* ---------- Admin Register -------------------- */
    public function adminRegister(Request $request)
    {
        try {
            $validate = $request->validate([
                'user_name'=>'required',
                'email'=>'required|email',
                'password'=>'required',
                'phone'=>'required|numeric',
            ]);
           try {


            $admin = Admin::create([
                'user_name'=>$request->input('user_name'),
                'email'=>$request->input('email'),
                'phone'=>$request->input('phone'),
                'password'=>bcrypt($request->input('password')),
            ]);

            return $this->responseSuccess('registration successfully' ,201);


           } catch (\Throwable $th) {
            return $this->responseError("user already exits" ,422);
           }
        } catch (\Throwable $th) {

            return $this->responseError($th->getMessage() ,400);
        }
        
        // if( $validate ){
        //     try {
        //         $user = User::create([
        //             'user_name'=>$request->input('user_name'),
        //             'email'=>$request->input('email'),
        //             'phone'=>$request->input('phone'),
        //             'premium'=>$request->input('premium'),
        //             'password'=>bcrypt($request->input('password')),
        //         ]);
        //         return $this->responseSuccess('registration successfully' ,201);
        //     } catch (\Exception $ex) {
        //         return $this->responseError($ex->getMessage() ,$ex->getCode());
        //     }
        // }else{
        //     return $this->responseError('registration error' ,417);
        // }
        
    }
    
    /* ---------- Admin Login -------------------- */
     public function adminLogin(Request $request)
     {
         try{
             // validation
             $request->validate([
                 'email' => 'required|email',
                 'password' => 'required',
 
             ]) ;
             $credentials = $request->only(['email' ,'password']);
             // generate toke
             $token = Auth::guard('admin-api')->attempt($credentials);
     
             if(!$token){
                 return $this->responseError('user not found',404);
             }
     
             $admin = Auth::guard('admin-api')->user();
             return $this->responseData('admin' ,$admin ,$token ,false ,'login successfully' ,200);
                 
         }catch(\Exception $ex){
             return $this->responseError($ex->getMessage(),200);
         }
         
     }

    /* ---------- Admin Logout -------------------- */
     public function adminLogout(Request $request)
     {
        $token = $request->input('token');
         
        if($token)
        {
         try {
             JWTAuth::setToken($token)->invalidate();
             return $this->responseSuccess('logged out successfully' ,200);
         } catch (TokenInvalidException $ex) {
             return $this->responseError('invalid Token',498);
         }
        }else{
         return $this->responseError('invalid Token',498);
        }
     }

      /* ---------- Create Course -------------------- */
      public function createCourse(Request $request)
      {

        try{
            $request->validate([
                'course_name' => 'required',
                'course_img' => 'required|image',
            ]);
        }catch(\Exception $ex){
            return $this->responseError($ex->getMessage() ,400);
        }

       try {
         // handle image 
         $imageName = time() . '.' . $request->file('course_img')->getClientOriginalExtension() ;
         //  store image in public folder
         $request->file('course_img')->move(public_path('course/images'),$imageName);
         $imagePath = 'course/images/' . $imageName;
 
         //create new course 
         $course = CourseInfo::create([
             'admin_id' => auth()->user()->id,
             'course_name' => $request->input('course_name'),
             'course_img_path' => $imagePath ,
             'number_of_videos' => 0 ,
         ]);

         return $this->responseSuccess('course created successfuly' ,201);
       } catch (\Exception $ex) {
        return $this->responseError($ex->getMessage() ,500);
        
       }
      }

      /* ---------- upload videos to  Course -------------------- */
      public function addVideos(Request $request){

        try{
            $request->validate([
                'course_id' => 'required',
                'title' => 'required',
                'video' => 'required|mimes:mp4,ogx,oga,ogv,ogg,webm',
            ]);
        }catch(\Exception $ex){
            return $this->responseError($ex->getMessage() ,400);
        }

        try{
                // handle video 
                $videoName = time() .'.'. $request->file('video')->getClientOriginalExtension();
                // store video in public/course/videos 
                $request->file('video')->move(public_path('course/videos'),$videoName);
                $videoPath = 'course/videos/' . $videoName;
                // calculate video duration
                $getID3 = new getID3;
                $file = $getID3->analyze($videoPath);
                $videoDuration = date('H:i:s.v', $file['playtime_seconds']);
        
                // add video 
        
                $vid = VideoInfo::create([
                    'course_id' => $request->input('course_id'),
                    'title' => $request->input('title'),
                    'video_path' => $videoPath,
                    'video_duration' => $videoDuration,
                ]);

            return $this->responseSuccess('video added successfuly' ,201);
        } catch (\Exception $ex) {
        return $this->responseError($ex->getMessage() ,500);
        
        }
      }
      
}
