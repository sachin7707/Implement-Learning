<?php

namespace App\Http\Controllers;

use App\Course;
use App\Jobs\ImportCourses;
use App\Maconomy\Client\Maconomy;
use Illuminate\Http\Request;

/**
 * @author jimmiw
 * @since 2018-09-26
 */
class CourseController extends Controller
{
    /** @var Maconomy  */
    private $client;

    /**
     * ApiController constructor.
     * @param Maconomy $client
     */
    public function __construct(Maconomy $client)
    {
        $this->client = $client;
    }
    /**
     * Syncs all the courses from maconomy
     */
    public function sync()
    {
        ImportCourses::dispatch();
    }

    /**
     * Syncs a single course from maconomy
     * @param string $id
     */
    public function syncSingle(string $id)
    {
        ImportCourses::dispatch($id);
    }

    /**
     * Handles updating a course's details from an external provider
     * @param Request $request
     * @param $id
     */
    public function update(Request $request, $id)
    {
        $course = Course::where('maconomy_id', $id)->first();

        // you are allowed to change the number of participants
        $course->participants_max = $request->input('participants_max');

        $course->save();
    }
}