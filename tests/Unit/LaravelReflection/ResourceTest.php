<?php

namespace BYanelli\OpenApiLaravel\Tests\Unit\LaravelReflection;

use BYanelli\OpenApiLaravel\LaravelReflection\JsonResourceProperty;
use BYanelli\OpenApiLaravel\LaravelReflection\Model;
use BYanelli\OpenApiLaravel\LaravelReflection\JsonResource;
use BYanelli\OpenApiLaravel\Tests\TestCase;
use TestApp\Http\Resources\Post as PostResource;
use TestApp\Http\Resources\User;
use TestApp\Post as PostModel;

class ResourceTest extends TestCase
{
    protected $resource;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resource = new JsonResource(PostResource::class, new Model(new PostModel()));
    }

    protected function assertHasProperties(JsonResource $resource, array $expectedProperties)
    {
        $actualProperties = collect($resource->properties())->map->name()->all();

        $this->assertEquals($expectedProperties, $actualProperties);

    }

    /** @test */
    public function get_property_names()
    {
        $this->assertHasProperties($this->resource, ['id', 'body', 'headlineSlug', 'author']);
    }

    /** @test */
    public function get_property_types()
    {
        $this->assertHasTypes($this->resource, [
            'id'            => 'integer',
            'body'          => 'string',
            'headlineSlug'  => 'string',
            'author'        => 'json_resource'
        ]);

        $this->assertHasResourceType($this->resource, 'author', User::class);

        $this->assertPropertyIsConditional($this->resource, 'body');
    }

    private function assertHasTypes(JsonResource $resource, array $types)
    {
        foreach ($types as $name => $type) {
            $this->assertHasType($resource, $name, $type);
        }
    }

    private function assertHasType(JsonResource $resource, string $name, string $type)
    {
        $properties = collect($resource->properties());

        $this->assertTrue($properties->contains(function (JsonResourceProperty $property) use ($name, $type) {
            return ($property->name() == $name) && ($property->type() == $type);
        }));
    }

    private function assertHasResourceType(JsonResource $resource, string $name, string $resourceType)
    {
        $properties = collect($resource->properties());

        $this->assertTrue($properties->contains(function (JsonResourceProperty $property) use ($name, $resourceType) {
            return ($property->name() == $name) && ($property->resourceType() == $resourceType);
        }));
    }

    private function assertPropertyIsConditional(JsonResource $resource, string $name)
    {
        $properties = collect($resource->properties());

        $this->assertTrue($properties->contains(function (JsonResourceProperty $property) use ($name) {
            return ($property->name() == $name) && $property->isConditional();
        }));
    }
}