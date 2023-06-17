<?php

namespace illustrate\Closure\Test;

use Closure;
use illustrate\Closure\ClosureContext;
use illustrate\Closure\ClosureContext as SomeAlias;
use illustrate\Closure\SerializableClosure;

final class NamespaceTest extends \PHPUnit\Framework\TestCase
{
    public function testNamespacedObjectInsideClosure()
    {
        $closure = function () {
            $object = new ClosureContext();

            self::assertInstanceOf('\illustrate\Closure\ClosureContext', $object);
            self::assertInstanceOf(SomeAlias::class, $object);
        };

        $executable = $this->s($closure);

        $executable();
    }

    protected function s($closure)
    {
        if ($closure instanceof Closure) {
            $closure = new SerializableClosure($closure);
        }

        return unserialize(serialize($closure))->getClosure();
    }
}
