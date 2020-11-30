<?php

namespace App\Http\Controllers;

use App\CourseType;
use App\CourseTypeText;
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
            'name' => 'string|required',
            'link' => 'string'
        ]);

        $courseType = CourseType::getByMaconomyIdOrFail($id);

        $courseType->link = $request->input('link');
        // NOTE: now saving the input "name" into title field instead - ILI-638
        $courseType->title = $request->input('name');

        // TODO: handle $request->input('texts')['before_course'] text+language

        // handles various texts
        if ($request->input('texts')) {
            foreach ($request->input('texts') as $data) {
                $courseTypeText = CourseTypeText::where('type', $data['type'])
                    ->where('language', $data['language'])
                    ->where('course_type_id', $courseType->id)
                    ->first();

                if ($courseTypeText) {
                    $courseTypeText->type = $data['type'];
                    $courseTypeText->text = $data['text'] ?? '';
                    $courseTypeText->language = $data['language'];
                } else {
                    $courseTypeText = new CourseTypeText([
                        'type' => $data['type'],
                        'text' => $data['text'] ?? '',
                        'language' => $data['language'],
                        'course_type_id' => $courseType->id
                    ]);
                }

                $courseTypeText->save();
            }
        }

        // handling upsell courses - ILI-721
        if ($request->input('upsell')) {
            // removing current list of upsell courses, since we do not have id's or anything on them... :/
            CourseTypeText::where('type', 'upsell')
                ->where('course_type_id', $courseType->id)
                ->delete();

            // adding the new upsell courses
            foreach ($request->input('upsell') as $data) {
                $courseTypeText = new CourseTypeText([
                    'type' => 'upsell',
                    // we are just json encoding the data, so we can pass "more" to frontend
                    'text' => json_encode($data),
                    'language' => $data['language'],
                    'course_type_id' => $courseType->id
                ]);

                $courseTypeText->save();
            }
        }

        $courseType->save();

        return new JsonResponse([
            'message' => 'CourseType ' . $id . ' has been updated',
            'data' => new CourseTypeResource(CourseType::getByMaconomyId($id))
        ]);
    }
}
