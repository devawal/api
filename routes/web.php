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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

// Show all task
$router->get('tasks', 'TasksController@tasksList');

// POST task
$router->post('task', 'TasksController@createTask');

// Update task with task ID
$router->put('task/{task_id}', 'TasksController@updateTask');
