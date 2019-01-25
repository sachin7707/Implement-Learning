<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

/**
 * @author jimmiw
 * @since 2019-01-25
 */
class MailController extends Controller
{
    /**
     * Receives the mail texts from wordpress and add them to the database
     */
    public function update(Request $request)
    {
        $data = $request->input('mailtexts');
        Log::info('MailController::update: ' . print_r($data,1));
    }
}
