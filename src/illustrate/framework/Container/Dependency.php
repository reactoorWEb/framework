<?php
namespace illustrate\Container;

use Closure;
use illustrate\Closure\SerializableClosure;
use Serializable;

class Dependency implements Serializable
{
    /** @var string */
    protected $concrete;

    /** @var bool */
    protected $shared;

    /** @var callable[] */
    protected $extenders = [];

    /** @var array */
    protected $arguments;

    /**
     * Dependency constructor.
     * @param string|callable $concrete
     * @param array $arguments
     * @param bool $shared
     */
    public function __construct($concrete, array $arguments, bool $shared)
    {
        $this->concrete = $concrete;
        $this->arguments = $arguments;
        $this->shared = $shared;
    }

    /**
     * @return string|callable
     */
    public function getConcrete()
    {
        return $this->concrete;
    }

    /**
     * @return bool
     */
    public function isShared(): bool
    {
        return $this->shared;
    }

    /**
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @return callable[]
     */
    public function getExtenders(): array
    {
        return $this->extenders;
    }

    /**
     * @param callable $callback
     */
    public function addExtender(callable $callback)
    {
        $this->extenders[] = $callback;
    }

    /**
     * @return string
     */
    public function serialize()
    {
        SerializableClosure::enterContext();

        $concrete = $this->concrete instanceof Closure
                    ? SerializableClosure::from($this->concrete)
                    : $this->concrete;
        $extenders = [];
        $arguments = [];

        foreach ($this->extenders as $value) {
            $extenders[] = $value instanceof Closure
                        ? SerializableClosure::from($value)
                        : $value;
        }

        foreach ($this->arguments as $value) {
            $arguments[] = $value instanceof Closure
                        ? SerializableClosure::from($value)
                        : $value;
        }

        $object = serialize([
            'concrete' => $concrete,
            'arguments' => $arguments,
            'shared' => $this->shared,
            'extenders' => $extenders,
        ]);

        SerializableClosure::exitContext();

        return $object;
    }

    /**
     * @param string $data
     */
    public function unserialize($data)
    {
        $object = unserialize($data);

        $this->concrete = $object['concrete'] instanceof SerializableClosure
                        ? $object['concrete']->getClosure()
                        : $object['concrete'];

        $this->shared = $object['shared'];

        foreach ($object['extenders'] as &$value) {
            if ($value instanceof SerializableClosure) {
                $value = $value->getClosure();
            }
        }

        foreach ($object['arguments'] as &$value) {
            if ($value instanceof SerializableClosure) {
                $value = $value->getClosure();
            }
        }

        $this->arguments = $object['arguments'];
        $this->extenders = $object['extenders'];
    }
}
