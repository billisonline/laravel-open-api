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

    /** @test */
    public function build_path_with_parameters()
    {
        $this->assertEquals(
            [
                'get' => [
                    'parameters' => [
                        [
                            'name' => 'post',
                            'in' => 'path',
                            'required' => false, //todo
                            'description' => 'Id of the Post to show'
                        ]
                    ]
                ]
            ],
            OpenApiPathBuilder::make()
                ->fromAction([PostController::class, 'show'])
                ->build()
                ->toArray()
        );
    }
}