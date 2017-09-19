<?php

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 */

class DB
{
    /**
     * @param string $method
     * @param array  $param
     * @return PDO
     */
    public static function __callStatic($method, $param)
    {
        return (new PDO("mysql:host=".DBHOST.";dbname=".DBNAME, DBUSER, DBPASS))->{$method}(...$param);
    }

    /**
     * @return PDO
     */
    public static function pdo()
    {
        return (new PDO("mysql:host=".DBHOST.";dbname=".DBNAME, DBUSER, DBPASS));
    }

    /**
     * @return PDO
     */
    public static function pdoInstance()
    {
        return (new PDO("mysql:host=".DBHOST.";dbname=".DBNAME, DBUSER, DBPASS));
    }
}
