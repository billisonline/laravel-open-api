<?php

namespace BYanelli\OpenApiLaravel\Builders;

use BYanelli\OpenApiLaravel\OpenApiOperation;
use BYanelli\OpenApiLaravel\Support\Action;
use BYanelli\OpenApiLaravel\Support\FormRequest;
use BYanelli\OpenApiLaravel\Support\JsonResource;
use Illuminate\Http\Resources\Json\JsonResource as LaravelJsonResource;
use Illuminate\Support\Traits\Tappable;

/**
 * @mixin OpenApiResponseBuilder
 */
class OpenApiOperationBuilder
{
    use Tappable, StaticallyConstructible, InteractsWithCurrentDefinition;

    /**
     * @var string
     */
    private $method;

    /**
     * @var string|null
     */
    private $description = null;

    /**
     * @var OpenApiParameterBuilder[]|array
     */
    private $parameters = [];

    /**
     * @var OpenApiRequestBodyBuilder|null
     */
    private $request;

    /**
     * @var OpenApiResponseBuilder[]|array
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

    /**
     * @var string[]|array
     */
    private $tags = [];

    public function __construct()
    {
        $this->saveCurrentDefinition();
    }

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
        if ($this->inDefinitionContext()) {
            foreach ($this->tags as $name => $description) {
                $this->currentDefinition->registerTag($name, $description);
            }
        }

        return new OpenApiOperation([
            'method' => $this->method,
            'operationId' => $this->operationId,
            'description' => $this->description,
            'parameters' => collect($this->parameters)->map->build()->all(),
            'requestBody' => optional($this->request)->build(),
            'responses' => collect($this->responses)->map->build()->all(),
            'tags' => $this->inDefinitionContext() ? array_keys($this->tags) : [],
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
     * @param int|OpenApiResponseBuilder|OpenApiSchemaBuilder|JsonResource|array|string $statusOrBody
     * @param OpenApiResponseBuilder|OpenApiSchemaBuilder|JsonResource|array|string|null $body
     * @return $this
     * @throws \Exception
     */
    public function response($statusOrBody, $body=null): self
    {
        if (is_null($body)) {
            [$status, $body] = [200, $statusOrBody];
        } else {
            $status = $statusOrBody;
        }

        $response = $this->findResponse($status) ?: OpenApiResponseBuilder::make()->status($status);

        if ($body instanceof OpenApiResponseBuilder) {
            $response = $body->status($status);
        } elseif ($body instanceof OpenApiSchemaBuilder) {
            $response = OpenApiResponseBuilder::make()->status($status)->jsonSchema($body);
        } elseif (is_array($body) && !empty($body)) {
            $response->jsonSchema($schema = OpenApiSchemaBuilder::fromArray($body));

            if ($this->action) {
                $schema
                    ->setComponentKey($this->action->responseComponentKey())
                    ->setComponentTitle($this->action->responseComponentTitle());
            }
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
                ->operationId($action->operationId())
                ->description($action->description())
                ->tap(function (OpenApiOperationBuilder $operation) use ($action) {
                    foreach ($action->pathParameters() as $parameter) {
                        $operation->addParameter(
                            OpenApiParameterBuilder::make()->fromPathParameter($parameter)
                        );
                    }

                    if ($responseClass = $action->responseClass()) {
                        // todo: multiple status codes
                        $operation->response(
                            OpenApiResponseBuilder::make()->fromResponse($responseClass)
                        );
                    }

                    if ($requestClass = $action->formRequestClass()) {
                        $operation->request($requestClass);
                    }

                    $this->addTag($action->tagName(), $action->tagDescription());
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
            $formRequest = new FormRequest($request);

            if ($formRequest->hasSchema()) {
                $request = (
                    $formRequest
                        ->schema()
                        ->setComponentKey($formRequest->componentKey())
                        ->setComponentTitle($formRequest->componentTitle())
                );
            }
        }

        if (is_array($request)) {
            $request = OpenApiSchemaBuilder::fromArray($request);
        }

        if ($request instanceof OpenApiSchemaBuilder) {
            if (!$request->hasComponentKeyAndTitle() && $this->action) {
                $request
                    ->setComponentKey($this->action->requestComponentKey())
                    ->setComponentTitle($this->action->requestComponentTitle());
            }

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

    public function addTag(string $name, string $description): self
    {
        $this->tags[$name] = $description;

        return $this;
    }
}