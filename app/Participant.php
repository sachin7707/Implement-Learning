<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @author jimmiw
 * @since 2018-10-23
 */
class Participant extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'title',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
