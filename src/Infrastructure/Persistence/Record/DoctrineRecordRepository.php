<?php

namespace App\Infrastructure\Persistence\Record;

use App\Domain\Record\Record;
use App\Domain\Record\RecordRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;

class DoctrineRecordRepository implements RecordRepository
{
    public function __construct(
        private readonly EntityManager $entityManager
    ) {
    }

    public function findByReferenceDate(DateTimeImmutable $reference): array
    {
        $dql = /** @lang DQL */
            'select r from App\Domain\Record\Record r where r.reference between ?1 and ?2';

        $query = $this->entityManager->createQuery($dql);
        $query->setParameter(1, $reference->modify('first day of this month'));
        $query->setParameter(2, $reference->modify('last day of this month'));

        return $query->getResult();
    }

    public function save(Record $record): Record
    {
        $this->entityManager->persist($record);
        $this->entityManager->flush();

        return $record;
    }

    public function delete(Record $record): void
    {
        $this->entityManager->remove($record);
        $this->entityManager->flush();
    }

    public function find(int $id): ?Record
    {
        $repository = $this->entityManager->getRepository(Record::class);

        return $repository->find($id);
    }
}
