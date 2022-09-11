<?php

declare(strict_types=1);

use App\Application\Settings\SettingsInterface;
use App\Infrastructure\Persistence\DoctrineCustomTypes;
use Brick\Doctrine\Types\Math\BigDecimalType;
use DI\ContainerBuilder;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $loggerSettings = $settings->get('logger');
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },

        EntityManager::class => function (ContainerInterface $container) {
            /** @var SettingsInterface $settings */
            $settings = $container->get(SettingsInterface::class);

            $doctrineConfig = $settings->get('doctrine', []);

            $cache = $doctrineConfig['dev_mode'] ?
                new ArrayAdapter() :
                new FilesystemAdapter(directory: $doctrineConfig['cache_dir']);

            $config = ORMSetup::createAttributeMetadataConfiguration(
                $doctrineConfig['metadata_dirs'],
                $doctrineConfig['dev_mode'],
                null,
                $cache
            );

            Type::addType(DoctrineCustomTypes::BIG_DECIMAL, BigDecimalType::class);

            $entityManager = EntityManager::create($doctrineConfig['connection'], $config);

            $entityManager->getConnection()
                ->getDatabasePlatform()
                ->registerDoctrineTypeMapping('decimal', DoctrineCustomTypes::BIG_DECIMAL);

            return $entityManager;
        },
    ]);
};
