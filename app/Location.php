<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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

    /**
     * Fetches the location, that has the given maconomy id
     * @param string $externalId
     * @return Location|null the location found
     */
    public static function getByExternalId(string $externalId)
    {
        // NOTE: also returning trashed locations, if they are found - ILI-521
        return self::where('externalId', $externalId)->first();
    }

    /**
     * Same as getByExternalId, but this throws an error if the item is not found.
     * @param string $externalId
     * @return Location|null
     * @throws ModelNotFoundException
     */
    public static function getByExternalIdOrFail(string $externalId)
    {
        $location = self::getByExternalId($externalId);

        if ($location === null) {
            throw new ModelNotFoundException(self::class . ' not found with id ' . $externalId);
        }

        return $location;
    }

    /**
     * Fetches the string to use, when displaying a location in mails
     * @return string
     */
    public function getDisplayString()
    {
        $address = $this->address . ', ' . $this->postal . ' ' . $this->city;

        if ($this->country) {
            $address .= ', ' . $this->country;
        }

        return $address;
    }
}
