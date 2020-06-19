<?php

namespace BYanelli\OpenApiLaravel;

use Spatie\DataTransferObject\DataTransferObject;

class OpenApiOperation extends DataTransferObject
{
   use DtoSerializationOptions;

    /** @var string */
    public $method;

    /** @var string */
    public $description = '';

    /** @var string */
    public $operationId = '';

    /** @var \BYanelli\OpenApiLaravel\OpenApiResponse[]|array  */
    public $responses = [];

    /** @var \BYanelli\OpenApiLaravel\OpenApiRequestBody|null  */
    public $requestBody;

    /** @var \BYanelli\OpenApiLaravel\OpenApiTag[]|array  */
    public $tags = [];

    /** @var \BYanelli\OpenApiLaravel\OpenApiParameter[]|array  */
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
     * @param OpenApiTag[]|array $tags
     * @return array
     */
    protected function serializeTags(array $tags): array
    {
        return [
            'tags' => (
                collect($tags)
                    ->map(function ($tag) {
                        if (is_object($tag) && ($tag instanceof OpenApiTag)) {
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
