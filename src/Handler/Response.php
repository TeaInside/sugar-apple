<?php

namespace Handler;

use Handler\Command;
use Handler\MainHandler;

final class Response
{	
	use Command;

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
		if (! $this->command()) {
			if (! $this->__lang_virtualizor()) {
				return false;
			}
		}
		return true;
	}

	private function __lang_virtualizor()
	{

	}
}