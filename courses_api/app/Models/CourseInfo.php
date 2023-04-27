<?php

namespace App\Models;

use App\Models\VideoInfo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourseInfo extends Model
{
    use HasFactory;

    protected $fillable=[
        'admin_id',
        'course_name',
        'course_img_path',
        'number_of_videos',
    ];






    
    /*  ================================= */

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
    public function videos()
    {
        return $this->hasMany(VideoInfo::class ,'course_id'); 
    }
    public function favorites()
    {
        return $this->belongsTo(Favorites::class ,'course_id'); 
    }


}
