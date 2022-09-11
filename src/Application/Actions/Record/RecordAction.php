<?php

namespace App\Application\Actions\Record;

use App\Application\Actions\Action;
use App\Domain\Record\RecordRepository;
use Psr\Log\LoggerInterface;

abstract class RecordAction extends Action
{
    public function __construct(
        LoggerInterface $logger,
        protected readonly RecordRepository $recordRepository,
    ) {
        parent::__construct($logger);
    }
}
