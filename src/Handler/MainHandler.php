<?php

namespace Handler;

use Telegram as B;
use Handler\Session;
use Handler\Response;
use Handler\SaveEvent;
use Handler\CMD\CMDHandler;
use Handler\VirtualizorHandler;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @license MIT
 */

final class MainHandler
{
    /**
     * @var array
     */
    public $event = [];

    /**
     * @var string
     */
    public $msgtype;

    /**
     * @var string
     */
    public $chattype;

    /**
     * @var string
     */
    public $text;

    /**
     * @var string
     */
    public $lowertext;

    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $first_name;

    /**
     * @var string
     */
    public $last_name;

    /**
     * @var string
     */
    public $userid;

    /**
     * @var string
     */
    public $msgid;

    /**
     * @var string
     */
    public $date;

    /**
     * @var string
     */
    public $chat_id;

    /**
     * @var array
     */
    public $replyto;

    /**
     * @var array|null
     */
    public $photo;

    /**
     * @var string
     */
    public $chattitle;

    /**
     * @var string
     */
    public $chatuname;

    /**
     * @var array|null
     */
    public $entities;

    /**
     * @var bool
     */
    private $session_action = false;

    /**
     * @param string $webhook_input
     */
    public function __construct($webhook_input = null)
    {
        if ($webhook_input) {
            $this->input = json_decode($webhook_input, true);
        } else {
            $this->input = json_decode(file_get_contents("php://input"), true);
        }
    }

    public function run()
    {
        $this->parseEvent();
        $this->parseSession();
        if (! $this->session_action) {
            $vir = new VirtualizorHandler($this);
            if (! $vir->exec()) {
                $this->response();
            }
        }
        $this->save_event();
    }

    private function parseSession()
    {
        if (Session::session_exists($this->userid)) {
            $sess = new Session($this->userid);
            if ($r = $sess->get("cmd_session")) {
                if (isset($r['chat_id']) &&
                    $r['chat_id'] === $this->chat_id &&
                    $r['expired_at'] >= time()
                ) {
                    $cmd = new CMDHandler($this);
                    switch ($r['cmd']) {
                        case '/anime':
                            $cmd->__anime($this->lowertext);
                            $sess->destroy();
                            break;
                        case '/manga':
                            $cmd->__manga($this->lowertext);
                            $sess->destroy();
                            break;
                        case '/idan':
                            $cmd->__idan($this->lowertext);
                            $sess->destroy();
                            break;
                        case '/idma':
                            $cmd->__idma($this->lowertext);
                            $sess->destroy();
                            break;
                        default:
                            break;
                    }
                    $this->session_action = true;
                } else {
                    $sess->destroy();
                }
            }
        }
    }

