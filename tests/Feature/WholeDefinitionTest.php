<?php

namespace BYanelli\OpenApiLaravel\Tests\Feature;

use BYanelli\OpenApiLaravel\Builders\OpenApiDefinitionBuilder;
use BYanelli\OpenApiLaravel\Builders\OpenApiInfoBuilder;
use BYanelli\OpenApiLaravel\Builders\OpenApiOperationBuilder;
use BYanelli\OpenApiLaravel\Builders\OpenApiPathBuilder;
use BYanelli\OpenApiLaravel\Tests\TestCase;

class WholeDefinitionTest extends TestCase
{
    /** @test */
    public function build_whole_definition()
    {
        $def = OpenApiDefinitionBuilder::with(function () {
            OpenApiInfoBuilder::make()->title('test')->version('1.0');

            OpenApiPathBuilder::make()->path('/api/posts')->addOperation(
                OpenApiOperationBuilder::make()->method('get')->operationId('indexPosts')
            );
            OpenApiPathBuilder::make()->path('/api/posts')->addOperation(
                OpenApiOperationBuilder::make()->method('post')->operationId('storePost')
            );
        });

        $this->assertEquals(
            [
                'openapi' => '3.0.0',
                'paths' =>  [
                    '/api/posts' =>  [
                        'get' =>  [
                            'operationId' => 'indexPosts',
                        ],
                        'post' =>  [
                            'operationId' => 'storePost',
                        ],
                    ],
                ],
                'info' =>  [
                    'title' => 'test',
                    'version' => '1.0',
                ],
            ],
            $def->build()->toArray()
        );
    }
}