<?php

require __DIR__."/config.php";

function __load_class($class)
{
	require __DIR__."/src/".str_replace("\\", "/", $class).".php";
}

spl_autoload_register("__load_class");