<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @author jimmiw
 * @since 2018-10-23
 */
class Participant extends Model
{
    protected $guarded = [];

    protected $hidden = ['created_at', 'updated_at', 'company_id', 'id'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
