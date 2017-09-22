<?php

namespace Handler;

use DB;
use PDO;
use Telegram as B;
use Handler\MainHandler;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @license MIT
 */

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

	/**
	 *
	 */
	public function exec()
	{
		if ($this->h->chattype == "private") {
			$this->private_save();
		} else {
			$this->group_save();
		}
	}

	private function group_save()
	{
		$st = DB::prepare("SELECT COUNT(`userid`) FROM `groups_admin` WHERE `group_id`=:gid LIMIT 1;");
		pc($st->execute(
			[
				":gid" => $this->h->chat_id
			]
		), $st);
		$st = $st->fetch(PDO::FETCH_NUM);
		if ($st[0] <= 0) {
			$rr = json_decode(B::getChatAdministrators(
			 	[
			 		"chat_id" => $this->h->chat_id
			 	]
			 )['content'], true) xor $admins = [] xor $i = 1;
			$query = "INSERT INTO `groups_admin` (`group_id`,`userid`,`status`,`privileges`,`created_at`,`updated_at`) VALUES ";
			foreach ($rr['result'] as $val) {
				$admins[':userid_'.$i] = $val['user']['id'];
				$admins[':status_'.$i] = $val['user']['status'];
				$admins[':created_at_'.$i] = date("Y-m-d");
				$admins[':updated_at_'.$i] = null;
				$query = "(:userid_".$i.",:status_".$i.",'OK',:created_at_".$i.",:updated_at_".$i."),";
			}
			$st = DB::prepare(rtrim($query, ",").";");
			pc($st->execute($admins), $st);
		}
		$st = DB::prepare("SELECT `group_name`,`group_username` FROM `a_groups` WHERE `group_id`=:gid LIMIT 1;");
		pc($st->execute(
			[
				":gid"	=> $this->h->chat_id
			]
		), $st);
		if ($st = $st->fetch(PDO::FETCH_ASSOC)) {
			if (
				$st['group_name'] 		!= $this->h->chattitle	||
				$st['group_username']	!= $this->h->chatuname
			) {
				$st = DB::prepare("UPDATE `a_groups` SET `group_name`=:gname, `group_username`=:guname, `updated_at`=:up, `msg_count`=`msg_count`+1 WHERE `group_id`=:gid LIMIT 1;");
				pc($st->execute(
					[
						":gname"	=> $this->h->chattitle,
						":guname"	=> $this->h->chatuname,
						":up"		=> (date("Y-m-d H:i:s")),
						":gid"		=> $this->h->chat_id
					]), $st);
			} else {
				$st = DB::prepare("UPDATE `a_groups` SET `updated_at`=:up, `msg_count`=`msg_count`+1 WHERE `group_id`=:gid LIMIT 1;");
				pc($st->execute(
					[
						":up"		=> (date("Y-m-d H:i:s")),
						":gid"		=> $this->h->chat_id
					]), $st);
			}
		} else {
			$st = DB::prepare("INSERT INTO `a_groups` (`group_id`,`group_name`,`group_username`,`msg_count`,`max_warn`,`welcome_message`,`lang`,`created_at`) VALUES (:gid, :gname, :guname, 1, 3, null, 'en', :created_at);");
			pc($st->execute(
				[
					":gid"			=> $this->h->chat_id,
					":gname"		=> $this->h->chattitle,
					":guname"		=> $this->h->chatuname,
					":created_at"	=> (date("Y-m-d H:i:s"))
				]
			), $st);
		}
		$st  = DB::prepare("SELECT `username`,`name`,`msg_count`,`private`,`lang` FROM `a_users` WHERE `userid`=:userid LIMIT 1;");
		pc($st->execute(
			[
				":userid" => $this->h->userid
			]
		), $st);
		if ($st = $st->fetch(PDO::FETCH_ASSOC)) {
			if (
				$st['username'] != $this->h->username	||
				$st['name']		!= $this->h->name
			) {
				$st = DB::prepare("UPDATE `a_users` SET `username`=:uname, `name`=:name, `updated_at`=:up, `msg_count`=`msg_count`+1 WHERE `userid`=:userid LIMIT 1;");
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
					]
				), $st);
			}
		} else {
			$st = DB::prepare("INSERT INTO `a_users` (`userid`,`username`,`name`,`photo`,`msg_count`,`private`,`notification`,`lang`,`created_at`) VALUES (:userid, :uname, :name, :photo, 1, 'false', 'false', 'en', :created_at);");
			pc($st->execute(
				[
					":userid"		=> $this->h->userid,
					":uname"		=> $this->h->username,
					":name"			=> $this->h->name,
					":photo"		=> null,
					":created_at"	=> (date("Y-m-d H:i:s"))
				]
			), $st);
		}
		$st = DB::prepare("INSERT INTO `group_messages` (`group_id`,`userid`,`message_uniq`,`message_id`,`type`,`reply_to_message_id`,`time`,`created_at`) VALUES (:gid, :userid, :msg_uniq, :msgid, :type, :replyto, :_time, :created_at);") xor $data = [];
		pc($st->execute(
			[
				":gid"			=> $this->h->chat_id,
				":userid" 		=> $this->h->userid,
				":msg_uniq"		=> ($data[':msg_uniq'] = $this->h->chat_id."|".$this->h->msgid),
				":msgid"		=> $this->h->msgid,
				":type"			=> $this->h->msgtype,
				":replyto"		=> (isset($this->h->replyto) ? $this->h->replyto['message_id'] : null),
				":_time"			=> (date("Y-m-d H:i:s", $this->h->date)),
				":created_at"	=> (date("Y-m-d H:i:s"))
			]
		), $st);
		$st = DB::prepare("INSERT INTO `group_messages_data` (`message_uniq`,`text`,`file_id`) VALUES (:msg_uniq,:txt,:file_id);");
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
			case 'sticker':
				$data[':txt']		= $this->h->text;
				$data[':file_id']	= $this->h->sticker;
				break;
			default:
				break;
		}
		pc($st->execute($data), $st);
		return true;
	}

	/**
	 * Save private logs.
	 */
	private function private_save()
	{
		$st  = DB::prepare("SELECT `username`,`name`,`msg_count`,`private`,`lang` FROM `a_users` WHERE `userid`=:userid LIMIT 1;");
		pc($st->execute(
			[
				":userid" => $this->h->userid
			]
		), $st);
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
					]
				), $st);
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
				]
			), $st);
		}
		$st = DB::prepare("INSERT INTO `private_messages` (`userid`,`message_uniq`,`message_id`,`type`,`reply_to_message_id`,`time`,`created_at`) VALUES (:userid, :msg_uniq, :msgid, :type, :replyto, :_time, :created_at);") xor $data = [];
		pc($st->execute(
			[
				":userid" 		=> $this->h->userid,
				":msg_uniq"		=> ($data[':msg_uniq'] = $this->h->userid."|".$this->h->msgid),
				":msgid"		=> $this->h->msgid,
				":type"			=> $this->h->msgtype,
				":replyto"		=> (isset($this->h->replyto) ? $this->h->replyto['message_id'] : null),
				":_time"		=> (date("Y-m-d H:i:s", $this->h->date)),
				":created_at"	=> (date("Y-m-d H:i:s"))
			]
		), $st);
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
			case 'sticker':
				$data[':txt']		= $this->h->text;
				$data[':file_id']	= $this->h->sticker;
				break;
			default:
				break;
		}
		pc($st->execute($data), $st);
		return true;
	}
}



