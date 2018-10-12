<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Course as CourseResource;

/**
 * @author jimmiw
 * @since 2018-10-12
 */
class Order extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return array_merge(
            parent::toArray($request),
            [
                'course' => new CourseResource($this->course)
            ]
        );
    }
}
