<?php

namespace BYanelli\OpenApiLaravel\Tests\Feature;

use BYanelli\OpenApiLaravel\Builders\OpenApiDefinitionBuilder;
use BYanelli\OpenApiLaravel\Builders\OpenApiInfoBuilder;
use BYanelli\OpenApiLaravel\Builders\OpenApiPathBuilder;
use BYanelli\OpenApiLaravel\Support\JsonResource;
use BYanelli\OpenApiLaravel\Tests\Support\AlternateUserResource;
use BYanelli\OpenApiLaravel\Tests\TestCase;
use TestApp\User;

class DefinitionBuilderTest extends TestCase
{
    /** @test */
    public function build_definition()
    {
        $def = (
            OpenApiDefinitionBuilder::make()
                ->info(
                    OpenApiInfoBuilder::make()
                        ->title('Test')
                        ->version('1.0')
                )
                ->addPath(OpenApiPathBuilder::make()->path('/api/users'))
                ->addPath(OpenApiPathBuilder::make()->path('/api/posts'))
        );

        $this->assertEquals(
            [
                'openapi' => '3.0.0',
                'paths' => [
                    '/api/users' => [],
                    '/api/posts' => [],
                ],
                'info' => [
                    'title' => 'Test',
                    'version' => '1.0',
                ]
            ],
            $def->build()->toArray()
        );
    }

    /** @test */
    public function specify_model_for_resource()
    {
        OpenApiDefinitionBuilder::with(function () {
            OpenApiDefinitionBuilder::getCurrent()
                ->registerResourceModel(AlternateUserResource::class, User::class);

            $modelClass = (new JsonResource(AlternateUserResource::class))->modelClass();

            $this->assertEquals(User::class, $modelClass);
        });
    }
}