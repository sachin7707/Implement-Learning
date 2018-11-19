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
    // a brand new order
    const STATE_NEW = 0;
    // before being confirmed
    const STATE_CLOSED = 1;
    // order is synced with maconomy, and thereby confirmed
    const STATE_CONFIRMED = 2;

    protected $guarded = [];
    protected $hidden = [
        'education_id',
    ];

    public function courses()
    {
        return $this->belongsToMany(Course::class);
    }

    public function company()
    {
        return $this->hasOne(Company::class);
    }

    public function education()
    {
        return $this->hasOne(Course::class, 'id', 'education_id');
    }

    /**
     * @return string
     */
    public function getStateAsText(): string
    {
        if ($this->state === self::STATE_CLOSED) {
            return 'closed, but not synced';
        } elseif ($this->state === self::STATE_CONFIRMED) {
            return 'confirmed and synced';
        }

        return 'new';
    }

    /**
     * @return string
     */
    public function getOnWaitingListAsText(): string
    {
        return $this->on_waitinglist === 1 ? 'on waiting list' : 'normal order';
    }

    /**
     * Fetches the total price for the order
     * @return int
     */
    public function getTotalPrice()
    {
        $totalPrice = 0;

        // if there is an education on the course,
        if ($this->education) {
            return (int)$this->education->price;
        }

        /** @var Course $course */
        foreach ($this->courses as $course) {
            $totalPrice += (int)$course->price;
        }

        return $totalPrice;
    }
}
