<?php

require __DIR__."/../../autoload.php";

use Telegram as B;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @see https://core.telegram.org/bots/api
 */

/**
 * Contoh ngirim pesan simple.
 */
B::sendMessage(
	[
		"chat_id" => "@deadinsidegroup",
		"text"    => "test"
	]
);
