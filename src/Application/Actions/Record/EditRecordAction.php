<?php

declare(strict_types=1);

namespace App\Application\Actions\Record;

use App\Application\Transformers\RecordTransformer;
use App\Domain\Record\DTO\RecordStoreDTO;
use League\Fractal\Resource\Item;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;

class EditRecordAction extends RecordAction
{
    use RecordValidation;

    /**
     * @throws \Exception
     */
    protected function action(): Response
    {
        $recordId = (int) $this->args['id'];
        $payload = $this->getFormData();

        $this->validate($payload, $this->rules());

        $record = $this->recordRepository->find($recordId);

        if ($record === null) {
            throw new HttpNotFoundException($this->request);
        }

        $record->updateFromDTO(new RecordStoreDTO(
            $payload['description'],
            $payload['type'],
            (float) $payload['amount'],
            new \DateTimeImmutable($payload['reference']),
            $payload['paid'] ?? false,
        ));

        $this->recordRepository->save($record);

        return $this->transform(new Item($record, new RecordTransformer()));
    }
}
