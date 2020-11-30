<?php

namespace App\Http\Controllers;

use App\Newsletter\NewsletterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @author jimmiw
 * @since 2019-02-06
 */
class NewsletterController extends Controller
{
    /** @var NewsletterService  */
    private $newsletterService;

    /**
     * NewsletterController constructor.
     * @param NewsletterService $newsletterService
     */
    public function __construct(NewsletterService $newsletterService)
    {
        $this->newsletterService = $newsletterService;
    }

    /**
     * Handles adding a user to the newsletter
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function signup(Request $request)
    {
        $this->validate($request, [
            'firstname' => 'string',
            'lastname' => 'string',
            'email' => 'string'
        ]);

        $response = $this->newsletterService->signup(
            $request->input('firstname'),
            $request->input('lastname'),
            $request->input('email')
        );

        return response()->json($response);
    }
}
