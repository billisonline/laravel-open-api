<?php

namespace BYanelli\OpenApiLaravel\Objects;

class OpenApiAuth
{
    public static function bearerToken(callable $callable)
    {
        OpenApiGroup::make()->usingBearerTokenAuth()->operations($callable);
    }
}