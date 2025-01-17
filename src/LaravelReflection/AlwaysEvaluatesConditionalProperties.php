<?php


namespace BYanelli\OpenApiLaravel\LaravelReflection;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;

/**
 * @mixin JsonResource
 */
trait AlwaysEvaluatesConditionalProperties
{
    /**
     * Retrieve a value based on a given condition.
     *
     * @param  bool  $condition
     * @param  mixed  $value
     * @param  mixed  $default
     * @return \Illuminate\Http\Resources\MissingValue|mixed
     */
    protected function when($condition, $value, $default = null)
    {
        $evaluated = value($value);

        if ($evaluated instanceof JsonResourcePropertySpy) {
            $evaluated->setIsConditional(true);
        }

        return $evaluated;
    }
}