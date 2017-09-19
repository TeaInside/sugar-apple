<?php

namespace Handler;

class MainHandler
{
	/**
	 * @param array
	 */
	private $event = [];

	/**
	 * @param string $webhook_input
	 */
	public function __construct($webhook_input = null)
	{
		if ($webhook_input) {
			$this->input = json_decode($webhook_input, true);
		} else {
			$this->input = json_decode(file_get_contents("php://input"), true);
		}
	}

	private function parseEvent()
	{

	}
}