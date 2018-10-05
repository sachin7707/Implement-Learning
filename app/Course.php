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
        return $this->hasMany(Order::class);
    }

    public function coursetype()
    {
        return $this->belongsTo(CourseType::class);
    }
}