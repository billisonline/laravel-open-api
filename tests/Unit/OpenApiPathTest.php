<?php

namespace BYanelli\OpenApiLaravel\Tests\Unit;

use BYanelli\OpenApiLaravel\OpenApiOperation;
use BYanelli\OpenApiLaravel\OpenApiOperationCollection;
use BYanelli\OpenApiLaravel\OpenApiPath;
use BYanelli\OpenApiLaravel\Tests\TestCase;

class OpenApiPathTest extends TestCase
{
    /**
     * @test
     * @dataProvider paths()
     */
    public function serialize_path($params, $result)
    {
        $operation = new OpenApiPath($params);

        $this->assertEquals($result, $operation->toArray());
    }

    public function paths()
    {
        return [
            'default' => [
                [
                    'path' => '/posts',
                    'operations' => [
                        new OpenApiOperation([
                            'method' => 'get',
                            'description' => 'Get all posts'
                        ]),
                        new OpenApiOperation([
                            'method' => 'post',
                            'description' => 'Create post'
                        ]),
                    ],
                ],
                [
                    'get' => [
                        'description' => 'Get all posts',
                    ],
                    'post' => [
                        'description' => 'Create post',
                    ],
                ]
            ],
        ];
    }

}