<?php

namespace App\Http\Controllers;

use App\Course;
use App\Jobs\ImportCourses;
use App\Maconomy\Client\Maconomy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Queue;

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
        Queue::later(1, new ImportCourses());
    }

    /**
     * Syncs a single course from maconomy
     * @param string $id
     */
    public function syncSingle(string $id)
    {
        Queue::later(1, new ImportCourses($id));
    }

    /**
     * Handles updating a course's details from an external provider (wordpress in our case)
     * @param Request $request
     * @param $id
     */
    public function update(Request $request, $id)
    {
        /** @var Course $course */
        $course = Course::where('maconomy_id', $id)->first();

        // you are allowed to change the maximum number of participants
        $course->participants_max = $request->input('participants_max');

        $course->save();
    }
}