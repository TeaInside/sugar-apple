<?php

namespace Handler\CMD;

use DB;
use PDO;
use Lang\Map;
use Telegram as B;
use Handler\Session;
use Handler\MainHandler;
use Contracts\CommandList;
use App\MyAnimeList\MyAnimeList;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @license MIT
 */

final class CMDHandler implements CommandList
{
    /**
     * @var Handler\MainHandler
     */
    private $h;

    /**
     * @var string
     */
    private $lang;

    /**
     * @var array
     */
    private $r1 = [];

    /**
     * @var array
     */
    private $r2 = [];

    /**
     * @param Handler\MainHandler $handler
     */
    public function __construct(MainHandler $handler)
    {
        $this->h    = $handler;
        $this->lang = Map::$language["id"];
        $this->buildContext();
    }

    public function __help($param)
    {

$msg = "/anime [spasi] <i>judul_anime</i> : Pencarian anime.
/idan [spasi] <i>id_anime</i> : Info anime.

/manga [spasi] <i>judul_anime</i> : Pencarian manga.
/idma [spasi] <i>id_manga</i> : Info manga.
";

        if ($this->h->chattype == "private") {
            B::sendMessage(
                [
                    "chat_id" => $this->h->chat_id,
                    "text" => $msg,
                    "parse_mode" => "HTML"
                ]
            );
        }
    }

    public function __report($param)
    {
        DB::prepare("SELECT `userid` FROM ``");
    }

    /**
     * @param  string $param
     * @return string|bool
     */
    public function __start($param)
    {
        if ($this->h->chattype == "private") {
            $this->lang .= "Basic";
            return B::sendMessage(
                [
                    "chat_id" => $this->h->chat_id,
                    "text"      => $this->fixer($this->lang::$a['private_start'])
                ]
            );
        }
        return false;
    }

    public function __forgive($param)
    {
        if (in_array($this->h->userid, SUDOERS) or in_array($this->h->userid, GLOBAL_ADMIN)) {
            $flag = true;
        } else {
            $st = DB::prepare("SELECT `status` FROM `groups_admin` WHERE `userid`=:uid AND `group_id`=:gid LIMIT 1;");
            pc($st->execute(
                [
                    ":uid" => $this->h->userid,
                    ":gid"      => $this->h->chat_id
                ]
            ), $st);
            if ($st = $st->fetch(PDO::FETCH_NUM)) {
                $flag = true;
            } else {
                $flag = false;
            }
        }
        if (!$flag) {
            B::sendMessage(
                [
                    "chat_id" => $this->h->chat_id,
                    "text"      => "You're not allowed to use this command!",
                    "reply_to_message_id" => $this->h->msgid
                ]
            );
            return 1;
        }
        if (isset($this->h->replyto)) {
            $st = DB::prepare("SELECT `reason` FROM `user_warn` WHERE `userid`=:userid AND `group_id`=:group_id LIMIT 1;");
            pc($st->execute(
                [
                    ":userid" => $this->h->replyto['from']['id'],
                    ":group_id" => $this->h->chat_id
                ]
            ), $st) xor $context = "";
            if ($st = $st->fetch(PDO::FETCH_NUM)) {
                $st = json_decode($st[0], true) xor $i = 1;
                foreach ($st as $val) {
                    $context .= "<b>".($i++)."</b>. <code>".($val['reason'] ? htmlspecialchars($val['reason']) : "Normal warn")."</code>\n";
                }
            }
            $st = DB::prepare("DELETE FROM `user_warn` WHERE `userid`=:userid AND `group_id`=:group_id LIMIT 1;");
            pc($st->execute(
                [
                    ":userid" => $this->h->replyto['from']['id'],
                    ":group_id" => $this->h->chat_id
                ]
            ), $st);
            $msg = "Done! <a href=\"tg://user?id=".$this->h->replyto['from']['id']."\">".htmlspecialchars($this->h->replyto['from']['first_name'])."</a> has been forgiven.\n".($context ? "<b>Warns found:</b>\n".$context : "");
            B::sendMessage(
                [
                    "chat_id"   => $this->h->chat_id,
                    "text"      => $msg,
                    "parse_mode" => "HTML"
                ]
            );
        }
    }

