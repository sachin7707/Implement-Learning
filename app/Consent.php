<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @author jimmiw
 * @since 2019-10-23
 */
class Consent extends Model
{
    protected $table = 'order_consents';
    protected $guarded = [];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
