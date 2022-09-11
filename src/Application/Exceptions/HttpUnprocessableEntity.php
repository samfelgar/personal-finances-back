<?php

declare(strict_types=1);

namespace App\Application\Exceptions;

use Slim\Exception\HttpSpecializedException;

class HttpUnprocessableEntity extends HttpSpecializedException
{
    /**
     * @var int
     */
    protected $code = 422;

    /**
     * @var string
     */
    protected $message = 'Unprocessable entity.';

    protected $title = '422 Unprocessable Entity';
    protected $description = 'The server cannot or will not process the request due to an apparent client error.';
}
