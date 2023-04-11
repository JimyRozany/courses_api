<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'video_path',
        'video_duration',

    ];





    /*  ================================= */

    public function course()
    {
        return $this->belongsTo(CourseInfo::class ,'course_id');
    }
}
