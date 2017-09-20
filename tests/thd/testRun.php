<?php

require __DIR__."/../../autoload.php";

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @see Handler\MainHandler
 */

$fake_webhook = '{
    "update_id": 344225591,
    "message": {
        "message_id": 2180,
        "from": {
            "id": 243692601,
            "is_bot": false,
            "first_name": "Ammar",
            "last_name": "F",
            "username": "ammarfaizi2",
            "language_code": "en-US"
        },
        "chat": {
            "id": -1001128970273,
            "title": "Crayner Team",
            "username": "crayner_team",
            "type": "supergroup"
        },
        "date": 1505947353,
        "new_chat_participant": {
            "id": 312537092,
            "is_bot": false,
            "first_name": "Kreateev",
            "last_name": "Media"
        },
        "new_chat_member": {
            "id": 312537092,
            "is_bot": false,
            "first_name": "Kreateev",
            "last_name": "Media"
        },
        "new_chat_members": [
            {
                "id": 312537092,
                "is_bot": false,
                "first_name": "Kreateev",
                "last_name": "Media"
            }
        ]
    }
}';

$app = new Bot($fake_webhook);
$app->run();