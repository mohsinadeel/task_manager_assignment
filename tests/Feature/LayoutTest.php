<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_displays_the_demo_attribution_and_repository_link(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Task manager')
            ->assertSee('Mohsin Adeel. Created as a demo.')
            ->assertSee('https://github.com/mohsinadeel/task_manager_assignment', false);
    }
}
