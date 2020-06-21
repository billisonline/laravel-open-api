<?php

namespace BYanelli\OpenApiLaravel\LaravelReflection;

use BYanelli\OpenApiLaravel\Objects\OpenApiDefinition;

trait PerDefinitionPerObjectSingleton
{
    /**
     * @param string $object
     * @return static
     */
    public static function for(string $object)
    {
        if ($definition = OpenApiDefinition::current()) {
            if ($existingInstance = $definition->getPropertiesInstance(static::class, $object)) {
                return $existingInstance;
            }

            $definition->setPropertiesInstance(static::class, $object, $newInstance = new static($object));

            return $newInstance;
        }

        return new static($object);
    }
}