<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectRequest;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;

class ProjectController extends Controller
{
    public function store(ProjectRequest $request): RedirectResponse
    {
        $project = Project::create($request->validated());

        return to_route('tasks.index', ['project' => $project->id])->with('status', 'Project created.');
    }
}
