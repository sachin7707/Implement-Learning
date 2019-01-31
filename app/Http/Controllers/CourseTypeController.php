<?php

namespace App\Http\Controllers;

use App\CourseType;
use App\Http\Resources\CourseType as CourseTypeResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @author jimmiw
 * @since 2018-10-05
 */
class CourseTypeController extends Controller
{
    /**
     * Fetches the course types in the project
     * @return JsonResponse
     */
    public function index()
    {
        return new JsonResponse(CourseType::all());
    }

    /**
     * Shows a single course type
     * @param $id
     * @return JsonResponse
     */
    public function show($id)
    {
        $courseType = CourseType::getByMaconomyIdOrFail($id);

        return new JsonResponse(new CourseTypeResource($courseType));
    }

    /**
     * You can save a few data fields on the course type as well
     * @param Request $request
     * @param $id
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, $id)
    {
        // validating that we have a course_id set
        $this->validate($request, [
            'name' => 'string|required'
        ]);

        $courseType = CourseType::getByMaconomyIdOrFail($id);

        $changed = false;

        // you can now update the course day's name as well ILI-500
        if ($request->input('name') !== null) {
            $courseType->name = $request->input('name');
            $changed = true;
        }

        // no changes? just return the current $course, before calling save
        if ($changed === false) {
            return new JsonResponse([
                'message' => 'CourseType ' . $id . ' was not updated, no data was sent',
                'data' => new CourseTypeResource(CourseType::getByMaconomyId($id))
            ]);
        }

        $courseType->save();

        return new JsonResponse([
            'message' => 'CourseType ' . $id . ' has been updated',
            'data' => new CourseTypeResource(CourseType::getByMaconomyId($id))
        ]);
    }
}
