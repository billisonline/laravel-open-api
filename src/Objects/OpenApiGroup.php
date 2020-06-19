<?php

namespace BYanelli\OpenApiLaravel\Objects;

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

    public static function current()
    {
        return static::$currentInstance;
    }

    public function usingBearerTokenAuth(): self
    {
        $this->usingBearerTokenAuth = true;

        return $this;
    }

    public function operations(callable $callable): void
    {
        //todo: stack?
        static::$currentInstance = $this;

        $callable();

        static::$currentInstance = null;
    }

    public function apply(OpenApiOperation $operation): void
    {
        if ($this->usingBearerTokenAuth) {
            $operation->usingBearerTokenAuth();
        }
    }
}