<?php

namespace Contracts\AppContracts;

interface Virtualizor
{
    public function __construct($code);

    public function exec();
}
