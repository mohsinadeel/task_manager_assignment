<?php

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class TaskCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_tasks_with_consecutive_priorities_and_timestamps(): void
    {
        $this->travelTo(Carbon::parse('2026-11-08 12:00:00'));
        $project = Project::create(['name' => 'Work']);
        foreach (['First', 'Second', 'Third'] as $name) {
            $this->post(route('tasks.store', $project), ['name' => $name, 'priority' => 99])
                ->assertRedirectToRoute('tasks.index', ['project' => $project->id]);
        }
        $tasks = $project->tasks()->get();
        $this->assertSame(['First', 'Second', 'Third'], $tasks->pluck('name')->all());
        $this->assertSame([1, 2, 3], $tasks->pluck('priority')->all());
        $this->assertDatabaseHas('tasks', [
            'project_id' => $project->id, 'name' => 'First', 'priority' => 1,
            'created_at' => '2026-11-08 12:00:00', 'updated_at' => '2026-11-08 12:00:00',
        ]);
    }

    public function test_rejects_blank_and_overlong_task_names_without_creating_tasks(): void
    {
        $project = Project::create(['name' => 'Work']);
        $this->post(route('tasks.store', $project), ['name' => '   '])->assertSessionHasErrors('name');
        $this->post(route('tasks.store', $project), ['name' => str_repeat('a', 256)])->assertSessionHasErrors('name');
        $this->assertDatabaseCount('tasks', 0);
    }

    public function test_escapes_task_names_in_the_list(): void
    {
        $project = Project::create(['name' => 'Inbox']);
        $project->tasks()->create(['name' => '<script>alert(1)</script>', 'priority' => 1]);
        $this->get('/?project='.$project->id)
            ->assertSee('&lt;script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }
}
