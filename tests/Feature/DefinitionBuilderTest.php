<?php

namespace BYanelli\OpenApiLaravel\Tests\Feature;

use BYanelli\OpenApiLaravel\Builders\OpenApiDefinitionBuilder;
use BYanelli\OpenApiLaravel\Builders\OpenApiInfoBuilder;
use BYanelli\OpenApiLaravel\Builders\OpenApiPathBuilder;
use BYanelli\OpenApiLaravel\Builders\OpenApiSchemaBuilder;
use BYanelli\OpenApiLaravel\OpenApiPath;
use BYanelli\OpenApiLaravel\Support\JsonResource;
use BYanelli\OpenApiLaravel\Tests\Support\AlternateUserResource;
use BYanelli\OpenApiLaravel\Tests\TestApp\app\Http\Controllers\UserController;
use BYanelli\OpenApiLaravel\Tests\TestCase;
use TestApp\Http\Controllers\PostController;
use TestApp\Http\Responses\TokenResponse;
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

    /** @test */
    public function specify_schema_for_response()
    {
        OpenApiDefinitionBuilder::with(function () {
            OpenApiDefinitionBuilder::getCurrent()
                ->registerResponseSchema(
                    TokenResponse::class,
                    OpenApiSchemaBuilder::make()->object([
                        'token'     => 'string',
                        'expiresAt' => 'string',
                    ])
                );

            $path = OpenApiPathBuilder::make()->fromActionName([UserController::class, 'authenticate']);

            $this->assertEquals([
                'post' => [
                    'operationId' => 'authenticateUser',
                    'responses' => [
                        200  => [
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'properties' => [
                                            'token' => ['type' => 'string'],
                                            'expiresAt' => ['type' => 'string'],
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ], $path->build()->toArray());
        });
    }
}