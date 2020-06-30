<?php

namespace BYanelli\OpenApiLaravel\Objects;

use BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiOperationDto;
use BYanelli\OpenApiLaravel\LaravelReflection\Action;
use BYanelli\OpenApiLaravel\LaravelReflection\FormRequest;
use BYanelli\OpenApiLaravel\LaravelReflection\JsonResource;
use Illuminate\Http\Resources\Json\JsonResource as LaravelJsonResource;
use Illuminate\Support\Traits\Tappable;

/**
 * @mixin OpenApiResponse
 */
class OpenApiOperation
{
    use Tappable, StaticallyConstructible, InteractsWithCurrentDefinition, InteractsWithCurrentGroup;

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
     * @var OpenApiResponse
     */
    private $lastAddedResponse;

    /**
     * @var string[]|array
     */
    private $tags = [];

    /**
     * @var bool
     */
    private $usingBearerTokenAuth = false;

    /**
     * @var bool
     */
    private $implicitPath = false;

    /**
     * @param Action|array|string|callable $action
     * @return self
     */
    public static function fromAction($action): self
    {
        return static::make()->action($action);
    }

    public function __construct()
    {
        $this->saveCurrentDefinition();
        $this->saveCurrentGroup();
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
        $this->applyGroupProperties();

        if ($this->inDefinitionContext()) {
            foreach ($this->tags as $name => $description) {
                $this->currentDefinition->registerTag($name, $description);
            }
        }

        return new OpenApiOperationDto([
            'method'                => $this->method,
            'operationId'           => $this->operationId,
            'description'           => $this->description,
            'parameters'            => collect($this->parameters)->map->build()->all(),
            'requestBody'           => optional($this->request)->build(),
            'responses'             => collect($this->responses)->map->build()->all(),
            'tags'                  => $this->inDefinitionContext() ? array_keys($this->tags) : [],
            'usingBearerTokenAuth'  => $this->usingBearerTokenAuth,
        ]);
    }

    public function addParameter(OpenApiParameter $parameter)
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

    private function findResponse(int $status): ?OpenApiResponse
    {
        return collect($this->responses)->first(function (OpenApiResponse $response) use ($status) {
            return $response->getStatus() == $status;
        });
    }

    public function addResponse(OpenApiResponse $response): self
    {
        $status = $response->getStatus();

        $this->responses = (
            collect($this->responses)
                ->filter(function (OpenApiResponse $response) use ($status) {
                    return $response->getStatus() != $status;
                })
                ->merge([$response])
                ->all()
        );

        $this->lastAddedResponse = $response;

        return $this;
    }

    /**
     * @param int|OpenApiResponse|OpenApiSchema|JsonResource|array|string $statusOrBody
     * @param OpenApiResponse|OpenApiSchema|JsonResource|array|string|null $body
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

        $response = $this->findResponse($status) ?: OpenApiResponse::make()->status($status);

        if ($body instanceof OpenApiResponse) {
            $response = $body->status($status);
        } elseif ($body instanceof OpenApiSchema) {
            $response = OpenApiResponse::make()->status($status)->jsonSchema($body);
        } elseif (is_array($body) && !empty($body)) {
            $response->jsonSchema($schema = OpenApiSchema::fromArray($body));

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

    public function implicitPath(): self
    {
        $this->implicitPath = true;

        if ($this->inDefinitionContext()) {
            $this->currentDefinition->addOperationWithImplicitPath($this);
        }

        return $this;
    }

    public function hasImplicitPath(): bool
    {
        return $this->implicitPath;
    }

    /**
     * @param Action|array|string|callable $action
     * @return $this
     */
    public function action($action): self
    {
        if (is_string($action) || is_array($action)) {
            $action = Action::fromName($action);
        }

        $this->action = $action;

        return (
            $this
                ->implicitPath()
                ->method($action->httpMethod())
                ->operationId($action->operationId())
                ->description($action->description())
                ->tap(function (OpenApiOperation $operation) use ($action) {
                    foreach ($action->pathParameters() as $parameter) {
                        $operation->addParameter(
                            OpenApiParameter::make()->fromPathParameter($parameter)
                        );
                    }

                    if ($responseClass = $action->responseClass()) {
                        // todo: multiple status codes
                        $operation->response(
                            OpenApiResponse::make()->fromResponse($responseClass)
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
     * @param OpenApiRequestBody|OpenApiSchema|array|string $request
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
            $request = OpenApiSchema::fromArray($request);
        }

        if ($request instanceof OpenApiSchema) {
            if (!$request->hasComponentKeyAndTitle() && $this->action) {
                $request
                    ->setComponentKey($this->action->requestComponentKey())
                    ->setComponentTitle($this->action->requestComponentTitle());
            }

            $request = OpenApiRequestBody::make()->jsonSchema($request);
        }

        $this->request = $request;

        return $this;
    }

    public function query(array $params): self
    {
        foreach ($params as $name => $type) {
            $this->parameters[] = (
                OpenApiParameter::make()
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

    public function usingBearerTokenAuth(): self
    {
        if ($this->inDefinitionContext()) {
            $this->currentDefinition->usingBearerTokenAuth();
        }

        $this->usingBearerTokenAuth = true;

        return $this;
    }

    public function getImplicitPathAction(): ?Action
    {
        return $this->action;
    }
}