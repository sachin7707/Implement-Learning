<?php

namespace App\Mail;

use App\MailText;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * A lots of defaults needs to be set all the time, added this class to make life easy :)
 * @author jimmiw
 * @since 2019-08-09
 */
class MailDefault extends Mailable
{
    use Queueable, SerializesModels;

    /** @var string */
    public $language;

    // default mail texts
    public $intro;
    public $footer;
    public $defaultBody;

    /**
     * Sets the language to use (language short codes: en, da etc)
     * @param $language
     */
    protected function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * Handles the default texts
     */
    protected function initDefaultTexts()
    {
        $this->footer = MailText::getByTypeAndLanguage(MailText::TYPE_MAIL_FOOTER, $this->language);
        $this->defaultBody =  MailText::getByTypeAndLanguage(MailText::TYPE_DEFAULT_BODY, $this->language);
    }
}