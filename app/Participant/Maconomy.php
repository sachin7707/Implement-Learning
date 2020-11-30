<?php

namespace App\Participant;

use App\Course;
use App\Participant;
use Illuminate\Database\Eloquent\Model;

/**
 * @author jimmiw
 * @since 2019-02-25
 */
class Maconomy extends Model
{
    protected $table = 'participant_maconomy';
    protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at'];

    public function participant()
    {
        return $this->hasOne(Participant::class);
    }

    public function course()
    {
        return $this->hasOne(Course::class);
    }
}
