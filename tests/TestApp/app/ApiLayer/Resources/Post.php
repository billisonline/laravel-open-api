<?php

namespace TestApp\ApiLayer\Resources;

use BYanelli\ApiLayer\Laravel\EloquentResource;
use BYanelli\ApiLayer\Resource\Features\Fields\Integer;
use BYanelli\ApiLayer\Resource\Features\Fields\Text;
use TestApp\Post as PostModel;

class Post extends EloquentResource
{
    protected $itemType = PostModel::class;

    public function fields()
    {
        return [
            'id' => new Integer,
            'body' => new Text,
        ];
    }
}