<?php

namespace Handler;

use Handler\MainHandler;

final class Response
{	
	/**
	 * @var Handler\MainHandler
	 */
	private $h;

	public function __construct(MainHandler $handler)
	{
		$this->h = $handler;
	}

	public function textResponse()
	{
		
	}
}