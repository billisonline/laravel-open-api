<?php

namespace BYanelli\OpenApiLaravel\Tests\Unit\Objects\Dtos;

use BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiDefinitionDto;
use BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiInfoDto;
use BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiOperationDto;
use BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiPathDto;
use BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiTagDto;
use BYanelli\OpenApiLaravel\Tests\TestCase;

class DefinitionDtoTest extends TestCase
{
    /**
     * @test
     * @dataProvider definitions()
     */
    public function serialize_definition($params, $result, $autoCollectTags)
    {
        $operation = new OpenApiDefinitionDto($params);

        if ($autoCollectTags) {
            $operation->autoCollectTags();
        }

        $this->assertEquals($result, $operation->toArray());
    }

    public function definitions()
    {
        return [
            'default' => [
                [
                    'info' => new OpenApiInfoDto([
                        'title'     => 'Test API',
                        'version'   => '0.1',
                    ]),
                    'tags' => [
                        $postsTag = new OpenApiTagDto([
                            'name' => 'posts',
                            'description' => 'All about posts',
                        ])
                    ],
                    'paths' => [
                        new OpenApiPathDto([
                            'path' => '/posts',
                            'operations' => [
                                new OpenApiOperationDto([
                                    'method'        => 'get',
                                    'description'   => 'Get posts',
                                    'tags'          => [$postsTag],
                                ]),
                                new OpenApiOperationDto([
                                    'method'        => 'post',
                                    'description'   => 'Create post',
                                    'tags'          => [$postsTag],
                                ]),
                            ],
                        ])
                    ],
                ],
                [
                    'openapi' => '3.0.0',
                    'info' => [
                        'title'     => 'Test API',
                        'version'   => '0.1',
                    ],
                    'tags' => [
                        [
                            'name' => 'posts',
                            'description' => 'All about posts',
                        ],
                    ],
                    'paths' => [
                        '/posts' => [
                            'get' => [
                                'tags'          => ['posts'],
                                'description'   => 'Get posts',
                            ],
                            'post' => [
                                'tags'          => ['posts'],
                                'description'   => 'Create post',
                            ],
                        ]
                    ]
                ],
                false,
            ],
            'with auto collected tags' => [
                [
                    'info' => new OpenApiInfoDto([
                        'title'     => 'Test API',
                        'version'   => '0.1',
                    ]),
                    'paths' => [
                        new OpenApiPathDto([
                            'path' => '/posts',
                            'operations' => [
                                new OpenApiOperationDto([
                                    'method'        => 'get',
                                    'description'   => 'Get posts',
                                    'tags'          => [$postsTag],
                                ]),
                                new OpenApiOperationDto([
                                    'method'        => 'post',
                                    'description'   => 'Create post',
                                    'tags'          => [$postsTag],
                                ]),
                            ],
                        ])
                    ]
                ],
                [
                    'openapi' => '3.0.0',
                    'info' => [
                        'title'     => 'Test API',
                        'version'   => '0.1',
                    ],
                    'tags' => [
                        [
                            'name' => 'posts',
                            'description' => 'All about posts',
                        ],
                    ],
                    'paths' => [
                        '/posts' => [
                            'get' => [
                                'tags'          => ['posts'],
                                'description'   => 'Get posts',
                            ],
                            'post' => [
                                'tags'          => ['posts'],
                                'description'   => 'Create post',
                            ],
                        ]
                    ]
                ],
                true,
            ],
        ];
    }


}