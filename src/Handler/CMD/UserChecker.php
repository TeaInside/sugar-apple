<?php

namespace Handler\CMD;

use Handler\MainHandler;

class UserChecker
{
	/**
	 * @var Handler\MainHandler
	 */
	private $h;

	/**
	 * @param Handler\MainHandler $handler
	 */
	public function __construct(MainHandler $handler)
	{
		$this->h = $handler;
	}
}