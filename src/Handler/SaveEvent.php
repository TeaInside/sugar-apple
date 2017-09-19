<?php

namespace Handler;

use DB;
use PDO;

class SaveEvent
{	
	/**
	 * @var array
	 */
	private $event = [];
	
	/**
	 * @param array $event
	 */
	public function __construct($event)
	{
		$this->event = $event;
	}
}