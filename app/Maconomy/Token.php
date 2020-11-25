<?php

namespace App\Maconomy;

use Illuminate\Database\Eloquent\Model;

/**
 * A model used for tokens, created on the webservice.
 * The tokens expires after a period of time, therefore new tokens should be created.
 *
 * @author jimmiw
 * @since 2019-01-02
 */
class Token extends Model
{
    protected $guarded = [];
    protected $table = 'ws_tokens';
}
