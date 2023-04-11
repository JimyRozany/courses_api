<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
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
            "id" => (string)$this->id,
            "admin_id" => (string)$this->admin_id,
            "course_name" => $this->course_name,
            "course_img_path" => $this->course_img_path,
            "number_of_videos" => $this->number_of_videos,
            "created_at" => $this->created_at->format('Y - M - d'),
            "updated_at" => $this->updated_at->format('Y - M - d'),
        ];
    }
}

