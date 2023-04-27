<?php

namespace App\Http\Controllers\Api\Favorites;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Traits\ResponseTrait;
use App\Models\CourseInfo;
use App\Models\Favorites;
use Exception;
use Illuminate\Support\Facades\Auth;

class FavoritesController extends Controller
{
    use ResponseTrait;
    /**================= Get all courses in favorites ================== */
    public function allCourses()
    {
        $user = Auth::user();

        $all_favorites = $user->favorites;
        $all_courses = [];

        foreach($all_favorites as $item)
        {
            $course = CourseInfo::find($item->course_id);
            array_push($all_courses ,$course);
        }

        return  $this->responseData('courses' ,$all_courses  ,'' ,"false" ,"Success", 200) ;




    }
    /**================= Add course to favorites ================== */
    public function add(Request $request)
    {
        try{
            $request->validate([
                'course_id' => 'required'
            ]);
        }catch(\Exception $ex){
            return $this->responseError($ex->getMessage() ,400);
        }
        $user = Auth::user();

        $course = $user->favorites->where('course_id', $request->input('course_id'))->first();

        if( !$course )
        {
            $favorite = Favorites::create([
                'user_id' => Auth::user()->id,
                'course_id' => $request->input('course_id'),
            ]);
            return $this->responseSuccess('course added successfuly' ,200);
        }else{
            return $this->responseError('course already exists' ,409);
        }
    }
    
    /**================= remove course from favorites ================== */
     public function remove(Request $request)
     {
        try{
            $request->validate([
                'course_id' => 'required'
            ]);
        }catch(\Exception $ex){
            return $this->responseError($ex->getMessage() ,400);
        }

        $user = Auth::user();

        $course = $user->favorites->where('course_id', $request->input('course_id'))->first();

        if( !$course  )
        {
            return $this->responseError('course not found' ,404);
        }else{
            $course->delete();
            return $this->responseSuccess('course deleted successfuly' ,200);
        }
     }

    
}
