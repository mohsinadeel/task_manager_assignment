<?php

namespace Tests;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    public function createApplication(): Application
    {
        $app = parent::createApplication();
        $config = $app->make('config');
        if (! $app->environment('testing')
            || $config->get('database.default') !== 'mysql'
            || $config->get('database.connections.mysql.database') !== 'testing'
            || $config->get('database.connections.mysql.url')
            || $config->get('database.connections.mysql.prefix') !== 'tasks_test_') {
            throw new RuntimeException('Task tests require MySQL database testing and prefix tasks_test_; refusing to reset another database.');
        }

        return $app;
    }
}
