<?php

namespace BYanelli\OpenApiLaravel\Support;

use BYanelli\OpenApiLaravel\Builders\OpenApiDefinitionBuilder;

trait PerDefinitionPerObjectSingleton
{
    /**
     * @param string $object
     * @return static
     */
    public static function for(string $object)
    {
        if ($definition = OpenApiDefinitionBuilder::getCurrent()) {
            if ($existingInstance = $definition->getPropertiesInstance(static::class, $object)) {
                return $existingInstance;
            }

            $definition->setPropertiesInstance(static::class, $object, $newInstance = new static($object));

            return $newInstance;
        }

        return new static($object);
    }
}