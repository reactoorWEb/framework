<?php

namespace illustrate\Closure\Test;

use Closure;
use illustrate\Closure\ReflectionClosure;

final class NamespaceUnqualifiedTest extends \PHPUnit\Framework\TestCase
{
    public function test_instantiate_non_qualified_class_name()
    {
        $f = function (){ new A\B; };
        $e = 'function (){ new \illustrate\Closure\Test\A\B; }';
        $this->assertEquals($e, $this->c($f));
    }

    protected function c(Closure $closure)
    {
        $r = new ReflectionClosure($closure);

        return $r->getCode();
    }
}
