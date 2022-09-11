<?php

namespace App\Domain\Record;

use App\Domain\Record\DTO\RecordStoreDTO;
use App\Infrastructure\Persistence\DoctrineCustomTypes;
use Brick\Math\BigDecimal;
use DateTimeImmutable;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table(name: 'records')]
class Record
{
    #[Id]
    #[Column]
    #[GeneratedValue]
    private int $id;

    #[Column]
    private string $description;

    #[Column]
    private RecordTypes $type;

    #[Column(type: DoctrineCustomTypes::BIG_DECIMAL, precision: 15, scale: 4)]
    private BigDecimal $amount;

    #[Column]
    private DateTimeImmutable $reference;

    #[Column(name: 'paid')]
    private bool $paid = false;

    #[Column(name: 'created_at')]
    private DateTimeImmutable $createdAt;

    #[Column(name: 'updated_at', nullable: true)]
    private ?DateTimeImmutable $updatedAt;

    public static function createFromStoreAction(RecordStoreDTO $recordStoreDTO): self
    {
        $record = new Record();

        $record->description = $recordStoreDTO->description;
        $record->type = RecordTypes::from($recordStoreDTO->type);
        $record->amount = BigDecimal::of($recordStoreDTO->amount);
        $record->reference = $recordStoreDTO->reference;
        $record->paid = $recordStoreDTO->paid;
        $record->createdAt = new DateTimeImmutable();
        $record->updatedAt = new DateTimeImmutable();

        return $record;
    }

    public function updateFromDTO(RecordStoreDTO $recordStoreDTO): self
    {
        $this->description = $recordStoreDTO->description;
        $this->type = RecordTypes::from($recordStoreDTO->type);
        $this->amount = BigDecimal::of($recordStoreDTO->amount);
        $this->reference = $recordStoreDTO->reference;
        $this->paid = $recordStoreDTO->paid;
        $this->updatedAt = new DateTimeImmutable();

        return $this;
    }

    public function markAsPaid(): void
    {
        $this->paid = true;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getType(): RecordTypes
    {
        return $this->type;
    }

    public function getAmount(): BigDecimal
    {
        return $this->amount;
    }

    public function getReference(): DateTimeImmutable
    {
        return $this->reference;
    }

    public function isPaid(): bool
    {
        return $this->paid;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setPaid(bool $paid): void
    {
        $this->paid = $paid;
    }
}
