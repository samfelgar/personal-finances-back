<?php

declare(strict_types=1);

namespace App\Application\Actions\Record;

use App\Application\Transformers\RecordTransformer;
use App\Domain\Record\DTO\RecordStoreDTO;
use App\Domain\Record\Record;
use DateTimeImmutable;
use League\Fractal\Resource\Item;
use Psr\Http\Message\ResponseInterface as Response;

class StoreRecordAction extends RecordAction
{
    use RecordValidation;

    protected function action(): Response
    {
        $payload = $this->getFormData();

        $this->validate($payload, $this->rules());

        $record = Record::createFromStoreAction(new RecordStoreDTO(
            $payload['description'],
            $payload['type'],
            (float) $payload['amount'],
            new DateTimeImmutable($payload['reference']),
            $payload['paid'] ?? false
        ));

        $this->recordRepository->save($record);

        return $this->transform(new Item($record, new RecordTransformer()));
    }
}
