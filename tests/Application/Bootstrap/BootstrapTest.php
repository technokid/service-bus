<?php /** @noinspection PhpUnhandledExceptionInspection */

/**
 * PHP Service Bus (publish-subscribe pattern implementation).
 *
 * @author  Maksim Masiukevich <dev@async-php.com>
 * @license MIT
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types = 1);

namespace ServiceBus\Tests\Application\Bootstrap;

use PHPUnit\Framework\TestCase;
use ServiceBus\Application\Bootstrap;
use ServiceBus\Application\DependencyInjection\Compiler\Logger\GraylogLoggerCompilerPass;
use ServiceBus\Application\DependencyInjection\Compiler\Logger\StdOutLoggerCompilerPass;
use ServiceBus\Application\DependencyInjection\Compiler\TaggedMessageHandlersCompilerPass;
use ServiceBus\Application\DependencyInjection\Extensions\ServiceBusExtension;
use ServiceBus\Application\Exceptions\ConfigurationCheckFailed;
use ServiceBus\Common\Module\ServiceBusModule;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use function ServiceBus\Tests\removeDirectory;

/**
 *
 */
final class BootstrapTest extends TestCase
{
    /** @var string */
    private $cacheDirectory;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->cacheDirectory = \sys_get_temp_dir() . '/bootstrap_test';

        if (\file_exists($this->cacheDirectory) === false)
        {
            \mkdir($this->cacheDirectory);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        removeDirectory($this->cacheDirectory);

        unset($this->cacheDirectory);
    }

    /** @test */
    public function withEmptyEnEntryPointName(): void
    {
        $this->expectException(ConfigurationCheckFailed::class);
        $this->expectExceptionMessage('Entry point name must be specified');

        Bootstrap::withEnvironmentValues();
    }

    /** @test */
    public function withIncorrectEnvironmentKey(): void
    {
        $this->expectException(ConfigurationCheckFailed::class);
        $this->expectExceptionMessage(
            'Provided incorrect value of the environment: "qwerty". Allowable values: prod, dev, test'
        );

        Bootstrap::create('ssss', 'qwerty');
    }

    /** @test */
    public function withIncorrectDotEnvPath(): void
    {
        $this->expectException(ConfigurationCheckFailed::class);

        Bootstrap::withDotEnv(__DIR__ . '/qwertyuiop.env');
    }

    /** @test */
    public function withIncorrectDotEnvFormat(): void
    {
        $this->expectException(ConfigurationCheckFailed::class);

        Bootstrap::withDotEnv(__FILE__);
    }

    /** @test */
    public function buildWithCorrectParameters(): void
    {
        $bootstrap = Bootstrap::create('abube', 'dev');

        $container = $bootstrap->boot();

        static::assertSame(
            'abube',
            $container->getParameter('service_bus.entry_point')
        );

        static::assertSame(
            'dev',
            $container->getParameter('service_bus.environment')
        );
    }

    /** @test */
    public function fullConfigure(): void
    {
        $module = new class() implements ServiceBusModule
        {
            public function boot(ContainerBuilder $containerBuilder): void
            {
                $containerBuilder->setParameter('TestModule', 'exists');
            }
        };

        $bootstrap = Bootstrap::withDotEnv(__DIR__ . '/valid_dot_env_file.env');
        $bootstrap->useCustomCacheDirectory($this->cacheDirectory);
        $bootstrap->addExtensions(new ServiceBusExtension());
        $bootstrap->addCompilerPasses(new TaggedMessageHandlersCompilerPass());
        $bootstrap->importParameters(['qwerty' => 'root']);
        $bootstrap->enableAutoImportMessageHandlers([__DIR__ . '/services']);
        $bootstrap->applyModules($module);

        $container = $bootstrap->boot();

        static::assertTrue($container->hasParameter('TestModule'));
        static::assertTrue($container->hasParameter('qwerty'));

        static::assertSame('exists', $container->getParameter('TestModule'));
        static::assertSame('root', $container->getParameter('qwerty'));
    }

    /** @test */
    public function withLogger(): void
    {
        $bootstrap = Bootstrap::create('withLogger', 'dev');
        $bootstrap->addCompilerPasses(new StdOutLoggerCompilerPass());

        $bootstrap->boot();

        static::assertTrue(true);
    }

    /** @test */
    public function withStdOutLogger(): void
    {
        $bootstrap = Bootstrap::create('withStdOutLogger', 'dev');
        $bootstrap->addCompilerPasses(new StdOutLoggerCompilerPass());

        $bootstrap->boot();

        static::assertTrue(true);
    }

    /** @test */
    public function withGraylogLogger(): void
    {
        $bootstrap = Bootstrap::create('withGraylogLogger', 'dev');
        $bootstrap->addCompilerPasses(new GraylogLoggerCompilerPass());

        $bootstrap->boot();

        static::assertTrue(true);
    }
}
