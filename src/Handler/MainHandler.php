<?php

namespace Handler;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 */

use Telegram as B;

final class MainHandler
{
	/**
	 * @var array
	 */
	private $event = [];

	/**
	 * @var string
	 */
	private $msgtype;

	/**
	 * @var string
	 */
	private $chattype;

	/**
	 * @var string
	 */
	private $text;

	/**
	 * @var string
	 */
	private $lowertext;

	/**
	 * @var string
	 */
	private $username;

	/**
	 * @var string
	 */
	private $first_name;

	/**
	 * @var string
	 */
	private $last_name;

	/**
	 * @var string
	 */
	private $userid;

	/**
	 * @var string
	 */
	private $msgid;

	/**
	 * @var string
	 */
	private $date;

	/**
	 * @var string
	 */
	private $chat_id;


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

	public function run()
	{
		$this->parseEvent();
		$this->response();
	}

	private function parseEvent()
	{
		if (isset($this->input['message']['text'])) {
			$this->msgtype  	= "text";
			$this->chattype 	= $this->input['message']['chat']['type'];
			$this->text     	= $this->input['message']['text'];
			$this->lowertext 	= strtolower($this->text);
			$this->username		= isset($this->input['message']['from']['username']) ? strtolower($this->input['message']['from']['username']) : null;
			$this->first_name   = $this->input['message']['from']['first_name'];
			$this->last_name    = isset($this->input['message']['from']['last_name']) ? $this->input['message']['from']['last_name'] : null;
			$this->userid		= $this->input['message']['from']['id'];
			$this->msgid		= $this->input['message']['message_id'];
			$this->date			= $this->input['message']['date'];
			$this->chat_id		= $this->input['message']['chat']['id'];
		}
	}

	private function response()
	{
		if ($this->msgtype == "text") {
			B::sendMessage([
				"text" => $this->text,
				"chat_id" => $this->chat_id
			]);
		}
	}
}

