# Hafo DI

## What is this?

This is a small PHP Dependency Injection Container with autowiring and as little configuration as possible.
It extends `Psr\Container\ContainerInterface` and provides some goodies to help accelerate development.

## How do I install this?

Via Composer:

```
composer require hafo/di
```

PHP version 7.1 or higher is required.

## How to use?

The easiest way is to use the `ContainerBuilder` class:

```php
use Hafo\DI\ContainerBuilder;

$builder = new ContainerBuilder();

// configure the builder
$builder->addFactories([
    Symfony\Component\Console\Application::class => function () {
        return new Symfony\Component\Console\Application();
    }
]);

$container = $builder->createContainer();

// run the application
$application = $container->get(Symfony\Component\Console\Application::class);
$application->run();
```

### Adding service factories

You can add services via ContainerBuilder:

```php
$builder->addFactories([
    Symfony\Component\Console\Application::class => function () {
        return new Symfony\Component\Console\Application();
    },
    // ... add more services
]);
```

It is recommended to use the classnames as identifiers as these can be used for autowiring, if enabled.
Also, it's best practice.

Parameter can be an array, or any iterable really, as long as it provides both the keys and the factory callbacks.

### Decorating services

A decorator is a simple callback that gets called when a service is created. It can be used to modify
the service before returning it.

```php
use Hafo\DI\Container;

$builder->addDecorators([
    Symfony\Component\Console\Application::class => [
        function (Symfony\Component\Console\Application $application, Container $container) {
            $application->add($container->get(MyProject\Console\SomeCommand::class));
        },
        // ... add more decorators for Application class
    ],
    // ... add more decorators for other services
]);
```

Decision whether a decorator should be used for a service is done via simple `is_a()` check,
so you can easily decorate multiple services that implement same interface, for example.

The container makes sure that each decorator gets called only once for each service instance.

### Adding parameters

You can also add parameters:

```php
$builder->addParameters([
    'rootDir' => __DIR__,
    // ... add more parameters
]);
```

Parameters are then registered in the DI container and can be accessed via the `get()` method, just like the services.

### Using autowiring

If you want to avoid writing many factories by hand, you can use autowiring.
Just make sure that the constructor arguments for your service are resolvable, which means that all the dependencies must
be instantiable using the DI container or they must have default values.

```php
use Hafo\DI\Autowiring\AutowiringCache\MemoryCache;
use Hafo\DI\Autowiring\DefaultAutowiring;
use Hafo\DI\ContainerBuilder;

$builder = new ContainerBuilder();

$builder->setAutowiring(new DefaultAutowiring(new MemoryCache()));

$container = $builder->createContainer();
```

You can use `Hafo\DI\Autowiring\AutowiringCache\NetteCache` instead of `MemoryCache` if you use the `nette/caching` package.

You can also implement your own `Hafo\DI\Autowiring\AutowiringCache` or even `Hafo\DI\Autowiring`.

### Interface-implementation map

It is good practice to use interfaces for your services. The DI container however doesn't know by default which specific class
you want to return. We need to specify it:

```php
$builder->addInterfaceImplementationMap([
    Doctrine\ORM\EntityManagerInterface::class => Doctrine\ORM\EntityManager::class
]);
```

### Reusable modules

You can create self-contained reusable modules simply by implementing the `Hafo\DI\Module` interface.
Making the module work in your project is then as easy as instantiating it and calling a method `install`,
passing the builder as an argument.
