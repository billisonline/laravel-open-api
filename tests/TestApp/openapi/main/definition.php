<?php

use BYanelli\OpenApiLaravel\Objects\KeyedResponseSchemaWrapper;
use BYanelli\OpenApiLaravel\Objects\OpenApiAuth;
use BYanelli\OpenApiLaravel\Objects\OpenApiDefinition;
use BYanelli\OpenApiLaravel\Objects\OpenApiInfo;
use BYanelli\OpenApiLaravel\Objects\OpenApiOperation;
use TestApp\Http\Controllers\PostController;
use TestApp\Http\Resources\Post;

OpenApiDefinition::current()
    ->responseSchemaWrapper(new KeyedResponseSchemaWrapper('data'));

OpenApiInfo::make()->title('test')->version('1.0');

OpenApiOperation::fromAction([PostController::class, 'index']);

OpenApiOperation::fromAction([PostController::class, 'show'])->response(Post::class);

OpenApiAuth::bearerToken(function () {
    OpenApiOperation::fromAction([PostController::class, 'store'])
        ->request([
            'title' => 'string',
            'body' => 'string',
        ]);
});