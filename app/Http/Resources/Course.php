<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @author jimmiw
 * @since 2018-10-10
 */
class Course extends JsonResource
{
    private $languageInternal;

    /**
     * Course constructor.
     * @param mixed $resource
     * @param string $language
     */
    public function __construct($resource)
    {
        parent::__construct($resource);

        // sets the language based on the course language as a default - ILI-741
        // added '' (blank) as danish language as well - ILI-755
//        $this->setLanguage(in_array($resource->language, ['Dansk', 'da', '']) ? 'da' : 'en');
    }

    /**
     * Transform the resource into an array.
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        // finds the difference between the start and end dates
        $diff = (new \DateTime($this->end_time))->diff(new \DateTime($this->start_time));

        return array_merge(
            parent::toArray($request),
            [
                // sets the duration on the course as well
                // NOTE: we need to add 1, because if they days are dec 7 and dec 7, then diff would yield 0. Also
                // if it's two days, with dec 7 and dec 8, diff would yield 1... +1 saves it all :)
                // fixes ILI-428
                'duration' => $diff->days + 1,
                'seats_available_including_reservations' => $this->getAvailableSeats(),
                'location' => $this->location,
                'dates' => $this->getCourseDatesFormatted($this->languageInternal),
                'sku' => $this->coursetype->number ?? null,
                'periods' => $this->getCoursePeriodsFormatted($this->languageInternal),
                'name' => $this->getTitle($this->languageInternal),
                'times' => $this->getCourseTimes(),
                // NOTE: name_internal is only used in WP admin, so they can see what specific courseday they are using
                'name_internal' => $this->name,
            ]
        );
    }

    /**
     * Set the language to display the course in
     * @param string $language use da or en
     */
    public function setLanguage($language)
    {
        $this->languageInternal = $language;
    }
}
