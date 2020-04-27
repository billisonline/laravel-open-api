<?php

namespace BYanelli\OpenApiLaravel\Tests\Unit;

use BYanelli\OpenApiLaravel\Support\Model;
use BYanelli\OpenApiLaravel\Tests\TestCase;
use TestApp\Post;

class ModelTest extends TestCase
{
    protected $model;

    protected function setUp(): void
    {
        parent::setUp();

        $this->model = new Model(new Post);
    }

    /** @test */
    public function get_attribute_type()
    {
        $this->assertEquals('integer', $this->model->getAttributeType('id'));
    }
}