<?php

namespace Squadron\Tests;

use Dotenv\Dotenv;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as Orchestra;
use ReflectionClass;
use ReflectionException;
use Squadron\Base\Exceptions\Handler;

abstract class TestCase extends Orchestra
{
    public function setUp(): void
    {
        $this->loadEnvironmentVariables();

        parent::setUp();

        app()->singleton(ExceptionHandler::class, Handler::class);
    }

    private function loadEnvironmentVariables(): void
    {
        if (! file_exists(__DIR__.'/../.env'))
        {
            return;
        }

        Dotenv::create(__DIR__.'/..')->load();
    }

    /**
     * Define environment setup.
     *
     * @param Application $app
     */
    protected function getEnvironmentSetUp($app): void
    {
        $dbType = env('DB_TYPE', 'memory');

        if ($dbType === 'memory')
        {
            $config = [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ];
        }
        else
        {
            $config = [
                'driver' => 'mysql',
                'host' => env('DB_HOST'),
                'port' => env('DB_PORT'),
                'database' => env('DB_DATABASE'),
                'username' => env('DB_USERNAME'),
                'password' => env('DB_PASSWORD'),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => true,
                'engine' => null,
            ];
        }

        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', $config);
    }

    /**
     * @param Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app): array
    {
        return $this->getServiceProviders();
    }

    /**
     * Calls private / protected method.
     *
     * @param $obj
     * @param $name
     * @param array $args
     *
     * @throws ReflectionException
     *
     * @return mixed
     */
    protected function callMethod($obj, $name, array $args = [])
    {
        $class = new ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method->invokeArgs($obj, $args);
    }

    protected function actingAsRole(string $role)
    {
        return $this->actingAs(new TestUser($role), 'api');
    }

    abstract protected function getServiceProviders(): array;
}
