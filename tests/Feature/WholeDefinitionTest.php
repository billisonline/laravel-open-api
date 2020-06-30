<?php

namespace BYanelli\OpenApiLaravel\Tests\Feature;

use BYanelli\OpenApiLaravel\Objects\OpenApiDefinition;
use BYanelli\OpenApiLaravel\Objects\OpenApiInfo;
use BYanelli\OpenApiLaravel\Objects\OpenApiOperation;
use BYanelli\OpenApiLaravel\Objects\OpenApiPath;
use BYanelli\OpenApiLaravel\Tests\TestCase;

class WholeDefinitionTest extends TestCase
{
    /** @test */
    public function build_whole_definition()
    {
        $def = OpenApiDefinition::with(function () {
            OpenApiInfo::make()->title('test')->version('1.0');

            OpenApiPath::make()->path('/api/posts')
                ->addOperation(OpenApiOperation::make()->method('get')->operationId('indexPosts'))
                ->addOperation(OpenApiOperation::make()->method('post')->operationId('storePost'));
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