    private function parseEvent()
    {
        isset($this->input['message']['chat']['title'])    and $this->chattitle = $this->input['message']['chat']['title'];
        isset($this->input['message']['reply_to_message'])    and $this->replyto   = $this->input['message']['reply_to_message'];
        isset($this->input['message']['chat']['username'])    and $this->chatuname = strtolower($this->input['message']['chat']['username']);
        isset($this->input['message']['entities'])            and $this->entities  = $this->input['message']['entities'];
        if (isset($this->input['inline_query'])) {
            $this->msgid = $this->input['inline_query']['id'];
        } elseif (isset($this->input['message']['text'])) {
            $this->msgtype    = "text";
            $this->chattype    = $this->input['message']['chat']['type'];
            $this->text         = $this->input['message']['text'];
            $this->lowertext    = strtolower($this->text);
            $this->username        = isset($this->input['message']['from']['username']) ? strtolower($this->input['message']['from']['username']) : null;
            $this->first_name   = $this->input['message']['from']['first_name'];
            $this->last_name    = isset($this->input['message']['from']['last_name']) ? $this->input['message']['from']['last_name'] : null;
            $this->name            = $this->first_name.(isset($this->last_name) ? " ".$this->last_name : "");
            $this->userid        = $this->input['message']['from']['id'];
            $this->msgid        = $this->input['message']['message_id'];
            $this->date            = $this->input['message']['date'];
            $this->chat_id        = $this->input['message']['chat']['id'];
        } elseif (isset($this->input['message']['photo'])) {
            $this->msgtype    = "photo";
            $this->chattype    = $this->input['message']['chat']['type'];
            $this->text         = isset($this->input['message']['caption']) ? $this->input['message']['caption'] : null;
            $this->lowertext    = isset($this->text) ? strtolower($this->text) : null;
            $this->username        = isset($this->input['message']['from']['username']) ? strtolower($this->input['message']['from']['username']) : null;
            $this->first_name   = $this->input['message']['from']['first_name'];
            $this->last_name    = isset($this->input['message']['from']['last_name']) ? $this->input['message']['from']['last_name'] : null;
            $this->name            = $this->first_name.(isset($this->last_name) ? " ".$this->last_name : "");
            $this->userid        = $this->input['message']['from']['id'];
            $this->msgid        = $this->input['message']['message_id'];
            $this->date            = $this->input['message']['date'];
            $this->chat_id        = $this->input['message']['chat']['id'];
            $this->photo        = $this->input['message']['photo'];
        } elseif (isset($this->input['message']['sticker'])) {
            $this->msgtype    = "sticker";
            $this->chattype    = $this->input['message']['chat']['type'];
            $this->text         = $this->input['message']['sticker']['emoji'];
            $this->lowertext    = $this->input['message']['sticker']['emoji'];
            $this->username        = isset($this->input['message']['from']['username']) ? strtolower($this->input['message']['from']['username']) : null;
            $this->first_name   = $this->input['message']['from']['first_name'];
            $this->last_name    = isset($this->input['message']['from']['last_name']) ? $this->input['message']['from']['last_name'] : null;
            $this->name            = $this->first_name.(isset($this->last_name) ? " ".$this->last_name : "");
            $this->userid        = $this->input['message']['from']['id'];
            $this->msgid        = $this->input['message']['message_id'];
            $this->date            = $this->input['message']['date'];
            $this->chat_id        = $this->input['message']['chat']['id'];
            $this->sticker        = $this->input['message']['sticker']['file_id'];
        } elseif (isset($this->input['message']['new_chat_member'])) {
            $this->msgtype    = "new_chat_member";
            $this->chattype    = $this->input['message']['chat']['type'];
            $this->username        = isset($this->input['message']['new_chat_member']['username']) ? strtolower($this->input['message']['new_chat_member']['username']) : null;
            $this->first_name   = $this->input['message']['new_chat_member']['first_name'];
            $this->last_name    = isset($this->input['message']['new_chat_member']['last_name']) ? $this->input['message']['new_chat_member']['last_name'] : null;
            $this->name            = $this->first_name.(isset($this->last_name) ? " ".$this->last_name : "");
            $this->userid        = $this->input['message']['new_chat_member']['id'];
            $this->msgid        = $this->input['message']['message_id'];
            $this->date            = $this->input['message']['date'];
            $this->chat_id        = $this->input['message']['chat']['id'];
        }
    }

    private function response()
    {
        if ($this->msgtype === "text" && isset($this->replyto['text'])) {
            if (substr($this->text, 0, 3) == "/s/") {
                $a = explode("/", $this->text);
                if (isset($a[2], $a[3])) {
                    $r = "<b>Did you mean:</b>\n\"".preg_replace("#".$a[2]."#", $a[3], $this->replyto['text'])."\"";
                    if ($r) {
                        return B::sendMessage(
                            [
                                "chat_id" => $this->chat_id,
                                "text" => $r,
                                "reply_to_message_id" => $this->replyto['message_id'],
                                "parse_mode" => "HTML"
                            ]
                        );
                    }
                }
            }
        }
        if (in_array($this->msgtype, ["text", "photo", "sticker", "new_chat_member"])) {
            $res = new Response($this);
            $res->exec();
        }
    }

    private function save_event()
    {
        if (in_array($this->msgtype, ["text", "photo", "sticker"])) {
            $se = new SaveEvent($this);
            $se->exec();
        }
    }
}
