<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Faker\Factory as Faker;
use App\Tasks;

class TasksTest extends TestCase
{
    /**
     * Task create test
     *
     * @return void
     */
    public function testTaskCreate()
    {
        $faker = Faker::create();
        $parent_id = rand(null, 10);
        $data = [
            'parent_id' => $parent_id == 0 ? null : $parent_id,
            'user_id' => rand(1, 5),
            'title' => 'Test '.rand(1, 10),
            'points' => rand(1, 10),
            'is_done' => rand(0, 1),
            'email' => $faker->email,
        ];
        $this->json('POST', '/api/task', $data);

        $actual = $this->response->getStatusCode();

        $this->assertEquals(201, $actual);
    }

    /**
     * Task update test
     *
     * @return void
     */
    public function testTaskUpdate()
    {
        $faker = Faker::create();
        $task_id = rand(1, 10);
        $parent_id = rand(1, 10);
        $data = [
            'parent_id' => $parent_id,
            'user_id' => rand(1, 5),
            'title' => 'Test '.rand(1, 10),
            'points' => rand(1, 10),
            'is_done' => rand(0, 1),
            'email' => $faker->email,
        ];
        $this->json('PUT', '/api/task/'.$task_id, $data);

        $actual = $this->response->getStatusCode();

        $this->assertEquals(201, $actual);
    }
}
