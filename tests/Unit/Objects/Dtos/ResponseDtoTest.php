<?php

namespace BYanelli\OpenApiLaravel\Tests\Unit\Objects\Dtos;

use BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiNamedSchemaDto;
use BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiResponseDto;
use BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiSchemaDto;
use BYanelli\OpenApiLaravel\Tests\TestCase;

class ResponseDtoTest extends TestCase
{
    /**
     * @test
     * @dataProvider responses()
     */
    public function serialize_response($params, $result)
    {
        $operation = new OpenApiResponseDto($params);

        $this->assertEquals($result, $operation->toArray());
    }

    public function responses()
    {
        return [
            'default' => [
                [
                    'status' => 200,
                ],
                []
            ],
            'with description' => [
                [
                    'status' => 200,
                    'description' => 'OK',
                ],
                [
                    'description' => 'OK',
                ]
            ],
            'with schema' => [
                [
                    'status' => 200,
                    'description' => 'OK',
                    'jsonSchema' => new OpenApiSchemaDto([
                        'type' => 'object',
                        'properties' => [
                            new OpenApiNamedSchemaDto([
                                'name' => 'id',
                                'type' => 'integer',
                            ]),
                            new OpenApiNamedSchemaDto([
                                'name' => 'title',
                                'type' => 'string',
                            ]),
                        ]
                    ])
                ],
                [
                    'description' => 'OK',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'id' => [
                                        'type' => 'integer'
                                    ],
                                    'title' => [
                                        'type' => 'string'
                                    ],
                                ],
                            ]
                        ]
                    ]
                ]
            ],
        ];
    }

}