<?php


namespace illustrate\Exeptions;


/**
 * Class Application Phoenix
 * @author  ArshiaMohammadei <arshia8587@gmail.com>
 * @package Reactoor\Phoenix
 */

class NotFoundException extends \Exception
{
    protected $message = 'Page not found';
    protected $code = 404;
}