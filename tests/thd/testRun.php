<?php

require __DIR__."/../../autoload.php";

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @see Handler\MainHandler
 */

$fake_webhook = '{
    "update_id": 44178274,
    "message": {
        "message_id": 2248,
        "from": {
            "id": 243692601,
            "is_bot": false,
            "first_name": "Ammar",
            "last_name": "F",
            "username": "ammarfaizi2",
            "language_code": "en-US"
        },
        "chat": {
            "id": -100112853117,
            "title": "Dead Inside",
            "username": "deadinsidegroup",
            "type": "supergroup"
        },
        "date": 1505926496,
        "reply_to_message": {
            "message_id": 22487,
            "from": {
                "id": 426180876,
                "is_bot": true,
                "first_name": "Dead Inside",
                "username": "SugarAppleBot"
            },
            "chat": {
                "id": -1001128531173,
                "title": "Dead Inside",
                "username": "deadinsidegroup",
                "type": "supergroup"
            },
            "date": 1505926483,
            "text": "Removing debug.tmp",
            "entities": [
                {
                    "offset": 0,
                    "length": 18,
                    "type": "pre"
                }
            ]
        },
        "text": "!sh echo 3"
    }
}';

$app = new Bot($fake_webhook);
$app->run();