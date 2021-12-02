<?php

namespace NetLinker\FairQueue\Tests;

use Dotenv\Dotenv;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\Dusk\Bootstrap\LoadConfiguration;
use Orchestra\Testbench\Dusk\TestCase as TestBench;

abstract class BrowserTestCase extends TestBench
{
    use TestHelper;

    protected static $baseServeHost = 'localhost';

    protected $app;

    protected const TEST_APP_TEMPLATE = __DIR__ . '/../testbench/template';

    protected const TEST_APP = __DIR__ . '/../testbench/laravel';

    public static function setUpBeforeClass(): void
    {
        if (!file_exists(self::TEST_APP_TEMPLATE)) {
            self::setUpLocalTestbench();
        }
        parent::setUpBeforeClass();
    }

    protected function getBasePath()
    {
        return self::TEST_APP;
    }

    /**
     * Setup before each test.
     */
    public function setUp(): void
    {
        $this->installTestApp();
        parent::setUp();
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        TestHelper::getEnvironmentSetUp($app);
    }

    /**
     * Tear down after each test.
     */
    public function tearDown(): void
    {
        $this->uninstallTestApp();
        parent::tearDown();
    }

    /**
     * Tell Testbench to use this package.
     *
     * @param $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return TestHelper::getPackageProviders($app);
    }


    /**
     * Get package aliases.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return TestHelper::getPackageAliases($app);
    }
}
