<?php

namespace BYanelli\OpenApiLaravel;

use Spatie\DataTransferObject\DataTransferObject;

class OpenApiDefinition extends DataTransferObject
{
    use DtoSerializationOptions;

    /** @var string */
    public $openapi = '3.0.0';

    /** @var \BYanelli\OpenApiLaravel\OpenApiPath[]|array */
    public $paths = [];

    /** @var \BYanelli\OpenApiLaravel\OpenApiInfo */
    public $info;

    /** @var \BYanelli\OpenApiLaravel\OpenApiTag[]|array */
    public $tags = [];

    /** @var \BYanelli\OpenApiLaravel\OpenApiSchema[]|array */
    public $components = [];

    /** @var bool  */
    public $usingBearerTokenAuth = false;

    protected $exceptKeys = ['usingBearerTokenAuth'];

    public $keyArrayBy  = [
        'paths' => 'path',
    ];

    public $ignoreKeysIfEmpty = [
        'tags',
    ];

    public function autoCollectTags()
    {
        $this->tags = (
            collect($this->tags)
                ->merge(iterator_to_array((function () {
                    foreach ($this->paths as $path) {
                        foreach ($path->operations as $operation) {
                            foreach ($operation->tags as $tag) {
                                yield $tag;
                            }
                        }
                    }
                })()))
                ->unique(function (OpenApiTag $tag) {return $tag->name;})
                ->all()
        );

        return $this;
    }

    private function allToArrayKeyedByComponentKey(array $dtos): array
    {
        return (
            collect($dtos)
                ->keyBy(function ($dto) {return $dto->componentKey;})
                ->map(function ($dto) {return $dto->toArray();})
                ->all()
        );
    }

    public function serializeComponents(array $components)
    {
        $arr = [
            'components' => array_filter([
                'schemas' => $this->allToArrayKeyedByComponentKey($components['schemas'] ?? []),
                'responses' => $this->allToArrayKeyedByComponentKey($components['responses'] ?? []),
            ])
        ];

        if ($this->usingBearerTokenAuth) {
            $arr['components']['securitySchemes']['BearerAuth'] = [
                'type'      => 'http',
                'scheme'    => 'bearer',
            ];
        }

        return empty($arr['components']) ? [] : $arr;
    }
}