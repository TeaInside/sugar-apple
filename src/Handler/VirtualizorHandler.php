<?php

namespace Handler;

use Telegram as B;
use Handler\MainHandler;
use App\Virtualizor\PHP\PHPVirtual;
use Handler\Security\Virtualizor\PHP;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @license MIT
 */
final class VirtualizorHandler
{
	/**
	 * @var Hanlder\MainHanlder
	 */
	private $h;

	/**
	 * Constructor.
	 * @param Handler\MainHandler $handler
	 */
	public function __construct(MainHandler $handler)
	{
		$this->h = $handler;
	}

	public function exec()
	{
		$this->__exec();
	}

	private function __exec()
	{
		$t = $this->h->lowertext;

		if (substr($t, 0, 5) == "<?php") {
			if (PHP::is_secure($this->h->text)) {
				$app = new PHPVirtual($this->h->text);
				$app = $app->exec();
				empty($app) and $app = "~";
				B::sendMessage(
					[
						"chat_id" 			  => $this->h->chat_id,
						"text" 	  			  => $app,
						"reply_to_message_id" => $this->h->msgid,
						"parse_mode"		  => "HTML"
					]
				);
			} else {
				$reject = true;
			}
		}
	}
}