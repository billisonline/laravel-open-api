<?php

namespace BYanelli\OpenApiLaravel\Tests\Feature;

use BYanelli\OpenApiLaravel\Builders\OpenApiDefinitionBuilder;
use BYanelli\OpenApiLaravel\Builders\OpenApiInfoBuilder;
use BYanelli\OpenApiLaravel\Builders\OpenApiOperationBuilder;
use BYanelli\OpenApiLaravel\Builders\OpenApiPathBuilder;
use BYanelli\OpenApiLaravel\Builders\OpenApiResponseBuilder;
use BYanelli\OpenApiLaravel\Builders\OpenApiSchemaBuilder;
use BYanelli\OpenApiLaravel\OpenApiPath;
use BYanelli\OpenApiLaravel\Support\JsonResource;
use BYanelli\OpenApiLaravel\Support\JsonResourceProperties;
use BYanelli\OpenApiLaravel\Support\ResponseProperties;
use BYanelli\OpenApiLaravel\Tests\Support\AlternateUserResource;
use BYanelli\OpenApiLaravel\Tests\TestApp\app\Http\Controllers\UserController;
use BYanelli\OpenApiLaravel\Tests\TestCase;
use Illuminate\Support\Arr;
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
            JsonResourceProperties::for(AlternateUserResource::class)
                ->setModel(User::class);

            $modelClass = (new JsonResource(AlternateUserResource::class))->modelClass();

            $this->assertEquals(User::class, $modelClass);
        });
    }

    /** @test */
    public function specify_schema_for_response()
    {
        $this->assertDefinitionEquals(
            function () {
                OpenApiInfoBuilder::make()->title('title')->version('version');

                ResponseProperties::for(TokenResponse::class)
                    ->setSchema([
                        'token'     => 'string',
                        'expiresAt' => 'string',
                    ]);

                OpenApiPathBuilder::make()->fromAction([UserController::class, 'authenticate']);
            },
            'paths./api/users/authenticate.post.responses.200.content.application/json.schema',
            [
                'type' => 'object',
                'properties' => [
                    'token'     => ['type' => 'string'],
                    'expiresAt' => ['type' => 'string'],
                ]
            ]
        );
    }

    protected function assertDefinitionEquals(callable $definition, string $path, array $expected)
    {
        $definition = OpenApiDefinitionBuilder::with($definition);

        $result = Arr::get($definition->build()->toArray(), $path);

        $this->assertEquals($expected, $result);
    }

    /** @test */
    public function specify_schema_for_resource()
    {
        $this->assertDefinitionEquals(
            function () {
                OpenApiInfoBuilder::make()->title('title')->version('version');

                JsonResourceProperties::for(AlternateUserResource::class)
                    ->setSchema([
                        'userEmail'     => 'string',
                        'userFirstName' => 'string',
                        'userLastName'  => 'string',
                    ]);

                OpenApiResponseBuilder::make()->fromResource(AlternateUserResource::class);
            },
            'components.schemas.AlternateUserResource',
            [
                'type' => 'object',
                'properties' => [
                    'userEmail'     => ['type' => 'string'],
                    'userFirstName' => ['type' => 'string'],
                    'userLastName'  => ['type' => 'string'],
                ]
            ]
        );
    }
}