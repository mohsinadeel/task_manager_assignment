<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReorderTasksRequest;
use App\Http\Requests\TaskRequest;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
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

    public function edit(Project $project, Task $task): View
    {
        return view('tasks.edit', compact('project', 'task'));
    }

    public function update(TaskRequest $request, Project $project, Task $task): RedirectResponse
    {
        $task->update($request->validated());

        return to_route('tasks.index', ['project' => $project->id])->with('status', 'Task updated.');
    }

    public function destroy(Project $project, Task $task): RedirectResponse
    {
        DB::transaction(function () use ($project, $task) {
            $locked = Project::whereKey($project->id)->lockForUpdate()->firstOrFail();
            $current = $locked->tasks()->findOrFail($task->id);
            $priority = $current->priority;
            $current->delete();
            $locked->tasks()->where('priority', '>', $priority)->decrement('priority');
            $locked->increment('order_version');
        }, 3);

        return to_route('tasks.index', ['project' => $project->id])->with('status', 'Task deleted.');
    }

    public function reorder(ReorderTasksRequest $request, Project $project): JsonResponse
    {
        return DB::transaction(function () use ($request, $project) {
            $locked = Project::whereKey($project->id)->lockForUpdate()->firstOrFail();
            $ids = array_map('intval', $request->validated('task_ids'));
            $current = $locked->tasks()->pluck('id')->all();
            // The submitted order must be a permutation of this project's full list.
            $expected = $current;
            $received = $ids;
            sort($expected);
            sort($received);
            abort_if($expected !== $received || (int) $locked->order_version !== (int) $request->validated('version'),
                409, 'The task list changed. Reload the page before reordering.');
            foreach ($ids as $index => $id) {
                $locked->tasks()->whereKey($id)->update(['priority' => $index + 1]);
            }
            $locked->increment('order_version');

            return response()->json(['version' => $locked->order_version]);
        }, 3);
    }
}
