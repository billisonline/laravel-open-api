<?php

namespace BYanelli\OpenApiLaravel\Objects\Dtos;

class OpenApiResponseDtoRef extends OpenApiResponseDto
{
    use IsRef;

    public $additionalParams = ['status'];
}