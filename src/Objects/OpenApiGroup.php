<?php

namespace BYanelli\OpenApiLaravel\Objects;

/**
 * Group operations together and specify properties that will be applied to all operations at build time.
 * {@link InteractsWithCurrentGroup}
 */
class OpenApiGroup
{
    use StaticallyConstructible;

    /**
     * @var self|null
     */
    private static $currentInstance;

    /**
     * @var bool
     */
    private $usingBearerTokenAuth;

    /**
     * Get the current group or return null if none has been set.
     *
     * @return OpenApiGroup|null
     */
    public static function current()
    {
        return static::$currentInstance;
    }

    /**
     * Mark the current group as using `Authorization: Bearer {token}` headers for auth.
     *
     * @return $this
     */
    public function usingBearerTokenAuth(): self
    {
        $this->usingBearerTokenAuth = true;

        return $this;
    }

    /**
     * Collect any operations defined in the given callable, setting this group as the "current" group.
     *
     * @param callable $callable
     */
    public function operations(callable $callable): void
    {
        //todo: stack?
        static::$currentInstance = $this;

        $callable();

        static::$currentInstance = null;
    }

    /**
     * Apply this group's properties to a given operation.
     *
     * @param OpenApiOperation $operation
     */
    public function apply(OpenApiOperation $operation): void
    {
        if ($this->usingBearerTokenAuth) {
            $operation->usingBearerTokenAuth();
        }
    }
}