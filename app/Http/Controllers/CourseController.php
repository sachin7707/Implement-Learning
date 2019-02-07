<?php

namespace App\Http\Controllers;

use App\Course;
use App\Http\Resources\Course as CourseResource;
use App\Jobs\ImportCourses;
use App\Location;
use App\Maconomy\Client\Maconomy;
use Eluceo\iCal\Component\Calendar;
use Eluceo\iCal\Component\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
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
    public function index(Request $request)
    {
        // initializing the model query
        $query = Course::query();

        if ((int)$request->get('withtrashed', 0) === 1) {
            $query->withTrashed();
        }

        // handling getting courses, using the given list of course_type skus'
        if ($request->get('sku', null) !== null) {
            $skus = explode(',', $request->get('sku'));
            $courseIds = DB::table('courses')
                ->leftJoin('course_types', 'courses.coursetype_id', '=', 'course_types.id')
                ->whereIn('course_types.number', $skus)
                ->pluck('courses.id');

            // adding the course IDs to the query
            $query->whereIn('id', $courseIds->toArray());
        }

        $collection = $query->orderBy('start_time', 'asc')
            ->get();

        return new JsonResponse(CourseResource::collection($collection));
    }

    /**
     * Shows a single course
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id)
    {
        // NOTE: internals are changed, so we always get the course, even if it's deleted - ILI-521
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
            'participants_max' => 'int',
            'deadline' => 'nullable|date',
        ]);

        /** @var Course $course */
        $course = Course::getByMaconomyIdOrFail($id);

        $changed = false;
        if ($request->input('participants_max', null) !== null) {
            // you are allowed to change the maximum number of participants
            $course->participants_max = $request->input('participants_max');
            $changed = true;
        }

        // if the deadline isset, save it to the database
        if ($request->input('deadline', null) !== null) {
            $course->deadline = new \DateTime($request->input('deadline'), new \DateTimeZone('GMT'));
            $changed = true;
        }
        // you can now update the course day's name as well ILI-500
        if ($request->input('name') !== null) {
            $course->name = $request->input('name');
            $changed = true;
        }

        // checking if there is a venue attached to the course - ILI-525
        if ($request->input('externalId', null) !== null) {
            $location = Location::where('externalId', $request->input('externalId'))
                ->first();

            // only setting the location, if we have it
            if ($location) {
                $course->location_id = $location->id;
            } else {
                // it can also be removed again
                $course->location_id = null;
            }
            $changed = true;
        }

        // handles creating/update trainers on a given course
        if ($request->input('trainers', null) !== null) {
            // TODO: add trainers to a course here, using course_trainer table - ILI-230
//            $changed = true;
        }

        // no changes? just return the current $course, before calling save
        if ($changed === false) {
            return new JsonResponse([
                'message' => 'Course ' . $id . ' was not updated, no data was sent',
                'data' => new CourseResource(Course::getByMaconomyId($id))
            ]);
        }

        $course->save();

        return new JsonResponse([
            'message' => 'Course ' . $id . ' has been updated',
            'data' => new CourseResource(Course::getByMaconomyId($id))
        ]);
    }

    /**
     * Handles calendar events for a given course
     * @param Request $request
     * @param string $id the maconomy course id
     * @return Response
     */
    public function calendar(Request $request, $id)
    {
        /** @var Course $course */
        $course = Course::getByMaconomyIdOrFail($id);

        $cal = new Calendar('implement.com');

        $event = new Event();
        $event->setDtStart(new \DateTime($course->start_time))
            ->setDtEnd(new \DateTime($course->end_time))
            ->setNoTime(true)
            ->setSummary($course->name);

        $cal->addEvent($event); // addComponent?

        return new Response($cal->render(), 200, [
            'Content-Type: text/calendar; charset=utf-8',
            'Content-Disposition: attachment; filename="cal.ics"'
        ]);
    }
}
