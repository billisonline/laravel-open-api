<?php

namespace BYanelli\OpenApiLaravel\Tests\Unit;

use BYanelli\OpenApiLaravel\OpenApiOperation;
use BYanelli\OpenApiLaravel\OpenApiResponse;
use BYanelli\OpenApiLaravel\OpenApiResponseCollection;
use BYanelli\OpenApiLaravel\Tests\TestCase;

class OpenApiOperationTest extends TestCase
{
    /**
     * @test
     * @dataProvider operations()
     */
    public function serialize_operation($params, $result)
    {
        $operation = new OpenApiOperation($params);

        $this->assertEquals($result, $operation->toArray());
    }

    public function operations()
    {
        return [
            'with description' => [
                [
                    'method' => 'post',
                    'description' => 'blah blah',
                ],
                [
                    'description' => 'blah blah',
                ]
            ],
            'with operation id' => [
                [
                    'method' => 'post',
                    'description' => 'blah blah',
                    'operationId' => 'createSomething',
                ],
                [
                    'description' => 'blah blah',
                    'operationId' => 'createSomething',
                ]
            ],
            'with responses' => [
                [
                    'method' => 'get',
                    'description' => 'blah blah',
                    'operationId' => 'createSomething',
                    'responses' => new OpenApiResponseCollection([
                        new OpenApiResponse([
                            'status' => 200,
                            'description' => 'ok response',
                        ]),
                        new OpenApiResponse([
                            'status' => 404,
                            'description' => 'not found response',
                        ]),
                    ])
                ],
                [
                    'description' => 'blah blah',
                    'operationId' => 'createSomething',
                    'responses' => [
                        200 => [
                            'description' => 'ok response',
                        ],
                        404 =>[
                            'description' => 'not found response',
                        ],
                    ]
                ]
            ],
        ];
    }
}
