<?php

namespace BYanelli\OpenApiLaravel\Builders;

use BYanelli\OpenApiLaravel\OpenApiOperation;
use BYanelli\OpenApiLaravel\OpenApiParameter;
use BYanelli\OpenApiLaravel\OpenApiRequestBody;
use BYanelli\OpenApiLaravel\OpenApiResponse;
use BYanelli\OpenApiLaravel\Support\Action;
use BYanelli\OpenApiLaravel\Support\JsonResource;
use Illuminate\Http\Resources\Json\JsonResource as LaravelJsonResource;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Tappable;

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
     * @param OpenApiResponseBuilder|JsonResource|array|string $response
     * @return $this
     * @throws \Exception
     */
    public function addResponse($response): self
    {
        if (is_array($response)) {
            $response = OpenApiResponseBuilder::make()->status(200)->jsonSchema(OpenApiSchemaBuilder::fromArray($response));
        }

        if ($this->isJsonResource($response)) {
            $response = OpenApiResponseBuilder::make()->fromResource($response);
        }

        $this->responses[] = $response;

        return $this;
    }

    public function fromAction(Action $action)
    {
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
                        $operation->addResponse(
                            OpenApiResponseBuilder::make()->fromResponse($responseClass)
                        );
                    }
                })
        );
    }

    /**
     * @param OpenApiRequestBodyBuilder|OpenApiSchemaBuilder|array $request
     * @return $this
     */
    public function request($request): self
    {
        if (is_array($request)) {
            $request = OpenApiSchemaBuilder::fromArray($request);
        }

        if ($request instanceof OpenApiSchemaBuilder) {
            $request = OpenApiRequestBodyBuilder::make()->jsonSchema($request);
        }

        $this->request = $request;

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

        if ($this->isActionPlural($verb)) {$noun = Str::plural($noun);}

        return Str::camel("{$verb} {$noun}");
    }

    private function isActionPlural(string $verb): bool
    {
        return in_array($verb, ['index']); //todo
    }
}