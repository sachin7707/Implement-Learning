<?php

namespace App\Mail;

use App\Calendar\OrderCalendar;
use App\Course;
use App\CourseTypeText;
use App\MailText;
use App\Order;
use App\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * @author jimmiw
 * @since 2018-10-30
 */
class OrderParticipant extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $courses;
    public $participant;
    public $calendarUrl;
    /** @var array */
    public $upsells;
    /** @var string the language the email should shown in */
    public $language;

    // mail texts
    public $intro;
    public $footer;
    public $beforeCourseHeader;
    public $imageUrl;

    /**
     * OrderParticipant constructor.
     * @param Order $order
     * @param Participant $participant
     */
    public function __construct(Order $order, Participant $participant)
    {
        $this->order = $order;
        $this->language = (string)$order->language ?? 'da';
        $this->courses = $order->courses;
        // we wrap the participant in an adapter, to be able to use the data in the email
        $this->participant = $participant;

        // creating the calendar url for the participants
        // TODO: change this url in the future? since it goes directly to the api instead of WP site.

        // fetches the link to the calendar
//        $generator = new OrderCalendar($order);
//        $this->calendarUrl = $generator->getLink();
        $this->calendarUrl = '#';

        $this->footer = MailText::getByTypeAndLanguage(MailText::TYPE_MAIL_FOOTER, $this->language);

        // adding texts, first from the course types, else from the general mail texts.
        $this->setIntoText();
        $this->setBeforeCourseHeader();
        $this->setImage();

        // setting upsell information
        $this->setUpsellInformation();
    }

    /**
     * @return OrderParticipant
     */
    public function build()
    {
        // TODO: we need to get the course material to "include" in the email. Kontainer vs Attachment?

        $subject = $this->language === 'da' ? 'Tilmelding til %Kursusnavn%' : 'Signup for %Kursusnavn%';
        // doing a bit of replaces in the subject
        $subject = str_replace('%Kursusnavn%', Helper::getTitle($this->order), $subject);

        // initializes the order calender generator, so we can attach the calendar
        $generator = new OrderCalendar($this->order);

        return $this->view('emails.orders.participant')
            ->text('emails.orders.participant_plain')
            ->subject($subject)
            ->attachData(
                $generator->getCalendar()->serialize(),
                'events.ics',
                [
                    'mime' => $generator->getAttachmentMime(),
                ]
            );
    }

    /**
     * Sets the beforeCourse header, by either using one from the CMS or our defaults in danish or english language.
     */
    private function setBeforeCourseHeader(): void
    {
        // fetching the before course header text from the course types, if possible
        $beforeCourseHeader = $this->getCourseTypeText('heading');
        if (! empty($beforeCourseHeader)) {
            $this->beforeCourseHeader = $beforeCourseHeader;
            // returning early
            return;
        }
        $this->beforeCourseHeader = MailText::getByTypeAndLanguage(
            MailText::TYPE_DEFAULT_PARTICIPANT_BEFORE_COURSE,
            $this->language
        );

        // handles empty before course headers, by using "old" defaults
        if (empty($this->beforeCourseHeader)) {
            $this->beforeCourseHeader = ($this->language === 'da' ? 'F&#248;r kurset skal du' : 'Before course start');
        }
    }

    /**
     * Sets the intro text to use, based on language and participant type
     */
    private function setIntoText(): void
    {
        // fetching the intro text from the course types, if possible
        $intro = $this->getCourseTypeText('before_course');
        if (! empty($intro)) {
            $this->intro = (object)['text' => $intro];
            // returning early
            return;
        }

        // setting general mail texts
        $introType = MailText::TYPE_DEFAULT_PARTICIPANT;
        if ($this->order->on_waitinglist) {
            $introType = MailText::TYPE_WAITINGLIST_PARTICIPANT;
        }

        $this->intro = MailText::getByTypeAndLanguage($introType, $this->language);
    }

    /**
     * adds upsell information to the email
     */
    private function setUpsellInformation()
    {
        $upsells = [];

        /** @var Course $course */
        foreach ($this->order->courses as $course) {
            if ($course->coursetype) {
                $texts = $course->coursetype->getUpsellTexts();
                if (empty($texts)) {
                    continue;
                }

                /** @var CourseTypeText $text */
                foreach ($texts as $text) {
                    $upsells[] = json_decode($text->text);
                }
            }
        }

        $this->upsells = $upsells;
    }

    /**
     * Fetches the course type text, for one of the current courses on the order, using the given type (using current
     * language).
     * @param string $type the type to find the text for
     * @return string the text found or an empty string
     */
    private function getCourseTypeText(string $type): string
    {
        foreach ($this->order->courses as $course) {
            // fetch intro text from course type first
            $courseTypeText = CourseTypeText::where('course_type_id', $course->coursetype_id)
                ->where('type', $type)
                ->where('language', $this->language)
                ->first();

            // checking if the course type has a text attached
            if (! empty($courseTypeText->text)) {
                // returning early, if possible
                return $courseTypeText->text;
            }
        }

        return '';
    }

    /**
     * Sets the image to use. This can either be a general image or a course type specific one
     */
    private function setImage()
    {
        // fetching the intro text from the course types, if possible
        $imageUrl = $this->getCourseTypeText('image');
        if (! empty($imageUrl)) {
            $this->imageUrl = $imageUrl;
            // returning early
            return;
        }

        // setting image url from general mail texts
        $this->imageUrl = MailText::getByTypeAndLanguage(
            MailText::TYPE_DEFAULT_PARTICIPANT_IMAGE_ARTICLE,
            $this->language
        );
    }
}
