<?php

namespace BYanelli\OpenApiLaravel\Tests\Unit\Objects\Dtos;

use BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiOperationDto;
use BYanelli\OpenApiLaravel\OpenApiOperationCollection;
use BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiPathDto;
use BYanelli\OpenApiLaravel\Tests\TestCase;

class PathDtoTest extends TestCase
{
    /**
     * @test
     * @dataProvider paths()
     */
    public function serialize_path($params, $result)
    {
        $operation = new OpenApiPathDto($params);

        $this->assertEquals($result, $operation->toArray());
    }

    public function paths()
    {
        return [
            'default' => [
                [
                    'path' => '/posts',
                    'operations' => [
                        new OpenApiOperationDto([
                            'method' => 'get',
                            'description' => 'Get all posts'
                        ]),
                        new OpenApiOperationDto([
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