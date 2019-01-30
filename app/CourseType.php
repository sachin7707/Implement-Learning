<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * @author jimmiw
 * @since 2018-10-05
 */
class CourseType extends Model
{
    protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at'];

    public function courses()
    {
        return $this->hasMany(Course::class);
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
}