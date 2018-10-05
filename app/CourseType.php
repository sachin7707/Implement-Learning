<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @author jimmiw
 * @since 2018-10-05
 */
class CourseType extends Model
{
    protected $guarded = [];

    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}