    public function __idan($id)
    {
        if (empty($id)) {
            $sess = new Session($this->h->userid);
            $exe = $sess->set("cmd_session", [
                "cmd"         => "/idan",
                "chat_id"     => $this->h->chat_id,
                "expired_at" => time()+300
            ]);
            if (!$exe) {
                die("Gagal menulis session!");
            }
            return B::sendMessage(
                [
                    "chat_id" => $this->h->chat_id,
                    "text"      => "Sebutkan ID anime!",
                    "reply_to_message_id" => $this->h->msgid,
                    "reply_markup" => json_encode(["force_reply"=>true,"selective"=>true])
                ]
            );
        } else {
            $fx = function ($str) {
                if (is_array($str)) {
                    return trim(str_replace(array("[i]", "[/i]","<br />"), array("<i>", "</i>","\n"), html_entity_decode(implode($str))));
                }
                return trim(str_replace(array("[i]", "[/i]","<br />"), array("<i>", "</i>","\n"), html_entity_decode($str, ENT_QUOTES, 'UTF-8')));
            };
            $st = new MyAnimeList(MAL_USER, MAL_PASS);
            $st = $st->get_info($id);
            $st = isset($st['entry']) ? $st['entry'] : $st;
            if (is_array($st) and count($st)) {
                $img = $st['image'];
                unset($st['image']);
                $rep = "";
                foreach ($st as $key => $value) {
                    $ve = $fx($value);
                    !empty($ve) and $rep .= "<b>".ucwords($key)."</b> : ".($ve)."\n";
                }
                $rep = str_replace("\n\n", "\n", $rep);
            } else {
                $rep = "Mohon maaf, anime dengan id \"{$id}\" tidak ditemukan !";
            }
            isset($img) and B::sendPhoto(
                [
                    "chat_id" => $this->h->chat_id,
                    "photo" => $img,
                    "reply_to_message_id" => $this->h->msgid
                ]
            );
            return B::sendMessage(
                [
                    "chat_id" => $this->h->chat_id,
                    "text" => $rep,
                    "reply_to_message_id" => $this->h->msgid,
                    "parse_mode" => "HTML"
                ]
            );
        }
    }
    
    public function __idma($id)
    {
        if (empty($query)) {
            $sess = new Session($this->h->userid);
            $exe = $sess->set("cmd_session", [
                "cmd"         => "/idma",
                "chat_id"     => $this->h->chat_id,
                "expired_at" => time()+300
            ]);
            if (!$exe) {
                die("Gagal menulis session!");
            }
            return B::sendMessage(
                [
                    "chat_id" => $this->h->chat_id,
                    "text"      => $this->fixer($this->lang::$a['manga_id']),
                    "reply_to_message_id" => $this->h->msgid,
                    "reply_markup" => json_encode(["force_reply"=>true,"selective"=>true])
                ]
            );
        } else {
            $fx = function ($str) {
                if (is_array($str)) {
                    return trim(str_replace(array("[i]", "[/i]","<br />"), array("<i>", "</i>","\n"), html_entity_decode(implode($str))));
                }
                return trim(str_replace(array("[i]", "[/i]","<br />"), array("<i>", "</i>","\n"), html_entity_decode($str, ENT_QUOTES, 'UTF-8')));
            };
            $st = new MyAnimeList(MAL_USER, MAL_PASS);
            $st = $st->get_info($id, "manga");
            $st = isset($st['entry']) ? $st['entry'] : $st;
            if (is_array($st) and count($st)) {
                $img = $st['image'];
                unset($st['image']);
                $rep = "";
                foreach ($st as $key => $value) {
                    $ve = $fx($value);
                    !empty($ve) and $rep .= "<b>".ucwords($key)."</b> : ".($ve)."\n";
                }
                isset($img) and B::sendPhoto(
                    [
                    "chat_id" => $this->h->chat_id,
                    "photo" => $img,
                    "reply_to_message_id" => $this->h->msgid
                    ]
                );
                return B::sendMessage(
                    [
                    "chat_id" => $this->h->chat_id,
                    "text" => $rep,
                    "reply_to_message_id" => $this->h->msgid,
                    "parse_mode" => "HTML"
                    ]
                );
            } else {
                B::sendMessage(
                    [
                        "text" => "Mohon maaf, manga \"{$id}\" tidak ditemukan !",
                        "chat_id" => $this->h->chat_id
                    ]
                );
            }
        }
    }

