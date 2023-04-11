<?php

namespace App\Http\Controllers\Api\Course;

use getID3;
use App\Models\VideoInfo;
use App\Models\CourseInfo;
use Illuminate\Http\Request;
use App\Http\Traits\ResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\CourseCollection;
use App\Http\Resources\CourseResource;
use App\Http\Resources\VideoCollection;
use App\Http\Resources\VideoResource;
use Illuminate\Support\Facades\File;


class CourseController extends Controller
{
    use ResponseTrait ; 

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

    /* ---------- Edit Course -------------------- */
    public function editCourse(Request $request)
    {
        try{
            $request->validate([
                'course_id' => 'required',
                'course_name' => 'nullable',
                'course_img' => 'nullable|image', 
            ]);
        }catch(\Exception $ex){
            return $this->responseError($ex->getMessage() ,400);
        }

        $course = CourseInfo::find($request->input('course_id'));
        
        if(!$course)
            return $this->responseError('course not found' ,404); 

        
        if($request->input('course_name'))
        {
            $course->course_name = $request->input('course_name');
            $course->save();
        }

        if($request->file('course_img'))
        {
            // handle image 
            $imageName = time() . '.' . $request->file('course_img')->getClientOriginalExtension() ;
            //  store image in public folder
            $request->file('course_img')->move(public_path('course/images'),$imageName);
            $imagePath = 'course/images/' . $imageName;

            $course->course_img_path = $imagePath;
            $course->save();
        }
        
        return $this->responseSuccess('updated Successfuly' ,200);

    }

    /* ---------- Remove Course -------------------- */
    public function removeCourse(Request $request)
    {
        try{
            $request->validate([
                'course_id' => 'required',
            ]);
        }catch(\Exception $ex){
            return $this->responseError($ex->getMessage() ,400);
        }

        $course = CourseInfo::find($request->input('course_id'));
        
        if(!$course)
        {
            return $this->responseError('Course Not Found' ,404); 
        }
        
        /* delete all videos */
        foreach($course->videos as $video)
        {
            // delete video from public folder 
            if(File::exists($video['title']))
            {
                File::delete($video['video_path']);
            }
        }

        $course->delete();

        return $this->responseSuccess('Course Deleted Successfuly' ,202);

    }

     /* ---------- Get all Courses -------------------- */
     public function allCourses()
     {
        $courses = CourseInfo::all();
        return CourseCollection::make($courses);

     }
     /* ---------- Get Course by id -------------------- */
     public function showCourse(Request $request)
     {
        $course = CourseInfo::find($request->input('course_id'));
        if(!$course)
        {
            return $this->responseError('course not found',404);
        }
        return CourseResource::make($course);
     }
    
  /* ------------------------ video---------------- */  
    /* ---------- upload videos to  Course -------------------- */
    public function addVideos(Request $request)
    {
        
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
                // update course info 'number of videos'
                $course = CourseInfo::find($request->input('course_id'));
                $course->update([
                    'number_of_videos' => $course->number_of_videos + 1
                ]);
            return $this->responseSuccess('video added successfuly' ,201);
        } catch (\Exception $ex) {
        return $this->responseError($ex->getMessage() ,500);
        
        }
    }

    /* ---------- edit videos in Course -------------------- */
    public function editVideo(Request $request)
    {
        // 'course_id',
        // 'title',
        // 'video_path',
        try{
            $request->validate([
                'video_id' => 'required',
                'title' => 'nullable',
                'video' => 'nullable|mimes:mp4,ogx,oga,ogv,ogg,webm', 
            ]);
        }catch(\Exception $ex){
            return $this->responseError($ex->getMessage() ,400);
        }

        $videObj = VideoInfo::find($request->input('video_id'));

        if(!$videObj)
            return $this->responseError('Video Not Found' ,404); 

        if($request->input('title'))
        {
            $videObj->title = $request->input('title');
            $videObj->save();
        }

        if($request->input('video'))
        {
            // handle video 
            $videoName = time() .'.'. $request->file('video')->getClientOriginalExtension();
            // store video in public/course/videos 
            $request->file('video')->move(public_path('course/videos'),$videoName);
            $videoPath = 'course/videos/' . $videoName;
            // calculate video duration
            $getID3 = new getID3;
            $file = $getID3->analyze($videoPath);
            $videoDuration = date('H:i:s.v', $file['playtime_seconds']);
            
            $videObj->video_path = $videoPath;
            $videObj->video_duration = $videoDuration;
            $videObj->save();
        }


        return $this->responseSuccess('Video Updated Successfuly' ,200);
      
    }

     /* ---------- show all videos in Course -------------------- */
     public function allVideos(Request $request)
     {
        $course = CourseInfo::find($request->input("course_id"));
        if(!$course)
        {
            return $this->responseError('video not found' ,404);
        } 
        $allVideos = $course->videos;
        return VideoCollection::make($allVideos);
     }
     /* ---------- show videos in Course -------------------- */
     public function showVideo(Request $request)
     {
        $video = VideoInfo::find($request->input('video_id'));
        if(!$video)
        {
            return $this->responseError('video not found' ,404);
        }
        return VideoResource::make($video);

     }
     /* ---------- remove videos in Course -------------------- */
     public function removeVideo(Request $request)
     {
        try{
            $request->validate([
                'video_id' => 'required',
            ]);
        }catch(\Exception $ex){
            return $this->responseError($ex->getMessage() ,400);
        }

        $videObj = VideoInfo::find($request->input('video_id'));

        if(!$videObj)
        {
            return $this->responseError('Video Not Found' ,404); 
        }

        // remove video from public folder
        if(File::exists($videObj->video_path))
        {
            File::delete($videObj->video_path);
        }

        $videObj->delete();
        
        if($videObj->course->number_of_videos > 0){
            $videObj->course->number_of_videos -= 1;
            $videObj->course->save();
        }
        

        return $this->responseSuccess('video Deleted Successfuly' ,202);


     }




}
