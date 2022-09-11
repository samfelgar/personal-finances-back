<?php
declare(strict_types=1);

use App\Application\Settings\Settings;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {

    // Global Settings Object
    $containerBuilder->addDefinitions([
        SettingsInterface::class => function () {
            return new Settings([
                'displayErrorDetails' => true, // Should be set to false in production
                'logError' => false,
                'logErrorDetails' => false,
                'logger' => [
                    'name' => 'slim-app',
                    'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
                    'level' => Logger::DEBUG,
                ],
                'doctrine' => [
                    'dev_mode' => (bool) ($_ENV['APP_DEBUG'] ?? false),
                    'cache_dir' => __DIR__ . '/../var/doctrine',
                    'metadata_dirs' => [
                        __DIR__ . '/../src/Domain'
                    ],
                    'connection' => [
                        'driver' => $_ENV['DB_DRIVER'] ?? 'pdo_mysql',
                        'host' => $_ENV['DB_HOST'],
                        'port' => $_ENV['DB_PORT'],
                        'dbname' => $_ENV['DB_DATABASE'],
                        'user' => $_ENV['DB_USERNAME'],
                        'password' => $_ENV['DB_PASSWORD'],
                        'charset' => 'utf8'
                    ]
                ]
            ]);
        }
    ]);
};
