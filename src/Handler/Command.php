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
			"/start" 		=> ["!start", "~start"],
			"/sh"			=> ["!sh", "~sh"],
			"/welcome"		=> ["!welcome", "~welcome"],
			"/ban"			=> ["!ban", "~ban"]
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
					$cmd->__start($param);
				break;
			case '/sh':
					$cmd->__sh($param);
				break;
			case '/welcome':
					$cmd->__welcome($param);
				break;
			case '/ban':
					$cmd->__ban($param);
				break;
			default:
				break;
		}
	}
}