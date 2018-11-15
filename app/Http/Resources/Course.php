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
        // finds the difference between the start and end dates
        $diff = (new \DateTime($this->end_time))->diff(new \DateTime($this->start_time));

        return array_merge(
            parent::toArray($request),
            [
                // sets the duration on the course as well
                'duration' => $diff->days,
                'seats_available_including_reservations' => $this->getAvailableSeats()
            ]
        );
    }
}
