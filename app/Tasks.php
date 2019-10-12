<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Tasks extends Model
{
    protected $table = 'tasks';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['gr_id', 'parent_id', 'user_id', 'title', 'points', 'is_done', 'created_at', 'updated_at'];

    /**
     * Get all tasks
     * 
     * @param array $users
     * @return collection object
     */
    public static function getAllTasks($users)
    {
        $userData = array();
        foreach ($users['data'] as $key => $u) {
            // Get all parent tasks
            $tasks = Tasks::getParentTaskByUserID($u['id']);
            foreach ($tasks as $index => $t) {
                // Get sub-task total points
                $t->total_points = Tasks::getTotalTasksByTaskUserID($t->id, $u['id']);
                // Get sub-tasks by task id
                $t->subtasks = Tasks::getSubTasksByTaskUserID($t->id, $u['id']);
                foreach ($t->subtasks as $st) {
                    // Get sub-task childrens total points
                    $st->total_points = Tasks::getTotalParentTasksByTaskUserID($st->id, $u['id']);
                    // get sub-task childrens
                    $st->subsubtasks = Tasks::getSubTasksByTaskUserID($st->id, $u['id']);
                }
            }

            $userData[] = array(
                'id' => $u['id'],
                'name' => $u['first_name'].' '.$u['last_name'],
                'task_done' => Tasks::getDoneTasksByUserID($u['id']),
                'total_task' => Tasks::getTotalTasksByUserID($u['id']),
                'tasks' => $tasks
            );
        }

        return $userData;
    }

    /**
     * Get done tasks count by user ID
     *
     * @param int $user
     * @return int
     */
    public static function getDoneTasksByUserID($user)
    {
        $total = Tasks::where('user_id', $user)->where('is_done', 1)->select(DB::raw("SUM(points) as total"))->first();

        return !empty($total) ? $total->total : 0;
    }

    /**
     * Get total tasks points count by user ID
     *
     * @param int $user
     * @return int
     */
    public static function getTotalTasksByUserID($user)
    {
        $total = Tasks::where('user_id', $user)->select(DB::raw("SUM(points) as total"))->first();

        return !empty($total) ? $total->total : 0;
    }

    /**
     * Get total tasks points count by task and user ID
     *
     * @param int $taskID
     * @return int
     */
    public static function getTotalTasksByTaskUserID($taskID, $userID)
    {
        $parent = Tasks::where('parent_id', $taskID)->where('user_id', $userID)->first();
        if (!empty($parent)) {
            $task = Tasks::where('parent_id', $taskID)->where('user_id', $userID)->select(DB::raw("SUM(points) as total"))->first();
        } else  {
            $task = Tasks::where('id', $taskID)->where('user_id', $userID)->select(DB::raw("SUM(points) as total"))->first();
        }

        return !empty($task) ? $task->total : 0;
    }

    /**
     * Get total parent tasks points count by task and user ID
     *
     * @param int $taskID
     * @param int $userID
     * @return int
     */
    public static function getTotalParentTasksByTaskUserID($taskID, $userID)
    {
        // Check if sub task has grand task
        $gr = Tasks::where('parent_id', $taskID)->whereNotNull('gr_id')->where('user_id', $userID)->first();
        if (!empty($gr)) {
            $task = Tasks::where('parent_id', $gr->parent_id)->whereNotNull('gr_id')->where('user_id', $userID)->select(DB::raw("SUM(points) as total"))->first();
            return $task->total;
        } else {
            $task = Tasks::where('id', $taskID)->where('user_id', $userID)->select(DB::raw("SUM(points) as total"))->first();

            return !empty($task) ? $task->total : 0;
        }
    }

    /**
     * Get task by ID
     *
     * @param int task ID
     * @return collection object
     */
    public static function getTaskByID($taskID)
    {
        return Tasks::find($taskID);
    }

    /**
     * Get task by user ID
     *
     * @param int $userID
     * @return collection object
     */
    public static function getParentTaskByUserID($userID)
    {
        return Tasks::where('user_id', $userID)->whereNull('parent_id')->select(DB::raw("id, title, user_id"))->get();
    }

    /**
     * Get sub-task by parent task ID and user ID
     *
     * @param int $taskID
     * @param int $userID
     * @return collection object
     */
    public static function getSubTasksByTaskUserID($taskID, $userID)
    {
        return Tasks::where('parent_id', $taskID)->where('user_id', $userID)->get();
    }

    /**
     * Get task by parent ID
     *
     * @param int $parent_id
     * @return int
     */
    public static function getTaskByParentID($parent_id)
    {
        return Tasks::where('parent_id', $parent_id)->count();
    }


    /**
     * Save task
     *
     * @param array $input
     * @return array
     */
    public static function saveTask($input)
    {
        // Check if parent_id has grand parent id
        $gr = Tasks::where('id', $input['parent_id'])->first();

        $task               = new Tasks();
        if (!empty($gr) && !empty($gr->parent_id)) {
            $task->gr_id    = $gr->parent_id;
        }
        $task->parent_id    = $input['parent_id'];
        $task->user_id      = $input['user_id'];
        $task->title        = $input['title'];
        $task->points       = $input['points'];
        $task->is_done      = $input['is_done'];
        $task->created_at   = date('Y-m-d H:i:s');
        $task->updated_at   = date('Y-m-d H:i:s');

        $task->save();

        return $task->toArray();
    }

    /**
     * Update task
     *
     * @param int $taskID
     * @param array $input
     * @return array
     */
    public static function updateTask($input, $taskID)
    {
        $task               = Tasks::find($taskID);
        $task->user_id      = $input['user_id'];
        $task->user_id      = $input['user_id'];
        $task->title        = $input['title'];
        $task->points       = $input['points'];
        $task->is_done      = $input['is_done'];
        $task->created_at   = date('Y-m-d H:i:s');
        $task->updated_at   = date('Y-m-d H:i:s');

        $task->save();

        return $task->toArray();
    }
}
