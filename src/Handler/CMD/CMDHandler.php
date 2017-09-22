<?php

namespace Handler\CMD;

use DB;
use PDO;
use Lang\Map;
use Telegram as B;
use Handler\MainHandler;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @license MIT
 */

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
		$this->buildContext();
	}

	/**
	 * @param  string $param
	 * @return string|bool
	 */
	public function __start($param)
	{
		if ($this->h->chattype == "private") {
			$this->lang .= "A";
			return B::sendMessage(
				[
					"chat_id" => $this->h->chat_id,
					"text" 	  => $this->fixer($this->lang::$a['private_start'])
				]
			);
		}
		return false;
	}

	public function __ban($param)
	{
		if (! isset($this->h->replyto) && isset($this->h->entities)) {
			$query = "SELECT `userid`,`name` FROM `a_users` WHERE " xor $data = [];
			foreach ($this->h->entities as $key => $value) {
				if ($value['type'] == "mention") {
					$query .= "`username`=:un_{$key} OR ";
					$data[':un_'.$key] = substr($this->h->lowertext, $value['offset']+1, $value['length']);
				}
			}
			if ($data) {
				$st = DB::prepare(rtrim($query, " OR ").";");
				pc($st->execute($data), $st);
				while ($r = $st->fetch(PDO::FETCH_NUM)) {
					B::kickChatMember(
						[
							"chat_id" => $this->h->chat_id,
							"user_id" => $r[0]
						]
					)['info']['http_code'] == 200 and B::sendMessage(
						[
							"chat_id" => $this->h->chat_id,
							"text"	  => "<a href=\"tg://user?id=".$this->h->userid."\">".htmlspecialchars($this->h->actorcall)."</a> banned <a href=\"tg://user?id=".$r[0]."\">".htmlspecialchars($r[1])."</a>!",
							"parse_mode" => "HTML"
						]
					);
				}
			}
		}
	}

	/**
	 * @param string $param
	 */
	public function __sh($param)
	{
		if (strpos($param, "sudo ") !== false) {
			if (in_array($this->h->userid, SUDOERS)) {
				$sh = shell_exec($param." 2>&1");
				if (empty($sh)) {
					$sh = "<pre>~</pre>";
				} else {
					$sh = "<pre>".htmlspecialchars($sh)."</pre>";
				}
			} else {
				$msg = "<b>WARNING</b>\nUnwanted user tried to use sudo.\n\n".
										   "<b>• Rejected at</b>: ".date("Y-m-d H:i:s")."\n".
										   "<b>• Tried by</b>: <a href=\"".$this->h->userid."\">".htmlspecialchars($this->h->name)."</a>\n".
										   "<b>• Command</b>: <code>".htmlspecialchars($this->h->text)."</code>";
				foreach (SUDOERS as $val) {
					B::sendMessage(
						[
							"chat_id"		=> $val,
							"text"			=> $msg,
							"parse_mode"	=> "HTML"
						]
					);
				}
				$sh = "<a href=\"tg://user?id=".$this->h->userid."\">".htmlspecialchars($this->h->name)."</a> is not in the sudoers file. This incident will be reported.";
			}
		} else {
			$sh = shell_exec($param." 2>&1");
			if (empty($sh)) {
				$sh = "<pre>~</pre>";
			} else {
				$sh = "<pre>".htmlspecialchars($sh)."</pre>";
			}
		}
		
		return B::sendMessage(
			[
				"chat_id" 				=> $this->h->chat_id,
				"text"	  				=> $sh,
				"parse_mode"			=> "HTML",
				"reply_to_message_id"	=> $this->h->msgid
			]
		);
	}

	public function __welcome($param)
	{
		$this->lang .= "A";
		if (! empty($param)) {
			$st = DB::prepare("UPDATE `a_groups` SET `welcome_message`=:wel, `updated_at`=:up WHERE `group_id`=:gid LIMIT 1;");
			pc($st->execute(
				[
					":wel"		=> $param,
					":up"		=> (date("Y-m-d H:i:s")),
					":gid"	=> $this->h->chat_id
				]
			), $st);
			return B::sendMessage(
				[
					"chat_id" 				=> $this->h->chat_id,
					"text"	  				=> $this->fixer($this->lang::$a['set_welcome_msg']),
					"reply_to_message_id"	=> $this->h->msgid
				]
			);
		} else {
			$st = DB::prepare("SELECT `welcome_message` FROM `a_groups` WHERE `group_id`=:gid LIMIT 1;");
			pc($st->execute(
				[
					":gid" => $this->h->chat_id
				]
			), $st);
			if ($st = $st->fetch(PDO::FETCH_NUM)) {
				if (! empty($st[0])) {
					$st = DB::prepare("UPDATE `a_groups` SET `welcome_message`=:wel, `updated_at`=:up WHERE `group_id`=:gid LIMIT 1;");
					pc($st->execute(
						[
							":wel"		=> null,
							":up"		=> (date("Y-m-d H:i:s")),
							":gid"	=> $this->h->chat_id
						]
					), $st);
					return B::sendMessage(
						[
							"chat_id" 				=> $this->h->chat_id,
							"text"	  				=> $this->fixer($this->lang::$a['error_empty_set_welcome_msg']),
							"reply_to_message_id"	=> $this->h->msgid
						]
					);
				} else {
					$st = DB::prepare("UPDATE `a_groups` SET `welcome_message`=:wel, `updated_at`=:up WHERE `group_id`=:gid LIMIT 1;");
					pc($st->execute(
						[
							":wel"		=> null,
							":up"		=> (date("Y-m-d H:i:s")),
							":gid"	=> $this->h->chat_id
						]
					), $st);
					return B::sendMessage(
						[
							"chat_id" 				=> $this->h->chat_id,
							"text"	  				=> $this->fixer($this->lang::$a['drop_welcome_msg']),
							"reply_to_message_id"	=> $this->h->msgid
						]
					);
				}
			}
		}
	}

	/**
	 * Build fixer context.
	 */
	private function buildContext()
	{
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

	/**
	 * @param  string $str
	 * @return string
	 */
	private function fixer($str)
	{
		return str_replace($this->r1, $this->r2, $str);
	}
}