<?php

/*
* Loads all external assets.
*/
require_once(__DIR__.DIRECTORY_SEPARATOR.'vendor'.
					DIRECTORY_SEPARATOR.'autoload.php');

use Symfony\Component\ClassLoader\UniversalClassLoader;
$loader = new UniversalClassLoader();
$loader->registerNamespace('ABCMath', __DIR__);
$loader->register();


?>