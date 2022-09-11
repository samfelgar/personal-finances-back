<?php

declare(strict_types=1);

namespace App\Application\Actions\Record;

use App\Application\Actions\ActionPayload;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;

class DeleteRecordAction extends RecordAction
{
    protected function action(): Response
    {
        $recordId = (int) $this->args['id'];

        $record = $this->recordRepository->find($recordId);

        if ($record === null) {
            throw new HttpNotFoundException($this->request);
        }

        $this->recordRepository->delete($record);
        return $this->respond(new ActionPayload(204));
    }
}
