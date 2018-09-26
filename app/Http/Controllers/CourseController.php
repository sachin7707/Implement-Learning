<?php

namespace App\Http\Controllers;

use App\Maconomy\Client\Maconomy as MaconomyClient;
use App\Wordpress\Client as WordpressClient;

/**
 * @author jimmiw
 * @since 2018-09-26
 */
class CourseController extends Controller
{
    /** @var MaconomyClient  */
    private $maconomyClient;
    /** @var WordpressClient  */
    private $wordpressClient;

    /**
     * ApiController constructor.
     * @param MaconomyClient $maconomyClient
     * @param WordpressClient $wordpressClient
     */
    public function __construct(MaconomyClient $maconomyClient, WordpressClient $wordpressClient)
    {
        $this->maconomyClient = $maconomyClient;
        $this->wordpressClient = $wordpressClient;
    }
    /**
     * Syncs all the courses from maconomy
     */
    public function sync()
    {
        // TODO: start the actual sync with maconomy

        // tells wordpress to sync all courses
        $this->wordpressClient->syncAll();
    }

    /**
     * Syncs a single course from maconomy
     * @param string $id
     */
    public function syncSingle(string $id)
    {
        // TODO: start a single course sync with maconomy

        // tells wordpress to sync a single course
        $this->wordpressClient->syncSingle($id);
    }
}