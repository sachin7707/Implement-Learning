<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @author jimmiw
 * @since 2019-02-25
 */
class Participant extends JsonResource
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
            [
                'maconomy_ids' => $this->maconomy,
                'maconomy_ids_small' => $this->getMaconomyIdsFlat(),
            ]
        );
    }

    private function getMaconomyIdsFlat()
    {
        $maconomyIds = [];

        foreach ($this->maconomy as $item) {
            $maconomyIds[] = $item->maconomy_id;
        }

        return $maconomyIds;
    }
}