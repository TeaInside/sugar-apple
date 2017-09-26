<?php

namespace Contracts;

use Handler\MainHandler;

interface CommandList
{
    public function __construct(MainHandler $handler);

    public function __start($param);

    public function __idan($param);

    public function __idma($param);

    public function __manga($param);

    public function __anime($param);

    public function __ban($param);

    public function __unban($param);

    public function __sh($param);

    public function __warn($param);

    public function __welcome($param);
}
