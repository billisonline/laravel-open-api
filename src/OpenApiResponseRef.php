<?php

namespace BYanelli\OpenApiLaravel;

class OpenApiResponseRef extends OpenApiResponse
{
    use IsRef;

    public $additionalParams = ['status'];
}