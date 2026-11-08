<?php

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskReorderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_reorder_persists_contiguous_priorities_and_rejects_stale_versions(): void
    {
        $project = Project::create(['name' => 'Work']);
        $a = $project->tasks()->create(['name' => 'First', 'priority' => 1]);
        $b = $project->tasks()->create(['name' => 'Second', 'priority' => 2]);
        $url = route('tasks.reorder', $project);
        $this->putJson($url, ['task_ids' => [$b->id, $a->id], 'version' => 0])->assertOk()->assertJson(['version' => 1]);
        $this->assertSame([$b->id, $a->id], $project->tasks()->pluck('id')->all());
        $this->assertSame([1, 2], $project->tasks()->pluck('priority')->all());
        $this->putJson($url, ['task_ids' => [$a->id, $b->id], 'version' => 0])->assertConflict();
        $this->assertSame([$b->id, $a->id], $project->tasks()->pluck('id')->all());
    }

    public function test_reorder_rejects_incomplete_duplicate_and_foreign_ids_without_partial_writes(): void
    {
        $project = Project::create(['name' => 'Work']);
        $a = $project->tasks()->create(['name' => 'A', 'priority' => 1]);
        $b = $project->tasks()->create(['name' => 'B', 'priority' => 2]);
        $other = Project::create(['name' => 'Home'])->tasks()->create(['name' => 'C', 'priority' => 1]);
        $url = route('tasks.reorder', $project);
        $this->putJson($url, ['task_ids' => [$a->id], 'version' => 0])->assertConflict();
        $this->putJson($url, ['task_ids' => [$a->id, $other->id], 'version' => 0])->assertConflict();
        $this->putJson($url, ['task_ids' => [$a->id, $a->id], 'version' => 0])->assertUnprocessable();
        $this->putJson($url, ['task_ids' => ['bad'], 'version' => 0])->assertUnprocessable();
        $this->assertSame([$a->id, $b->id], $project->tasks()->pluck('id')->all());
        $this->assertSame(0, $project->fresh()->order_version);
    }
}
