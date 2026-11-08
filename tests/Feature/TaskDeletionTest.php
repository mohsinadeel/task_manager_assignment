<?php

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_deletes_a_task_and_closes_the_priority_gap_only_in_its_project(): void
    {
        $project = Project::create(['name' => 'Work']);
        $other = Project::create(['name' => 'Home']);
        $otherTask = $other->tasks()->create(['name' => 'Private task', 'priority' => 1]);
        $a = $project->tasks()->create(['name' => 'First', 'priority' => 1]);
        $b = $project->tasks()->create(['name' => 'Second', 'priority' => 2]);
        $c = $project->tasks()->create(['name' => 'Third', 'priority' => 3]);
        $this->delete(route('tasks.destroy', [$project, $b]))
            ->assertRedirectToRoute('tasks.index', ['project' => $project->id]);
        $this->assertDatabaseMissing('tasks', ['id' => $b->id]);
        $this->assertSame([$a->id, $c->id], $project->tasks()->pluck('id')->all());
        $this->assertSame([1, 2], $project->tasks()->pluck('priority')->all());
        $this->assertSame(1, $otherTask->fresh()->priority);
    }

    public function test_returns_404_for_deleting_a_task_through_another_project(): void
    {
        $project = Project::create(['name' => 'Work']);
        $other = Project::create(['name' => 'Home'])->tasks()->create(['name' => 'C', 'priority' => 1]);
        $this->delete(route('tasks.destroy', [$project, $other]))->assertNotFound();
        $this->assertDatabaseHas('tasks', ['id' => $other->id, 'name' => 'C']);
    }
}