    public function __manga($query)
    {
        $this->lang .= "MyAnimeList";
        if (empty($query)) {
            $sess = new Session($this->h->userid);
            $exe = $sess->set("manga_cmd", [
                "expired_at" => time()+3600
            ]);
            if (!$exe) {
                die("Gagal menulis session!");
            }
            return B::sendMessage(
                [
                    "chat_id" => $this->h->chat_id,
                    "text"      => $this->fixer($this->lang::$a['manga_query']),
                    "reply_to_message_id" => $this->h->msgid,
                    "reply_markup" => json_encode(["force_reply"=>true,"selective"=>true])
                ]
            );
        } else {
            $st = new MyAnimeList(MAL_USER, MAL_PASS);
            $st->search($query, "manga");
            $st->exec();
            $st = $st->get_result();
            if (isset($st['entry']['id'])) {
                $rep = "";
                $rep.="Hasil pencarian manga :\n<b>{$st['entry']['id']}</b> : {$st['entry']['title']}\n\nBerikut ini adalah manga yang cocok dengan <b>{$query}</b>.\n\nKetik /idma [spasi] [id_anime] atau balas dengan id manga untuk menampilkan info manga lebih lengkap.";
            } elseif (is_array($st) and $xz = count($st['entry'])) {
                $rep = "Hasil pencarian manga :\n";
                foreach ($st['entry'] as $vz) {
                    $rep .= "<b>".$vz['id']."</b> : ".$vz['title']."\n";
                }
                $rep.="\nBerikut ini adalah beberapa manga yang cocok dengan <b>{$query}</b>.\n\nKetik /idma [spasi] [id_manga] untuk info lebih lengkap.";
            } else {
                $rep = "Mohon maaf, anime \"{$query}\" tidak ditemukan !";
            }
            return B::sendMessage(
                [
                    "chat_id" => $this->h->chat_id,
                    "text" => $rep,
                    "parse_mode" => "HTML",
                    "disable_web_page_preview" => true
                ]
            );
        }
    }

    public function __anime($query)
    {
        if (empty($query)) {
            $sess = new Session($this->h->userid);
            $exe = $sess->set("cmd_session", [
                "cmd"         => "/anime",
                "chat_id"     => $this->h->chat_id,
                "expired_at" => time()+300
            ]);
            if (!$exe) {
                die("Gagal menulis session!");
            }
            return B::sendMessage(
                [
                    "chat_id" => $this->h->chat_id,
                    "text"      => "Anime apa yang ingin kamu cari?",
                    "reply_to_message_id" => $this->h->msgid,
                    "reply_markup" => json_encode(["force_reply"=>true,"selective"=>true])
                ]
            );
        } else {
            $st = new MyAnimeList(MAL_USER, MAL_PASS);
            $st->search($query);
            $st->exec();
            $st = $st->get_result();
            if (isset($st['entry']['id'])) {
                $rep = "";
                $rep.="Hasil pencarian anime :\n<b>{$st['entry']['id']}</b> : {$st['entry']['title']}\n\nBerikut ini adalah anime yang cocok dengan <b>{$query}</b>.\n\nKetik /idan [spasi] [id_anime] atau balas dengan id anime untuk menampilkan info anime.";
            } elseif (is_array($st) and $xz = count($st['entry'])) {
                $rep = "Hasil pencarian anime :\n";
                foreach ($st['entry'] as $vz) {
                    $rep .= "<b>".$vz['id']."</b> : ".$vz['title']."\n";
                }
                $rep.="\nBerikut ini adalah beberapa anime yang cocok dengan <b>{$query}</b>.\n\nKetik /idan [spasi] [id_anime] untuk menampilkan info lebih lengkap.";
            } else {
                $rep = "Mohon maaf, anime \"{$query}\" tidak ditemukan !";
                $noforce = true;
            }
            return B::sendMessage(
                [
                    "chat_id" => $this->h->chat_id,
                    "text" => $rep,
                    "parse_mode" => "HTML",
                    "disable_web_page_preview" => true
                ]
            );
        }
    }

