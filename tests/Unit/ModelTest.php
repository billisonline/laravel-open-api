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
    public function get_column_type()
    {
        $this->assertEquals('integer', $this->model->getColumnType('id'));
    }

    /** @test */
    public function get_dynamic_property_type()
    {
        $this->assertEquals('string', $this->model->getGetMutatorType('headline_slug'));
    }
}