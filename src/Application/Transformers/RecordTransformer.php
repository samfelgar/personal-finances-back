<?php

declare(strict_types=1);

namespace App\Application\Transformers;

use App\Domain\Record\Record;
use League\Fractal\TransformerAbstract;

class RecordTransformer extends TransformerAbstract
{
    public function transform(Record $record): array
    {
        return [
            'id' => $record->getId(),
            'description' => $record->getDescription(),
            'type' => $record->getType()->value,
            'amount' => (string) $record->getAmount()->toScale(2),
            'reference' => $record->getReference()->format('c'),
            'paid' => $record->isPaid(),
            'created_at' => $record->getCreatedAt()->format('c'),
            'updated_at' => $record->getUpdatedAt()?->format('c'),
        ];
    }
}
