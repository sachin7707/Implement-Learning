<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Course
 *
 * @author jimmiw
 * @since 2018-09-27
 * @mixin \Eloquent
 * @property int $participants_max
 */
class Course extends Model
{
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}