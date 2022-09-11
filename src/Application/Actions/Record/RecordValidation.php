<?php

declare(strict_types=1);

namespace App\Application\Actions\Record;

use App\Domain\Record\RecordTypes;
use Laminas\Validator\Date;
use Laminas\Validator\InArray;
use Laminas\Validator\NotEmpty;

trait RecordValidation
{
    private function rules(): array
    {
        return [
            'description' => [new NotEmpty()],
            'type' => [
                new NotEmpty(),
                new InArray([
                    'haystack' => array_map(fn (RecordTypes $type) => $type->value, RecordTypes::cases()),
                ])
            ],
            'amount' => [new NotEmpty()],
            'reference' => [new NotEmpty(), new Date()],
        ];
    }
}
