<?php

namespace BYanelli\OpenApiLaravel\Tests\Unit;

use BYanelli\OpenApiLaravel\OpenApiOperation;
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
        ];
    }
}
