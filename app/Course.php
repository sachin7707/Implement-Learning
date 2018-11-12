<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

/**
 * App\Course
 *
 * @author jimmiw
 * @since 2018-09-27
 * @mixin \Eloquent
 * @property int $participants_max
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $start_time
 * @property string $end_time
 * @property float $price
 * @property string $maconomy_id
 * @property int $seats_available
 * @property int $participants_min
 * @property int $participants_current
 * @property string $name
 * @property string $language
 * @property string $venue_number
 * @property string $venue_name
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Order[] $orders
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Course whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Course whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Course whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Course whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Course whereMaconomyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Course whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Course whereParticipantsCurrent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Course whereParticipantsMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Course whereParticipantsMin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Course wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Course whereSeatsAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Course whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Course whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Course whereVenueName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Course whereVenueNumber($value)
 */
class Course extends Model
{
    protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at'];

    public function orders()
    {
        return $this->belongsToMany(Order::class);
    }

    public function coursetype()
    {
        return $this->belongsTo(CourseType::class);
    }

    /**
     * Fetches the number of available seats, on a course, on the given order.
     * @param Course $course the course to check
     * @param Order $order the current order. (can be null)
     * @return int the number of seats available on a given course
     */
    public function getAvailableSeats(Order $order = null): int
    {
        $select = DB::table('orders')
            ->select(DB::raw('sum(seats) as seat_count'))
            ->where('state', '!=', Order::STATE_CONFIRMED)
            ->where('course_id', $this->id);

        // if an order was given, add an excluding id to the select, to avoid counting these reservations
        // since the order is "in progress"
        if ($order !== null) {
            // excluding current order
            $select->where('id', '!=', $order->id);
        }

        // fetches the seat count
        $reservedSeats = $select->value('seat_count');
        // calculates the number of available seats, using max and current number of participants
        $seatsAvailable = $this->participants_max - $this->participants_current;

        // no seats available? return 0
        if ($seatsAvailable <= $reservedSeats) {
            return 0;
        }

        // return the number of available seats, when reserved seats are removed
        return $seatsAvailable - $reservedSeats;
    }

    /**
     * Fetches the course, that has the given maconomy id
     * @param string $maconomyId
     * @return Course|null the course found
     */
    public static function getByMaconomyId(string $maconomyId)
    {
        return self::where('maconomy_id', $maconomyId)->first();
    }

    /**
     * Same as getByMaconomyId, but this throws an error if the item is not found.
     * @param string $maconomyId
     * @return Course|null
     * @throws ModelNotFoundException
     */
    public static function getByMaconomyIdOrFail(string $maconomyId)
    {
        $course = self::getByMaconomyId($maconomyId);

        if ($course === null) {
            throw new ModelNotFoundException(self::class . ' not found with id ' . $maconomyId);
        }

        return $course;
    }
}
