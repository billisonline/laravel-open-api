<?php

namespace BYanelli\OpenApiLaravel\Tests\Unit;

use BYanelli\OpenApiLaravel\OpenApiDefinition;
use BYanelli\OpenApiLaravel\OpenApiOperation;
use BYanelli\OpenApiLaravel\OpenApiPath;
use BYanelli\OpenApiLaravel\Tests\TestCase;

class OpenApiDefinitionTest extends TestCase
{
    /**
     * @test
     * @dataProvider definitions()
     */
    public function serialize_definition($params, $result)
    {
        $operation = new OpenApiDefinition($params);

        $this->assertEquals($result, $operation->toArray());
    }

    public function definitions()
    {
        return [
            'default' => [
                [
                    'paths' => [
                        new OpenApiPath([
                            'path' => '/posts',
                            'operations' => [
                                new OpenApiOperation([
                                    'method' => 'get',
                                    'description' => 'Get posts'
                                ]),
                                new OpenApiOperation([
                                    'method' => 'post',
                                    'description' => 'Create post',
                                ]),
                            ],
                        ])
                    ]
                ],
                [
                    'openapi' => '3.0.0',
                    'paths' => [
                        '/posts' => [
                            'get' => [
                                'description' => 'Get posts',
                            ],
                            'post' => [
                                'description' => 'Create post',
                            ],
                        ]
                    ]
                ]
            ]
        ];
    }


}