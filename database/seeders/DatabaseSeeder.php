<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Website launch' => ['Review the brief', 'Build the first page', 'Test on mobile'], 'Personal' => ['Plan the week', 'Read a chapter']] as $name => $tasks) {
            $project = Project::firstOrCreate(['name' => $name]);
            if (! $project->wasRecentlyCreated) {
                continue;
            }
            foreach ($tasks as $index => $task) {
                $project->tasks()->firstOrCreate(['name' => $task], ['priority' => $index + 1]);
            }
        }
    }
}
