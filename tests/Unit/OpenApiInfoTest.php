<?php

namespace BYanelli\OpenApiLaravel\Tests\Unit;

use BYanelli\OpenApiLaravel\OpenApiInfo;
use BYanelli\OpenApiLaravel\Tests\TestCase;

class OpenApiInfoTest extends TestCase
{
    /**
     * @test
     * @dataProvider info()
     */
    public function serialize_info($params, $result)
    {
        $operation = new OpenApiInfo($params);

        $this->assertEquals($result, $operation->toArray());
    }

    public function info()
    {
        return [
            'default' => [
                [
                    'title' => 'Test API',
                    'version' => '0.1',
                ],
                [
                    'title' => 'Test API',
                    'version' => '0.1',
                ]
            ],
            'with terms and description' => [
                [
                    'title'             => 'Test API',
                    'version'           => '0.1',
                    'termsOfService'    => 'https://our.website/terms',
                    'description'       => 'zzz',
                ],
                [
                    'title'             => 'Test API',
                    'version'           => '0.1',
                    'termsOfService'    => 'https://our.website/terms',
                    'description'       => 'zzz',
                ]
            ],
            'with contact' => [
                [
                    'title'         => 'Test API',
                    'version'       => '0.1',
                    'contactName'   => 'Dev',
                    'contactEmail'  => 'dev@our.website',
                    'contactUrl'    => 'http://our.website/support',
                ],
                [
                    'title'     => 'Test API',
                    'version'   => '0.1',
                    'contact'   => [
                        'name'  => 'Dev',
                        'email' => 'dev@our.website',
                        'url'   => 'http://our.website/support',
                    ],
                ]
            ],
            'with external docs' => [
                [
                    'title'     => 'Test API',
                    'version'   => '0.1',
                    'externalDocs'   => [
                        'description'  => 'Our Docs',
                        'url'   => 'http://our.website/docs',
                    ],
                ],
                [
                    'title'     => 'Test API',
                    'version'   => '0.1',
                    'externalDocs'   => [
                        'description'  => 'Our Docs',
                        'url'   => 'http://our.website/docs',
                    ],
                ]
            ],
            'with license' => [
                [
                    'title'         => 'Test API',
                    'version'       => '0.1',
                    'licenseName'   => 'MIT',
                    'licenseUrl'    => 'https://mit.edu/license',
                ],
                [
                    'title'         => 'Test API',
                    'version'       => '0.1',
                    'license'  => [
                        'name'   => 'MIT',
                        'url'           => 'https://mit.edu/license',
                    ],
                ]
            ],
        ];
    }


}