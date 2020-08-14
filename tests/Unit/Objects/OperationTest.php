<?php

namespace BYanelli\OpenApiLaravel\Tests\Unit\Objects;

use BYanelli\OpenApiLaravel\Objects\OpenApiOperation;
use BYanelli\OpenApiLaravel\Objects\OpenApiResponse;
use BYanelli\OpenApiLaravel\Tests\TestCase;
use TestApp\Http\Resources\Post as PostResource;

class OperationTest extends TestCase
{
    /** @test */
    public function query()
    {
        $op = OpenApiOperation::make()
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
        $op = OpenApiOperation::make()
            ->method('get')
            ->response([
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
        $op = OpenApiOperation::make()
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
        $op = OpenApiOperation::make()->method('get')->response(
            OpenApiResponse::make()->fromResource(PostResource::class)
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
                                        'headlineSlug' => [
                                            'type' => 'string',
                                            'description' => 'The URL slug for the post\'s headline',
                                        ],
                                        'author' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'id' => ['type' => 'integer'],
                                                'posts' => [
                                                    'type' => 'array',
                                                    'items' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'id' => ['type' => 'integer'],
                                                            'body' => [
                                                                'type' => 'string',
                                                                'nullable' => true,
                                                            ],
                                                            'headlineSlug' => [
                                                                'type' => 'string',
                                                                'description' => 'The URL slug for the post\'s headline',
                                                            ],
                                                            'author' => [
                                                                '$ref' => '#/components/schemas/whatever', //todo
                                                            ],
                                                        ],
                                                        'description' => 'A blog post.'
                                                    ]
                                                ],
                                            ],
                                        ],
                                    ],
                                    'description' => 'A blog post.',
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
        $op = OpenApiOperation::make()->method('get')->response(
            OpenApiResponse::make()->fromResource(PostResource::class)->plural()
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
                                            'headlineSlug' => [
                                                'type' => 'string',
                                                'description' => 'The URL slug for the post\'s headline',
                                            ],
                                            'author' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'id' => ['type' => 'integer'],
                                                    'posts' => [
                                                        'type' => 'array',
                                                        'items' => [
                                                            'type' => 'object',
                                                            'properties' => [
                                                                'id' => ['type' => 'integer'],
                                                                'body' => [
                                                                    'type' => 'string',
                                                                    'nullable' => true,
                                                                ],
                                                                'headlineSlug' => [
                                                                    'type' => 'string',
                                                                    'description' => 'The URL slug for the post\'s headline',
                                                                ],
                                                                'author' => [
                                                                    '$ref' => '#/components/schemas/whatever', //todo
                                                                ],
                                                            ],
                                                            'description' => 'A blog post.'
                                                        ]
                                                    ],
                                                ],
                                            ],
                                        ],
                                        'description' => 'A blog post.',
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