    public function __ban($param)
    {
        if (in_array($this->h->userid, SUDOERS) or in_array($this->h->userid, GLOBAL_ADMIN)) {
            $flag = true;
        } else {
            $st = DB::prepare("SELECT `status` FROM `groups_admin` WHERE `userid`=:uid AND `group_id`=:gid LIMIT 1;");
            pc($st->execute(
                [
                    ":uid" => $this->h->userid,
                    ":gid"      => $this->h->chat_id
                ]
            ), $st);
            if ($st = $st->fetch(PDO::FETCH_NUM)) {
                $flag = true;
            } else {
                $flag = false;
            }
        }
        if ($flag) {
            if (! isset($this->h->replyto) && isset($this->h->entities['mention'])) {
                $query = "SELECT `userid`,`name` FROM `a_users` WHERE " xor $data = [];
                foreach ($this->h->entities['mention'] as $key => $value) {
                    $query .= "`username`=:un_{$key} OR ";
                    $data[':un_'.$key] = $value;
                }
                if ($data) {
                    $st = DB::prepare(rtrim($query, " OR ").";");
                    pc($st->execute($data), $st);
                    while ($r = $st->fetch(PDO::FETCH_NUM)) {
                        if (($rrr = json_decode(B::kickChatMember(
                            [
                                "chat_id" => $this->h->chat_id,
                                "user_id" => $r[0]
                            ]
                        )['content'], true)) === ["ok" => true, "result" => true]) {
                            $msg = "<a href=\"tg://user?id=".$this->h->userid."\">".htmlspecialchars($this->h->first_name)."</a> banned <a href=\"tg://user?id=".$r[0]."\">".htmlspecialchars($r[1])."</a>!";
                        } else {
                            $msg = $rrr['description'];
                        }
                        B::sendMessage(
                            [
                                "chat_id" => $this->h->chat_id,
                                "text"      => $msg,
                                "parse_mode" => "HTML"
                            ]
                        );
                    }
                }
            } else {
                ($rrr = json_decode(B::kickChatMember(
                    [
                                "chat_id" => $this->h->chat_id,
                                "user_id" => $this->h->replyto['from']['id']
                            ]
                )['content'], true)) === ["ok" => true, "result" => true] and
                B::sendMessage(
                    [
                        "chat_id" => $this->h->chat_id,
                        "text"    => "<a href=\"tg://user?id=".$this->h->userid."\">".htmlspecialchars($this->h->first_name)."</a> banned <a href=\"tg://user?id=".$this->h->replyto['from']['id']."\">".htmlspecialchars($this->h->replyto['from']['first_name'])."</a>!",
                        "parse_mode" => "HTML"
                    ]
                ) or B::sendMessage(
                    [
                        "text" => $rrr['description'],
                        "chat_id" => $this->h->chat_id
                    ]
                );
            }
        } else {
            B::sendMessage(
                [
                    "chat_id" => $this->h->chat_id,
                    "text"      => "You're not allowed to use this command!",
                    "reply_to_message_id" => $this->h->msgid
                ]
            );
        }
    }

    /**
     * @param string $param
     */
    public function __sh($param)
    {
        if (strpos($param, "sudo ") !== false) {
            if (in_array($this->h->userid, SUDOERS)) {
                $sh = shell_exec($param." 2>&1");
                if (empty($sh)) {
                    $sh = "<pre>~</pre>";
                } else {
                    $sh = "<pre>".htmlspecialchars($sh)."</pre>";
                }
            } else {
                if ($this->h->chattype == "private") {
                    $chatroom = "Private chat";
                } else {
                    $group = isset($this->h->chatuname) ? "<a href=\"https://t.me/".$this->h->chatuname."/".$this->h->msgid."\">".htmlspecialchars($this->h->chattitle)."</a>" : "<code>".htmlspecialchars($this->h->chattitle)."</code>";
                    $chatroom = "Group (".$group.")";
                }
                $msg = "<b>WARNING</b>\nUnwanted user tried to use sudo.\n\n<b>• Rejected at</b>: ".date("Y-m-d H:i:s")."\n<b>• Tried by</b>: <a href=\"tg://user?id=".$this->h->userid."\">".htmlspecialchars($this->h->name)."</a> (<code>".($this->h->userid)."</code>)\n<b>• Chat Room</b>: ".$chatroom."\n<b>• Command</b>: <code>".htmlspecialchars($this->h->text)."</code>";
                foreach (SUDOERS as $val) {
                    B::sendMessage(
                        [
                            "chat_id"        => $val,
                            "text"            => $msg,
                            "parse_mode"    => "HTML",
                            "disable_web_page_preview" => true
                        ]
                    );
                }
                $sh = "<a href=\"tg://user?id=".$this->h->userid."\">".htmlspecialchars($this->h->name)."</a> is not in the sudoers file. This incident will be reported.";
            }
        } else {
            $sh = shell_exec($param." 2>&1");
            if (empty($sh)) {
                $sh = "<pre>~</pre>";
            } else {
                $sh = "<pre>".htmlspecialchars($sh)."</pre>";
            }
        }
        
        return B::sendMessage(
            [
                "chat_id"                => $this->h->chat_id,
                "text"                    => $sh,
                "parse_mode"            => "HTML",
                "reply_to_message_id"    => $this->h->msgid
            ]
        );
    }

