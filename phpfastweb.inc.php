<?php

//General functions on main namespace
require_once(dirname(__FILE__)."/src/functions.inc.php");

//Cache for loading clases
//@todo: Hacer cache una clase estática
require(dirname(__FILE__)."/src/cache.class.php");
$cache = cache::create();
function __autoload($class_name) {
	cache::autoload($class_name);
}

