<?php
/**
 * Reactoor\illustrate\Exeptions - php errors for cool kids
 * @author Arshiamohammadei <arshia8587@gmail.com>
 */
namespace illustrate\Exeptions\Inspector;

use illustrate\Exeptions\Exception\Inspector;

class InspectorFactory implements InspectorFactoryInterface
{
    /**
     * @param \Throwable $exception
     * @return InspectorInterface
     */
    public function create($exception)
    {
        return new Inspector($exception, $this);
    }
}
