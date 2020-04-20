<?php

namespace BYanelli\OpenApiLaravel\Tests\Feature;

use BYanelli\OpenApiLaravel\Builders\OpenApiOperationBuilder;
use BYanelli\OpenApiLaravel\Builders\OpenApiPathBuilder;
use BYanelli\OpenApiLaravel\Tests\TestCase;
use TestApp\Http\Controllers\PostController;

class PathBuilderTest extends TestCase
{
    /** @test */
    public function build_path_from_action()
    {
        $this->assertEquals(
            [
                'get' => [
                    'description' => 'test'
                ]
            ],
            OpenApiPathBuilder::make()
                ->fromAction([PostController::class, 'index'], function (OpenApiOperationBuilder $operation) {
                    $operation->description('test');
                })
                ->build()
                ->toArray()
        );
    }
}