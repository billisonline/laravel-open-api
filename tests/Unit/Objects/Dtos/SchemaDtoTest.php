<?php

namespace BYanelli\OpenApiLaravel\Tests\Unit\Objects\Dtos;

use BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiNamedSchemaDto;
use BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiSchemaDto;
use BYanelli\OpenApiLaravel\Tests\TestCase;

class SchemaDtoTest extends TestCase
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

        $operation = new OpenApiSchemaDto($params);

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
                    'items' => new OpenApiSchemaDto([
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
                        new OpenApiNamedSchemaDto([
                            'name' => 'id',
                            'type' => 'integer',
                        ]),
                        new OpenApiNamedSchemaDto([
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