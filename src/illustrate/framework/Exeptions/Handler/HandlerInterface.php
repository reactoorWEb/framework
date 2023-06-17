<?php
/**
 * Reactoor\illustrate\Exeptions - php errors for cool kids
 * @author Arshiamohammadei <arshia8587@gmail.com>
 */
namespace illustrate\Exeptions\Handler;

use illustrate\Exeptions\Inspector\InspectorInterface;
use illustrate\Exeptions\RunInterface;

interface HandlerInterface
{
    /**
     * @return int|null A handler may return nothing, or a Handler::HANDLE_* constant
     */
    public function handle();

    /**
     * @param  RunInterface  $run
     * @return void
     */
    public function setRun(RunInterface $run);

    /**
     * @param  \Throwable $exception
     * @return void
     */
    public function setException($exception);

    /**
     * @param  InspectorInterface $inspector
     * @return void
     */
    public function setInspector(InspectorInterface $inspector);
}
