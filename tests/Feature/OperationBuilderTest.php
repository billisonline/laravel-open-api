<?php

namespace BYanelli\OpenApiLaravel\Tests\Feature;

use BYanelli\OpenApiLaravel\Builders\OpenApiOperationBuilder;
use BYanelli\OpenApiLaravel\Builders\OpenApiResponseBuilder;
use BYanelli\OpenApiLaravel\Builders\OpenApiSchemaBuilder;
use BYanelli\OpenApiLaravel\Tests\TestCase;
use TestApp\Http\Resources\Post as PostResource;
use TestApp\Post;

class OperationBuilderTest extends TestCase
{
    /** @test */
    public function query()
    {
        $op = OpenApiOperationBuilder::make()
            ->method('get')
            ->query([
                'foo' => 'string',
                'bar' => 'boolean',
            ]);

        $this->assertEquals(
            [
                'parameters' => [
                    [
                        'name' => 'foo',
                        'in' => 'query',
                        'required' => false,
                        'schema' => ['type' => 'string'],
                    ],
                    [
                        'name' => 'bar',
                        'in' => 'query',
                        'required' => false,
                        'schema' => ['type' => 'boolean'],
                    ],
                ]
            ],
            $op->build()->toArray()
        );
    }

    /** @test */
    public function zzz()
    {
        $op = OpenApiOperationBuilder::make()
            ->method('get')
            ->successResponse([
                'id' => 'integer',
                'name' => 'string',
            ]);

        $this->assertEquals(
            [
                'responses' => [
                    200 => [
                        'description' => 'Success',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'id' => ['type' => 'integer'],
                                        'name' => ['type' => 'string'],
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            $op->build()->toArray()
        );
    }
    /** @test */
    public function empty()
    {
        $op = OpenApiOperationBuilder::make()
            ->method('get')
            ->emptyResponse();

        $this->assertEquals(
            [
                'responses' => [
                    200 => [
                        'description' => 'Success',
                    ]
                ]
            ],
            $op->build()->toArray()
        );
    }

    /** @test */
    public function qqq()
    {
        $op = OpenApiOperationBuilder::make()->method('get')->successResponse(
            OpenApiResponseBuilder::make()->fromResource(PostResource::class)
        );

        $this->assertEquals(
            [
                'responses' => [
                    200 => [
                        'description' => 'Success',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'id' => ['type' => 'integer'],
                                        'body' => [
                                            'type' => 'string',
                                            'nullable' => true,
                                        ],
                                        'headlineSlug' => ['type' => 'string'],
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            $op->build()->toArray()
        );
    }

    /** @test */
    public function plural()
    {
        $op = OpenApiOperationBuilder::make()->method('get')->successResponse(
            OpenApiResponseBuilder::make()->fromResource(PostResource::class)->plural()
        );

        $this->assertEquals(
            [
                'responses' => [
                    200 => [
                        'description' => 'Success',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'array',
                                    'items' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'id' => ['type' => 'integer'],
                                            'body' => [
                                                'type' => 'string',
                                                'nullable' => true,
                                            ],
                                            'headlineSlug' => ['type' => 'string'],
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            $op->build()->toArray()
        );
    }
}