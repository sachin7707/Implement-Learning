<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @author jimmiw
 * @since 2018-11-13
 */
class Company extends JsonResource
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
            ['participants' => $this->getParticipantCollection()]
        );
    }

    /**
     * Fetches the list of participants as a nice collection
     * @return array
     */
    private function getParticipantCollection()
    {
        $participants = [];

        foreach ($this->participants as $participant) {
            $participants[] = new Participant($participant);
        }

        return $participants;
    }
}
