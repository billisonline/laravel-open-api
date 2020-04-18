<?php

namespace BYanelli\OpenApiLaravel\Tests\Unit;

use BYanelli\OpenApiLaravel\OpenApiExternalDocs;
use BYanelli\OpenApiLaravel\OpenApiTag;
use BYanelli\OpenApiLaravel\Tests\TestCase;

class OpenApiTagTest extends TestCase
{
    /**
     * @test
     * @dataProvider tags()
     */
    public function serialize_tag($params, $result)
    {
        $operation = new OpenApiTag($params);

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
                    'externalDocs' => new OpenApiExternalDocs([
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