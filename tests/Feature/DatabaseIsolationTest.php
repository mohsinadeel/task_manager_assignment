<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabaseIsolationTest extends TestCase
{
    public function test_migrations_and_rollback_preserve_other_applications_tables(): void
    {
        config(['database.connections.portal_probe' => array_replace(config('database.connections.mysql'), ['prefix' => ''])]);
        $schema = Schema::connection('portal_probe');
        $connection = DB::connection('portal_probe');
        $tables = ['integration_portal_probe', 'stripe_integration_probe'];
        $created = [];
        try {
            foreach ($tables as $table) {
                $this->assertFalse($schema->hasTable($table), 'Refusing to overwrite an existing probe table.');
                $schema->create($table, function (Blueprint $table): void {
                    $table->id();
                    $table->string('marker');
                });
                $created[] = $table;
                $connection->table($table)->insert(['marker' => 'preserve-me']);
            }
            $this->artisan('migrate', ['--force' => true])->assertSuccessful();
            foreach (['projects', 'tasks', 'migrations'] as $table) {
                $this->assertTrue($schema->hasTable('tasks_test_'.$table));
            }
            $this->artisan('migrate:rollback', ['--force' => true])->assertSuccessful();
            $this->assertFalse($schema->hasTable('tasks_test_projects'));
            $this->assertFalse($schema->hasTable('tasks_test_tasks'));
            foreach ($tables as $table) {
                $this->assertSame(['preserve-me'], $connection->table($table)->pluck('marker')->all());
            }
            $this->artisan('migrate', ['--force' => true])->assertSuccessful();
        } finally {
            foreach ($created as $table) {
                $schema->drop($table);
            }
            DB::disconnect('portal_probe');
        }
    }
}
