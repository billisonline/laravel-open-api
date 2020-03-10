<?php

namespace BYanelli\SuperFactory\Tests;

use BYanelli\SuperFactory\Widget;
use PHPUnit\Framework\TestCase;

class WidgetTest extends TestCase
{
    public function testSomething()
    {
        $widget = new Widget();
        $this->assertTrue($widget->doSomething());
    }
}
