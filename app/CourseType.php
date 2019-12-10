<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use function error_log;

/**
 * @author jimmiw
 * @since 2018-10-05
 */
class CourseType extends Model
{
    protected $guarded = [];
    protected $hidden = ['id', 'created_at', 'updated_at'];

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function texts()
    {
        return $this->hasMany(CourseTypeText::class);
    }

    /**
     * Fetches the coursetype, that has the given maconomy id
     * @param string $maconomyId
     * @return CourseType|null the coursetype found
     */
    public static function getByMaconomyId(string $maconomyId)
    {
        return self::where('number', $maconomyId)->first();
    }

    /**
     * Same as getByMaconomyId, but this throws an error if the item is not found.
     * @param string $maconomyId
     * @return CourseType|null
     * @throws ModelNotFoundException
     */
    public static function getByMaconomyIdOrFail(string $maconomyId)
    {
        $courseType = self::getByMaconomyId($maconomyId);

        if ($courseType === null) {
            throw new ModelNotFoundException(self::class . ' not found with id ' . $maconomyId);
        }

        return $courseType;
    }

    /**
     * Fetches the upsell texts associated with this coursetype
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getUpsellTexts()
    {
        return CourseTypeText::where('type', 'upsell')
            ->where('course_type_id', $this->id)
            ->get();
    }

    /**
     * Fetches the title of the current course type
     * @param string $language the language to use ('da' or 'en' is supported)
     * @return string the title of the course type
     */
    public function getTitle($language = '')
    {
        error_log("sent language $language");
        // making sure we are getting a proper language "tag" to use
        $language = in_array($language, ['Dansk', 'da', '']) ? 'da' : 'en';
        error_log("found language: $language");

        // checking our course type texts for a title, using the given language
        $text = $this->texts()
            ->where('type', 'title')
            ->where('language', $language)
            ->first();

        if ($text) {
            error_log("we went with a text this seems good: {$text->text}");
            return $text->text;
        }

        error_log("probably not amazing: {$this->title}");
        // doing a fallback to the coursetype's title
        return $this->title;
    }
}
