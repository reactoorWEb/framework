<?php

namespace illustrate\Closure;

use ilustrate\Closure\SerializableClosure;

/**
 * Class Application Reactoor\illustrate
 * @author  ArshiaMohammadei <arshia8587@gmail.com>
 * @package illustrate\Closure
 */
function serialize($data)
{
	SerializableClosure::enterContext();
	SerializableClosure::wrapClosures($data);
	$data = \serialize($data);
	SerializableClosure::exitContext();
	return $data;
}

/**
 * Unserialize
 *
 * @param string $data
 * @param array|null $options
 * @return mixed
 */
function unserialize($data, array $options = null)
{
	SerializableClosure::enterContext();
	$data = ($options === null || \PHP_MAJOR_VERSION < 7)
		? \unserialize($data)
		: \unserialize($data, $options);
	SerializableClosure::unwrapClosures($data);
	SerializableClosure::exitContext();
	return $data;
}