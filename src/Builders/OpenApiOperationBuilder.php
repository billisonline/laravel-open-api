<?php

namespace BYanelli\OpenApiLaravel\Builders;

use BYanelli\OpenApiLaravel\OpenApiOperation;
use BYanelli\OpenApiLaravel\OpenApiParameter;
use BYanelli\OpenApiLaravel\OpenApiResponse;
use BYanelli\OpenApiLaravel\Support\Action;
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
            'responses' => collect($this->responses)->map->build()->all(),
        ]);
    }

    public function addParameter(OpenApiParameterBuilder $parameter)
    {
        $this->parameters[] = $parameter;

        return $this;
    }

    public function addResponse(OpenApiResponseBuilder $response): self
    {
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