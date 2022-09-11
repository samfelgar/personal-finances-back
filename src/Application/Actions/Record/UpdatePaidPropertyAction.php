<?php

declare(strict_types=1);

namespace App\Application\Actions\Record;

use App\Application\Exceptions\HttpUnprocessableEntity;
use App\Application\Transformers\RecordTransformer;
use League\Fractal\Resource\Item;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;

class UpdatePaidPropertyAction extends RecordAction
{
    protected function action(): Response
    {
        $recordId = (int) $this->args['id'];
        $payload = $this->getFormData();

        if (!isset($payload['paid'])) {
            throw new HttpUnprocessableEntity($this->request, 'You must inform \'paid\' argument');
        }

        $record = $this->recordRepository->find($recordId);

        if ($record === null) {
            throw new HttpNotFoundException($this->request);
        }

        $record->setPaid($payload['paid']);

        $this->recordRepository->save($record);

        return $this->transform(new Item($record, new RecordTransformer()));
    }
}
