<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseInfo extends Model
{
    use HasFactory;

    protected $fillable=[
        'admin_id',
        'course_name',
        'course_img_path',
        'number_of_videos',
    ];


    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }


}
