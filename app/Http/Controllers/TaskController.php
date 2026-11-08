<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $project = Project::firstOrCreate(['name' => 'Inbox']);

        return view('tasks.index', ['project' => $project, 'tasks' => $project->tasks]);
    }

    public function store(TaskRequest $request, Project $project): RedirectResponse
    {
        DB::transaction(function () use ($request, $project) {
            $locked = Project::whereKey($project->id)->lockForUpdate()->firstOrFail();
            $locked->tasks()->create(['name' => $request->validated('name'), 'priority' => $locked->tasks()->count() + 1]);
            $locked->increment('order_version');
        }, 3);

        return to_route('tasks.index', ['project' => $project->id])->with('status', 'Task created.');
    }
}
