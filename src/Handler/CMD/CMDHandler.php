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
	 * @param Handler\MainHandler $handler
	 */
	public function __construct(MainHandler $handler)
	{
		$this->h 	= $handler;
		$this->lang = Map::$language["id"];
	}

	public function __start($param)
	{
		$this->lang .= "A";
		B::sendMesssage(
			[
				"chat_id" 				=> $this->h->chat_id,
				"text" 	  				=> $this->lang::$a['private_start'],
				"reply_to_message_id"	=> $this->h->msgid
			]
		);
	}
}