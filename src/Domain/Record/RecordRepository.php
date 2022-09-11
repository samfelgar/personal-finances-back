<?php

namespace App\Domain\Record;

use DateTimeImmutable;

interface RecordRepository
{
    /**
     * @param DateTimeImmutable $reference
     * @return Record[]
     */
    public function findByReferenceDate(DateTimeImmutable $reference): array;

    public function save(Record $record): Record;

    public function delete(Record $record): void;

    public function find(int $id): ?Record;
}
