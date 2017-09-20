<?php

namespace Handler;

use DB;
use PDO;
use Handler\MainHandler;

class SaveEvent
{	
	/**
	 * @var array
	 */
	private $event = [];

	/**
	 * @var Handler\MainHandler
	 */
	private $h;
	
	/**
	 * @param array $event
	 */
	public function __construct(MainHandler $handler)
	{
		$this->h = $handler;
	}

	public function save()
	{
		if ($this->h->chattype == "private") {
			$this->private_save();
		}
	}

	private function private_save()
	{
		$st  = DB::prepare("SELECT `username`,`name`,`msg_count`,`private`,`lang` FROM `a_users` WHERE `userid`=:userid LIMIT 1;");
		$exe = $st->execute(
			[
				":userid" => $this->h->userid
			]
		);
		pc($exe, $st);
		if ($st = $st->fetch(PDO::FETCH_ASSOC)) {
			if (
				$st['username'] != $this->h->username	||
				$st['name']		!= $this->h->name		||
				$st['private']	!= "true"
			) {
				$st = DB::prepare("UPDATE `a_users` SET `username`=:uname, `name`=:name, `private`='true', `msg_count`=`msg_count`+1 WHERE `userid`=:userid LIMIT 1;");
				pc($st->execute(
					[
						":uname"	=> $this->h->username,
						":name"		=> $this->h->name,
						":userid"	=> $this->h->userid
					]
				), $st);
			}
		}
	}
}



