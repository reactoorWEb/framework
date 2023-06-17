<?php

namespace illustrate\Closure\Test;

use Closure;
use illustrate\Closure\ReflectionClosure;

final class NamespaceGroupTest extends \PHPUnit\Framework\TestCase
{
    public function test_namespace_fully_qualified()
    {
        $f = function () { new \A; };
        $e = 'function () { new \A; }';
        $this->assertEquals($e, $this->c($f));
    }

    protected function c(Closure $closure)
    {
        $r = new ReflectionClosure($closure);

        return $r->getCode();
    }
}
