<?php

namespace App\Http\Resources;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
                'courses' => $this->getCourseCollection(),
                'company' => new CompanyResource($this->company),
                'total_price' => $this->getTotalPrice(),
                'total_price_text' => 'DKK ' . $this->getTotalPrice() . ',- ekskl. moms',
            ]
        );
    }

    /**
     * Fetching the courses' on the order, but setting the language to use, so we are getting the correct language
     * on the dates.
     * @return array
     */
    private function getCourseCollection()
    {
        $courses = [];

        foreach ($this->getCoursesSorted() as $course) {
            $courses[] = new CourseResource($course);
        }

        return $courses;
    }
}
