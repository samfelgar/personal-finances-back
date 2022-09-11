<?php

declare(strict_types=1);

use App\Domain\Record\RecordRepository;
use App\Domain\User\UserRepository;
use App\Infrastructure\Persistence\Record\DoctrineRecordRepository;
use App\Infrastructure\Persistence\User\InMemoryUserRepository;
use DI\ContainerBuilder;
use function DI\autowire;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        UserRepository::class => autowire(InMemoryUserRepository::class),
        RecordRepository::class => autowire(DoctrineRecordRepository::class),
    ]);
};
