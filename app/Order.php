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
 * @property bool $on_waitinglist
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
    protected $dates = ['last_sync_date'];

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
        return $this->hasOne(CourseType::class, 'id', 'education_id');
    }

    /**
     * @return string
     */
    public function getStateAsText(): string
    {
        if ((int)$this->state === self::STATE_CLOSED) {
            return 'closed, but not synced';
        } elseif ((int)$this->state === self::STATE_CONFIRMED) {
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
            $totalPrice = (int)$this->education->price;
        } else {

            /** @var Course $course */
            foreach ($this->courses as $course) {
                $totalPrice += (int)$course->price;
            }
        }

        // now using 1 seat, if the number of selected seats are 0 (or less)
        return $totalPrice * ($this->seats <= 0 ? 1 : $this->seats);
    }

    /**
     * Tests if the current order is set to be a "waiting list" order.
     * NOTE: This simply means that there were no seats left, but we are not after the deadline when the order was made.
     * @return bool
     */
    public function isOnWaitingList(): bool
    {
        return (bool)$this->on_waitinglist;
    }

    /**
     * Fetches the hash for the current order.
     * NOTE: we are simply doing a MD5 atm
     * @return string
     */
    public function getHash()
    {
        return md5($this->id);
    }

    /**
     * Fetches the courses on the order, sorted by the sort column on course_order table
     * @return Course[]
     */
    public function getCoursesSorted()
    {
        return $this->courses()
            ->withPivot('sort')
            ->orderBy('sort')
            ->withTrashed()
            ->get();
    }
}
