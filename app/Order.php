<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Order
 *
 * @author jimmiw
 * @since 2018-09-26
 * @mixin \Eloquent
 */
class Order extends Model
{
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
