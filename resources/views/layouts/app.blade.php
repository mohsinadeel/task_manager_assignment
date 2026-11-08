<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Task manager</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<main>
    <header>
        <a class="brand" href="{{ route('tasks.index') }}">Task manager</a>
        <a class="repo-link" href="{{ rtrim(config('services.portal_url'), '/').'/feedback?demo=tasks' }}">Send feedback</a>
        <a class="repo-link" href="https://github.com/mohsinadeel/task_manager_assignment" target="_blank" rel="noopener noreferrer">View on GitHub</a>
    </header>
    @if(session('status')) <p class="notice" role="status">{{ session('status') }}</p> @endif
    @if($errors->any())
        <div class="error" role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    @yield('content')
    <footer>
        <p>Drag tasks to change their priority. Changes are saved automatically.</p>
        <p>&copy; {{ date('Y') }} Mohsin Adeel. Created as a demo.</p>
    </footer>
</main>
</body>
</html>
