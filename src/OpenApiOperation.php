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

    /** @var \BYanelli\OpenApiLaravel\OpenApiTag[]|array  */
    public $tags = [];

    /** @var \BYanelli\OpenApiLaravel\OpenApiParameter[]|array  */
    public $parameters = [];

    protected $exceptKeys = ['method'];

    public $ignoreKeysIfEmpty = [
        'description',
        'operationId',
        'responses',
        'tags',
        'parameters',
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
                    ->map(function (OpenApiTag $tag) {return $tag->name;})
                    ->all()
            )
        ];
    }
}
