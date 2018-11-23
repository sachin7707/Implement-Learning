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
                // NOTE: we need to add 1, because if they days are dec 7 and dec 7, then diff would yield 0. Also
                // if it's two days, with dec 7 and dec 8, diff would yield 1... +1 saves it all :)
                // fixes ILI-428
                'duration' => $diff->days + 1,
                'seats_available_including_reservations' => $this->getAvailableSeats()
            ]
        );
    }
}
