<?php

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class TaskEditingTest extends TestCase
{
    use RefreshDatabase;

    public function test_updates_the_name_without_changing_priority_or_creation_time(): void
    {
        $this->travelTo(Carbon::parse('2026-11-08 12:00:00'));
        $project = Project::create(['name' => 'Work']);
        $task = $project->tasks()->create(['name' => 'First', 'priority' => 1]);
        $this->get(route('tasks.edit', [$project, $task]))->assertOk()->assertSee('First');
        $this->travelTo(Carbon::parse('2026-11-08 13:00:00'));
        $this->put(route('tasks.update', [$project, $task]), ['name' => 'Renamed', 'priority' => 99])
            ->assertRedirectToRoute('tasks.index', ['project' => $project->id]);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id, 'name' => 'Renamed', 'priority' => 1,
            'created_at' => '2026-11-08 12:00:00', 'updated_at' => '2026-11-08 13:00:00',
        ]);
    }

    public function test_rejects_an_invalid_name_without_changing_the_task(): void
    {
        $project = Project::create(['name' => 'Work']);
        $task = $project->tasks()->create(['name' => 'First', 'priority' => 1]);
        $this->put(route('tasks.update', [$project, $task]), ['name' => ''])->assertSessionHasErrors('name');
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'name' => 'First']);
    }

    public function test_returns_404_for_editing_a_task_through_another_project(): void
    {
        $project = Project::create(['name' => 'Work']);
        $other = Project::create(['name' => 'Home'])->tasks()->create(['name' => 'C', 'priority' => 1]);
        $this->get(route('tasks.edit', [$project, $other]))->assertNotFound();
        $this->put(route('tasks.update', [$project, $other]), ['name' => 'Changed'])->assertNotFound();
        $this->assertDatabaseHas('tasks', ['id' => $other->id, 'name' => 'C']);
    }
}
