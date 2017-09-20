<?php

require __DIR__."/../../autoload.php";

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @see Handler\MainHandler
 */

$fake_webhook = '{
    "update_id": 44178218,
    "message": {
        "message_id": 116,
        "from": {
            "id": 243692601,
            "is_bot": false,
            "first_name": "Ammar",
            "last_name": "F",
            "username": "ammarfaizi2",
            "language_code": "en-US"
        },
        "chat": {
            "id": 243692601,
            "first_name": "Ammar",
            "last_name": "F",
            "username": "ammarfaizi2",
            "type": "private"
        },
        "date": 1505923714,
        "reply_to_message": {
            "message_id": 110,
            "from": {
                "id": 426180876,
                "is_bot": true,
                "first_name": "Dead Inside",
                "username": "SugarAppleBot"
            },
            "chat": {
                "id": 243692601,
                "first_name": "Ammar",
                "last_name": "F",
                "username": "ammarfaizi2",
                "type": "private"
            },
            "date": 1505923377,
            "text": "123",
            "entities": [
                {
                    "offset": 0,
                    "length": 3,
                    "type": "pre"
                }
            ]
        },
        "photo": [
            {
                "file_id": "AgADBQAD8KcxGxYlEVYy-bXQUtljUYUozDIABDUIAAF-nK5mqnycAgABAg",
                "file_size": 293,
                "width": 26,
                "height": 21
            }
        ],
        "caption": "text"
    }
}';

$app = new Bot($fake_webhook);
$app->run();