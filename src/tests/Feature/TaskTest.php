<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Task;

use function PHPUnit\Framework\assertSame;

class TaskTest extends TestCase
{
    use RefreshDatabase;
    /**
     * @test
     */
    public function getTaskIndex()
    {
        $tasks = Task::factory()->count(10)->create();

        $response = $this->getJson('/api/tasks');

        $response
            ->assertOk()
            ->assertJsonCount($tasks->count());
    }

    /**
     * @test
     */
    public function TaskCreate()
    {
        $data = [
            'title' => 'test'
        ];

        $response = $this->postJson('/api/tasks', $data);
        
        $response 
            ->assertCreated()
            ->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function TaskUpdate()
    {
        $task = Task::factory()->create();

        $task->title = 'update';

        $response = $this->patchJson('/api/tasks/'. $task->id, $task->toArray());
        
        $response 
            ->assertOK()
            ->assertJsonFragment($task->toArray());
    }

    /**
     * @test
     */
    public function TaskDelete()
    {
        $tasks = Task::factory()->count(10)->create();

        $response = $this->deleteJson('/api/tasks/'. $tasks->first()->id);
        $response->assertOK();

        $response = $this->getJson('/api/tasks');
        $response->assertJsonCount($tasks->count() - 1);
    }

    /**
     * @test
     */
    public function EmptyTitleValid()
    {
        $data = [
            'title' => ''
        ];

        $response = $this->postJson('/api/tasks', $data);
        
        $response 
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'title' => 'タイトルは、必ず指定してください。'
        ]);
    }

     /**
     * @test
     */
    public function TitleMaxInputValid()
    {
        $data = [
            'title' => str_repeat('a', 256)
        ];

        $response = $this->postJson('/api/tasks', $data);
        
        $response 
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'title' => 'タイトルは、255文字以下にしてください。'
        ]);
    }
}
