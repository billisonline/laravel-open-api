<?php

namespace BYanelli\OpenApiLaravel\Tests\Feature;

use BYanelli\OpenApiLaravel\Builders\OpenApiSchemaBuilder;
use BYanelli\OpenApiLaravel\Tests\TestCase;

class SchemaBuilderTest extends TestCase
{
    /** @test */
    public function build_object_schema()
    {
        $schema = OpenApiSchemaBuilder::make()->object([
            'id'        => 'integer',
            'name'      => 'string',
            'tags[]'    => 'string',
            'others[]'  => [
                'id'    => 'integer',
                'name'  => 'string',
            ]
        ]);

        $this->assertEquals(
            [
                'type' => 'object',
                'properties' => [
                    'id' => ['type' => 'integer'],
                    'name' => ['type' => 'string'],
                    'tags' => [
                        'type' => 'array',
                        'items' => [
                            'type' => 'string',
                        ]
                    ],
                    'others' => [
                        'type' => 'array',
                        'items' => [
                            'type' => 'object',
                            'properties' => [
                                'id' => ['type' => 'integer'],
                                'name' => ['type' => 'string'],
                            ]
                        ]
                    ],
                ]
            ],
            $schema->build()->toArray()
        );
    }
}