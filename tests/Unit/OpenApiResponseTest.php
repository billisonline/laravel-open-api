<?php

namespace BYanelli\OpenApiLaravel\Tests\Unit;

use BYanelli\OpenApiLaravel\OpenApiNamedSchema;
use BYanelli\OpenApiLaravel\OpenApiResponse;
use BYanelli\OpenApiLaravel\OpenApiSchema;
use BYanelli\OpenApiLaravel\Tests\TestCase;

class OpenApiResponseTest extends TestCase
{
    /**
     * @test
     * @dataProvider responses()
     */
    public function serialize_response($params, $result)
    {
        $operation = new OpenApiResponse($params);

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
                    'jsonSchema' => new OpenApiSchema([
                        'type' => 'object',
                        'properties' => [
                            new OpenApiNamedSchema([
                                'name' => 'id',
                                'type' => 'integer',
                            ]),
                            new OpenApiNamedSchema([
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