@extends('layouts.app')
@section('content')
<section class="edit-panel">
    <a href="{{ route('tasks.index', ['project' => $project->id]) }}">← Back to {{ $project->name }}</a>
    <h1>Edit task</h1>
    <form method="post" action="{{ route('tasks.update', [$project, $task]) }}">
        @csrf @method('put')
        <label for="name">Task name</label>
        <input id="name" name="name" value="{{ old('name', $task->name) }}" maxlength="255" required autofocus>
        <p class="hint">Priority #{{ $task->priority }}. Reorder tasks on the project page.</p>
        <button type="submit">Save task</button>
        <a class="cancel" href="{{ route('tasks.index', ['project' => $project->id]) }}">Cancel</a>
    </form>
</section>
@endsection
