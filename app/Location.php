<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @author jimmiw
 * @since 2019-01-30
 */
class Location extends Model
{
    protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at', 'id'];

    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}
