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
        $op = OpenApiOperationBuilder::make()->method('get')->addResponse(
            OpenApiResponseBuilder::make()->status(200)->jsonSchema(
                OpenApiSchemaBuilder::make()
                    ->type('object')
                    ->addProperty(
                        OpenApiSchemaBuilder::make()
                            ->name('id')
                            ->type('integer')
                    )
                    ->addProperty(
                        OpenApiSchemaBuilder::make()
                            ->name('name')
                            ->type('string')
                    )
            )
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