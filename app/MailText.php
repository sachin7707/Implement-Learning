<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @author jimmiw
 * @since 2019-01-31
 */
class MailText extends Model
{
    const TYPE_DEFAULT_PARTICIPANT = 'default_participant_mail';
    const TYPE_DEFAULT_PARTICIPANT_BEFORE_COURSE = 'default_participant_mail_before_course';
    const TYPE_DEFAULT_BODY = 'default_mail_body';
    const TYPE_WAITINGLIST_PARTICIPANT = 'default_waitinglist_participant_mail';
    const TYPE_DEFAULT_WELCOME_HOME = 'default_welcome_home_mail';
    const TYPE_REMINDER = 'remindermail';
    const TYPE_MAIL_FOOTER = 'mailfooter';

    protected $guarded = [];

    /**
     * Fetches the mail text, using the given $type
     * @param string $type the mail type, see TYPE_X constants
     * @param string $language the language code to get the text for (ISO-2 language code)
     * @return MailText|null the found mailtext or null
     */
    public static function getByTypeAndLanguage(string $type, string $language)
    {
        return self::where('type', $type)
            ->where('language', $language)
            ->first();
    }

    /**
     * Checks if the type is valid
     * @param string $type
     * @return bool
     */
    public static function isTypeValid(string $type)
    {
        return in_array($type, [
            self::TYPE_DEFAULT_PARTICIPANT,
            self::TYPE_DEFAULT_PARTICIPANT_BEFORE_COURSE,
            self::TYPE_DEFAULT_BODY,
            self::TYPE_WAITINGLIST_PARTICIPANT,
            self::TYPE_DEFAULT_WELCOME_HOME,
            self::TYPE_MAIL_FOOTER,
            self::TYPE_REMINDER
        ]);
    }
}
