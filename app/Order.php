<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Order
 *
 * @author jimmiw
 * @since 2018-09-26
 * @mixin \Eloquent
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $course_id
 * @property int $seats
 * @property-read \App\Course $course
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereSeats($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereUpdatedAt($value)
 */
class Order extends Model
{
    const STATE_NEW = 0;
    const STATE_CONFIRMED = 1;

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
