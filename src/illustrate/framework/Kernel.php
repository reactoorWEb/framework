<?php

/*
* Application:Reactoor\Phoenix Team
* Date: 6/12/2023
* Creator: Arshiamohammadei
*/

namespace illustrate;

use  Symfony\Component\HttpKernel\Kernel as HttpKernel;

/**
 * Class Application Phoenix
 * @author  ArshiaMohammadei <arshia8587@gmail.com>
 * @package Reactoor\Phoenix
 */
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
class Kernel implements HttpKernelInterface
{
	private $content = '';
	public function content($contact)
	{
		return $this->content = $contact;
	}
	public function handle(Request $request, int $type = self::MAIN_REQUEST, bool $catch = true, $content = ''):
	Response
	{
		return new Response($this->content);
	}
}
