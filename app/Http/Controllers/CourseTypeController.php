<?php

namespace App\Http\Controllers;

use App\CourseType;
use Illuminate\Http\JsonResponse;

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
}
