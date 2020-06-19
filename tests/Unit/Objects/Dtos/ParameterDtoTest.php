<?php

namespace BYanelli\OpenApiLaravel\Tests\Unit\Objects\Dtos;

use BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiParameterDto;
use BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiSchemaDto;
use BYanelli\OpenApiLaravel\Tests\TestCase;

class ParameterDtoTest extends TestCase
{
    /**
     * @test
     * @dataProvider parameters()
     */
    public function serialize_parameter($params, $result)
    {
        $operation = new OpenApiParameterDto($params);

        $this->assertEquals($result, $operation->toArray());
    }

    /** @test */
    public function cannot_serialize_parameter_with_invalid_location()
    {
        $this->expectException(\Exception::class);

        $operation = new OpenApiParameterDto(['name' => 'test', 'in' => 'somewhere invalid']);

        $operation->toArray();
    }

    public function parameters()
    {
        return [
            'default' => [
                [
                    'name' => 'id',
                    'in' => 'query',
                ],
                [
                    'name' => 'id',
                    'in' => 'query',
                    'required' => false,
                ]
            ],
            'with description' => [
                [
                    'name' => 'id',
                    'in' => 'path',
                    'required' => true,
                    'description' => 'blah blah',
                ],
                [
                    'name' => 'id',
                    'in' => 'path',
                    'required' => true,
                    'description' => 'blah blah',
                ]
            ],
            'with schema' => [
                [
                    'name' => 'id',
                    'in' => 'path',
                    'required' => true,
                    'schema' => new OpenApiSchemaDto([
                        'type' => 'integer',
                        'minimum' => 1
                    ]),
                ],
                [
                    'name' => 'id',
                    'in' => 'path',
                    'required' => true,
                    'schema' => [
                        'type' => 'integer',
                        'minimum' => 1
                    ],
                ]
            ],
        ];
    }

}