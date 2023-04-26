<?php

spl_autoload_register(function(String $class) {

	$prefix = "ContestApp\\";
	
	$sources = sprintf("%s/classes/", __DIR__);
	$classes = str_replace("\\", "/", substr($class, strlen($prefix)));
	$classFilePath = sprintf("%s%s.php", $sources, $classes);

	if (is_readable($classFilePath)) {
		require_once $classFilePath;
	}

});