    public function __warn($param)
    {
        if (in_array($this->h->userid, SUDOERS) or in_array($this->h->userid, GLOBAL_ADMIN)) {
            $flag = true;
        } else {
            $st = DB::prepare("SELECT `status` FROM `groups_admin` WHERE `userid`=:uid AND `group_id`=:gid LIMIT 1;");
            pc($st->execute(
                [
                    ":uid" => $this->h->userid,
                    ":gid"      => $this->h->chat_id
                ]
            ), $st);
            if ($st = $st->fetch(PDO::FETCH_NUM)) {
                $flag = true;
            } else {
                $flag = false;
            }
        }
        if (!$flag) {
            B::sendMessage(
                [
                    "chat_id" => $this->h->chat_id,
                    "text"      => "You're not allowed to use this command!",
                    "reply_to_message_id" => $this->h->msgid
                ]
            );
            return 1;
        }
        if (isset($this->h->replyto)) {
            $param = !empty($param) ? $param : null;
            $sq = DB::prepare("SELECT `max_warn` FROM `a_groups` WHERE `group_id`=:group_id LIMIT 1;");
            pc($sq->execute([":group_id" => $this->h->chat_id]), $sq);
            $sq = $sq->fetch(PDO::FETCH_NUM);
            $st = DB::prepare("SELECT `warn_count`,`reason` FROM `user_warn` WHERE `userid`=:userid AND `group_id`=:group_id LIMIT 1;");
            pc($st->execute(
                [
                    ":userid" => $this->h->replyto['from']['id'],
                    ":group_id" => $this->h->chat_id
                ]
            ), $st) xor $nl = "<a href=\"tg://user?id=".$this->h->replyto['from']['id']."\">".htmlspecialchars($this->h->replyto['from']['first_name'])."</a>";
            if ($st = $st->fetch(PDO::FETCH_NUM)) {
                $st[1] = json_decode($st[1], true);
                $st[1][] = [
                    "reason" => $param,
                    "warned_by" => $this->h->userid,
                    "date"    => date("Y-m-d H:i:s")
                ] xor $st[1] = json_encode($st[1]);
                if (($st[0] = $st[0]+1) >= $sq[0]) {
                    (($rr = B::kickChatMember(
                        [
                            "chat_id" => $this->h->chat_id,
                            "user_id" => $this->h->replyto['from']['id']
                        ]
                    )['content']) != '{"ok":true,"result":true}') and $err = "Error : <code>\n".htmlspecialchars(json_decode($rr, true)['description'])."</code>" or $err = "";
                    B::sendMessage(
                        [
                            "text" => $nl." <b>banned</b>: reached the max number of warnings (<code>".($st[0])."/".$sq[0]."</code>)\n\n".$err,
                            "chat_id" => $this->h->chat_id,
                            "parse_mode" => "HTML"
                        ]
                    );
                } else {
                    B::sendMessage(
                        [
                            "text" => $nl." has been warned (<code>".($st[0])."/".$sq[0]."</code>)",
                            "chat_id" => $this->h->chat_id,
                            "parse_mode" => "HTML"
                        ]
                    );
                }
                $stq = DB::prepare("UPDATE `user_warn` SET `warn_count`=`warn_count`+1,`reason`=:res,`updated_at`=:updated_at WHERE `userid`=:userid AND `group_id`=:group_id LIMIT 1;");
                pc($stq->execute(
                    [
                            ":res" => $st[1],
                            ":updated_at" => date("Y-m-d H:i:s"),
                            ":userid" => $this->h->replyto['from']['id'],
                            ":group_id" => $this->h->chat_id
                        ]
                ), $stq);
            } else {
                B::sendMessage(
                    [
                            "text" => $nl." has been warned (<code>1/".$sq[0]."</code>)",
                            "chat_id" => $this->h->chat_id,
                            "parse_mode" => "HTML"
                        ]
                );
                $st = DB::prepare("INSERT INTO `user_warn` (`group_id`, `userid`, `reason`, `warn_count`, `created_at`, `updated_at`) VALUES (:group_id, :userid, :res, 1, :created_at, NULL);");
                pc($st->execute(
                    [
                        ":group_id" => $this->h->chat_id,
                        ":userid" => $this->h->replyto['from']['id'],
                        ":res" => json_encode([ [
                                    "reason" => $param,
                                    "warned_by" => $this->h->userid,
                                    "date"    => date("Y-m-d H:i:s")
                                ]]),
                        ":created_at" => date("Y-m-d H:i:s")
                    ]
                ), $st);
            }
        }
    }

