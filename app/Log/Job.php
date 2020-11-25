<?php

namespace App\Log;

use Illuminate\Database\Eloquent\Model;

/**
 * @author jimmiw
 * @since 2019-02-07
 */
class Job extends Model
{
    protected $guarded = [];
    protected $table = 'log_jobs';
}
