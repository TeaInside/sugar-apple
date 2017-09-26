<?php

namespace Handler\CMD;

use DB;
use PDO;
use Handler\MainHandler;

class UserChecker
{
	/**
	 * @var PDO
	 */
	private $pdo;

	/**
	 * @var string
	 */
	private $query;

	/**
	 * @var string
	 */
	private $unique;

	/**
	 * @var string|bool(false)
	 */
	private $result;

	/**
	 * @param string $input
	 * @param string $type
	 */
	public function __construct($input, $type = "username")
	{
		$this->input 	= $input;
		$this->unique	= sha1(time());
		$this->query 	= "SELECT `{$this->unique}` FROM `a_users` WHERE `{$type}`=:tx LIMIT 1;";
		$this->pdo		= DB::pdo();
	}

	private function exec($select)
	{
		$st = $this->pdo->prepare(str_replace($this->unique, $select, $this->query));
		pc($st->execute(
			[
				":tx" => $this->input
			]
		), $st);
		if ($st = $st->fetch(PDO::FETCH_NUM)) {
			return $st[0];
		} else {
			return false;
		}
	}

	public function getUserID()
	{
		$this->exec("userid");
		return $this->result;
	}

	public function getUsername()
	{
		$this->exec("username");
		return $this->result;	
	}
}