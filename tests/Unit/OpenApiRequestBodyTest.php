<?php

namespace BYanelli\OpenApiLaravel\Tests\Unit;

use BYanelli\OpenApiLaravel\OpenApiNamedSchema;
use BYanelli\OpenApiLaravel\OpenApiRequestBody;
use BYanelli\OpenApiLaravel\OpenApiSchema;
use BYanelli\OpenApiLaravel\Tests\TestCase;

class OpenApiRequestBodyTest extends TestCase
{
    /**
     * @test
     * @dataProvider requestBodies()
     */
    public function serialize_request_body($params, $result)
    {
        $requestBody = new OpenApiRequestBody($params);

        $this->assertEquals($result, $requestBody->toArray());
    }

    public function requestBodies()
    {
        return [
            'with schema' => [
                [
                    'jsonSchema' => new OpenApiSchema([
                        'type' => 'object',
                        'properties' => [
                            new OpenApiNamedSchema([
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