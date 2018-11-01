<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @author jimmiw
 * @since 2018-10-23
 */
class Company extends Model
{
    protected $fillable = [
        'name',
        'cvr',
        'attention',
        'address',
        'postal',
        'city',
        'country',
        'phone',
        'email',
        'ean',
        'purchase_no',
        'billing_name',
        'billing_cvr',
        'billing_attention',
        'billing_address',
        'billing_postal',
        'billing_city',
        'billing_country',
        'billing_phone',
        'billing_email',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }
}
