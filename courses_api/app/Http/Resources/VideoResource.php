<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'course_id'=>       (string)$this->course_id,
            'id'=>              (string)$this->id,
            'title'=>           $this->title,
            'video_path'=>      $this->video_path,
            'video_duration'=>  $this->video_duration,
        ];
    }
}


/*

'course_id',
        'title',
        'video_path',
        'video_duration',
*/