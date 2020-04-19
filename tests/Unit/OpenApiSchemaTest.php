<?php

namespace BYanelli\OpenApiLaravel\Tests\Unit;

use BYanelli\OpenApiLaravel\OpenApiNamedSchema;
use BYanelli\OpenApiLaravel\OpenApiSchema;
use BYanelli\OpenApiLaravel\Tests\TestCase;

class OpenApiSchemaTest extends TestCase
{
    /**
     * @test
     * @dataProvider schemas()
     */
    public function serialize_schema($params, $result)
    {
        if ($result == self::IDENTICAL_RESULT) {
            $result = $params;
        }

        $operation = new OpenApiSchema($params);

        $this->assertEquals($result, $operation->toArray());
    }

    public function schemas()
    {
        return [
            'integer' => [
                [
                    'type' => 'integer',
                    'minimum' => 1,
                    'maximum' => 10,
                ],
                self::IDENTICAL_RESULT,
            ],
            'string' => [
                [
                    'type' => 'string',
                    'minLength' => 1,
                    'maxLength' => 10,
                    'pattern' => '/[A-Za-z0-9]+/'
                ],
                self::IDENTICAL_RESULT,
            ],
            'array' => [
                [
                    'type' => 'array',
                    'items' => new OpenApiSchema([
                        'type' => 'string',
                    ]),
                ],
                [
                    'type' => 'array',
                    'items' => [
                        'type' => 'string',
                    ],
                ],
            ],
            'object' => [
                [
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
                    ],
                ],
                [
                    'type' => 'object',
                    'properties' => [
                        'id' => [
                            'type' => 'integer'
                        ],
                        'title' => [
                            'type' => 'string'
                        ],
                    ],
                ],
            ],
        ];
    }


}