<?php

namespace BYanelli\OpenApiLaravel\Tests\Unit;

use BYanelli\OpenApiLaravel\Support\Model;
use BYanelli\OpenApiLaravel\Support\Resource;
use BYanelli\OpenApiLaravel\Tests\TestCase;
use TestApp\Http\Resources\Post as PostResource;
use TestApp\Post as PostModel;

class ResourceTest extends TestCase
{
    protected $resource;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resource = new Resource(PostResource::class, new Model(new PostModel()));
    }

    /** @test */
    public function get_property_names()
    {
        $this->assertEquals(['id', 'conditional'], $this->resource->propertyNames());
    }

    /** @test */
    public function get_property_attribute_type()
    {
        $this->assertEquals('integer', $this->resource->propertyType('id'));
    }
}