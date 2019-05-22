<?php

namespace App;

use App\Participant\Maconomy;
use Illuminate\Database\Eloquent\Model;

/**
 * @author jimmiw
 * @since 2018-10-23
 */
class Participant extends Model
{
    protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at', 'company_id', 'id', 'maconomy_id'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function maconomy()
    {
        return $this->hasMany(Maconomy::class);
    }

    /**
     * Checks if the participant has a maconomy id for the given course
     * @param int $courseId
     * @return bool
     */
    public function hasMaconomyId($courseId)
    {
        return ($this->getMaconomyByCourse($courseId) ? true : false);
    }

    /**
     * Fetches the participants maconomy id, for the given course
     * @param int $courseId
     * @return Model|\Illuminate\Database\Eloquent\Relations\HasMany|object|null
     */
    public function getMaconomyByCourse($courseId)
    {
        return $this->maconomy()
            ->where('course_id', '=', $courseId)
            ->first();
    }
}
