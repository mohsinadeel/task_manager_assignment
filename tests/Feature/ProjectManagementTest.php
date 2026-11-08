<?php

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_empty_state_and_project_creation(): void
    {
        $this->get('/')->assertOk()->assertSee('Start with a project');
        $this->post('/projects', ['name' => 'Work'])->assertRedirect();
        $this->assertDatabaseHas('projects', ['name' => 'Work']);
    }

    public function test_filters_tasks_by_selected_project(): void
    {
        $project = Project::create(['name' => 'Work']);
        $other = Project::create(['name' => 'Home']);
        $project->tasks()->create(['name' => 'Renamed', 'priority' => 1]);
        $other->tasks()->create(['name' => 'Private task', 'priority' => 1]);
        $this->get('/?project='.$project->id)->assertOk()->assertSee('Renamed')->assertDontSee('Private task');
        $this->get('/?project='.$other->id)->assertSee('Private task')->assertDontSee('Renamed');
    }

    public function test_rejects_blank_project_names_and_unknown_project_filters(): void
    {
        $this->post('/projects', ['name' => ''])->assertSessionHasErrors('name');
        $this->assertDatabaseCount('projects', 0);
        $this->get('/?project=999999')->assertSessionHasErrors('project');
    }
}
