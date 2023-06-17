<?php
/**
 * Reactoor\illustrate\Exeptions - php errors for cool kids
 * @author Arshiamohammadei <arshia8587@gmail.com>
 */

namespace  illustrate\Exeptions\Exception;

use ErrorException as BaseErrorException;

/**
 * Wraps ErrorException; mostly used for typing (at least now)
 * to easily cleanup the stack trace of redundant info.
 */
class ErrorException extends BaseErrorException
{
}
