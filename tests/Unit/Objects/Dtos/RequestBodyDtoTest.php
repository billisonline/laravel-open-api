<?php

namespace BYanelli\OpenApiLaravel\Tests\Unit\Objects\Dtos;

use BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiNamedSchemaDto;
use BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiRequestBodyDto;
use BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiSchemaDto;
use BYanelli\OpenApiLaravel\Tests\TestCase;

class RequestBodyDtoTest extends TestCase
{
    /**
     * @test
     * @dataProvider requestBodies()
     */
    public function serialize_request_body($params, $result)
    {
        $requestBody = new OpenApiRequestBodyDto($params);

        $this->assertEquals($result, $requestBody->toArray());
    }

    public function requestBodies()
    {
        return [
            'with schema' => [
                [
                    'jsonSchema' => new OpenApiSchemaDto([
                        'type' => 'object',
                        'properties' => [
                            new OpenApiNamedSchemaDto([
                                'name' => 'key',
                                'type' => 'string'
                            ])
                        ]
                    ]),
                ],
                [
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'key' => ['type' => 'string']
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ];
    }

}