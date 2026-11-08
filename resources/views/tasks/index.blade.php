@extends('layouts.app')
@section('content')
<div class="workspace">
    <aside>
        <h2>Projects</h2>
        <form method="get" action="{{ route('tasks.index') }}">
            <label for="project">Current project</label>
            <select name="project" id="project">
                @forelse($projects as $option)
                    <option value="{{ $option->id }}" @selected($project?->id === $option->id)>{{ $option->name }}</option>
                @empty <option value="">No projects yet</option> @endforelse
            </select>
            @if($projects->isNotEmpty())<button class="secondary" type="submit">View tasks</button>@endif
        </form>
        <hr>
        <form method="post" action="{{ route('projects.store') }}">
            @csrf
            <label for="project-name">New project</label>
            <input id="project-name" name="name" maxlength="255" required placeholder="e.g. Website launch">
            <button class="secondary" type="submit">Create project</button>
        </form>
    </aside>
    <section>
        <div class="section-heading"><div><p class="eyebrow">YOUR TASKS</p><h1>{{ $project?->name ?? 'Start with a project' }}</h1></div><span class="count">{{ $tasks->count() }} tasks</span></div>
        @if($project)
            <form class="add-task" method="post" action="{{ route('tasks.store', $project) }}">
                @csrf
                <label class="sr-only" for="task-name">Task name</label>
                <input id="task-name" name="name" value="{{ old('name') }}" maxlength="255" required placeholder="What needs to be done?">
                <button type="submit">Add task</button>
            </form>
            <p id="order-status" class="hint" role="status" aria-live="polite">Highest priority at the top. Use the handle or arrow buttons to reorder.</p>
            <ol id="task-list" data-url="{{ route('tasks.reorder', $project) }}" data-version="{{ $project->order_version }}">
                @foreach($tasks as $task)
                    <li data-id="{{ $task->id }}">
                        <button class="handle icon" type="button" aria-label="Drag {{ $task->name }}" title="Drag to reorder">⠿</button>
                        <span class="priority">{{ $task->priority }}</span>
                        <div class="task-info"><strong>{{ $task->name }}</strong><small>Created {{ $task->created_at->format('M j, Y') }}</small></div>
                        <div class="actions">
                            <button class="icon move" type="button" data-direction="-1" aria-label="Move {{ $task->name }} up">↑</button>
                            <button class="icon move" type="button" data-direction="1" aria-label="Move {{ $task->name }} down">↓</button>
                            <a href="{{ route('tasks.edit', [$project, $task]) }}">Edit</a>
                            <form method="post" action="{{ route('tasks.destroy', [$project, $task]) }}" data-confirm="Delete this task?">
                                @csrf @method('delete')
                                <button class="delete" type="submit" aria-label="Delete {{ $task->name }}">Delete</button>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ol>
            @if($tasks->isEmpty())<div class="empty"><h2>A clear starting point.</h2><p>Add your first task above, then put the most important one first.</p></div>@endif
        @else
            <div class="empty"><h2>A little structure goes a long way.</h2><p>Create a project to start adding tasks.</p></div>
        @endif
    </section>
</div>
@endsection
