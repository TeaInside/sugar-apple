<?php

namespace Handler;

use DB;
use PDO;
use Telegram as B;
use Handler\MainHandler;


class Notification
{
	/**
	 * @var Handler\MainHandler
	 */
	private $h;

	/**
	 * Constructor.
	 * @param Handler\MainHandler $handler
	 */
	public function __construct(MainHandler $handler)
	{
		$this->h = $handler;
	}

	public function exec()
	{
		if (isset($this->h->entities['mention'])) {
			foreach ($this->h->entities['mention'] as $val) {
				$st = DB::prepare("SELECT `userid` FROM `a_users` WHERE `username`=:uname LIMIT 1;");
				pc($st->execute([
					":uname" => $val
				]), $st);
				if ($st = $st->fetch(PDO::FETCH_NUM)) {
					B::sendMessage(
						[
							"chat_id" => $st[0],
							"text" => "Ada pesan dari ".$this->name
						]
					);
				}
			}
		}
	}
}