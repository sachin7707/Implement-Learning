<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @author jimmiw
 * @since 2019-10-23
 */
class Consent extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return array_merge(
            [
                'company' => $this->company,
                'name' => $this->name,
                'email' => $this->email,
                'order_id' => $this->order_id,
                'order' => new Order($this->order),
            ]
        );
    }
}