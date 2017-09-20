<?php

namespace Handler\CMD;

use Lang\Map;
use Telegram as B;
use Handler\MainHandler;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @license MIT
 */

class CMDHandler
{
	/**
	 * @var Handler\MainHandler
	 */
	private $h;

	/**
	 * @var string
	 */
	private $lang;

	/**
	 * @var array
	 */
	private $r1 = [];

	/**
	 * @var array
	 */
	private $r2 = [];

	/**
	 * @param Handler\MainHandler $handler
	 */
	public function __construct(MainHandler $handler)
	{
		$this->h 	= $handler;
		$this->lang = Map::$language["id"];
		$this->buildContext();
	}

	/**
	 * @param  string $param
	 * @return string|bool
	 */
	public function __start($param)
	{
		if ($this->h->chattype == "private") {
			$this->lang .= "A";
			return B::sendMessage(
				[
					"chat_id" => $this->h->chat_id,
					"text" 	  => $this->fixer($this->lang::$a['private_start'])
				]
			);
		}

		return false;
	}

	/**
	 * Build fixer context.
	 */
	private function buildContext()
	{
		$this->r1	= [
			"{name}",
			"{first_name}",
			"{last_name}",
		];
		$this->r2	= [
			$this->h->name,
			$this->h->first_name,
			$this->h->last_name
		];
	}

	/**
	 * @param  string $str
	 * @return string
	 */
	private function fixer($str)
	{
		return str_replace($this->r1, $this->r2, $str);
	}
}