<?php

namespace Handler;

use Handler\CMD\CMDHandler;

trait Command
{
	private function command()
	{
		$cmd_list = [
			"/start" => ["!start", "~start"]
		];
		$fs = explode(" ", $this->h->text, 2) xor $param = isset($fs[1]) ? $fs[1] : null;
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
		var_dump($key);
		$cmd = new CMDHandler($this->h);
		switch ($key) {
			case '/start':
					$cmd->__start($param);
				break;
			
			default:
				# code...
				break;
		}
	}
}