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
    $router->get('/course', 'CourseController@index');
    $router->get('/course/{id}', 'CourseController@show');
    $router->put('/course/{id}', 'CourseController@update');
    $router->get('/course/{id}/cal', 'CourseController@calendar');
    // resends the emails, for the given order
    $router->post('/course/{id}/sendemails/{type}', 'CourseController@sendEmailsCourse');

    $router->get('/coursetype', 'CourseTypeController@index');
    $router->put('/coursetype/{id}', 'CourseTypeController@update');
    $router->get('/coursetype/{id}', 'CourseTypeController@show');

    // handles orders
    $router->get('/orders', 'OrderController@index');
    // fetches a given order
    $router->get('/orders/{id}', 'OrderController@show');
    // creates a new order
    $router->post('/orders', 'OrderController@create');
    // updates a given order
    $router->put('/orders/{id}', ['middleware' => 'transform.courses', 'uses' => 'OrderController@update']);
    // updates a given order
    $router->post('/orders/{id}/close', ['middleware' => 'transform.order', 'uses' => 'OrderController@closeOrder']);
    // resyncs the given order to maconomy
    $router->post('/orders/{id}/resync', 'OrderController@resyncOrder');
    // resends the emails, for the given order
    $router->post('/orders/{id}/resendemails', 'OrderController@resendEmailsOrder');
    // shows all the users' who have made orders on the system
    $router->get('/orderconsents', 'OrderController@consents');

    // handle mail texts from wp
    $router->put('/texts/mails', 'MailController@update');

    $router->get('/location', 'LocationController@index');
    $router->put('/location/{id}', 'LocationController@update');

    $router->get('/trainer', 'TrainerController@index');
    $router->put('/trainer/{id}', 'TrainerController@update');

    // newsletter signup routes
    $router->post('/newsletter/signup', 'NewsletterController@signup');


    // handles calenders for participants on the orders
    $router->get('/calendar/{orderHash}', 'CalendarController@show');
});


if (env('APP_ENV') !== 'production') {
    $router->get('/test/wp/sync/{id}', function ($id) {
        Log::debug('sync/'.$id.' called');

        return new \Illuminate\Http\JsonResponse([
            'message' => 'sync/' . $id,
            'note' => 'this is a test route',
        ]);
    });

    $router->get('/test/wp/sync', function () {
        Log::debug('sync_all called');
        return new \Illuminate\Http\JsonResponse([
            'message' => 'sync_all called',
            'note' => 'this is a test route',
        ]);
    });
}