    public function __unban($param)
    {
        B::unbanChatMember(
            [
            ]
        );
    }

    public function __whatanime($param)
    {
        B::sendMessage(
            [
                "text" => "Malformed request!",
                "chat_id" => $this->h->chat_id,
                "reply_to_message_id" => $this->h->msgid
            ]
        );
    }

    public function __welcome($param)
    {
        $this->lang .= "Basic";
        if (! empty($param)) {
            $st = DB::prepare("UPDATE `a_groups` SET `welcome_message`=:wel, `updated_at`=:up WHERE `group_id`=:gid LIMIT 1;");
            pc($st->execute(
                [
                    ":wel"        => $param,
                    ":up"        => (date("Y-m-d H:i:s")),
                    ":gid"    => $this->h->chat_id
                ]
            ), $st);
            return B::sendMessage(
                [
                    "chat_id"                => $this->h->chat_id,
                    "text"                    => $this->fixer($this->lang::$a['set_welcome_msg']),
                    "reply_to_message_id"    => $this->h->msgid
                ]
            );
        } else {
            $st = DB::prepare("SELECT `welcome_message` FROM `a_groups` WHERE `group_id`=:gid LIMIT 1;");
            pc($st->execute(
                [
                    ":gid" => $this->h->chat_id
                ]
            ), $st);
            if ($st = $st->fetch(PDO::FETCH_NUM)) {
                if (! empty($st[0])) {
                    $st = DB::prepare("UPDATE `a_groups` SET `welcome_message`=:wel, `updated_at`=:up WHERE `group_id`=:gid LIMIT 1;");
                    pc($st->execute(
                        [
                            ":wel"        => null,
                            ":up"        => (date("Y-m-d H:i:s")),
                            ":gid"    => $this->h->chat_id
                        ]
                    ), $st);
                    return B::sendMessage(
                        [
                            "chat_id"                => $this->h->chat_id,
                            "text"                    => $this->fixer($this->lang::$a['error_empty_set_welcome_msg']),
                            "reply_to_message_id"    => $this->h->msgid
                        ]
                    );
                } else {
                    $st = DB::prepare("UPDATE `a_groups` SET `welcome_message`=:wel, `updated_at`=:up WHERE `group_id`=:gid LIMIT 1;");
                    pc($st->execute(
                        [
                            ":wel"        => null,
                            ":up"        => (date("Y-m-d H:i:s")),
                            ":gid"    => $this->h->chat_id
                        ]
                    ), $st);
                    return B::sendMessage(
                        [
                            "chat_id"                => $this->h->chat_id,
                            "text"                    => $this->fixer($this->lang::$a['drop_welcome_msg']),
                            "reply_to_message_id"    => $this->h->msgid
                        ]
                    );
                }
            }
        }
    }

    public function __debug($param)
    {
        if (isset($this->h->replyto)) {
            B::sendMessage(
                [
                    "text" => "<pre>".htmlspecialchars(json_encode($this->h->replyto, 192))."</pre>",
                    "chat_id" => $this->h->chat_id,
                    "reply_to_message_id" => $this->h->msgid,
                    "parse_mode" => "HTML"
                ]
            );
        }
    }

    /**
     * Build fixer context.
     */
    private function buildContext()
    {
        $this->r1    = [
            "{name}",
            "{first_name}",
            "{last_name}",
        ];
        $this->r2    = [
            $this->h->name,
            $this->h->first_name,
            $this->h->last_name
        ];
    }

    /**
     * @param  string $str
     * @return string
     */
    private function fixer($str)
    {
        return str_replace($this->r1, $this->r2, $str);
    }
}
