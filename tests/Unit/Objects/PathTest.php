<?php

namespace BYanelli\OpenApiLaravel\Tests\Unit\Objects;

use BYanelli\OpenApiLaravel\Objects\OpenApiOperation;
use BYanelli\OpenApiLaravel\Objects\OpenApiPath;
use BYanelli\OpenApiLaravel\Tests\TestCase;
use TestApp\Http\Controllers\PostController;

class PathTest extends TestCase
{
    /** @test */
    public function build_path_from_action()
    {
        $this->assertEquals(
            [
                'get' => [
                    'operationId' => 'postIndex',
                    'description' => 'test',
                ]
            ],
            OpenApiPath::fromAction([PostController::class, 'index'])
                ->addOperation(
                    OpenApiOperation::fromAction([PostController::class, 'index'])
                        ->description('test')
                )
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
                    'operationId' => 'postShow',
                    'parameters' => [
                        [
                            'name' => 'post',
                            'in' => 'path',
                            'required' => true,
                            'description' => 'Id of the Post to show',
                            'schema' => ['type' => 'string'],
                        ]
                    ],
                    'description' => 'Show post',
                ]
            ],
            OpenApiPath::make()
                ->action([PostController::class, 'show'])
                ->addOperation(OpenApiOperation::fromAction([PostController::class, 'show']))
                ->build()
                ->toArray()
        );
    }
}