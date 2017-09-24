<?php

namespace Handler;

defined("PRIVATE_STORAGE") or die("PRIVATE_STORAGE not defined!\n");

use Contracts\AutomaticStorageManagement;

final class Session implements AutomaticStorageManagement
{
    /**
     * @var string
     */
    private $sessid;

    /**
     * @var string
     */
    private $sessfile;

    /**
     * @var array
     */
    private $sessdata = [];

    /**
     * @var bool
     */
    private $destroyed = false;

    /**
     * @param string $sessid
     */
    public function __construct($sessid)
    {
        $this->sessid   = sha1($sessid);
        $this->sessfile = PRIVATE_STORAGE."/session/".$sessid.".json";
        $this->__init();
    }

    public static function session_exists($sessid)
    {
        return file_exists(PRIVATE_STORAGE."/session/".$sessid.".json");
    }

    /**
     * Init file and property.
     */
    private function __init()
    {
        is_dir(PRIVATE_STORAGE."/session") or mkdir(PRIVATE_STORAGE."/session");
        if (file_exists($this->sessfile)) {
            $this->sessdata = json_decode(file_get_contents($this->sessfile), true);
        } else {
            file_put_contents($this->sessfile, "", LOCK_EX);
        }
    }

    /**
     * Set session.
     * @param string $key
     * @param string $value
     * @return bool
     */
    public function set($key, $value)
    {
        $this->sessdata[$key] = $value;
        return (bool)file_put_contents($this->sessfile, json_encode($this->sessdata), LOCK_EX);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        $this->sessdata = json_decode(file_get_contents($this->sessfile), true);
        if (isset($this->sessdata[$key])) {
            return $this->sessdata[$key];
        } else {
            return false;
        }
    }

    public function __destruct()
    {
        if (! $this->destroyed) {
            file_put_contents($this->sessfile, json_encode($this->sessdata), LOCK_EX);
        }
    }

    public function destroy()
    {
        $this->sessdata = [];
        $this->destroyed = true;
        return unlink($this->sessfile);
    }
}
