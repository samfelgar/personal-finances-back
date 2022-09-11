<?php

namespace App\Domain\Record;

enum RecordTypes: string
{
    case Revenue = 'revenue';
    case Debt = 'debt';
}
