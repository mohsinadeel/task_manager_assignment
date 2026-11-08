@extends('layouts.app')
@section('content')
<div>
    <section>
        <div class="section-heading"><div><p class="eyebrow">YOUR TASKS</p><h1>{{ $project?->name ?? 'Start with a project' }}</h1></div><span class="count">{{ $tasks->count() }} tasks</span></div>
        @if($project)
            <form class="add-task" method="post" action="{{ route('tasks.store', $project) }}">
                @csrf
                <label class="sr-only" for="task-name">Task name</label>
                <input id="task-name" name="name" value="{{ old('name') }}" maxlength="255" required placeholder="What needs to be done?">
                <button type="submit">Add task</button>
            </form>
            <ol>
                @foreach($tasks as $task)
                    <li data-id="{{ $task->id }}">
                        <span class="priority">{{ $task->priority }}</span>
                        <div class="task-info"><strong>{{ $task->name }}</strong><small>Created {{ $task->created_at->format('M j, Y') }}</small></div>
                        <div class="actions">
                            <a href="{{ route('tasks.edit', [$project, $task]) }}">Edit</a>
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
