<?php
declare(strict_types=1);

namespace App\Application\Handlers;

use App\Application\Actions\ActionError;
use App\Application\Actions\ActionPayload;
use App\Application\Exceptions\HttpUnprocessableEntity;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpNotImplementedException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Handlers\ErrorHandler as SlimErrorHandler;
use Throwable;

class HttpErrorHandler extends SlimErrorHandler
{
    /**
     * @inheritdoc
     */
    protected function respond(): Response
    {
        $exception = $this->exception;
        $statusCode = 500;
        $error = new ActionError(
            ActionError::SERVER_ERROR,
            'An internal error has occurred while processing your request.'
        );

        if ($exception instanceof HttpException) {
            $statusCode = $exception->getCode();
            $error->setDescription($exception->getMessage());
            $error->setType($this->httpErrorType($exception::class));
        }

        if (!($exception instanceof HttpException)
            && $exception instanceof Throwable
            && $this->displayErrorDetails
        ) {
            $error->setDescription($exception->getMessage());
        }

        $payload = new ActionPayload($statusCode, null, $error);
        $encodedPayload = json_encode($payload, JSON_PRETTY_PRINT);

        $response = $this->responseFactory->createResponse($statusCode);
        $response->getBody()->write($encodedPayload);

        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * @param class-string<HttpException> $classname
     * @return string
     */
    protected function httpErrorType(string $classname): string
    {
        return match ($classname) {
            HttpNotFoundException::class => ActionError::RESOURCE_NOT_FOUND,
            HttpMethodNotAllowedException::class => ActionError::NOT_ALLOWED,
            HttpUnauthorizedException::class => ActionError::UNAUTHENTICATED,
            HttpForbiddenException::class => ActionError::INSUFFICIENT_PRIVILEGES,
            HttpBadRequestException::class => ActionError::BAD_REQUEST,
            HttpNotImplementedException::class => ActionError::NOT_IMPLEMENTED,
            HttpUnprocessableEntity::class => ActionError::VALIDATION_ERROR,
            default => ActionError::SERVER_ERROR
        };
    }
}
