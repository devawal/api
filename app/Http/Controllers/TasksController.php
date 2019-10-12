<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Utility\Helpers;
use App\Tasks;
use Exception;

/**
 * Tasks controller 
 * 
 * @author   Abdul awal <awal.ashu@gmail.com>
 */
class TasksController extends Controller
{
    /**
     * The user data source URL.
     *
     * @var string
     */
    protected $userSource = 'https://gitlab.iterato.lt/snippets/3/raw';

    /**
     * Show all task
     *
     * @return view
     */
    public function tasksList()
    {
        // Call helper method for curl
        $user = Helpers::curl($this->userSource);

        // Get all tasks data
        $tasks = Tasks::getAllTasks($user);

        return view('tasks', compact('tasks'));
    }

    /**
     * Create new task
     * 
     * @param \Illuminate\Http\Request  $request
     * @return json
     */
    public function createTask(Request $request)
    {
        try {
            $input = $request->all();

            // Call task create validation
            $validation = $this->taskRules($input);
            if (!empty($validation)) {
                // Return validation errors with 400 status code
                return response()->json($validation, 400);
            }

            // Save task
            $task = Tasks::saveTask($input);

            // Return 201 ststus code when created
            return response()->json($task, 201);
        } catch (Exception $e) {
            // Return 500 ststus code when get any internal server error
            return response()->json(['message' => $e->getMessage().' In '.$e->getFile()], 500);
        }
    }

    /**
     * Update existing task
     * 
     * @param int $taskID
     * @param \Illuminate\Http\Request  $request
     * @return json
     */
    public function updateTask(Request $request, $taskID)
    {
        try {
            $input = $request->all();

            // Call task update validation
            $validation = $this->taskRules($input);
            if (!empty($validation)) {
                // Return validation errors with 400 status code
                return response()->json($validation, 400);
            }

            // Update task
            $task = Tasks::updateTask($input, $taskID);

            // Return 201 ststus code when created
            return response()->json($task, 201);
        } catch (Exception $e) {
            // Return 500 ststus code when get any internal server error
            return response()->json(['message' => $e->getMessage().' In '.$e->getFile()], 500);
        }
    }

    /**
     * Task validation rules
     *
     * @param array $input
     * @return array
     */
    private function taskRules($input)
    {
        $message = array();

        // Call helper method for curl
        $user = Helpers::curl($this->userSource);

        // Check for parent id
        if (!empty($input['parent_id'])) {
            $taskID = Tasks::getTaskByID($input['parent_id']);
            if (empty($taskID)) {
                $message['parent_id'] = 'existing task id or null';
            }
            // Check Maximum depth: 5
            $parentID = Tasks::getTaskByParentID($input['parent_id']);
            if ($parentID >= 5) {
               $message['maximum_depth'] = 'Maximum depth: 5';
            }
        }

        // Check for user
        if (!isset($input['user_id'])) {
            $message['user_id'] = 'required and existing user id';
        } else {
            $key = array_search($input['user_id'], array_column($user['data'], 'id'));
            if ($key === false) {
                $message['user_id'] = 'required and existing user id';
            }
        }

        // Check for title
        if (!isset($input['title']) || empty($input['title'])) {
            $message['title'] = 'required';
        }

        // Check for points
        if (!isset($input['points']) || !is_int($input['points'])) {
            $message['point'] = 'required integer where the minimum value is 1 and the maximum value is 10';
        } else {
            if ($input['points'] < 1 || $input['points'] > 10) {
                $message['point'] = 'required integer where the minimum value is 1 and the maximum value is 10';
            }
        }

        // Check is done
        if (!isset($input['is_done']) || !is_int($input['is_done'])) {
            $message['is_done'] = 'required integer, 0 or 1';
        } else {
            if ($input['is_done'] < 0 || $input['is_done'] > 1) {
                $message['is_done'] = 'required integer, 0 or 1';
            }
        }

        return $message;
    }
}
