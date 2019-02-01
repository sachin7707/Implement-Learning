<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @author jimmiw
 * @since 2019-02-01
 */
class CourseTypeText extends Model
{
    protected $guarded = [];

    public function coursetype()
    {
        $this->belongsTo(CourseType::class);
    }
}
