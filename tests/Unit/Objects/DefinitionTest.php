<?php

namespace BYanelli\OpenApiLaravel\Tests\Unit\Objects;

use BYanelli\OpenApiLaravel\Objects\OpenApiDefinition;
use BYanelli\OpenApiLaravel\Objects\OpenApiInfo;
use BYanelli\OpenApiLaravel\Objects\OpenApiOperation;
use BYanelli\OpenApiLaravel\Objects\OpenApiPath;
use BYanelli\OpenApiLaravel\Objects\OpenApiResponse;
use BYanelli\OpenApiLaravel\Objects\OpenApiSchema;
use BYanelli\OpenApiLaravel\Objects\Dtos\OpenApiPathDto;
use BYanelli\OpenApiLaravel\LaravelReflection\JsonResource;
use BYanelli\OpenApiLaravel\LaravelReflection\JsonResourceProperties;
use BYanelli\OpenApiLaravel\LaravelReflection\ResponseProperties;
use BYanelli\OpenApiLaravel\Tests\Library\AlternateUserResource;
use BYanelli\OpenApiLaravel\Tests\TestApp\app\Http\Controllers\UserController;
use BYanelli\OpenApiLaravel\Tests\TestCase;
use Illuminate\Support\Arr;
use TestApp\Http\Controllers\PostController;
use TestApp\Http\Responses\TokenResponse;
use TestApp\User;

class DefinitionTest extends TestCase
{
    /** @test */
    public function build_definition()
    {
        $def = (
            OpenApiDefinition::make()
                ->info(
                    OpenApiInfo::make()
                        ->title('Test')
                        ->version('1.0')
                )
                ->addPath(OpenApiPath::make()->path('/api/users'))
                ->addPath(OpenApiPath::make()->path('/api/posts'))
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
        OpenApiDefinition::with(function () {
            JsonResourceProperties::for(AlternateUserResource::class)
                ->model(User::class);

            $modelClass = (new JsonResource(AlternateUserResource::class))->modelClass();

            $this->assertEquals(User::class, $modelClass);
        });
    }

    /** @test */
    public function specify_schema_for_response()
    {
        $this->assertDefinitionPartsEqual(
            function () {
                OpenApiInfo::make()->title('title')->version('version');

                ResponseProperties::for(TokenResponse::class)
                    ->schema([
                        'token'     => 'string',
                        'expiresAt' => 'string',
                    ]);

                OpenApiOperation::make()->action([UserController::class, 'authenticate']);
            },
            [
                'paths./api/users/authenticate.post.responses.200.content.application/json.schema' => [
                    '$ref' => '#/components/schemas/TokenResponse'
                ],
                'components.schemas.TokenResponse' => [
                    'type' => 'object',
                    'properties' => [
                        'token'     => ['type' => 'string'],
                        'expiresAt' => ['type' => 'string'],
                    ],
                    'title' => 'TokenResponse',
                ]
            ]
        );
    }

    protected function assertDefinitionEquals(callable $definition, string $path, array $expected)
    {
        $definition = OpenApiDefinition::with($definition);

        $result = Arr::get($definition->build()->toArray(), $path);

        $this->assertEquals($expected, $result);
    }

    protected function assertDefinitionPartsEqual(callable $definition, array $expectedParts)
    {
        $definition = OpenApiDefinition::with($definition);

        $result = $definition->build()->toArray();

        foreach ($expectedParts as $path => $expected) {
            $this->assertEquals($expected, Arr::get($result, $path));
        }
    }

    /** @test */
    public function specify_schema_for_resource()
    {
        $this->assertDefinitionEquals(
            function () {
                OpenApiInfo::make()->title('title')->version('version');

                JsonResourceProperties::for(AlternateUserResource::class)
                    ->schema([
                        'userEmail'     => 'string',
                        'userFirstName' => 'string',
                        'userLastName'  => 'string',
                    ]);

                OpenApiPath::make()->path('/user')->get()->response(AlternateUserResource::class);
            },
            'components.schemas.AlternateUserResource',
            [
                'type' => 'object',
                'properties' => [
                    'userEmail'     => ['type' => 'string'],
                    'userFirstName' => ['type' => 'string'],
                    'userLastName'  => ['type' => 'string'],
                ],
                'title' => 'AlternateUser',
            ]
        );
    }
}