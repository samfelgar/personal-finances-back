<?php
declare(strict_types=1);

namespace App\Application\Actions;

use App\Application\Exceptions\HttpUnprocessableEntity;
use App\Domain\DomainException\DomainRecordNotFoundException;
use InvalidArgumentException;
use Laminas\Validator\NotEmpty;
use Laminas\Validator\ValidatorInterface;
use League\Fractal\Manager;
use League\Fractal\Resource\ResourceAbstract;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;

abstract class Action
{
    protected Request $request;

    protected Response $response;

    protected array $args;

    public function __construct(
        protected LoggerInterface $logger,
    ) {
    }

    /**
     * @throws HttpNotFoundException
     * @throws HttpBadRequestException
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $this->request = $request;
        $this->response = $response;
        $this->args = $args;

        try {
            return $this->action();
        } catch (DomainRecordNotFoundException $e) {
            throw new HttpNotFoundException($this->request, $e->getMessage());
        }
    }

    /**
     * @throws DomainRecordNotFoundException
     * @throws HttpBadRequestException
     */
    abstract protected function action(): Response;

    protected function getFormData(): object|array
    {
        return $this->request->getParsedBody();
    }

    protected function assertRequiredFields(array $payload, array $required): void
    {
        $validator = new NotEmpty();

        foreach ($required as $requiredField) {
            $field = $payload[$requiredField] ?? null;

            if (!$validator->isValid($field)) {
                $message = implode("\n", $validator->getMessages());

                throw new HttpUnprocessableEntity($this->request, $message);
            }
        }
    }

    /**
     * @param array $payload
     * @param array<string, ValidatorInterface|ValidatorInterface[]> $rules
     * @return void
     */
    protected function validate(array $payload, array $rules): void
    {
        foreach ($rules as $field => $rule) {
            if (!isset($payload[$field])) {
                throw new InvalidArgumentException("The field {$field} does not exists");
            }

            $value = $payload[$field];

            if (is_array($rule)) {
                foreach ($rule as $validator) {
                    $this->isValid($value, $validator);
                }

                continue;
            }

            if (!($rule instanceof ValidatorInterface)) {
                throw new InvalidArgumentException('The rule must be a instance of Laminas\ValidatorInterface');
            }

            $this->isValid($value, $rule);
        }
    }

    private function isValid(mixed $value, ValidatorInterface $validator): void
    {
        if (!$validator->isValid($value)) {
            throw new HttpUnprocessableEntity($this->request, implode("\n", $validator->getMessages()));
        }
    }

    /**
     * @return mixed
     * @throws HttpBadRequestException
     */
    protected function resolveArg(string $name)
    {
        if (!isset($this->args[$name])) {
            throw new HttpBadRequestException($this->request, "Could not resolve argument `{$name}`.");
        }

        return $this->args[$name];
    }

    /**
     * @param array|object|null $data
     */
    protected function respondWithData($data = null, int $statusCode = 200): Response
    {
        $payload = new ActionPayload($statusCode, $data);

        return $this->respond($payload);
    }

    protected function transform(ResourceAbstract $resource, int $statusCode = 200): Response
    {
        $transformedData = (new Manager())
            ->createData($resource)
            ->toJson();

        $this->response
            ->getBody()
            ->write($transformedData);

        return $this->response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($statusCode);
    }

    protected function respond(ActionPayload $payload): Response
    {
        $json = json_encode($payload, JSON_PRETTY_PRINT);
        $this->response->getBody()->write($json);

        return $this->response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($payload->getStatusCode());
    }
}
