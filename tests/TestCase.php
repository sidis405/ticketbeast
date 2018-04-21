<?php

namespace Tests;

use App\Exceptions\Handler;
use Seeds\TestingDatabaseSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected static $migrationsRun = false;

    protected function setUp()
    {
        parent::setUp();
        $this->disableExceptionHandling();
        // $this->seed(TestingDatabaseSeeder::class);
        if (!static::$migrationsRun) {
            Artisan::call('migrate');
            static::$migrationsRun = true;
        }
    }


    public function signIn($user = null)
    {
        $user = $user ? $user : create('App\User');
        $this->actingAs($user);
        return $this;
    }

    protected function disableExceptionHandling()
    {
        $this->oldExceptionHandler = $this->app->make(ExceptionHandler::class);
        $this->app->instance(ExceptionHandler::class, new class extends Handler {
            public function __construct()
            {
            }
            public function report(\Exception $e)
            {
            }
            public function render($request, \Exception $e)
            {
                throw $e;
            }
        });
    }

    protected function withExceptionHandling()
    {
        $this->app->instance(ExceptionHandler::class, $this->oldExceptionHandler);
        return $this;
    }

    protected function enableExceptionHandling()
    {
        return $this->withExceptionHandling();
    }
}
