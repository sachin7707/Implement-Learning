<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @author jimmiw
 * @since 2019-09-20
 */
class CourseDate extends Model
{
    protected $guarded = [];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
