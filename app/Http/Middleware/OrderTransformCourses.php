<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * @author jimmiw
 * @since 2018-11-21
 */
class OrderTransformCourses
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->isJson()) {
            $this->transformData($request->json());
        } else {
            $this->transformData($request->request);
        }

        return $next($request);
    }

    /**
     * @param ParameterBag $bag
     */
    private function transformData(ParameterBag $bag)
    {
        $bag->set('courses', $this->transformCourses($bag->get('courses')));
    }

    /**
     * Transforms the given course data, into something we can use in the backend
     * @param array $data the list of courses to transform
     * @return array key-value based array, with sorting as key and maconomy id as value
     */
    private function transformCourses($data)
    {
        $courses = [];

        foreach ($data as $courseObject) {
            // NOTE: sort is starting at 1, so we substract 1, to make it zero based arrays
            $courses[$courseObject['sort']] = $courseObject['id'];
        }

        ksort($courses);

        return $courses;
    }
}
