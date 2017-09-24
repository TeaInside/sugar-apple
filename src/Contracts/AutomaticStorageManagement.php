<?php

namespace Contracts;

interface AutomaticStorageManagement
{
    public function __construct($file_id);

    public function __destruct();
}
