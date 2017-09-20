<?php

require __DIR__."/../../autoload.php";

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @see Handler\MainHandler
 */

$fake_webhook = '{
    "update_id": 44178220,
    "message": {
        "message_id": 118,
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
        "date": 1505924203,
        "sticker": {
            "width": 512,
            "height": 512,
            "emoji": "\u2728",
            "set_name": "fangirl_line",
            "thumb": {
                "file_id": "AAQEABPGgWEZAAQJraGHZ9C3KUB5AAIC",
                "file_size": 5000,
                "width": 128,
                "height": 128
            },
            "file_id": "CAADBAADYwIAAkcsHgABRJq1A1dLZ9kC",
            "file_size": 27112
        }
    }
}';

$app = new Bot($fake_webhook);
$app->run();