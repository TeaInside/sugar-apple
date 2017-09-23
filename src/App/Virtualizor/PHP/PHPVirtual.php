<?php

namespace App\Virtualizor\PHP;

defined("PHPVIRTUAL_DIR") or die("PHPVIRTUAL_DIR not defined!\n");
defined("PHPVIRTUAL_URL") or die("PHPVIRTUAL_URL not defined!\n");

use Curl;
use Contracts\AppContracts\Virtualizor as VirtualizorContract;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @license MIT
 */
class PHPVirtual implements VirtualizorContract
{
	/**
	 * @var string
	 */
	private $phpcode;

	/**
	 * @var string
	 */
	private $hash;

	/**
	 * Constructor.
	 * @param string $phpcode
	 */
	public function __construct($phpcode)
	{
		$this->phpcode = $phpcode;
		$this->hash	   = sha1($phpcode);
		$this->__init();
	}

	private function __init()
	{
		is_dir(PHPVIRTUAL_DIR) or shell_exec("mkdir -p ".PHPVIRTUAL_DIR);
		if (! file_exists(PHPVIRTUAL_DIR."/".$this->hash.".php")) {
			file_put_contents(PHPVIRTUAL_DIR."/".$this->hash.".php", $this->phpcode);
		}
	}

	public function exec()
	{
		$ch = new Curl(PHPVIRTUAL_URL."/".$this->hash.".php");
		return $ch->exec();
	}
}
