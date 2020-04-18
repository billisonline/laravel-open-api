<?php

namespace BYanelli\OpenApiLaravel\Tests\Unit;

use BYanelli\OpenApiLaravel\OpenApiParameter;
use BYanelli\OpenApiLaravel\Tests\TestCase;

class OpenApiParameterTest extends TestCase
{
    /**
     * @test
     * @dataProvider parameters()
     */
    public function serialize_parameter($params, $result)
    {
        $operation = new OpenApiParameter($params);

        $this->assertEquals($result, $operation->toArray());
    }

    /** @test */
    public function cannot_serialize_parameter_with_invalid_location()
    {
        $this->expectException(\Exception::class);

        $operation = new OpenApiParameter(['name' => 'test', 'in' => 'somewhere invalid']);

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
        ];
    }

}