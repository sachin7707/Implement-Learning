<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Course as CourseResource;
use App\Http\Resources\CourseType as CourseTypeResource;
use App\Http\Resources\Company as CompanyResource;

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
                'state_text' => $this->getStateAsText(),
                'on_waitinglist_text' => $this->getOnWaitingListAsText(),
                'education' => new CourseTypeResource($this->education),
                'courses' => CourseResource::collection($this->courses()->orderBy('pivot_id')->get()),
                'company' => new CompanyResource($this->company),
                'total_price' => $this->getTotalPrice(),
                'total_price_text' => 'DKK ' . $this->getTotalPrice() . ',- ekskl. moms',
            ]
        );
    }
}
