<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @author jimmiw
 * @since 2018-10-10
 */
class Course extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return array_merge(
            parent::toArray($request),
            ['seats_available_including_reservations' => $this->getAvailableSeats()]
        );
    }
}
