<?php

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_displays_the_demo_attribution_and_repository_link(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Task manager')
            ->assertSee('Mohsin Adeel. Created as a demo.')
            ->assertSee('https://github.com/mohsinadeel/task_manager_assignment', false);
    }

    public function test_edit_form_routes_and_assets_keep_the_hosted_subdirectory(): void
    {
        $project = Project::create(['name' => 'Work']);
        $task = $project->tasks()->create(['name' => 'Review the brief', 'priority' => 1]);

        $response = $this->withServerVariables([
            'SCRIPT_NAME' => '/laravel-task-management-demo/index.php',
            'PHP_SELF' => '/laravel-task-management-demo/index.php',
            'SCRIPT_FILENAME' => public_path('index.php'),
        ])->get('http://localhost/laravel-task-management-demo/projects/'.$project->id.'/tasks/'.$task->id.'/edit');
        $response->assertOk()
            ->assertSee('/laravel-task-management-demo/build/assets/', false)
            ->assertSee('/laravel-task-management-demo"', false)
            ->assertSee('/laravel-task-management-demo/projects/'.$project->id.'/tasks/'.$task->id.'"', false);
    }
}
