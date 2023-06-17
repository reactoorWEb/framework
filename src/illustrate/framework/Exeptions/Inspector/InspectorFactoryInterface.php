<?php
/**
 * Reactoor\illustrate\Exeptions - php errors for cool kids
 * @author Arshiamohammadei <arshia8587@gmail.com>
 */
namespace illustrate\Exeptions\Inspector;

interface InspectorFactoryInterface
{
    /**
     * @param \Throwable $exception
     * @return InspectorInterface
     */
    public function create($exception);
}
