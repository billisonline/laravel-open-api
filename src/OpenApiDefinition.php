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

    public $keyArrayBy  = [
        'paths' => 'path',
    ];

    public $ignoreKeysIfEmpty = [
        'tags',
    ];

    public function autoCollectTags()
    {
        $collectedTags = [];

        foreach ($this->paths as $path) {
            foreach ($path->operations as $operation) {
                foreach ($operation->tags as $tag) {
                    $collectedTags[] = $tag;
                }
            }
        }

        $this->tags = (
            collect(array_merge($this->tags, $collectedTags))
                ->unique(function (OpenApiTag $tag) {return $tag->name;})
                ->all()
        );

        return $this;
    }
}