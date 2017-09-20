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

	public function exec()
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
				$st = DB::prepare("UPDATE `a_users` SET `username`=:uname, `name`=:name, `private`='true', `updated_at`=:up, `msg_count`=`msg_count`+1 WHERE `userid`=:userid LIMIT 1;");
				pc($st->execute(
					[
						":uname"	=> $this->h->username,
						":name"		=> $this->h->name,
						":up"		=> (date("Y-m-d H:i:s")),
						":userid"	=> $this->h->userid
					]
				), $st);
			} else {
				$st = DB::prepare("UPDATE `a_users` SET `msg_count`=`msg_count`+1, `updated_at`=:up WHERE `userid`=:userid LIMIT 1;");
				pc($st->execute(
					[
						":up"		=> (date("Y-m-d H:i:s")),
						":userid" 	=> $this->h->userid
					]), $st);
			}
		} else {
			$st = DB::prepare("INSERT INTO `a_users` (`userid`,`username`,`name`,`photo`,`msg_count`,`private`,`notification`,`lang`,`created_at`) VALUES (:userid, :uname, :name, :photo, 1, 'true', 'true', 'en', :created_at);");
			pc($st->execute(
				[
					":userid"		=> $this->h->userid,
					":uname"		=> $this->h->username,
					":name"			=> $this->h->name,
					":photo"		=> null,
					":created_at"	=> (date("Y-m-d H:i:s"))
				]), $st);
		}
		$st = DB::prepare("INSERT INTO `private_messages` (`userid`,`message_uniq`,`message_id`,`type`,`reply_to_message_id`,`time`,`created_at`) VALUES (:userid, :msg_uniq, :msgid, :type, :replyto, :time, :created_at);") xor $data = [];
		pc($st->execute(
			[
				":userid" 		=> $this->h->userid,
				":msg_uniq"		=> ($data[':msg_uniq'] = $this->h->userid."|".$this->h->msgid),
				":msgid"		=> $this->h->msgid,
				":type"			=> $this->h->msgtype,
				":replyto"		=> (isset($this->h->replyto) ? $this->h->replyto['message_id'] : null),
				":time"			=> (date("Y-m-d H:i:s", $this->h->date)),
				":created_at"	=> (date("Y-m-d H:i:s"))
			]), $st);
		$st = DB::prepare("INSERT INTO `private_messages_data` (`message_uniq`,`text`,`file_id`) VALUES (:msg_uniq, :txt, :file_id);");
		switch ($this->h->msgtype) {
			case 'text':
				$data[':txt'] 		= $this->h->text;
				$data[':file_id']	= null;
				break;
			case 'photo':
				$ed 				= end($this->h->photo);
				$data[':txt']		= $this->h->text;
				$data[':file_id']	= $ed['file_id'];
				break;
			default:
				break;
		}
		pc($st->execute($data), $st);
	}
}



