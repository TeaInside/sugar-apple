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
			$group = isset($this->h->chatuname) ? "<a href=\"https://t.me/".$this->h->chatuname."/".$this->h->msgid."\">".htmlspecialchars($this->h->chattitle)."</a>" : "<b>".htmlspecialchars($this->h->chattitle)."</b>";
			foreach ($this->h->entities['mention'] as $val) {
				$st = DB::prepare("SELECT `userid`,`private` FROM `a_users` WHERE `username`=:uname LIMIT 1;");
				pc($st->execute([
					":uname" => $val
				]), $st);
				$msg = "<a href=\"tg://user?id=".$this->h->userid."\">".htmlspecialchars($this->h->name)."</a> tagged you in ".$group."\n\n<code>".htmlspecialchars($this->h->text)."</code>";
				if ($st = $st->fetch(PDO::FETCH_NUM) and $st[1] == "true") {
					B::sendMessage(
						[
							"chat_id" => $st[0],
							"text" => $msg,
							"parse_mode" => "HTML",
							"disable_web_page_preview" => true
						]
					);
				}
			}
		}

		if (isset($this->h->replyto)) {
			$group = isset($this->h->chatuname) ? "<a h6ref=\"https://t.me/".$this->h->chatuname."/".$this->h->msgid."\">".htmlspecialchars($this->h->chattitle)."</a>" : "<b>".htmlspecialchars($this->h->chattitle)."</b>";
			$st = DB::prepare("SELECT `userid`,`private` FROM `a_users` WHERE `userid`=:userid LIMIT 1;");
			pc($st->execute([
				":userid" => $this->h->replyto['from']['id']
			]), $st);
			$msg = "<a href=\"tg://user?id=".$this->h->userid."\">".htmlspecialchars($this->h->name)."</a> replied to your message in ".$group."\n\n<code>".htmlspecialchars($this->h->text)."</code>";
			if ($st = $st->fetch(PDO::FETCH_NUM) and $st[1] == "true") {
				B::sendMessage(
					[
						"chat_id" => $st[0],
						"text" => $msg,
						"parse_mode" => "HTML",
						"disable_web_page_preview" => true
					]
				);
			}
		}
	}
}
