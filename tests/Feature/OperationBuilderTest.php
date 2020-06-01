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
    public function zzz()
    {
        $op = OpenApiOperationBuilder::make()
            ->method('get')
            ->addResponse([
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
    public function qqq()
    {
        $op = OpenApiOperationBuilder::make()->method('get')->addResponse(
            OpenApiResponseBuilder::make()->fromResource(PostResource::class, Post::class)
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
}