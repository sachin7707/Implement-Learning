<?php

namespace App\Http\Controllers;

use App\MailText;
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
        $this->validate($request, [
            'texts' => 'required',
            'lang' => 'required|string'
        ]);
        $texts = json_decode($request->input('texts'), true);
        $language = $request->input('language');

        foreach ($texts as $type => $text) {
            // if the type is not valid, just skip it
            if (! MailText::isTypeValid($type)) {
                continue;
            }

            // fetching and updating exsisting mailtype
            $mailText = MailText::getByTypeAndLanguage($type, $language);

            if ($mailText) {
                $mailText->text = $text;
                $mailText->save();
            } else {
                // new MailText, create a new instance on the given language
                $mailText = new MailText();
                $mailText->save([
                    'type' => $type,
                    'text' => $text,
                    'language' => $language
                ]);
            }
        }
    }
}
