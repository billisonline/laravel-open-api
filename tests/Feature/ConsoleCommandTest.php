<?php

namespace BYanelli\OpenApiLaravel\Tests\Feature;

use BYanelli\OpenApiLaravel\Tests\TestCase;

class ConsoleCommandTest extends TestCase
{
    /** @test */
    public function output_definition_from_console_command()
    {
        $this->artisan('openapi:spec')->test->setOutputCallback(function (string $output) {
            $output = json_decode($output, true);

            $this->assertEquals(
                [
                    'openapi' => '3.0.0',
                    'paths' =>  [
                        '/api/posts' =>  [
                            'get' =>  [
                                'operationId' => 'postIndex',
                                'tags' => ['Post'],
                                'description' => 'List posts',
                            ],
                            'post' =>  [
                                'operationId' => 'postStore',
                                'requestBody' => [
                                    'content' => [
                                        'application/json' => [
                                            'schema' => [
                                                '$ref' => '#/components/schemas/PostStoreRequest'
                                            ]
                                        ]
                                    ]
                                ],
                                'tags' => ['Post'],
                                'description' => 'Create post',
                                'security' => [
                                    ['BearerAuth' => [],],
                                ],
                            ],
                        ],
                        '/api/posts/{post}' => [
                            'get' => [
                                'operationId' => 'postShow',
                                'responses' => [
                                    '200' => [
                                        'content' => [
                                            'application/json' => [
                                                'schema' => [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'data' => [
                                                            '$ref' => '#/components/schemas/Post',
                                                        ]
                                                    ]
                                                ]
                                            ]
                                        ],
                                        'description' => 'Success',
                                    ]
                                ],
                                'parameters' => [
                                    [
                                        'name' => 'post',
                                        'in' => 'path',
                                        'description' => 'Id of the Post to show',
                                        'required' => false,
                                    ]
                                ],
                                'tags' => ['Post'],
                                'description' => 'Show post',
                            ]
                        ]
                    ],
                    'info' =>  [
                        'title' => 'test',
                        'version' => '1.0',
                    ],
                    'tags' => [
                        [
                            'name' => 'Post',
                            'description' => 'Manage posts',
                        ],
                    ],
                    'components' => [
                        'schemas' => [
                            'PostStoreRequest' => [
                                'type' => 'object',
                                'properties' => [
                                    'title' => ['type' => 'string'],
                                    'body' => ['type' => 'string'],
                                ],
                                'title' => 'PostStoreRequest',
                            ],
                            'Post' => [
                                'title' => 'Post',
                                'type' => 'object',
                                'properties' => [
                                    'id' => [
                                        'type' => 'integer',
                                    ],
                                    'body' => [
                                        'type' => 'string',
                                        'nullable' => true,
                                    ],
                                    'headlineSlug' => [
                                        'type' => 'string',
                                        'description' => 'The URL slug for the post\'s headline',
                                    ],
                                ],
                            ]
                        ],
                        'securitySchemes' => [
                            'BearerAuth' => [
                                'type' => 'http',
                                'scheme' => 'bearer',
                            ],
                        ],
                    ]
                ],
                $output
            );
        });
    }
}