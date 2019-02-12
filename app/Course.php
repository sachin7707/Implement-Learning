<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

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
 * @property \DateTime $start_time
 * @property \DateTime $end_time
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
    use SoftDeletes;

    const LANGUAGES = ['da' => 'da_DK', 'en' => 'en_GB'];

    protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at', 'pivot', 'id'];
    protected $dates = ['deleted_at', 'last_sync_date', 'start_time', 'end_time'];

    public function orders()
    {
        return $this->belongsToMany(Order::class);
    }

    public function coursetype()
    {
        return $this->belongsTo(CourseType::class); //, 'coursetype_id', 'id', 'course_types');
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function trainers()
    {
        return $this->belongsToMany(Trainer::class);
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
            ->leftJoin('course_order', 'course_order.order_id', '=', 'orders.id')
            ->where('state', '!=', Order::STATE_CONFIRMED)
            ->where('course_id', $this->id);

        // if an order was given, add an excluding id to the select, to avoid counting these reservations
        // since the order is "in progress"
        if ($order !== null) {
            // excluding current order
            $select->where('orders.id', '!=', $order->id);
        }

        // fetches the seat count
        $reservedSeats = (int)$select->value('seat_count');
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
        // NOTE: also returning trashed courses, if they are found - ILI-521
        return self::where('maconomy_id', $maconomyId)->withTrashed()->first();
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

    /**
     * Fetches the title of the course, by first checking the course type, else falling back to own name.
     * @return string
     */
    public function getTitle()
    {
        // checking if the course type exists, and uses that name
        if ($this->coursetype) {
            return $this->coursetype->title;
        }

        // no course type found, just use the course's name instead.
        return $this->name;
    }

    /**
     * Fetches the dates a course is running on
     * @return array
     * @throws \Exception
     */
    public function getCourseDates()
    {
        if (empty($this->coursetype)) {
            return [];
        }


        $dates = [];

        // if there are periods defined, use these instead of course dates - ILI-618
        $periods = $this->getCoursePeriods();
        if (! empty($periods)) {
            foreach ($periods as $period) {
                $start = $end = $period;
                if (is_array($period)) {
                    /** @var Carbon $start */
                    $start = current($period);
                    /** @var Carbon $end */
                    $end = end($period);
                }
                $dates[] = new Carbon($start->format('c'));

                $duration = $start->diffInDays($end);
                if ($duration > 0) {
                    foreach (range(1, $duration) as $days) {
                        $start->add(new \DateInterval('P1D'));
                        $dates[] = new Carbon($start->format('c'));
                    }
                }
            }
        }

        return $dates;
    }

    /**
     * Fetches the course dates, but using the data in the "periods" field instead of the coursetype's duration
     * @return array
     * @throws \Exception
     */
    public function getCoursePeriods()
    {
        $dates = [];

        if (empty($this->periods)) {
            $dates = $this->getCourseDatesByDuration();

            return [[
                current($dates),
                end($dates)
            ]];
        }

        $periods = explode(',', $this->periods);

        foreach ($periods as $period) {
            $intervals = [];
            $periodDates = explode('-', $period);
            foreach ($periodDates as $periodDate) {
                $intervals[] = new Carbon($periodDate);
            }

            $dates[] = $intervals;
        }

        return $dates;
    }

    /**
     * Fetches the course periods parsed as a short text
     * @param string $languageCode
     * @return array
     */
    public function getCoursePeriodsFormatted($languageCode = 'da')
    {
        $periods = $this->getCoursePeriods();

        $formattedDates = [];

        setlocale(LC_TIME, self::LANGUAGES[$languageCode]);

        foreach ($periods as $dates) {
            $formattedPeriod = [];

            $start = $end = $dates;
            if (is_array($dates)) {
                /** @var Carbon $start */
                $start = current($dates);
                /** @var Carbon $end */
                $end = end($dates);
            }

            $formattedPeriod[] = $this->getNiceDateField($start->formatLocalized('%e'))
                . $this->getNiceDateField($start->formatLocalized('/%m'));
            // only add the "end" value, if it's different from start
            if (! $start->eq($end)) {
                $formattedPeriod[] = $this->getNiceDateField($end->formatLocalized('%e'))
                    . $this->getNiceDateField($end->formatLocalized('/%m'));
            }
            $formattedDates[] = implode('-', $formattedPeriod);
        }

        return $formattedDates;
    }

    /**
     * Fetches the course dates formatted as nice strings
     * @return array
     * @throws \Exception
     */
    public function getCourseDatesFormatted($languageCode = 'da')
    {
        $formattedDates = [];

        $dates = $this->getCourseDates();
        setlocale(LC_TIME, self::LANGUAGES[$languageCode]);

        /** @var Carbon $date */
        foreach ($dates as $date) {
            $formattedDates[] = utf8_encode($date->formatLocalized('%A d. %e. %B %G'));
        }

        return $formattedDates;
    }

    /**
     * Fetches the link to the course
     * @return string
     */
    public function getLink()
    {
        return $this->coursetype->link ?? '';
    }

    /**
     * Fetches the language used on the course
     * @return string
     */
    public function getLanguage()
    {
        return empty($this->language) ? 'Dansk' : $this->language;
    }

    /**
     * Checks if there is a text for the given type
     * @param string $type the type of text to check, e.g. before_course
     * @return bool
     */
    public function hasText(string $type)
    {
        $courseTypeText = $this->coursetype->texts()
            ->where('type', $type)
            // TODO: add order language?
            ->first();

        return ! empty($courseTypeText->text);
    }

    /**
     * Fetches the text for the given $type
     * @param string $type the type of text to fetch, e.g. before_course
     * @return string
     */
    public function getText(string $type)
    {
        $courseTypeText = $this->coursetype->texts()
            ->where('type', $type)
            // TODO: add order language?
            ->first();

        return $courseTypeText->text;
    }

    /**
     * @param string $dateField
     * @return string
     */
    private function getNiceDateField($dateField): string
    {
        return str_pad(trim($dateField), 2, '0', STR_PAD_LEFT);
    }

    /**
     * @return array
     * @throws \Exception
     */
    private function getCourseDatesByDuration(): array
    {
// fetching the duration from the course type
        $duration = (int)$this->coursetype->duration;

        $startTime = $this->start_time;
        $dates = [];
        $dates[] = new Carbon($startTime->format('c'));

        if ($duration > 1) {
            foreach (range(1, $duration - 1) as $days) {
                $startTime->add(new \DateInterval('P1D'));
                $dates[] = new Carbon($startTime->format('c'));
            }
        }

        return $dates;
    }
}
