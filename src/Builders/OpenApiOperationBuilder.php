<?php

namespace BYanelli\OpenApiLaravel\Builders;

use BYanelli\OpenApiLaravel\OpenApiOperation;
use BYanelli\OpenApiLaravel\OpenApiParameter;
use BYanelli\OpenApiLaravel\OpenApiRequestBody;
use BYanelli\OpenApiLaravel\OpenApiResponse;
use BYanelli\OpenApiLaravel\Support\Action;
use BYanelli\OpenApiLaravel\Support\FormRequestProperties;
use BYanelli\OpenApiLaravel\Support\JsonResource;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Resources\Json\JsonResource as LaravelJsonResource;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Tappable;

/**
 * @mixin OpenApiResponseBuilder
 */
class OpenApiOperationBuilder
{
    use Tappable, StaticallyConstructible;

    /**
     * @var string
     */
    private $method;

    /**
     * @var string|null
     */
    private $description = null;

    /**
     * @var OpenApiParameter[]|array
     */
    private $parameters = [];

    /**
     * @var OpenApiRequestBody|null
     */
    private $request;

    /**
     * @var OpenApiResponse[]|array
     */
    private $responses = [];

    /**
     * @var string
     */
    private $operationId;

    /**
     * @var Action
     */
    private $action;

    /**
     * @var OpenApiResponseBuilder
     */
    private $lastAddedResponse;

    public function method(string $method): self
    {
        $this->method = $method;

        return $this;
    }

    public function description(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function build()
    {
        return new OpenApiOperation([
            'method' => $this->method,
            'operationId' => $this->operationId,
            'description' => $this->description,
            'parameters' => collect($this->parameters)->map->build()->all(),
            'requestBody' => optional($this->request)->build(),
            'responses' => collect($this->responses)->map->build()->all(),
        ]);
    }

    public function addParameter(OpenApiParameterBuilder $parameter)
    {
        $this->parameters[] = $parameter;

        return $this;
    }

    private function isJsonResource($val): bool
    {
        if (is_object($val) && ($val instanceof JsonResource)) {
            return true;
        }

        if (is_string($val) && class_exists($val) && is_subclass_of($val, LaravelJsonResource::class)) {
            return true;
        }

        return false;
    }

    /**
     * @param OpenApiResponseBuilder|JsonResource|array|string $body
     * @return $this
     * @throws \Exception
     */
    public function successResponse($body): self
    {
        return $this->response(200, $body);
    }

    private function findResponse(int $status): ?OpenApiResponseBuilder
    {
        return collect($this->responses)->first(function (OpenApiResponseBuilder $response) use ($status) {
            return $response->getStatus() == $status;
        });
    }

    public function addResponse(OpenApiResponseBuilder $response): self
    {
        $status = $response->getStatus();

        $this->responses = (
            collect($this->responses)
                ->filter(function (OpenApiResponseBuilder $response) use ($status) {
                    return $response->getStatus() != $status;
                })
                ->merge([$response])
                ->all()
        );

        $this->lastAddedResponse = $response;

        return $this;
    }

    /**
     * @param int $status
     * @param OpenApiResponseBuilder|JsonResource|array|string $body
     * @return $this
     * @throws \Exception
     */
    public function response(int $status, $body): self
    {
        $response = $this->findResponse($status) ?: OpenApiResponseBuilder::make()->status($status);

        if ($body instanceof OpenApiResponseBuilder) {
            $response = $body->status($status);
        } elseif (is_array($body) && !empty($body)) {
            $response->jsonSchema(OpenApiSchemaBuilder::fromArray($body));
        } elseif (is_array($body) && empty($body)) {
            //
        } elseif ($this->isJsonResource($body)) {
            $response->fromResource($body);

            if ($this->action && $this->action->isPlural()) {$response->plural();}
        } else {
            throw new \Exception;
        }

        return $this->addResponse($response);
    }

    public function emptyResponse(int $status = 200)
    {
        return $this->response($status, []);
    }

    public function fromAction(Action $action)
    {
        $this->action = $action;

        return (
            $this
                ->method($action->httpMethod())
                ->operationId($this->buildOperationId($action))
                ->tap(function (OpenApiOperationBuilder $operation) use ($action) {
                    foreach ($action->pathParameters() as $parameter) {
                        $operation->addParameter(
                            OpenApiParameterBuilder::make()->fromPathParameter($parameter)
                        );
                    }

                    if ($responseClass = $action->responseClass()) {
                        // todo: multiple status codes
                        $operation->successResponse(
                            OpenApiResponseBuilder::make()->fromResponse($responseClass)
                        );
                    }

                    if ($requestClass = $action->formRequestClass()) {
                        $operation->request($requestClass);
                    }
                })
        );
    }

    /**
     * @param OpenApiRequestBodyBuilder|OpenApiSchemaBuilder|array|string $request
     * @return $this
     */
    public function request($request): self
    {
        //todo: test
        if ($this->isFormRequest($request)) {
            $request = FormRequestProperties::for($request)->schema();
        }

        if (is_array($request)) {
            $request = OpenApiSchemaBuilder::fromArray($request);
        }

        if ($request instanceof OpenApiSchemaBuilder) {
            $request = OpenApiRequestBodyBuilder::make()->jsonSchema($request);
        }

        $this->request = $request;

        return $this;
    }

    public function query(array $params): self
    {
        foreach ($params as $name => $type) {
            $this->parameters[] = (
                OpenApiParameterBuilder::make()
                    ->name($name)
                    ->type($type)
                    ->inQuery()
            );
        }

        return $this;
    }

    public function operationId(string $operationId): self
    {
        $this->operationId = $operationId;

        return $this;
    }

    private function buildOperationId(Action $action): string
    {
        [$verb, $noun] = [$action->actionMethod(), $action->objectName()];

        if ($this->action->isPlural()) {$noun = Str::plural($noun);}

        return Str::camel("{$verb} {$noun}");
    }

    public function __call($name, $arguments)
    {
        //todo: error handling
        $this->lastAddedResponse->{$name}(...$arguments);

        return $this;
    }

    private function isFormRequest($request): bool
    {
        return is_string($request) && is_subclass_of($request, FormRequest::class);
    }
}