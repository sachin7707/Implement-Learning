<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * @author jimmiw
 * @since 2019-02-05
 */
class Trainer extends Model
{
    protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at', 'id'];

    public function course()
    {
        return $this->belongsToMany(Course::class);
    }

    /**
     * Fetches the location, that has the given external id
     * @param string $externalId
     * @return Trainer|null
     */
    public static function getByExternalId(string $externalId)
    {
        return self::where('external_id', $externalId)->first();
    }

    /**
     * Same as getByExternalId, but this throws an error if the item is not found.
     * @param string $externalId
     * @return Trainer|null
     * @throws ModelNotFoundException
     */
    public static function getByExternalIdOrFail(string $externalId)
    {
        $trainer = self::getByExternalId($externalId);

        if ($trainer === null) {
            throw new ModelNotFoundException(self::class . ' not found with id ' . $externalId);
        }

        return $trainer;
    }
}
