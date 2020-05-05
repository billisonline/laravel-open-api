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
    public $resourceSchemas = [];

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

    public function serializeResourceSchemas(array $resourceSchemas)
    {
        if (empty($resourceSchemas)) {return [];}

        return ['components' => ['schemas' => ['resources' =>
            collect($resourceSchemas)
                ->keyBy(function (OpenApiNamedSchema $schema) {return $schema->name;})
                ->map(function (OpenApiNamedSchema $schema) {return $schema->toArray();})
                ->all()
        ]]];
    }

}