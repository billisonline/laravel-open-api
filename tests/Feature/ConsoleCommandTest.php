<?php

namespace BYanelli\OpenApiLaravel\Tests\Feature;

use BYanelli\OpenApiLaravel\Tests\TestCase;

class ConsoleCommandTest extends TestCase
{
    /** @test */
    public function output_definition_from_console_command()
    {
        $this->artisan('openapi:generate')->test->setOutputCallback(function (string $output) {
            $output = json_decode($output, true);

            $this->assertEquals(
                [
                    'openapi' => '3.0.0',
                    'paths' =>  [
                        '/api/posts' =>  [
                            'get' =>  [
                                'operationId' => 'indexPosts',
                            ],
                            'post' =>  [
                                'operationId' => 'storePost',
                            ],
                        ],
                        '/api/posts/{post}' => [
                            'get' => [
                                'operationId' => 'showPost',
                                'responses' => [
                                    '200' => [
                                        'content' => [
                                            'application/json' => [
                                                'schema' => [
                                                    '$ref' => '#/components/schemas/resources/Post'
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                'parameters' => [
                                    [
                                        'name' => 'post',
                                        'in' => 'path',
                                        'description' => 'Id of the Post to show',
                                        'required' => false,
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'info' =>  [
                        'title' => 'test',
                        'version' => '1.0',
                    ],
                    'components' => [
                        'schemas' => [
                            'resources' => [
                                'Post' => [
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
                                        ],
                                    ],
                                ]
                            ]
                        ]
                    ]
                ],
                $output
            );
        });
    }
}