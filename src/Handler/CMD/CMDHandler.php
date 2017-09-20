<?php

namespace Handler\CMD;

use Lang\Map;
use Telegram as B;
use Handler\MainHandler;

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

	private function fixer($str)
	{
		return str_replace($this->r1, $this->r2, $str);
	}

	public function __start($param)
	{
		$this->lang .= "A";
		B::sendMessage(
			[
				"chat_id" 				=> $this->h->chat_id,
				"text" 	  				=> $this->fixer($this->lang::$a['private_start']),
				"reply_to_message_id"	=> $this->h->msgid
			]
		);
	}
}