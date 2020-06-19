<?php

namespace BYanelli\OpenApiLaravel\LaravelReflection;

use BYanelli\OpenApiLaravel\Objects\OpenApiSchema;
use Illuminate\Foundation\Http\FormRequest as BaseFormRequest;

class FormRequest
{
    /**
     * @var BaseFormRequest|string
     */
    private $formRequestClass;

    /**
     * @var FormRequestProperties
     */
    private $definedProperties;

    public function __construct(string $formRequestClass)
    {
        if (!is_subclass_of($formRequestClass, BaseFormRequest::class)) {
            throw new \Exception;
        }

        $this->formRequestClass = $formRequestClass;
        $this->definedProperties = FormRequestProperties::for($formRequestClass);
    }

    public function hasSchema(): bool
    {
        return !is_null($this->schema());
    }

    public function schema(): ?OpenApiSchema
    {
        return $this->definedProperties->schema();
    }

    public function componentKey(): string
    {
        return $this->componentTitle();
    }

    public function componentTitle(): string
    {
        return class_basename($this->formRequestClass);
    }


}