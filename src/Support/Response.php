<?php


namespace BYanelli\OpenApiLaravel\Support;


use BYanelli\OpenApiLaravel\Builders\OpenApiSchemaBuilder;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Response as BaseResponse;

class Response
{
    /**
     * @var BaseResponse|Responsable|string
     */
    private $responseClass;

    /**
     * @var FormRequestProperties
     */
    private $definedProperties;

    public function __construct(string $responseClass)
    {
        if (!(is_subclass_of($responseClass, BaseResponse::class) || is_subclass_of($responseClass, Responsable::class))) {
            throw new \Exception;
        }

        $this->responseClass = $responseClass;
        $this->definedProperties = ResponseProperties::for($responseClass);
    }

    public function schema(): ?OpenApiSchemaBuilder
    {
        return $this->definedProperties->getSchema();
    }

    public function componentKey(): string
    {
        return $this->componentTitle();
    }

    public function componentTitle(): string
    {
        return class_basename($this->responseClass);
    }
}