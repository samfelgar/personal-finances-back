<?php

declare(strict_types=1);

namespace App\Application\Actions\Record;

use App\Application\Transformers\RecordTransformer;
use DateTimeImmutable;
use Exception;
use League\Fractal\Resource\Collection;
use Psr\Http\Message\ResponseInterface as Response;

class ListRecordsByReferenceAction extends RecordAction
{
    /**
     * @throws Exception
     */
    protected function action(): Response
    {
        $queryParams = $this->request->getQueryParams();

        $this->assertRequiredFields($queryParams, [
            'reference',
        ]);

        $reference = new DateTimeImmutable($queryParams['reference']);

        $records = $this->recordRepository->findByReferenceDate($reference);

        return $this->transform(new Collection($records, new RecordTransformer()));
    }
}
