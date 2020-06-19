<?php

namespace BYanelli\OpenApiLaravel\Tests\Unit\Objects\Dtos;

use BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiExternalDocsDto;
use BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiTagDto;
use BYanelli\OpenApiLaravel\Tests\TestCase;

class TagDtoTest extends TestCase
{
    /**
     * @test
     * @dataProvider tags()
     */
    public function serialize_tag($params, $result)
    {
        $operation = new OpenApiTagDto($params);

        $this->assertEquals($result, $operation->toArray());
    }

    public function tags()
    {
        return [
            'default' => [
                [
                    'name' => 'posts',
                    'description' => 'Post-related stuff'
                ],
                [
                    'name' => 'posts',
                    'description' => 'Post-related stuff'
                ],
            ],
            'with external docs' => [
                [
                    'name' => 'posts',
                    'description' => 'Post-related stuff',
                    'externalDocs' => new OpenApiExternalDocsDto([
                        'description' => 'Our Docs',
                        'url' => 'https://our.website/docs'
                    ])
                ],
                [
                    'name' => 'posts',
                    'description' => 'Post-related stuff',
                    'externalDocs' => [
                        'description' => 'Our Docs',
                        'url' => 'https://our.website/docs'
                    ]
                ],
            ],
        ];
    }

}