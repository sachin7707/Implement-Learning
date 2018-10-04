<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use Illuminate\Support\Facades\Log;

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api/v1'], function () use ($router) {
    $router->get('/sync', 'CourseController@sync');
    $router->get('/sync/{id}', 'CourseController@syncSingle');
    $router->put('/course/{id}', 'CourseController@update');

    // handles orders
    $router->get('/orders', 'OrderController@index');
    $router->get('/orders/{id}', 'OrderController@show');
    // creates a new order
    $router->post('/orders', 'OrderController@create');
    // updates a given order
    $router->put('/orders/{id}', 'OrderController@update');
});


if (env('APP_ENV') === 'local') {
    $router->get('/test/wp/sync/{id}', function ($id) {
        Log::debug('sync/'.$id.' called');

        return new \Illuminate\Http\JsonResponse([
            'message' => 'sync/' . $id,
            'note' => 'this is a test route',
        ]);
    });

    $router->get('/test/wp/sync_all', function () {
        Log::debug('sync_all called');
        return new \Illuminate\Http\JsonResponse([
            'message' => 'sync_all called',
            'note' => 'this is a test route',
        ]);
    });
}
