<?php

namespace BYanelli\OpenApiLaravel\Objects\Dtos;

use Spatie\DataTransferObject\DataTransferObject;

class OpenApiOperationDto extends DataTransferObject
{
   use SerializationOptions;

    /** @var string */
    public $method;

    /** @var string */
    public $description = '';

    /** @var string */
    public $operationId = '';

    /** @var \BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiResponseDto[]|array  */
    public $responses = [];

    /** @var \BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiRequestBodyDto|null  */
    public $requestBody;

    /** @var \BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiTagDto[]|array  */
    public $tags = [];

    /** @var \BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiParameterDto[]|array  */
    public $parameters = [];

    /** @var bool  */
    public $usingBearerTokenAuth = false;

    protected $exceptKeys = ['method'];

    public $ignoreKeysIfEmpty = [
        'description',
        'operationId',
        'responses',
        'tags',
        'parameters',
        'requestBody',
    ];

    public $keyArrayBy = [
        'responses' => 'status',
    ];

    /**
     * @param OpenApiTagDto[]|array $tags
     * @return array
     */
    protected function serializeTags(array $tags): array
    {
        return [
            'tags' => (
                collect($tags)
                    ->map(function ($tag) {
                        if (is_object($tag) && ($tag instanceof OpenApiTagDto)) {
                            return $tag->name;
                        }

                        if (is_string($tag)) {
                            return $tag;
                        }

                        throw new \Exception;
                    })
                    ->all()
            )
        ];
    }

    public function serializeUsingBearerTokenAuth(bool $usingBearerTokenAuth)
    {
        if ($usingBearerTokenAuth) {
            return [
                'security' => [
                    ['BearerAuth' => [],],
                ]
            ];
        }

        return [];
    }
}
