<?php
declare(strict_types=1);

namespace Tests\Unit\Hafo\DI\Autowiring;

use Hafo\DI\Autowiring\AutowiringCache;
use Hafo\DI\Autowiring\AutowiringCache\MemoryCache;
use Hafo\DI\Autowiring\DefaultAutowiring;
use Hafo\DI\Container;
use PHPUnit\Framework\TestCase;

class DefaultAutowiringTest extends TestCase
{
    public function testCreateFactorySimple()
    {
        $container = $this->createMock(Container::class);

        $autowiringCache = new MemoryCache();
        $autowiring = new DefaultAutowiring($autowiringCache);

        $factory = $autowiring->createFactory(\stdClass::class);

        self::assertIsCallable($factory);

        $instance = $factory($container);

        self::assertInstanceOf(\stdClass::class, $instance);
    }

    public function testCreateFactoryComplex()
    {
        $container = $this->createMock(Container::class);
        $container->expects(self::once())
            ->method('get')
            ->willReturnCallback(function ($id) {
                self::assertEquals(AutowiringCache::class, $id);

                return new MemoryCache();
            });

        $autowiringCache = new MemoryCache();
        $autowiring = new DefaultAutowiring($autowiringCache);

        $factory = $autowiring->createFactory(DefaultAutowiring::class);

        self::assertIsCallable($factory);

        $instance = $factory($container);

        self::assertInstanceOf(DefaultAutowiring::class, $instance);
    }

    public function testClassNotFound()
    {
        $autowiringCache = new MemoryCache();
        $autowiring = new DefaultAutowiring($autowiringCache);

        $factory = $autowiring->createFactory('\Hafo\DI\Autowiring\Class\Not\Found');

        self::assertNull($factory);
    }
}
