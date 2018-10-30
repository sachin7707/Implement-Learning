<?php

namespace App\Http\Controllers;

use App\Course;
use App\Http\Resources\Course as CourseResource;
use App\Jobs\ImportCourses;
use App\Maconomy\Client\Maconomy;
use Illuminate\Http\JsonResponse;
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
     * Fetches the full list of courses
     * @return JsonResponse
     */
    public function index()
    {
        return new JsonResponse(CourseResource::collection(Course::all()));
    }

    /**
     * Shows a single course
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id)
    {
        $course = Course::getByMaconomyIdOrFail($id);

        return new JsonResponse(new CourseResource($course));
    }

    /**
     * Syncs all the courses from maconomy
     * @return JsonResponse
     */
    public function sync()
    {
        Queue::later(1, new ImportCourses());

        return new JsonResponse([
            'message' => 'Sync all added to queue'
        ]);
    }

    /**
     * Syncs a single course from maconomy
     * @param string $id
     * @return JsonResponse
     */
    public function syncSingle(string $id)
    {
        Queue::later(1, new ImportCourses($id));

        return new JsonResponse([
            'message' => 'Sync/'.$id.' added to queue'
        ]);
    }

    /**
     * Handles updating a course's details from an external provider (wordpress in our case)
     * @param Request $request
     * @param $id
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, $id)
    {
        // validating that we have a course_id set
        $this->validate($request, [
            'participants_max' => 'required',
            'deadline' => 'nullable|date',
        ]);

        /** @var Course $course */
        $course = Course::getByMaconomyIdOrFail($id);

        // you are allowed to change the maximum number of participants
        $course->participants_max = $request->input('participants_max');

        // if the deadline isset, save it to the database
        if ($request->input('deadline', null) !== null) {
            $course->deadline = new \DateTime($request->input('deadline'), new \DateTimeZone('GMT'));
        }

        $course->save();

        return new JsonResponse([
            'message' => 'Course ' . $id . ' has been updated',
            'data' => new CourseResource(Course::getByMaconomyId($id))
        ]);
    }
}