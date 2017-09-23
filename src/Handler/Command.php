<?php

namespace Handler;

use Handler\CMD\CMDHandler;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @license MIT
 */

trait Command
{
	private function command()
	{
		$cmd_list = [
			"/sh"			=> ["!sh", "~sh"],
			"/ban"			=> ["!ban", "~ban"],
			"/start" 		=> ["!start", "~start"],
			"/anime"		=> ["!anime", "~anime"],
			"/manga"		=> ["!manga", "~manga"],
			"/welcome"		=> ["!welcome", "~welcome"],
		];
		$fs = explode(" ", $this->h->text, 2) xor $param = isset($fs[1]) ? trim($fs[1]) : null;
		$fs = explode("@", $fs[0]);
		$fs = strtolower($fs[0]);
		foreach ($cmd_list as $key => $val) {
			if ($fs == $key) {
				return $this->__command($key, $param);
			} else {
				foreach ($val as $val) {
					if ($fs == $val) {
						return $this->__command($key, $param);
					}
				}
			}
		}
		return false;
	}

	private function __command($key, $param = null)
	{
		$cmd = new CMDHandler($this->h);
		switch ($key) {
			case '/start':
					return $cmd->__start($param);
				break;
			case '/sh':
					return $cmd->__sh($param);
				break;
			case '/welcome':
					return $cmd->__welcome($param);
				break;
			case '/ban':
					return $cmd->__ban($param);
				break;
			case '/anime':
					return $cmd->__anime($param);
				break;
			case '/manga':
					return $cmd->__manga($param);
				break;
			default:
				break;
		}
	}
}