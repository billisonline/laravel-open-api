<?php

namespace BYanelli\OpenApiLaravel\Tests\Unit;

use BYanelli\OpenApiLaravel\OpenApiResponse;
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
            ]
        ];
    }

}