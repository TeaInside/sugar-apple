<?php

namespace App\MyAnimeList;

use Exception;

class MyAnimeListException extends Exception
{
    public function __construct(...$a)
    {
        parent::__construct(...$a);
    }
}
