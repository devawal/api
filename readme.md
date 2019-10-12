# Simple Task Management Application

A simple Lumen based REST full tasks management application with simple UI and API. Each task can have subtasks and tasks without subtasks have points. That means parent task has a sum of points from subtasks. What is more, if all subtasks of a task are done, then task also is done. If at least one subtask of a task marked as not done then it also becomes not done.

## About this application

1. The application is using PHP Lumen framework;
2. The application have a simple UI and required API endpoints;
3. It is follow PSR-2 style guide;
4. The Application is written in OOP;
5. The code in simple and clean;
6. The code is tested in PHPUnit test.

## Environment setup

First clone this repository by using `git clone https://github.com/devawal/tasks-management.git`
And run `composer install`

## Third party dependencies

Only PHP Unit is used for unit testing.

## Database

The application is using `MySQL` database and use default connection settings. To connect and create table at first create a database with a name and configure it in `.env` file. In this application I used `tasks_management`.
Run the migration command by typing console command `php artisan migrate`

## User data source

The application is using users from the following link
`https://gitlab.iterato.lt/snippets/3/raw`

The URL can be updated from `App\Http\Controllers\TasksController` in 

```
/**
 * The user data source URL.
 *
 * @var string
 */
protected $userSource = 'https://gitlab.iterato.lt/snippets/3/raw';

```

## Create task

End point for create task is: `http://localhost/tasks_management/public/api/task`

### Input data
```
{
	"parent_id":null,
	"user_id":1,
	"title":"Task 1",
	"points":5,
	"is_done":1,
	"email":"john.doe@email.com"
}
``` 

### Output

The application API will return `201` status code if it passed all validations otherwise it will throw `400` status code with validation message

```
{
    "parent_id": null,
    "user_id": 2,
    "title": "Task 1",
    "points": 5,
    "is_done": 1,
    "created_at": "2019-10-12 14:34:15",
    "updated_at": "2019-10-12 14:34:15",
    "id": 1
}
```

## Update task

End point for update task is: `http://localhost/tasks_management/public/api/task/{task_id}`

### Input data
```
{
	"parent_id":null,
	"user_id":2,
	"title":"Task 1",
	"points":10,
	"is_done":1,
	"email":"john.doe@email.com"
}
```

### Output

The application API will return `201` status code if it passed all validations otherwise it will throw `400` status code with validation message

All other internal errors will return `500` with error message

## View task list

End point for all task: `http://localhost/tasks_management/public/tasks`

# Testing

The application is tested with random data set using `faker`. For Unit test following is the command `./vendor/bin/phpunit`

Test class `tests\TasksTest`
Test configuration file `phpunit.xml`

## Test output from windows
```
$ ./vendor/bin/phpunit
PHPUnit 6.5.14 by Sebastian Bergmann and contributors.

..                                                                  2 / 2 (100%)

Time: 1.83 seconds, Memory: 10.00MB

OK (2 tests, 2 assertions)
```