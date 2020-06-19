<?php

namespace BYanelli\OpenApiLaravel\Tests\Unit\Objects\Dtos;

use BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiOperationDto;
use BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiParameterDto;
use BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiResponseDto;
use BYanelli\OpenApiLaravel\Tests\TestCase;

class OperationDtoTest extends TestCase
{
    /**
     * @test
     * @dataProvider operations()
     */
    public function serialize_operation($params, $result)
    {
        $operation = new OpenApiOperationDto($params);

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
            'with parameters' => [
                [
                    'method' => 'post',
                    'description' => 'blah blah',
                    'operationId' => 'createSomething',
                    'parameters' => [
                        new OpenApiParameterDto([
                            'in' => 'path',
                            'name' => 'id',
                            'required' => true,
                        ]),
                        new OpenApiParameterDto([
                            'in' => 'query',
                            'name' => 'type',
                        ]),
                    ],
                ],
                [
                    'description' => 'blah blah',
                    'operationId' => 'createSomething',
                    'parameters' => [
                        [
                            'in' => 'path',
                            'name' => 'id',
                            'required' => true,
                        ],
                        [
                            'in' => 'query',
                            'name' => 'type',
                            'required' => false,
                        ],
                    ],
                ]
            ],
            'with responses' => [
                [
                    'method' => 'get',
                    'description' => 'blah blah',
                    'operationId' => 'createSomething',
                    'responses' => [
                        new OpenApiResponseDto([
                            'status' => 200,
                            'description' => 'ok response',
                        ]),
                        new OpenApiResponseDto([
                            'status' => 404,
                            'description' => 'not found response',
                        ]),
                    ],
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
