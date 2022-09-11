<?php

declare(strict_types=1);

namespace App\Domain\Record\DTO;

use DateTimeImmutable;

class RecordStoreDTO
{
    public function __construct(
        public readonly string $description,
        public readonly string $type,
        public readonly float $amount,
        public readonly DateTimeImmutable $reference,
        public readonly bool $paid,
    ) {
    }
}
